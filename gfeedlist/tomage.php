<?php
define('MAGENTO', dirname(__FILE__));
ini_set('memory_limit', '-1');
 
require_once MAGENTO . '/../app/Mage.php';
 
Mage::app();
//create dvd english product
$product = Mage::getModel('catalog/product');
$product->setTypeId('simple');
$product->setTaxClassId(0); //none
//$product->setWebsiteIds(array(1));  // store id
$product->setAttributeSetId(4); //Videos Attribute Set
/*	<option value="6">dvd</option>7
<option value="5">vhs</option> */
//$product->setMediaFormat(6);  //DVD video
//$product->setLanguage(9); //English
$product->setSku(ereg_replace("\n","","videoTest2.d8"));
$product->setName(ereg_replace("\n","","videoTest2.12"));
$product->setDescription("videoTest2");
$product->setInDepth("video test");    
$product->setPrice("139.95");
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