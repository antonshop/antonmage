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
    
    /*Get Facebook Url*/
    public function getFacebookLoginUrl(){
    	return Mage::getModel('referral/referral')->getFacebookLoginUrl();
    }

    /*Get Response Url*/
    public function getResponseUrl($name){
    	switch ($name){
    		case 'fb':
    			$url = Mage::getUrl('referral/index/fbrespond');
    		case 'tw':
    			$url = Mage::getUrl('referral/index/twrespond');
    		default:
    			$url = Mage::getUrl('referral/index/gprespond');
    	}
    	return $url;
    }
    
}