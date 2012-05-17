<?php

class Zizio_Powershare_Block_Adminhtml_Incentives_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

	public function __construct()
	{
		parent::__construct();
		$this->setId('powershare_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('powershare')->__('Incentive Configuration'));
	}

	protected function _beforeToHtml()
	{
		try
		{
			$this->addTab('form_conf', array(
				'label'		=>	Mage::helper('powershare')->__('Configuration'),
				'title'		=>	Mage::helper('powershare')->__('Configuration'),
				'content'	=>	$this->getLayout()->createBlock('powershare/adminhtml_incentives_edit_conf_form')->toHtml()
			));
			
			$this->addTab('form_info', array(
				'label'		=>	Mage::helper('powershare')->__('Availability'),
				'title'		=>	Mage::helper('powershare')->__('Availability'),
				'content'	=>	$this->getLayout()->createBlock('powershare/adminhtml_incentives_edit_info_form')->toHtml()
			));
			
			$this->addTab('categories', array(
	            'label'     => Mage::helper('powershare')->__('Categories Selection'),
	            'title'     => Mage::helper('powershare')->__('Categories Selection'),
	            'url'       => $this->getUrl('*/*/categories', array('_current' => true)),
	            'class'     => 'ajax',
	        ));
	        
	        $this->addTab('product_section', array(
				'label'	 	=> Mage::helper('powershare')->__('Products Selection'),
				'title'	 	=> Mage::helper('powershare')->__('Products Selection'),
				'content'   => $this->getLayout()->createBlock('powershare/adminhtml_incentives_edit_product_form')->toHtml(),
			));
		}
		catch (Exception $ex)
		{
			Zizio_Powershare_Helper_Data::LogError($ex);
		}
		
		return parent::_beforeToHtml();
	}
}
