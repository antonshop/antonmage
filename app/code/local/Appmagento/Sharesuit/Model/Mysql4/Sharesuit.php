<?php

class Appmagento_Sharesuit_Model_Mysql4_Sharesuit extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the sharesuit_id refers to the key field in your database table.
        $this->_init('sharesuit/sharesuit', 'sharesuit_id');
    }
}