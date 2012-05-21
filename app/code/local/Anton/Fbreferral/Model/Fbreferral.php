<?php

require  'facebook.php';

class Anton_Fbreferral_Model_Fbreferral extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('fbreferral/fbreferral');
    }
    
	/*get facebook feferral config*/
    public function  getFbStatus(){
   		return Mage::getStoreConfig('fbreferral/general/fb_status');
    }
    
    public function  getFbAppid(){
   		return Mage::getStoreConfig('fbreferral/general/fb_app_id');
    }
    
    public function  getFbAppsecret(){
   		return Mage::getStoreConfig('fbreferral/general/fb_app_secret');
    }
    
    public function  getFbDiscountamount(){
   		return Mage::getStoreConfig('fbreferral/general/fb_discount_amount');
    }
    
    public function  getFbOrderamount(){
   		return Mage::getStoreConfig('fbreferral/general/fb_order_amount');
    }
    
    public function  getFbFeedmassage(){
   		return Mage::getStoreConfig('fbreferral/general/fb_feed_message');
    }
    
    public function  getFbCartshowmassage(){
   		return Mage::getStoreConfig('fbreferral/general/fb_cartshow_message');
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
                'redirect_uri'  => Mage::getBaseUrl().'fbreferral/index/response'
    		)
    	);
    }
    
    /* updatae facebook user log */
    public function setFacebookuser($fbuser){
   		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
   		$fbreferral = Mage::getSingleton('core/resource')->getTableName('fbreferral');
   		$sql = "insert into $fbreferral (fbuser,content,status) values ( $fbuser ,1,1)";
		$res = $write->query($sql);
    }
    
    /* get facebook user */
    public function getFacebookuser($fbuser){
   		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
   		$fbreferral = Mage::getSingleton('core/resource')->getTableName('fbreferral');
		$sql = "select * from $fbreferral where fbuser = $fbuser";
		return $read->fetchAll($sql);
    }
    
	/* updatae facebook user count */
    public function getFacebookuserCount($fbuser){
		return count(self::getFacebookuser($fbuser));
    }
}










