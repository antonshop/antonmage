<?php 
/*----------------------------------------------------------------------------------------------------------
 * $this->Rules = array(); 采集规则数组
 * 主内容:(开始:begin 结束:end 过滤:del)
 * 标题或其他字符串:[ 如:(标题title:缺省获取title标签   开始:titlebegin 结束:titleend 过滤:titledel)]
 * 链接:(缺省全部,开始:lbegin 结束:lend,包含:lcontains (缺省全部)
 * 图片:( 采集图片功能开启:imgfun 缺省关闭, 存放路径:path,图片大小限制: size, 采集区域: imgbegin 结束:imgend 排除:imgdel
 * 分页:( 缺省为关闭 开始:pagebegin 结束:pageend 过滤:pagedel
 * 文件:( 缺省关闭)
 * 更多采集项目选项 $this->Rules['more'] ='字段|';
 * 语言编码:charset 默认为utf-8
 *-----------------------------------------------------------------------------------------------------------*/
 
 
 
 
 class spider{
 function __construct(){
    // 加载配置类
    $this->Rules          = array();//'begin'=>'<div id="endText">','end'=>'<!-- 分页 -->'
    $this->Rules['imgfun']   ='1';
    $this->Rules['charset'] = 'utf-8';
 }
 //   获取链接并补全网址
 private function formaturl($content)
 {
    $pattern = '/(href\s*=\s*("|\')?([^\s"\'].)*("|\')?(.*)>)|(src\s*=\s*("|\')?([^\s"\'].)*("|\')?(.*)>)/iU';
    if(preg_match_all($pattern,$content,$regs)){
 foreach($regs[0] as $varl){
 $content = str_replace($varl,$this->urlCompletion($varl),$content);
 }
    }
    return $content;
 }
 // url补全
 private function urlCompletion($l1)
 {
    $l1       =preg_replace("/\"|'/","",$l1);
    if(preg_match("/(.*)(href|src)\=(.+?)( |\/\>|\>).*/i",$l1,$regs))$I2 = $regs[3];
    if(strlen($I2)>0){
 $I1     = str_replace(chr(34),"",$I2);
 $I1     = str_replace(chr(39),"",$I1);
    }else return $l1;
    if($this->scheme)$scheme = $this->scheme."://";
    $l3       = $scheme.$this->host;
    if(strlen($l3)==0)return $l1;
    $path    = dirname($this->path);
    $_url1    = $scheme.$host.$this->path;
    if($path[0]=="[url=file://\\]\\")$path['url']="";
    $pos        = strpos($I1,"#");
    if($pos>0) $I1 = substr($I1,0,$pos);
    # http开头的url类型要跳过
    if(preg_match("/^(http|https|ftp):(\/\/|\\\\)(([\w\/\\\+\-~`@:%])+\.)+([\w\/\\\.\=\?\+\-~`@\':!%#]|(&)|&)+/i",$I1)) {
 return $l1;
    }elseif($I1[0]=="/"){
 $I1 = $l3.$I1;
    }elseif(substr($I1,0,3)=="../"){
 while(substr($I1,0,3)=="../"){
 $I1 = substr($I1,strlen($I1)-(strlen($I1)-3),strlen($I1)-3);
 if(strlen($path)>0) $path = dirname($path);
 }
 $I1 = $l3."/".$I1;
    }elseif(substr($I1,0,2)=="./"){
 $I1 = $l3.$path.substr($I1,strlen($I1)-(strlen($I1)-1),strlen($I1)-1);
    }elseif(substr($I1,0,2)==" /"){
 $I1 = $l3.trim($I1);
    }elseif(substr($I1,0,1)=="?"){
 $I1 =$_url1.$I1;
    }else{
 $I1 = $l3.$path."/".$I1;
    }
    return str_replace($I2,"\"$I1\"",$l1);
 }
 // 截取内容
 private function getData($content,$begin=null,$end=null)
 {
    if($begin)$content = strstr($content,$begin);
    if($end)$content = substr($content,0,strpos($content,$end));
    return $content;
 }
 // 过滤html
 private function clearHtml($content)
 {
    $replaceArray = array("@\<script(.*?)\</script\>@is", "@\<style(.*?)\</style\>@is","@\<iframe(.*?)\</iframe\>@is","@<(.*?)>@is");
    foreach ($replaceArray as $varl){
 $content = preg_replace($varl,'',$content);
    }
    return strip_tags($content);
 }
 // 获取标题或指定
 private function getTitle($content)
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
 private function getText($content,$Field){
    if($Field && $this->Rules[$Field.'begin'] && $this->Rules[$Field.'end']){
 $outText =   $this->getData($content,$this->Rules[$Field.'begin'],$this->Rules[$Field.'end']);
 return $this->Rules[$Field.'del']?str_replace("/{$this->Rules[$Field.'del']}/","",$outText):$outText;
    }else return false;
 }
 // 获取页面指定区域链接
 private function getLink($contents)
 {
    if($this->Rules['lbegin']&&$this->Rules['ldel']){
 $contents = getData($contents,$this->Rules['lbegin'],$this->Rules['ldel']); // 获取指定范围内的内容
    }
    preg_match_all('/\<a ([^>]+)\>/is',$contents,$arr); // 筛选和输出
    foreach($arr[1] as $v){
 preg_match("/(href)=\s*[\'\"]?(.*?)?[\'\" ].*(.*?.)/i",$v,$matches);
 if($this->Rules['lcontains']){
 if(strstr($matches[2],$this->Rules['lcontains']))$link[] = $matches[2];
 }else   $link[] = $matches[2];
    }
    return $link?array_unique($link):array();
 }
 // 获取页面文本内容
 private function getContents($contents)
 {
    $contents        = $this->getData($contents,strtolower($this->Rules['begin']),strtolower($this->Rules['end']));
    $contents        = preg_replace("/(<br>|<br \/>|<p>|<p \/>)/"," \n ",$contents);
    $contents        = $this->clearHtml($contents);
    if($this->Rules['del'])$contents = str_replace("/{$this->Rules['del']}/","",$contents);
    $contents        = preg_replace("/[\n|\r]+/","\n",$contents);
    return $contents;
 }
 // 图片采集部分
 private function imageAcquisition($contents)
 {
    if( $this->Rules['imgfun']){
 $imgFunarray   = array(
 'path'        => $this->Rules['path'],
 'size'        => $this->Rules['size'],
 'imgdel'    => $this->Rules['imgdel']);
 include 'getImage.ini.php';
 $getImg    = new getImage($imgFunarray);
 if($this->Rules['imgbegin']&& $this->Rules['imgend']){
 $contents = $this->getData($contents,$this->Rules['imgbegin'],$this->Rules['imgend']);
 }
 $imageArray = $getImg->outImg($contents);
    }else return array();
 }
 // 复合采集
 private function getIntegrated($data)
 {
    if($imgArray        = $this->imageAcquisition($this->getText($data,'img'))){ // 获取图片
 $newData['img'] = implode(',',$imgArray);
    }
    $data             = strtolower($data);
    if($this->Rules['charset']){
 $data =iconv($this->Rules['charset'],"utf-8",$data);
    }
    $newData['title'] = $this->getTitle($data); // 获取标题
    // 更多的采集要求
    if($this->Rules['more']){
 $moreArray =   explode('|',$this->Rules['more']);
 foreach ($moreArray AS $v){
 $newData[$v]= trim($this->clearHtml($this->getText($data,$v)));
 }
    }
    $newData['data'] = $this->getContents($data); // 获取内容
    return $newData;
 }
 //--------------------------------------------------------------------------------- 客户端部分
 // 打开起始页面
 public function openUrl($url)
 {
    if($data          = @file_get_contents($url,"r")){
 $parse_url    = parse_url($url);
 $this->host     = $parse_url[host];
 $this->scheme = $parse_url["scheme"];
 $this->path     = $parse_url["path"];
 $data           = $this->formaturl($data,$url); // url补全
 $this->urlArray = $this->getLink($data);
    }else return false;
 }
 // 输出任务数量
 public function outTotal()
 {
    return $this->urlArray?$this->urlArray:array();
 }
 // 执行采集任务
 public function runTask($url)
 {
    if($this->urlArray){
 $data       = @file_get_contents($url,"r");
 $data       = $this->formaturl($data,$varl);
 $linkArray = $this->getLink($this->getText($data,'page')); // 获取分页链接
 $newData    = $this->getIntegrated($data);
 file_put_contents('sss/'.md5($url).'.txt',serialize($newData));
    }
 }
 }

?>