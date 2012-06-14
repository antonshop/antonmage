<?php
class Appmagento_Tags_Block_Adminhtml_Tags extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_tags';
    $this->_blockGroup = 'tags';
    $this->_headerText = Mage::helper('tags')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('tags')->__('Add Item');
    parent::__construct();
  }
}