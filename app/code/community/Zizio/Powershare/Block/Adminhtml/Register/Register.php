<?php
class Zizio_Powershare_Block_Adminhtml_Register_Register extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();

		$this->_blockGroup = 'powershare';
		$this->_controller = 'adminhtml_Register';
		$this->_mode = 'Register';
		$this->_headerText = Mage::helper('powershare')->__('Zizio Store Registration');

		$this->_removeButton('back');
		$this->_removeButton('reset');

		$this->_updateButton('save', 'label', Mage::helper('powershare')->__('I accept. Register me!'));
		$this->_updateButton('save', 'onclick', "saveOnly();");

		$this->_formScripts[] = "
			function NoConnection ()
			{
				alert('Failed to connect to Zizio server, try again after refresh, or contact support@zizio.com');
			}

			function saveOnly()
			{
				if (typeof(zizio_mng) == 'object')
				{
					return zizio_mng.Submit(editForm, '');
				}
				else
				{
					NoConnection ();
				}
				return false;
			}

			var zizio_reg_action = 'register';
		";
	}

	/**
	 * Prepare html output
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		// Prepare the "I Accept. Register Me." button below the registration form grid and append them to the grid HTML:
		$buttonAttributes = array(
			'class'		 => 'save',
			'label'		 => Mage::helper('powershare')->__('I Accept. Register Me!'),
			'onclick'	 => "saveOnly();",
		);
		$buttonBlock = $this->getLayout()->createBlock('adminhtml/widget_button');
		$buttonBlock->addData($buttonAttributes);
		$htmlToAppend = "<table border='0' width='760'><tr><td style='vertical-align:middle;'>";
		$htmlToAppend .= '<p>By clicking on \'I accept. Register me.\' you agree to the <a href="javascript:void(0);" onclick="zizio_mng.PrivacyPolicy();">Privacy Policy</a> and to the \'Terms of Service\' above.&nbsp;&nbsp;&nbsp;' . $buttonBlock->toHtml() . '</p>';
		$htmlToAppend .= "</td></tr></table>";
		return parent::_toHtml() . $htmlToAppend;
	}

	public function getButtonsHtml($area = null)
	{
		return Zizio_Powershare_Helper_Data::GetSupportLinkHtml() . parent::getButtonsHtml($area);
	}
}
