<?php 
/*require_once 'app/Mage.php';
   Mage::app('default');
  
   $category = Mage::getModel('catalog/category')->load(22);
  
    $productCollections = $category->getProductCollection();
 $productCollections->addAttributeToSelect('name');


  foreach($productCollections AS $productCollection) {
   echo $productCollection->getName();
      echo "<br/>";
   }
   exit;*/

require_once "app/Mage.php";
Mage::app('1');

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

function print_tree($tree,$level) {
	$level ++;
	foreach($tree as $item) {
		echo str_repeat("*", $level).$item['name'].'*'.$item['category_id']."<br>";
		print_tree($item['children'],$level);
	}
}

$cateids = array();
function get_category_ids($tree){
	global $cateids;
	//print_r($tree);
	foreach($tree['children'] as $item){
		$cateids[] = $item['category_id'];
		if(is_array($item['children'])){
			get_category_ids($item);
		}
	}
	return $cateids;
}

function get_product_info($tree){
	global $products;
	$cateids = get_category_ids($tree);
	//$cateids = array(3);
	foreach($cateids as $id){
		$_featcategory = Mage::getModel('catalog/category')->load($id);
		$_productCollection = Mage::getResourceModel('reports/product_collection')
		->addAttributeToSelect('*')
		->addCategoryFilter($_featcategory);
		
		foreach ($_productCollection as  $product ) { 
			if(in_array($product->getId(),$products)){
				continue;
			}else{
				$products[] = $product->getId();
			}
			//print_r($product->getId());
			//echo "<br>";
		}
	}
	
}
$tree = load_tree();
//print_r(get_category_ids($tree));
//print_r($tree);

//print_tree($tree['children'],0);
//
//print_r($tree);
//
$products = array();
get_product_info($tree);
sort($products);
echo "<pre>";print_r($products);echo "</pre>";


$google_list_item = array(
	'id' => 'id',
	'name' => 'title',
	'description' => 'description',
	'google_product_category' => 'google product category',
	'product_type' => 'product type',
	'link' => 'link',
	'image_link' => 'image link',
	'' => 'additional image link',
	'' => 'condition',
	'' => 'availability',
	'' => 'price',
	'' => 'sale price',
	'' => 'sale price effective date',
	'' => 'brand',
	'' => 'gtin',
	'' => 'mpn',
	'' => 'gender',
	'' => 'age group',
	'' => 'color',
	'' => 'size',
	'' => 'material',
	'' => 'pattern',
	'' => 'item group id',
	'' => 'tax',
	'' => 'shipping',
	'' => 'shipping weight',
	'' => 'online only',
	'' => 'excluded destination',
	'' => 'expiration date',
);




