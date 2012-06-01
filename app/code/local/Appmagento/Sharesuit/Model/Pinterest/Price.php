<?php
class Appmagento_Sharesuit_Model_Pinterest_Price
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('sharesuit')->__('Yes')),
            array('value'=>0, 'label'=>Mage::helper('sharesuit')->__('No')),              
        );
    }

}
?>