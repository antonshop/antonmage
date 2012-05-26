<?php

class Anton_Referral_Block_Adminhtml_Referral_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'referral';
        $this->_controller = 'adminhtml_referral';
        
        $this->_updateButton('save', 'label', Mage::helper('referral')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('referral')->__('Delete Item'));
    }

    public function getHeaderText()
    {
        if( Mage::registry('referral_data') && Mage::registry('referral_data')->getId() ) {
            return Mage::helper('referral')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('referral_data')->getTitle()));
        } else {
            return Mage::helper('referral')->__('Add Item');
        }
    }
}