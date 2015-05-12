<script language="javascript" type="text/javascript" src="http://js.users.51.la/17785954.js"></script>
<noscript><a href="http://www.51.la/?17785954" target="_blank"><img alt="&#x6211;&#x8981;&#x5566;&#x514D;&#x8D39;&#x7EDF;&#x8BA1;" src="http://img.users.51.la/17785954.asp" style="border:none" /></a></noscript>

<?php

$debug = 0;
if ($debug == 1)echo "ssssss";


//echo "<script>alert('http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."');</script>";

$param = array();
$param["to"] = urldecode(substr($_SERVER['QUERY_STRING'],3));

if ($debug == 1)echo "<br>TO: ". $param["to"]."<br>";


function getDomain($url){
    $s = stripos($url, "http://");
	if ($s === false){
	    $s = stripos($url, "https://");
		if ($s != false) $s = $s + strlen("https://");
	}
	else $s = $s + strlen("http://");
	if ($s === false) $s = 0;
	
	$e = stripos(substr($url, $s), "/");
	if ($e === false)$e = stripos(substr($url, $s), "?");
	if ($e === false) $e = strlen(substr($url, $s));
	
	return substr($url, $s, $e);
}

$to = getDomain($param["to"]);
if ($debug == 1)
echo "<br>domain: ". $to."<br>";


include'cps.php';

//if (stripos("kk".$to, "www.vip.com") != false && stripos($param['to'], ".htm") != false){
//    $to = "www.vipshop.com";
	//echo "<script>alert('".$param['to']."');</script>";
//}

if ($redirect[$to] != false){

    $s = stripos($param['to'], "/?");
	if ($s != false && stripos($param['to'], "/?w=") != false)
	    $s = false;
	if ($s === false){
	    $s = stripos($param['to'], ".html?");
		if ($s != false) $s = $s + 5;
	}
	if ($s === false){
	    $s = stripos($param['to'], ".shtml?");
		if ($s != false) $s = $s + 6;
	}
	if ($s === false){
	    $s = stripos($param['to'], ".html#");
		if ($s != false) $s = $s + 5;
	}
	if ($s === false){
	    $s = stripos($param['to'], ".htm?");
		if ($s != false) $s = $s + 4;
	}
	if ($s === false){
	    $s = stripos($param['to'], "/welcome?");
	}
	if ($s === false)
	    $s = strlen($param['to']);
    $param['to'] = substr($param['to'], 0, $s);
	
	if ( 1 ===0 
	//||  stripos($param['to'], "www.jd.com") != false 
	//|| stripos($param['to'], "www.yhd.com") != false  
	//|| stripos($param['to'], "www.vip.com") != false 
	//|| stripos($param['to'], "www.meilishuo.com") != false 
	//|| stripos($param['to'], "www.nuomi.com") != false 
	|| stripos($param['to'], "www.paipai.com") != false 
	|| stripos($param['to'], "www.ppdai.com") != false 
	)
	    $param['to'] = $redirect[$to];
	else
    	$param['to'] = $redirect[$to] . urlencode($param['to']) ;//"http%3A%2F%2F" . $to;
}

if ($debug == 1)echo $param["to"]."<br>KKKKKKKKKKKKKKKKK";
if ($debug == 1)
echo "<script>alert('".$param['to']."');</script>";
echo "<script>location.href='http://120.25.239.154/tuiguang.php?to=".urlencode($param["to"])."';</script>";
exit;

?>
