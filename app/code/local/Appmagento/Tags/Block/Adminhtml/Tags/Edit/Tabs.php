<?php

class Appmagento_Tags_Block_Adminhtml_Tags_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('tags_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('tags')->__('News Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('tags')->__('Item Information'),
          'title'     => Mage::helper('tags')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('tags/adminhtml_tags_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}