<?php

class Zizio_Powershare_Helper_Data extends Mage_Core_Helper_Abstract
{
	/*
	 * date formats
	 */
	
	const DATE_PART = "yyyy-MM-dd";
	const DATETIME_PART = "yyyy-MM-dd HH:mm:ss zzz";
	const DATETIME_NO_TZ_PART = "yyyy-MM-dd HH:mm:ss";
	const DATETIME_JS_PART 	= "MMM dd yyyy HH:mm:ss zzz";

	/*
	 * config paths
	 */
	
	// magento wide
	const MODULE_DISABLE_OUTPUT_PATH = 'advanced/modules_disable_output/Zizio_Powershare';
	
	// zizio wide
	const REFRESH_ENC_KEY_PATH =    'zizio/refresh_enc_key';
	const ZIZIO_REG_PREFIX = 	    'zizio/reg/'; 
	const ACCOUNT_NOTIFIED_PATH =   'zizio/reg/account_notified';
	const DBCHANGES_NOTIFIED_PATH = 'zizio/powershare/dbchanges_notified';
	const ZIZIO_PUB_ID_PATH = 	    'zizio/reg/zizio_pub_id';
	const ZIZIO_ENC_KEY_PATH =      'zizio/reg/zizio_enc_key';
	const BASE_URL_PATH = 		    'zizio/settings/base_url';
	const DEBUG_PATH = 			    'zizio/settings/debug';
	const PORT_PATH = 			    'zizio/settings/port';
	const PROTOCOL_PATH = 		    'zizio/settings/protocol';
	
	// module related
	const DESIGN_SKIN_PATH = 		     'zizio/powershare/settings/design_skin';
	const ZIZIO_ACCOUNT_URL_PATH =       'zizio/powershare/settings/zizio_account_url';
	const POWERSHARE_REGISTERED_PATH =   'zizio/powershare/registered';
	const CRON_HOURLY_LAST_RUN_PATH =    'zizio/powershare/cron/hourly_last_run';
	const CRON_DAILY_LAST_RUN_PATH =     'zizio/powershare/cron/daily_last_run';
	const MESSAGES_LAST_GET_PATH = 	     'zizio/powershare/messages_last_get';	
	const PRODUCT_ENABLED_PATH = 	     'zizio/powershare/settings/product_page/enabled';
	const PRODUCT_POWERSHARE_BARS_PATH = 'zizio/powershare/settings/product_page/powershare_bars';
	const SUCCESS_ENABLED_PATH = 	     'zizio/powershare/settings/success_page/enabled';
	const SUCCESS_BUTTONS_PATH = 		 'zizio/powershare/settings/success_page/buttons';
	const SUCCESS_CSS_SELECTOR_PATH =    'zizio/powershare/settings/success_page/css_selector';
	const SUCCESS_POSITION_PATH = 	     'zizio/powershare/settings/success_page/position';
	const SUCCESS_CONT_CAPTION_PATH =    'zizio/powershare/settings/success_page/cont_caption';
	const SUCCESS_SHARE_CAPTION_PATH =   'zizio/powershare/settings/success_page/share_caption';
	const NOTIFY_PINIT_PATH = 		     'zizio/powershare/notify_pinit';

	// urls
	const REFRESH_ENC_KEY_URL =		   '/powers/mage/admin/admin_refresh_enc_key';
	const ADMIN_GET_DECORATION_URL =   '/powers/mage/admin/admin_get_decoration';
	const ADMIN_SET_DECORATION_URL =   '/powers/mage/admin/admin_set_decoration';
	
	/*
	 * config data
	 */
	
	static $EXT_TYPE = "powershare";
	static $BASE_URL = "";
	static $DEBUG = null;
	static $DESIGN_SKIN = "";
	static $JS_VER = "1.0";
	static $PORT = "";
	static $PROTOCOL = "";
	static $ZIZIO_ACCOUNT_URL = "";

	/*
	 * runtime data storage
	 */
	
	static $PowershareTreated = false;
	static $PathToHelper = null;
	static $ScriptUrlArgs = array();
	static private $_default_watermark = null;
	static private $_watermarks = null;

	/*
	 * Constructors
	 */

	/**
	 * Constructor
	 */
	public function _construct()
	{
		$this->_moduleName = "Zizio_PowerShare";
		
		parent::_construct();
	}

	/**
	 * Static constructor
	 * Should be invoked only once - at the bottom of this file.
	 */
	public static function StaticConstructor ()
	{
		// Fetch parameters to connect to our server from the Magento configuration.
		self::$BASE_URL = Mage::getStoreConfig(self::BASE_URL_PATH);
		self::$DEBUG = Mage::getStoreConfig(self::DEBUG_PATH) ? true : false;
		self::$DESIGN_SKIN = Mage::getStoreConfig(self::DESIGN_SKIN_PATH);
		self::$PORT = Mage::getStoreConfig(self::PORT_PATH);
		self::$PROTOCOL = Mage::getStoreConfig(self::PROTOCOL_PATH);
		self::$ZIZIO_ACCOUNT_URL = Mage::getStoreConfig(self::ZIZIO_ACCOUNT_URL_PATH);

		// Note that if config values are missing, we default to hard-coded values:
		if (self::$BASE_URL === null)
			self::$BASE_URL = "widgets.zizio.com";
		if (self::$DESIGN_SKIN === null)
			self::$DESIGN_SKIN = "v1.0";
		if (self::$PORT === null)
			self::$PORT = "";
		if (self::$PROTOCOL === null)
			self::$PROTOCOL = "https";
		if (self::$ZIZIO_ACCOUNT_URL === null)
			self::$ZIZIO_ACCOUNT_URL = "www.zizio.com/account";
			
		// Physical path to the location of our helper files
		self::$PathToHelper = Mage::getConfig()->getModuleDir('', 'Zizio_Powershare') . DS . 'Helper';
	}

	/*
	 * Own settings helpers
	 */

	/**
	 * Called only on fresh installation, after filling out registration form (/Zizio/Powershare/controllers/Adminhtml/ZizioregController.php)
	 */
	public static function InitializeSettings()
	{
		self::SaveSetting(self::DESIGN_SKIN_PATH, "v1.0");
		self::SaveSetting(self::PRODUCT_ENABLED_PATH, "1");
		self::SaveSetting(self::SUCCESS_ENABLED_PATH, "1");
		self::SaveSetting(self::SUCCESS_CSS_SELECTOR_PATH, "");
		self::SaveSetting(self::SUCCESS_POSITION_PATH, "bottom");
		self::SaveSetting(self::SUCCESS_CONT_CAPTION_PATH, "Share the joy!");
		self::SaveSetting(self::SUCCESS_SHARE_CAPTION_PATH, "Tell your Friends");
		self::SaveSetting(self::ACCOUNT_NOTIFIED_PATH, "1"); // Disable Zizio Account notification
		self::SaveSetting(self::NOTIFY_PINIT_PATH, "1"); // Disable new PinIt share button notification
		self::SaveSetting(self::DBCHANGES_NOTIFIED_PATH, "1"); // Disable DB Changes notification

		// Refresh store config
		Mage::app()->getStore(null)->resetConfig();

		// Clear DB cache
		self::ClearDBSchemaCaches();
	}

	public static function GetNotificationBoxHtml($title, $content)
	{
		$body = "";
		foreach ($content as $item)
		{
			if ($item['type'] == "text")
			{
				$body .= "<p class='z-message-text'>{$item['text']}</p>";
			}
			else if ($item['type'] == "link")
			{
				$extra_attrs = "";
				if (isset($item['target']))
					$extra_attrs = "target='{$item['target']}'";
				$body .= "<p class='z-read-more'><a href='{$item['link']}' style='float: right; margin-bottom: 12px;' {$extra_attrs}>{$item['caption']}</a></p>";
			}
		}

		$html = '
<style type="text/css">
	#z-message-popup-window-mask {
	    background-color: #EFEFEF;
	    bottom: 0;
	    height: 100%;
	    left: 0;
	    opacity: 0.5;
	    filter: alpha(opacity=50);
	    position: absolute;
	    right: 0;
	    top: 0;
	    width: 100%;
	    z-index: 9999;
	}

	.z-message-popup.z-show {
		top: 280px;
		display: block;
	}

	.z-message-popup {
		background: none repeat scroll 0 0 #F3BF8F;
		left: 50%;
		margin: 0 0 0 -203px;
		padding: 0 4px 4px;
		position: absolute;
		width: 407px;
		z-index: 99999;
		display: none;
	}

	.z-message-popup .z-message-popup-head:after, .z-message-popup .z-message-popup-content .z-message:after {
		clear: both;
		content: ".";
		display: block;
		font-size: 0;
		height: 0;
		line-height: 0;
		overflow: hidden;
	}

	.z-message-popup .z-message-popup-head {
		padding: 1px 0;
	}

	.z-message-popup .z-message-popup-head a {
		background: url("/skin/adminhtml/default/default/images/bkg_btn-close.gif") repeat-x scroll 0 50% #D97920 !important;
		border: 1px solid #EA7601;
		color: #FFFFFF;
		cursor: pointer;
		float: right;
		font: 12px/17px Arial,Helvetica,sans-serif;
		padding: 0 12px 0 7px;
		text-decoration: none !important;
		position: relative;
		width: 47px;
		height: 17px;
	}

	.z-message-popup .z-message-popup-head a i {
		position: absolute;
		left: 11px;
	}

	.z-message-popup .z-message-popup-head a span {
		position: absolute;
		left: 7px;
		padding-left: 19px;
		background: url("/skin/adminhtml/default/default/images/bkg_btn-close2.gif") no-repeat scroll 0 50% transparent;
	}

	.z-message-popup .z-message-popup-head h2 {
		color: #644F3B;
		font: bold 12px/19px Arial,Helvetica,sans-serif;
		margin: 0;
		padding: 0 10px;
	}

	.z-message-popup .z-message-popup-content {
		background: none repeat scroll 0 0 #FDF4EB;
		padding: 21px 21px 10px;
		width: 365px;
	}

	.z-message-popup .z-message-popup-content .z-message-icon {
		color: #659601;
		background-position: 50% 0;
		background-repeat: no-repeat;
		float: left;
		font-size: 10px;
		line-height: 12px;
		overflow: hidden;
		padding: 47px 0 0;
		text-align: center;
		text-transform: uppercase;
		width: 50px;
	}

	.z-message-popup .z-message-popup-content .z-read-more {
		margin: 7px 0 0;
		text-align: right;
	}

	.z-message-popup .z-message-popup-content .z-message-text {
		color: #644F3B;
		float: right;
		clear: right;
		/* min-height: 4.5em; */
		overflow: hidden;
		width: 295px;
	}

	.z-message p {
		text-align: left;
	}

	.z-clearfix {
		clear: both;
	}
</style>
<script type="text/javascript">
//<![CDATA[
	var messagePopupClosed = false;
	function openMessagePopup() {
		var height = $("html-body").getHeight();
       	$("z-message-popup-window-mask").setStyle({"height":height+"px"});
       	toggleSelectsUnderBlock($("z-message-popup-window-mask"), false);
       	Element.show("z-message-popup-window-mask");
       	$("z-message-popup-window").addClassName("z-show");
   	}

   	function closeMessagePopup() {
       	toggleSelectsUnderBlock($("z-message-popup-window-mask"), true);
       	Element.hide("z-message-popup-window-mask");
       	$("z-message-popup-window").removeClassName("z-show");
       	messagePopupClosed = true;

       	if ($("message-popup-window-mask"))
			Element.hide("message-popup-window-mask");
		if ($$(".flash-window")[0])
			$$(".flash-window")[0].remove();
   	}

   	Event.observe(window, "keyup", function(evt) {
       	if(messagePopupClosed) return;
       	var code;
       	if (evt.keyCode) code = evt.keyCode;
       	else if (evt.which) code = evt.which;
       	if (code == Event.KEY_ESC) {
           	closeMessagePopup();
       	}
   	});

   	if ($("z-message-popup-window"))
   		$("z-message-popup-window").remove();
   	if ($("z-message-popup-window-mask"))
	   	$("z-message-popup-window-mask").remove();
//]]>
</script>
<div id="z-message-popup-window-mask" style="display:none;"></div>
<div id="z-message-popup-window" class="z-message-popup">
   <div class="z-message-popup-head">
       <a href="#" onclick="closeMessagePopup(); return false;" title="close"><i>x</i><span>close</span></a>
       <h2>' . $title . '</h2>
   </div>
   <div class="z-message-popup-content">

       <div class="z-message">
           <span class="z-message-icon z-message-notice" style="background-image:url(http://widgets.magentocommerce.com/SEVERITY_NOTICE.gif);">notice</span>
			' . $body .'
       </div>

       <div class="z-clearfix"></div>

   </div>
</div>
<script type="text/javascript">
//<![CDATA[
	if ($("message-popup-window-mask"))
		Element.hide("message-popup-window-mask");
	if ($("message-popup-window"))
		Element.hide("message-popup-window");
	if ($$(".flash-window")[0])
		$$(".flash-window")[0].remove();
	openMessagePopup();
//]]>
</script>';

		return $html;
	}

	/**
	 * Store default zizio query string arguments for current request
	 */
	public static function StoreScriptUrlArgs($args=array())
	{
		self::$ScriptUrlArgs = self::$ScriptUrlArgs + $args;
	}

	/**
	 * Get default zizio query string arguments for current request
	 */
	public static function GetScriptUrlArgs()
	{
		return
			self::$ScriptUrlArgs +
			array(
				'p' => self::GetPublisherId(),
				'v' => self::$JS_VER,
				'd' => self::$DESIGN_SKIN,
				'l' => self::GetLocaleCode()
			);
	}

	public static function IsZizioPowershareTreated ()
	{
		return self::$PowershareTreated;
	}

	public static function SetZizioPowershareTreated ($treated = true)
	{
		self::$PowershareTreated = $treated;
	}

	public static function IsZizioPowershareEnabled ()
	{
		return (Mage::getStoreConfig(self::MODULE_DISABLE_OUTPUT_PATH) != "1");
	}

	public static function IsZizioPowershareRegistered ()
	{
		$registered = Mage::getModel('core/config_data')->load(self::POWERSHARE_REGISTERED_PATH, 'path');
		return ($registered && ($registered->getValue() == "1"));
	}

	public static function IsEnterpriseEdition()
	{
		$modules = Mage::getConfig()->getNode('modules')->children();
		$modulesArray = (array)$modules;
		return isset($modulesArray['Enterprise_Enterprise']);
	}
	
	public static function CheckEncKey ()
	{
		if (self::GetConfigValue(self::REFRESH_ENC_KEY_PATH) == "1")
			return;

    	$args = array();
		$response = self::CallUrl(self::REFRESH_ENC_KEY_URL, $args, null, true);
        if ($response && isset($response['result']))
        {
        	if ($response['result'] == 'OK')
        	{
        		if (isset($response['enc_key']))
        		{
        			self::SaveEncriptionKey($response['enc_key']);
        		}

	        	self::SaveConfigValue(self::REFRESH_ENC_KEY_PATH, "1");
        	}
        }
	}

	public static function IsNotifiedAboutZizioAccount()
	{
		$notified = Mage::getModel('core/config_data')->load(self::ACCOUNT_NOTIFIED_PATH, 'path');
		return ($notified && ($notified->getValue() == "1"));
	}

	public static function IsNotifiedAboutPinIt()
	{
		$notified = Mage::getModel('core/config_data')->load(self::NOTIFY_PINIT_PATH, 'path');
		return ($notified && ($notified->getValue() == "1"));
	}

	public static function IsNotifiedAboutDbChanges()
	{
		$notified = Mage::getModel('core/config_data')->load(self::DBCHANGES_NOTIFIED_PATH, 'path');
		return ($notified && ($notified->getValue() == "1"));
	}

	public static function GetPublisherId ()
	{
		$pub_id_entry = Mage::getModel('core/config_data')->load(self::ZIZIO_PUB_ID_PATH, 'path');
		if ($pub_id_entry)
		{
			$pub_id = $pub_id_entry->getValue();
			return $pub_id;
		}
		else
		{
			return null;
		}
	}

	public static function GetEncriptionKey ()
	{
		$enc_key_entry = Mage::getModel('core/config_data')->load(self::ZIZIO_ENC_KEY_PATH, 'path');
		if ($enc_key_entry)
		{
			$enc_key = $enc_key_entry->getValue();
			if ($enc_key)
				return $enc_key;
		}
		return null;
	}


	public static function GetToken ($args, $base64 = true)
	{
		$enc_key = self::GetEncriptionKey();

		ksort($args);
		$_args = array();
		foreach($args as $k => $v)
			$_args[] = "{$k}={$v}";
		$str = md5(implode('&', $_args) . $enc_key);
		return $str;
	}

	public static function Encrypt ($data, $base64 = true)
	{
		$enc_key = self::GetEncriptionKey();
		$token = mcrypt_encrypt(MCRYPT_3DES, $enc_key, $data, MCRYPT_MODE_ECB, "\0\0\0\0\0\0\0\0");

		if ($base64)
			$token = self::ToBase64($token);

		return $token;
	}

	public static function ToBase64 ($str)
	{
		return str_replace(array('+', '/'), array('-', '_'), base64_encode($str));
	}

	public static function SavePublisherId ($zizio_pub_id)
	{
		$pub_id_entry = Mage::getModel('core/config_data')
			->load(self::ZIZIO_PUB_ID_PATH, 'path')
			->setValue($zizio_pub_id)
			->setPath(self::ZIZIO_PUB_ID_PATH)
			->save();
	}

	public static function SaveEncriptionKey ($zizio_enc_key)
	{
		$enc_key_entry = Mage::getModel('core/config_data')
			->load(self::ZIZIO_ENC_KEY_PATH, 'path')
			->setValue($zizio_enc_key)
			->setPath(self::ZIZIO_ENC_KEY_PATH)
			->save();
	}

	public static function SaveLastRemoteMessagesDate ()
	{
		self::SaveConfigDate(self::MESSAGES_LAST_GET_PATH);
	}

	public static function GetLastRemoteMessagesDate ()
	{
		return self::GetConfigDate(self::MESSAGES_LAST_GET_PATH);
	}

	public static function SaveConfigValue ($path, $value)
	{
		$entry = Mage::getModel('core/config_data')
			->load($path, 'path')
			->setValue($value)
			->setPath($path)
			->save();
	}

	public static function HasConfigValue ($path)
	{
		$entry = Mage::getModel('core/config_data')->load($path, 'path');
		if ($entry && $entry->hasValue())
			return true;
		return false;
	}
	
	public static function GetConfigValue ($path)
	{
		$entry = Mage::getModel('core/config_data')->load($path, 'path');
		if ($entry)
			return $entry->getValue();

		return null;
	}

	public static function GetDefaultConfig ($path)
	{
		$node = Mage::getConfig()->getNode("default/" . $path);
		if ($node)
			return (string) $node;
		return null;
	}

	public static function SaveSetting ($key, $value)
	{
		$setting = Mage::getModel('core/config_data')->load($key, 'path');
		if (!$setting)
			$setting = Mage::getModel('core/config_data');
		$setting->setValue($value);
		$setting->setPath($key);
		$setting->save();
	}

	public static function GetSetting ($key, $use_default=false)
	{
		if ($use_default)
		{
			$setting = Mage::getModel('core/config_data')->load($key, 'path');
			return $setting ? $setting->getValue() : null;
		}
		else
		{
			$setting = Mage::app()->getStore()->getConfig($key);
			return $setting;
		}
	}

	public static function GetStoresData($ids_only=false)
	{
		$stores = array();
		foreach (Mage::app()->getWebsites(true) as $website)
		{
			$website_id = (string) $website->getId();
			if ($ids_only)
				$stores[$website_id] = array();
			else
				$stores[$website_id] = array(
					'name' => $website->getName(),
					'urls' => array()
				);
			foreach ($website->getStoreCollection() as $store)
			{
				$id = (string) $store->getId();
				if ($ids_only)
					$stores[$website_id][] = $id;
				else
					$stores[$website_id]['urls'][$id] = array(
						'name' => $store->getName(),
						'url'  => $store->getBaseUrl()
					);
			}
		}
		return $stores;
	}

	public static function SaveConfigDate ($path)
	{
		self::SaveConfigValue($path, self::DateTimeToString( self::DateTimeToUTC() ));
	}

	public static function GetConfigDate ($path)
	{
		$entry = Mage::getModel('core/config_data')->load($path, 'path');
		if ($entry && $entry->hasValue())
			return self::DateTimeToUTC($entry->getValue());
		return null;
	}

	/*
	 * Returns the version of the extension that's currently installed as a string. ex: "0.1.0"
	 * $default - a default value to be returned in case the version cannot be retrieved.
	 */
	public static function GetExtVer ($default = null)
	{
		//return Mage::getResourceSingleton('core/resource')->getDbVersion("powershare_setup");
		$config = Mage::getConfig();
		if ($config != null)
		{

			$moduleConfig = $config->getModuleConfig('Zizio_Powershare');
			if ($moduleConfig != null)
			{
				return (string)$moduleConfig->version;
			}
		}
		return $default;
	}

	public static function GetExtType ()
	{
		return self::$EXT_TYPE;
	}

	public static function ClearDBSchemaCaches()
	{
		$app = Mage::app();
		if ($app != null)
		{
			$cache = $app->getCache();
			if ($cache != null)
			{
				$cache->clean('matchingTag', array('DB_PDO_MYSQL_DDL'));
			}
		}
	}
	
	public static function ClearFPCCache($only_once=false)
	{
		if (!$only_once || Mage::registry("zizio/fpc_cleaned") == null)
		{
			Mage::app()->cleanCache('FPC');
			if ($only_once)
				Mage::register("zizio/fpc_cleaned", true);
		}
	}

	public static function Register ($data=array())
	{
		// check if publisher is already registered
		if (self::IsZizioPowershareRegistered())
			return true;

		$session = Mage::getSingleton('admin/session');

		$reg_fields = array(
			'do'  => "register",
			'et'  => self::GetExtType(),
			'ev'  => self::GetExtVer(),
			'uid' => isset($data['zizio_user_id']) ? $data['zizio_user_id'] : "",
			'un'  => $session->getZizioUsername(),
			'pw'  => $session->getZizioPassword(),
			'mv'  => Mage::getVersion(), // magento version
			'me'  => self::IsEnterpriseEdition() ? "ent" : "comm", // magento edition
		 	'e'   => $session->getZizioUsername(),
			'fn'  => isset($data['firstname']) ? $data['firstname'] : "",
			'ln'  => isset($data['lastname']) ? $data['lastname'] : "",
			'p'   => isset($data['phone']) ? $data['phone'] : "",
			'sn'  => isset($data['storename']) ? $data['storename'] : "",
			'cc'  => isset($data['coupon']) ? $data['coupon'] : ""
	 	);
		if (self::GetPublisherId())
			$reg_fields['id'] = self::GetPublisherId();	// existing publisher id

		$response = self::SendUrl(self::GetRegisterUrl(), $reg_fields);
		if ($response)
		{
			$response = self::json_decode(trim($response, "()"), true);
		}
		if (!$response || !isset($response['register']) || ($response['register'] != "success")) {
			$session = Mage::getSingleton('adminhtml/session');
			if ($session)
			{
				$error = "Unknown error, Please contact support@zizio.com";	
				
				if (is_array($response))
				{
					if (isset($response['invalid_email']))
						$error = "Invalid Zizio Account e-mail address, click 'Switch account' to login again.";
					elseif (isset($response['login_incorrect']))
						$error = "Invalid Zizio Account username / password, click 'Switch account' to login again.";
				}
				
				$session->addError("Zizio registration error - {$error}");
			}
			return false;
		}

		// don't overwrite existing settings
		$data = array();
		if (isset($response['pub_id']) && !self::GetPublisherId()) {
			$data['zizio_pub_id'] = $response['pub_id'];
		}
		if (isset($response['enc_key']) && (!self::GetEncriptionKey() || isset($_data['pub_id']))) {
			$data['zizio_enc_key'] = $response['enc_key'];
		}

		// save data
		self::_SaveRegData($data);

		// initialize
		self::SaveLastRemoteMessagesDate();
		self::SaveConfigDate(self::CRON_HOURLY_LAST_RUN_PATH);
		self::SaveConfigDate(self::CRON_DAILY_LAST_RUN_PATH);
		self::SendAdminPing(self::GetPublisherId());
		self::InitializeSettings();

		// mark publisher registered
		$registered = Mage::getModel('core/config_data')
			->setValue("1")
			->setPath(self::POWERSHARE_REGISTERED_PATH)
			->save();

		return true;
	}

	public static function _SaveRegData ($data)
	{
		foreach ($data as $key => $item)
		{
			$path = self::ZIZIO_REG_PREFIX . $key;
			$entry = Mage::getModel('core/config_data')
				->load($path, 'path')
				->setValue($item)
				->setPath($path)
				->save();
		}
	}

	public static function SendAdminPing ($zizio_pub_id)
	{
		$url = sprintf("%s://%s%s/powers/mage/admin/ping", self::$PROTOCOL, self::$BASE_URL, self::$PORT);
		$data = array();
		$data['pub_id'] = $zizio_pub_id;
		$data['ext_type'] = self::GetExtType();
		$data['ext_ver'] = self::GetExtVer("");
		self::SendUrl($url, $data);
	}

	public static function CompareVersions ($a, $b)
	{
		$a = explode(".", $a);
		$b = explode(".", $b);
		$max_len = max(count($a), count($b));
		for ($i=0; $i<$max_len; $i++)
		{
			$diff = array_shift($a) - array_shift($b);
			if ($diff != 0)
				return $diff;
		}
		return 0;
	}

	/*
	 * Returns an array containing details about the latest version of the extension, fetched from
	 * the Zizio server.
	 */
	public static function GetRemoteMessages()
	{
		$last_call = self::GetLastRemoteMessagesDate();
		if ($last_call == null)
			$last_call = self::DateTimeToUTC(null);

		$url = sprintf("%s://%s%s/powers/mage/admin/messages?last_get=%s&pub_id=%s&mage_ver=%s&ext_type=%s&ext_ver=%s",
			self::$PROTOCOL,
			self::$BASE_URL,
			self::$PORT,
			urlencode(self::DateTimeToString($last_call, false)),
			urlencode(self::GetPublisherId()),
			urlencode(Mage::getVersion()),
			urlencode(self::GetExtType()),
			urlencode(self::GetExtVer(""))
		);
		$response = self::SendUrl($url, null);
		if ($response)
			return self::json_decode($response, true);
		else
			return null;
	}

	/*
	 * Logs an exception by sending it to our remote server.
	 */
	public static function LogError ($ex)
	{
		try
		{
			$url = sprintf("%s://%s%s/s/log_remote_error", self::$PROTOCOL, self::$BASE_URL, self::$PORT);

			$data = array();
			$data['pub_id'] = self::GetPublisherId();
			$data['msg'] = $ex->getMessage();
			$data['code'] = $ex->getCode();
			$data['file'] = $ex->getFile();
			$data['line'] = $ex->getLine();
			$data['ext_type'] = self::GetExtType();
			$data['ext_ver'] = self::GetExtVer("");

			$trace = $ex->getTrace();
			if ($trace == null)
				$trace = debug_backtrace();

			$count = 0;
			$data["stack_trace"] = '';
			foreach ($trace as $i => $step)
			{
				$row = sprintf("%s: %s at line %s\r\n",
					isset($step['function']) ? $step['function'] : '',
					isset($step['file']) ? $step['file'] : '',
					isset($step['line']) ? $step['line'] : '');
				$data["stack_trace"] .= $row;
				$count++;
				if ($count > 20)
					break;
			}

			self::SendUrl($url, $data);
		}
		catch (Exception $e)
		{
		}

		// If we're running inside a unit test, rethrow the exception so the unit test
		// knows about it and fails accordingly.
		if ((isset($GLOBALS["zizio_test_running"])) && ($GLOBALS["zizio_test_running"]))
		{
			throw $ex;
		}
	}

	public static function CallUrl ($relative_url, $args, $post_fields, $safe=true)
	{
        if ($args === null)
        	$args = array();
		$url = self::BuildScriptUrl($relative_url, $args, false);
        if ($safe)
        	$response = self::SendSafeUrl($url, $post_fields);
        else
        	$response = self::SendUrl($url, $post_fields);

		$response = self::json_decode($response, true);
		return $response;
	}

	public static function SendUrl ($url, $post_fields)
	{
		try
		{
			if (extension_loaded('curl'))
			{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_USERAGENT, "Zizio Mage Admin");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				if ($post_fields)
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
				$responseBody = curl_exec($ch);
				
				// check http code
				$info = curl_getinfo($ch);
				$code = isset($info['http_code']) ? $info['http_code'] : 0;
				if ($code != 200)
				{
					$session = Mage::getSingleton('adminhtml/session');
					if ($session)
						$session->addError(sprintf('Curl returned %s HTTP code for URL "%s". Contact your IT or support@zizio.com', $code, $url));
					return false;
				}					
				
				curl_close ($ch);
				return $responseBody;
			}
			else
			{
				$session = Mage::getSingleton('adminhtml/session');
				if ($session)
					$session->addError('No curl extension. Contact your IT or support@zizio.com');
				return false;
			}
		}
		catch (Exception $e)
		{
			$session = Mage::getSingleton('adminhtml/session');
			if ($session)
				$session->addError('Error in curl_exec execution. Contact your IT or support@zizio.com');
			return false;
		}
	}
	
	public function getFieldElement($elementId, $type, $config=array(), $form=null)
	{
        $type = strtolower($type);
		if (substr($type, 0, 6) == "zizio/")
        	$className = 'Zizio_Powershare_Model_Form_Element_'.ucfirst(substr($type, 6));
		else
			$className = 'Varien_Data_Form_Element_'.ucfirst($type);
		$element = new $className($config);
		$element->setId($elementId);
        if ($renderer = Varien_Data_Form::getFieldsetElementRenderer()) {
            $element->setRenderer($renderer);
        }
		if ($form)
        	$element->setForm($form);
		
		return $element;
	}
	
	/*
	 * Web / HTML helpers
	 */

	/**
	 * @note NOT STATIC due to use of __() method
	 */
	public function GetFieldNote($html, $alert_text=null, $alert_caption="Learn more")
	{
		$html = $this->__($html);
		if ($alert_text)
		{
			$alert_text = str_replace(array("\r\n", "\n"), "\\n", htmlentities($this->__($alert_text), ENT_QUOTES));
			$alert_caption = $this->__($alert_caption);
			$html .= " <a href='javascript:void(0);' onclick='alert(\"{$alert_text}\");'>{$alert_caption}</a>";
		}
		return "<p class='note'><span>{$html}</span></p>";
	}

	public static function BuildQS($args, $eq="=", $sep="&")
	{
		$enc_args = array();
		foreach ($args as $k=>$v) {
			$enc_args[] = urlencode($k) . $eq . urlencode($v);
		}
		return implode($sep, $enc_args);
	}

	public static function ParseNodeAttributes ($raw_attributes)
	{
		$matched_attributes = array();
		$attributes = array();
		if (preg_match_all('/(\w+)\s*=\s*(["\'])(.*?)\2/', $raw_attributes, $matched_attributes, PREG_SET_ORDER))
			foreach ($matched_attributes as $attribute)
				$attributes[$attribute[1]] = $attribute[3];
		return $attributes;
	}

	public static function BuildNode ($node_name, $attributes, $omittag = true)
	{
		$node_name = trim($node_name);
		$html_attributes = '';
		foreach ($attributes as $name => $val)
			$html_attributes .= "{$name}=\"".htmlentities($val)."\" ";
		if ($html_attributes != '')
			$html_attributes = " " . substr($html_attributes, 0, -1);
		$omittag = $omittag ? '' : ' /';
		return "<{$node_name}{$html_attributes}{$omittag}>";
	}

	public static function FindHtmlElement($html, $find, $multi = false)
	{
		$matches = array();
		$found = false;
		$end_offset = 0;
		while (!$found)
		{
			//$start_offset = strpos($html, "<{$find['tag_name']}", $end_offset);
			$preg_matches = array();
			$start_offset = preg_match("/\<({$find['tag_name']})/", substr($html, $end_offset), $preg_matches, PREG_OFFSET_CAPTURE) ? true : false;
			if ($start_offset)
			{
				$tag_name = $preg_matches[1][0];
				$start_offset = $preg_matches[1][1] + $end_offset - 1;
				$end_offset = strpos($html, ">", $start_offset);
				$tag = substr($html, $start_offset, $end_offset - $start_offset + 1);
				$found = true;

				// check attributes if given
				if (isset($find['attributes']) && is_array($find['attributes']))
				{
					// parse node attributes
					$raw_attributes = trim(substr($tag, strlen($tag_name) + 1, -1));
					$attributes = self::ParseNodeAttributes($raw_attributes);

					// check condition attributes
					foreach ($find['attributes'] as $name => $value)
					{
						if (!isset($attributes[$name]) || !preg_match($value, $attributes[$name]))
						{
							$found = false;
							break;
						}
					}
				}
			}
			else
				break;

			if ($found)
			{
				// add a match if found
				$matches[] = array(
					'tag' => $tag,
					'tag_name' => $tag_name,
					'attributes' => $attributes,
					'raw_attributes' => $raw_attributes,
					'start_offset' => $start_offset,
					'end_offset' => $end_offset,
				);

				// break loop for single-mode
				if (!$multi)
					break;

				// reset vars for multi-mode
				$found = false;
			}
		}

		// return array of matches for multi-mode or match (or null) for single-mode
		if ($multi)
			return $matches;
		else
			return array_shift($matches);
	}

	public static function json_decode ($data, $as_array=false)
	{
		if (function_exists('json_decode'))
			return json_decode($data, $as_array);

		// TODO: implement json_decode
		return null;
	}

	public static function json_encode ($data)
	{
		if (function_exists('json_encode'))
			return json_encode($data);

		// TODO: implement json_encode
		return null;
	}

	public static function GetScriptBlock ($src)
	{
		return sprintf("<script type='text/javascript' src='%s'></script>", $src);
	}

	/*
	 * Date / Time related
	 */

	public static function GetGmtOffset($timezoneString = null)
	{
		if ($timezoneString == null)
		{
			$timezoneString = Mage::app()->getStore()->getConfig('general/locale/timezone');
		}
		$zone = new DateTimeZone($timezoneString);
		$now = new DateTime("now", $zone);
		return $zone->getOffset($now);
	}

	//TODO Refactor all uses of Mage::getModel('core/date')->gmtDate() and Mage::getModel('core/date')->date()
	//TODO unit tests!
	/**
	 * Returns a string representation of a date converted to store timezone and store Locale
	 *
	 * @param   string|integer|Zend_Date|array|null $dateTime date in UTC
	 * @return  string
	 */
	public static function DateTimeToString($dateTime, $includeTZ = true)
	{
		if ($includeTZ)
		{
			return $dateTime->toString(self::DATETIME_PART);
		}
		else
		{
			return $dateTime->toString(self::DATETIME_NO_TZ_PART);
		}
	}

	/**
	 * Returns a string representation of the date (year-month-day) part of a date
	 * converted to store timezone and store Locale
	 *
	 * @param   string|integer|Zend_Date|array|null $dateTime date in UTC
	 * @return  string
	 */
	public static function DateTimeToDateString($dateTime)
	{
		return $dateTime->toString(self::DATE_PART);
	}

	/**
	 * Returns a string representation of a date converted to js format
	 *
	 * @param   Zend_Date $dateTime date in UTC
	 * @return  string
	 */
	public static function DateTimeToJsString($dateTime)
	{
		return $dateTime->toString(self::DATETIME_JS_PART, null, 'en');
	}

	/**
	 * Returns a string representation of a date converted to store timezone and store Locale
	 *
	 * When sending strings to this method they should always be formatted according to self::DATETIME_PART !
	 *
	 * @param   string|integer|Zend_Date|array|null $dateTime date in UTC
	 * @return  string
	 */
	public static function DateTimeToStoreTZ($dateTime = null)
	{
		return Mage::app()->getLocale()->date($dateTime, self::DATETIME_PART, null, true);
		// This method (storeDate())does not accept datetime part and assumes something like YYYY-DD-MM hh:mm:ss which
		// is bad for us. date() is better because does the same work and allows us to supply our self::DATETIME_PART.
		//return Mage::app()->getLocale()->storeDate(null, $dateTime, true);
	}

	/**
	 * Returns a string representation of a date converted to UTC and store Locale
	 *
	 * When sending strings to this method they should always be formatted according to self::DATETIME_PART !
	 *
	 * @param   string|integer|Zend_Date|array|null $dateTime date in store's timezone
	 * 			if null, the the current time now in UTC is returned.
	 * @return  string
	 */
	public static function DateTimeToUTC($dateTime = null)
	{
		$locale = Mage::app()->getLocale();
        $dateObj = $locale->storeDate(null, $dateTime, true);
        if ($dateTime != null)
        	$dateObj->set($dateTime, self::DATETIME_PART);
        $dateObj->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);
        return $dateObj;
	}

	/*
	 * Currency related
	 */

	public static function GetCurrencyCodeHtml()
	{
		return '&nbsp;&nbsp;<strong>[' .  Mage::app()->getStore()->getBaseCurrency()->getCode() . ']</strong>';
	}

	public static function GetBaseCurrencySymbol()
	{
		try
		{
			$base_currency_code = self::GetBaseCurrencyCode();
			$base_currency = Mage::app()->getLocale()->currency($base_currency_code);
			return $base_currency->getSymbol(
				$base_currency_code,
				self::GetLocaleCode());
		}
		catch (Exception $ex)
		{
			self::LogError($ex);
			return Mage::app()->getStore()->getBaseCurrencyCode();
		}
	}

	public static function GetBaseCurrencyCode()
	{
		return Mage::app()->getStore()->getBaseCurrencyCode();
	}

	public static function GetSupportLinkHtml()
	{
		return '<a href="mailto:support@zizio.com" style="color:red;"><strong>Need Help? Contact Zizio!</strong></a>';
	}

	public static function GetWebsitesBaseUrls ()
	{
		$websties = array();
		foreach (Mage::app()->getWebsites(true) as $website)
		{
			foreach ($website->getStoreCollection() as $store)
			{
				$id = $website->getId();
				$websties[$id] = $store->getBaseUrl();
				break;
			}
		}
		return $websties;
	}

	/*
	 * Get the baseUrl for a given Website. Note that baseUrl is actually a property
	 * of Store so we need to find a Store in the Website to get to it.
	 */
	public static function GetBaseUrlForWebsite($websiteId)
	{
		$app = Mage::app();
		if ($app != null)
		{
			$website = $app->getWebsite($websiteId);
			if ($website != null)
			{
				// Try to get the default store for this website:
				$store =  $website->getDefaultStore();
				// If there's not default store, get the first store:
				if ($store == null)
				{
					foreach ($website->getStoreCollection() as $store)
					{
						$store = $store->getBaseUrl();
						break;
					}
				}
				if ($store != null)
					return $store->getBaseUrl();
			}
		}
		// If we're unable to determine the store, retun null:
		return null;
	}

	/*
	 * Magento wrappers
	 */

	public static function GetProductImage ($product, $small)
	{
		if ($small)
			return Mage::getBaseUrl('media') . 'catalog/product' . $product->getSmallImage();
		else
			return Mage::getBaseUrl('media') . 'catalog/product' . $product->getImage();

		// TODO: this code is probably better though it produces temporary files:
		$product = Mage::getModel('catalog/product')->load($item->getProductId());
		return (string) Mage::helper('catalog/image')->init($product, 'thumbnail', $product->getSmallImage())->resize(50);
	}

	/**
	 * Use this when passing locale to zizio server.
	 */
	public static function GetLocaleCode()
	{
		try
		{
			return Mage::app()->getLocale()->getLocaleCode();
		}
		catch (Exception $ex)
		{
			self::LogError($ex);
		}
		return "";
	}

	/**
	 * Leaving this here for reference only.
	 */
	public static function GetLanguageCode()
	{
		try
		{
			return Mage::app()->getLocale()->getLocale()->getLanguage();
		}
		catch (Exception $ex)
		{
			self::LogError($ex);
		}
		return "";
	}

	public static function GetDefaultWebsiteName()
	{
		try
		{
			$def_view = Mage::app()->getDefaultStoreView();
			if ($def_view)
				return $def_view->getWebsite()->getName();
		}
		catch (Exception $ex)
		{
			self::LogError($ex);
		}
		return "";
	}

	public static function WriteWelcomeNotification()
	{
		$notification = array();
		$notification['url'] 			= "http://www.zizio.com/support/powershare#welcome";
		$notification['title'] 			= "Thank you for having installed Zizio Power Share.";
		$notification['description'] 	=
			"<p>Thank you for having installed the Zizio Power Share extension for Magento.</p>" .
			"<p>-- The Zizio.com Team.</p>";
		$notification['severity'] 		= 4;
		$notification['is_read'] 		= 0;
		$notification['date_added'] 	= date('Y-m-d H:i:s');
		$notifications[] = $notification;
		// Save new notifications to DB:
		$inbox = Mage::getModel('adminNotification/inbox');
		$inbox->parse($notifications);
	}

	public static function GetAllCustomerGroupIds()
	{
		$customerGroups = Mage::getResourceModel('customer/group_collection')->load();
		$customerGroupIds = array();
		$found = false;
		foreach ($customerGroups as $customerGroup)
		{
			$customerGroupIds[] = $customerGroup->getId();
			if ($customerGroup->getId()==0)
				$found = true;
		}
		if (!$found)
			array_unshift($customerGroups, 0);
		return $customerGroupIds;
	}

	/*
	 * Price calculation
	 */

	private static function _CalcProductPrice($price, $percent, $type)
	{
		if ($type)
			return $price * (1+($percent/100));
		else
			return $price - ($price/(100+$percent)*$percent);
	}


	/**
	 * Gets original product price including/excluding tax
	 *
	 * @param	null|bool $tax - null = default, true = including, false = excluding
	 * @param	mixed $store
	 * @return	float $price
	 */
	public static function GetProductPrice($product, $tax = null, $website = null, $round = false)
	{
		$tax_helper = Mage::helper('tax');

		// get store
		if ($website === null)
			$website = Mage::app()->getWebsite(null);
		if ($website === null)
			$store = Mage::app()->getDefaultStoreView();
		else
			$store = $website->getDefaultStore();

		// calculate price
		$price = $product->getFinalPrice();
		$priceIncludesTax = $tax_helper->priceIncludesTax($store);
		$percent = $product->getTaxPercent();
		$includingPercent = null;
		$taxClassId = $product->getTaxClassId();

		if ($percent === null && $taxClassId)
		{
			$request = Mage::getSingleton('tax/calculation')->getRateRequest(null, null, null, $store);
			$percent = Mage::getSingleton('tax/calculation')->getRate($request->setProductClassId($taxClassId));
		}
		if ($priceIncludesTax && $taxClassId)
		{
			$request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false, $store);
			$includingPercent = Mage::getSingleton('tax/calculation')->getRate($request->setProductClassId($taxClassId));
		}
		if (!(($percent === false || $percent === null) && $priceIncludesTax && !$includingPercent))
		{
			if ($priceIncludesTax)
				$price = self::_CalcProductPrice($price, $includingPercent, false);
			if ($tax || (($tax === null) && ($tax_helper->getPriceDisplayType($store) != Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX)))
				$price = self::_CalcProductPrice($price, $percent, true);
		}

		if ($round)
			return $store->roundPrice($price);
		else
			return $price;
	}

	/*
	 * Resources url builders
	 */

	private static function BuildScriptUrl($relative_url, $args=array(), $html_encoded=true)
	{
		if ($html_encoded)
			list($eq, $sep) = array ("=", "&amp;");
		else
			list($eq, $sep) = array ("=", "&");

		$args = $args + self::GetScriptUrlArgs();
		$url = implode(array(self::$PROTOCOL, "://", self::$BASE_URL, self::$PORT, $relative_url, "?", self::BuildQS($args, $eq, $sep)));
		return $url;
	}

	public static function GetAdminRegisterTermsUrl($args=array())
	{
		return self::BuildScriptUrl("/powers/mage/admin/terms", $args);
	}

	public static function GetAdminRegisterScriptUrl($args=array())
	{
		return self::BuildScriptUrl("/js/mage/admin_register.js", $args);
	}

	public static function GetZUtilsScriptUrl($args=array())
	{
		return self::BuildScriptUrl("/js/zutils.js", $args);
	}

	public static function GetProductPageScriptUrl($args=array())
	{
		return self::BuildScriptUrl("/res/powers/mage/product", $args);
	}

	public static function GetSuccessPageScriptUrl($args=array())
	{
		return self::BuildScriptUrl("/res/powers/mage/success", $args);
	}

	public static function GetRegisterUrl($args=array())
	{
		return self::BuildScriptUrl("/powers/mage/admin/register", $args, false);
	}

	public static function GetSystemConfigScriptUrl($args=array())
	{
		$args['v'] = "1.4";
		return self::BuildScriptUrl("/js/powers/mage/admin/config.js", $args, false);
	}

	public static function GetAdminIncentiveGridScriptUrl($args=array())
	{
		$args['v'] = "1.1";
		return self::BuildScriptUrl("/js/powers/mage/admin/incentive.js", $args);
	}

	public static function GetAdminIncentiveEditScriptUrl($args=array())
	{
		$args['v'] = "1.2";
		return self::BuildScriptUrl("/js/powers/mage/admin/incentive.js", $args);
	}

	public static function GetBaseWidgetsUrl()
	{
		return implode(array(self::$PROTOCOL, "://", self::$BASE_URL, self::$PORT));
	}

	public static function GetZizioLoginUrl()
	{
		return "https://" . self::$ZIZIO_ACCOUNT_URL;
	}

	public static function GetZizioAttachUrl()
	{
		$pub_id = self::GetPublisherId();
		$data = self::ToBase64(self::json_encode(array('attach' => true)));

		$args = array(
			'd' 	 => $data,
			'pub_id' => $pub_id
		);
		$args['_token'] = self::ToBase64(self::GetToken($args, true));

		$url = implode(array("https://", self::$ZIZIO_ACCOUNT_URL, "/attach?", self::BuildQS($args, "=", "&amp;")));
		return $url;
	}

	public static function _FetchWatermarksData($args=array(), $use_cache=true)
	{
		$watermarks = array(array('value' => "", 'label' => "None"));
		try {
			// check cache
    		if ($use_cache && (self::$_watermarks !== null) && (self::$_default_watermark !== null))
				return;

			// get from zizio and parse
    		$data = self::CallUrl('/powers/mage/admin/get_watermarks', $args, null, true);
	    	foreach ($data['watermarks'] as $key => $name)
	    		$watermarks[] = array('value' => $key, 'label' => Mage::helper('powershare')->__($name));

	    	// store cache
	    	self::$_default_watermark = $data['default'];
	    	self::$_watermarks = $watermarks;
    	}
    	catch (Exception $ex)
		{
			self::LogError($ex);
			self::$_watermarks = $watermarks;
		}
	}

	public static function GetDefaultWatermark($args=array(), $use_cache=true)
	{
		self::_FetchWatermarksData($args, $use_cache);
		return self::$_default_watermark;
	}

	public static function GetWatermarksOptionsArray($args=array(), $use_cache=true)
	{
		self::_FetchWatermarksData($args, $use_cache);
		return self::$_watermarks;
	}

	public static function SendSafeUrl($url, $post_data)
	{
		if ($post_data)
			$data = self::json_encode($post_data);
		else
			$data = '{}';

		$token_args = array(
			'pl' 		=> $data,
			'pub_id' 	=> self::GetPublisherId(),
			'trans_id' 	=> uniqid()
		);
		$token_args['_token'] = self::ToBase64(self::GetToken($token_args, true));
		return self::SendUrl($url, $token_args);
	}
}

Zizio_Powershare_Helper_Data::StaticConstructor();
