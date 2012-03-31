<?php 
set_time_limit(0);
ini_set('memory_limit', '-1');
require_once "../app/Mage.php";
require_once "feedlist_item.php";
Mage::app('default');
define('SEP',"\t");


$str = 'e leather a particularly soft and velvety feel.<a href="http://www.mbtsale6.com">MBT Shoes</a> is th';
echo preg_replace('/<a[\s]+[^>]*?href[\s]?=[\s\"\']+(.*?)[\"\']+.*?>([^<]+|.*?)?<\/a>/', '\2', $str);
exit;
$product = Mage::getModel("catalog/product")->load(1);
echo "<pre>";
print_r($product->getData('gb'));
echo "</pre>";





