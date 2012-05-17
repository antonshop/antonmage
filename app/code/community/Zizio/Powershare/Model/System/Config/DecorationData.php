<?php

class Zizio_Powershare_Model_System_Config_DecorationData extends Mage_Core_Model_Config_Data
{
    /**
     * Save object data
     *
     */
    public function save()
    {
    	parent::save();
    	
    	try 
    	{
	    	$post_args = array();
    		$values = $this->getFieldsetData();
			if ($values == null)
			{
				// Magento older then 1.4.2 dosen't have FieldsetData 
				$groups = $this->getGroups();
				$values = $groups['widget_decoration']['fields']['decoration']['value'];
			}
			else
			{
				$values = $values['decoration'];
			}    		
    		
			foreach ($values as $k => $v)
	    	{
	    		if (substr($k, -4) != "_old")
	    		{
	    			if ($v != $values[$k."_old"])
	    			{
	    				$post_args[$k] = $v;
	    			}
	    		}	    			
	    	}
	    	
	    	if (count($post_args) > 0)
	    	{	    	
	    		$args = array();
	    		$decoration = Zizio_Powershare_Helper_Data::CallUrl(Zizio_Powershare_Helper_Data::ADMIN_SET_DECORATION_URL, $args, $post_args, true);
	    	}    		
    	}
		catch (Exception $ex)
		{
			Zizio_Powershare_Helper_Data::LogError($ex);
		}
   	}
    
    public function afterLoad()
    {
    	parent::afterLoad();    	
    }
}
