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
class Anton_Fbreferral_Helper_Data extends Mage_Core_Helper_Abstract
{
 public function fbreferralconfigurl()
    {
        return Mage::getUrl('fbreferral/index/index');
    }

    public function facebookloginurl()
    {

    $facebook=  Mage::getModel('fbreferral/fbreferral')->getfbuser();

    

        
          $user       = $facebook->getUser();



        return $loginUrl   = $facebook->getLoginUrl(
            array(
                'scope'         => 'email,offline_access,publish_stream,user_birthday,user_location,user_about_me,user_hometown',
                'redirect_uri'  => Mage::getBaseUrl().'fbreferral/index/publish'
            )
    );

    }
    public function publishurl()
    {
        return Mage::getUrl('fbreferral/index/publish');
    }
}