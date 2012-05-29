<?php
/**
 * Appmagento
 *
 * @category    Appmagento
 * @package     Appmagento_Sharesuit
 * @copyright   Copyright (c) 2011 http://www.appmagento.com
 */

class Appmagento_Sharesuit_IndexController extends Mage_Core_Controller_Front_Action
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
    
    public function twredirectAction(){
    	
    	$twitter = Mage::getModel('sharesuit/sharesuit')->getTwitterOauth();
		$request_token = $twitter->getRequestToken(Mage::getBlockSingleton('sharesuit/sharesuit')->getResponseUrl('tw'));
		
		/* Save temporary credentials to session. */
		$_SESSION['oauth_token'] = $request_token['oauth_token'];
		$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

    	if($twitter->http_code == 200){
    		$url = Mage::getModel('sharesuit/sharesuit')->getTwUrl();
		    return $this->_redirectUrl($url);
    	} else {
    		Mage::getSingleton('core/session')->addError($this->__('Could not connect to Twitter. Refresh the page or try again later.'));
			return $this->_redirectUrl($this->getrUrl());
    	}
    }
	
	public function twrespondAction(){

		$twitter = Mage::getModel('sharesuit/sharesuit')->getUserTwitterOauth();
		$access_token = $twitter->getAccessToken($_REQUEST['oauth_verifier']);;
		$_SESSION['access_token'] = $access_token;
		
		/* get facebook configuration discount */
		$fb_discount_amount = Mage::getModel('sharesuit/sharesuit')->getFbDiscountamount();
		$fb_order_amount = Mage::getModel('sharesuit/sharesuit')->getFbOrderamount();
		$basemin_order_amount = Mage::helper('core')->currency($fb_order_amount, true, true);
		$basediscount_amount = $fb_discount_amount;
		$fb_discount_amount = Mage::helper('core')->currency($fb_discount_amount, false, false);
		
		$subTotal = $this->getSubtotal();
		if(isset($_SESSION['oauth_token']))unset($_SESSION['oauth_token']);
		if(isset($_SESSION['oauth_token_secret']))unset($_SESSION['oauth_token_secret']);

		if (200 == $twitter->http_code && $subTotal >= $fb_discount_amount && $subTotal >= $fb_order_amount) {
			
			/*set the twitter user status*/
			Mage::getModel('sharesuit/sharesuit')->setSharesuituser($access_token['user_id'],2);
			
			/* The user has been verified and the access tokens can be saved for future use */
			//$_SESSION['status'] = 'verified';
			$getproductId = self::getProductsIds();
			foreach($getproductId as $pid){
				$product = Mage::getModel('catalog/product')->load($pid);
				$message = str_replace('{product_name}', $product->getName(), Mage::getModel('sharesuit/sharesuit')->getFbFeedmassage());
				$twitter->post('statuses/update', array('status' => $message));
			}
			
			$status = Mage::getModel('sharesuit/sharesuit')->getSharesuituserCount($access_token['user_id'],2);
			$this->successMessage($status, $basediscount_amount);
			//Mage::getSingleton('core/session')->addSuccess($this->__('Congratulations! You have redeemed ' . ' ' . Mage::helper('core')->currency($basediscount_amount, true, true) . ' ' . ' for sharing product(s) to your friends.'));
	
			return $this->_redirectUrl($this->getrUrl());
		} else if($subTotal < $fb_order_amount) {
			
			/*Error message on cart page*/
			Mage::getSingleton('core/session')->addError($this->__('Your order amount should be minimum' . ' ' . $basemin_order_amount . ' ' . 'to get Sharesuit discount!'));
			return $this->_redirectUrl($this->getrUrl());
		
		} else {
			
			/*Error message on cart page*/
			Mage::getSingleton('core/session')->addError($this->__('Could not connect to Twitter. Refresh the page or try again later.'));
			return $this->_redirectUrl($this->getrUrl());
		}
		return $this->_redirectUrl($this->getrUrl());
	}
	    
	public function fbrespondAction(){
		
		/* get facebook */
		$facebook = Mage::getModel('sharesuit/sharesuit')->getFacebook();
		$fbuser = $facebook->getUser();
		
		/* get facebook configuration discount */
		$fb_discount_amount = Mage::getModel('sharesuit/sharesuit')->getFbDiscountamount();
		$fb_order_amount = Mage::getModel('sharesuit/sharesuit')->getFbOrderamount();
		$basemin_order_amount = Mage::helper('core')->currency($fb_order_amount, true, true);
		$basediscount_amount = $fb_discount_amount;
		$fb_discount_amount = Mage::helper('core')->currency($fb_discount_amount, false, false);

		$subTotal = $this->getSubtotal();

		if($fbuser && $subTotal >= $fb_discount_amount && $subTotal >= $fb_order_amount){
			$getproductId = self::getProductsIds();
			try {
				foreach($getproductId as $pid){
					$product = Mage::getModel('catalog/product')->load($pid);
					$message = str_replace('{product_name}', $product->getName(),Mage::getModel('sharesuit/sharesuit')->getFbFeedmassage());
					$feedinfo = array(
						'message'		=> $message,
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
			Mage::getModel('sharesuit/sharesuit')->setSharesuituser($fbuser);
			/*get the facebook user status*/
			$status = Mage::getModel('sharesuit/sharesuit')->getSharesuituserCount($fbuser);
			
			$this->successMessage($status, $basediscount_amount);
			
			return $this->_redirectUrl($this->getrUrl());
		} else {
			/*Error message on cart page*/
			Mage::getSingleton('core/session')->addError($this->__('Your order amount should be minimum' . ' ' . $basemin_order_amount . ' ' . 'to get Sharesuit discount!'));
			return $this->_redirectUrl($this->getrUrl());
		}
	}
	
	public function successMessage($status, $amount){
		if ($status <= 1) {
			/* success message first time */
			Mage::getSingleton('core/session')->addSuccess($this->__('Congratulations! You have got ' . ' ' . Mage::helper('core')->currency($amount, true, true) . ' ' . ' discount for sharing our products with your friends.'));

			return $this->_redirectUrl($this->getrUrl());
		} else {
			/* success message more time */
			Mage::getSingleton('core/session')->addSuccess($this->__('Thank you for sharing products with your friends.'));

			return $this->_redirectUrl($this->getrUrl());
		}
	}
	
	public function gprespondAction(){
		
	}
	
}