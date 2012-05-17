<?php

class Zizio_Powershare_Model_System_Config_EnabledDisabled
{
	public function toOptionArray()
    {
        return array(
        	'0' => Mage::helper('powershare')->__('Off'),
            '1' => Mage::helper('powershare')->__('On')
        );
    }
}
