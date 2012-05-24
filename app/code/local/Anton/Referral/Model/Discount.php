<?php

class Anton_Referral_Model_Discount extends Mage_SalesRule_Model_Quote_Discount {

	protected $_discount;

	public function collect(Mage_Sales_Model_Quote_Address $address) {//Mage_Sales_Model_Quote_Item_Abstract $item,
		parent::collect($address);
		$quote = $address->getQuote();
		$store = Mage::app()->getStore($quote->getStoreId());

		//$items = $this->_getAddressItems($address);
		//if (!count($items)) {
		//return $this;
		//}

		$eventArgs = array(
            'website_id' => $store->getWebsiteId(),
            'customer_group_id' => $quote->getCustomerGroupId(),
            'coupon_code' => $quote->getCouponCode(),
		);
			
		$this->_calculator->init($store->getWebsiteId(), $quote->getCustomerGroupId(), $quote->getCouponCode());
		$address->setDiscountDescription(array());

		foreach ($items as $item) {
			if ($item->getNoDiscount()) {
				$item->setDiscountAmount(0);
				$item->setBaseDiscountAmount(0);
			} else {
				/**
				 * Child item discount we calculate for parent
				 */
				if ($item->getParentItemId()) {
					continue;
				}

				$eventArgs['item'] = $item;
				Mage::dispatchEvent('sales_quote_address_discount_item', $eventArgs);

				if ($item->getHasChildren() && $item->isChildrenCalculated()) {
					foreach ($item->getChildren() as $child) {
						$this->_calculator->process($child);
						$eventArgs['item'] = $child;
						Mage::dispatchEvent('sales_quote_address_discount_item', $eventArgs);
						$this->_aggregateItemDiscount($child);
					}
				} else {
					$this->_calculator->process($item);
					$this->_aggregateItemDiscount($item);
				}
			}
		}

		/**
		 * Process shipping amount discount
		 */
		$address->setShippingDiscountAmount(0);
		$address->setBaseShippingDiscountAmount(0);
		if ($address->getShippingAmount()) {
			$this->_calculator->processShippingAmount($address);
			$this->_addAmount(-$address->getShippingDiscountAmount());
			$this->_addBaseAmount(-$address->getBaseShippingDiscountAmount());
		}

		$this->_calculator->prepareDescription($address);
		return $this;
	}


	/**
	 * Aggregate item discount information to address data and related properties
	 *
	 * @param   Mage_Sales_Model_Quote_Item_Abstract $item
	 * @return  Mage_SalesRule_Model_Quote_Discount
	 */
	protected function _aggregateItemDiscount($item)
	{

		/*Database connectivity*/
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('write');
		/*Getting table prefix*/
		$tPrefix = (string) Mage::getConfig()->getTablePrefix();
		$facebook = Mage::getModel('referral/referral')->getFacebook();

		$user = $facebook->getUser();

		$discount_amount = Mage::getModel('referral/referral')->getFbDiscountamount();
		$min_order_amount = Mage::getModel('referral/referral')->getFbOrderamount();
		$enabled = Mage::getModel('referral/referral')->getFbStatus();


		$basediscount_amount = $discount_amount;
		$discount_amount = Mage::helper('core')->currency($discount_amount, false, false);
		$fetchstatus = Mage::getModel('referral/referral')->getReferraluser($user);
		$referral = $fetchstatus[0]['status'];
		
		/* get the cart subtotal */
		$lastQid = Mage::getSingleton('checkout/session')->getQuoteId();
		$customerQuote = Mage::getModel('sales/quote')->load($lastQid);
		$subTotal = $customerQuote->getSubtotal();
		$cartItems = Mage::helper('checkout/cart')->getCart()->getItemsCount();
		if ($subTotal >= $discount_amount && $referral == 1 && $enabled == 1 && $subTotal >= $min_order_amount) {

			/**
			 * Process Referral discount
			 */

			$fbDiscount = $discount_amount;
			$basefbDiscount = $basediscount_amount;
			$item->setDiscountAmount($item->getDiscountAmount()+($fbDiscount/$cartItems));
			$item->setBaseDiscountAmount($item->getBaseDiscountAmount()+($basefbDiscount/$cartItems));
				
		}


		$this->_addAmount(-($item->getDiscountAmount()));
		$this->_addBaseAmount(-($item->getBaseDiscountAmount()));

		return $this;
	}


	public function fetch(Mage_Sales_Model_Quote_Address $address) {
		$amount = $address->getDiscountAmount();
		$cartItems = Mage::helper('checkout/cart')->getCart()->getItemsCount();
		$discountAmount = Mage::getModel('referral/referral')->getFbDiscountamount();

		if ($amount != 0) {

			$discountAmount = '-' . floor($discountAmount);

			if ($discountAmount != $amount) {
				$address->setDiscountDescription('Overall');
				$description = $address->getDiscountDescription();
				$title = Mage::helper('sales')->__('Discount (%s)', $description);
			} else {
				$address->setDiscountDescription('for Referral');
				$description = $address->getDiscountDescription();
				$title = Mage::helper('sales')->__('Discount (%s)', $description);
			}
			$address->addTotal(array(
                'code' => $this->getCode(),
                'title' => $title,
                'value' => $amount
			));
		}
		return $this;
	}

}