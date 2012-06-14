<?php

class Appmagento_Tags_Block_Adminhtml_Tags_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'tags';
        $this->_controller = 'adminhtml_tags';
        
        $this->_updateButton('save', 'label', Mage::helper('tags')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('tags')->__('Delete Item'));
    }

    public function getHeaderText()
    {
        if( Mage::registry('tags_data') && Mage::registry('tags_data')->getId() ) {
            return Mage::helper('tags')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('tags_data')->getTitle()));
        } else {
            return Mage::helper('tags')->__('Add Item');
        }
    }
}