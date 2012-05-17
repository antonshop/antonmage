<?php

class Zizio_Powershare_Model_System_Config_PowerShareButtons
	extends Zizio_Powershare_Model_System_Config_PowerShareField
{
	
    /**
     * Enter description here...
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html = parent::_getElementHtml($element);
        $html .= '<div id="z-powers-buttons"></div><script type="text/javascript">' . $this->_getJs($element) . '</script>';
        return $html;
    }
    
}
