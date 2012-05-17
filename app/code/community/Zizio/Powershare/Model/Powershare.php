<?php

class Zizio_Powershare_Model_Powershare extends Mage_Core_Model_Abstract
{
	protected $_resourceName = "powershare/incentives";
	
	public function _construct()
	{
		parent::_construct();
		
		$this->setCustomerGroupIds(Zizio_Powershare_Helper_Data::GetAllCustomerGroupIds());
	}	
	
	private function IsResponseOk ($response, &$err_msg = null)
	{
        if (!$response)
        	return false;
        
        if (!isset($response['result']))
        	return false;
        
        if ($response['result'] != 'OK')
        {
        	if ($err_msg !== null)
        		$err_msg = $response['result'];	
        	return false;
        }
                	
        return true;
	}
    
	/* public function getId()
    {
    	return $this->_getData('_id');
    } */
	
    public function load($id, $field=null)
    {
        if (method_exists($this, "_beforeLoad"))
    		$this->_beforeLoad($id, $field);

        $args = array();
    	$args['ince_id'] = $id;
        $response = Zizio_Powershare_Helper_Data::CallUrl('/powers/mage/admin/get_incentive', $args, null);
        $err_msg = "";
        if ($this->IsResponseOk($response, $err_msg))
        	$this->setData($response['item']);
        else 
        	$this->setErrorMsg($err_msg);
        
        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;
        return $this;
	}
    
	protected function _afterLoad()
	{
		parent::_afterLoad();

		$websiteIds = $this->_getData('website_ids');
		if (is_string($websiteIds))
			$this->setData('website_ids', explode(',', $websiteIds));

		$storeIds = $this->_getData('store_ids');
		if (is_string($storeIds))
			$this->_setData('store_ids', explode(',', $storeIds));
		
		$groupIds = $this->_getData('customer_group_ids');
		if (is_string($groupIds))
		{
			$groupIds = explode(',', $groupIds);
			$all_groupIds = Mage::getModel('customer/group')->getCollection()->getAllIds();
			$this->_setData('customer_group_ids', array_intersect($groupIds, $all_groupIds));
		}

		$categoryIds = $this->_getData('category_ids');
		if (is_string($categoryIds))
			$this->_setData('category_ids', explode(',', $categoryIds));

		$productIds = $this->_getData('product_ids');
		if (is_string($productIds))
			$this->_setData('product_ids', explode(',', $productIds));
	}

	protected function _getSaveData()
	{
		$data = $this->getData();
		
		if (is_array($this->_getData('website_ids')))
			$data['website_ids'] = join(',', $this->getWebsiteIds());

		if (is_array($this->_getData('store_ids')))
			$data['store_ids'] = join(',', $this->getStoreIds());

		if (is_array($this->_getData('customer_group_ids')))
			$data['customer_group_ids'] = join(',', $this->getCustomerGroupIds());

		if (is_array($this->_getData('category_ids')))
			$data['category_ids'] = join(',', $this->getCategoryIds());

		if (is_array($this->_getData('product_ids')))
			$data['product_ids'] = join(',', $this->getProductIds());
		
		return $data;
	}
    
    public function save()
    {
        Zizio_Powershare_Helper_Data::CheckEncKey();
    	
    	$args = array();
    	$args['ince_id'] = $this->getData('_id');
		$this->setData('gmt_offset', Zizio_Powershare_Helper_Data::GetGmtOffset());
    	$response = Zizio_Powershare_Helper_Data::CallUrl('/powers/mage/admin/save_incentive', $args, $this->_getSaveData());
        $err_msg = "";
        if ($this->IsResponseOk($response, $err_msg))
        {
			$this->_hasDataChanges = false;
        	$this->setData('_id', $response['ince_id']);
    		return true;    		
        }
        else 
        {
        	$this->setErrorMsg($err_msg);
        }
            	
    	return false;
    }
    
    public function delete ()
    {
        $args = array();
    	$args['ince_id'] = $this->getData('id');
        $response = Zizio_Powershare_Helper_Data::CallUrl('/powers/mage/admin/delete_incentive', $args, null);
        $err_msg = "";
        if ($this->IsResponseOk($response, $err_msg))
        	return true;
        else
        	return false;
    }
    
    public function isObjectNew($flag=null)
    {
        if ($flag !== null) {
            $this->_isObjectNew = $flag;
        }
        if ($this->_isObjectNew !== null) {
            return $this->_isObjectNew;
        }
        return !(bool)$this->getId();
    }

}