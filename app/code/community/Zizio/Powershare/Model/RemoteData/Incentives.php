<?php

class Zizio_Powershare_Model_RemoteData_Incentives extends Mage_Core_Model_Resource_Abstract
{
	protected $_idFieldName = "_id";	
	
    /**
     * Get primary key field name
     *
     * @return string
     */
    public function getIdFieldName()
    {
        if (empty($this->_idFieldName)) {
            Mage::throwException(Mage::helper('core')->__('Empty identifier field name'));
        }
        return $this->_idFieldName;
    }
    
    /**
     * Resource initialization
     */
    protected function _construct()
    {
    	
    }

    /**
     * Retrieve connection for read data
     */
    protected function _getReadAdapter()
    {
    	
    }

    /**
     * Retrieve connection for write data
     */
	protected function _getWriteAdapter()
	{
		
	}
    
}
