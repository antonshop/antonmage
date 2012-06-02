<?php
/**
 * Appmagento
 *
 * @category    Appmagento
 * @package     Appmagento_Sharesuit
 * @copyright   Copyright (c) 2011 http://www.appmagento.com
 */

class Appmagento_Sharesuit_CustomerController extends Mage_Core_Controller_Front_Action
{
	
	public function FbloginAction()
    {
        $me = null;
        $cookie = $this->get_facebook_cookie(Mage::getModel('sharesuit/sharesuit')->getFbAppid(), Mage::getModel('sharesuit/sharesuit')->getFbAppsecret());
        $me = json_decode($this->getFbData('https://graph.facebook.com/me?access_token=' . $cookie['access_token']));
        $this->checklogin($me, 1);
    }

    public function checklogin($me, $type=1){
    	if ($me) {
			$me = (array)$me;
            $session = Mage::getSingleton('customer/session');

            $db_read = Mage::getSingleton('core/resource')->getConnection('read');
            $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
            $sql = 'SELECT `customer_id`
					FROM `' . $tablePrefix . 'sharesuit_customer`
					WHERE `user` = ' . $me['id'] . '
					LIMIT 1';
            $data = $db_read->fetchRow($sql);

            if ($data) {
                $session->loginById($data['customer_id']);
            } else {
            	$sql = 'SELECT `customer_id`, `type`
						FROM `' . $tablePrefix . 'sharesuit_customer`
						WHERE `email` = \'' . $me['email'] . '\'
						LIMIT 1';
            	
            	$cus = $db_read->fetchRow($sql);
            	if($cus){
            		$db_write = Mage::getSingleton('core/resource')->getConnection('write');
            		$sql = 'INSERT INTO `' . $tablePrefix . 'sharesuit_customer` 
            		(`customer_id`, `user`, `email`, `status`, `type`) 
            		VALUES (' . $cus['customer_id'] . ', \'' . $me['id'] . '\', \''. $me['email'] .'\', 1, '. $type .')';
            		
            		$db_write->query($sql); 
            		$session->loginById($cus['customer_id']);
            	} else {
            		$this->_registerCustomer($me, $session, $type);
            	}
            }
            $this->_loginPostRedirect($session);
        }
    }
    
    public function FblogoutAction()
    {
        $session = Mage::getSingleton('customer/session');
        $session->logout()
                ->setBeforeAuthUrl(Mage::getUrl());

        $this->_redirect('customer/account/logoutSuccess');
    }
    
    public function gploginAction(){
    	$client = Mage::getModel('sharesuit/google_plus')->getApiClient();
    	$plus = Mage::getModel('sharesuit/google_plus')->getApiPlus($client);
		$oauth2 = Mage::getModel('sharesuit/google_plus')->getApiOauth2($client);
		
		$logout = Mage::app()->getRequest()->getParam('logout');
    	if (isset($logout)) {
			unset($_SESSION['gp_access_token']);
		}
		
		$code = Mage::app()->getRequest()->getParam('code');
		if (isset($code)) {
		  $client->authenticate();
		  $_SESSION['gp_token'] = $client->getAccessToken();
		  $redirect = Mage::getModel('sharesuit/google_plus')->getRedirectUri();
		  $this->_redirect($redirect);
		}
		
		if (isset($_SESSION['gp_access_token'])) {
		  $client->setAccessToken($_SESSION['gp_access_token']);
		}
		
		if ($client->getAccessToken()) {
		  	$me = $plus->people->get('me');
		  	$user = $oauth2->userinfo->get();
		  	$data = array(
		  			'id' => $user['id'],
		  			'first_name' => $user['given_name'],
		  			'last_name' => $user['family_name'],
		  			'email' => $user['email'],
		  			'is_active' => 1,
		  	);
		  	$this->checklogin($data, 2);
	    }
    }
    
    public function twloginAction(){
    	print_r($_SESSION);
    	if(isset($_SESSION['oauth_twitter_httpcode']) && $_SESSION['oauth_twitter_httpcode']==200 && $_SESSION['oauth_twitter_content']){
    		$data = array(
    			'id' => '',
    			'first_name' => '',
    			'last_name' => '',
    			'email' => '',
    			'is_active' => '',
    		);
    		$this->checklogin($me);
    	}
    }
    
	public function twloginredirectAction(){
    	
    	$twitter = Mage::getModel('sharesuit/sharesuit')->getTwitterOauth();
		$request_token = $twitter->getRequestToken(Mage::getBlockSingleton('sharesuit/sharesuit')->getResponseUrl('tw'));
		
		/* Save temporary credentials to session. */
		$_SESSION['oauth_token'] = $request_token['oauth_token'];
		$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
		$_SESSION['oauth_twitter_login'] = 1;

    	if($twitter->http_code == 200){
    		$url = Mage::getModel('sharesuit/sharesuit')->getTwUrl();
		    return $this->_redirectUrl($url);
    	} else {
    		Mage::getSingleton('core/session')->addError($this->__('Could not connect to Twitter. Refresh the page or try again later.'));
			return $this->_redirectUrl($this->getrUrl());
    	}
    }

    private function _registerCustomer($data, &$session, $type=1)
    {
        $customer = Mage::getModel('customer/customer')->setId(null);
        $customer->setData('firstname', $data['first_name']);
        $customer->setData('lastname', $data['last_name']);
        $customer->setData('email', $data['email']);
        $customer->setData('password', md5(time() . $data['id'] . $data['locale']));
        $customer->setData('is_active', 1);
        $customer->setData('confirmation', null);
        $customer->setConfirmation(null);
        $customer->getGroupId();
        $customer->save();

        Mage::getModel('customer/customer')->load($customer->getId())->setConfirmation(null)->save();
        $customer->setConfirmation(null);
        $session->setCustomerAsLoggedIn($customer);
        $customer_id = $session->getCustomerId();
        $db_write = Mage::getSingleton('core/resource')->getConnection('write');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
        $sql = 'INSERT INTO `' . $tablePrefix . 'sharesuit_customer`
        		(`customer_id`, `user`, `email`, `status`, `type`) 
				VALUES (' . $customer_id . ', \'' . $data['id'] . '\', \''. $data['email'] .'\' ,1, '. $type .')';
        $db_write->query($sql);
    }

    private function _loginPostRedirect(&$session)
    {

        if ($referer = $this->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME)) {
            $referer = Mage::helper('core')->urlDecode($referer);
            if ((strpos($referer, Mage::app()->getStore()->getBaseUrl()) === 0)
                    || (strpos($referer, Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, true)) === 0)) {
                $session->setBeforeAuthUrl($referer);
            } else {
                $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
            }
        } else {
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
        }
        $this->_redirectUrl($session->getBeforeAuthUrl(true));
        
    }

    private function get_facebook_cookie($app_id, $app_secret)
    {
        if ($_COOKIE['fbsr_' . $app_id] != '') {
            return $this->get_new_facebook_cookie($app_id, $app_secret);
        } else {
            return $this->get_old_facebook_cookie($app_id, $app_secret);
        }
    }

    private function get_old_facebook_cookie($app_id, $app_secret)
    {
        $args = array();
        parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
        ksort($args);
        $payload = '';
        foreach ($args as $key => $value) {
            if ($key != 'sig') {
                $payload .= $key . '=' . $value;
            }
        }
        if (md5($payload . $app_secret) != $args['sig']) {
            return array();
        }
        return $args;
    }

    private function get_new_facebook_cookie($app_id, $app_secret)
    {
        $signed_request = $this->parse_signed_request($_COOKIE['fbsr_' . $app_id], $app_secret);
        // $signed_request should now have most of the old elements
        $signed_request['uid'] = $signed_request['user_id']; // for compatibility 
        if (!is_null($signed_request)) {
            // the cookie is valid/signed correctly
            // lets change "code" into an "access_token"
			$access_token_response = $this->getFbData("https://graph.facebook.com/oauth/access_token?client_id=$app_id&redirect_uri=&client_secret=$app_secret&code=$signed_request[code]");
			parse_str($access_token_response);
			$signed_request['access_token'] = $access_token;
			$signed_request['expires'] = time() + $expires;
        }

        return $signed_request;
    }

    private function parse_signed_request($signed_request, $secret)
    {
        list($encoded_sig, $payload) = explode('.', $signed_request, 2);

        // decode the data
        $sig = $this->base64_url_decode($encoded_sig);
        $data = json_decode($this->base64_url_decode($payload), true);

        if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
            error_log('Unknown algorithm. Expected HMAC-SHA256');
            return null;
        }

        // check sig
        $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
        if ($sig !== $expected_sig) {
            error_log('Bad Signed JSON signature!');
            return null;
        }

        return $data;
    }

    private function base64_url_decode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }
	
	private function getFbData($url)
	{
		$data = null;

		if (ini_get('allow_url_fopen') && function_exists('file_get_contents')) {
			$data = file_get_contents($url);
		} else {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$data = curl_exec($ch);
		}
		return $data;
	}
	
}