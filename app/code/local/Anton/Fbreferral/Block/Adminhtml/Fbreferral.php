<?php
class Anton_Fbreferral_Block_Adminhtml_Fbreferral extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_fbreferral';
    $this->_blockGroup = 'fbreferral';
    $this->_headerText = Mage::helper('fbreferral')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('fbreferral')->__('Add Item');
    parent::__construct();
  }
}