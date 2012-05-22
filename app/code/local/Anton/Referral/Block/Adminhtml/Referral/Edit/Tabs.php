<?php

class Anton_Referral_Block_Adminhtml_Referral_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('referral_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('referral')->__('News Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('referral')->__('Item Information'),
          'title'     => Mage::helper('referral')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('referral/adminhtml_referral_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}