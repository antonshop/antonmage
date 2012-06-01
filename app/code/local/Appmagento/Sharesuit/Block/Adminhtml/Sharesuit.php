<?php
/**
 * Appmagento
 *
 * @category    Appmagento
 * @package     Appmagento_Sharesuit
 * @copyright   Copyright (c) 2011 http://www.appmagento.com
 */

class Appmagento_Sharesuit_Block_Adminhtml_Sharesuit extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_sharesuit';
    $this->_blockGroup = 'sharesuit';
    $this->_headerText = Mage::helper('sharesuit')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('sharesuit')->__('Add Item');
    parent::__construct();
  }
}