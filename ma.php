<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"  "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>来自www.hellokeykey.com</title>
    </head>
    <body>
 
<?php
//初始化magento的api连接
$client = new SoapClient('http://www.hillkey.com/api/soap/?wsdl');
$session = $client->login('apiuser', 'apikey');
echo "<br />";
echo "session:" . $session;
echo "<br />";
$product_type = "simple";
$product_name = "iPhone" . rand();//产品名称
$product_sku = "happigo001".rand();//产品货号
$product_des = "description";//产品描述
$product_sdes = "short description"; //产品简短描述
$product_price = 100;//产品价格
$product_status = "1";//产品状态，1为激活，0为不激活
$product_website = array(1); //产品属于哪个website
$product_category_id[0] = 10; //产品所属分类ID
$product_weight = "500";  //商品重量
$product_news_from_date = "2011-04-01 00:00:00";//产品新品开始日期
$product_news_to_date = "2011-04-30 00:00:00";//产品新品结束日期
$product_tax_class_id = "0"; //产品税的设置
$product_visibility = "4"; //产品可见的设置
//        $product_url_path = "test111"; //产品url    不管用，使用url_key
$product_url_key = $product_name;   //此值有空格的话，会自动换成横线，不错，省的处理了
$product_cost = "90";  //商品成本
$product_special_price = "95";  //商品特价价格
$product_special_from_date = "2011-04-01 00:00:00";  //特价开始日期
$product_special_to_date = "2011-05-01 00:00:00";  //特价结束日期
$product_is_in_stock = "1"; //是否有库存
$product_qty = 100;  //库存数量
$product_manage_stock = "1"; //manage_stock 状态
$product_enable_googlecheckout = "0"; //是否支持google支付
$product_custom_design = "default/modern"; //设置使用哪个模板主题
$product_custom_layout_update = '<reference name="left">
    <block type="core/template" name="left.permanent.callout" template="callouts/left_col.phtml">
<action method="setImgSrc"><src>images/media/col_left_callout.jpg</src></action>
<action method="setImgAlt" translate="alt" module="catalog"><alt>Our customer service is available 24/7. Call us at (555) 555-0123.</alt></action>
<action method="setLinkUrl"><url>checkout/cart</url></action>
    </block>
</reference>'; // layout xml
$product_custom_design_from = "2011-04-01 00:00:00"; //模板启用日期
$product_custom_design_to = "2011-05-01 00:00:00"; //模板启用日期
$product_page_layout = "two_columns_right"; //模板结构
$product_gift_message_available = "1"; //是否允许礼物留言
$attribute_set = "9"; // 这个是属性组，属性组是
$imagePath = "http://www.hillkey.com/media/catalog/product/h/t/htc-touch-diamond.jpg";  //产品图片路径
//        产品的数据数组
$newProductData = array(
    'name' => $product_name,
    'websites' => $product_website,
    'short_description' => $product_sdes,
    'description' => $product_des,
    'price' => $product_price,
    'status' => $product_status,
    'weight' => $product_weight,
    'news_from_date' => $product_news_from_date,
    'news_to_date' => $product_news_to_date,
    'tax_class_id' => $product_tax_class_id,
    'visibility' => $product_visibility,
    'url_key' => $product_name,
    'cost' => $product_cost,
    'special_price' => $product_special_price,
    'special_from_date' => $product_special_from_date,
    'special_to_date' => $product_special_to_date,
    'enable_googlecheckout' => $product_enable_googlecheckout,
    'custom_design' => $product_custom_design,
    'custom_layout_update' => $product_custom_layout_update,
    'page_layout' => $product_page_layout,
    'custom_design_from' => $product_custom_design_from,
    'custom_design_to' => $product_custom_design_to,
    'gift_message_available' => $product_gift_message_available,
);
//        产品图片
//        初始化产品图片信息，注意自己上传个产品图片到magento的产品图片文件夹
//        label为图片的alt属性
//        position 为图片的显示顺序
//        type 为此图片作为'thumbnail','small_image','image'中的哪一个
//        mime为图片类型
 
$newImage = array(
    'file' => array(
'name' => 'file_name',
'content' => base64_encode(file_get_contents($imagePath)),
'mime' => 'image/jpeg'
    ),
    'label' => 'Cool Image Through Soap',
    'position' => 1,
    'types' => array('thumbnail', 'small_image', 'image'),
    'exclude' => 0
);
//    array(
//        'file' => array(
//    'name' => 'file_name',
//    'content' => base64_encode(file_get_contents('/media/catalog/product/m/o/model.jpg')),
//    'mime' => 'image/jpeg'
//        ),
//        'label' => 'Cool Image Through Soap',
//        'position' => 2,
//        'types' => array('small_image'),
//        'exclude' => 0
//    )
//        print_r($newImage);
echo '<p style="color:#FFFFFF; background-color:#999999; padding:10px;">New product Info:</p>';
echo "New product NO.:" . $client->call($session, 'product.create', array($product_type, $attribute_set, $product_sku, $newProductData));
echo "<br />";
//        使用api创建一个商品（本代码来自www.hellokeykey.com）
$see_new_product_info = $client->call($session, 'product.info', $product_sku);
//        更新商品的库存信息
$client->call($session, 'product_stock.update', array($product_sku, array('manage_stock' => $product_manage_stock, 'qty' => $product_qty, 'is_in_stock' => "1")));
//        设置商品分类
$client->call($session, 'category.assignProduct', array($product_category_id, $product_sku, 1));
//        创建产品图片，注意是先有产品后添加图片的
$imageFilename = $client->call($session, 'product_media.create', array($product_sku, $newImage));
//输出刚创建的产品信息
$see_new_product_info = $client->call($session, 'product.info', $product_sku);
var_dump($see_new_product_info);
echo "<p>stock:";
//        输出库存信息
var_dump($client->call($session, 'product_stock.list', $product_sku));
echo "</p><br />";
echo '<p style="color:#FFFFFF; background-color:#999999; padding:10px;">New Product Image info:</p>';
var_dump($client->call($session, 'product_media.list', $product_sku));
//        $attributeSets = $attributeSets[1];
//        $attributeSet_ID = array_search('Default',$attributeSets);
//        echo "<br />";
//        echo "Default AttributeSet ID;".$attributeSet_ID;
//        $set = current($attributeSets);
//        以下为示例输出，一个是输出现有的属性组以及代码。然后是输出一个magento simple product产品，自己后台新建一个产品，将此产品的属性填写完整，好输出来分析下各个属性值的特点，与自己要创建的坐下对比
$attributeSets = $client->call($session, 'product_attribute_set.list');
$attributes = $client->call($session, 'product_attribute.list','40');
$attribute_options = $client->call($session, 'product_attribute.options', array('attribute_id'=>'502'));
//        $set = current($attributeSets);
//        $attributes = $client->call($session, 'product_attribute.list', $set['set_id']);
//        var_dump($attributes);
echo '<p style="color:#FFFFFF; background-color:#999999; padding:10px;">Attribute Sets:</p>';
var_dump($attributeSets);
echo '<p style="color:#FFFFFF; background-color:#999999; padding:10px;">Attribute Set of shoes:</p>';
var_dump($attributes);
echo '<p style="color:#FFFFFF; background-color:#999999; padding:10px;">Attribute shoes_size:</p>';
var_dump($attribute_options);
echo '<p style="color:#FFFFFF; background-color:#999999; padding:10px;">Product Date:</p>';
//        echo "<br />set<br />";
//        var_dump($set);
$sku_of_product = "HTC Touch Diamond";
$p_info = $client->call($session, 'product.info', $sku_of_product);
var_dump($p_info);
echo "<p>stock:";
var_dump($client->call($session, 'product_stock.list', $sku_of_product));
echo "</p><br />";
echo "<br />";
echo '<p style="color:#FFFFFF; background-color:#999999; padding:10px;">Product Image info:</p>';
var_dump($client->call($session, 'product_media.list', $sku_of_product));
//        $attribute_options = $client->call($session, 'product_attribute.options', array('attribute_id'=>'put_attribute_id_as_int_here'));
//var_dump($attribute_options);
?>
 
<?php $client->endSession($session); ?>
    </body>
</html>