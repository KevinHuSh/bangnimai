<html lang="en">
<head>
<link rel="shortcut icon" type="image/x-icon" href="bi_logo.ico" media="screen" /> 
<meta charset="utf-8">
<title>渠道查询</title>
<link rel="stylesheet" href="jquery-ui.css">
<script src="jquery-1.10.2.js"></script>
<script src="jquery-ui.js"></script>
</head>
<body>
<div id="datepicker"></div>
<script>
$.datepicker.regional['zh-CN'] = { 
        clearText: '清除', 
        clearStatus: '清除已选日期', 
        closeText: '关闭', 
        closeStatus: '不改变当前选择', 
        prevText: '<上月', 
        prevStatus: '显示上月', 
        prevBigText: '<<', 
        prevBigStatus: '显示上一年', 
        nextText: '下月>', 
        nextStatus: '显示下月', 
        nextBigText: '>>', 
        nextBigStatus: '显示下一年', 
        currentText: '今天', 
        currentStatus: '显示本月', 
        monthNames: ['一月','二月','三月','四月','五月','六月', '七月','八月','九月','十月','十一月','十二月'], 
        monthNamesShort: ['一','二','三','四','五','六', '七','八','九','十','十一','十二'], 
        monthStatus: '选择月份', 
        yearStatus: '选择年份', 
        weekHeader: '周', 
        weekStatus: '年内周次', 
        dayNames: ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'], 
        dayNamesShort: ['周日','周一','周二','周三','周四','周五','周六'], 
        dayNamesMin: ['日','一','二','三','四','五','六'], 
        dayStatus: '设置 DD 为一周起始', 
        dateStatus: '选择 m月 d日, DD', 
        dateFormat: 'yy-mm-dd', 
        firstDay: 1, 
        initStatus: '请选择日期', 
        isRTL: false};
    
    $.datepicker.setDefaults($.datepicker.regional['zh-CN']); 
	
//$( "#datepicker" ).datepicker();
</script>

<form id="qudao_form" action="qudao.php"  method="post">
<label for="end-datepicker">渠道ID:</label> <input type="text" id="qudao" name="qudao" size="15" value="<?php echo $_POST['qudao']; ?>" /></br></br>
<label for="start-datepicker">起始日期:</label> <input type="text" class="datepicker test-image-datepicker" id="start-datepicker" name="start-datepicker" size="15" value="<?php echo $_POST['start-datepicker']; ?>" /></br></br>
<label for="end-datepicker">结束日期:</label> <input type="text" class="datepicker test-image-datepicker" id="end-datepicker" name="end-datepicker" size="15" value="<?php echo $_POST['end-datepicker']; ?>" /></br></br>
<input type="submit" value="查询"/>
</form>

<?php

if(strlen($_POST['qudao'])>0 && strlen($_POST['start-datepicker'])>0 && strlen($_POST['end-datepicker'])>0)
{
    $qudao = $_POST['qudao'];;
    $s_dt = $_POST['start-datepicker']." 00:00:00";
    $e_dt = $_POST['end-datepicker']." 23:59:59";
	
    $mysql_server_name="localhost:3307"; //数据库服务器名称
    $mysql_username="root"; // 连接数据库用户名
    $mysql_password="napheir"; // 连接数据库密码
    $mysql_database="installation"; // 数据库的名字
    $mysql_port=3307;
    
    // 连接到数据库
    $conn=mysql_connect($mysql_server_name, $mysql_username,
                        $mysql_password) or die("Unable to connect to the database.");
	
                        
     // 从表中提取信息的sql语句
    $strsql="select count(distinct uuid) from info where qudao='".$qudao."' and entrydate between '".$s_dt."' and '".$e_dt."';"; 
    //$strsql="select count(distinct uuid) from info where entrydate between '".$s_dt."' and '".$e_dt."';"; 
	
    // 执行sql查询
    if(!($result = mysql_db_query($mysql_database, $strsql, $conn)))
	    die("<br><H1>Database failure</h1>");
	
	if($row = mysql_fetch_row($result))
	    echo "<br><H1>".$qudao.",<br> 从【".$s_dt."】至【".$e_dt."】，<br>有效推广数为 <font color='red'>".$row[0]."</font></h1>";
    else
	   echo "<br><H1>Database selection failure</h1>";

    // 关闭连接
    mysql_close($conn);  
}
?>
</body>
<script>
$(function(){ 
    $("#start-datepicker").datepicker(); 
    $("#end-datepicker").datepicker(); 
    $( "#start-datepicker" ).datepicker( "option", "showAnim", "drop" );
    $( "#end-datepicker" ).datepicker( "option", "showAnim", "drop" );
}); 

var myDate = new Date();
if ($("#end-datepicker")[0].value.length ==0)
    $("#end-datepicker")[0].value = myDate.getFullYear()+"-" + myDate.getMonth() + "-" + myDate.getDate();

myDate = new Date(myDate.getFullYear(), myDate.getMonth(), myDate.getDate()-7);
if ($("#start-datepicker")[0].value.length == 0)
    $("#start-datepicker")[0].value = myDate.getFullYear()+"-" + myDate.getMonth() + "-" + myDate.getDate();

$("form").submit(function(){
    var regDateFormat = /^\d{4}-\d{2}-\d{2}$/;
	if (!regDateFormat.test($("#start-datepicker")[0].value)){
       alert("起始日期格式错误！");
       return false; 
	}
	
	if (!regDateFormat.test($("#end-datepicker")[0].value)){
       alert("结束日期格式错误！");
       return false; 
	}
	
	if ($("#qudao")[0].value.length == 0){
       alert("渠道编号不能为空！");
       return false; 
	}
});
</script>
</html>


