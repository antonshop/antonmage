<?php

class Company_Ecpss_PaymentController extends Mage_Core_Controller_Front_Action
{
    /**
     * Order instance
     */
    protected $_order;

    /**
     *  Get order
     *
     *  @param    none
     *  @return	  Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if ($this->_order == null) {
            $session = Mage::getSingleton('checkout/session');
            $this->_order = Mage::getModel('sales/order');
            $this->_order->loadByIncrementId($session->getLastRealOrderId());
        }
        return $this->_order;
    }

    /**
     * When a customer chooses Ecpss on Checkout/Payment page
     *
     */
	public function redirectAction()
	{
		$session = Mage::getSingleton('checkout/session');
		$session->setEcpssPaymentQuoteId($session->getQuoteId());

		$order = $this->getOrder();

		if (!$order->getId()) {
			$this->norouteAction();
			return;
		}

		$order->addStatusToHistory(
			$order->getStatus(),
			Mage::helper('ecpss')->__('Customer was redirected to Ecpss')
		);
		$order->save();

		$this->getResponse()
			->setBody($this->getLayout()
				->createBlock('ecpss/redirect')
				->setOrder($order)
				->toHtml());

        $session->unsQuoteId();
    }

	/**
	 *  Ecpss response router
	 *
	 *  @param    none
	 *  @return	  void
	 */
	public function notifyAction()
	{
		$model = Mage::getModel('ecpss/payment');
        
        if ($this->getRequest()->isPost()) {
			$postData = $this->getRequest()->getPost();
        	$method = 'post';

		} else if ($this->getRequest()->isGet()) {
			$postData = $this->getRequest()->getQuery();
			$method = 'get';

		} else {
			$model->generateErrorResponse();
		}

//		$returnedMAC = $postData['MAC'];
//		$correctMAC = $model->getResponseMAC($postData);


		$order = Mage::getModel('sales/order')
			->loadByIncrementId($postData['reference']);

		if (!$order->getId()) {
			$model->generateErrorResponse();
		}

		if ($returnedMAC == $correctMAC) {
			if (1) {
				$order->addStatusToHistory(
					$model->getConfigData('order_status_payment_accepted'),
					Mage::helper('ecpss')->__('Payment accepted by Ecpss')
				);
				
				$order->sendNewOrderEmail();

				if ($this->saveInvoice($order)) {
//                $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
				}
				
			 } else {
			 	$order->addStatusToHistory(
					$model->getConfigData('order_status_payment_refused'),
					Mage::helper('ecpss')->__('Payment refused by Ecpss')
				);
				
				// TODO: customer notification on payment failure
			 }
				
			$order->save();

        } else {
            $order->addStatusToHistory(
                Mage_Sales_Model_Order::STATE_CANCELED,//$order->getStatus(),
                Mage::helper('ecpss')->__('Returned MAC is invalid. Order cancelled.')
            );
            $order->cancel();
            $order->save();
            $model->generateErrorResponse();
        }
    }

    /**
     *  Save invoice for order
     *
     *  @param    Mage_Sales_Model_Order $order
     *  @return	  boolean Can save invoice or not
     */
    protected function saveInvoice(Mage_Sales_Model_Order $order)
    {
        if ($order->canInvoice()) {
            $convertor = Mage::getModel('sales/convert_order');
            $invoice = $convertor->toInvoice($order);
            foreach ($order->getAllItems() as $orderItem) {
               if (!$orderItem->getQtyToInvoice()) {
                   continue;
               }
               $item = $convertor->itemToInvoiceItem($orderItem);
               $item->setQty($orderItem->getQtyToInvoice());
               $invoice->addItem($item);
            }
            $invoice->collectTotals();
            $invoice->register()->capture();
            Mage::getModel('core/resource_transaction')
               ->addObject($invoice)
               ->addObject($invoice->getOrder())
               ->save();
            return true;
        }

        return false;
    }

   /**
	 *  Success payment page
	 *
	 *  @param    none
	 *  @return	  void
	 */
	public function returnAction()
	{
		$model = Mage::getModel('ecpss/payment');
		//middle payment
		if ($this->getRequest()->isPost()) {
			$rData['BillNo'] = $this->getRequest()->getPost();
        	$method = 'post';

		} else if ($this->getRequest()->isGet()) {
			$rData = $this->getRequest()->getQuery();
			$method = 'get';

		} else {
			$model->generateErrorResponse();
		}
		$tmplist = explode("\n", $model->getConfigData('middle_sitelist'));
		$subsite = array();
		echo "<pre>";
		print_r($rData);echo "<br>";
		foreach($tmplist as $value){
			$site = explode('=>', $value);
			$subsite[$site[0]] = $site[1];
			if(strpos($site[0], $rData['BillNo']['BillNo']) !== false){
				$rData['BillNo']['BillNo'] = str_replace($site[0].'_', '', $rData['BillNo']['BillNo']);
				$rData['BillNo']['ReturnURL'] = $site[1];
			}
		}
		
		if(isset($rData['goback_url']) && isset($rData['middle_token']) && $rData['middle_token'] == $model->getConfigData('middle_token')){
			$this->getFormUrl($rData['goback_url'], $rData);
		}
		//print_r($sitelist);echo "<br>";
		print_r($rData);echo "</pre><br>**";exit;
		
        $order = $this->getOrder();
		if ($this->_validated()) {
			
	        //echo $model->getConfigData('order_status_payment_accepted').'abc';
	        $order->addStatusToHistory(
                Mage_Sales_Model_Order::STATE_PROCESSING,//$order->getStatus(),
                Mage::helper('ecpss')->__('Payment succeed!')
            );
			$order->setState($model->getConfigData('order_status_payment_accepted'), true);
	        $order->save();
	        //exit;
			$this->_redirect('checkout/onepage/success');
			//exit;
		} else {
			//echo 'abcccc';exit;
			$order->addStatusToHistory(
                Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,//$order->getStatus(),
                Mage::helper('ecpss')->__('Payment failed!')
            );
			$order->setState($model->getConfigData('order_status_payment_refused'), true);
	        $order->save();
			$this->_redirect('checkout/onepage/failure');
			//exit;
		}
        //exit;
		//$this->_redirect('checkout/onepage/success');
	}
	
	/**
	 *  Middle payment send
	 *
	 *  @param    none
	 *  @return	  void
	 */
	public function sendAction(){
		$model = Mage::getModel('ecpss/payment');
		if ($this->getRequest()->isPost()) {
			$rData = $this->getRequest()->getPost();
        	$method = 'post';

		} else if ($this->getRequest()->isGet()) {
			$rData = $this->getRequest()->getQuery();
			$method = 'get';

		} else {
			$model->generateErrorResponse();
		}
		
		$this->getFormUrl($model->getEcpssUrl(), $rData, true);
	}
	
	public function getFormUrl($url, $rData, $status=null){
		$ordernum =array('BillNo', 'Remark');
		$standard = Mage::getModel('ecpss/payment');
        $form = new Varien_Data_Form();
        $form->setAction($url)
            ->setId('ecpss_payment_checkout')
            ->setName('ecpss_payment_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);
		foreach ($rData as $field => $value) {
			if($status && in_array($field, $ordernum)){
				$value = $standard->getConfigData('order_prefix') .'_'. $value;
			}
            $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
        }
        
		$form->addField('order_prefix', 'hidden', array('name' => 'order_prefix', 'value' => $standard->getConfigData('order_prefix')));
		$form->addField('middle_siteurl', 'hidden', array('name' => 'middle_siteurl', 'value' => $standard->getConfigData('middle_siteurl')));
		$form->addField('goback_url', 'hidden', array('name' => 'goback_url', 'value' => $standard->getConfigData('goback_url')));
		$form->addField('middle_token', 'hidden', array('name' => 'middle_token', 'value' => $standard->getConfigData('middle_token')));
		
        $formHTML = $form->toHtml();

        $html = '<html><body>';
        $html.= $this->__('You will be redirected to Ecpss in a few seconds.');
        $html.= $formHTML;
        $html.= '<script type="text/javascript">document.getElementById("ecpss_payment_checkout").submit();</script>';
        $html.= '</body></html>';
		echo $html;
	}
    
	private function _validated()
	{
		$model = Mage::getModel('ecpss/payment');
		
		if ($this->getRequest()->isPost()) {
			$rData = $this->getRequest()->getPost();
        	$method = 'post';

		} else if ($this->getRequest()->isGet()) {
			$rData = $this->getRequest()->getQuery();
			$method = 'get';

		} else {
			$model->generateErrorResponse();
		}print_r($rData);
		//print_r($rData);
		//订单号
		$BillNo = $rData["BillNo"];
		//币种
		$Currency = $rData["Currency"];
		//银行ID号
		//$BankID = $rData["BankID"];
		//金额
		$Amount = $rData["Amount"];
		//支付状态
		$Succeed = $rData["Succeed"];
		//支付平台流水号
		//$TradeNo = $rData["TradeNo"];
		//支付结果
		$Result = $rData["Result"];
		//取得的MD5校验信息
		$MD5info = $rData["MD5info"]; 
		//备注
		//$Remark = $rData["Remark"];
		//支付人名称
		//$Drawee = $rData["Drawee"];
		
		//MD5私钥
	    //$MD5key = "12345678";
	    $MD5key = $model->getConfigData('security_code');
	    //echo $MD5key;
		//校验源字符串
	    $md5src = $BillNo.$Currency.$Amount.$Succeed.$MD5key;
	    //MD5检验结果
		$md5sign = strtoupper(md5($md5src)); 
		//print_r($rData);
		//echo $md5sign.'<br/>'.$MD5info;exit;
		if (($MD5info == $md5sign)&&($Succeed == 19 || $Succeed == 88 || $Succeed == 9)) {
			return true;
		} else {
			return false;
		}
	}
    
	/**
	 *  Success payment page
	 *
	 *  @param    none
	 *  @return	  void
	 */
	public function successAction()
	{
		$session = Mage::getSingleton('checkout/session');
		$session->setQuoteId($session->getEcpssPaymentQuoteId());
		$session->unsEcpssPaymentQuoteId();
		
		$order = $this->getOrder();
		$standard = Mage::getModel('ecpss/payment');
		if (!$order->getId()) {
			$this->norouteAction();
			return;
		}

		$order->addStatusToHistory(
			$order->getStatus(),
			Mage::helper('ecpss')->__('Customer successfully returned from Ecpss')
		);
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
		$order->save();
        
		$this->_redirect('checkout/onepage/success');
	}

	/**
	 *  Failure payment page
	 *
	 *  @param    none
	 *  @return	  void
	 */
	public function errorAction()
	{
        $session = Mage::getSingleton('checkout/session');
        $errorMsg = Mage::helper('ecpss')->__(' There was an error occurred during paying process.');

        $order = $this->getOrder();

        if (!$order->getId()) {
            $this->norouteAction();
            return;
        }
        if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {
            $order->addStatusToHistory(
                Mage_Sales_Model_Order::STATE_CANCELED,//$order->getStatus(),
                Mage::helper('ecpss')->__('Customer returned from Ecpss.') . $errorMsg
            );
            
            $order->save();
        }

        $this->loadLayout();
        $this->renderLayout();
        Mage::getSingleton('checkout/session')->unsLastRealOrderId();
    }
}
