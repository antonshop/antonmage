<?php

class Zizio_Powershare_Model_Form_Element_Position extends Varien_Data_Form_Element_Select
{

	public function getOptions()
	{
        return array(
            'before' => Mage::helper('powershare')->__('Before'),
            'top' => Mage::helper('powershare')->__('Top'),
            'bottom' => Mage::helper('powershare')->__('Bottom'),
            'after' => Mage::helper('powershare')->__('After')
        );
    }
	
	public function toOptionArray()
    {
        return $this->getOptions();
    }
    
}