<?php

class Zizio_Powershare_Model_System_Config_PowerShareBarsData extends Zizio_Powershare_Model_System_Config_Data
{
	
    protected function _beforeSave()
    {
    	$value = Zizio_Powershare_Helper_Data::json_decode($this->getValue(), true);
    	if (!$value) {
    		$this->setValue("{}");
    	} else {
		    $value = array_values(array_filter($value));
	    	$this->setValue(Zizio_Powershare_Helper_Data::json_encode($value));
    	}
    	
        return parent::_beforeSave();
    }
	
}
