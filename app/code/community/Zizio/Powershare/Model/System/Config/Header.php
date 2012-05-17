<?php

class Zizio_Powershare_Model_System_Config_Header
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{

	/**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $element->getComment();
        if (!$html)
        	$html = $element->getText();
    	return 
	        "<div class=\"box\">
			    <p>{$html}</p>
			</div>";
    }
    
}
