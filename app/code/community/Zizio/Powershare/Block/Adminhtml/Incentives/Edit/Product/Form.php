<?php

class Zizio_Powershare_Block_Adminhtml_Incentives_Edit_Product_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function getFormHtml()
    {
		$product_block_brid = $this->getLayout()->createBlock('powershare/adminhtml_incentives_edit_product_grid');
    	return parent::getFormHtml().$product_block_brid->getHtml();
    }

    protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		return parent::_prepareForm();
	}
}
