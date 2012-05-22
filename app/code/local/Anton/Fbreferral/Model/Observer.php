<?php
/**
 * @name         :  Apptha FB Referral
 * @version      :  1.2
 * @since        :  Magento 1.5
 * @author       :  Apptha - http://www.apptha.com
 * @copyright    :  Copyright (C) 2011 Powered by Apptha
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  December 01 2011
 *
 * */
class Anton_Fbreferral_Model_Observer {


   
    public function processUpdateDiscount($observer) {
        $read = Mage::getSingleton('core/resource')->getConnection('read');
        $quoteItemTable = Mage::getSingleton('core/resource')->getTableName('sales_flat_quote_item');

        $quoteId = Mage::getSingleton('checkout/session')->getLastQuoteId();
        $selectQuote = $read->select()
                        ->from(array('ct' => $quoteItemTable), array('ct.discount_amount'))
                        ->where('ct.quote_id =? ', $quoteId);
        $customerQuote = $read->fetchAll($selectQuote);
        $discountQuoteAmount = round($customerQuote[0]['discount_amount']);

        $session = Mage::getSingleton('customer/session');
        $resource = Mage::getSingleton('core/resource');
        $write = Mage::getSingleton('core/resource')->getConnection('write');

        $fbreferral = Mage::getSingleton('core/resource')->getTableName('fbreferral');
        
        $facebook = Mage::getModel('fbreferral/fbreferral')->getFacebook();
        $fbuser = $facebook->getUser();
        
        if($fbuser)
        {
        	$update = $write->query("DELETE from $fbreferral WHERE fbuser =" . $fbuser);
        }
//echo $fbuser;exit;
        return $this;
    }

}