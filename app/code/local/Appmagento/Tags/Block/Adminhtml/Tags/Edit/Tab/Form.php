<?php

class Appmagento_Tags_Block_Adminhtml_Tags_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('tags_form', array('legend'=>Mage::helper('tags')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('tags')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('tags')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('tags')->__('Active'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('tags')->__('Inactive'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('tags')->__('Content'),
          'title'     => Mage::helper('tags')->__('Content'),
          'style'     => 'width:98%; height:400px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getTagsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getTagsData());
          Mage::getSingleton('adminhtml/session')->setTagsData(null);
      } elseif ( Mage::registry('tags_data') ) {
          $form->setValues(Mage::registry('tags_data')->getData());
      }
      return parent::_prepareForm();
  }
}