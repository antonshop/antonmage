<?php 
require_once 'app/Mage.php';
   Mage::app('default');
  
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
$_product = Mage::getModel('catalog/product')->load(167);
$id = 'getid';
$em = '';
echo '**' .$_product->$id($em). '**' . $_product->getName();
   exit;

require_once "app/Mage.php";
Mage::app('1');

$google_list_item_usa = array(
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
);

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

function put_feedlist($pids){
	//global $google_list_item_usa;
	$feedliststr = '';
	$product_info = array();
	$product = Mage::getModel('catalog/product')->load(167);
	$product['id'] = '';
	//print_r($google_list_item_usa);
	
}

$tree = load_tree();
$category_tree = print_tree($tree['children'],0);

$pids = array();

$filename = isset($_POST['filename']) ? $_POST['filename'] : false ;
$category_id = isset($_POST['filename']) ? $_POST['category_id'] : false ;
$google_product_category = isset($_POST['google_product_category']) ? $_POST['google_product_category'] : false ;

if($filename){

	get_product_ids($tree, $category_id);
	sort($pids);//print_r($pids);
	put_feedlist($pids);
}
print_r($google_list_item_usa);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
</head>
<style>
body{ line-height:26px; color:#333; font-size:14px;}
.feedlist{ padding:20px 0 0 30px;}
</style>
<body>
<div class="feedlist">
<form action="feedlist.php" method="post" onsubmit="return checkform()">
	File name: <input type="text" id="filename" name="filename"><br />
    Category: &nbsp;<select name="category_id" id="category_id">
    	<option value="0">所有分类</option>
        <?php foreach($category_tree as $item){
        	echo '<option value="' . $item['category_id'] . '">' . $item['name'] . '</option>';
         }?>
    </select><br />
    Condition: &nbsp;<select name="condition" id="condition">
    	<option value="new">new</option>
        <option value="used">used</option>
        <option value="refurbished">refurbished</option>
    </select><br />
    Availability: &nbsp;<select name="availability" id="availability">
    	<option value="in stock">in stock</option>
        <option value="available for order">available for order</option>
        <option value="out of stock">out of stock</option>
        <option value="out of stock">preorder</option>
    </select><br />
    currency: &nbsp;<input type="text" id="currency" name="currency">ISO 4217 标准<br />
    Google product category: <input type="text" name="google_product_category" id="google_product_category"><br />
    material:  <input type="text" name="material" id="material"><br />
    pattern:  <input type="text" name="pattern" id="pattern"><br />
    online only <input type="checkbox" value="y">yes  <input type="checkbox" value="n">no<br />
    <input type="submit" value="submit">
</form>
<script type="text/javascript">
	function checkform() {
		var filename = document.getElementById('filename');
		var category_id = document.getElementById('category_id');
		var google_product_category = document.getElementById('google_product_category');
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
	}
</script>
</div>
</body>
</html>




