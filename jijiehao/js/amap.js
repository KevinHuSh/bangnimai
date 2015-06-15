		
	
		var map = new AMap.Map("mapContainer", {
			resizeEnable: true
		});
		var marker = new Array();
		var windowsArr = new Array();
		//基本地图加载
		function placeSearch(addr){
			map = new AMap.Map("mapContainer", {
				resizeEnable: true
			});
			marker = new Array();
			windowsArr = new Array();
			if (typeof addr === "undefined"
				|| addr.length == 0)
				addr = '涞寅路658弄';

			var MGeocoder;
		    //加载地理编码插件
		    AMap.service(["AMap.Geocoder"], function() {        
		        MGeocoder = new AMap.Geocoder({ 
		            city:"021", //城市，默认：“全国”
		            radius:1000 //范围，默认：500
		        });
		        //返回地理编码结果  
		        //地理编码
		        MGeocoder.getLocation(addr, function(status, result){
		        	if(status === 'complete' && result.info === 'OK'){
		        		keywordSearch_CallBack(result);
		        	}
		        });
		    });
		}
		
		function addmarker(i, d) {
		    var lngX = d.location.getLng();
		    var latY = d.location.getLat();
		    var markerOption = {
		        map:map,                 
		        icon:"http://webapi.amap.com/images/"+(i+1)+".png",  
		        position:new AMap.LngLat(lngX, latY)
		    };            
		    var mar = new AMap.Marker(markerOption);  
		    marker.push(new AMap.LngLat(lngX, latY));
		
		    var infoWindow = new AMap.InfoWindow({  
		        content:d.formattedAddress, 
		        autoMove:true, 
		        size:new AMap.Size(150,0),  
		        offset:{x:0,y:-30}
		    });  
		    windowsArr.push(infoWindow);  
		    
		    var aa = function(e){infoWindow.open(map,mar.getPosition());};  
		    AMap.event.addListener(mar,"mouseover",aa);  
		}
		//回调函数
		function keywordSearch_CallBack(data) {
			alert(data);
		    //地理编码结果数组
		    var geocode = new Array();
		    geocode = data.geocodes;
		    for (var i = 0; i < geocode.length; i++) {
		    	addmarker(i, geocode[i]);
		    }  
		    map.setFitView();
		}


