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

    protected $_discount;

   
    public function processUpdateDiscount($observer) {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('read');
        $tPrefix = (string) Mage::getConfig()->getTablePrefix();
        $quoteItemTable = $tPrefix . 'sales_flat_quote_item';

        $quoteId = Mage::getSingleton('checkout/session')->getLastQuoteId();
        $selectQuote = $read->select()
                        ->from(array('ct' => $quoteItemTable), array('ct.discount_amount'))
                        ->where('ct.quote_id =? ', $quoteId);
        $customerQuote = $read->fetchAll($selectQuote);
        $discountQuoteAmount = round($customerQuote[0]['discount_amount']);

        $session = Mage::getSingleton('customer/session');
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('write');
        $tPrefix = (string) Mage::getConfig()->getTablePrefix();
        $fbstatusTable = $tPrefix . 'fbreferral_status';

        $facebook = Mage::getModel('fbreferral/fbreferral')->getfbuser();
        
        $user = $facebook->getUser();
        if($user)
        {
        $update = $read->query("DELETE from $fbstatusTable  WHERE fbuser =" . $user);
        }

        return $this;
    }

}