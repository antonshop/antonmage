<?php
/**
  * get color by product name 
  */
set_time_limit(0);
ini_set('memory_limit', '-1');
$handle=fopen('content.csv','r');
$url = 'http://www.christinlouboutinshoes2012.com/';
$folder = dirname(__FILE__).'/2012img';

$row=1;
while($data = fgetcsv($handle,"\t"))  {
	if(trim($data[5])){
		$imglist = explode("|||",$data[5]);
		//print_r($imglist);exit;
		foreach($imglist as $value){
			getFile($url, $folder, $value);
		}
	}
	if(trim($data[8]))
		getFile($url, $folder, $data[8]);
	writenum($row);
	$row++;
}

function writenum($num){
	file_put_contents('num.txt', $num);
}

function getFile($url, $folder, $imgname){     
	//set_time_limit(0);    

	$destination_folder = $folder ? $folder.'/' : '';//文件下载保存目录  
	$newfname = $destination_folder . basename($imgname);     
	//echo $url . $imgname;
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
?>