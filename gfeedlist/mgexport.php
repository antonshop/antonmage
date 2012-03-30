<?php
set_time_limit(0);
ini_set('memory_limit', '-1');
require_once "../app/Mage.php";
require_once "feedlist_item.php";
Mage::app('default');
define('SEP',",");

$exportss = array(
	"store" => '',
	"websites" => '',
	"attribute_set" => '',
	"type" => '',
	"category_ids" => '',
	"sku" => '',
	"has_options" => '',
	"name" => '',
	"meta_title" => '',
	"meta_description" => '',
	"image" => '',
	"small_image" => '',
	"thumbnail" => '',
	"url_key" => '',
	"url_path" => '',
	"custom_design" => '',
	"page_layout" => '',
	"options_container" => '',
	"image_label" => '',
	"small_image_label" => '',
	"thumbnail_label" => '',
	"country_of_manufacture" => '',
	"msrp_enabled" => '',
	"msrp_display_actual_price_type" => '',
	"gift_message_available" => '',
	"gb" => '',
	"mpn" => '',
	"rw_google_base_product_type" => '',
	"rw_google_base_product_categ" => '',
	"rw_google_base_12_digit_sku","price" => '',
	"special_price" => '',
	"weight" => '',
	"msrp" => '',
	"color" => '',
	"status" => '',
	"is_recurring" => '',
	"visibility" => '',
	"enable_googlecheckout" => '',
	"tax_class_id" => '',
	"brand" => '',
	"agegroup" => '',
	"gender" => '',
	"rw_google_base_skip_submi" => '',
	"description" => '',
	"short_description" => '',
	"meta_keyword" => '',
	"custom_layout_update" => '',
	"special_from_date" => '',
	"special_to_date" => '',
	"news_from_date" => '',
	"news_to_date" => '',
	"custom_design_from" => '',
	"custom_design_to" => '',
	"qty" => '',
	"min_qty" => '',
	"use_config_min_qty" => '',
	"is_qty_decimal" => '',
	"backorders" => '',
	"use_config_backorders" => '',
	"min_sale_qty" => '',
	"use_config_min_sale_qty" => '',
	"max_sale_qty" => '',
	"use_config_max_sale_qty" => '',
	"is_in_stock" => '',
	"low_stock_date" => '',
	"notify_stock_qty" => '',
	"use_config_notify_stock_qty" => '',
	"manage_stock" => '',
	"use_config_manage_stock" => '',
	"stock_status_changed_auto" => '',
	"use_config_qty_increments" => '',
	"qty_increments" => '',
	"use_config_enable_qty_inc" => '',
	"enable_qty_increments" => '',
	"stock_status_changed_automatically" => '',
	"use_config_enable_qty_increments" => '',
	"product_name" => '',
	"store_id" => '',
	"product_type_id" => '',
	"product_status_changed" => '',
	"product_changed_websites" => '',
	"gallery" => '',
	"gallery_labels" => '',
);

$read = Mage::getSingleton('core/resource')->getConnection('core_read');  
//make connection  
$qry = "select * FROM ".Mage::getSingleton('core/resource')->getTableName('catalog_product_entity'); 
$products = $read->fetchAll($qry);

$sql = "select attribute_code FROM ".Mage::getSingleton('core/resource')->getTableName('eav_attribute')." WHERE is_user_defined = 1"; 
$userattribute = $read->fetchAll($sql);
/*echo "<pre>";
print_r($userattribute);
echo "</pre>";exit;*/
$storeId = Mage::app()->getStore('default')->getId();

foreach($products as $value){
	$model = Mage::getModel('catalog/product'); 
	$product = $model->load(25);
	
	
	// get attribute set name
	$attributeSetModel = Mage::getModel("eav/entity_attribute_set");
	$attributeSetModel->load($product->getAttributeSetId());
	$attributeSetName  = $attributeSetModel->getAttributeSetName();
	//echo $product->getTypeId().'** ';exit;
	//get categories list
	$catCollection = $product->getCategoryCollection();
	$categs = $catCollection->exportToArray();
	# Get categories names
	$cate_list = '';
	foreach($categs as $cat){
		$categsToLinks [] = Mage::getModel('catalog/category')->load($cat['entity_id'])->getName();
		$cate_arr = explode('/',$cat['path']);
		array_shift($cate_arr);
		
		foreach($cate_arr as $value){
			$cate_list .= Mage::getModel('catalog/category')->load($value)->getName().'/';
		}
		
		$cate_list = substr($cate_list,0,-1).',';
	}
	$cate_list = substr($cate_list,0,-1);

	$stock      = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);

	$exports = array(
		"store" 			=> Mage::app()->getStore()->getCode(),
		"websites"			=> 'base',
		"attribute_set" 	=> $attributeSetName,
		"type" 				=> $product->getTypeId(),
		"categories" 		=> $cate_list,
		"sku" 				=> $product->getData('sku'),
		"has_options" 		=> $product->getData('has_options'),
		"name" 				=> $product->getName(),
		"meta_title" 		=> $product->getData('meta_title'),
		"meta_description" 	=> $product->getData('meta_description'),
		"image" 			=> $product->getData('image'),
		"small_image" 		=> $product->getData('small_image'),
		"thumbnail" 		=> $product->getData('thumbnail'),
		"url_key" 			=> $product->getData('url_key'),
		"url_path" 			=> $product->getData('url_path'),
		"custom_design" 	=> $product->getData('custom_design'),
		"page_layout" 		=> $product->getData('page_layout'),
		"options_container" => $product->getData('options_container'),
		"image_label" 		=> $product->getData('image_label'),
		"small_image_label" => $product->getData('small_image_label'),
		"thumbnail_label" 	=> $product->getData('thumbnail_label'),
		"country_of_manufacture" => $product->getData('country_of_manufacture'),
		"msrp_enabled" 		=> $product->getData('country_of_manufacture'),
		"msrp_display_actual_price_type" => $product->getData('msrp_display_actual_price_type'),
		"gift_message_available" => $product->getData('gift_message_available'),
		"rw_google_base_product_type" => $product->getData('rw_google_base_product_type'),
		"rw_google_base_product_categ" => $product->getData('rw_google_base_product_categ'),
		"rw_google_base_12_digit_sku" => $product->getData('rw_google_base_12_digit_sku'),
		"price" 			=> $product->getData('price'),
		"special_price" 	=> $product->getData('special_price'),
		"weight" 			=> $product->getData('weight'),
		"msrp" 				=> $product->getData('msrp'),
		"status" 			=> $product->getData('status'),
		"is_recurring" 		=> $product->getData('is_recurring'),
		"enable_googlecheckout" => $product->getData('enable_googlecheckout'),
		"tax_class_id" 		=> $product->getData('tax_class_id'),
		
		"rw_google_base_skip_submi" => $product->getData('rw_google_base_skip_submi'),
		"description" 		=> $product->getData('description'),
		"short_description" => $product->getData('short_description'),
		"meta_keyword"		=> $product->getData('meta_keyword'),
		"custom_layout_update" => $product->getData('custom_layout_update'),
		"special_from_date" => $product->getData('special_from_date'),
		"special_to_date" 	=> $product->getData('special_to_date'),
		"news_from_date" 	=> $product->getData('news_from_date'),
		"news_to_date" 		=> $product->getData('news_to_date'),
		"custom_design_from" => $product->getData('custom_design_from'),
		"custom_design_to" 	=> $product->getData('custom_design_to'),
		/*"gb" 				=> '',
		"mpn" 				=> '',
		"color" 			=> '',
		"brand" 			=> '',
		"agegroup" 			=> '',
		"gender" 			=> '',*/
		//"visibility"		=> '',
		
		"qty" 				=> $stock->getData('qty'),
		"min_qty" 			=> $stock->getData('min_qty'),
		"use_config_min_qty" => $stock->getData('use_config_min_qty'),
		"is_qty_decimal" 	=> $stock->getData('is_qty_decimal'),
		"backorders" 		=> $stock->getData('backorders'),
		"use_config_backorders" => $stock->getData('use_config_backorders'),
		"min_sale_qty" 		=> $stock->getData('min_sale_qty'),
		"use_config_min_sale_qty" => $stock->getData('use_config_min_sale_qty'),
		"max_sale_qty" 		=> $stock->getData('max_sale_qty'),
		"use_config_max_sale_qty" => $stock->getData('use_config_max_sale_qty'),
		"low_stock_date" 	=> $stock->getData('low_stock_date'),
		"notify_stock_qty" 	=> $stock->getData('notify_stock_qty'),
		"use_config_notify_stock_qty" => $stock->getData('use_config_notify_stock_qty'),
		"manage_stock" 		=> $stock->getData('manage_stock'),
		"use_config_manage_stock" => $stock->getData('use_config_manage_stock'),
		"stock_status_changed_auto" => $stock->getData('stock_status_changed_auto'),
		"use_config_qty_increments" => $stock->getData('use_config_qty_increments'),
		"qty_increments" 	=> $stock->getData('qty_increments'),
		"use_config_enable_qty_inc" => $stock->getData('use_config_enable_qty_inc'),
		"enable_qty_increments" => $stock->getData('enable_qty_increments'),
		"stock_status_changed_automatically" => $stock->getData('stock_status_changed_automatically'),
		"use_config_enable_qty_increments" => $stock->getData('use_config_enable_qty_increments'),
		//"product_name" 		=> '',
		//"store_id" 			=> '',
		//"product_type_id" 	=> '',
		//"product_status_changed" => '',
		//"product_changed_websites" => '',
		
		//"gallery" 			=> '',
		//"gallery_labels" 	=> '',
	);
	
	//get gallery list
	$gallery = $product->getData('media_gallery');
	array_shift($gallery['images']);
	
	foreach($gallery['images'] as $value){
		$exports['gallery'] .= basename($value['file']).";";
		$exports['gallery_labels'] .= basename($value['label']).";";
	}
	$exports['gallery'] = substr($exports['gallery'], 0, -1);
	$exports['gallery_labels'] = substr($exports['gallery_labels'], 0, -1);
	
	// user attribute
	foreach($userattribute as $value){
		if($product->getData($value['attribute_code'])){
			$exports[$value['attribute_code']] = $product->getAttributeText($value['attribute_code']);
		}
	}
	if($product->has_options){
		
		foreach ($product->getOptions() as $o) {
			$k=0;
			$oval = $o->getValues();
			print_r($o->getValues());
			foreach ($oval as $v) {
				//print_r($v->getData());
				if($v['price']){
					$options[$v['option_type_id']]['price'] =$v['price'];
					$options[$v['option_type_id']]['title'] = $v['title'];
					$title_options = $o->getTitle();
				}
				$k++;
			}
		}
	}
	//print_r($exports);exit;
}



		
?>