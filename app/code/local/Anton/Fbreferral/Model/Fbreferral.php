<?php

class Anton_Fbreferral_Model_Fbreferral extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('fbreferral/fbreferral');
    }
}