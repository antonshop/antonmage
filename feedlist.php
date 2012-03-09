<?php 
set_time_limit(0);
/*require_once 'app/Mage.php';
Mage::app('default');


 // $model = Mage::getModel('catalog/product'); //getting product model 
//	$_product = $model->load(168);
	$product = Mage::getModel('catalog/product')->load(168);
	$cate = $product->getCategoryids();
	print_r(Mage::getModel('catalog/category')->load($cate[0])->getName());exit;
	$i = 1;
    echo "<pre>";
    foreach ($product->getOptions() as $o) {
        echo "<strong>Custom Option:" . $i . "</strong><br/>";
        echo "Custom Option TYPE: " . $o->getType() . "<br/>";
        echo "Custom Option TITLE: " . $o->getTitle() . "<br/>";
        echo "Custom Option Values: <br/>";
        // Getting Values if it has option values, case of select,dropdown,radio,multiselect
        $values = $o->getValues();
        foreach ($values as $v) {
                print_r($v->getData());
        }
        $i++;
 
        echo "----------------------------------<br/>";
    }
exit;*/

/*$options = Mage::getModel('catalog/product_option')->getProductOptionCollection($product);
	
	foreach ($options as $option) {
		$optionValue = Mage::getResourceModel('catalog/product_option_value_collection')
		->addFieldToFilter('option_id', $option->getId())->getData();
		$scodd[] = array($option->getPrice(),$optionValue);
	}
	print_r($scodd);
	foreach($_product->getOptions() as $item){
		print_r($item->getValues(1)->getData());
	}*/
//   $category = Mage::getModel('catalog/category')->load(3);
//  
//    $productCollections = $category->getProductCollection();
// $productCollections->addAttributeToSelect('*');
//print_r($category->getAllChildren(1));
//exit;
 // foreach($productCollections AS $productCollection) {
//   print_r($productCollection->getAttributeText('brand'));
//   echo '**' .$productCollection->getid(). '**' . $productCollection->getName();
//      echo "<br/>";
//   }
//$_product = Mage::getModel('catalog/product')->load(167);
//$id = 'getid';
//$em = '';
//echo '**' .$_product->$id($em). '**' . $_product->getName();
//   exit;
require_once "app/Mage.php";
require_once "feedlist_item.php";
Mage::app('default');
define('SEP',"\t");
/*$google_list_item = array(
	'id' => 'id',
	'name' => 'title',
	'description' => 'description',
	'google_product_category' => 'google product category',
	'product_type' => 'product type',
	'link' => 'link',
	'image_link' => 'image link',
	'a_image_link' => 'additional image link',
	'condition' => 'condition',
	'availability' => 'availability',
	'price' => 'price',
	'sale_price' => 'sale price',
	'sale_price_date' => 'sale price effective date',
	'brand' => 'brand',
	'gtin' => 'gtin',
	'mpn' => 'mpn',
	'gender' => 'gender',
	'age_group' => 'age group',
	'color' => 'color',
	'size' => 'size',
	'material' => 'material',
	'pattern' => 'pattern',
	'item_group_id' => 'item group id',
	'tax' => 'tax',
	'shipping' => 'shipping',
	'shipping_weight' => 'shipping weight',
	'online_only' => 'online only',
	'excluded_destination' => 'excluded destination',
	'expiration_date' => 'expiration date',
);*/

//select query  
$read = Mage::getSingleton('core/resource')->getConnection('core_read');  
//make connection  
$qry = "select * FROM directory_currency_rate"; //query  
$res = $read->fetchAll($qry); //fetch row  
$currency_rate = array();
foreach($res as $re){
	$currency_rate[$re['currency_to']] = $re['rate'];
}


function nodeToArray(Varien_Data_Tree_Node $node)
{
	$result = array();
	$result['category_id'] = $node->getId();
	$result['parent_id'] = $node->getParentId();
	$result['name'] = $node->getName();
	$result['is_active'] = $node->getIsActive();
	$result['position'] = $node->getPosition();
	$result['level'] = $node->getLevel();
	$result['children'] = array();
	
	foreach ($node->getChildren() as $child) {
		$result['children'][] = nodeToArray($child);
	}
	
	return $result;
}

function load_tree() {

	$tree = Mage::getResourceSingleton('catalog/category_tree')
		->load();
	
	$store = 1;
	$parentId = 1;
	
	$tree = Mage::getResourceSingleton('catalog/category_tree')
		->load();
	
	$root = $tree->getNodeById($parentId);
	
	if($root && $root->getId() == 1) {
		$root->setName(Mage::helper('catalog')->__('Root'));
	}
	
	$collection = Mage::getModel('catalog/category')->getCollection()
		->setStoreId($store)
		->addAttributeToSelect('name')
	//->addAttributeToSelect('id')
	->addAttributeToSelect('is_active');
	
	$tree->addCollectionData($collection, true);
	
	return nodeToArray($root);

}

function get_post($post){
	if(isset($_POST[$post]))
		return $_POST[$post];
	else 
		return '';
}

$cate_options = array();
$cate_i = 0;
function print_tree($tree,$level) {
	
	global $cate_options, $cate_i;
	$level ++;
	
	foreach($tree as $item) {
		$cate_options[$cate_i]['name'] = str_repeat("&nbsp;&nbsp;", $level).$item['name'];
		$cate_options[$cate_i]['category_id'] = $item['category_id'];
		$cate_i++;
		print_tree($item['children'],$level);
	}
	
	return $cate_options;
}


function get_category_ids($tree){
	
	global $cateids;
	foreach($tree['children'] as $item){
		$cateids[] = $item['category_id'];
		if(is_array($item['children'])){
			get_category_ids($item);
		}
	}
	return $cateids;
}

function get_subcate_ids($id){
	$category = Mage::getModel('catalog/category')->load($id);
	return $category->getAllChildren(true);
}

function get_product_ids($tree, $category_id){
	global $pids;
	if($category_id){
		$cateids = get_subcate_ids($category_id);
	}else{
		$cateids = get_category_ids($tree);
	}
	foreach($cateids as $id){
		$_featcategory = Mage::getModel('catalog/category')->load($id);
		$_productCollection = Mage::getResourceModel('reports/product_collection')
		->addAttributeToSelect('*')
		->addCategoryFilter($_featcategory);
		
		foreach ($_productCollection as  $product ) { 
			if(in_array($product->getId(),$pids)){
				continue;
			}else{
				$pids[] = $product->getId();
			}
		}
	}
}

function put_feedlist($filename, $pids, $list_item, $google_list_item_attr){
	global $post_content, $currency, $title_options, $country;
	$replace_foundarr = array("\r",'"',"\n");
	$replace_arr = array("",'""',"");
	
	$content = '';
	//$pids = array(168);
	
	foreach($pids as $id){
		$model = Mage::getModel('catalog/product'); 
		$product = $model->load($id);
		/* custom options */
		
		if($product->getOptions()){
			if(isset($options)){				
				unset($options);
			}else{
				$options = array();
			}
			
			foreach ($product->getOptions() as $o) {
				$k=0;
				$oval = $o->getValues();
				
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
			//if($id=168){print_r($options);}
			
			$i=1;
			foreach($options as $value){
				foreach($list_item as $key=>$item){
					if($item['title'] == 'id'){
						$content .= $product->$item['method']()."_".$country."_".$i.SEP;
					}else if(!empty($item['value'])){
						if($item['value'] == 'type'){
							$cate = $product->getCategoryids();
							
							if(isset($cate[0]) && $cate[0]){
								$cate_id = $cate[0];
								$content .= Mage::getModel('catalog/category')->load($cate[0])->getName() . SEP;
							}else{
								$content .=  SEP;
							}
						}else if($item['value'] == 'fl'){
							$content .= number_format($product->$item['method'](), 2, '.', '') . SEP;
						}else if($item['value'] == 'pr'){
							/* price changed*/
							$temp_price = floatval($value['price'])+floatval($product->$item['method']());
							$content .= ($temp_price / $currency_rate[$currency]) . $currency . SEP;
						}else{
							$content .= number_format($product->$item['method'](), 2, '.', ''). $currency . SEP;
						}
					}else{
						$content .= str_replace($replace_foundarr,$replace_arr,$product->$item['method']()) . SEP;
					}
				}
				$content .= feedlist_fixinfo($country);
				$content .= $post_content.SEP;
				foreach($google_list_item_attr as $attr){
					if(empty($attr['value'])){
						$content .= SEP;
					}else{
						$content .= $product->getAttributeText($attr['value']) . SEP;
					}	
				}
				
				
				$content .= $value['title']."\n";
				$i++;
			}
			
		}else{
			$i=1;
			foreach($list_item as $key=>$item){
				if($item['title'] == 'id'){
					$content .= $product->$item['method']()."_".$country."_".$i.SEP;
				}else if(!empty($item['value'])){
					//$product_info[$key] = $product->$item['method']($item['value']);
					if($item['value'] == 'type'){
						$cate = $product->getCategoryids();
						
						if(isset($cate[0]) && $cate[0]){
							$cate_id = $cate[0];
							$content .= Mage::getModel('catalog/category')->load($cate_id)->getName() . SEP;
						}else{
							$content .=  SEP;
						}
					}else if($item['value'] == 'fl'){
						$content .= number_format($product->$item['method'](), 2, '.', '') . SEP;
					}else{
						$content .= number_format($product->$item['method'](), 2, '.', ''). $currency . SEP;
					}
				}else{
					$content .= str_replace($replace_foundarr,$replace_arr,$product->$item['method']()) . SEP;
				}
				$i++;
			}
			$content .= feedlist_fixinfo($country);
			foreach($google_list_item_attr as $attr){
				if(empty($attr['value'])){
					$content .= SEP;
				}else{
					$content .= $product->getAttributeText($attr['value']) . SEP;
				}	
			}
			
			$content .= $post_content."\n";
		}
	}
//	var_dump($attributes);US CH GB AU 
//echo get_feedlist_title() . $title_options ."\n";
	file_put_contents($filename.".txt", get_feedlist_title() .SEP. $title_options ."\n" . $content);
}

function feedlist_fixinfo($areacode){
	$content = $areacode . '::0:'.SEP;
	$content .= $areacode . ':::0'.SEP;
	$content .= 'Product Search, Product Ads, Commerce Search'.SEP;
	$content .= Mage::getModel('core/date')->date('Y-m-d\TH:i:s', strtotime('+25 day')).SEP;
	return $content;
}


function get_feedlist_title(){
	global $google_list_item, $google_list_item_attr ,$google_list_item_fixinfo, $post_title;
	$content = ''; 
	foreach($google_list_item as $item){
		$content .= $item['title'].SEP;
	}
	
	foreach($google_list_item_fixinfo as $fixinfo){
		$content .= $fixinfo['title'].SEP;
	}
	$content .= $post_title;
	foreach($google_list_item_attr as $attr){
		$content .= SEP.$attr['title'];
	}
	return $content;
}

function get_attr(){
	global $google_list_item_attr;
	$attr = '';
	foreach($google_list_item_attr as $key=>$value){
		$attr .= $key."=>".$value['value'].",";
	}
	return substr($attr, 0, -1);
}

$tree = load_tree();
$category_tree = print_tree($tree['children'],0);

$pids = array();

$filename = get_post('filename');
$category_id = get_post('category_id');
$google_product_category = get_post('google_product_category');
$attribute_code = get_post('attribute_code');
$condition = get_post('condition');
$availability = get_post('availability');
$currency = get_post('currency');
$country = get_post('country');
$material = get_post('material');
$pattern = get_post('pattern');
$online_only = get_post('online_only');

$post_content = $google_product_category.SEP.$condition.SEP.$availability;
$post_title = 'google product category'.SEP."condition".SEP."availability";

if($material){
	SEP.$post_content .= $material;
	SEP.$post_title  .= "material";
}
if($pattern){
	SEP.$post_content .= $pattern;
	SEP.$post_title  .= "pattern";
}
$title_options = '';
//file_put_contents('title.txt',get_feedlist_title());exit;

if($filename){
	$attr = explode(',',$attribute_code);
	$attr_arr = array();
	foreach($attr as $att){
		$attr_arr[] = explode('=>',$att);
	}
	foreach($attr_arr as $val){
		if(isset($google_list_item_attr[$val[0]])){
			$google_list_item_attr[$val[0]]['value'] = $val[1];
		}
	}
	//print_r($google_list_item_attr);exit;
	get_product_ids($tree, $category_id);
	sort($pids);//print_r($pids);
	put_feedlist($filename, $pids, $google_list_item, $google_list_item_attr);
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
</head>
<style>
body{ line-height:26px; color:#333; font-size:14px; margin:0px; padding:0px;}
div ul li { margin:0px; padding:0px;}
.feedlist{ padding:20px 0 0 30px;}
.item_table{ width:960px;}
.item_table li{ float:left; list-style:none; line-height:28px;}
.item_title{ width:220px; text-align:right;}
.item_input{ text-align:left; width:680px; padding-left:15px;}
.item_input .intxt{ width:220px; border:1px #ccc solid; height:20px;}
.longtxt{ width:340px; border:1px #ccc solid; height:20px;}
.submit_but{ border:1px solid #333; background:#333; color:#fff; width:60px; height:22px; padding-bottom:3px;  cursor:pointer;}
.item_input select{width:220px;}
.item_input textarea{ text-align:left;}
</style>
<body>
<div class="feedlist">
<form action="feedlist.php" method="post" onsubmit="return checkform()">
 <div class="item_table">
 	<ul>
    	<li class="item_title">File name: </li>
        <li class="item_input"><input type="text" class="intxt" id="filename" name="filename">.txt 文件名称</li>
    </ul>
    <ul>
    	<li class="item_title">Category:</li>
        <li class="item_input">
            <select name="category_id" id="category_id">
                <option value="0">所有分类</option>
                <?php foreach($category_tree as $item){
                    echo '<option value="' . $item['category_id'] . '">' . $item['name'] . '</option>';
                 }?>
            </select>
        </li>
    </ul>
    <ul>
    	<li class="item_title">Google product category: </li>
        <li class="item_input"><input type="text" class="intxt" name="google_product_category" id="google_product_category"> Google产品分类</li>
    </ul>
    <ul>
    	<li class="item_title">Condition:</li>
        <li class="item_input"><select name="condition" id="condition">
    	<option value="new">new</option>
        <option value="used">used</option>
        <option value="refurbished">refurbished</option>
    </select> 商品的使用情况或状态</li>
    </ul>
    <ul>
    	<li class="item_title">Availability:</li>
        <li class="item_input"><select name="availability" id="availability">
    	<option value="in stock">in stock</option>
        <option value="available for order">available for order</option>
        <option value="out of stock">out of stock</option>
        <option value="out of stock">preorder</option>
    </select> 商品的库存状况</li>
    </ul>
    <ul>
    	<li class="item_title">currency:</li>
        <li class="item_input"><input type="text" class="intxt" id="currency" name="currency"> 货币 ISO 4217 标准</li>
    </ul>
    <ul>
    	<li class="item_title">country:</li>
        <li class="item_input"><input type="text" class="intxt" id="country" name="country"> 国家编号</li>
    </ul>
    <ul>
    	<li class="item_title">material:</li>
        <li class="item_input"><input type="text" class="intxt" name="material" id="material"> 商品的材质</li>
    </ul>
    <ul>
    	<li class="item_title">pattern:</li>
        <li class="item_input"><input type="text" class="intxt" name="pattern" id="pattern"> 商品的图案/图形</li>
    </ul>
<!--    <ul>
    	<li class="item_title">online only</li>
        <li class="item_input"><input name="online_only" checked="checked" type="radio" value="y">yes  <input name="online_only" type="radio" value="n">
        no 商品是否只能在线购买</li>
    </ul>-->
    <ul>
    	<li class="item_title"> Attribute Code:</li>
        <li class="item_input">
        	<textarea cols="50" rows="3" name="attribute_code" id="attribute_code"><?php echo get_attr();?></textarea>
         自定义产品属性 逗号分割</li>
    </ul>
    <ul>
    	<li class="item_title">&nbsp;</li>
        <li class="item_input"><input type="submit" value="submit" class="submit_but"></li>
    </ul>
 </div>
    
</form>
<script type="text/javascript">
	function checkform() {
		var filename = document.getElementById('filename');
		var category_id = document.getElementById('category_id');
		var google_product_category = document.getElementById('google_product_category');
		var currency = document.getElementById("currency");
		var country = document.getElementById("country");
		
		if(filename.value==''){
			alert("Please input file name!");
			filename.focus();
			return false;
		}
		if(google_product_category.value==''){
			alert("Please input google product category!");
			google_product_category.focus();
			return false;
		}
		if(currency.value == ''){
			alert("Please input currency!");
			currency.focus();
			return false;
		}
		if(country.value == ''){
			alert("Please input country!");
			country.focus();
			return false;
		}
	}
</script>
</div>
</body>
</html>




