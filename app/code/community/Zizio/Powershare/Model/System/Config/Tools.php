<?php

class Zizio_Powershare_Model_System_Config_Tools
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{

	/**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
        $helper = Mage::helper('powershare');
		
		$html = $this->_getHeaderHtml($element);

		$html .= $this->_getButtonHtml($helper->GetZizioAttachUrl(), $helper->__('Attach to Zizio Account'), $helper->__('Attach this store to your Zizio Account to recieve in-depth SHARE statistics and tools'), true);

        $html .= $this->_getFooterHtml($element);

        return $html;
	}
    
	private function _getButtonHtml($location, $label, $description, $new_tab=false)
	{
		if ($new_tab)
			$button = "<a target=\"_blank\" href=\"{$location}\"><button type=\"button\" class=\"scalable\"><span>{$label}</span></button></a>";
		else
			$button = "<button onclick=\"setLocation('{$location}')\" type=\"button\" class=\"scalable\"><span>{$label}</span></button>";
		
		return <<<EOF
<tr>
	<td class="scope-label">
		{$button}
	</td>
	<td class="scope-label">
		{$description}
	</td>
</tr>
EOF;
	}

}
