<?php
class Anton_Fbreferral_Block_Fbreferral extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('fbreferral/fbreferral.phtml');
    }
    
    public function getFacebook(){
   		return Mage::getModel('fbreferral/fbreferral')->getFacebook();
    }
    
    /*Get Facebook Url*/
    public function getFacebookLoginUrl(){
    	return Mage::getModel('fbreferral/fbreferral')->getFacebookLoginUrl();
    }

    /*Get Facebook Response Url*/
    public function getResponseUrl(){
    	return Mage::getUrl('fbreferral/index/response');
    }
    
}