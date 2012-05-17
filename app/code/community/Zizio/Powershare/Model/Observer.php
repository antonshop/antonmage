<?php

class Zizio_Powershare_Model_Observer
{

	private $_helper = null;
	
	static $processors = array(
		// product view hook
		array(
			'action'		  => '/^catalog_product_view$/',
			'block'			  => '/^content$/',
			'exclusive'		  => true,
			'registered_only' => true,
			'do'			  => 'ProductViewProcessor'
		),
		// success page hook
		array(
			'action'		  => '/^(checkout_onepage_success|checkout_multishipping_success|firecheckout_index_success)$/',
			'block'			  => '/^content$/',
			'exclusive'		  => true,
			'registered_only' => true,
			'do'			  => 'SuccessPageProcessor'
		),
		// admin notification hook
		array(
			'action'		  => '/(_adminhtml|adminhtml_)/',
			'block'			  => '/^footer$/',
			'exclusive'		  => false,
			'registered_only' => false,
			'do'			  => 'AdminNotificationProcessor'
		)
	);

	/*
	 * "Static constructor". Should be invoked only once - at the bottom of this file.
	 */
	public static function StaticConstructor()
	{
		// nothing
	}

	public function __construct()
	{
		// get helper class
		$this->_helper = new Zizio_Powershare_Helper_Data();
	}

	/************************************************************************
	 * Cron Jobs
	 ************************************************************************/

	public function GetCronHelper()
	{
		return Mage::helper('powershare/cron');
	}

	public function HourlyCronJobs($schedule)
	{
		$this->GetCronHelper()->HourlyCronJobs($schedule);
		return $this;
	}

	public function DailyCronJobs($schedule)
	{
		$this->GetCronHelper()->DailyCronJobs($schedule);
		return $this;
	}

	public function SetBlockHtml(&$block, $html)
	{
		// create a flat block to contain html
		$zizio_block = $block->getLayout()->createBlock('core/text', '');
		$zizio_block->setIsAnonymous(false); // without this line the block will inherit its parent name and the hook will be triggered twice because we use block_name sniffing
		$zizio_block->setText($html);

		// replace block content with the flat block
		$block->unsetChildren();
		$block->insert($zizio_block);
	}

	public function GetBlockHtml($block)
	{
		try
		{
			/*
			 * challenge: get block rendered html while
			 * 	a. unable to use private methods of the block class and
			 *  b. unwilling to override the block class
			 * method: call to each child toHtml() (a copy of $block->_toHtml())
			 * pro: efficient and simple
			 * cons:
			 *  1. if someone overrode the block class and the output might not be accurate
			 *	2. cannot call parent::_toHtml() which validates output using return value from block's _beforeHtml()
			 */
			$html = '';
			foreach ($block->getSortedChildren() as $child_name)
			{
				$child = $block->getLayout()->getBlock($child_name);
				if (!$child)
				{
					Mage::throwException(Mage::helper('core')->__('Invalid block: %s', $child_name));
				}
				$html .= $child->toHtml();
			}

			return $html;
		}
		catch (Exception $ex)
		{
			// supress errors in external methods
			return null;
		}
	}

	public function BeforeRenderHtml($observer = null)
	{
		try
		{
			$GLOBALS["zizio_test_entered"] = true;

			// for versions after 1.4.1 we use the after html hook
			if ($this->_helper->CompareVersions(Mage::getVersion(), '1.4.1') >= 0)
			{
				$GLOBALS["zizio_test_exited"] = true;
				$GLOBALS["zizio_test_exit_point"] = 'Line ' . __line__ . ': Mage::getVersion() >= \'1.4.1\'';
				return $this;
			}

			// check if extension is enabled
			if (!$observer || !$this->_helper->IsZizioPowershareEnabled())
			{
				$GLOBALS["zizio_test_exited"] = true;
				$GLOBALS["zizio_test_exit_point"] = 'Line ' . __line__ . ': (!$observer || !$this->_helper->IsZizioPowershareEnabled() || !$this->_helper->IsZizioPowershareRegistered())';
				return $this;
			}

			$event = $observer->getEvent();
			$block = $event->getBlock();
			$block_name = $block->getNameInLayout();
			$action = $block->getAction();
			$action_name = (gettype($action) == 'object') ? $action->getFullActionName() : '';
			
			// check hooks
			foreach (self::$processors as $processor)
			{
				if (($processor['action'] ? preg_match($processor['action'], $action_name) : true) && preg_match($processor['block'], $block_name))
				{
					if ($processor['registered_only'] && !$this->_helper->IsZizioPowershareRegistered())
					{
						continue;
					}
					if (!$processor['exclusive'] || ($this->_helper->IsZizioPowershareTreated() == false))
					{
						// trigger our hook to parse html
						$html = $this->{$processor['do']}($observer, null, $block);

						if ($html != null)
						{
							// set parsed html back to block
							$this->SetBlockHtml($block, $html);
						}

						// flag the powershare as treated
						if ($processor['exclusive'])
							$this->_helper->SetZizioPowershareTreated();
					}
				}
			}

			$GLOBALS["zizio_test_exited"] = true;
			$GLOBALS["zizio_test_exit_point"] = 'Line ' . __line__ . ': end of method logic';
		}
		catch (Exception $ex)
		{
			$this->_helper->LogError($ex);
		}
		return $this;
	}

	public function AfterRenderHtml($observer = null)
	{
		try
		{
			$GLOBALS["zizio_test_entered"] = true;

			// for versions before 1.4.1 we use the before html hook
			if ($this->_helper->CompareVersions(Mage::getVersion(), '1.4.1') < 0)
			{
				$GLOBALS["zizio_test_exited"] = true;
				$GLOBALS["zizio_test_exit_point"] = 'Line ' . __line__ . ': Mage::getVersion() < \'1.4.1\'';
				return $this;
			}

			// check if extension is enabled
			if (!$observer || !$this->_helper->IsZizioPowershareEnabled())
			{
				$GLOBALS["zizio_test_exited"] = true;
				$GLOBALS["zizio_test_exit_point"] = 'Line ' . __line__ . ': (!$observer || !$this->_helper->IsZizioPowershareEnabled() || !$this->_helper->IsZizioPowershareRegistered())';
				return $this;
			}

			$event = $observer->getEvent();
			$block = $event->getBlock();
			$block_name = $block->getNameInLayout();
			$action = $block->getAction();
			$action_name = (gettype($action) == 'object') ? $action->getFullActionName() : '';
			$transport = $event->getTransport();

			// check hooks
			foreach (self::$processors as $processor)
			{
				if (($processor['action'] ? preg_match($processor['action'], $action_name) : true) && preg_match($processor['block'], $block_name))
				{
					if ($processor['registered_only'] && !$this->_helper->IsZizioPowershareRegistered())
					{
						continue;
					}
					if (!$processor['exclusive'] || ($this->_helper->IsZizioPowershareTreated() == false))
					{
						// process hook
						$html = $this->{$processor['do']}($observer, $transport['html'], $block);

						if ($html != null)
						{
							// set parsed html back to block
							$transport['html'] = $html;
						}

						// flag the powershare as treated
						if ($processor['exclusive'])
							$this->_helper->SetZizioPowershareTreated();
					}
				}
			}

			$GLOBALS["zizio_test_exited"] = true;
			$GLOBALS["zizio_test_exit_point"] = "Line " . __line__ . ": end of method logic";
		}
		catch (Exception $ex)
		{
			$this->_helper->LogError($ex);
		}
		return $this;
	}

	public function ProductViewProcessor ($observer, $html, $block)
	{
		if ($this->_helper->GetSetting(Zizio_Powershare_Helper_Data::PRODUCT_ENABLED_PATH) != "1")
			return null;
		
		Mage::register("zizio/in_powershare", true, true);
		
		$product = Mage::registry('current_product');
		
		try {
			$powershare_bars = $this->_helper->json_decode($this->_helper->GetSetting(Zizio_Powershare_Helper_Data::PRODUCT_POWERSHARE_BARS_PATH));
		} catch(Exception $ex) {
			$powershare_bars = null;
		}
		
		$js_data = array(
			'bars'			   => $powershare_bars,	
			'store_id'		   => Mage::app()->getStore()->getId(),
			'cg_id'			   => Mage::getSingleton('customer/session')->getCustomerGroupId(),
			'cat_id'		   => $product->getCategoryId(),
			'cat_ids'		   => $product->getCategoryIds(),
			'curr_code'		   => $this->_helper->GetBaseCurrencyCode(),
			'curr_sym'		   => $this->_helper->GetBaseCurrencySymbol(),
			'ext_ver'		   => $this->_helper->GetExtVer(),
			'locale'		   => $this->_helper->GetLocaleCode(),
			'product_id'	   => $product->getId(),
			'product_desc'	   => $product->hasShortDescription() ? $product->getShortDescription() : ( $product->hasDescription() ? $product->getDescription() : "" ),
			'product_name'	   => $product->getName(),
			'product_price'	   => $this->_helper->GetProductPrice($product, null, null, true),
			'product_sml_img'  => $this->_helper->GetProductImage($product, true),
			'product_url'	   => $product->getProductUrl(),
			'pub_id'		   => $this->_helper->GetPublisherId(),
			'req_id'		   => isset($_GET['zizio_req']) ? $_GET['zizio_req'] : null,
			'zizio_ref'		   => isset($_GET['zizio']) ? "1" : "0", // currently, don't care if the referral was for other sale
			'apply_coupon_url' => $block->getUrl("powershare/index/applyCoupon", array('_query' => array('ajax' => "1", 'zizio_coupon' => "__ZIZIO_COUPON__")))
		);

		$zizio_widget_format =
"<script type='text/javascript'>
	var Zizio = Zizio || {};
	Zizio.PowerS = Zizio.PowerS || {};
	Zizio.PowerS.data = Zizio.PowerS.data || {};
	Zizio.PowerS.data.orig = %s;
</script>";

		$zizio_js = sprintf($zizio_widget_format,
			$this->_helper->json_encode($js_data)
		);

		if (Mage::registry("zizio/utils_js_added") == null)
		{
			$zizio_js .= $this->_helper->GetScriptBlock($this->_helper->GetZUtilsScriptUrl());
			Mage::register("zizio/utils_js_added", true);
		}
		$zizio_js .= $this->_helper->GetScriptBlock($this->_helper->GetProductPageScriptUrl());

		// add script block to html and return it
		if ($html == null)
			$html = $this->GetBlockHtml($block);
		return $html . $zizio_js;

	}

	public function SuccessPageProcessor ($observer, $html, $block)
	{
		if ($this->_helper->GetSetting(Zizio_Powershare_Helper_Data::SUCCESS_ENABLED_PATH) != "1")
			return null;
		
		$order_id = Mage::getSingleton('checkout/session')->getLastOrderId();
		if (!$order_id)
			return null;
		$store_id = Mage::app()->getStore()->getId();
			
		$order = Mage::getModel('sales/order')->load($order_id);
		$items = $order->getItemsCollection();

		// data to pass to js
		$products = array();

		foreach ($items as $item)
		{
			// check if this item is currently in powershare
			// only for top-level items
			if ($item->getParentItemId() == null)
			{
				// add sales info to data
				$product = Mage::getModel('catalog/product')->load($item->getProductId());
				$product->setStoreId($store_id);
				
				$products[] = array(
					'id'	  => $product->getId(),
					'cat_ids' => $product->getCategoryIds(),
					'desc'	  => $product->hasShortDescription() ? $product->getShortDescription() : ( $product->hasDescription() ? $product->getDescription() : "" ),
					'name'	  => $product->getName(),
					'price'	  => $this->_helper->GetProductPrice($product, null, null, true),
					'qty'	  => $item->getQtyOrdered(),
					'sml_img' => $this->_helper->GetProductImage($product, true),
					'url'	  => $product->getProductUrl(),
				);
			}
		}
		
		try {
			$powershare_buttons = explode(",", $this->_helper->GetSetting(Zizio_Powershare_Helper_Data::SUCCESS_BUTTONS_PATH));
		} catch(Exception $ex) {
			$powershare_buttons = array();
		}
		
		$js_data = array(
			'store_id' 			=> $store_id,
			'buttons'	        => $powershare_buttons,
			'cg_id'				=> Mage::getSingleton('customer/session')->getCustomerGroupId(),
			'css_selector'      => $this->_helper->GetSetting(Zizio_Powershare_Helper_Data::SUCCESS_CSS_SELECTOR_PATH),
			'curr_code'	        => $this->_helper->GetBaseCurrencyCode(),
			'curr_sym' 	        => $this->_helper->GetBaseCurrencySymbol(),
			'ext_ver'	        => $this->_helper->GetExtVer(),
			'locale' 	        => $this->_helper->GetLocaleCode(),
			'position'	        => $this->_helper->GetSetting(Zizio_Powershare_Helper_Data::SUCCESS_POSITION_PATH),
			'pub_id' 	        => $this->_helper->GetPublisherId(),
			'products' 	        => $products,
			'cont_caption'      => $this->_helper->GetSetting(Zizio_Powershare_Helper_Data::SUCCESS_CONT_CAPTION_PATH),
			'share_caption'     => $this->_helper->GetSetting(Zizio_Powershare_Helper_Data::SUCCESS_SHARE_CAPTION_PATH),
			'apply_coupon_url'  => $block->getUrl("powershare/index/applyCoupon", array('_query' => array('ajax' => "1", 'zizio_coupon' => "__ZIZIO_COUPON__")))
		);
		
		$zizio_widget_format =
"<script type='text/javascript'>
	var Zizio = Zizio || {};
	Zizio.PowerS = Zizio.PowerS || {};
	Zizio.PowerS.data = Zizio.PowerS.data || {};
	Zizio.PowerS.data.orig = %s;
</script>";

		$zizio_js = sprintf($zizio_widget_format,
			$this->_helper->json_encode($js_data)
		);

		if (Mage::registry("zizio/utils_js_added") == null)
		{
			$zizio_js .= $this->_helper->GetScriptBlock($this->_helper->GetZUtilsScriptUrl());
			Mage::register("zizio/utils_js_added", true);
		}
		$zizio_js .= $this->_helper->GetScriptBlock($this->_helper->GetSuccessPageScriptUrl());

		// add script block to html and return it
		if ($html == null)
			$html = $this->GetBlockHtml($block);
		return $html . $zizio_js;
	}
	
	public function AdminNotificationProcessor ($observer, $html, $block)
	{
		$event = $observer->getEvent();
		$action = $block->getAction();
		$session = Mage::getSingleton('admin/session');

		$notify = null; 
		
		// installation alert: 
		//  user logged-in
		//  extension not registered
		//  didn't prompt this session yet
		//  not on registration screen itself
		if ($session->isLoggedIn() &&
			!$this->_helper->IsZizioPowershareRegistered() &&
			!$session->getDataZizioPowershareRegisterPrompt() &&
			($action->getFullActionName() != "powershare_adminhtml_zizioRegister_index"))
		{
			// mark prompt
			$session->setDataZizioPowershareRegisterPrompt(true);
		
			// prepare notification content
			$register_url = $action->getUrl("powershare/adminhtml_powershare/index");
			$notify = array(
				array('type' => "text",
					  'text' => "Thank you for installing <b>Zizio Power Share</b> extension.<br/>"
	            			  . "In order to activate it, please complete our simple registration.<br/>"),
				array('type' => "link",
					  'link' => $register_url,
					  'caption' => "Go to registration"),
				array('type' => "text",
					  'text' => "If you prefer to register later, you can always find the form under "
					  		  . "<b>Promotions</b> -> <b>Zizio Power Share</b> menu.")
			);
		}
		
		// new features alerts:
		//  user logged-in
		//  extension registered
		//  didn't alert this user yet
		if ($session->isLoggedIn() &&
			$this->_helper->IsZizioPowershareRegistered())
		{
			// zizio account attachment alert
			if (!$this->_helper->IsNotifiedAboutZizioAccount())
			{
				// mark prompt
				$this->_helper->SaveConfigValue(Zizio_Powershare_Helper_Data::ACCOUNT_NOTIFIED_PATH, "1");
				
				// prepare notification content
				$notify = array(
					array('type' => "text",
						  'text' => "Thank you for upgrading your Zizio Power Share extension.<br/>"
					              . "You now have a private store owner zone at Zizio.com.<br/>"
					              . "You can always login at <a href=\"http://www.zizio.com/account\" target=\"_blank\">http://www.zizio.com/account</a><br/>"
					              . "An account has already been set-up for you, to claim it, use the "
					              . "<a href=\"http://www.zizio.com/account/password_reset\" target=\"_blank\">Forgot your password?</a> link"),
				    array('type' => "link",
						  'link' => "http://www.zizio.com/account",
						  'caption' => "Login to your Zizio Account",
						  'target' => "_blank")
				);
			}
			// new pinit share button alert
			elseif (!$this->_helper->IsNotifiedAboutPinIt())
			{
				// mark prompt
				$this->_helper->SaveConfigValue(Zizio_Powershare_Helper_Data::NOTIFY_PINIT_PATH, "1");
	
				// prepare notification content
				$conf_url = Mage::getModel('adminhtml/url')	
					->setRouteName("adminhtml")
					->setControllerName("system_config")
					->setActionName("edit")
					->getUrl(null, array(
						'section' => "powershare"
					));
				
				$notify = array(
					array('type' => "text",
						  'text' => "Success! you have just upgraded to the latest Zizio Power Share.<br/>"
						  		  . "<br/>"
					              . "<b>Whats new?</b></br>"
					              . "&bull; <a href=\"http://pinterest.com\" target=\"_blank\">Pinterest</a> Pin It button<br/>"
					              . "&bull; Select / Hide Share buttons<br/>"
					              . "&bull; Multiple sets of buttons per page<br/>"
					              . "<br/>"
					              . "All of these can be controlled from <b>Power Share Configuration Area</b> under <b>System</b> &gt;&gt; "
					              . "<b>Configuration</b> in your <b>Admin Panel</b>"),
				    array('type' => "link",
						  'link' => $conf_url,
						  'caption' => "Go to Power Share configuration page")
				);
			}
			// db changes were made, check settings alert
			elseif (!$this->_helper->IsNotifiedAboutDbChanges())
			{
				// mark prompt
				$this->_helper->SaveConfigValue(Zizio_Powershare_Helper_Data::DBCHANGES_NOTIFIED_PATH, "1");
			
				// prepare notification content
				$notify = array(
					array('type' => "text",
						  'text' => "Thank you for upgrading your Zizio Power Share extension.<br/><br/>"
					              . "In the latest update we have made some changes to the structure of the data stored by Zizio locally in your database. "
					              . "This shouldn't affect you, but still we advise you to check your Zizio Power Share settings and ensure "
					              . "nothing was changed unintentionally.<br/><br/>"
					              . "In order to access your settings page you might need to log out and log back in to your Magento Admin Panel.")
				);
			}
		}
		
		if ($notify == null)
			return null;
			
		// generate html to add to response
		$zizio_html = $this->_helper->GetNotificationBoxHtml("Zizio Power Share", $notify);
		
		// add script block to html and return it
		if ($html == null)
			$html = $this->GetBlockHtml($block);
		
		// @patch for old magento, content doesn't show otherwise
		if ($event->getName() == "core_block_abstract_to_html_before")
			print $html . $zizio_html;
		
		return $html . $zizio_html;
	}
	
	public function AdminControllerPredispatch ($observer = null)
	{
		// check if cronjobs need running
		try
		{
			Mage::helper('powershare/cron')->CheckCronJobs();
		}
		catch (Exception $ex)
		{
			Zizio_Powershare_Helper_Data::LogError($ex);
		}
		
		// forward non-registered users from config to registration
		$action = $observer->getEvent()->getControllerAction();
		$params = $action->getRequest()->getParams();
		
		if (($action->getFullActionName() == "adminhtml_system_config_edit") &&
			(isset($params['section']) && ($params['section'] == "powershare")))
		{
			if ($this->_helper->IsZizioPowershareRegistered())
			{
				// patch old magentos for ie 9
				$enterprise = $this->_helper->IsEnterpriseEdition();
				if (($enterprise && ($this->_helper->CompareVersions(Mage::getVersion(), '1.11.0') < 0)) ||
					(!$enterprise && ($this->_helper->CompareVersions(Mage::getVersion(), '1.6.0') < 0)))
				{
					$action->getResponse()->setHeader("X-UA-Compatible", "IE=8", true);
				}
			}
			else
			{
				// for magento ver > 1.3.0
				$e = null;
				try { $e = new Mage_Core_Controller_Varien_Exception();
				} catch(Exception $ex) { }
				if ($e !== null)
				{
					$e->prepareForward('index', 'adminhtml_zizioRegister', 'powershare', $params);
					throw $e;
				}
				// for mage 1.3.0
				$url = Mage::getModel('adminhtml/url')	
						   ->setRouteName("powershare")
						   ->setControllerName("adminhtml_zizioRegister")
						   ->setActionName("index");
				$action->getResponse()->setRedirect($url->getUrl());
			}
		}
		return $this;
	}
	
	/* public function CheckoutCartProductAddAfter($observer)
	{
		try
		{
			// add coupon code if available in session
			$session = Mage::getSingleton('adminhtml/session');
			if ($session->hasZizioCouponCode())
			{
				$coupon = $session->getZizioCouponCode();
				$session->unsZizioCouponCode(); // prevent the coupon from being applied again in the same session
				$cart = Mage::getSingleton('checkout/cart');
				$cart->getQuote()->setCouponCode($coupon)->save();
			}
		}
		catch (Exception $ex)
		{
			$this->_helper->LogError($ex);
		}
	} */
	
}

Zizio_Powershare_Model_Observer::StaticConstructor();
