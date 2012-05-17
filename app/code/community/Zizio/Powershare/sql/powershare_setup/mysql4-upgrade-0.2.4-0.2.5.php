<?php

try
{
	$installer = $this;

	$installer->startSetup();
	
	$conf = array();

	// store default buttons settings
	Mage::getModel('core/config_data')
		->setPath('zizio_powershare/success_page/buttons')
		->setScope("default")
		->setScopeId("0")
		->setValue('facebook,mail,twitter,googp1,like,pinit')
		->save();
	
	$installer->endSetup();
}
catch (Exception $ex)
{
	Zizio_Powershare_Helper_Data::LogError($ex);
}
