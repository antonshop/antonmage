<?php

class Zizio_Powershare_Model_System_Config_PowerShareBars
	extends Zizio_Powershare_Model_System_Config_PowerShareField
{
	
    /**
     * Render element html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
    	$html = parent::render($element);
    	$html .= '<tr>
        		<td colspan="4">
        			<table cellspacing="0" class="form-list">
        				<colgroup class="label"></colgroup>
        				<colgroup class="value"></colgroup>
        				<tbody id="z-powers-bars">
        				</tbody>
        			</table>
    				<script type="text/javascript">' . $this->_getJs($element) . '</script>
        		</td>
        	</tr>';
    	return $html;
    }
    
}
