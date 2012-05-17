<?php

try
{
	$installer = $this;

	$installer->startSetup();

	$installer->endSetup();
}
catch (Exception $ex)
{
	Zizio_Powershare_Helper_Data::LogError($ex);
}
