<?php

require  'facebook.php';

class Anton_Referral_Model_Referral extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('referral/referral');
    }
    
	/*get facebook referral config*/
    public function  getFbStatus(){
   		return Mage::getStoreConfig('referral/general/fb_status');
    }
    
    public function  getFbAppid(){
   		return Mage::getStoreConfig('referral/general/fb_app_id');
    }
    
    public function  getFbAppsecret(){
   		return Mage::getStoreConfig('referral/general/fb_app_secret');
    }
    
    public function  getFbDiscountamount(){
   		return Mage::getStoreConfig('referral/general/fb_discount_amount');
    }
    
    public function  getFbOrderamount(){
   		return Mage::getStoreConfig('referral/general/fb_order_amount');
    }
    
    public function  getFbFeedmassage(){
   		return Mage::getStoreConfig('referral/general/fb_feed_message');
    }
    
    public function  getFbCartmassage(){
    	$message = Mage::getStoreConfig('referral/general/fb_cartshow_message');
  		$message = str_replace('{money}',self::getFbDiscountamount(), $message);
   		return $message;
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
   		$sql = "insert into $referral (`fbuser`, `content`, `status`, `type`) values ( $fbuser ,1 ,1, $type)";
		$res = $write->query($sql);
    }
    
    /* get facebook user */
    public function getReferraluser($fbuser, $type=1){
   		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
   		$referral = Mage::getSingleton('core/resource')->getTableName('referral');
		$sql = "select * from $referral where fbuser = $fbuser and type = $type";
		return $read->fetchAll($sql);
    }
    
	/* updatae facebook user count */
    public function getReferraluserCount($fbuser){
		return count(self::getReferraluser($fbuser));
    }
}










