<?php
/**
 *
 * @package    Company
 * 
 * @author     
 */
class Company_Ecpss_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('ecpss/form.phtml');
        parent::_construct();
    }

}