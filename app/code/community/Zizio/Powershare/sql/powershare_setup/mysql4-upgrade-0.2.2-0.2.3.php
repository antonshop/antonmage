<?php

try
{
	$installer = $this;

	$installer->startSetup();

	// an helper class to get inherited value for config keys
	class Zizio_Powershare_Upgrade_0_2_2_to_0_2_3
	{
		protected function _getDataConfig($scope, $scope_id, $path)
		{
		    $config_collection = Mage::getModel('core/config_data')->getCollection();
	        $config_collection->getSelect()
		        ->where('scope=?', $scope)
		        ->where('scope_id=?', $scope_id)
		        ->where('path=?', $path);
		    try {
		        foreach ($config_collection as $data)
		          	return $data->getValue();
	        } catch(Exception $ex) { }
	        
	        return null;
		}
		
		public function getValue($store_id, $website_id, $path, $def_val=null)
		{
			try
			{
				$this->_inherit = false;
				
		        if ($store_id !== null)
		        {
		        	$test = $this->_getDataConfig("stores", $store_id, $path);
		        	if ($test !== null)
		        		return $test;
		        	$this->_inherit = true;
		        }
		        
		        if ($website_id !== null)
		        {
		        	$test = $this->_getDataConfig("websites", $website_id, $path);
		        	if ($test !== null)
		        		return $test;
		        	$this->_inherit = true;
		        }
		
		        $test = $this->_getDataConfig("default", 0, $path);
		        if ($test !== null)
		        	return $test;
			}
			catch(Exception $ex) { }
	        
	        return $def_val;
		}
	}
	
	$helper = new Zizio_Powershare_Upgrade_0_2_2_to_0_2_3();
	
	$conf = array();
	
	$coll = Mage::getModel('core/config_data')->getCollection();
	$coll->addFieldToFilter('path', array('in' => array(
		'zizio_powershare/product_page/share_caption',
		'zizio_powershare/product_page/position',
		'zizio_powershare/product_page/css_selector'
	)))->load();
	
	// iterate thru all explicitly set values and build a tree in the format: {scope: {scope_id: {key: value}}}
	foreach($coll as $item)
	{
		$path = preg_replace("|^.*?([^/]*)$|", "\\1", $item->getPath());
		$val = $item->getValue();
		$scope = $item->getScope();
		$scope_id = $item->getScopeId();
		if (!isset($conf[$scope]))
			$conf[$scope] = array();
		if (!isset($conf[$scope][$scope_id]))
			$conf[$scope][$scope_id] = array();
		$conf[$scope][$scope_id][$path] = $val;
	}
	
	// iterate thru all scoped we have at least some settings in
	foreach($conf as $scope => $confs)
	{
		foreach($confs as $scope_id => $vals)
		{
			// get store / website ids
			$store_id = null;
			$website_id = null;
			if ($scope == "websites")
				$website_id = $scope_id;
			else if ($scope == "stores")
			{
				$store_id = $scope_id;
				$website_id = Mage::app()->getStore($store_id)->getWebsiteId();
			}
			
			// get all values from (1) explicit (2) inheritance (3) default value   
			$vals['inline'] = "0";
			$vals['buttons'] = array('facebook', 'mail', 'twitter', 'googp1', 'like', 'pinit');
			if (!isset($vals['share_caption']))
				$vals['share_caption'] = $helper->getValue($store_id, $website_id, 'zizio_powershare/product_page/share_caption', "");
			if (!isset($vals['position']))
				$vals['position'] = $helper->getValue($store_id, $website_id, 'zizio_powershare/product_page/position', "bottom");
			if (!isset($vals['css_selector']))
				$vals['css_selector'] = $helper->getValue($store_id, $website_id, 'zizio_powershare/product_page/css_selector', "");
			$setting = array($vals);
			
			// json encode and store in db
			$setting = json_encode($setting);
			Mage::getModel('core/config_data')
				->setPath('zizio_powershare/product_page/powershare_bars')
				->setScope($scope)
				->setScopeId($scope_id)
				->setValue($setting)
				->save();
		}
	}
	
	$installer->endSetup();
}
catch (Exception $ex)
{
	Zizio_Powershare_Helper_Data::LogError($ex);
}
