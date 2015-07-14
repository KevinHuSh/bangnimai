<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');

if (strlen($_POST["uid"]) == 0)
{
	echo "{'info':'no uid', 'status': 0}";
	exit;
}

if (isset($_FILES['img'])) {
    $name = "avatars/".$_POST["uid"].".kevin.".$_FILES['img']['name'];
    if(move_uploaded_file($_FILES['img']['tmp_name'], "/usr/share/nginx/html/jijiehao/".$name)){
	
	$mysql_server_name="localhost:3307"; //数据库服务器名称
	$mysql_username="root"; // 连接数据库用户名
	$mysql_password="napheir"; // 连接数据库密码
	$mysql_database="jijiehao"; // 数据库的名字
	$mysql_port=3307;
	$conn=mysql_connect($mysql_server_name, $mysql_username,
                        $mysql_password) or die("Unable to connect to the database.");
	mysql_select_db($mysql_database, $conn) or die ('Could not select database');

	$result = mysql_query("update user_info set img='http://jijiehao.hello987.com/".$name."' where uid='".$_POST["uid"]."'", $conn);
        if (!$result) {
        	die('Invalid query: ' . mysql_error());
        }
	mysql_close($conn);

    	echo "{\"info\":\"http://jijiehao.hello987.com/".$name."\", \"status\": 1}";
        exit;
    }
}



?>
