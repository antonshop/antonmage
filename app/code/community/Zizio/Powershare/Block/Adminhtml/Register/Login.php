<?php
class Zizio_Powershare_Block_Adminhtml_Register_Login extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		
		$this->_blockGroup = 'powershare';
		$this->_controller = 'adminhtml_Register';
		$this->_mode = 'Login';
		$this->_headerText = Mage::helper('powershare')->__('Login to your Zizio Account');

		$this->_removeButton('back');
		$this->_removeButton('reset');
		$this->_removeButton('save');

		$this->_formScripts[] = "
			function NoConnection ()
			{
				alert('Failed to connect to Zizio server, please try again later');
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
			
			var zizio_reg_action = 'login';
		";
	}

	public function getHeaderHtml()
	{
		$create_url = $this->getUrl('*/adminhtml_ZizioRegister/create');
		$htmlToAppend = "<p style='float: left; margin: 4px 0 0 10px;'>Don't have an account? <a href='{$create_url}'>Create an Account</a></p>";
		return parent::getHeaderHtml() . $htmlToAppend;
	}
	
	public function getButtonsHtml($area = null)
	{
		return Zizio_Powershare_Helper_Data::GetSupportLinkHtml() . parent::getButtonsHtml($area);
	}
}
