<?php
    $ommit = $_GET['o'];
    $ti = trim($_GET['ti'], "\t\n\r\0\x0B ");
    $src = $_GET['src'];
    $start_price = $_GET['start_price'];
    $end_price = $_GET['end_price'];
    if (strlen($start_price) == 0)$start_price = 0.0;
    if (strlen($end_price) == 0)$end_price = 0.0;
    if ($start_price > $end_price)$start_price ^= $end_price ^= $start_price ^= $end_price;

    if (strlen($ti) == 0)$ti="连衣裙";

    $tabidx = 0;
    if (strcmp($src, "taobao.com") == 0)
	$tabidx = 0;
    elseif(strcmp($src, "jd.com") == 0)
	$tabidx = 1;
    elseif(strcmp($src, "yhd.com") == 0)
	$tabidx = 2;
    elseif(strcmp($src, "suning.com") == 0)
	$tabidx = 3;
    elseif(strcmp($src, "gome.com") == 0)
	$tabidx = 4;
    elseif(strcmp($src, "dangdang.com") == 0)
	$tabidx = 5;
    elseif(strcmp($src, "tmall.com") == 0)
	$tabidx = 6;
    else $tabidx = 0;

///////////////query ommit
if (strlen($ommit) > 0)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "localhost:6337/search?query=".urlencode($ti)."^");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);      
    //curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1000);    
    $output = curl_exec($ch);    
//    echo $output."\n";
//    echo "http://localhost:6337/search?query=".$ti."^";

    $json = @json_decode($output, true);
    $tks = "";
    if (array_key_exists("results", $json) && count($json['results'])>0 && array_key_exists("token",  $json['results'][0]))
    {
        $i = 0;
        foreach($json['results'][0]['token'] as $token )
	    if (strlen($token) > 3 && $i < 10){
                $tks = $tks." ".$token;
                $i = $i + 1;
            }
    }
    if (strlen($tks) > 0)
	$ti = $tks;
    curl_close($ch);
}

?>
<html>
<head>
<title>帮你比 -- 综合电商比价平台，让电商的价格更加透明！</title>
<link rel="shortcut icon" type="image/x-icon" href="bi_logo.ico" media="screen" /> 
<meta http-equiv="Content-Language" content="en-gb">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="jquery-ui.css">
<style>
    html {
        font-size:10px;
    }

    .iframetab {
        width:100%;
        height:auto;
        border:0px;
        margin:0px;
        background: url("iframeno.png") no-repeat top;
    }

    .ui-tabs-panel {
        padding:5px !important;
    }
	input,img{vertical-align:middle;}

</style>
<style>
.mytable {border-collapse:collapse;border:solid #6AA70B;border-width:0px 1 1 1px;width:30%;position:absolute;top:43px;float:center;z-index:999;left:37%;}
.mytable ul {margin:0px;padding:0px}
.mytable ul li {margin:0px;padding:5px;list-style:none;border-bottom:#6AA70B 1px dotted ;font-family: “Verdana,宋体”;font-size: 12px;color:#008000;text-align:left;height:30px; line-height:30px;}
.mytable ul li a{ color:#333; text-decoration:none; display:block; width:100%; height:30px; }
.mytable ul li a:hover { color:#FF6600; text-decoration:none; display:block; width:100%; height:30px; }
.mytable ul li.t1 {background-color:#EEEEEE;}/* 第一行的背景色 */
.mytable ul li.t2{background-color:#FFFFFF;}/* 第二行的背景色 */
.mytable ul li.t3 {background-color:#E7E6E2;}/* 鼠标经过时的背景色 */
</style>
<link rel="stylesheet" href="sorts.css">
<script src="jquery-1.10.2.js"></script>
<script src="jquery-ui.js"></script>
<script src="favor.js"></script>
<!--<script src="baidu.js"></script>-->
<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?8cfab82e21f64b64f98602742e52e3de";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>

<script language="javascript" type="text/javascript">

function loadTabFrame(tab, url) {

    if ($(tab).find("iframe").length == 0) {
        var html = [];
        html.push('<div class="tabIframeWrapper">');
        html.push('<iframe class="iframetab" src="' + url + '">Load Failed?</iframe>');
        html.push('</div>');
        $(tab).append(html.join(""));
        $(tab).find("iframe").height($(window).height()-80);
	return;
    }
	
//	alert(decodeURI($(tab+" iframe")[0].src));alert(decodeURI(url));
	if (decodeURI($(tab+" iframe")[0].src) == decodeURI(url));else
		$(tab+" iframe")[0].src = url;
    //$(tabs).tabs( "refresh" );
    return false;
} 

function merge_url_param(a)
{
    var url = a.attr("rel");
    if (typeof a.attr("sales-url") !== typeof undefined && a.attr("sales-url").length > 0)
	url += "&" + a.attr("sales-url");
    else if (typeof a.attr("cmt-url") !== typeof undefined && a.attr("cmt-url").length > 0)
	url += "&" + a.attr("cmt-url");
    else if (typeof a.attr("uptm-url") !== typeof undefined && a.attr("uptm-url").length > 0)
	url += "&" + a.attr("uptm-url");
    return url;
}

function reload_all_frame()
{
	loadTabFrame("#tabs-1", merge_url_param($("a.tabref[href='#tabs-1']")));
	loadTabFrame("#tabs-2", merge_url_param($("a.tabref[href='#tabs-2']")));
	loadTabFrame("#tabs-3", merge_url_param($("a.tabref[href='#tabs-3']")));
	loadTabFrame("#tabs-4", merge_url_param($("a.tabref[href='#tabs-4']")));
	loadTabFrame("#tabs-5", merge_url_param($("a.tabref[href='#tabs-5']")));
	loadTabFrame("#tabs-6", merge_url_param($("a.tabref[href='#tabs-6']")));
	loadTabFrame("#tabs-7", merge_url_param($("a.tabref[href='#tabs-7']")));
}

function search(){
	$("a.tabref[href='#tabs-1']").attr("rel", "http://s.taobao.com/search?spm=1.7274553.1997520241-1.10.0EP5oG&refpid=430145_1006&style=grid&tab=all&q=" +　encodeURI($("#query").val()));
		
	$("a.tabref[href='#tabs-2']").attr("rel", "http://search.jd.com/Search?enc=utf-8&suggest=8&keyword=" +　encodeURI($("#query").val()));
		
	$("a.tabref[href='#tabs-3']").attr("rel", "http://search.yhd.com/c0-0/k" +　encodeURI($("#query").val()) + "/1/?tp=1.1.12.0.18.KoIciCG-10-CjjKf");
		
	$("a.tabref[href='#tabs-4']").attr("rel", "http://search.suning.com/"+encodeURI($("#query").val())+"/cityId=9173");
		
	$("a.tabref[href='#tabs-5']").attr("rel", "http://www.gome.com.cn/search?question=" +　encodeURI($("#query").val()));
		
	$("a.tabref[href='#tabs-6']").attr("rel", "http://search.dangdang.com/?key=" +　encodeURI($("#query").val()));
		
	$("a.tabref[href='#tabs-7']").attr("rel", "http://list.tmall.com/search_product.htm?q=" +　$("#query").val());

	reload_all_frame();
	var beginTab = $("#tabs ul li:eq(" + $tabs.tabs('option', 'active')+ ")").find("a");
	loadTabFrame($(beginTab).attr("href"), merge_url_param($(beginTab)));
}

function shift_sort(a){
	if (a.attr("title") == a.text())return true;

	$(".sorts .sort .active").text($(".sorts .sort .active").attr("txt"));
	$(".sorts .sort .active").removeClass("active");

	a.addClass("active");
	a.text(a.attr("title"));

	return false;
}

$(document).ready(function() {
        var $tabs = $('#tabs').tabs();
	$("a.tabref").click(function() {
		$("input[name='src']").val($(this).attr("site"));
		loadTabFrame($(this).attr("href"), merge_url_param($(this)));
	});

        $(tabs).find( ".ui-tabs-nav" ).sortable({
		axis: "x",
      		stop: function() {
        		$(tabs).tabs( "refresh" );
      		}	
    	});
				
	reload_all_frame();
	var beginTab = $($("#tabs > ul > li").get(<?php echo $tabidx;?>)).find("a");
	//var beginTab = $("#tabs ul li:eq(" + $tabs.tabs('option', 'active')+ ")").find("a");
	loadTabFrame($(beginTab).attr("href"),$(beginTab).attr("rel"));
        $(tabs).tabs("option", "active", <?php echo $tabidx;?>);
	
	

	$("[trace=sortSaleDesc]").click(function(){
		if (shift_sort($(this)))return;

		$("a.tabref").removeAttr("uptm-url");
		$("a.tabref").removeAttr("uptm-url");
		$("a.tabref[href='#tabs-1']").attr("sales-url", "sort=sale-desc");
		$("a.tabref[href='#tabs-2']").attr("sales-url", "psort=3");
		$("a.tabref[href='#tabs-3']").attr("sales-url", "#page=1&sort=2");
		//$("a.tabref[href='#tabs-4']").attr("sales-url", "sort=sddale-desc");
		$("a.tabref[href='#tabs-5']").attr("sales-url", "sort=10");
		$("a.tabref[href='#tabs-6']").attr("sales-url", "sort_type=sort_sale_amt_desc");
		$("a.tabref[href='#tabs-7']").attr("sales-url", "sort=d");
		reload_all_frame();
		var beginTab = $("#tabs ul li:eq(" + $tabs.tabs('option', 'active')+ ")").find("a");
		loadTabFrame($(beginTab).attr("href"), merge_url_param($(beginTab)));
	        $(tabs).tabs( "refresh" );
	});

	$("[trace=sortCmtDesc]").click(function(){
		if (shift_sort($(this)))return;

		$("a.tabref").removeAttr("sales-url");
		$("a.tabref").removeAttr("uptm-url");
		$("a.tabref[href='#tabs-1']").attr("cmt-url", "sort=renqi-desc");
		$("a.tabref[href='#tabs-2']").attr("cmt-url", "psort=4");
		$("a.tabref[href='#tabs-3']").attr("cmt-url", "#page=1&sort=5");
		//$("a.tabref[href='#tabs-4']").attr("sales-url", "sort=sddale-desc");
		$("a.tabref[href='#tabs-5']").attr("cmt-url", "sort=50");
		$("a.tabref[href='#tabs-6']").attr("cmt-url", "sort_type=sort_score_desc");
		$("a.tabref[href='#tabs-7']").attr("cmt-url", "sort=rq");
		reload_all_frame();
		var beginTab = $("#tabs ul li:eq(" + $tabs.tabs('option', 'active')+ ")").find("a");
		loadTabFrame($(beginTab).attr("href"), merge_url_param($(beginTab)));
	        $(tabs).tabs( "refresh" );
	});

	$("[trace=sortUptmDesc]").click(function(){
		if (shift_sort($(this)))return;

                $("a.tabref").removeAttr("sales-url");
                $("a.tabref").removeAttr("cmt-url");
		$("a.tabref[href='#tabs-1']").attr("uptm-url", "auction_tag%5B%5D=1154");
		$("a.tabref[href='#tabs-2']").attr("uptm-url", "psort=5");
		$("a.tabref[href='#tabs-3']").attr("uptm-url", "#page=1&sort=6");
		//$("a.tabref[href='#tabs-4']").attr("sales-url", "sort=sddale-desc");
		$("a.tabref[href='#tabs-5']").attr("uptm-url", "sort=30");
		$("a.tabref[href='#tabs-6']").attr("uptm-url", "sort_type=sort_last_changed_date_desc");
		$("a.tabref[href='#tabs-7']").attr("uptm-url", "sort=new");
		reload_all_frame();
		var beginTab = $("#tabs ul li:eq(" + $tabs.tabs('option', 'active')+ ")").find("a");
		loadTabFrame($(beginTab).attr("href"), merge_url_param($(beginTab)));
	        $(tabs).tabs( "refresh" );
	});

	$("[trace=sortDefault]").click(function(){
		if (shift_sort($(this)))return;

                $("a.tabref").removeAttr("sales-url");
                $("a.tabref").removeAttr("cmt-url");
                $("a.tabref").removeAttr("uptm-url");
		reload_all_frame();
		var beginTab = $("#tabs ul li:eq(" + $tabs.tabs('option', 'active')+ ")").find("a");
		loadTabFrame($(beginTab).attr("href"), merge_url_param($(beginTab)));
	        $(tabs).tabs( "refresh" );
	});

	$("button").click(function(){
		$("#priceFilter").submit();
	});

});
</script>
<script src="qs.js"></script>
</head>

<body>
<div>
<p> </p>
<p >
<div style="height:35px;position:absolute;right:0%;" >
	<a  href="#" onclick="AddFavorite(window.location,document.title)" > 收藏本站</a><br>
	<a  href="#" onclick="SetHome(this, 'http://www.hello987.com')">设为首页</a>
</div>
	<img src="bangnibi.png"  style="position:relative;left:30%;width:7%;height:35px"/>
	<input type="text" id="query" style="position:absolute;left:37%;width:30%;height:35px;border:2px solid #ff0000;" value="<?php echo $ti;?>" placeholder="请输入要搜索的词" lang="zh-CN" autocomplete="off" aria-haspopup="true" aria-combobox="list" role="combobox"/>
	<!-- <img id="spSearch" src="search_btn.jpg" style="height:35px;width:80px;position:absolute;left:67%;"  onmouseover="this.style.cursor='pointer';this.style.cursor='hand'" onmouseout="this.style.cursor='default'" />
-->
<p>
<div id="suggestions" class="mytable">
</div>
</p>
</p>

<div class="sort-inner">
    <ul class="sorts">
        <li class="sort">
            <a class="J_Ajax link active first" data-url="sortbar" data-key="sort" data-value="default" data-anchor="J_relative" trace="sortDefault" title="综合排序" href="#" txt="综合">综合</a>
       </li>
       <li class="sort">
            <a class="J_Ajax link  " data-url="sortbar" data-key="sort" data-value="sale-desc" data-anchor="J_relative" trace="sortSaleDesc" title="销量从高到低" href="#" txt="销量">销量</a>
       </li>
       <li class="sort">
            <a class="J_Ajax link  " data-url="sortbar" data-key="sort" data-value="renqi-desc" data-anchor="J_relative" trace="sortCmtDesc" title="评论从高到低" href="#" txt="评论" >评论</a>
       </li>
       <li class="sort">
            <a class="J_Ajax link  " data-url="sortbar" data-key="sort" data-value="renqi-desc" data-anchor="J_relative" trace="sortUptmDesc" title="上架时间从近到远" href="#" txt="新品" >新品</a>
       </li><!--
       <li class="sort has-droplist J_LaterHover" data-hover-cls="has-droplist-hover" tabindex="0" data-leave-timer="417">
           <div class="trigger">
               <div class="link  ">
                   <span class="text" title="价格从低到高">价格</span>
                   <span class="icon icon-btn-arrow-2-h"></span>
               </div>
           </div>

           <ul class="droplist">
               <li class="sort">
                   <a class="J_Ajax link" tabindex="0" data-url="sortbar" data-key="sort" data-value="price-asc" data-anchor="J_relative" trace="sortPrice" href="#" >价格从低到高</a>
               </li>
          
               <li class="sort">
                   <a class="J_Ajax link" tabindex="0" data-url="sortbar" data-key="sort" data-value="price-desc" data-anchor="J_relative" trace="sortPrice" href="#">价格从高到低</a>
               </li>
          </ul>
      </li> -->
    </ul>
<form id="priceFilter" action="search.php" method="get">
<input name="ti" value="<?php echo $ti;?>" style="display:none; ">
<input name="src" value="<?php echo $src;?>" style="display:none; ">
<div class="prices">
  <div class="inputs J_LaterHover" data-hover-cls="inputs-hover" data-leave-timer="87864">
    <div class="inner">
      <ul class="items g-clearfix">
        <li class="item">
          <input autocomplete="off" class="J_SortbarPriceInput input" placeholder="¥" type="text" name="start_price" value="<?php if($start_price>0)echo $start_price; ?>" aria-label="价格最小值">
        </li>
        <li class="sep">-</li>
        <li class="item">
          <input autocomplete="off" class="J_SortbarPriceInput input" placeholder="¥" type="text" name="end_price" value="<?php if($end_price>0)echo $end_price; ?>" aria-label="价格最大值">
        </li>
        <li class="submit">
          <button class="J_SortbarPriceSubmit btn" type="button">确定</button>
        </li>
      </ul>
    </div>
  </div>
</div>
</form>


</div>

</div>
   
   <div id="tabs">
            <ul>
                <li><a class="tabref" href="#tabs-1" rel="http://s.taobao.com/search?spm=1.7274553.1997520241-1.10.0EP5oG&refpid=430145_1006&style=grid&tab=all&q=<?php echo $ti;if($start_price+$end_price>0)echo "&filter=reserve_price%5B".$start_price."%2C".$end_price."%5D"; ?>" site="taobao.com"><img src="http://www.taobao.com/favicon.ico" > 淘宝网</a></li>
                <li><a class="tabref" href="#tabs-2" rel="http://search.jd.com/Search?keyword=<?php echo $ti; if($start_price+$end_price>0)echo "&ev=exprice_".$start_price."-".$end_price."%40"?>&enc=utf-8&suggest=8" site="jd.com"><img src="http://www.jd.com/favicon.ico" width="16px" height="16"> 京东商城</a></li>
                <li><a class="tabref" href="#tabs-3" rel="http://search.yhd.com/c0-0-0/b/<?php if($start_price+$end_price>0)echo "a-s1-v4-p1-price".$start_price.",".$end_price.",23-d0-f0-m1-rt0-pid-mid0-"; ?>k<?php echo $ti;?>/1/?tp=1.1.12.0.18.KoIciCG-10-CjjKf" site="yhd.com"><img src="http://www.yhd.com/favicon.ico" > 1号店</a></li>
		<li><a class="tabref" href="#tabs-4" rel="http://search.suning.com/<?php echo $ti;?>/cityId=9173<?php if($start_price+$end_price>0)echo "&cf=price:".$start_price."-".$end_price; ?>" site="suning.com"><img src="http://www.suning.com/favicon.ico" > 苏宁易购</a></li>
                <li><a class="tabref" href="#tabs-5" rel="http://www.gome.com.cn/search?question=<?php echo $ti; if($start_price+$end_price>0)echo "&price=".$start_price."x".$end_price; ?>" site="gome.com"><img src="http://www.gome.com.cn/favicon.ico" > 国美商城</a></li>
                <li><a class="tabref" href="#tabs-6" rel="http://search.dangdang.com/?key=<?php echo $ti; if($start_price+$end_price>0)echo "&lowp=".$start_price."&highp=".$end_price;?>" site="dangdang.com"><img src="http://www.dangdang.com/favicon.ico" > 当当网</a></li>
                <li><a class="tabref" href="#tabs-7" rel="http://list.tmall.com/search_product.htm?q=<?php echo $ti; if($start_price+$end_price>0)echo "&start_price=".$start_price."&end_price=".$end_price;?>" site="tmall.com"><img src="http://www.tmall.com/favicon.ico" > 天猫商城</a></li>
            </ul>
            <div id="tabs-1" class="tabMain">
            </div>

            <div id="tabs-2">
            </div>

            <div id="tabs-3">
            </div>

            <div id="tabs-4">
            </div>

            <div id="tabs-5">
            </div>

            <div id="tabs-6">
            </div>

            <div id="tabs-7">
            </div>
        </div>

<p align="center">帮你比 -- 沪ICP备15020472号-1</p>
<p align="center"> 版权所有 © 上海安初信息科技有限公司</p>
<p align="center">
<script language="javascript" type="text/javascript" src="http://js.users.51.la/17785954.js"></script>
<noscript><a href="http://www.51.la/?17785954" target="_blank"><img alt="&#x6211;&#x8981;&#x5566;&#x514D;&#x8D39;&#x7EDF;&#x8BA1;" src="http://img.users.51.la/17785954.asp" style="border:none" /></a></noscript>
</p>
</body>

