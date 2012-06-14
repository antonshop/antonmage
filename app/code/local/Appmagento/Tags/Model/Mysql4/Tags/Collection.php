<?php

class Appmagento_Tags_Model_Mysql4_Tags_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('tags/tags');
    }
}