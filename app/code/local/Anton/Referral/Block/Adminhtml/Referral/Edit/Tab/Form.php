<?php

class Anton_Referral_Block_Adminhtml_Referral_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('referral_form', array('legend'=>Mage::helper('referral')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('referral')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('referral')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('referral')->__('Active'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('referral')->__('Inactive'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('referral')->__('Content'),
          'title'     => Mage::helper('referral')->__('Content'),
          'style'     => 'width:98%; height:400px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getReferralData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getReferralData());
          Mage::getSingleton('adminhtml/session')->setReferralData(null);
      } elseif ( Mage::registry('referral_data') ) {
          $form->setValues(Mage::registry('referral_data')->getData());
      }
      return parent::_prepareForm();
  }
}