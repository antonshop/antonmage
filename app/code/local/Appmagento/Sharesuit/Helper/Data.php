<?php 
/**
 * Appmagento
 *
 * @category    Appmagento
 * @package     Appmagento_Sharesuit
 * @copyright   Copyright (c) 2011 http://www.appmagento.com
 */

class Appmagento_Sharesuit_Helper_Data extends Mage_Core_Helper_Abstract
{
 public function sharesuitconfigurl()
    {
        return Mage::getUrl('sharesuit/index/index');
    }

    public function facebookloginurl()
    {

    $facebook=  Mage::getModel('sharesuit/sharesuit')->getfbuser();

    

        
          $user       = $facebook->getUser();



        return $loginUrl   = $facebook->getLoginUrl(
            array(
                'scope'         => 'email,offline_access,publish_stream,user_birthday,user_location,user_about_me,user_hometown',
                'redirect_uri'  => Mage::getBaseUrl().'sharesuit/index/publish'
            )
    );

    }
    public function publishurl()
    {
        return Mage::getUrl('sharesuit/index/publish');
    }
}