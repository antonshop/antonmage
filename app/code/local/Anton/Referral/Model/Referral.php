<?php

require  'facebook.php';
require_once 'twitteroauth/twitteroauth.php';

class Anton_Referral_Model_Referral extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('referral/referral');
    }
    
	/* get referral general config */
    public function  getFbFeedmassage(){
   		return Mage::getStoreConfig('referral/general/feed_message');
    }
    
    public function  getFbCartmessage(){
    	$message = Mage::getStoreConfig('referral/general/cartshow_message');
  		$message = str_replace('{money}','$'.self::getFbDiscountamount(), $message);
   		return $message;
    }
    
    public function  getFbDiscountamount(){
   		return Mage::getStoreConfig('referral/general/discount_amount');
    }
    
    public function  getFbOrderamount(){
   		return Mage::getStoreConfig('referral/general/order_amount');
    }

    /* get referral facebook config */
    public function  getFbStatus(){
   		return Mage::getStoreConfig('referral/facebook/fb_status');
    }
    
    public function  getFbAppid(){
   		return Mage::getStoreConfig('referral/facebook/fb_app_id');
    }
    
    public function  getFbAppsecret(){
   		return Mage::getStoreConfig('referral/facebook/fb_app_secret');
    }
    
    /* get referral twitter config */
	public function  getTwStatus(){
   		return Mage::getStoreConfig('referral/twitter/tw_status');
    }
    
	public function  getTwConsumerkey(){
   		return Mage::getStoreConfig('referral/twitter/tw_consumer_key');
    }
    
	public function  getTwConsumersecret(){
   		return Mage::getStoreConfig('referral/twitter/tw_consumer_secret');
    }
    
	public function  getTwCallbackurl(){
   		return Mage::getStoreConfig('referral/twitter/tw_callback_url');
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
                'redirect_uri'  => Mage::getBaseUrl().'referral/index/fbrespond'
    		)
    	);
    }
    
    /* updatae facebook user log */
    public function setReferraluser($fbuser, $type=1){
   		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
   		$referral = Mage::getSingleton('core/resource')->getTableName('referral');
   		$sql = "insert into $referral (`fbuser`, `content`, `status`, `type`) values ( $fbuser , 1, 1, $type)";
		$res = $write->query($sql);
    }
    
    /* get facebook user */
    public function getReferraluser($fbuser, $type=1){
   		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
   		$referral = Mage::getSingleton('core/resource')->getTableName('referral');
		$sql = "select * from $referral where fbuser = '".$fbuser."' and type = $type";
		return $read->fetchAll($sql);
    }
    
	/* updatae facebook user count */
    public function getReferraluserCount($fbuser, $type=1){
		return count(self::getReferraluser($fbuser, $type));
    }
    
    /* get twitter oauth */
    public function getTwitterOauth(){
    	
    	/* Build TwitterOAuth object with client credentials. */
		$connection = new TwitterOAuth($this->getTwConsumerkey(), $this->getTwConsumersecret());
		 
		/* Get temporary credentials. */
		//$request_token = $connection->getRequestToken(Mage::getBlockSingleton('referral/referral')->getResponseUrl('tw'));
//echo Mage::getBlockSingleton('referral/referral')->getResponseUrl('tw');exit;
		
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










