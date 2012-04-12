<?php

class Wt_Find_Block_Adminhtml_Find_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'find';
        $this->_controller = 'adminhtml_find';
        
        $this->_updateButton('save', 'label', Mage::helper('find')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('find')->__('Delete Item'));
    }

    public function getHeaderText()
    {
        if( Mage::registry('find_data') && Mage::registry('find_data')->getId() ) {
            return Mage::helper('find')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('find_data')->getTitle()));
        } else {
            return Mage::helper('find')->__('Add Item');
        }
    }
}