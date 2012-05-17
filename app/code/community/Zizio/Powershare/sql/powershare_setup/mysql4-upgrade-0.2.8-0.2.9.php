<?php

try
{
	$installer = $this;

	$installer->startSetup();
	
	// an helper class to get inherited value for config keys
	class Zizio_Powershare_Upgrade_0_2_8_to_0_2_9
	{
		public function hasConfigData($path, $scope, $scope_id)
		{
		    $config_collection = Mage::getModel('core/config_data')->getCollection();
	        $config_collection->getSelect()
		        ->where('path=?', $path)
	        	->where('scope=?', $scope)
		        ->where('scope_id=?', $scope_id);
		        
		    try
		    {
		        foreach ($config_collection as $data)
		          	return ($data && $data->hasValue());
	        }
	        catch(Exception $ex)
	        {
	        	// noop
	        }
	        
	        return false;
		}

		public function setConfigData($path, $scope, $scope_id, $value)
		{
		    Mage::getModel('core/config_data')
		    	->setPath($path)
		    	->setScope($scope)
		    	->setScopeId($scope_id)
		    	->setValue($value)
		    	->save();
		}
	}
	
	// migration list, old_key => new_key
	$migration = array(
		//'zizio/settings/base_url' => "zizio/powershare/settings/base_url",
		//'zizio/settings/debug' => "zizio/powershare/settings/debug",
		//'zizio/settings/port' => "zizio/powershare/settings/port",
		//'zizio/settings/protocol' => "zizio/powershare/settings/protocol",
		'zizio_powershare/cron/hourly_last_run' => "zizio/powershare/cron/hourly_last_run",
		'zizio_powershare/cron/daily_last_run' => "zizio/powershare/cron/daily_last_run",
		'zizio_powershare/product_page/enabled' => "zizio/powershare/settings/product_page/enabled",
		'zizio_powershare/product_page/powershare_bars' => "zizio/powershare/settings/product_page/powershare_bars",
		'zizio_powershare/success_page/enabled' => "zizio/powershare/settings/success_page/enabled",
		'zizio_powershare/success_page/share_caption' => "zizio/powershare/settings/success_page/share_caption",
		'zizio_powershare/success_page/css_selector' => "zizio/powershare/settings/success_page/css_selector",
		'zizio_powershare/success_page/position' => "zizio/powershare/settings/success_page/position",
		'zizio_powershare/success_page/buttons' => "zizio/powershare/settings/success_page/buttons",
		'zizio_powershare/messages/last_get' => "zizio/powershare/messages_last_get",
		'zizio_powershare/settings/design_skin' => "zizio/powershare/settings/design_skin",
		'zizio_powershare/settings/zizio_account_url' => "zizio/powershare/settings/zizio_account_url",
		'zizio_powershare/notify/pinit' => "zizio/powershare/notify_pinit",
		'zizio_powershare/registered' => "zizio/powershare/registered"
	);

	$helper = new Zizio_Powershare_Upgrade_0_2_8_to_0_2_9();
	
	// get all zizio config records
	$coll = Mage::getModel('core/config_data')->getCollection();
	$coll->addFieldToFilter('path', array('like' => "%zizio%"))->load();

	foreach($coll as $item)
	{
		try
		{
			// skip records unrelated to migration
			if (!isset($migration[$item->getPath()]))
				continue;
				
			$path = $migration[$item->getPath()];
			$scope = $item->getScope();
			$scope_id = $item->getScopeId();
			
			// skip records already migrated
			if ($helper->hasConfigData($path, $scope, $scope_id))
				continue;
			
			$helper->setConfigData($path, $scope, $scope_id, $item->getValue());
		}
		catch (Exception $ex)
		{
			Zizio_Powershare_Helper_Data::LogError($ex);
		}
	}
	
	$installer->endSetup();
}
catch (Exception $ex)
{
	Zizio_Powershare_Helper_Data::LogError($ex);
}
