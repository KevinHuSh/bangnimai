<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');

if (strlen($_POST["uid"]) == 0)
{
	echo "{'info':'no uid', 'status': 0}";
	exit;
}

if (isset($_FILES['img'])) {
    $name = "shares/".$_POST["uid"].".kevin.".$_FILES['img']['name'];
    if(move_uploaded_file($_FILES['img']['tmp_name'], "/usr/share/nginx/html/jijiehao/".$name)){
	
    	echo "{\"info\":\"http://jijiehao.hello987.com/".$name."\", \"status\": 1}";
        exit;
    }
}



?>
