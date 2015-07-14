var userID = '3';
var userName = "Kevin";
var userLongtitude = 0;
var userLatitude = 0;
var alert_dialog;

var offset = 0;
var s = 10;

var filter_type = 1;

window.onload = function() {
	if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) )
	{
   		var ww = ( $(window).width() < window.screen.width ) ? $(window).width() : window.screen.width; //get proper width
   		var mw = 640; // min width of site
   		var ratio =  ww / mw; //calculate ratio
     		$('#vp').attr('content', 'initial-scale=' + ratio + ', maximum-scale=' + ratio + ', minimum-scale=' + ratio + ', user-scalable=no, width=' + mw);
    }
};

function replace_template(raw, flds, json)
{
	for (var i = 0; i < flds.length; i++) {
		if (json[flds[i]] === null)json[flds[i]] = "";
		var re = new RegExp("#"+flds[i]+"#", 'g');
		raw = raw.replace(re, json[flds[i]]);
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

function changePage(url){
	$.mobile.changePage(url ,{transition: "slide", reloadPage:true});
}

function getLocation(){
	navigator.geolocation.getCurrentPosition(onSuccess, onError);
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
loadMore();

setTimeout(getLocation, 600000);
};

function onError(error) {
	alert('code: '    + error.code    + '\n' +
		'message: ' + error.message + '\n');
	setTimeout(getLocation, 600000);
}

function login(){
	$.getJSON(
		"http://jijiehao.hello987.com/db_driver.php?method=v_userinfo&uid=" + userID + "&cb=?",
		function(data){
			if (data.length == 0){
				alert_("用户注册错误");
			}

			userName =data[0]['name'];
			localStorage.setItem("userID", userID);
			localStorage.setItem("userName", userName);
			$("#personalPanel img").attr("src", data[0]['img']);
		});
}

//document.addEventListener("deviceready", ready, false);
$(document).ready(ready);

function ready(){
	//localStorage.clear();
	localStorage.setItem("userID", "12d30d16-1a32-11e5-9858-00163e00292a");
	if (localStorage.getItem("userID")){
		userID = localStorage.getItem("userID");
		login();
	}
	else{
		$.getJSON(
			"http://jijiehao.hello987.com/db_driver.php?method=v_userid&cb=?",
			function(data){
				userID = data[0]['uid'];
				login();
				alert(userID + ":" + userName+":"+$("#personalPanel img").attr("src"));
			});
	}

	if (localStorage.getItem("userName"))
		userName = localStorage.getItem("userName");

	getLocation();

	//FastClick.attach(document.body);
	//$(document).bind("mobileinit", function () { $.mobile.defaultPageTransition = 'none'; });

	alert_dialog = $( "#alert-dialog" ).dialog({
      autoOpen: false,
      modal: false,
      resizable:false,
      dialogClass: "alert",
      width: 250,
      height: 120,
      show: {
        effect: "blind",
        duration: 1000
      },
      hide: {
        effect: "explode",
        duration: 1000
      }
    });

    $("#alert-dialog-done").click(function(){
    	alert_dialog.dialog("close");
    });
}

function alert_(str){
	$("#alert-dialog div:nth-of-type(1)").text(str);
	alert_dialog.dialog("open");
}

function goback(ele){
	//alert($(ele).children("a"));
	$(ele).parent("a").trigger("click");
}

