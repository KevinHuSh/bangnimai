<?php
    $ti = $_GET['ti'];
    $src = $_GET['src'];
    if (strlen($ti) == 0)$ti="连衣裙";

    if (strcmp($src, "taobao.com") == 0)
	$src = 0;
    elseif(strcmp($src, "jd.com") == 0)
	$src = 1;
    elseif(strcmp($src, "yhd.com") == 0)
	$src = 2;
    elseif(strcmp($src, "suning.com") == 0)
	$src = 3;
    elseif(strcmp($src, "gome.com") == 0)
	$src = 4;
    elseif(strcmp($src, "dangdang.com") == 0)
	$src = 5;
    elseif(strcmp($src, "tmall.com") == 0)
	$src = 6;
    else $src = 0;
?>
<html>
<head>

<title>帮你比 -- 综合电商比价平台，让电商的价格更加透明！</title>
<link rel="shortcut icon" type="image/x-icon" href="bi_logo.ico" media="screen" /> 
    <meta http-equiv="Content-Language" content="en-gb">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="jquery-ui.css">
  <script src="jquery-1.10.2.js"></script>
  <script src="jquery-ui.js"></script>

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
function reload_all_frame()
{
	loadTabFrame("#tabs-1", $("a.tabref[href='#tabs-1']").attr("rel"));
	loadTabFrame("#tabs-2", $("a.tabref[href='#tabs-2']").attr("rel"));
	loadTabFrame("#tabs-3", $("a.tabref[href='#tabs-3']").attr("rel"));
	loadTabFrame("#tabs-4", $("a.tabref[href='#tabs-4']").attr("rel"));
	loadTabFrame("#tabs-5", $("a.tabref[href='#tabs-5']").attr("rel"));
	loadTabFrame("#tabs-6", $("a.tabref[href='#tabs-6']").attr("rel"));
	loadTabFrame("#tabs-7", $("a.tabref[href='#tabs-7']").attr("rel"));
}

$(document).ready(function() {
        var $tabs = $('#tabs').tabs();
	$("a.tabref").click(function() {
		loadTabFrame($(this).attr("href"),$(this).attr("rel"));
	});

        $(tabs).find( ".ui-tabs-nav" ).sortable({
		axis: "x",
      		stop: function() {
        		$(tabs).tabs( "refresh" );
      		}	
    	});
				
	reload_all_frame();
	var beginTab = $($("#tabs > ul > li").get(<?php echo $src;?>)).find("a");
	//var beginTab = $("#tabs ul li:eq(" + $tabs.tabs('option', 'active')+ ")").find("a");
	loadTabFrame($(beginTab).attr("href"),$(beginTab).attr("rel"));
        $(tabs).tabs("option", "active", <?php echo $src;?>);
	
	$('#spSearch').click(function() {
		$("a.tabref[href='#tabs-1']").attr("rel", "http://s.taobao.com/search?spm=1.7274553.1997520241-1.10.0EP5oG&refpid=430145_1006&style=grid&tab=all&q=" +　encodeURI($("#query").val()));
		
		$("a.tabref[href='#tabs-2']").attr("rel", "http://search.jd.com/Search?enc=utf-8&suggest=8&keyword=" +　encodeURI($("#query").val()));
		
		$("a.tabref[href='#tabs-3']").attr("rel", "http://search.yhd.com/c0-0/k" +　encodeURI($("#query").val()) + "/1/?tp=1.1.12.0.18.KoIciCG-10-CjjKf");
		
		$("a.tabref[href='#tabs-4']").attr("rel", "http://search.suning.com/"+encodeURI($("#query").val())+"/cityId=9173");
		
		$("a.tabref[href='#tabs-5']").attr("rel", "http://www.gome.com.cn/search?question=" +　encodeURI($("#query").val()));
		
		$("a.tabref[href='#tabs-6']").attr("rel", "http://search.dangdang.com/?key=" +　encodeURI($("#query").val()));
		
		$("a.tabref[href='#tabs-7']").attr("rel", "http://list.tmall.com/search_product.htm?q=" +　$("#query").val());
		reload_all_frame();
		//$("a.tabref[href='#tabs-7']").attr("rel", "http://list.tmall.com/search_product.htm?q=" +　encodeURIComponent_GBK($("#query").val()));
		
		//alert($("a.tabref[href='#tabs-1']").attr("rel"));
		beginTab = $("#tabs ul li:eq(" + $tabs.tabs('option', 'active')+ ")").find("a");
		loadTabFrame($(beginTab).attr("href"),$(beginTab).attr("rel"));
	});

	/////////////////////////////////////////////////////////////////////// Query Suggestion /////////////////////////////////////////////////////////////
	var typingTimer;                //timer identifier
	var doneTypingInterval = 100;  //time in ms, 5 second for exam

	$('#query').focus(function(){
		$(this).keyup();
	});
	$(document).bind("click",function(e){
		var target  = $(e.target);
		if(target.closest("#query,#suggestions").length == 0)$('#suggestions').hide();
	});
	$('#query').keydown(function(e){
		clearTimeout(typingTimer);
	});
	$('#query').keyup(function(e){
		if (e && e.keyCode == 13){
			$("#suggestions").hide();
			$('#spSearch').click();
			return;
		}
		if (e && e.keyCode == 40){
			$("#suggestions").focus();	
			return;
		}
		var input = $(this).val().trim();
		//alert(input);
                if (input.length > 0){
			if (typingTimer) clearTimeout(typingTimer);                 // Clear if already set     
			typingTimer = setTimeout(function(){
		$.getJSON("http://120.25.239.154:6339/qs?key="+input+"&cb=?",function(data){
//        		alert(data[0]);
			var sugg = "<ul> ";
			for (var i=0;i<data.length && i<10;i++){
				var tm = data[i].term;
				if (tm == input)continue;
				var idx = tm.indexOf(input);
				if (idx >= 0)
					tm = (idx>0 ? "<b>" + tm.substring(0, idx) + "</b>": "") + tm.substring(idx, idx + input.length) + (idx + input.length < tm.length? "<b>" + tm.substring(idx + input.length)+"</b>":"");
				else tm = "<b>" + tm + "</b>";
				sugg += "<li term=\""+data[i].term+"\"><a>" + tm + "</a></li>";
			}
			sugg += "</ul>";
			$("#suggestions").html(sugg);

			var Ptr = $("#suggestions li");
			for (var i=1;i<Ptr.length+1;i++) 
				$(Ptr[i-1]).attr("class", "t2");
				//$(Ptr[i-1]).attr("class", (i%2!=0?"t1":"t2"));
			for(var i=0;i<Ptr.length;i++) {
				$(Ptr[i]).mouseover(function(){
					$(this).attr("tmpClass", $(this).attr("class"));
					$(this).attr("class", "t3");
				});
				$(Ptr[i]).mouseout(function(){
					$(this).attr("class", $(this).attr("tmpClass"));
				});
			}
			if (sugg.length > 10)	$("#suggestions").show();

			$(".mytable ul li").click(function(){
			    $('#query').val($(this).attr("term"));
			    $('#spSearch').click();
			    $("#suggestions").hide();
	//			alert($(this).attr("term"));
			});
    		});
		}, doneTypingInterval);
		}else $("#suggestions").hide();
	});
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

});
	
</script>
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
</head>

<body>
<div>
<p> </p>
<p >
	<img src="bangnibi.png"  style="position:relative;left:30%;width:7%;height:35px"/>
	<input type="text" id="query" style="position:relative;left:30%;width:30%;height:35px" value="<?php echo $ti;?>" placeholder="请输入要搜索的词" lang="zh-CN" autocomplete="off" aria-haspopup="true" aria-combobox="list" role="combobox"/>
	<img id="spSearch" src="search.png" style="height:35px;width:7%;position:absolute;left:67%;"  onmouseover="this.style.cursor='pointer';this.style.cursor='hand'" onmouseout="this.style.cursor='default'" />
<p>
<div id="suggestions" class="mytable">
</div>
</p>
</p>
<p> </p>

</div>
   
   <div id="tabs">
            <ul>
                <li><a class="tabref" href="#tabs-1" rel="http://s.taobao.com/search?spm=1.7274553.1997520241-1.10.0EP5oG&refpid=430145_1006&style=grid&tab=all&q=<?php echo $ti;?>"><img src="http://www.taobao.com/favicon.ico" > 淘宝网</a></li>
                <li><a class="tabref" href="#tabs-2" rel="http://search.jd.com/Search?keyword=<?php echo $ti;?>&enc=utf-8&suggest=8"><img src="http://www.jd.com/favicon.ico" width="16px" height="16"> 京东商城</a></li>
                <li><a class="tabref" href="#tabs-3" rel="http://search.yhd.com/c0-0/k<?php echo $ti;?>/1/?tp=1.1.12.0.18.KoIciCG-10-CjjKf"><img src="http://www.yhd.com/favicon.ico" > 1号店</a></li>
				<li><a class="tabref" href="#tabs-4" rel="http://search.suning.com/<?php echo $ti;?>/cityId=9173"><img src="http://www.suning.com/favicon.ico" > 苏宁</a></li>
                <li><a class="tabref" href="#tabs-5" rel="http://www.gome.com.cn/search?question=<?php echo $ti;?>"><img src="http://www.gome.com.cn/favicon.ico" > 国美商城</a></li>
                <li><a class="tabref" href="#tabs-6" rel="http://search.dangdang.com/?key=<?php echo $ti;?>"><img src="http://www.dangdang.com/favicon.ico" > 当当网</a></li>
                <li><a class="tabref" href="#tabs-7" rel="http://list.tmall.com/search_product.htm?q=<?php echo $ti;?>"><img src="http://www.tmall.com/favicon.ico" > 天猫商城</a></li>
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
</html>
