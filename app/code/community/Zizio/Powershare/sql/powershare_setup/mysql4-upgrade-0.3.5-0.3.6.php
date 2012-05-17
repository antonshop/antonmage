<?php

try
{
	$installer = $this;

	$installer->startSetup();
	
	$conf = array();

	// store default buttons settings
	Mage::getModel('core/config_data')
		->setPath('zizio/powershare/settings/success_page/cont_caption')
		->setScope("default")
		->setScopeId("0")
		->setValue('Share the joy!')
		->save();
	
	$installer->endSetup();
}
catch (Exception $ex)
{
	Zizio_Powershare_Helper_Data::LogError($ex);
}
