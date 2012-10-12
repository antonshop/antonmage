<?php
require('source/lib/soap/nusoap.php');

echo getIP();

$client = new SoapClient("http://www.webxml.com.cn/WebServices/WeatherWebService.asmx?wsdl");   

$ip = new SoapClient("http://webservice.webxml.com.cn/WebServices/IpAddressSearchWebService.asmx?wsdl");

$param = array('theIpAddress'=>'202.82.107.1');//注意参数 
$out = $ip->getCountryCityByIp($param); 
$arr = $out->getCountryCityByIpResult->string; 
echo '<pre>'; 
print_r($arr); 
echo '</pre>';
$area = explode(' ', $arr[1]); 
//echo $area[0];
echo '<pre>';   
//print_r($ip->getCountryCityByIp('202.82.107.1'));
print_r($ip->__getFunctions ());
print_r($client->__getFunctions ()) ;//获取WebService提供的函数   
echo '</pre>';   

$city = $area[0];   
$param = array('theCityName'=>$city);//参数   
$out = $client->getWeatherbyCityName($param);//调用getWeatherbyCityName方法   
$arr = $out->getWeatherbyCityNameResult->string;   

//打印输出   
echo '<pre>';   
print_r($arr);   
echo '</pre>';  

//获取ip
function getIP() {
	if (getenv("HTTP_CLIENT_IP")){
		$ip = getenv("HTTP_CLIENT_IP");
	}else if(getenv("HTTP_X_FORWARDED_FOR")){
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	}else if(getenv("REMOTE_ADDR")){
		$ip = getenv("REMOTE_ADDR");
	}else{ 
		$ip = "Unknow";	
	}
	return $ip;
}

?>