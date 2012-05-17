<?php

class Zizio_Powershare_Block_Adminhtml_Incentives extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_incentives';
		$this->_blockGroup = 'powershare';
		$this->_headerText = Mage::helper('powershare')->__('Power Share Incentives Management');
		//$this->_addButtonLabel = Mage::helper('powershare')->__('Add Social Deal');
		
		parent::__construct();
	}
	
	/**
	 * Prepare html output
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		try
		{
			// get generic html
			$html = parent::_toHtml();
			
			// add js
			$static_data = array(
				'pub_id'	 => Zizio_Powershare_Helper_Data::GetPublisherId(),
				'gmt_offset' => Zizio_Powershare_Helper_Data::GetGmtOffset(),
				'ext_type'	 => "powershare",
				'admin_loc'	 => "grid",
				'ext_ver'	 => Zizio_Powershare_Helper_Data::GetExtVer("")
			);
			$zizio_js = sprintf("<script type='text/javascript'> var z_statdata = %s; </script>",
							    Zizio_Powershare_Helper_Data::json_encode($static_data));
			if (Mage::registry("zizio/utils_js_added") == null)
			{
				$zizio_js .= Zizio_Powershare_Helper_Data::GetScriptBlock(Zizio_Powershare_Helper_Data::GetZUtilsScriptUrl());
				Mage::register("zizio/utils_js_added", true);
			}
			$zizio_js .= Zizio_Powershare_Helper_Data::GetScriptBlock(Zizio_Powershare_Helper_Data::GetAdminIncentiveGridScriptUrl());

			// Prepare the "Click Here" button and link below the powershare grid and append them to the grid HTML:
			$buttonAttributes = array(
				'label'		=>	Mage::helper('powershare')->__('Click Here!'),
				'onclick'	=>	'setLocation(\'' . $this->getCreateUrl() .'\')',
			);
			$buttonBlock = $this->getLayout()->createBlock('adminhtml/widget_button');
			$buttonBlock->addData($buttonAttributes);

			$html .= "<table width='100%' height='100'><tr><td style='text-align:center; vertical-align:middle;'>";
			$html .= $buttonBlock->toHtml();
			$html .= "<br />Create a new Incentive - <a href='".$this->getCreateUrl()."'>click here</a>!</td></tr></table>";
			
			// add additional buttons
			$html .= $this->getAdditionalHtml();
			
			return $zizio_js . $html;
		}
		catch (Exception $ex)
		{
			Zizio_Powershare_Helper_Data::LogError($ex);
			return parent::_toHtml();
		}
	}
	
	private function _getAdditionalButtonHtml($location, $label, $description, $new_tab=false)
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
	
	public function getAdditionalHtml()
	{
		$helper = Mage::helper('powershare');

		return '
<br/>
<div class="content-header">
    <table cellspacing="0">
        <tr>
            <td><h3>' . $helper->__('Additional Management') . '</h3></td><td class="form-buttons"></td>
         </tr>
    </table>
</div>
<table class="form-list">
	<tr><td class="scope-label">'
	 . '<a href="' . $helper->GetZizioAttachUrl() . '" target="_blank">Attach this store</a> to your Zizio Account and recieve in-depth sale statistics and tools<br/>'
	 . '<a href="' . $helper->GetZizioLoginUrl() . '" target="_blank">View your Zizio Account</a><br/>'
. '</tr></td>
</table>';

	}

	public function getButtonsHtml($area = null)
	{
		$configuration = '<a href="' . $this->getUrl('adminhtml/system_config/edit', array('section' => "powershare")) . '">Power Share Configuration</a>';
		return Zizio_Powershare_Helper_Data::GetSupportLinkHtml() . "&nbsp;|&nbsp;" . $configuration . parent::getButtonsHtml($area);
	}
}
