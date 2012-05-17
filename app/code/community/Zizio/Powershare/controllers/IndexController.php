<?php
class Zizio_Powershare_IndexController extends Mage_Core_Controller_Front_Action
{

	/************************************************************************
	 * Actions
	 ************************************************************************/

	/*
	 * Maps to <base url>/index.php/powershare/index/testEvents
	 */
	public function testEventsAction()
	{
		$this->h3("Zizio Power Share - Testing Events");
		echo("<p>Magento version: " . Mage::getVersion() . "<br />");
		echo("Extension version: " . Zizio_Powershare_Helper_Data::GetExtVer("") . "<br />");
		echo("IsZizioPowershareEnabled() is: " . (Zizio_Powershare_Helper_Data::IsZizioPowershareEnabled() ? "true" : "false") . "</p>");

		$this->SuiteSetup();

		$this->Test("core_block_abstract_to_html_before");
		$this->Test("core_block_abstract_to_html_after");
		$this->Test("catalog_product_collection_load_after");

		$this->SuiteCleanup();
	}
	
	/*
	 * Maps to <base url>/index.php/powershare/index/applyCoupon
	 */
	public function applyCouponAction()
	{
		try
		{
			$coupon = trim(Mage::app()->getRequest()->getParam('zizio_coupon'));
			if ($coupon)
				Mage::getSingleton('checkout/cart')->getQuote()->setCouponCode($coupon)->save();
				//Mage::getSingleton('adminhtml/session')->setZizioCouponCode($coupon);
		}
		catch (Exception $ex)
		{
			Mage::helper('powershare')->LogError($ex);
		}
	}
	
	/*
	 * Maps to <base url>/index.php/powershare/index/validateCoupon
	 */
	public function validateCouponAction()
	{
		try
		{
			$callback = trim(Mage::app()->getRequest()->getParam('zizio_callback'));
			$coupon_code = trim(Mage::app()->getRequest()->getParam('zizio_coupon'));
			$end_date = trim(Mage::app()->getRequest()->getParam('zizio_end_date'));
			
			$response = array('warnings' => array(), 'error' => null);
			if ($coupon_code)
			{
				$rule = null;
				try
				{
					$coupon = Mage::getModel('salesrule/coupon')->load($coupon_code, "code");
					if (($coupon->getCode() == $coupon_code) && $coupon->hasRuleId())
						$rule = Mage::getModel('salesrule/rule')->load($coupon->getRuleId());
				}
				catch (Exception $ex)
				{
	        		// mangeto ver 1.4 and down uses older rule model, no salesrule/coupon
					$collection = Mage::getModel('salesrule/rule')->getCollection();
					$collection->addFieldToFilter('coupon_code', $coupon_code);
					if ($collection->count() > 0)
						$rule = $collection->getFirstItem();
				}
				
				if ($rule)
				{
					if ($end_date)
					{
						$end_date = Mage::app()->getLocale()->date($end_date);
						$to_date = Mage::app()->getLocale()->date($rule->getToDate());
						if ($rule->getToDate() && ($to_date < $end_date)) // $rule->getToDate() == null means no expiration
							$response['warnings'][] = "Coupon expires before incentive";
					}
					if (count($rule->getConditions()->getConditions()) > 0)
						$response['warnings'][] = "Coupon has restrictive conditions";
				}
				else
					$response['error'] = "Can't find coupon code";
					
			}
			else
				$response['error'] = "No coupon code supplied";

			if (count($response['warnings']) == 0)
				$response['warnings'] = null;
			$response['result'] = ($response['error'] || $response['warnings']) ? "fail" : "success";
				
			$this->getResponse()->setHeader("Content-Type", "text/javascript");
			$this->getResponse()->setBody(sprintf("%s(%s);", $callback, Zizio_Powershare_Helper_Data::json_encode($response)));
		}
		catch (Exception $ex)
		{
			Zizio_Powershare_Helper_Data::LogError($ex);
			if ($callback)
			{
				$this->getResponse()->setHeader("Content-Type", "text/javascript");
				$this->getResponse()->setBody(sprintf("%s(%s);", $callback, Mage::helper('core')->jsonEncode(array(
					'status' => "fail",
					'error' => "Unknown error"))));
			}
		}
	}

	/************************************************************************
	 * Tests
	 ************************************************************************/

	protected function core_block_abstract_to_html_before()
	{
		$block = new Mage_Core_Block_Text();
		Mage::dispatchEvent('core_block_abstract_to_html_before', array('block' => $block));
	}

	protected function core_block_abstract_to_html_after()
	{
		$block = new Mage_Core_Block_Text();
		$transportObject = new Varien_Object;
		Mage::dispatchEvent('core_block_abstract_to_html_after', array('block' => $block, 'transport' => $transportObject));
	}

	protected function catalog_product_collection_load_after()
	{
		$product = Mage::getModel('catalog/product');
		// limit the number of products read, so as not to read the entire catalog (!)
		$product_collection = $product->getCollection()->setPage(1, 4);
		Mage::dispatchEvent(
			'catalog_product_collection_load_after',
			array('collection' => $product_collection)
		);
	}

	/************************************************************************
	 * Testing Framework
	 ************************************************************************/

	protected function SuiteSetup()
	{
		$GLOBALS["zizio_test_running"] = true;
	}

	protected function SuiteCleanup()
	{
		unset($GLOBALS["zizio_test_running"]);
	}

	protected function Test($methodName)
	{
		$this->TestSetup($methodName);

		try
		{
			// Actual test:
			$this->{$methodName}();

			// If no exception occurred then test succeeded:
			$this->passed();
		}
		catch (Exception $ex)
		{
			$this->failed($ex);
		}

		// Assert that the event handler was actually invoked:
		$this->echoAssert("zizio_test_entered");
		$this->echoAssert("zizio_test_exited");
		$this->echoExitPoint();

		$this->TestCleanup();
	}

	protected function TestSetup($methodName)
	{
		$this->hr();
		$this->h4($methodName);
	}

	protected function TestCleanup()
	{
		unset($GLOBALS["zizio_test_entered"]);
		unset($GLOBALS["zizio_test_exited"]);
		unset($GLOBALS["zizio_test_exit_point"]);
	}

	protected function echoAssert($str)
	{
		$value = (isset($GLOBALS[$str])) && ($GLOBALS[$str]);
		$formatttedValue = $value ? $this->good("true") : $this->bad("false");
		echo($str . ": " . $formatttedValue . ".<br />");
	}

	protected function echoExitPoint()
	{
		$exitPoint = (isset($GLOBALS["zizio_test_exit_point"])) ?  $GLOBALS["zizio_test_exit_point"] : $this->bad("unknown");
		echo("exit point: " . $exitPoint . ".<br />");
	}


	/************************************************************************
	 * Helper Functions
	 ************************************************************************/

	protected function h3($str)
	{
		echo("<h3>".$str."</h3>");
	}

	protected function h4($str)
	{
		echo("<strong>".$str.": </strong>");
	}

	protected function hr()
	{
		echo("<hr />");
	}

	protected function passed()
	{
		echo($this->good("Passed.") . "<br/>");
	}

	protected function failed($ex)
	{
		echo($this->bad("Failed!") . "<br/>");
		if ($ex != null)
		{
			echo("<pre>".$ex."</pre>");
		}
	}

	protected function good($str)
	{
		return("<font color='green'>".$str."</font>");
	}

	protected function bad($str)
	{
		return("<font color='red'>".$str."</font>");
	}

}