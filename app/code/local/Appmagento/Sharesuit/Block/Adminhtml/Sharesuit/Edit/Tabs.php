<?php

class Appmagento_Sharesuit_Block_Adminhtml_Sharesuit_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('sharesuit_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('sharesuit')->__('News Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('sharesuit')->__('Item Information'),
          'title'     => Mage::helper('sharesuit')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('sharesuit/adminhtml_sharesuit_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}