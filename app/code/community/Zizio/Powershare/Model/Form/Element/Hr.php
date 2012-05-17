<?php

class Zizio_Powershare_Model_Form_Element_Hr
	extends Varien_Data_Form_Element_Abstract
	implements Varien_Data_Form_Element_Renderer_Interface
{
    
    public function getHtml()
    {
    	$html = '<span id="'.$this->getHtmlId().'" class="field-row" style="border-bottom: 1px solid #CCCCCC;">'."\n";
        $html.= '</span>'."\n";

        return $html;
    }
    
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
    	$html_id = $this->getHtmlId();
    	$this->setHtmlId($element->getId());
    	$html = "<tr>
    				<td colspan='4'>
    					" . $this->getHtml() . "
    				</td>
    			</tr>";
    	$this->setHtmlId($html_id);
    	return $html;
    }

}
