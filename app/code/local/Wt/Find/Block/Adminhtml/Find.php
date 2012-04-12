<?php
class Wt_Find_Block_Adminhtml_Find extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_find';
    $this->_blockGroup = 'find';
    $this->_headerText = Mage::helper('find')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('find')->__('Add Item');
    parent::__construct();
  }
}