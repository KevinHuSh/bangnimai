$(document).ready(function() {
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
			search();
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
			    $("#suggestions").hide();
			    search();
	//			alert($(this).attr("term"));
			});
    		});
		}, doneTypingInterval);
		}else $("#suggestions").hide();
	});
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
});
