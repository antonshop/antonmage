<?php

class Wt_Find_Model_Find extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('find/find');
    }
}