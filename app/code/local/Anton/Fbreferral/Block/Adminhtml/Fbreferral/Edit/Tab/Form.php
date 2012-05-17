<?php

class Anton_Fbreferral_Block_Adminhtml_Fbreferral_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('fbreferral_form', array('legend'=>Mage::helper('fbreferral')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('fbreferral')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('fbreferral')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('fbreferral')->__('Active'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('fbreferral')->__('Inactive'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('fbreferral')->__('Content'),
          'title'     => Mage::helper('fbreferral')->__('Content'),
          'style'     => 'width:98%; height:400px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getFbreferralData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getFbreferralData());
          Mage::getSingleton('adminhtml/session')->setFbreferralData(null);
      } elseif ( Mage::registry('fbreferral_data') ) {
          $form->setValues(Mage::registry('fbreferral_data')->getData());
      }
      return parent::_prepareForm();
  }
}