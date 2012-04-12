<?php

class Wt_Find_Model_Mysql4_Find extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the find_id refers to the key field in your database table.
        $this->_init('find/find', 'find_id');
    }
}