<?php 
set_time_limit(0);
ini_set('memory_limit', '-1');
require_once "../app/Mage.php";
require_once "feedlist_item.php";
Mage::app('default');
define('SEP',"\t");


$product = Mage::getModel("catalog/product")->load(1);
echo "<pre>";
print_r($product->getData('gb'));
echo "</pre>";





