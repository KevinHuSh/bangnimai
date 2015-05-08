<?php
    $qudao = $_GET['qudao'];
    $uuid = $_GET['uuid'];
	
	if (strlen($qudao ) == 0 || strlen($uuid)==0){
	    echo "<H1>Invalid request!~!!</H1>";
		exit;
	}
	
    $mysql_server_name="localhost:3307"; //数据库服务器名称
    $mysql_username="root"; // 连接数据库用户名
    $mysql_password="napheir"; // 连接数据库密码
    $mysql_database="installation"; // 数据库的名字
    $mysql_port=3307;
    
    // 连接到数据库
    $conn=mysql_connect($mysql_server_name, $mysql_username,
                        $mysql_password) or die("Unable to connect to the database.");
	echo "</br>Successfully connected!"; 
	
                        
     // 从表中提取信息的sql语句
    $strsql="insert into info (qudao, uuid,entrydate)values('".$qudao."','".$uuid."', now());"; 
    // 执行sql查询
    if(!mysql_db_query($mysql_database, $strsql, $conn))
	    die("<br><H1>Fail to insert into database</h1>");
		
	echo "<br><H1>Successfully to insert into database</h1>";

    // 关闭连接
    mysql_close($conn);  
?>
