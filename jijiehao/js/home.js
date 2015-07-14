var userID = '5';
userName = "Kevin";
var userLongtitude = 0;
var userLatitude = 0;

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

	$("#cmt").bind("change", function(event){
		$.getJSON(
			"http://jijiehao.hello987.com/db_driver.php?method=comment&uid=" + userID +"&aid="+$("span[show-tab='comments']").attr("aid")+"&cmt="+$("#cmt").val()+"&cb=?", 
			function(data){
				loadComments($("span[show-tab='comments']").attr("aid"));
			});  
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

function loadComments(aid)
{
	$("#cmts-frame").html("");
	$.getJSON(
		"http://jijiehao.hello987.com/db_driver.php?method=v_comment&aid=" + aid + "&cb=?",
		function(data){
			$.get("templates/comment.template", function(html){
				for (var i = 0; i < data.length; i++) {
					var raw=replace_template(html, ["img", "name", "tm", "cmt"], data[i]);
						//alert(raw);
						$("#cmts-frame").append(raw);
					}
				});
		});
}

function showComments(){
	$("span[show-tab='comments']").click(function(){
		loadComments($(this).attr("aid"));
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
					//alert(raw);
					$("#attaches").append(raw);
				}
				$("#attaches").append("<div class=\"attach\">");
				switchIcon();
			});
		});
}

function init_create_group()
{
	$("#activity a[href='#create_group']").click(function(){

		var aids = $("span[show-tab='comments']").attr("aid") ;
		$("#activity .icon-i").each(function(){
			aids += "," + $(this).attr("aid");
		});

		$("#var-aids").text(aids);

		alert(aids);
		$.getJSON(
			"http://jijiehao.hello987.com/db_driver.php?method=v_acts&aids=" + aids + "&cb=?",
			function(data){
				$.get("templates/acts.act.template", function(html){
					$("#acts").html("");
					for (var i = 0; i < data.length; i++) {
						var raw=replace_template(html, ["aid", "cover_img", "category", "title"], data[i]);
						$("#acts").append(raw);
					}
				});
			});
	});
}

function createGroup()
{
	var param = userID + ",";//uid
	param += $("#var-aids").text().split(",")[0] + ",";//main_aid
	param += $("#max_follower_no").val() + ",";//max_follower_no
	param += $("#joinable").val() + ","//joinable
	param += $("#phone").val() + ","//phone
	param += $("#summary").val() + ","//summary
	param += $("#info").val() //info
	param = param.replace(/"/g, "\\\"").replace(/'/g, "\\\"").replace(/,/g, "','");
	param = "'" + param + "'";
	alert(param);

	$.getJSON(
		"http://jijiehao.hello987.com/db_driver.php?method=group&param=" + param + "&aids="+$("#var-aids").text()+"&cb=?",
		function(data){
			alert(data["id"]);
			load_act_table(data["id"], 1);
			$.mobile.changePage( "home.html#act_table", { transition: "slideup", changeHash: false });
		});
}

function init_whereto()
{
	$("#activity a[href='#whereto']").click(function(){

		var aid = $("span[show-tab='comments']").attr("aid") ;
		$.getJSON(
			"http://jijiehao.hello987.com/db_driver.php?method=v_stuck_in&aid=" + aid + "&cb=?",
			function(data){
				$.get("templates/whereto.row.template", function(html){
					$("#whereto-rows").html("");
					for (var i = 0; i < data.length; i++) {
						var raw=replace_template(html, ["img", "name", "gid"], data[i]);
						var imgs = data[i]["cover_imgs"].split(",");
						for (var j = 0; j < imgs.length; j++) 
							raw = raw.replace("#_#", "<span><img src='"+imgs[j]+"' class='act-avatar trans_center'/></span>#_#");
						raw = raw.replace("#_#", "");
						$("#whereto-rows").append(raw);
					}
					init_act_table();
				});
			});
	});
}

function init_act_table()
{
	$("#whereto a[href='#act_table']").click(function(){
		load_act_table($(this).attr("gid"), $(this).attr("ismine"));
	});
}


function load_act_table(gid, ismine)
{
	$("#applications").html("");
	$("#act_table .footer").hide();
	$("#applying").show();

	alert(gid);
	$.getJSON(
		"http://jijiehao.hello987.com/db_driver.php?method=v_act_table&gid=" + gid + "&cb=?",
		function(data){
			$.get("templates/act_table.template", function(html){
				$("#act_table-content").html("");
				var raw=replace_template(html, ["img", "name", "summary", "phone", "info"], data[0]);
				$.get("templates/act_table.act.template", function(act){
					for (var i = 0; i < data.length; i++) {
						raw = raw.replace("#_#", replace_template(act, ["category", "title","cover_img"], data[i])+"#_#");
					}
					raw = raw.replace("#_#", "");
					$("#act_table-content").html(raw);
				});
			});
		});

	if(parseInt(ismine) != 1)return;


	$("#applying").hide();
	$("#act_table .footer").show();
	$.get("templates/act_table.app.template", function(app){
		$.getJSON(
			"http://jijiehao.hello987.com/db_driver.php?method=v_message&gid=" + gid + "&cb=?",
			function(uinfo){
				for (var i = 0; i < uinfo.length; i++) {
					var raw = replace_template(app, ["img" , "name"], uinfo[i]);
						if (parseInt(uinfo[i]["approved"]) == 1){//1 applying, 2. approved, 3. rejected
							raw = raw.replace("#rej-appr-sty#", "approve");
							raw = raw.replace("#rej-appr#", "阻止");
						}
						else{
							raw = raw.replace("#rej-appr-sty#", "reject");
							raw = raw.replace("#rej-appr#", "同意");
						}
						$("#applications").append(raw);
					}
					if(uinfo.length > 0)$("#applications").append("<div class='row'></div>");
					alert($("#app-number").text());
					$("#app-number").text(uinfo.length.toString());
				});
	});
}

function init_show_messages(){
	$("#home a[href='#messages']").click(function(){

		var uid = $(this).attr("uid") ;
		$("#messages-rows").html("");
		$.get("templates/messages.row.template", function(html){
			$.getJSON(
				"http://jijiehao.hello987.com/db_driver.php?method=v_message&uid=" + uid + "&cb=?",
				function(data){
					for (var i = 0; i < data.length; i++) {
						var raw = replace_template(html, ["name", "summary", "img"], data[i]);
						if (parseInt(data[i]["approved"]) == 1){
							raw = raw.replace("#icon#", "icon-g");
						}else{
							raw = raw.replace("#icon#", "icon-z");
						}
						$("#messages-rows").append(raw);
					};
				});
		});
	});
}

function init_show_schedule(){
	$("#home a[href='#myschedule']").click(function(){

		var uid = $(this).attr("uid") ;
		$("#myschedule-rows").html("");
		$.get("templates/myschedule.row.template", function(html){
			$.getJSON(
				"http://jijiehao.hello987.com/db_driver.php?method=v_app&uid=" + uid + "&cb=?",
				function(data){
					for (var i = 0; i < data.length; i++) {
						var raw = replace_template(html, ["name", "gid", "img"], data[i]);
						if (parseInt(data[i]["approved"]) != 2){
							raw = raw.replace("#color#", "red");
							raw = raw.replace("#status#", "申请中");
						}else if (parseInt(data[i]["approved"]) == 2){
							raw = raw.replace("#color#", "blue");
							raw = raw.replace("#status#", "已加入");
						}
						$("#myschedule-rows").append(raw);
					};
					load_schedule_cells();
				});
		});
	});
}

function load_schedule_cells()
{
	$.get("templates/myschedule.act.template", function(html){
		$("#myschedule-rows .acts").each(function(){
			var gid = $(this).attr("gid");
			var obj = $(this);
			$(obj).html("");
			$.getJSON(
				"http://jijiehao.hello987.com/db_driver.php?method=v_act_table&gid=" + gid + "&cb=?",
				function(data){
					for (var i = 0; i < data.length; i++) {
						var raw = replace_template(html, ["cover_img", "title", "category"], data[i]);
						$(obj).append(raw);
					};
				});
		});
	});
}

function showImgOption(e)
{
	$("#settings .cover, #settings .pic").show();
	e.stopPropagation(); 
	$('body').click(function() {
		$("#settings .cover, #settings .pic").hide();
	});
}

function setGender(gen)
{
		var url = "./setting-gender.html?g=" + gen;
		$.mobile.changePage(url ,{transition: "slide", reloadPage:true});
}

$(document).ready(function(){
	//load_group("group2", JSON.parse("{\"0\":\"http:\/\/i1.s2.dpfile.com\/pc\/mc\/17378603f970b8f66d066b8301e8e812(450c280)\/aD0yODAmaz0vcGMvbWMvMTczNzg2,http:\/\/i3.s2.dpfile.com\/pc\/mc\/a6143b8d355b32464d5bef9c94ae1c13(450c280)\/aD0yODAmaz0vcGMvbWMvYTYxNDNi,http:\/\/i2.dpfile.com\/pc\/82656d378658cc0e69af20133e3938f1\/29619112_m.jpg\",\"imgs\":\"http:\/\/i1.s2.dpfile.com\/pc\/mc\/17378603f970b8f66d066b8301e8e812(450c280)\/aD0yODAmaz0vcGMvbWMvMTczNzg2,http:\/\/i3.s2.dpfile.com\/pc\/mc\/a6143b8d355b32464d5bef9c94ae1c13(450c280)\/aD0yODAmaz0vcGMvbWMvYTYxNDNi,http:\/\/i2.dpfile.com\/pc\/82656d378658cc0e69af20133e3938f1\/29619112_m.jpg\",\"1\":\"19\",\"follower_no\":\"19\",\"2\":\"\u609f\u7a7a\",\"name\":\"\u609f\u7a7a\",\"3\":\"http:\/\/ww1.sinaimg.cn\/mw600\/a00dfa2agw1esxqmyg89wj20f00820v5.jpg\",\"img\":\"http:\/\/ww1.sinaimg.cn\/mw600\/a00dfa2agw1esxqmyg89wj20f00820v5.jpg\",\"4\":\"\u4e0b\u5348\u8336 +\u4e0b\u5348\u8336 +\u805a\u9910\",\"title\":\"\u4e0b\u5348\u8336 +\u4e0b\u5348\u8336 +\u805a\u9910\",\"5\":\"2015-11-11 00:00:00\",\"tm\":\"2015-11-11 00:00:00\",\"6\":null,\"cmt_count\":null,\"7\":\"3542529.39704345\",\"dist\":\"3542529.39704345\"}"));
	load_group_list();
	show_my_setting();
	init_show_messages();
	init_show_schedule();
});

    $("#gender-setting").on('pageshow',function(){
        alert("reload");
        alert(getUrlParameter("g"));
        $(".gender").click(function(){
            var span = $(this).find("span:nth-of-type(2)");
            if ($(span).hasClass("icon-i"))return;
            $(".icon-i").toggleClass("icon-i");
            $(span).addClass("icon-i");
        });
    });

    function getUrlParameter(sParam)
    {
        var sPageURL = window.location.search.substring(1);
        var sURLVariables = sPageURL.split('&');
        for (var i = 0; i < sURLVariables.length; i++) 
        {
            var sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] == sParam) 
            {
                return decodeURIComponent(sParameterName[1]);
            }
        }
    } 
