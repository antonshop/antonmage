<?php
class Zizio_Powershare_Block_Adminhtml_Register_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		
		$this->_blockGroup = 'powershare';
		$this->_controller = 'adminhtml_Register';
		$this->_mode = 'Create';
		$this->_headerText = Mage::helper('powershare')->__('Create a Zizio Account, It\'s free!');

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
			
			var zizio_reg_action = 'create';
		";
	}

	public function getHeaderHtml()
	{
		$login_url = $this->getUrl('*/adminhtml_ZizioRegister/login');
		$htmlToAppend = "<p style='float: left; margin: 4px 0 0 10px;'>Already have an account? <a href='{$login_url}'>Login</a></p>";
		return parent::getHeaderHtml() . $htmlToAppend;
	}
	
	public function getButtonsHtml($area = null)
	{
		return Zizio_Powershare_Helper_Data::GetSupportLinkHtml() . parent::getButtonsHtml($area);
	}
}
