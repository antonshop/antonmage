<?php
/**
 * Appmagento
 *
 * @category    Appmagento
 * @package     Appmagento_Sharesuit
 * @copyright   Copyright (c) 2011 http://www.appmagento.com
 */

require  'facebook.php';
require_once 'twitteroauth/twitteroauth.php';

class Appmagento_Sharesuit_Model_Sharesuit extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('sharesuit/sharesuit');
    }
    
	/* get sharesuit general config */
    public function  getFbFeedmassage(){
   		return Mage::getStoreConfig('sharesuit/general/feed_message');
    }
    
    public function  getFbCartmessage(){
    	$message = Mage::getStoreConfig('sharesuit/general/cartshow_message');
  		$message = str_replace('{discount_amount}','$'.self::getFbDiscountamount(), $message);
   		return $message;
    }
    
    public function  getFbDiscountamount(){
   		return Mage::getStoreConfig('sharesuit/general/discount_amount');
    }
    
    public function  getFbOrderamount(){
   		return Mage::getStoreConfig('sharesuit/general/order_amount');
    }

    /* get sharesuit facebook config */
    public function  getFbStatus(){
   		return Mage::getStoreConfig('sharesuit/facebook/fb_status');
    }
    
    public function  getFbAppid(){
   		return Mage::getStoreConfig('sharesuit/facebook/fb_app_id');
    }
    
    public function  getFbAppsecret(){
   		return Mage::getStoreConfig('sharesuit/facebook/fb_app_secret');
    }
    
    /* get sharesuit twitter config */
	public function  getTwStatus(){
   		return Mage::getStoreConfig('sharesuit/twitter/tw_status');
    }
    
	public function  getTwConsumerkey(){
   		return Mage::getStoreConfig('sharesuit/twitter/tw_consumer_key');
    }
    
	public function  getTwConsumersecret(){
   		return Mage::getStoreConfig('sharesuit/twitter/tw_consumer_secret');
    }
    
	public function  getTwCallbackurl(){
   		return Mage::getStoreConfig('sharesuit/twitter/tw_callback_url');
    }
    
    
	public function getFacebook()
    {
        $app_id = self::getFbAppid();
		$app_secret= self::getFbAppsecret();

		/*facebook object*/
		return $facebook = new Facebook(array(
                    'appId'  => $app_id,
                    'secret' => $app_secret,
                    'cookie' => false,
		));
    }
    
    public function getFacebookLoginUrl(){
    	
    	$facebook = self::getFacebook();
    	return $facebook->getLoginUrl(
    		array(
    			'scope'         => 'email, user_location, offline_access, user_birthday, user_about_me, user_hometown, publish_stream',
                'redirect_uri'  => Mage::getBaseUrl().'sharesuit/index/fbrespond'
    		)
    	);
    }
    
    /* updatae facebook user log */
    public function setSharesuituser($fbuser, $type=1){
   		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
   		$sharesuit = Mage::getSingleton('core/resource')->getTableName('sharesuit');
   		$sql = "insert into $sharesuit (`fbuser`, `content`, `status`, `type`) values ( $fbuser , 1, 1, $type)";
		$res = $write->query($sql);
    }
    
    /* get facebook user */
    public function getSharesuituser($fbuser, $type=1){
   		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
   		$sharesuit = Mage::getSingleton('core/resource')->getTableName('sharesuit');
		$sql = "select * from $sharesuit where fbuser = '".$fbuser."' and type = $type";
		return $read->fetchAll($sql);
    }
    
	/* updatae facebook user count */
    public function getSharesuituserCount($fbuser, $type=1){
		return count(self::getSharesuituser($fbuser, $type));
    }
    
    /* get twitter oauth */
    public function getTwitterOauth(){
    	
    	/* Build TwitterOAuth object with client credentials. */
		$connection = new TwitterOAuth($this->getTwConsumerkey(), $this->getTwConsumersecret());
		 
		/* Get temporary credentials. */
		//$request_token = $connection->getRequestToken(Mage::getBlockSingleton('sharesuit/sharesuit')->getResponseUrl('tw'));
//echo Mage::getBlockSingleton('sharesuit/sharesuit')->getResponseUrl('tw');exit;
		
		/* Save temporary credentials to session. */
//		$_SESSION['oauth_token'] = $request_token['oauth_token'];
//		$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
		
		return $connection;
    }
    
    public function getUserTwitterOauth(){
    	/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
    	//$config = array();
//print_r($config);exit;
		return new TwitterOAuth($this->getTwConsumerkey(), $this->getTwConsumersecret(), $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
    }
	
    /* get twitter url */
    public function getTwUrl(){
    	if(isset($_SESSION['oauth_token'])){
    		return $this->getTwitterOauth()->getAuthorizeURL($_SESSION['oauth_token']);
    	} 
    }
    
}










