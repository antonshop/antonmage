<?php
class Anton_Fbreferral_IndexController extends Mage_Core_Controller_Front_Action
{
	
	/*redirect url*/
	public function getrUrl(){
		return Mage::getBaseUrl() . "checkout/cart";
	}	
		
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function getProductsIds(){
    	/* get the cart products */
		$checkoutsession = Mage::getSingleton('checkout/session');
		
		/* get cart products id */
		$productsid = array();
		foreach ($checkoutsession->getQuote()->getAllItems() as $item) {
			$getproductId[] = $item->getProductId();
		}
		return $getproductId;
    }
    
    public function getSubtotal(){
    	/* get the cart subtotal */
		$lastQid = Mage::getSingleton('checkout/session')->getQuoteId();
		$customerQuote = Mage::getModel('sales/quote')->load($lastQid);
		return $subTotal = $customerQuote->getSubtotal();
    }
    
	public function fbrespondAction(){
		
		/* get facebook */
		$facebook = Mage::getModel('fbreferral/fbreferral')->getFacebook();
		$fbuser = $facebook->getUser();
		
		/* get facebook configuration discount */
		$fb_discount_amount = Mage::getStoreConfig('fbreferral/general/fb_discount_amount');
		$fb_order_amount = Mage::getStoreConfig('fbreferral/general/fb_order_amount');
		$basemin_order_amount = Mage::helper('core')->currency($fb_order_amount, true, true);
		$basediscount_amount = $fb_discount_amount;
		$fb_discount_amount = Mage::helper('core')->currency($fb_discount_amount, false, false);

		$subTotal = $this->getSubtotal();

		//echo $fbuser .'**'. $fb_discount_amount .'**'. $fb_order_amount. '**sbuTotal'.$subTotal;exit;
		if($fbuser && $subTotal >= $fb_discount_amount && $subTotal >= $fb_order_amount){
			$getproductId = self::getProductsIds();
			try {
				foreach($getproductId as $pid){
					$product = Mage::getModel('catalog/product')->load($pid);
					$feedinfo = array(
						'message'		=> Mage::getModel('fbreferral/fbreferral')->getFbFeedmassage(),
						'name'			=> $product->getName(),
						'link'			=> $product->getProductUrl(),
						'picture'		=> $product->getImageUrl(),
						'description'	=> $product->getShortDescription()
					);
					$facebook->api("/$fbuser/feed", 'post', $feedinfo);
				}
				
			} catch (FacebookApiException $e) {
				/* error message */
				Mage::getSingleton('core/session')->addError($this->__('You declined the facebook app permission'));

				return $this->_redirectUrl($this->getrUrl());
			}
			
			/*set the facebook user status*/
			Mage::getModel('fbreferral/fbreferral')->setReferraluser($fbuser);
			/*get the facebook user status*/
			$status = Mage::getModel('fbreferral/fbreferral')->getReferraluserCount($fbuser);
			
			
			if ($status <= 1) {
				/* success message first time */
				Mage::getSingleton('core/session')->addSuccess($this->__('Congratulations! You have redeemed ' . ' ' . Mage::helper('core')->currency($basediscount_amount, true, true) . ' ' . ' for sharing product(s) to your friends.'));

				return $this->_redirectUrl($this->getrUrl());
			} else {
				/* success message more time */
				Mage::getSingleton('core/session')->addSuccess($this->__('Thanks for sharing product(s) to your friends.'));

				return $this->_redirectUrl($this->getrUrl());
			}
			
			return $this->_redirectUrl($this->getrUrl());
		} else {
			/*Error message on cart page*/
			Mage::getSingleton('core/session')->addError($this->__('Your order amount should be minimum' . ' ' . $basemin_order_amount . ' ' . 'to get FB Referral discount!'));
			return $this->_redirectUrl($this->getrUrl());
		}
	}
	
	public function twrespondAction(){
		
	}
	
	public function gprespondAction(){
		
	}
	
}