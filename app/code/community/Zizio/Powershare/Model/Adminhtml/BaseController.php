<?php

class Zizio_Powershare_Model_Adminhtml_BaseController extends Mage_Adminhtml_Controller_Action
{

    protected function _construct()
    {
		$ret = parent::_construct();
		
		// patch old magentos for ie 9
		$enterprise = Zizio_Powershare_Helper_Data::IsEnterpriseEdition();
		if (($enterprise && (Zizio_Powershare_Helper_Data::CompareVersions(Mage::getVersion(), '1.11.0') < 0)) ||
			(!$enterprise && (Zizio_Powershare_Helper_Data::CompareVersions(Mage::getVersion(), '1.6.0') < 0)))
		{
			$this->getResponse()->setHeader("X-UA-Compatible", "IE=8", true);
		}
		
		return $ret;
    }
	
}
