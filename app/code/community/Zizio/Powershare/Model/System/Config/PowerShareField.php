<?php

class Zizio_Powershare_Model_System_Config_PowerShareField
	extends Zizio_Powershare_Model_System_Config_Field implements Varien_Data_Form_Element_Renderer_Interface
{
	
    protected function _getJs($element)
    {
    	return "(function() {
window.Zizio = window.Zizio || {};
window.Zizio.PowerS = window.Zizio.PowerS || {};
var powers = window.Zizio.PowerS;
powers.base_widget_url = '".Zizio_Powershare_Helper_Data::GetBaseWidgetsUrl()."';

powers.orig_element_ids = powers.orig_element_ids || {};
powers.orig_element_ids['".$element->getName()."'] = '".$element->getHtmlId()."';

if (!powers.init) {
	$(document).observe('dom:loaded', function() {
		(function() {
			if (!window.configForm)
				return;
			configForm._zizio_submit = configForm.submit;
			configForm.submit = function() {
				return;
			};
		})();
		
		for (var element in powers.orig_element_ids)
			$(powers.orig_element_ids[element]).style.visibility = 'hidden';
		
		var scr = document.createElement('script');
		scr.type = 'text/javascript';
		scr.src = '".Zizio_Powershare_Helper_Data::GetSystemConfigScriptUrl()."';
		document.getElementsByTagName('head')[0].appendChild(scr);
	});
}

powers.init = true;
    	})();";
    }	
    
}
