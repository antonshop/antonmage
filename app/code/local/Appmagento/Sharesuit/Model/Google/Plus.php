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
    
    public function getRedirectUri(){
    	return Mage::getBaseUrl() . "sharesuit/customer/gplogin";
    }
    
    public function getApiClient(){
    	$client = new apiClient();
    	$client->setClientId(self::getGpClientId());
    	$client->setClientSecret(self::getGpClientSecret());
    	$client->setRedirectUri(self::getRedirectUri());
    	return $client;
    }
    
    public function getApiPlus($client){
    	return new apiPlusService($client);
    }
    
    public function getApiOauth2($client){
    	return new apiOauth2Service($client);
    }
    
    public function getAuthUrl(){
    	$client = self::getApiClient();
    	
    	
		
		$plus = self::getApiPlus($client);
		$oauth2 = self::getApiOauth2($client);
    	//$client->();
    	return $client->createAuthUrl();
    }
}











?>