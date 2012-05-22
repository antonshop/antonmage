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

    /*Get Facebook Response Url*/
    public function getResponseUrl(){
    	return Mage::getUrl('referral/index/fbrespond');
    }
    
}