<?php

class Wt_Find_Block_Adminhtml_Find_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('find_form', array('legend'=>Mage::helper('find')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('find')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('find')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('find')->__('Active'),
              ),

              array(
                  'value'     => 0,
                  'label'     => Mage::helper('find')->__('Inactive'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('find')->__('Content'),
          'title'     => Mage::helper('find')->__('Content'),
          'style'     => 'width:98%; height:400px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getFindData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getFindData());
          Mage::getSingleton('adminhtml/session')->setFindData(null);
      } elseif ( Mage::registry('find_data') ) {
          $form->setValues(Mage::registry('find_data')->getData());
      }
      return parent::_prepareForm();
  }
}