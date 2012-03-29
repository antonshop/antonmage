<?php
define('MAGENTO', dirname(__FILE__));
ini_set('memory_limit', '-1');
 
require_once MAGENTO . '/../app/Mage.php';
 
Mage::app();
//create dvd english product
$product = Mage::getModel('catalog/product');
$product->setTypeId('simple');
$product->setTaxClassId(0); //none
$product->setAttributeSetId(4); //Videos Attribute Set
$product->setSku(ereg_replace("\n","","test3"));
$product->setName(ereg_replace("\n","",""));
$product->setDescription("testtesttest3");
$product->setInDepth("test");    
$product->setPrice("324");
$product->setData("gb",'google category xx');
$product->setData("color",'3');
$product->setShortDescription(ereg_replace("\n","","videoTest2.1"));
$product->setWeight(0);
$product->setStatus(1); //enabled
$product->setVisibility(4); //nowhere
$product->setMetaDescription(ereg_replace("\n","","videoTest2.1"));
$product->setMetaTitle(ereg_replace("\n","","videotest2"));
$product->setMetaKeywords("video test");

try{
$product->save();
	$productId = $product->getId();
	echo $product->getId() . ", $price, $itemNum added\n";
}
catch (Exception $e){ 		
	echo "$price, $itemNum not added\n";
	echo "exception:$e";
} 

		
?>