<?php
/**
 *
 * @package    Ecpss
 * 
 * @author     xinhaozheng@gmail.com
 */
class Company_Ecpss_Model_Source_Transport
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'https', 'label' => Mage::helper('ecpss')->__('https')),
            array('value' => 'http', 'label' => Mage::helper('ecpss')->__('http')),
        );
    }
}