<?php
 /**
 * CosmoCommerce
 *
 * NOTICE OF LICENSE
 * CosmoCommerce Commercial License 
 * support@cosmocommerce.com
 *
 * @category   CosmoCommerce
 * @package    CosmoCommerce_Ecpss
 * @copyright  Copyright (c) 2009 CosmoCommerce,LLC. (http://www.cosmocommerce.com)
 * @license	     CosmoCommerce Commercial License(http://www.cosmocommerce.com/cosmocommerce_commercial_license.txt)
 */

/**
 * Redirect to Ecpss
 *
 * @category   Mage
 * @package    CosmoCommerce_Ecpss
 * @author     CosmoCommerce  <sales@cosmocommerce.com>
 */
class Company_Ecpss_Block_Redirect extends Mage_Core_Block_Abstract
{

	protected function _toHtml()
	{
		$standard = Mage::getModel('ecpss/payment');
        $form = new Varien_Data_Form();
        $form->setAction($standard->getMiddleUrl())
            ->setId('ecpss_payment_checkout')
            ->setName('ecpss_payment_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);
        foreach ($standard->setOrder($this->getOrder())->getStandardCheckoutFormFields() as $field => $value) {
            $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
        }

        $formHTML = $form->toHtml();

        $html = '<html><body>';
        //$html.= $this->__('You will be redirected to Ecpss in a few seconds.');
        $html.= $formHTML;
        $html.= '<script type="text/javascript">document.getElementById("ecpss_payment_checkout").submit();</script>';
        $html.= '</body></html>';


        return $html;
    }
}