function group_img_auto_fitin(groupid)
{
	$("#"+groupid+" .left-img > img").load(function() {
		if ($( this ).height()*1.0/$( this ).width() < 381.0/319.0){
			$( this ).css("height", "100%");
			$( this ).css("width", "auto");
		}
		else{
			$( this ).css("width", "100%");
			$( this ).css("height", "auto");
		}
	});

	$("#"+groupid + " .right-img > img").load(function() {
		if ($( this ).height()*1.0/$( this ).width() < 381.0/2.0/319.0){
			$( this ).css("width", "100%");
			$( this ).css("height", "auto");
		}
		else{
			$( this ).css("height", "50%");
			$( this ).css("width", "auto");
		}
	});
}

function activity_cover_fitin()
{
	$("#activity .cover > img").load(function() {
		if ($( this ).height()*1.0/$( this ).width() < 384.0/640){
			$( this ).css("height", "100%");
			$( this ).css("width", "auto");
		}
		else{
			$( this ).css("width", "100%");
			$( this ).css("height", "auto");
		}
	});
}

function replace_template(raw, flds, json)
{
	for (var i = 0; i < flds.length; i++) {
		if (json[flds[i]] === null)json[flds[i]] = "";
		raw = raw.replace("#"+flds[i]+"#", json[flds[i]]);
	}

	return raw;
}

function load_group(groupid, json){
	if (json["imgs"] === null || json["title"] === null || json["gid"] === null)return;

	$("#home").append("<div class=\"act-group\" id=\""+groupid+"\"></div>");
	$("#"+groupid).load("templates/act-group.template", function(){
		var raw = $(this).html();

		raw=replace_template(raw, ["img", "gid", "name", "tm", "title", "cmt_count"], json);

		var imgs = json["imgs"].split(",");
		for (var i = 0; i < imgs.length; i++) {
			raw = raw.replace("#img"+(i+1).toString()+"#", imgs[i]);
		}

		$(this).html(raw);

		if (json["img"] === null || json["img"].length == 0)
			$("#"+groupid+" .avatar").hide();
		after_load_act_list(groupid);
	});
}

function switch_location_detail_comment(groupid)
{
	$(".tabs > span").each(function(){
		$("#"+$(this).attr("show-tab")).hide();
	});

	$("#location").show();

	$(".tabs > span").click(function(){
		$(".tabs > span").each(function(){
			$(this).css("color", "#999999");
			$(this).css("border-bottom", "0px");
			$("#" + $(this).attr("show-tab")).hide();
		});
		$("#"+$(this).attr("show-tab")).show();
		//alert("#"+$(this).attr("show-tab"));
		$(this).css("color", "red");
		$(this).css("border-bottom", "2px solid red");
	});
}

function show_my_setting()
{
	$(".side-panel").hide();
	$("[href='#personalPanel']").click(function(e){
		$(".side-panel").show();
		e.stopPropagation(); 
	});

	$('body').click(function() {
		$(".side-panel").hide();
	});
}

function init_goto_activity(groupid){
	$("#"+groupid+" a[href='#activity']").click(function(){
		var gid = $(this).attr("gid");

		//alert("http://jijiehao.hello987.com/db_driver.php?method=v_active&gid=" + gid +"&cb=?");
		$.getJSON(
			"http://jijiehao.hello987.com/db_driver.php?method=v_active&gid=" + gid +"&cb=?", 
			function(data){
				var json  = data[0];
				$("#v_main_act_info").load("templates/main_act_info.template", function(){
					var raw = $(this).html();
					raw=replace_template(raw, ["img", "addr", "name", "tm", "title", "cmt_count", "detail", "main_aid"], json);
					var imgs = json["imgs"].split(",");
					raw = raw.replace("#cover_img#", imgs[0]);
					$(this).html(raw);
					//alert($(this).html());
					activity_cover_fitin();

					if (json["img"] === null || json["img"].length == 0)
						$("#v_main_act_info .avatar").hide();

					switch_location_detail_comment(groupid);
					showComments();
				});
		});

		showAttaches(gid);
	});
}

function after_load_act_list(groupid){
	//alert(groupid);
	group_img_auto_fitin(groupid);
	init_goto_activity(groupid);
}

function load_group_list(){
	$.getJSON(
		"http://jijiehao.hello987.com/db_driver.php?method=v_active&cb=?", 
		function(data){
			for (var i = 0; i < data.length; i++) {
				var gid = "group"+(i+2).toString();
				load_group(gid, data[i]);
			};
			//alert(data);
		});
}

function showComments(){
	$("span[show-tab='comments']").click(function(){
		$("#cmts-frame").html("");
		$.getJSON(
			"http://jijiehao.hello987.com/db_driver.php?method=v_comment&aid=" + $(this).attr("aid") + "&cb=?",
			function(data){
				$.get("templates/comment.template", function(html){
					for (var i = 0; i < data.length; i++) {
						var raw=replace_template(html, ["img", "name", "tm", "cmt"], data[i]);
						//alert(raw);
						$("#cmts-frame").append(raw);
					}
				});
		});
	});
}

function switchIcon(){
	$(".switch").click(function(){
		if($(this).hasClass("icon-i")){
			$(this).removeClass("icon-i");
			$(this).addClass("icon-f");
		}
		else{
			$(this).removeClass("icon-f");
			$(this).addClass("icon-i");
		}
	});
}

function showAttaches(gid){
	$.getJSON(
		"http://jijiehao.hello987.com/db_driver.php?method=v_attach&gid=" + gid + "&cb=?",
		function(data){
			$.get("templates/attach.template", function(html){
				$("#attaches").html("");
				for (var i = 0; i < data.length; i++) {
					var raw=replace_template(html, ["aid", "cover_img", "category", "title"], data[i]);
					alert(raw);
					$("#attaches").append(raw);
				}
				$("#attaches").append("<div class=\"attach\">");
				switchIcon();
			});
		});
}

$(document).ready(function(){
	//load_group("group2", JSON.parse("{\"0\":\"http:\/\/i1.s2.dpfile.com\/pc\/mc\/17378603f970b8f66d066b8301e8e812(450c280)\/aD0yODAmaz0vcGMvbWMvMTczNzg2,http:\/\/i3.s2.dpfile.com\/pc\/mc\/a6143b8d355b32464d5bef9c94ae1c13(450c280)\/aD0yODAmaz0vcGMvbWMvYTYxNDNi,http:\/\/i2.dpfile.com\/pc\/82656d378658cc0e69af20133e3938f1\/29619112_m.jpg\",\"imgs\":\"http:\/\/i1.s2.dpfile.com\/pc\/mc\/17378603f970b8f66d066b8301e8e812(450c280)\/aD0yODAmaz0vcGMvbWMvMTczNzg2,http:\/\/i3.s2.dpfile.com\/pc\/mc\/a6143b8d355b32464d5bef9c94ae1c13(450c280)\/aD0yODAmaz0vcGMvbWMvYTYxNDNi,http:\/\/i2.dpfile.com\/pc\/82656d378658cc0e69af20133e3938f1\/29619112_m.jpg\",\"1\":\"19\",\"follower_no\":\"19\",\"2\":\"\u609f\u7a7a\",\"name\":\"\u609f\u7a7a\",\"3\":\"http:\/\/ww1.sinaimg.cn\/mw600\/a00dfa2agw1esxqmyg89wj20f00820v5.jpg\",\"img\":\"http:\/\/ww1.sinaimg.cn\/mw600\/a00dfa2agw1esxqmyg89wj20f00820v5.jpg\",\"4\":\"\u4e0b\u5348\u8336 +\u4e0b\u5348\u8336 +\u805a\u9910\",\"title\":\"\u4e0b\u5348\u8336 +\u4e0b\u5348\u8336 +\u805a\u9910\",\"5\":\"2015-11-11 00:00:00\",\"tm\":\"2015-11-11 00:00:00\",\"6\":null,\"cmt_count\":null,\"7\":\"3542529.39704345\",\"dist\":\"3542529.39704345\"}"));
	load_group_list();
	show_my_setting();
});
