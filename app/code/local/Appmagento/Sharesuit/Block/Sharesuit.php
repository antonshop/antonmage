<?php
/**
 * Appmagento
 *
 * @category    Appmagento
 * @package     Appmagento_Sharesuit
 * @copyright   Copyright (c) 2011 http://www.appmagento.com
 */

class Appmagento_Sharesuit_Block_Sharesuit extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('sharesuit/sharesuit.phtml');
    }
    
    public function getFacebook(){
   		return Mage::getModel('sharesuit/sharesuit')->getFacebook();
    }
    
    /* get facebook url*/
    public function getFacebookLoginUrl(){
    	return Mage::getModel('sharesuit/sharesuit')->getFacebookLoginUrl();
    }

    /* get response url*/
    public function getResponseUrl($name){
    	switch ($name){
    		case 'fb':
    			$url = Mage::getUrl('sharesuit/index/fbrespond');
    			break;
    		case 'tw':
    			$url = Mage::getUrl('sharesuit/index/twrespond');
    			break;
    		default:
    			$url = Mage::getUrl('sharesuit/index/gprespond');
    	}
    	return $url;
    }
    
    /* get twitter redirect url */
    public function getTwRedirecturl(){
    	return Mage::getUrl('sharesuit/index/twredirect');
    } 
    
}
















