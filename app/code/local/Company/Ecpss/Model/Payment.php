<?php
/**
 *
 * @package    Ecpss
 * 
 * @author     xinhaozheng@gmail.com
 */

class Company_Ecpss_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'ecpss_payment';
    protected $_formBlockType = 'ecpss/form';

    // Ecpss return codes of payment
    const RETURN_CODE_ACCEPTED      = 'Success';
    const RETURN_CODE_TEST_ACCEPTED = 'Success';
    const RETURN_CODE_ERROR         = 'Fail';

    // Payment configuration
    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    // Order instance
    protected $_order = null;

    /**
     *  Returns Target URL
     *
     *  @return	  string Target URL
     */
    public function getEcpssUrl()
    {
        $url = $this->getConfigData('transport').'://'.$this->getConfigData('gateway');
        return $url;
    }

    /**
     *  Return back URL
     *
     *  @return	  string URL
     */
	protected function getReturnURL()
	{
		return Mage::getUrl('ecpss/payment/return', array('_secure' => true));
	}

	/**
	 *  Return URL for Ecpss success response
	 *
	 *  @return	  string URL
	 */
	protected function getSuccessURL()
	{
		return Mage::getUrl('checkout/onepage/success', array('_secure' => true));
	}

    /**
     *  Return URL for Ecpss failure response
     *
     *  @return	  string URL
     */
    protected function getErrorURL()
    {
        return Mage::getUrl('ecpss/payment/error', array('_secure' => true));
    }

	/**
	 *  Return URL for Ecpss notify response
	 *
	 *  @return	  string URL
	 */
	protected function getNotifyURL()
	{
		return Mage::getUrl('checkout/onepage/success', array('_secure' => true));
	}

    /**
     * Capture payment
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            ->setLastTransId($this->getTransactionId());

        return $this;
    }

    /**
     *  Form block description
     *
     *  @return	 object
     */
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('ecpss/form_payment', $name);
        $block->setMethod($this->_code);
        $block->setPayment($this->getPayment());

        return $block;
    }

    /**
     *  Return Order Place Redirect URL
     *
     *  @return	  string Order Redirect URL
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('ecpss/payment/redirect');
    }

    /**
     *  Return Standard Checkout Form Fields for request to Ecpss
     *
     *  @return	  array Array of hidden form fields
     */
    public function getStandardCheckoutFormFields()
    {
        $session = Mage::getSingleton('checkout/session');
        
        $order = $this->getOrder();
        if (!($order instanceof Mage_Sales_Model_Order)) {
            Mage::throwException($this->_getHelper()->__('Cannot retrieve order object'));
        }


     $MerNo =$this->getConfigData('partner_id');	 //商户号
     $BillNo =$order->getRealOrderId();		//订单号
     $Currency = $this->getConfigData('ecpss_currency');				//币种
     $Amount =  sprintf('%.2f', $order->getBaseGrandTotal());				//金额
     $DispAmount="0";					//外币金额
     $Language =$this->getConfigData('language');					//语言
     $ReturnURL = $this->getReturnURL(); 			//返回地址
     $Remark = $order->getRealOrderId();  //备注
     

		

		 $billingAddress=$order->getBillingAddress();
		$FirstName=$billingAddress->getFirstname();//[选填]'----------- 账单接收人姓		
		$LastName=$billingAddress->getLastname();//[选填]'----------- 账单接收人名
		$Email=$order->getData('customer_email');//[选填]'-----------账单接收人邮箱		
		$Phone=$billingAddress->getTelephone();//[选填]'-----------账单接收人电话		
		$ZipCode=$billingAddress->getPostcode();//[选填]'----------- 账单接收人邮编		
		$Address=$billingAddress->getStreetFull();//[选填]'-----------账单接收人地址		
		$City=$billingAddress->getCity();//[选填]'-----------账单接收人城市
		$State=$billingAddress->getRegion();//[选填]'----------- 账单接收人省或者州
		
		$Country=$billingAddress->getCountry();//[选填]----------账单接收人所在国家

		/*
		 *以下九个参数为收货人信息,能收集的数据请尽力收集，,实在收集不到的参数---请赋空值,谢谢
		 */
		 $shippingAddress=$order->getShippingAddress();
		 $shippingFirstName=$shippingAddress->getFirstname();//------------------收货人的姓
		 $shippingLastName=$shippingAddress->getLastname();//[-------------------收货人的名
		 $shippingEmail=$order->getData('customer_email');//[---------收货人的Email
		 $shippingPhone=$shippingAddress->getTelephone();//----------------收货人的固定电话
		 $shippingZipcode=$shippingAddress->getPostcode();//----------------收货人的邮编
		 $shippingAddress=$shippingAddress->getStreetFull();//-------------收货人具体地址
		
		//物品信息
		$products = "";
		$parameter = array('MerNo'           => $MerNo,
                           'DispAmount'        => $DispAmount,
                           'Remark'           => $Remark, 
                           'BillNo'      => $BillNo, // order ID
                           'Amount'             =>$Amount,
                           'Currency'      => $Currency,
                           'ReturnURL'          => $ReturnURL ,
                           'Language'      => $Language ,
						    
							 'firstname'	=> $FirstName,
								'lastname'	=>$LastName,
							   'email'  =>$Email,
							   'phone'	=>$Phone,
							   'zipcode'=>$ZipCode,
							   'address'=>$Address,
							   'city'=>$City,
							   'state'=>$State,
							   'shippingFirstName'=>$shippingFirstName,
							   'shippingLastName'=>$shippingLastName,
							   'shippingEmail'=>$shippingEmail,
							   'shippingPhone'=>$shippingPhone,
							   'shippingZipcode'=>$shippingZipcode,
							   'shippingAddress'=>$shippingAddress,
							   'shippingCity'=>$City,
							   'shippingSstate'=>$State,
							   'products'=>$products
                        );
							
		$MD5key = $this->getConfigData('security_code');
     $md5src = $MerNo.$BillNo.$Currency.$Amount.$Language.$ReturnURL.$MD5key;		//校验源字符串
     $MD5info = strtoupper(md5($md5src));		//MD5检验结果


	 
		$sign_type = 'MD5';
//        $md5src = $MerNo.$BillNo.$Currency.$Amount.$Language.$ReturnURL.$MD5key;		//校验源字符串
//        $MD5info = strtoupper(md5($md5src));		//MD5检验结果		
		
		$fields = array();
		$sort_array = array();
		$arg = "";
		$sort_array = $this->arg_sort($parameter); //$parameter
		while (list ($key, $val) = each ($sort_array)) {
			$fields[$key] = $this->charset_encode($val,'utf-8');
		}
		$fields['MD5info'] = $MD5info;


        return $fields;
    }
    
   
	
	public function arg_sort($array) {
		ksort($array);
		reset($array);
		return $array;
	}

	public function charset_encode($input,$_output_charset ,$_input_charset ="GBK" ) {
		$output = "";
		if($_input_charset == $_output_charset || $input ==null) {
			$output = $input;
		} elseif (function_exists("mb_convert_encoding")){
			$output = mb_convert_encoding($input,$_output_charset,$_input_charset);
		} elseif(function_exists("iconv")) {
			$output = iconv($_input_charset,$_output_charset,$input);
		} else die("sorry, you have no libs support for charset change.");
		return $output;
	}
   
	/**
	 * Return authorized languages by ecpss
	 *
	 * @param	none
	 * @return	array
	 */
	protected function _getAuthorizedLanguages()
	{
		$languages = array();
		
        foreach (Mage::getConfig()->getNode('global/payment/ecpss_payment/languages')->asArray() as $data) 
		{
			$languages[$data['code']] = $data['name'];
		}
		
		return $languages;
	}
	
	/**
	 * Return language code to send to ecpss
	 *
	 * @param	none
	 * @return	String
	 */
	protected function _getLanguageCode()
	{
		// Store language
		$language = strtoupper(substr(Mage::getStoreConfig('general/locale/code'), 0, 2));

		// Authorized Languages
		$authorized_languages = $this->_getAuthorizedLanguages();

		if (count($authorized_languages) === 1) 
		{
			$codes = array_keys($authorized_languages);
			return $codes[0];
		}
		
		if (array_key_exists($language, $authorized_languages)) 
		{
			return $language;
		}
		
		// By default we use language selected in store admin
		return $this->getConfigData('language');
	}



    /**
     *  Output failure response and stop the script
     *
     *  @param    none
     *  @return	  void
     */
    public function generateErrorResponse()
    {
        die($this->getErrorResponse());
    }

    /**
     *  Return response for ecpss success payment
     *
     *  @param    none
     *  @return	  string Success response string
     */
    public function getSuccessResponse()
    {
        $response = array(
            'Pragma: no-cache',
            'Content-type : text/plain',
            'Version: 1',
            'OK'
        );
        return implode("\n", $response) . "\n";
    }

    /**
     *  Return response for ecpss failure payment
     *
     *  @param    none
     *  @return	  string Failure response string
     */
    public function getErrorResponse()
    {
        $response = array(
            'Pragma: no-cache',
            'Content-type : text/plain',
            'Version: 1',
            'Document falsifie'
        );
        return implode("\n", $response) . "\n";
    }

}