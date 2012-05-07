<?php
/**
 *
 * @package    Ecpss
 * 
 * @author     
 */
 
class Company_Ecpss_Model_Mysql4_Api_Debug_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('ecpss/api_debug');
    }
}