<?php

class Anton_Referral_Model_Observer {
   
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

        $referral = Mage::getSingleton('core/resource')->getTableName('referral');
        
        $facebook = Mage::getModel('referral/referral')->getFacebook();
        $fbuser = $facebook->getUser();
        
        if($fbuser)
        {
        	$update = $write->query("DELETE from $referral WHERE fbuser =" . $fbuser);
        }
//echo $fbuser;exit;
        return $this;
    }

}