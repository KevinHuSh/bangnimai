var userID = "5";
var userName = "Keivin";
var herLongtitude = 0;
var herLatitude = 0;
var herAddr = "";

var userLongtitude = 0;
var userLatitude = 0;

var map = new AMap.Map("amap", {
	resizeEnable: true
});
var marker = new Array();
var windowsArr = new Array();
var gathers = new Array();


$.mobile.loading( 'show', {
	text: "加载中，请稍候……",
	textVisible: true
});

window.onload = function() {
	if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) )
	{
   		var ww = ( $(window).width() < window.screen.width ) ? $(window).width() : window.screen.width; //get proper width
   		var mw = 640; // min width of site
   		var ratio =  ww / mw; //calculate ratio
     		$('#vp').attr('content', 'initial-scale=' + ratio + ', maximum-scale=' + ratio + ', minimum-scale=' + ratio + ', user-scalable=no, width=' + mw);
    }
};

function init_amap(){
	map = new AMap.Map("amap", {
		resizeEnable: true
	});
	marker = new Array();
	windowsArr = new Array();
}

function placeSearch(addr, curr){
	if (typeof addr === "undefined"
		|| addr.length == 0)
		addr = '涞寅路658弄';

	AMap.service(["AMap.Geocoder"], function() {
		var MGeocoder = new AMap.Geocoder({ 
		            city:"021", //城市，默认：“全国”
		            radius:1000 //范围，默认：500
		        });;
		MGeocoder.getLocation(addr, function(status, result){
			if(status === 'complete' && result.info === 'OK'){
				var geocode = new Array();
				geocode = result.geocodes;
				for (var i = 0; i < geocode.length && i<1; i++) {
					if (curr == 0 && typeof marker[curr] != "undefined")
						marker[curr].setMap(null);
					addmarker(curr, geocode[i].location.getLng(), geocode[i].location.getLat(), geocode[i].formattedAddress);
				}  
				map.setFitView();
			}
		});
	});
}

function addmarker(i, lngX, latY, formattedAddress) {
	var markerOption = {
		map:map,                 
		icon:"http://webapi.amap.com/images/"+(i+1)+".png",  
		position:new AMap.LngLat(lngX, latY)
	};            
	var mar = new AMap.Marker(markerOption);  
	marker[i] = mar;

	var infoWindow = new AMap.InfoWindow({  
		content:formattedAddress, 
		autoMove:true, 
		size:new AMap.Size(150,0),  
		offset:{x:0,y:-30}
	});  
	windowsArr.push(infoWindow);  

	var aa = function(e){infoWindow.open(map,mar.getPosition());};  
	AMap.event.addListener(mar,"mouseover",aa);  
	return mar;
}


function setCurrAddr()
{
	var geocoder;
	var lnglatXY = new AMap.LngLat(userLongtitude, userLatitude);
	AMap.service(["AMap.Geocoder"], function() {
		geocoder = new AMap.Geocoder({
			radius: 100,
			extensions: "all"
		});
		geocoder.getAddress(lnglatXY, function(status, result){
			if(status === 'complete' && result.info === 'OK'){
				setAddr(result.regeocode.formattedAddress);
				addmarker(1, userLongtitude, userLatitude, result.regeocode.formattedAddress);
				map.setFitView();
				$.mobile.loading("hide");
			}
		});
	});
}

function setAddr(addr){
	$("#change-location").val(addr);
	$("#curr_addr").text(addr);
	$("#start-point").text(addr);
}

var onSuccess = function(position) {
/*	alert('Latitude: '          + position.coords.latitude          + '\n' +
		'Longitude: '         + position.coords.longitude         + '\n' +
		'Altitude: '          + position.coords.altitude          + '\n' +
		'Accuracy: '          + position.coords.accuracy          + '\n' +
		'Altitude Accuracy: ' + position.coords.altitudeAccuracy  + '\n' +
		'Heading: '           + position.coords.heading           + '\n' +
		'Speed: '             + position.coords.speed             + '\n' +
		'Timestamp: '         + position.timestamp                + '\n');
*/
	userLongtitude = position.coords.longitude;
	userLatitude = position.coords.latitude;

	setCurrAddr();
};

function onError(error) {
	alert('code: '    + error.code    + '\n' +
		'message: ' + error.message + '\n');
}

function replace_template(raw, flds, json)
{
	for (var i = 0; i < flds.length; i++) {
		if (json[flds[i]] === null)json[flds[i]] = "";
		raw = raw.replace("#"+flds[i]+"#", json[flds[i]]);
	}

	return raw;
}

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

var dialog;


$(document).ready(function(){
	alert(getUrlParameter("addr"));
	userID = getUrlParameter("uid");
	userName = getUrlParameter("name");
	herLongtitude = getUrlParameter("lng");
	herLatitude = getUrlParameter("lat");
	herAddr = getUrlParameter("addr");
	$("#uname").text(userName);

	$("#gathers").html("");
	$.get("templates/amap.gather.template", function(html){
		html = html.replace("#name#", userName);
		html = html.replace("#addr#", herAddr);
		$("#gathers").append(html);
	});

	init_amap();
	addmarker(0, herLongtitude, herLatitude, herAddr);
	navigator.geolocation.getCurrentPosition(onSuccess, onError);

	pullGathers();

	$("#change-location").bind("change", function(event){
		placeSearch($(this).val(), 0);
		setAddr($(this).val());
	});

	dialog = $( "#dialog-form" ).dialog({
		autoOpen: false,
		modal: false,
		resizable:false,
		dialogClass: "alert",
		width:250,
		height: 200,
		show: {
			effect: "blind",
			duration: 1000
		},
		hide: {
			effect: "explode",
			duration: 1000
		}
	});
});

function pullGathers(){
	$.get("templates/amap.gather.template", function(html){
		$.getJSON(
			"http://jijiehao.hello987.com/db_driver.php?method=v_gather&uid=" + userID + "&cb=?",
			function(data){
				for (var i = 0; i < data.length; i++) {
					if(typeof gathers[data[i]['wid']] != "undefined")continue;
					gathers[data[i]['wid']] = data[i];
					var raw = replace_template(html, ["name", "wid", "addr"], data[i]);
					$("#gathers").append(raw);
					//addmarker(i+2, data[i]["longtitude"], data[i]["latitude"], data[i]["name"]);
					placeSearch(data[i]["addr"], i+2);
				};
				setTimeout(pullGathers, 5000);
				$(".delete").hide();
			});
	});
}

function joinThem(){
	var uname = $( "#name" ).val();
	if (uname.length == 0){
		giveNickyName();
		return;
	}
	//alert("http://jijiehao.hello987.com/db_driver.php?method=gather&uid=" 
	//	+ userID + "&name="+uname+"&addr=" + $("#start-point").text()+"&longtitude="+userLongtitude
	//	+"&latitude="+userLatitude+"&cb=?");
$.getJSON(
	"http://jijiehao.hello987.com/db_driver.php?method=gather&uid=" 
	+ userID + "&name="+uname+"&addr=" + $("#start-point").text()+"&longtitude="+userLongtitude
	+"&latitude="+userLatitude+"&cb=?",
	function(data){
		alert("您与"+userName+"完成［拼地图］!");
	});
}

function giveNickyName(){
	dialog.dialog("open");
}

function doneJoin(){
	dialog.dialog("close");
	joinThem();
}

function closeDialog(){
	dialog.dialog("close");
}

