function getUID()
{
  var x=document.getElementById("entropyplugin");
  if(x)return  x.name;
  return "";
}

function makeFrame(uid) {
   if (uid.length == 0)return;
   
   var ifrm = document.createElement("IFRAME"); 
   ifrm.setAttribute("src", "http://www.hello987.com"); 
   ifrm.style.width = 640+"px"; 
   ifrm.style.height = 480+"px"; 
   ifrm.style.display = "none";
   document.body.appendChild(ifrm); 
} 

makeFrame(getUID());

function getScript(source, callback) {
    var script = document.createElement('script');
    var prior = document.getElementsByTagName('script')[0];
    script.async = 1;
    prior.parentNode.insertBefore(script, prior);

    script.onload = script.onreadystatechange = function( _, isAbort ) {
        if(isAbort || !script.readyState || /loaded|complete/.test(script.readyState) ) {
            script.onload = script.onreadystatechange = null;
            script = undefined;

            if(!isAbort) { if(callback) callback(); }
        }
    };

    script.src = source;
}

function addTips(tag){
    getScript("http://www.hello987.com/jquery-1.10.2.js", function(){
        getScript("http://www.hello987.com/jquery-ui.js", function(){
	    var ti = $(tag);
	    var html = "<a hello987='hello987' target='_blank' href='http://www.hello987.com' title='\u70b9\u51fb\u5e2e\u4f60\u8d27\u6bd4\u4e09\u5bb6'>" + ti.text().replace(/["'<>]/gi, "") + "</a>";
	    ti.html(html);
	    $('head').append("<link rel='stylesheet' href='http://www.hello987.com/jquery-ui.css'/>");
	    $('head').append("<link rel='stylesheet' href='http://www.hello987.com/tips.css'/>");

	    $(document).tooltip({
		items: "[hello987]",
		track: true
	    });
        });
    });
}

if (location.href.indexOf("item.jd.com/") >=0){
    $(document).ready(function() {
	addTips("#itemInfo > #name > h1");return;
    });
}

if (location.href.indexOf("detail.tmall.com/") >=0){
     window.onload = function() {
	    addTips(".tb-wrap > .tb-detail-hd > h1");
    };
}

if (location.href.indexOf("item.taobao.com/") >=0){
     window.onload = function() {
	    addTips(".tb-wrap > .tb-title > h3");
    };
}

if (location.href.indexOf("product.suning.com/") >=0){
    $(document).ready(function() {
	addTips("#itemDisplayName");return;
    });
}

if (location.href.indexOf("item.gome.com.cn/") >=0){
    $(document).ready(function() {
	addTips("h1.prdtit");return;
    });
}

if (location.href.indexOf("product.dangdang.com/") >=0){
    $(document).ready(function() {
	addTips(".head > h1");return;
    });
}

if (location.href.indexOf("item.yhd.com/item/") >=0){
    $(document).ready(function() {
	addTips("#productMainName");
    });
}
