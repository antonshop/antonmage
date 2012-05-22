<?php

class Anton_Referral_Model_Mysql4_Referral extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the referral_id refers to the key field in your database table.
        $this->_init('referral/referral', 'referral_id');
    }
}