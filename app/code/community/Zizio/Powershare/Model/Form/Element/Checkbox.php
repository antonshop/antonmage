<?php

class Zizio_Powershare_Model_Form_Element_Checkbox extends Varien_Data_Form_Element_Checkbox
{

    public function getHtml()
    {
        $html = $this->getData('default_html');
        if (is_null($html)) {
            $html = ( $this->getNoSpan() === true ) ? '' : '<span class="field-row" style="'.$this->getStyle().'">'."\n";
            $style = "float: left; margin-right: 2px; margin-top: 2px;";
            $this->setData('style', $style);
            $html.= $this->getElementHtml();
            $html.= $this->getLabelHtml();
            $html.= ( $this->getNoSpan() === true ) ? '' : '</span>'."\n";
        }
        return $html;
    }

}
