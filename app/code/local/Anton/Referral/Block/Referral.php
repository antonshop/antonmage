<?php
class Anton_Referral_Block_Referral extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('referral/referral.phtml');
    }
    
    public function getFacebook(){
   		return Mage::getModel('referral/referral')->getFacebook();
    }
    
    /* get facebook url*/
    public function getFacebookLoginUrl(){
    	return Mage::getModel('referral/referral')->getFacebookLoginUrl();
    }

    /* get response url*/
    public function getResponseUrl($name){
    	switch ($name){
    		case 'fb':
    			$url = Mage::getUrl('referral/index/fbrespond');
    			break;
    		case 'tw':
    			$url = Mage::getUrl('referral/index/twrespond');
    			break;
    		default:
    			$url = Mage::getUrl('referral/index/gprespond');
    	}
    	return $url;
    }
    
    /* get twitter redirect url */
    public function getTwRedirecturl(){
    	return Mage::getUrl('referral/index/twredirect');
    } 
    
}
















