<?php

class Anton_Fbreferral_Block_Adminhtml_Fbreferral_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('fbreferral_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('fbreferral')->__('News Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('fbreferral')->__('Item Information'),
          'title'     => Mage::helper('fbreferral')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('fbreferral/adminhtml_fbreferral_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}