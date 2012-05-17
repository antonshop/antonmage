<?php

class Zizio_Powershare_Model_System_Config_Field extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
    	$inherit = Mage::registry("{$element->getId()}_inherit");
        if ($inherit !== null)
        	$element->setInherit($inherit ? "1" : "0");
    	
    	return parent::render($element);
    }

}