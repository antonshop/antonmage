<?php

class Zizio_Powershare_Model_System_Config_DecorationField extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
    	try 
    	{
	    	$args = array();
	    	$decoration = Zizio_Powershare_Helper_Data::CallUrl(Zizio_Powershare_Helper_Data::ADMIN_GET_DECORATION_URL, $args, null, true);
	    	$html = "";
	    	foreach ($decoration['template'] as $item)
	    	{
	    		$element_prefix = $element->getName(); 
	    		$html_element = null;
	   			$args = array();
	   			$args['name'] = $element_prefix."[".$item['key']."]";
	   			$args['label'] = $item['name'];
	    		if (isset($decoration['data'][$item['key']]))
	    			$args['value'] = $decoration['data'][$item['key']];
	   			
	   			if ($item['type'] == 'select')
	    		{
	    			$values = array();
	    			foreach ($item['options'] as $option)
	    			{
	    				$values[] = array('value' => $option[1], 'label' => $option[0]);
	    			}
	    			$args['values'] = $values;
	    			$html_element = new Varien_Data_Form_Element_Select($args);
	    		}
	    		
	    		if ($item['type'] == 'text')
	    		{
	    			$args['class'] = "input-text";
	    			$html_element = new Varien_Data_Form_Element_Text($args);
	    		}
	    		
	    		if ($html_element)
	    		{	
	    			if (isset($item['comment']))
	    				$html_element->setComment($item['comment']);
	    				
	    			$html_element->setId($element_prefix.$item['key']);
	    			$html_element->setForm($element->getForm());
	    			if (isset($item['admin']))
	    			{
	    				$html .= "<script type='text/javascript'>
		    				window.Zizio = window.Zizio || {};
							window.Zizio.PowerS = window.Zizio.PowerS || {};
							window.Zizio.PowerS.data = window.Zizio.PowerS.data || {};
							window.Zizio.PowerS.data.decoration_element = '{$element_prefix}{$item['key']}';
							window.Zizio.PowerS.data.decoration_res = " . Mage::helper('powershare')->json_encode($item['admin'], true) . ";
						</script>";
	    			}
	    			$html .= parent::render($html_element);
	    			
	    			// Insert hidden element for old value
	    			$args['name'] = $element_prefix."[".$item['key']."_old]";
	    			$args['label'] = null;
	    			$old_value = new Varien_Data_Form_Element_Hidden($args);
	    			$old_value->setForm($element->getForm());
	    			$html .= parent::render($old_value);    			
	    		}	
	    	}
    	
    		return $html;
    	}
		catch (Exception $ex)
		{
			Zizio_Powershare_Helper_Data::LogError($ex);
		}
    }
}