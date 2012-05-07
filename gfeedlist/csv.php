<?php 
set_time_limit(0);
ini_set('memory_limit', '-1');
define('SPIDERIMG', 'spiderimg/');
define('SPIDERCSV', 'spidercsv/');
define('FILENAME',SPIDERCSV . "spider.csv");

$handle=fopen('a.csv','r');

$pageurl = "http://www.onlinenikenfljerseys.com/nike-nfl-denver-broncos-youth-customized-game-team-color-orange-jersey-p-681.html";

//file_put_contents('info.txt', $info);
$list = array(
	'store'				=> 'default',
	'websites'			=> 'base',
	'attribute_set'		=> 'Default',
	'type'				=> 'simple',
	'name'				=> '',
	'categories'		=> '',
	'has_options'		=> '1',
	'required_options'	=> '1',
	'image'				=> '',
	'small_image'		=> '',
	'thumbnail'			=> '',
	'url_key'			=> '',
	'image_label'		=> '',
	'price'				=> '',
	'status'			=> '1',
	'tax_class_id'		=> 'None',
	'is_in_stock'		=> '1',
	'description'		=> '',
	'visibility'		=> 'Catalog, Search',
	'qty'				=> rand(600,1000),
	'Size:drop_down:1'	=> ''
);

$url = "http://www.onlinenikenfljerseys.com/images/";
$folder = SPIDERIMG;
/*$imgname = "Nike-NFL-Arizona-Cardinals-11-Larry-Fitzgerald-Elite-Team-Color-Red-Jersey.jpg";
echo getFile($url, $folder, $imgname);*/

if(file_exists(FILENAME))unlink(FILENAME);
if(!is_dir(FILENAME))mkdir(SPIDERCSV);
if(!is_dir(FILENAME))mkdir(SPIDERIMG);
$spider = new spider();
$spider->fwriteTitle($list);
$i=0;
while($data = fgetcsv($handle,"\t"))   
{   print_r($data);exit;
	$info  = file_get_contents('info.txt');
	if($i>3) break;
	$i++;
	$downinfo = $list;
	$downinfo['name'] = $spider->getData($info, '<h1 class="pro_name">', '</h1>');
	$downinfo['image_label'] = $spider->getData($info, '<h1 class="pro_name">', '</h1>');
	//$downinfo['spider']->getData($info, '<title>', '</title>')."<br>";
	//$downinfo['model'] = $spider->getData($info, '<div>Model:', '</div>');
	$downinfo['Size:drop_down:1'] = $spider->getOptionList($spider->clearHtml($spider->getData($info, '--Select Size--</option>', '</select>')));
	$downinfo['description'] = $spider->getData($info, '<p><strong>Description</strong>:</p>', '</div>');
	$downinfo['categories'] = $spider->getData($info, '<span class="category-subs-selected">', '</span>');
	$downinfo['price'] = str_replace('$', '', $spider->getData($info, '<h2 id="productPrices" class="productGeneral">', '</h2>'));
	$imgurl = $spider->getData($info, '<a href="javascript:popupWindow(\\\'', '\\\')"');
	//echo '<input value="' . $imgurl . '" size="100">';
	$imginfo  = file_get_contents(str_replace('&amp;', '&', $imgurl));
	//file_put_contents('imginfo.txt', $imginfo);
	
	$img_name = basename($spider->getData($imginfo, '<img src="', '" alt='));
	$downinfo['image'] = $img_name;
	$downinfo['small_image'] = $img_name;
	$downinfo['thumbnail'] = $img_name;
	$spider->fwriteFile($downinfo);
	echo getFile($url, $folder, $img_name);
	//print_r($downinfo);
}


class spider{
	
	public function getData($content, $begin, $end)
	{
		if($begin)$content = strstr($content,$begin);
		if($end)$content = substr($content,0,strpos($content,$end));
		return str_replace(array($begin,$end),array('',''),$content);
	}
	
	 // 过滤html
	public function clearHtml($content)
	{
		$replaceArray = array("@\<script(.*?)\</script\>@is", "@\<style(.*?)\</style\>@is","@\<iframe(.*?)\</iframe\>@is","@<(.*?)>@is");
		foreach ($replaceArray as $varl){
			$content = preg_replace($varl,'',$content);
		}
		return strip_tags($content);
	}
	
	// 获取标题或指定
	public function getTitle($content)
	{
		if(empty($content))return false;
		if($this->Rules['titlebegin'] && $this->Rules['titleend']){
			// 获取指定区域的标题
			$title   = trim($this->clearHtml($this->getText($content,'title')));
		}else {
		// 默认标题
			preg_match('/<title>(.+)<\/title>/s',$content,$matches);
			$title   =   $this->Rules['tdel']?str_replace("/{$this->Rules['tdel']}/","",$matches[1]):$matches[1];
		}
		return $title;
	}
	
	// 获取指定区域内容
	public function getText($content,$Field){
		if($Field && $this->Rules[$Field.'begin'] && $this->Rules[$Field.'end']){
			$outText =   $this->getData($content,$this->Rules[$Field.'begin'],$this->Rules[$Field.'end']);
			return $this->Rules[$Field.'del']?str_replace("/{$this->Rules[$Field.'del']}/","",$outText):$outText;
		}else return false;
	}

	public function getFile($url, $folder, $imgname){     
		//set_time_limit(0);    
		$destination_folder = $folder ? $folder.'/' : '';//文件下载保存目录  
		$newfname = $destination_folder . $imgname;     
		$file = fopen ($url . $imgname, "rb");     
		if ($file) {     
		$newf = fopen ($newfname, "wb");     
		if ($newf)     
		 while(!feof($file)) {     
				fwrite($newf, fread($file, 1024 * 8 ), 1024 * 8 );     
			}     
		}     
		if ($file) {
			fclose($file);
		}     
		if ($newf) {     
			fclose($newf);     
		}     
	} 
	
	public function getOptionList($options){
		$content = '';
		$options = explode(" ",$options);
		foreach($options as $value){
			$value = trim($value);
			if(!empty($value))
				$content .= $value . ":fixed:0.00|";
		}
		return substr($content, 0, -1);
	}
	
	public function fwriteFile($array, $file=FILENAME){
		$add = fopen($file, 'a+');
		fputcsv($add, $array);
		fclose($add);
	}
	
	public function fwriteTitle($array, $file=FILENAME){
		$title = '';
		foreach($array as $key=>$value){
			$title .= $key . ',';
		}
		$add = fopen($file, 'a+');
		fwrite($add, substr($title, 0, -1)."\n");
		fclose($add);
	}

}
function getFile($url, $folder, $imgname){     
    //set_time_limit(0);    
    $destination_folder = $folder ? $folder.'/' : '';//文件下载保存目录  
    $newfname = $destination_folder . $imgname;     
    $file = fopen ($url . $imgname, "rb");     
    if ($file) {     
    $newf = fopen ($newfname, "wb");     
    if ($newf)     
   	 while(!feof($file)) {     
			fwrite($newf, fread($file, 1024 * 8 ), 1024 * 8 );     
		}     
    }     
    if ($file) {     
        fclose($file);
    }     
    if ($newf) {     
        fclose($newf);     
    }     
}  

exit;


echo "<pre>";
$imglink = "http://www.onlinenikenfljerseys.com/index.php?main_page=popup_image&pID=";
$site = "http://www.onlinenikenfljerseys.com/";
while($data = fgetcsv($handle,"\t"))   
{   
   //print_r($data[6]);
	preg_match("([0-9]+)", $data[6], $matches);
	print_r($matches);
	echo $imglink.$matches[0]."<br>";
	$content = file_get_contents($imglink.$matches[0]);
	//echo '<input value="'. $content .'">';
	$imgname = explode('<img', $content);
	$imgname = explode('"', $imgname[1]);
	print_r($imgname[1]);
	exit;   
   
}
echo "</pre>";


?>