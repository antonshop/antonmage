<?php
/**
 * Appmagento
 *
 * @category    Appmagento
 * @package     Appmagento_Sharesuit
 * @copyright   Copyright (c) 2011 http://www.appmagento.com
 */

require_once 'src/apiClient.php';
require_once 'src/contrib/apiPlusService.php';
require_once 'src/contrib/apiOauth2Service.php';

class Appmagento_Sharesuit_Model_Google_Plus extends Mage_Core_Model_Abstract
{
    /* get sharesuit google plus config */
    public function getGpLogin(){
   		return Mage::getStoreConfig('sharesuit/google/gp_login');
    }
    
	public function getGpPlusone(){
   		return Mage::getStoreConfig('sharesuit/google/gp_plusone');
    }
    
	public function getGpClientId(){
   		return Mage::getStoreConfig('sharesuit/google/gp_client_id');
    }
    
	public function getGpClientSecret(){
   		return Mage::getStoreConfig('sharesuit/google/gp_client_secret');
    }
    
    public function getApiClient(){
    	return new apiClient();
    }
    
    public function getApiPlus(){
    	return new apiPlusService(self::getApiClient());
    }
    
    public function getApiOauth2(){
    	return new apiOauth2Service(self::getApiClient());
    }
    
    public function getAuthUrl(){
    	$client = self::getApiClient();
    	$plus = self::getApiPlus();
		$oauth2 = self::getApiOauth2();
    	
    	$client->setClientId(self::getGpClientId());
		$client->setClientSecret(self::getGpClientSecret());
		$client->setRedirectUri(Mage::getBaseUrl() . "sharesuit/index/gprespond");
    	//$client->();
    	return $client->createAuthUrl();
    }
}











?>