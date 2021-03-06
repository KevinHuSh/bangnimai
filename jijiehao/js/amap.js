		

var map = new AMap.Map("amap", {
	resizeEnable: true
});
var marker = new Array();
var windowsArr = new Array();
var gathers = new Map();

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
					if (curr == 0 && typeof marker[curr] != "undefined"){
						marker[curr].setMap(null);
						userLongtitude = geocode[i].location.getLng();
						userLatitude = geocode[i].location.getLat();
					}
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
				$("#change-location").val(result.regeocode.formattedAddress);
				$("#curr_addr").text(result.regeocode.formattedAddress);

				addmarker(0, userLongtitude, userLatitude, result.regeocode.formattedAddress);

				map.setFitView();
			}
		});
	});
}

var onSuccess = function(position) {
	alert('Latitude: '          + position.coords.latitude          + '\n' +
		'Longitude: '         + position.coords.longitude         + '\n' +
		'Altitude: '          + position.coords.altitude          + '\n' +
		'Accuracy: '          + position.coords.accuracy          + '\n' +
		'Altitude Accuracy: ' + position.coords.altitudeAccuracy  + '\n' +
		'Heading: '           + position.coords.heading           + '\n' +
		'Speed: '             + position.coords.speed             + '\n' +
		'Timestamp: '         + position.timestamp                + '\n');

	userLongtitude = position.coords.longitude;
	userLatitude = position.coords.latitude;

	init_amap();
	setCurrAddr();
};

function onError(error) {
	alert('code: '    + error.code    + '\n' +
		'message: ' + error.message + '\n');
}

function init_remove(){
	$(".remove").off("click").click(function(e){
		var wid = $(this).attr("wid");
		marker[$(this).attr("curr")].setMap(null);
		map.setFitView();
		gathers.delete(wid);
		$(this).parent().hide(1000);
		e.stopPropagation(); 
		$.getJSON(
			"http://jijiehao.hello987.com/db_driver.php?method=del_gather&wid=" + wid + "&cb=?",
			function(data){

			});
	});
}

function init_delete(){
	$(".delete").off("click").click(function(e){
		$(this).removeClass('delete',1000);
		$(this).addClass('remove',1000);
		$(this).text("删除");
		init_remove();
		e.stopPropagation(); 
	});

	$('body').click(function() {
		$(".remove").text("×");
		$(".remove").addClass('delete',1000);
		$(".remove").removeClass('remove',1000);
		init_delete();
	});
}

$(document).ready(function(){
	$("#change-location").bind("change", function(event){
		placeSearch($(this).val(), 0);
	});

	navigator.geolocation.getCurrentPosition(onSuccess, onError);
	setTimeout(pullGathers, 5000);
	$("#gathers").html("");

});

function pullGathers(){
	$.get("templates/amap.gather.template?v=?", function(html){
		$.getJSON(
			"http://jijiehao.hello987.com/db_driver.php?method=v_gather&uid=" + userID + "&cb=?",
			function(data){
				for (var i = 0; i < data.length; i++) {
					if(gathers.has(data[i]['wid']))continue;
					gathers.set(data[i]['wid'], data[i]);
					var raw = replace_template(html, ["name", "wid", "addr"], data[i]);
					raw = raw.replace("#curr#", i+1);
					$("#gathers").prepend(raw);
					init_delete();
				//addmarker(i+2, data[i]["longtitude"], data[i]["latitude"], data[i]["name"]);
				placeSearch(data[i]["addr"], i+1);
			};

			setTimeout(pullGathers, 5000);
		});
	});
}

function send2weixin(){
}
