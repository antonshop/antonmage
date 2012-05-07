<?php
/**
 *
 * @package    Ecpss
 * 
 * @author  
 */
class Company_Ecpss_Model_Source_Language
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'EN', 'label' => Mage::helper('ecpss')->__('English')),
            array('value' => 'FR', 'label' => Mage::helper('ecpss')->__('French')),
            array('value' => 'DE', 'label' => Mage::helper('ecpss')->__('German')),
            array('value' => 'IT', 'label' => Mage::helper('ecpss')->__('Italian')),
            array('value' => 'ES', 'label' => Mage::helper('ecpss')->__('Spain')),
            array('value' => 'NL', 'label' => Mage::helper('ecpss')->__('Dutch')),
        );
    }
}



