<?php

class Zizio_Powershare_Block_Adminhtml_Incentives_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();

		$this->_objectId = 'id';
		$this->_blockGroup = 'powershare';
		$this->_controller = 'adminhtml_incentives';
		
		$this->_updateButton('save', 'label', Mage::helper('powershare')->__('Save Item'));
		$this->_updateButton('delete', 'label', Mage::helper('powershare')->__('Delete Item'));
		
		// Save and Continue Edit
		$this->_addButton(
			'saveandcontinue', array(
				'label'	  => Mage::helper('adminhtml')->__('Save And Continue Edit'),
				'onclick' => 'saveAndContinueEdit()',
				'class'	  => 'save'
			),
			-100
		);

		$this->_formScripts[] = "function saveAndContinueEdit() { editForm.submit($('edit_form').action + 'back/edit/'); }";
	}
	
	public function getHeaderText()
	{
		$incentive = Mage::registry('powershare_incentive');
		if($incentive && $incentive->getId())
			return Mage::helper('powershare')->__("Edit '%s' Incentive", $incentive->getName());
		else
			return Mage::helper('powershare')->__("Create New Incentive");
	}
	
    protected function _toHtml()
    {
        $html = parent::_toHtml();
        
		$static_data = array(
			'pub_id'	 		  => Zizio_Powershare_Helper_Data::GetPublisherId(),
			'gmt_offset' 		  => Zizio_Powershare_Helper_Data::GetGmtOffset(),
			'ext_type'	 		  => "powershare",
			'admin_loc'	 		  => "edit",
			'ext_ver'	 		  => Zizio_Powershare_Helper_Data::GetExtVer(""),
			'validate_coupon_url' => $this->getUrl("powershare/index/validateCoupon", array('_query' => array(
									     'ajax' => "1",
									     'zizio_coupon' => "__ZIZIO_COUPON__",
									     'zizio_callback' => "__ZIZIO_CALLBACK__",
									     'zizio_end_date' => "__ZIZIO_END_DATE__"
								     ))),
			'coupons_grid_url'    => $this->getUrl("adminhtml/promo_quote/index")
		);
		$zizio_js = sprintf("<script type='text/javascript'> var z_statdata = %s; </script>",
						    Zizio_Powershare_Helper_Data::json_encode($static_data));
		if (Mage::registry("zizio/utils_js_added") == null)
		{
			$zizio_js .= Zizio_Powershare_Helper_Data::GetScriptBlock(Zizio_Powershare_Helper_Data::GetZUtilsScriptUrl());
			Mage::register("zizio/utils_js_added", true);
		}
		$zizio_js .= Zizio_Powershare_Helper_Data::GetScriptBlock(Zizio_Powershare_Helper_Data::GetAdminIncentiveEditScriptUrl());

        return $zizio_js . $html;
    }
}
