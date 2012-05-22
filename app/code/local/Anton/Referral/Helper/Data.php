<?php

class Anton_Referral_Helper_Data extends Mage_Core_Helper_Abstract
{
 public function referralconfigurl()
    {
        return Mage::getUrl('referral/index/index');
    }

    public function facebookloginurl()
    {

    $facebook=  Mage::getModel('referral/referral')->getfbuser();

    

        
          $user       = $facebook->getUser();



        return $loginUrl   = $facebook->getLoginUrl(
            array(
                'scope'         => 'email,offline_access,publish_stream,user_birthday,user_location,user_about_me,user_hometown',
                'redirect_uri'  => Mage::getBaseUrl().'referral/index/publish'
            )
    );

    }
    public function publishurl()
    {
        return Mage::getUrl('referral/index/publish');
    }
}