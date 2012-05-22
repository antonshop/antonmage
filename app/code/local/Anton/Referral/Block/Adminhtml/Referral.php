<?php
class Anton_Referral_Block_Adminhtml_Referral extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_referral';
    $this->_blockGroup = 'referral';
    $this->_headerText = Mage::helper('referral')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('referral')->__('Add Item');
    parent::__construct();
  }
}