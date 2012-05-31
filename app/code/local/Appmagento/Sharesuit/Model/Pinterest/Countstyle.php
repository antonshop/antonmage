<?php
class Appmagento_Sharesuit_Model_Pinterest_Countstyle
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'horizontal', 'label'=>Mage::helper('sharesuit')->__('Horizontal')),
            array('value'=>'vertical', 'label'=>Mage::helper('sharesuit')->__('Vertical')),
            array('value'=>'none', 'label'=>Mage::helper('sharesuit')->__('No')),                  
        );
    }

}
?>