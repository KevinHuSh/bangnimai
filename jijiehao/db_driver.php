<?php

$mysql_server_name="localhost:3307"; //数据库服务器名称
$mysql_username="root"; // 连接数据库用户名
$mysql_password="napheir"; // 连接数据库密码
$mysql_database="jijiehao"; // 数据库的名字
$mysql_port=3307;

// 连接到数据库
$conn=mysql_connect($mysql_server_name, $mysql_username,
                        $mysql_password) or die("Unable to connect to the database.");
#        echo "</br>Successfully connected!";

mysql_select_db($mysql_database, $conn) or die ('Could not select database');

switch ($_GET["method"]) {
	case 'v_active':
		showActives();
		break;
	case 'v_comment':
		showComments();
		break;
	
	case 'v_attach':
		showAttaches();
		break;
	default:
		# code...
		break;
}

function select($sql_str)
{
	global $conn;
 	$result = mysql_query($sql_str, $conn);
        if (!$result) {
        die('Invalid query: ' . mysql_error());
        }
        $rows = array();
        while($r = mysql_fetch_array($result)) {
                $rows[] = $r;
        }
        echo $_GET["cb"]."(".json_encode($rows).")";
}

function showActives(){
	$offset = $_GET["offset"];
	if (strlen($offset ) == 0)
		$offset = 0;
	$size = $_GET["s"];
	if (strlen($size ) == 0)
		$size = 10;
	$lat = $_GET["lat"];
	$lon = $_GET["lon"];
	if (strlen($lat ) == 0)
		$lat = 0;
	if (strlen($lon ) == 0)
		$lon = 0;


	$sql_str = "select gid, imgs, follower_no, name, img, title, tm, cmt_count, pow(pow(longtitude-".$lon.", 2)+pow(latitude-".$lat.",2),0.5) as dist from v_activity  where pow(pow(longtitude-".$lon.", 2)+pow(latitude-".$lat.",2),0.5)<=100000000 limit ".$offset.",".$size.";";
	//echo $sql_str;

	if (strlen($_GET['gid']) > 0)
		$sql_str = "select gid, imgs, img, addr, name, tm, title, cmt_count, detail, main_aid from v_activity where gid=".$_GET['gid'];
	select($sql_str);
}

function showComments()
{
	if (strlen($_GET['aid']) == 0){
		echo "Missing parameter aid";
		return;
	}

	$sql_str = "select comment.cmt, comment.tm,user_info.name,user_info.img from comment, user_info where comment.uid=user_info.uid and name is not null and img is not null and comment.aid=".$_GET['aid'];

	select($sql_str);
}

function showAttaches()
{
	if (strlen($_GET['gid']) == 0){
		echo "Missing parameter aid";
		return;
	}

	$sql_str = "select activity.aid,cover_img,title,category from active_attach,activity where active_attach.aid=activity.aid and active_attach.gid=".$_GET['gid'];
 	select($sql_str);
}


mysql_close($conn);

?>
