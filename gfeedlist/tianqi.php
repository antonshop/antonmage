<?php 
/*$str = file_get_contents('http://61.4.185.48:81/g/');
$str = explode("id=", $str);
$id = substr($str[1], 0, strpos($str[1], ";"));*/

$id = '101010100';
if(isset($_COOKIE['weatherid']) && !empty($_COOKIE['weatherid'])){
	$id = $_COOKIE['weatherid'];
} else {
	$str = file_get_contents('http://61.4.185.48:81/g/');
	$str = explode("id=", $str);
	$id = substr($str[1], 0, strpos($str[1], ";"));
}
$content = file_get_contents('http://m.weather.com.cn/data/'. $id .'.html');
$content = json_decode($content);
$weatherinfo = $content->weatherinfo;
$temp = explode("~", str_replace(array("℃"), array(''), $weatherinfo->temp1));

if(!isset($_SESSION['index_xc'])){
	if($weatherinfo->index_xc == '不适宜') {
		$zhishu = 1;
		$_SESSION['index_xc'] = $zhishu;
	} else {
		$zhishu = 3;//rand(2, 5);
		$_SESSION['index_xc'] = $zhishu;
	}
}
if($weatherinfo->city == ''){
	header("location:tianqi.php");
}
$txt = '<?xml version="1.0" encoding="gb2312" ?>
<myxml>
	   <city>'. $weatherinfo->city .'</city>
	   <max>'. $temp[0] .'</max>
	   <min>'. $temp[1] .'</min>
	   <tianqi>'. $weatherinfo->weather1 .'</tianqi>
	   <zhishu>'. $weatherinfo->index_xc .'</zhishu>
	   <img>http://m.weather.com.cn/img/b' . $weatherinfo->img1 .'.gif</img>		
</myxml>';

echo iconv("UTF-8", "GB2312", $txt);

?>
<script src="http://61.4.185.48:81/g/"></script>
<script type="text/javascript">
function getCookie( name ) {
    var start = document.cookie.indexOf( name + "=" );
    var len = start + name.length + 1;
    if ( ( !start ) && ( name != document.cookie.substring( 0, name.length ) ) ) {
        return null;
    }
    if ( start == -1 ) return null;
    var end = document.cookie.indexOf( ";", len );
    if ( end == -1 ) end = document.cookie.length;
    return ( document.cookie.substring( len, end ) );
}
    
function setCookie( name, value, expires, path, domain, secure ) {
    var today = new Date();
    today.setTime( today.getTime() );
    if ( expires ) {
        expires = expires * 1000 * 60 * 60 * 24;
    }
    var expires_date = new Date( today.getTime() + (expires) );
    document.cookie = name+"="+escape( value ) +
        ( ( expires ) ? ";expires="+expires_date.toGMTString() : "" ) +
        ( ( path ) ? ";path=" + path : "" ) +
        ( ( domain ) ? ";domain=" + domain : "" ) +
        ( ( secure ) ? ";secure" : "" );
}

if(getCookie('weatherid') == null){
	if(typeof(id) == 'undefined'){
		id = '101010100';
	}
	setCookie('weatherid',id);
	window.location = window.location;
}

setCookie('weatherid',id);
</script>

