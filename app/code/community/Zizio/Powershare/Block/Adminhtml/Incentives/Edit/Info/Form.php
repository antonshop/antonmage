<?php

class Zizio_Powershare_Block_Adminhtml_Incentives_Edit_Info_Form extends Mage_Adminhtml_Block_Widget_Form
{

	protected function _prepareForm()
	{
		try
		{
			$form = new Varien_Data_Form();
			$this->setForm($form);
			$incentive = Mage::registry('powershare_incentive');
			
			$fieldset = $form->addFieldset('powershare_form',
						array('legend'=>Mage::helper('powershare')->__('Incentive Availability')));

	        /* store selection */
	        
	        if (!Mage::app()->isSingleStoreMode())
	        {
	        	$data = Zizio_Powershare_Helper_Data::GetStoresData();
	        	$store2website = array();
	        	$values = array();
	        	foreach ($data as $website_id => $website)
	        	{
	        		if (count($website['urls']) == 0)
	        			continue;
	        		$value = array('label' => $website['name'], 'value' => array());
	        		foreach ($website['urls'] as $store_id => $store)
	        		{
	        			$value['value'][] = array('label' => $store['name'], 'style' => "padding-left: 16px;", 'value' => $store_id);
	        			$store2website[$store_id] = $website_id;
	        		}
	        		$values[] = $value;
	        	}
	        	
	        	// get default website stores
				if ($incentive->getStoreIds() == null)
				{
					$website = Mage::App()->getWebsite(true);
					if ($website && isset($data[$website->getId()]))
					{
						$incentive->setWebsiteId($website->getId());
						$incentive->setStoreIds(array_keys($data[$website->getId()]['urls']));
					}
				}
	        	
	            $params = array(
	                'name'				 => "store_ids[]",
	                'label'				 => Mage::helper('catalogrule')->__('Stores'),
	                'title'				 => Mage::helper('catalogrule')->__('Stores'),
	                'required'			 => true,
	                'values'			 => $values,
	            	'onchange'			 => "Zizio_Powershare_StoreSelectionChanged(this);",
	            	'after_element_html' => "<script type='text/javascript'>
												var Zizio_Powershare_Store2Website = " . Zizio_Powershare_Helper_Data::json_encode($store2website) . "
												var Zizio_Powershare_StoreSelectionChanged = function(element) {
													// selected stores
													var store_ids = \$F(element);
													
													// ensure no multi website
													if (store_ids.length > 1) {
														var force_website = null;
														// isolate new selected store and get its website id
														for (var i=0; i<store_ids.length; i++) {
															if (Zizio_Powershare_StoreSelectionChanged.last_state.indexOf(store_ids[i]) == -1) {
																force_website = Zizio_Powershare_Store2Website[store_ids[i]];
																break;
													        }
													    }
													    // ensure all selected stores are of the same website
													    if (force_website !== null) {
													    	Zizio_Powershare_StoreSelectionChanged.last_state = [];
													    	for (var i=0; i<element.options.length; i++) {
														    	if (!element.options[i].selected) {
																	continue;										    	
														        }	
														    	if (Zizio_Powershare_Store2Website[element.options[i].value] == force_website) {
													    			Zizio_Powershare_StoreSelectionChanged.last_state.push(element.options[i].value);
	        													} else {
													    			element.options[i].selected = false;
													    		}
													        }
													    } else {
															Zizio_Powershare_StoreSelectionChanged.last_state = store_ids;
													 	}
													} else {
														Zizio_Powershare_StoreSelectionChanged.last_state = store_ids;
													}
													$('website_id').value = Zizio_Powershare_Store2Website[store_ids[0]] || '';
												};
												Zizio_Powershare_StoreSelectionChanged.last_state = [];
											</script>"
				);
				$fieldset->addField("website_id", "hidden", array( 'name' => "website_id" ));
				$fieldset->addField("store_ids", "multiselect", $params)->setSize(8);
	        }
	        else
	        {
	        	$store = Mage::app()->getStore(true);
	            $fieldset->addField("website_id", "hidden", array( 'name' => "website_id" ));
	            $incentive->setWebsiteId($store->getWebsiteId());
	            $fieldset->addField("store_ids", "hidden", array( 'name' => "store_ids[]" ));
	            $incentive->setStoreIds($store->getId());
	        }

	        /* customer groups selection */
	        
	        $customerGroups = Mage::getResourceModel('customer/group_collection')
	            ->load()->toOptionArray();

	        $found = false;
	        foreach ($customerGroups as $group) {
	            if ($group['value'] == 0) {
	                $found = true;
	            }
	        }
	        if (!$found) {
	            array_unshift($customerGroups, array('value'=>0, 'label'=>Mage::helper('catalogrule')->__('NOT LOGGED IN')));
	        }

	        $params = array(
	            'name'      => 'customer_group_ids[]',
	            'label'     => Mage::helper('catalogrule')->__('Customer Groups'),
	            'title'     => Mage::helper('catalogrule')->__('Customer Groups'),
	            'required'  => true,
	            'values'    => $customerGroups
	        );

	        $fieldset->addField('customer_group_ids', 'multiselect', $params)->setSize(8);
			
	        // set form values
			$form->setValues($incentive->getData());
		}
		catch (Exception $ex)
		{
			Zizio_Powershare_Helper_Data::LogError($ex);
		}
		return parent::_prepareForm();
	}
}
