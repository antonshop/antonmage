<?php

class Appmagento_Sharesuit_Block_Adminhtml_Sharesuit_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('sharesuit_form', array('legend'=>Mage::helper('sharesuit')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('sharesuit')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('sharesuit')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('sharesuit')->__('Active'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('sharesuit')->__('Inactive'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('sharesuit')->__('Content'),
          'title'     => Mage::helper('sharesuit')->__('Content'),
          'style'     => 'width:98%; height:400px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getSharesuitData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getSharesuitData());
          Mage::getSingleton('adminhtml/session')->setSharesuitData(null);
      } elseif ( Mage::registry('sharesuit_data') ) {
          $form->setValues(Mage::registry('sharesuit_data')->getData());
      }
      return parent::_prepareForm();
  }
}