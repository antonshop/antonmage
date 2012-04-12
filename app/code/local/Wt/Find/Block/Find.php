<?php
class Wt_Find_Block_Find extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('find/find.phtml');
    }
}