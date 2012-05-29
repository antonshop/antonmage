<?php

class Appmagento_Sharesuit_Block_Adminhtml_Sharesuit_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'sharesuit';
        $this->_controller = 'adminhtml_sharesuit';
        
        $this->_updateButton('save', 'label', Mage::helper('sharesuit')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('sharesuit')->__('Delete Item'));
    }

    public function getHeaderText()
    {
        if( Mage::registry('sharesuit_data') && Mage::registry('sharesuit_data')->getId() ) {
            return Mage::helper('sharesuit')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('sharesuit_data')->getTitle()));
        } else {
            return Mage::helper('sharesuit')->__('Add Item');
        }
    }
}