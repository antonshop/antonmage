<?php

class Anton_Fbreferral_Model_Mysql4_Fbreferral extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the fbreferral_id refers to the key field in your database table.
        $this->_init('fbreferral/fbreferral', 'fbreferral_id');
    }
}