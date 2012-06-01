<?php
/**
 * Appmagento
 *
 * @category    Appmagento
 * @package     Appmagento_Sharesuit
 * @copyright   Copyright (c) 2011 http://www.appmagento.com
 */

class Appmagento_Sharesuit_Model_Pinterest_Pinterest extends Mage_Core_Model_Abstract
{
    /* get pinterest config */
    public function getPinterest(){
    	return Mage::getStoreConfig('sharesuit/pinterest/pin_enable');
    }
    
	public function getPinLayout(){
    	return Mage::getStoreConfig('sharesuit/pinterest/pin_count_layout');
    }
    
	public function getPinPrice(){
    	return Mage::getStoreConfig('sharesuit/pinterest/pin_price');
    }
}
?>