<?php 
set_time_limit(0);
ini_set('memory_limit', '-1');
require_once "../app/Mage.php";
require_once "feedlist_item.php";
Mage::app('default');
define('SEP',"\t");
define('ERR',"");


/* currency */
$read = Mage::getSingleton('core/resource')->getConnection('core_read');  
//make connection  
$qry = "select * FROM ".Mage::getSingleton('core/resource')->getTableName('directory_currency_rate');//query  
$res = $read->fetchAll($qry); //fetch row  
$currency_rate = array();
foreach($res as $re){
	$currency_rate[$re['currency_to']] = $re['rate'];
}
$basecurrency = $re['currency_from'];


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

/* category tree */
function load_tree() {

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
		if($item['is_active']){
			$cate_options[$cate_i]['name'] = str_repeat("&nbsp;&nbsp;", $level).$item['name'];
			$cate_options[$cate_i]['category_id'] = $item['category_id'];
			$cate_i++;
			print_tree($item['children'],$level);
		}
	}
	
	return $cate_options;
}

/* get category ids */
function get_category_ids($tree){
	
	global $cateids;
	foreach($tree['children'] as $item){
		if($item['is_active']){
			$cateids[] = $item['category_id'];
		}
		if(is_array($item['children'])){
			get_category_ids($item);
		}
	}
	return $cateids;
}
/* get sub category id */
function get_subcate_ids($id){
	$category = Mage::getModel('catalog/category')->load($id);
	return $category->getAllChildren(true);
}

/* get products ids by category_id */
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

/* put file list */
function put_feedlist($filename, $pids, $list_item, $google_list_item_attr){
	global $post_content, $currency, $title_options, $country, $currency_rate, $optionsarr;
	$replace_foundarr = array("\r",'\t',"\n");
	$replace_arr = array("",' ',"");
	
	$content = '';
	
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
			
			$i=1;
			foreach($options as $value){
				foreach($list_item as $key=>$item){
					$content .= get_item_value($product, $currency, $country, $currency_rate, $key, $item, $i);
				}
				$content .= feedlist_fixinfo($country);
				$content .= $post_content;
				$content .= feedlist_item_attr($google_list_item_attr, $product).SEP;
				/*foreach($google_list_item_attr as $attr){
					if(empty($attr['value'])){
						$content .= SEP;
					}else{
						$content .= $product->getAttributeText($attr['value']) . SEP ;
					}	
				}*/
				
				$content .= $value['title']."\n";
				$i++;
			}
			
		}else{/* no custom options */
			$i=1;
			foreach($list_item as $key=>$item){
				$content .= get_item_value($product, $currency, $country, $currency_rate, $key, $item, $i);
				$i++;
			}
			$content .= feedlist_fixinfo($country);
			$content .= $post_content;
			$content .= feedlist_item_attr($google_list_item_attr, $product);
			/*foreach($google_list_item_attr as $attr){
				if(empty($attr['value'])){
					$content .= SEP;
				}else{
					$content .= $product->getAttributeText($attr['value']) . SEP ;
				}	
			}*/
			/* no custom options add tab */
			if(!empty($optionsarr)){
				if(in_array($id, $optionsarr)){
					unset($optionsarr[$id]);
				}else{
					$content .= SEP;
				}
			}
			$content .= "\n";
		}
		
	}
	/* custom options */
	if($title_options){
		$titles = get_feedlist_title() .SEP. $title_options."\n";
	}else{
		$titles = get_feedlist_title()."\n";
	}
	
	/* txt file */
	if(!is_dir('tmep')){ mkdir('temp');}
	file_put_contents('temp/' . $filename.".txt", $titles  . $content);
	
	/* zip */
	require('phpzip.php');
	$zip = new PHPZip;
	//$zip->add_file($titles  . $content, 'temp/'.$filename.".txt");
	

	//header("Content-Disposition: attachment; filename=". $filename .".zip");
	//header("Content-Type: application/unknown");
	$zip->zip(dirname(__FILE__)."/temp", $filename.".zip");
	
	check_error("temp/" . $filename . ".txt");
	//die($zip->file());
}
/* fix information */
function feedlist_fixinfo($areacode){
	global $excluded;
	$content = $areacode . '::0:'.SEP;
	$content .= $areacode . ':::0'.SEP;
	$content .= $excluded.SEP;
	$content .= Mage::getModel('core/date')->date('Y-m-d\TH:i:s', strtotime('+25 day')).SEP;
	return $content;
}

function get_item_value($product, $currency, $country, $currency_rate, $key, $item, $i){
	$replace_foundarr = array("\r",'\t',"\n");
	$replace_arr = array("",' ',"");
	$content = '';


	if($item['title'] == 'id'){
		$content .= $product->$item['method']()."_".$country."_".$i.SEP;
	}else if(!empty($item['value'])){
		/* category name */
		if($item['value'] == 'type'){
			$cate = $product->getCategoryids();
			if(isset($cate[0]) && $cate[0]){
				$cate_id = $cate[0];
				$content .= Mage::getModel('catalog/category')->load($cate_id)->getName() . SEP;
			}else{
				$content .=  SEP;
			}
		}else if($item['value'] == 'desc'){
			$content .= substr($product->$item['method'](), 0, 999) . SEP;
		}else if($item['value'] == 'url'){
			$content .= str_replace('/'.basename(__FILE__), '/index.php', $product->$item['method']()) . SEP;
		}else if($item['value'] == 'fl'){
			$content .= number_format($product->$item['method'](), 2, '.', '') . SEP;
		}else if($item['value'] == 'pr'){
				/* price changed*/
				$temp_price = floatval($value['price'])+floatval($product->$item['method']());
				$content .= number_format($temp_price * floatval($currency_rate[$currency]),2,".","") . $currency . SEP;
		}else{
			$content .= number_format($product->$item['method'](), 2, '.', ''). $currency . SEP;
		}
	}else{
		$content .= str_replace($replace_foundarr,$replace_arr,$product->$item['method']()) . SEP;
	}
	return $content;
}

/* check error */
function check_error($filename){
	
	$content = file_get_contents($filename);
	$content = explode("\n", $content);
	$title = $content[0];
	$title = explode("\t", $title);
	array_shift($content);
	$errinfo = '';
	$tmpid = '';

	foreach($content as $itemstr){
		$item = explode("\t", $itemstr);
		
		if(preg_match("/\t\t/", $itemstr) || strlen($item[1])>=70){
			if($tmpid == substr($itemstr, 0, strpos($itemstr, "_"))){
				continue;
			}
			$tmpid = substr($itemstr, 0, strpos($itemstr, "_"));
			$errinfo .= $item[0].', ';
			
			foreach($item as $key=>$value){
				if(!$value){
					$errinfo .= $title[$key].", ";
				}
			}
			if(strlen($item[1])>=70){
				$errinfo .= 'name('. strlen($item[1]) .'), ';
			}

			if($item[1] == strtoupper($item[1])){
				$errinfo .= 'nameuppercase, ';
			}
			$errinfo .= "\n";
		}
	}
	if(!empty($errinfo)){
		file_put_contents("error.txt", $errinfo);
		unlink($filename);
	}
}

/**/
function feedlist_item_attr($item_attr, $product){
	$content = '';
	
	foreach($item_attr as $attr){
		/*if(empty($attr['value']) || !$product->getData($attr['value'])){
			$content .= SEP;
		}else{
			if($attr['type'] == 'txt'){
				$content .= $product->getData($attr['value']) . SEP ;
			}else{
				$content .= $product->getAttributeText($attr['value']) . SEP ;
			}
		}*/
		if(!empty($attr['value']) && $product->getData($attr['value'])){
			if($attr['type'] == 'txt'){
				$content .= SEP . $product->getData($attr['value'])  ;
			}else{
				$content .=  SEP .$product->getAttributeText($attr['value']);
			}
		}else{
			$content .= SEP;
		}
	}
	return $content;
}

/* feed list title */
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
/* 自定义属性 */
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
/* get post */
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
$excluded = get_post('excluded');

$post_content = $condition.SEP.$availability;
$post_title = "condition".SEP."availability";

/* 可选项 */
if($material){
	SEP.$post_content .= $material;
	SEP.$post_title  .= "material";
}
if($pattern){
	SEP.$post_content .= $pattern;
	SEP.$post_title  .= "pattern";
}
$title_options = '';

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
	get_product_ids($tree, $category_id);
	sort($pids);
	$idstr = '';
	foreach($pids as $item){
		$idstr .= $item . ",";
	}
	/* get products options */
	$idstr = substr($idstr, 0, -1);
	$sql = "SELECT * FROM " . Mage::getSingleton('core/resource')->getTableName('catalog_product_option') . " WHERE product_id in (".$idstr.")";
	$res = $read->fetchAll($sql);
	$optionsarr = array();
	foreach($res as $value){
		$optionsarr[$value['product_id']] = $value;
	}
	
	put_feedlist($filename, $pids, $google_list_item, $google_list_item_attr);
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>feed list</title>
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
<form action="feedlist2.php" method="post" onsubmit="return checkform()">
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
<!--    <ul>
    	<li class="item_title">Google product category: </li>
        <li class="item_input"><input type="text" class="intxt" name="google_product_category" id="google_product_category"> Google产品分类</li>
    </ul>-->
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
        <li class="item_input">
        	<select name="currency">
            	<?php foreach($currency_rate as $key=>$rate){?>
            	<option value="<?php echo $key;?>"<?php if($key == $basecurrency){echo ' selected="selected"';}?>><?php echo $key;?></option>
                <?php }?>
            </select>
       <!-- <input type="text" class="intxt" id="currency" name="currency"> -->货币 ISO 4217 标准</li>
    </ul>
    <ul>
    	<li class="item_title">country:</li>
        <li class="item_input"><input type="text" class="intxt" id="country" name="country"> 国家编号</li>
    </ul>
    <ul>
    	<li class="item_title">Excluded:</li>
        <li class="item_input"><input type="text" class="intxt" value="Product Ads, Commerce Search" id="excluded" name="excluded"> 不适用平台</li>
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




