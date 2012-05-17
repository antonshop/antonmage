<?php
class Anton_Fbreferral_Block_Fbreferral extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('fbreferral/fbreferral.phtml');
    }
}