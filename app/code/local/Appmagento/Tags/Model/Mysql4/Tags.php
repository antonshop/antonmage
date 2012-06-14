<?php

class Appmagento_Tags_Model_Mysql4_Tags extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the tags_id refers to the key field in your database table.
        $this->_init('tags/tags', 'tags_id');
    }
}