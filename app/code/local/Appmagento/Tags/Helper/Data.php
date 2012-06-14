<?php
/**
 * Appmagento
 *
 * @category    Appmagento
 * @package     Appmagento_Tags
 * @copyright   Copyright (c) 2011 http://www.appmagento.com
 */
class Appmagento_Tags_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_ENABLED = 'tags/tags/enabled';
    const XML_PATH_TITLE = 'tags/tags/title';
    const XML_PATH_MENU_LEFT = 'tags/tags/menuLeft';
    const XML_PATH_MENU_RIGHT = 'tags/tags/menuRoght';
    const XML_PATH_FOOTER_ENABLED = 'tags/tags/footerEnabled';
    const XML_PATH_LAYOUT = 'tags/tags/layout';

    public function getEnabled() {
        return Mage::getStoreConfig(self::XML_PATH_ENABLED);
    }
    
    public function getTitle() {
    	$title = Mage::getStoreConfig(self::XML_PATH_TITLE);
    	if(!$title){
    		$title = 'Tags';
    	}
    	return $title;
    }
    
    public function getLayout() {
    	return Mage::getStoreConfig(self::XML_PATH_LAYOUT);
    }
}