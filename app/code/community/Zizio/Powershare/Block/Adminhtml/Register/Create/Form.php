<?php
class Zizio_Powershare_Block_Adminhtml_Register_Create_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
			'id'		 => 'edit_form',
			'action'	 => $this->getUrl('*/*/register'),
			'method'	 => 'post',
			'enctype'	 => 'multipart/form-data'));

		$header_html = "Why create a <a href='javascript:void(0);' onclick=\"alert('Zizio account enables you to:\\n\\n* Access your store\'s Power Share performance reports\\n* Change your store\'s settings such as: store name, personal info, mail and more...\\n* Upgrade / Downgrade your Power Share plan');\">Zizio Account</a>?";
		$header = new Varien_Data_Form_Element_Text();
		$header->setText($header_html)
			   ->setRenderer(new Zizio_Powershare_Model_System_Config_Header());
		$form->addElement($header);
		
		$fieldset = $form->addFieldset('zizio_registration', array());

		$user = Mage::getSingleton('admin/session')->getUser();
		
		$user_data = $user->getData();
		$user_data['username'] = $user_data['email'];
		$user_data['email']    = "";
		$user_data['password'] = "";
		$user_data['ext_ver']  = Zizio_Powershare_Helper_Data::GetExtVer();
		$user_data['ext_type'] = Zizio_Powershare_Helper_Data::GetExtType();
		$user_data['mage_ver'] = Mage::getVersion();
		
		$zizio_reg_script = Zizio_Powershare_Helper_Data::GetScriptBlock(Zizio_Powershare_Helper_Data::GetAdminRegisterScriptUrl());
	
		$fieldset->addField('username', 'text', array(
			'name'				 => 'username',
			'class'				 => 'validate-email',
			'label'				 => Mage::helper('adminhtml')->__('Email'),
			'title'				 => Mage::helper('adminhtml')->__('User Email'),
			'required'			 => true,
			'after_element_html' => "
				<script type='text/javascript'>
					Validation.add('validate-zizio-username-exists', 'Username already exists. If it\'s your account, try to login using the link at the top', function(v) {
						return false;
					});
				</script>"
		));
		
		$fieldset->addField('password', 'password', array(
			'name'				 => 'password',
			'class'				 => 'validate-password',
			'label'				 => Mage::helper('powershare')->__('Password'),
			'title'				 => Mage::helper('powershare')->__('Password'),
			'required'			 => true
		));

		$submitButton = $this->getLayout()->createBlock('adminhtml/widget_button');
		$submitButton->addData(array(
			'class'		 => 'save',
			'label'		 => Mage::helper('powershare')->__('Create a free account'),
			'onclick'	 => "return saveOnly();"
		));
		
		$fieldset->addField('password2', 'password', array(
			'name'				 => 'password2',
			'class'				 => 'validate-zizio-passwords-match zizio-new-user',
			'label'				 => Mage::helper('powershare')->__('Validate Password'),
			'title'				 => Mage::helper('powershare')->__('Validate Password'),
			'required'			 => true,
			'after_element_html' => "<script type='text/javascript'>
					Validation.add('validate-zizio-passwords-match', 'Passwords must match.', function(v) {
	    				if (\$F('password').strip() != \$F('password2').strip())
							return false;
						else
							return true;
					});
				</script>
				</td></tr><tr><td colspan='2' style='text-align: right; padding-top: 4px;'>" .
				$submitButton->toHtml() .
				$zizio_reg_script
		));
	
		$fieldset->addField('mage_ver', 'hidden', array(
			'required'	 => false,
			'name'		 => 'mage_ver',
		));

		$fieldset->addField('ext_type', 'hidden', array(
			'required'	 => false,
			'name'		 => 'ext_type',
		));

		$fieldset->addField('ext_ver', 'hidden', array(
			'required'	 => false,
			'name'		 => 'ext_ver',
		));
		
		$form->setUseContainer(true);
		$this->setForm($form);
		$form->setValues($user_data);
		return parent::_prepareForm();
	}
}
