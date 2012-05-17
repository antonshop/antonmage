<?php
class Zizio_Powershare_Block_Adminhtml_Register_Register_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
			'id'		 => 'edit_form',
			'action'	 => $this->getUrl('*/*/save'),
			'method'	 => 'post',
			'enctype'	 => 'multipart/form-data'));

		$username = Mage::getSingleton('admin/session')->getZizioUsername();
		$login_url = $this->getUrl('*/adminhtml_ZizioRegister/login');
		$header_html = "Adding a store to Zizio Account <b>{$username}</b> <a href=\"{$login_url}\">Switch account</a>";
		$header = new Varien_Data_Form_Element_Text();
		$header->setText($header_html)
			   ->setRenderer(new Zizio_Powershare_Model_System_Config_Header());
		$form->addElement($header);
		
		$fieldset = $form->addFieldset('zizio_registration', array('legend' => Mage::helper('powershare')->__('Store Details')));

		$userId = Mage::getSingleton('admin/session')->getUser()->getId();
		$user = Mage::getModel('admin/user')->load($userId);
		$user_data = $user->getData();
		$user_data['ext_ver']  	= Zizio_Powershare_Helper_Data::GetExtVer();
		$user_data['ext_type'] 	= Zizio_Powershare_Helper_Data::GetExtType();
		$user_data['storename'] = Zizio_Powershare_Helper_Data::GetDefaultWebsiteName();

		$zizio_reg_script = Zizio_Powershare_Helper_Data::GetScriptBlock(Zizio_Powershare_Helper_Data::GetAdminRegisterScriptUrl());
	
		$fieldset->addField('storename', 'text', array(
			'name'				 => 'storename',
			'label'				 => Mage::helper('powershare')->__('Store Name'),
			'title'				 => Mage::helper('powershare')->__('Store Name'),
			'required'			 => true,
			'after_element_html' => $zizio_reg_script . "<script type='text/javascript'>
					window.zizio_create_account_url = '" . $this->getUrl('*/adminhtml_ZizioRegister/create') . "';
				</script>"
		));

		$fieldset->addField('firstname', 'text', array(
			'name'		 => 'firstname',
			'label'		 => Mage::helper('powershare')->__('First Name'),
			'title'		 => Mage::helper('powershare')->__('First Name'),
			'required'	 => true
		));

		$fieldset->addField('lastname', 'text', array(
			'name'		 => 'lastname',
			'label'		 => Mage::helper('powershare')->__('Last Name'),
			'title'		 => Mage::helper('powershare')->__('Last Name'),
			'required'	 => true,
		));

		$fieldset->addField('phone', 'text', array(
			'label'				 => Mage::helper('powershare')->__('Phone'),
			'required'			 => false,
			'name'				 => 'phone',
			'after_element_html' => "<p class='note'><span>" . Mage::helper('powershare')->__('Your number will not be shared.') . "</span></p>",
		));

		$fieldset->addField('coupon', 'text', array(
			'label'				 => Mage::helper('powershare')->__('Coupon Code'),
			'required'			 => false,
			'name'				 => 'coupon',
			'after_element_html' => "<br/>"
		));

		$fieldset->addField('terms', 'note', array(
			'label'	 => Mage::helper('powershare')->__('Terms of Service'),
			'text'	 => '<iframe src="' . Zizio_Powershare_Helper_Data::GetAdminRegisterTermsUrl() . '" width="600" height="200"></iframe>'
		));

		$fieldset->addField('ext_type', 'hidden', array(
			'required'	 => false,
			'name'		 => 'ext_type',
		));

		$fieldset->addField('ext_ver', 'hidden', array(
			'required'	 => false,
			'name'		 => 'ext_ver',
		));
		
		$fieldset->addField('zizio_user_id', 'hidden', array(
			'required'	 => false,
			'name'		 => 'zizio_user_id',
		));
		
		$form->setUseContainer(true);
		$this->setForm($form);
		$form->setValues($user_data);
		return parent::_prepareForm();
	}
}
