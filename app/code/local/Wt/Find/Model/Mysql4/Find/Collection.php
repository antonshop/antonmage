<?php

class Wt_Find_Model_Mysql4_Find_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('find/find');
    }
}