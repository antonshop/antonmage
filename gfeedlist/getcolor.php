<?php
/**
  * get color by product name 
  */
set_time_limit(0);
ini_set('memory_limit', '-1');
$handle=fopen('spider.csv','r');
$color = array('Red', 'White', 'Purple', 'Blue', 'Black', 'Orange', 'Green', 'Brown', 'Yellow', 'Pink');

$row=1;
while($data = fgetcsv($handle,"\t"))  {
	if($row > 1){
		$name = $data[4];
		foreach($color as $value){
			if(strstr($name, $value)){
				$data[] = $value;
				break;
			}
		}
		//print_r($data);exit;
	}else{
		$data[] = 'color';
	}
	fwriteFile($data,'spidercolor.csv');
	$row++;
}

function fwriteFile($array, $file=FILENAME){
	$add = fopen($file, 'a+');
	fputcsv($add, $array);
	fclose($add);
}
?>