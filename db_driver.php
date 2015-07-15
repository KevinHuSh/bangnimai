<?php
header("Content-Type: text/html; charset=utf-8");

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
mysql_query("set character set 'utf8'", $conn);
mysql_query("set names 'utf8'", $conn);

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
	case 'v_sugg':
		showSuggestions();
		break;
	case 'v_acts':
		showActs();
		break;
	case 'v_stuck_in':
		showGroupImgs();
		break;
	case 'v_act_table':
		showActTable();
		break;
	case 'v_message':
		showMessage();
		break;
	case 'v_app':
		showApp();
		break;
	case 'v_gather':
		showGather();
		break;
	case 'v_userinfo':
		showUserInfo();
		break;
	case 'v_userid':
		showUserID();
		break;
	case 'comment':
		comment();
		break;
	case 'del_gather':
		delGather();
		break;
	case 'gather':
		gather();
		break;
	case 'group':
		group();
		break;
	case 'apply':
		apply();
		break;
	case 'approve':
		approve();
		break;
	case 'userinfo':
		userinfo();
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

function insert_group($sql_str)
{
	global $conn;
 	$result = mysql_query($sql_str, $conn);
        if (!$result) {
        	die('Invalid query: ' . mysql_error());
        }
	$gid = mysql_insert_id();
        echo $_GET["cb"]."({'id':'".$gid."'})";
	
	$aids = explode(',', $_GET['aids']);
	for ($i = 1; $i < count($aids); $i++) {
		$sql_str = "insert into active_attach(gid, aid)values('".$gid."', '".$aids[$i]."')";
		$result = mysql_query($sql_str, $conn);
		 if (!$result)  die('Invalid query: ' . mysql_error());
	}
}

function showActives(){
	$offset = $_GET["offset"];
	if (strlen($offset ) == 0)
		$offset = 0;
	$size = $_GET["s"];
	if (strlen($size ) == 0)
		$size = 100;
	$lat = $_GET["lat"];
	$lon = $_GET["lon"];
	if (strlen($lat ) == 0)
		$lat = 0;
	if (strlen($lon ) == 0)
		$lon = 0;

	$location = "(select ceil((".$lat."-from_lat)/((to_lat-from_lat)/100.)) as block_lat, ceil((".$lon."-from_lon)/((to_lon-from_lon)/100.)) as block_lon, city from city_location where ".$lat." >= from_lat and ".$lon." >= from_lon and ".$lat."<= to_lat and ".$lon."<=to_lon) loc";
	if (strlen($_GET["uid"]) > 0)
	    $location = "(select  city, ceil((max(ceil((gather.latitude-city_location.from_lat)/((to_lat-from_lat)/100.)))+min(ceil((gather.latitude-city_location.from_lat)/((to_lat-from_lat)/100.))))/2) as block_lat, ceil((max(ceil((gather.longtitude-city_location.from_lon)/((to_lon-from_lon)/100.)))+min(ceil((gather.longtitude-city_location.from_lon)/((to_lon-from_lon)/100.))))/2) as block_lon from  gather, city_location where  gather.uid = '".$_GET['uid']."' and  gather.latitude >= city_location.from_lat and gather.latitude <= city_location.to_lat and gather.longtitude >= city_location.from_lon and gather.longtitude <= city_location.to_lon group by city) loc";

	$filter = "";
	if (strlen($_GET['filter']) > 0){
	    if ((int)$_GET['filter'] == 1)
		$filter = " and activity.category in ('下午茶', '聚餐', '游戏', '电影') ";
	    if ((int)$_GET['filter'] == 2)
		$filter = " and activity.category in ('商场', '展览', '戏剧') ";
	    if ((int)$_GET['filter'] == 3)
		$filter = " and activity.category not in ('商场', '展览', '戏剧', '下午茶', '聚餐', '游戏', '电影') ";
	}

	$sql_str = "SELECT SUBSTRING_INDEX(GROUP_CONCAT(t.name SEPARATOR ';'), ';', 1) as name,SUBSTRING_INDEX(GROUP_CONCAT(t.gid SEPARATOR ';'), ';', 1) as gid, SUBSTRING_INDEX(GROUP_CONCAT(t.main_aid SEPARATOR ';'), ';', 1) as main_aid, SUBSTRING_INDEX(GROUP_CONCAT(t.imgs SEPARATOR ';'), ';', 1) as imgs, SUBSTRING_INDEX(GROUP_CONCAT(t.img SEPARATOR ';'), ';', 1) as img, SUBSTRING_INDEX(GROUP_CONCAT(t.title SEPARATOR ';'), ';', 1) as title, SUBSTRING_INDEX(GROUP_CONCAT(t.tm SEPARATOR ';'), ';', 1) as tm, SUBSTRING_INDEX(GROUP_CONCAT(t.cmt_count SEPARATOR ';'), ';', 1) as cmt_count from  ( SELECT  active_group.gid AS gid,  active_group.main_aid AS main_aid,  v_top3_active.imgs AS imgs,  activity.follower_no AS follower_no,  activity.rank AS rank,  user_info.uid AS uid,  user_info.name AS name,  user_info.img AS img,  v_top3_active.title AS title,  active_group.create_tm AS tm,  ifnull(v_comment_count.cmt_count,0) AS cmt_count,  activity.detail AS detail,  activity.addr AS addr,  abs(activity.block_lat-loc.block_lat)+abs(activity.block_lon-loc.block_lon),  (activity.follower_no+100)/(activity.rank + 20)/POW(abs(activity.block_lat-loc.block_lat)+abs(activity.block_lon-loc.block_lon), 1) as score FROM  active_group  join v_top3_active on v_top3_active.gid = active_group.gid  join activity on active_group.main_aid = activity.aid  left join v_comment_count on v_comment_count.gid = active_group.gid  left join user_info on active_group.uid = user_info.uid,  ".$location." where  activity.city = loc.city and  activity.follower_no>5 ".$filter."  order by score desc, active_group.create_tm desc ) t group by t.main_aid, t.imgs order by t.score desc limit ".$offset.",".$size.";";
	if ($lon == 0)
		$sql_str = "select gid, imgs, follower_no, name, img, title, tm, cmt_count, pow(pow(longtitude-".$lon.", 2)+pow(latitude-".$lat.",2),0.5) as dist from v_activity  where pow(pow(longtitude-".$lon.", 2)+pow(latitude-".$lat.",2),0.5)<=100000000 limit ".$offset.",".$size.";";

	if (strlen($_GET['gid']) > 0)
		$sql_str = "select gid, imgs, img, addr, name, tm, title, cmt_count, detail, main_aid, uid from v_activity where gid=".$_GET['gid'];
	if (strlen($_GET['aid']) > 0)
		$sql_str = "select cover_img, addr, activity.tm, title, count(uid) as cmt_count, detail, activity.aid as main_aid from activity left join comment on activity.aid=comment.aid where activity.aid=".$_GET['aid'];
//	echo $sql_str;
	select($sql_str);
}

function showComments()
{
	if (strlen($_GET['aid']) == 0){
		echo "Missing parameter aid";
		return;
	}

	$sql_str = "select comment.cmt, comment.tm,user_info.name,user_info.img from comment, user_info where comment.uid=user_info.uid and name is not null and img is not null and comment.aid=".$_GET['aid']." order by comment.tm desc";

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

function showSuggestions()
{
        if (strlen($_GET['aid']) == 0){
                echo "Missing parameter aid";
                return;
        }

        $sql_str = "select SUBSTRING_INDEX(GROUP_CONCAT(a.aid order by abs(b.block_lon-a.block_lon)+abs(b.block_lat-a.block_lat),a.rank), ',', 1) as aid, SUBSTRING_INDEX(GROUP_CONCAT(a.title order by abs(b.block_lon-a.block_lon)+abs(b.block_lat-a.block_lat),a.rank), ',', 1) as title, SUBSTRING_INDEX(GROUP_CONCAT(a.cover_img order by abs(b.block_lon-a.block_lon)+abs(b.block_lat-a.block_lat),a.rank), ',', 1) as cover_img, a.category from activity a, (select * from activity where aid=".$_GET['aid'].") as b where a.aid<>b.aid and a.category<>b.category and abs(b.block_lon-a.block_lon)+abs(b.block_lat-a.block_lat)<=3 and a.city=b.city group by a.category limit 12";
        select($sql_str);
}

function showActs()
{
	if (strlen($_GET['aids']) == 0){
	echo "Missing parameter aids";
		return;
	}

	$sql_str = "select aid,cover_img,title,category,url from activity where aid in (".$_GET['aids'].")";
	//echo $sql_str;
 	select($sql_str);
}

function showGroupImgs()
{
	if (strlen($_GET['aid']) == 0){
	echo "Missing parameter aid";
		return;
	}

	$sql_str = "select    concat(main_cover.cover_img, ',', group_concat(activity.cover_img SEPARATOR ',')) as cover_imgs,    concat(active_group.main_aid, ',', group_concat(active_attach.aid SEPARATOR ',')) as aids,    active_group.gid,    user_info.img,    user_info.name,     user_info.uid     from    active_group left join active_attach on active_group.gid=active_attach.gid left join user_info on user_info.uid = active_group.uid left join activity on active_attach.aid = activity.aid, (select cover_img from activity where activity.aid=".$_GET['aid'].") main_cover where    active_group.joinable=1 and active_group.uid<>1 and active_group.main_aid=".$_GET['aid']."    group by active_group.gid, user_info.img, user_info.name, user_info.uid order by  active_group.create_tm desc";
	//echo $sql_str;
 	select($sql_str);
}

function showActTable()
{
	if (strlen($_GET['gid']) == 0){
	echo "Missing parameter gid";
		return;
	}

	$sql_str = "select activity.aid, activity.cover_img, activity.title, activity.category, activity.tm, IFNULL(active_group.phone, '没有公开') as phone, active_group.info, IFNULL(active_group.summary, '精彩活动安排') as summary, user_info.name, user_info.img from active_group  left join active_attach on active_attach.gid=".$_GET['gid']." and active_attach.gid=active_group.gid, user_info, activity where active_group.gid=".$_GET['gid']." and active_group.uid=user_info.uid and (active_group.main_aid=activity.aid or active_attach.aid=activity.aid) group by  activity.aid, activity.cover_img, activity.title, activity.category, activity.tm, phone, summary, user_info.name, user_info.img,active_group.info";
	//echo $sql_str;
 	select($sql_str);
}

function showMessage()
{
	if (strlen($_GET['gid']) == 0 and strlen($_GET['uid']) == 0){
	echo "Missing parameter gid";
		return;
	}

	$sql_str = "select participate.gid, user_info.uid, user_info.name, user_info.img, participate.approved, IFNULL(active_group.summary, '精彩活动安排') as summary from  participate, user_info, active_group where  participate.gid = ".$_GET['gid']." and active_group.gid =".$_GET['gid']." and participate.uid=user_info.uid";

	if (strlen($_GET['uid']) > 0)
		$sql_str="select participate.gid, user_info.uid, user_info.name, user_info.img, participate.approved, IFNULL(active_group.summary, '精彩活动安排') as summary from participate, user_info, active_group where active_group.uid ='".$_GET['uid']."' and participate.gid = active_group.gid and participate.uid=user_info.uid order by participate.approved asc,participate.tm desc";
	//echo $sql_str;
 	select($sql_str);
}

function showApp()
{
	if (strlen($_GET['uid']) == 0){
		echo "Missing parameter uid";
		return;
	}

	$sql_str = "select * from ( select  active_group.gid,  participate.approved,  user_info.img,  user_info.name, participate.tm from  active_group left join participate on participate.gid = active_group.gid,  user_info  where  participate.uid='".$_GET['uid']."' and active_group.uid = user_info.uid and user_info.uid<>1 union all select  active_group.gid,  null, user_info.img,  user_info.name, active_group.create_tm as tm from  active_group, user_info  where  (active_group.uid='".$_GET['uid']."') and active_group.uid = user_info.uid and user_info.uid<>1 ) as t order by tm desc";

	if (strlen($_GET['gid']) != 0)
		$sql_str = "select approved from participate where gid='".$_GET['gid']."' and uid='".$_GET['uid']."'";
	//echo $sql_str;
 	select($sql_str);
}

function showGather()
{
	if (strlen($_GET['uid']) == 0){
		echo "Missing parameter uid";
		return;
	}

	$sql_str = "select * from gather where status=1 and uid = '".$_GET['uid']."' order by tm desc";

	//echo $sql_str;
 	select($sql_str);
}

function comment()
{
	if (strlen($_GET['uid']) == 0 or strlen($_GET['aid'])==0 or strlen($_GET['cmt'])==0){
		echo "Missing parameter uid";
		return;
	}

	$sql_str = "insert into comment(uid, aid, cmt)values('".$_GET['uid']."', '".$_GET['aid']."', '".$_GET['cmt']."')";
	select($sql_str);
}

function delGather()
{
	if (strlen($_GET['wid']) == 0)
	{
		echo "Missing parameter wid";
		return;
	}

	$sql_str = "update gather set status = 0 where wid=".$_GET['wid'];
	select($sql_str);
}

function gather()
{
	if (strlen($_GET['uid']) == 0 or strlen($_GET['addr'])==0 or strlen($_GET['longtitude'])==0 or strlen($_GET['latitude'])==0 ){
		echo "Missing parameter uid";
		return;
	}

	$sql_str = "insert ignore into gather(uid, addr, longtitude, latitude, name)values('".$_GET['uid']."', '".urldecode($_GET['addr'])."', ".$_GET['longtitude'].",".$_GET['latitude'].", '".urldecode($_GET['name'])."')";
echo $sql_str;
	select($sql_str);
}

function group()
{
	if (strlen($_GET['param']) == 0)
	{
		echo "Missing parameter wid";
		return;
	}

	$sql_str = "insert into active_group (uid, main_aid, max_follower_no, joinable, phone, summary, info)values(".$_GET['param'].")";
	insert_group($sql_str);
}

function apply()
{
	if (strlen($_GET['param']) == 0)
	{
		echo "Missing parameter wid";
		return;
	}

	$sql_str = "insert into participate (uid, gid, approved)values(".$_GET['param'].", 1)";
	select($sql_str);
}

function approve()
{
	if (strlen($_GET['uid']) == 0 or strlen($_GET['gid']) == 0)
	{
		echo "Missing parameter uid";
		return;
	}

	$sql_str = "update participate set approved=2 where gid=".$_GET['gid']." and uid='".$_GET['uid']."'";
	select($sql_str);
}

function userinfo()
{
	if (strlen($_GET['param']) == 0 or  strlen($_GET['uid']) == 0 )
	{
		echo "Missing parameter wid";
		return;
	}

	$sql_str = "update user_info set ".$_GET['param']." where uid='".$_GET['uid']."'";
	select($sql_str);
}

function showUserInfo(){
	if (strlen($_GET['uid']) == 0){
		echo "Missing parameter uid";
                return;
	}
	global $conn;
        $sql_str="select * from user_info where uid='".$_GET['uid']."'";
        $result = mysql_query($sql_str, $conn);
        if (!$result) {
        die('Invalid query: ' . mysql_error());
        }
        $rows = array();
        while($r = mysql_fetch_array($result)) {
                $rows[] = $r;
        }

        if (count($rows) > 0){
                echo $_GET["cb"]."(".json_encode($rows).")";
                return;
        }

        $str = "insert into user_info(uid, name, gender, img)values('".$_GET['uid']."', '还没写名字', '男女不详', concat('icons/default_avatar.', mod(floor(rand()*100), 3),'.png'))";
        $result = mysql_query($str, $conn);
        select($sql_str);
}

function showUserID(){
	$sql_str="select UUID() as uid";
	select($sql_str);
}
mysql_close($conn);

?>
