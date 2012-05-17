<?php

class Anton_Fbreferral_Block_Adminhtml_Fbreferral_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'fbreferral';
        $this->_controller = 'adminhtml_fbreferral';
        
        $this->_updateButton('save', 'label', Mage::helper('fbreferral')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('fbreferral')->__('Delete Item'));
    }

    public function getHeaderText()
    {
        if( Mage::registry('fbreferral_data') && Mage::registry('fbreferral_data')->getId() ) {
            return Mage::helper('fbreferral')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('fbreferral_data')->getTitle()));
        } else {
            return Mage::helper('fbreferral')->__('Add Item');
        }
    }
}