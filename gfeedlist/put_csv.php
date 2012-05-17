<?php
set_time_limit(0);
ini_set('memory_limit', '-1');
define('SPIDERIMG', '/spiderimg/');
define('SPIDERCSV', 'spidercsv/');
define('FILENAME', "spider.csv");

$color = array('Red', 'White', 'Purple', 'Blue', 'Black', 'Orange', 'Green', 'Brown', 'Yellow', 'Pink', 'Claret', 'Gold', 'Nude', 'Beige', 'Gray', 'Rose', 'Amethyste', 'Camel', 'Silver', 'Tan', 'Fuchsia', 'Chocolate', 'Champagne', 'Ivory', 'Taupe', 'Turquoise', 'Bronze');

$list = array(
	'store'				=> 'default',
	'websites'			=> 'base',
	'attribute_set'		=> 'Default',
	'type'				=> 'simple',
	'name'				=> '',
	'categories'		=> '',
	'sku'				=> '',
	'has_options'		=> '1',
	'required_options'	=> '1',
	'image'				=> '',
	'small_image'		=> '',
	'manage_stock'		=> '0',
	'use_config_manage_stock' => '0',
	'thumbnail'			=> '',
	'url_key'			=> '',
	'image_label'		=> '',
	'price'				=> '',
	'color'				=> '',
	'status'			=> 'Enabled',
	'weight'			=> rand(1,2),
	'tax_class_id'		=> 'None',
	'is_in_stock'		=> '1',
	'description'		=> '',
	'short_description'		=> '',
	'visibility'		=> 'Catalog, Search',
	'qty'				=> rand(600,1000),
	'Size:drop_down:1'	=> '',
	'gallery'			=> ''
);
$handle=fopen('Content.csv','r');
fwriteTitle($list, FILENAME);
$size = 'US5=UK3.5=EU36|US6=UK4.5=EU37|US7=UK5.5=EU38|US8=UK6.5=EU39|US9=UK7.5=EU40|US10=UK8.5=EU41';
$search = array("(", ")", ":", "/", "|||");
$replace = array("", "", "", "=", ":fixed:0.00|");

while($data = fgetcsv($handle,"\t")) {
	$csvlist = $list;
	$csvlist['name'] = trim($data[3]);
	$csvlist['categories'] = trim($data[6]);
	$csvlist['sku'] = trim($data[4]);
	$csvlist['image'] = SPIDERIMG . basename($data[8]);
	$csvlist['small_image'] = SPIDERIMG . basename($data[8]);
	$csvlist['thumbnail'] = SPIDERIMG . basename($data[8]);
	$csvlist['image_label'] = trim($data[3]);
	$csvlist['price'] = trim($data[9]);
	$csvlist['description'] = $data[10];
	$csvlist['Size:drop_down:1'] = str_replace($search, $replace, $data[7]).":fixed:0.00";
	foreach($color as $value){
		if(strstr($csvlist['name'], $value)){
			$csvlist['color'] = $value;
		}
	}
	
	$gallery = '';
	$data[5] = trim($data[5]);
	if(!empty($data[5])){
		$imglist = explode("|||", $data[5]);
		foreach($imglist as $value){
			$gallery .= SPIDERIMG.basename($value).';';
		}
		$csvlist['gallery'] = $gallery;
	}
	
	fwriteFile($csvlist, FILENAME);
	echo "<pre>";
	//print_r($csvlist);
	echo "<pre>";
	//exit;
}

function fwriteTitle($array, $file=FILENAME){
	$title = '';
	foreach($array as $key=>$value){
		$title .= $key . ',';
	}
	$add = fopen($file, 'a+');
	fwrite($add, substr($title, 0, -1)."\n");
	fclose($add);
}

function fwriteFile($array, $file=FILENAME){
	$add = fopen($file, 'a+');
	fputcsv($add, $array);
	fclose($add);
}




?>