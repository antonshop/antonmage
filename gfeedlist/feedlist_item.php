<?php
$google_list_item['id'] = array(
	'title' => 'id',
	'method' => 'getid',
	'value' => '',
);
$google_list_item['name'] = array(
	'title' => 'title',
	'method' => 'getName',
	'value' => '',
);
$google_list_item['description'] = array(
	'title' => 'description',
	'method' => 'getDescription',
	'value' => 'desc',
);
$google_list_item['link'] = array(
	'title' => 'link',
	'method' => 'getProductUrl',
	'value' => 'url',
);
$google_list_item['image_link'] = array(
	'title' => 'image link',
	'method' => 'getImageUrl',
	'value' => '',
);	
$google_list_item['price'] = array(
	'title' => 'price',
	'method' => 'getPrice',
	'value' => 'pr',
);                                                      
$google_list_item['sale_price'] = array(
	'title' => 'sale price',
	'method' => 'getPrice',
	'value' => 'pr',
);
/*$google_list_item['sale_price_date'] = array(
	'title' => 'sale price effective date',
	'method' => 'getSpecialFromDate',
	'value' => '',
);*/
$google_list_item['shipping_weight'] = array(
	'title' => 'shipping weight',
	'method' => 'getWeight',
	'value' => 'fl',
);
$google_list_item['product_type'] = array(
	'title' => 'product type',
	'method' => '',
	'value' => 'type',
);
/*$google_list_item['item_group_id'] = array(
	'title' => 'item group id',
	'method' => 'getSku',
	'value' => '',
);*/
$google_list_item['mpn'] = array(
	'title' => 'mpn',
	'method' => 'getSku',
	'value' => '',
);

$google_list_item_attr['color'] = array(
	'title' => 'color',
	'value' => 'color',
	'type'  => ''
);
$google_list_item_attr['brand'] = array(
	'title' => 'brand',
	'value' => 'brand',
	'type'  => ''
);
$google_list_item_attr['gb'] = array(
	'title' => 'google product category',
	'value' => 'gb',
	'type'  => 'txt'
);	
$google_list_item_attr['age_group'] = array(
	'title' => 'age group',
	'value' => 'agegroup',
	'type'  => ''
);		
$google_list_item_attr['gender'] = array(
	'title' => 'gender',
	'value' => 'gender',
	'type'  => ''
);	
$google_list_item_attr['gtin'] = array(
	'title' => 'gtin',
	'value' => 'mpn',
	'type'  => 'txt'
);	

/*$google_list_item_attr['size'] = array(
	'title' => 'size',
	'value' => '',
);	*/	


$google_list_item_fixinfo['tax'] = array(
	'title' => 'tax',
);
$google_list_item_fixinfo['shipping'] = array(
	'title' => 'shipping',
);$google_list_item_fixinfo['excluded_destination'] = array(
	'title' => 'excluded destination',
);$google_list_item_fixinfo['expiration_date'] = array(
	'title' => 'expiration date',
);


