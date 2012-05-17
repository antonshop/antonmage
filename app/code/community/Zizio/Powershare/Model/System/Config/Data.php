<?php

class Zizio_Powershare_Model_System_Config_Data extends Mage_Core_Model_Config_Data
{
	
	static $xml2alias = array(
		'powershare/product_page/enabled' 		   => "zizio/powershare/settings/product_page/enabled",
		'powershare/product_page/powershare_bars'  => "zizio/powershare/settings/product_page/powershare_bars",
		'powershare/success_page/enabled' 		   => "zizio/powershare/settings/success_page/enabled",
		'powershare/success_page/cont_caption' 	   => "zizio/powershare/settings/success_page/cont_caption",
		'powershare/success_page/share_caption'    => "zizio/powershare/settings/success_page/share_caption",
		'powershare/success_page/heading_position' => "zizio/powershare/settings/success_page/heading_position",
		'powershare/success_page/css_selector' 	   => "zizio/powershare/settings/success_page/css_selector",
		'powershare/success_page/powershare_bars'  => "zizio/powershare/settings/success_page/powershare_bars",
		'powershare/success_page/position' 		   => "zizio/powershare/settings/success_page/position",
		'powershare/success_page/buttons' 		   => "zizio/powershare/settings/success_page/buttons"
	);
	
	private $_store_id = null;
	private $_website_id = null;
	private $_real_path = null;
	private $_inherit = null;
	
	/**
	 * handles display of current values
	 * 
	 * @see /app/code/core/Mage/Adminhtml/Block/System/Config/Form.php
	 */
	public function afterLoad()
	{
		if (Zizio_Powershare_Helper_Data::CompareVersions(Mage::getVersion(), '1.4.1') < 0)
		{
			if (array_key_exists($this->getPath(), self::$xml2alias))
			{
				$this->_loadParams();
				
				// get fresh db value
				$real_value = $this->_getDbModel($this->_store_id, $this->_website_id, $this->_real_path, true);
				
				// register field inheritance status
				$field_id = str_replace("/", "_", $this->getPath());
				Mage::register("{$field_id}_inherit", $this->_inherit, true);
				
				// save real path
				$this->setPath($this->_real_path)->setValue($real_value);
			}
		}
		parent::afterLoad();
	}
	
	/**
	 * handles setting of new values
	 * 
	 * @see /app/code/core/Mage/Adminhtml/Model/Config/Data.php
	 */
	public function save()
	{
		Zizio_Powershare_Helper_Data::ClearFPCCache(true);
		
		if (Zizio_Powershare_Helper_Data::CompareVersions(Mage::getVersion(), '1.4.1') < 0)
		{
			if (array_key_exists($this->getPath(), self::$xml2alias)) // alias - save outside the transaction
			{
				$this->_beforeSave();
				$this->_loadParams();
				
				// get current field and scope object
				$config_data = $this->_getDbModel($this->_store_id, $this->_website_id, $this->_real_path);
				
				// get a new model if necessary
				if (($config_data === null) ||							   // no current object
					($config_data->getScopeId() != $this->getScopeId()) || // scope_id has changed
					($config_data->getScope() != $this->getScope()))       // scope has changed
				{
					$config_data = Mage::getModel('core/config_data');
				} 
				
				// fill object with correct data and save
				$config_data
					->setScope($this->getScope())
					->setScopeId($this->getScopeId())
					->setValue($this->getValue())
					->setPath($this->_real_path)
					->save();
					
				return true;
			}
			elseif (in_array($this->getPath(), self::$xml2alias))	  // original - don't save
				return true;
		}
		return parent::save();										  // unrelated - continue normally
	}
	
	/**
	 * handles deletion of fields where inheritance was switched on
	 * 
	 * a terrible location to patch on, but it's the last resort;
	 * this data member is set after 'field', 'groups' and 'group_id' when in saveAction 
	 * 
	 * @see /app/code/core/Mage/Adminhtml/Model/Config/Data.php
	 */
	public function setPath($path)
	{
		if ((Zizio_Powershare_Helper_Data::CompareVersions(Mage::getVersion(), '1.4.1') < 0) && $this->hasGroupId())
		{
			$this->setData('path', $path);
			
			// only to check for current value existence 
			$this->_loadParams();
			$config_data = $this->_getDbModel($this->_store_id, $this->_website_id, $this->_real_path);
		
			$groups = $this->getGroups();
			$group = $groups[$this->getGroupId()];
			$new_inherit = !empty($group['fields'][$this->getField()]['inherit']);
			
			// if this field used to be set and now it is inherited - delete it
			if (!$this->_inherit && $new_inherit)
				$config_data->delete();
			
			$this->unsetData('path');
		}
		return parent::setData('path', $path);
	}

	/********************************************************************************
	 * helpers
	 ********************************************************************************/
	
	protected function _getDataConfig($scope, $scope_id, $path, $return_value)
	{
	    $config_collection = Mage::getModel('core/config_data')->getCollection();
        $config_collection
	        ->getSelect()
	        ->where('scope=?', $scope)
	        ->where('scope_id=?', $scope_id)
	        ->where('path=?', $path);
	    try {
	        foreach ($config_collection as $data)
	          	return $return_value ? $data->getValue() : Mage::getModel('core/config_data')->load($data->getConfigId());
        } catch(Exception $ex) { }
        
        return null;
	}
	
	protected function _getDbModel($store_id, $website_id, $path, $return_value=false)
	{
		$this->_inherit = false;
		
        if ($store_id !== null)
        {
        	$test = $this->_getDataConfig("stores", $store_id, $path, $return_value);
        	if ($test !== null)
        		return $test;
        	$this->_inherit = true;
        }
        
        if ($website_id !== null)
        {
        	$test = $this->_getDataConfig("websites", $website_id, $path, $return_value);
        	if ($test !== null)
        		return $test;
        	$this->_inherit = true;
        }

        $test = $this->_getDataConfig("default", 0, $path, $return_value);
        if ($test !== null)
        	return $test;
        
        return null;
	}
	
	protected function _loadParams()
	{
		try {
			$store = Mage::app()->getRequest()->getParam('store', '');
			$store_id = Mage::app()->getStore($store)->getId();
			$store_id = ($store_id == 0) ? null : $store_id;
		} catch (Exception $ex) {
			$store_id = null;
		}
		try {
			$website = Mage::app()->getRequest()->getParam('website', '');
			$website_id = Mage::app()->getWebsite($website)->getId();
		} catch (Exception $ex) {
			$website_id = null;
		}
		$real_path = self::$xml2alias[$this->getPath()];
		
		$this->_store_id = $store_id;
		$this->_website_id = $website_id;
		$this->_real_path = $real_path;
	}
	
}
