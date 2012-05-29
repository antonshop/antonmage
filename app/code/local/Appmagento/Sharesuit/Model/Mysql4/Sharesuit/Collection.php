<?php

class Appmagento_Sharesuit_Model_Mysql4_Sharesuit_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('sharesuit/sharesuit');
    }
}