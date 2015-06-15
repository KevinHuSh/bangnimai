drop database jijiehao;
CREATE DATABASE jijiehao;
use jijiehao;

CREATE TABLE activity (
aid INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
title VARCHAR(50) NOT NULL,
category VARCHAR(30) NOT NULL,
district VARCHAR(50),
addr VARCHAR(100),
detail VARCHAR(500),
tm TIMESTAMP,
longtitude float,
latitude float,
follower_no int,
cover_img VARCHAR(1000)
)  ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE comment(
uid  VARCHAR(100),
aid INT(6),
cmt VARCHAR(100),
tm TIMESTAMP,
index(aid)
)  ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE user_info(
uid VARCHAR(100) PRIMARY KEY, 
name VARCHAR(30),
psw VARCHAR(30),
gender VARCHAR(3),
img VARCHAR(100),
last_login TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

insert into user_info(uid)values('1');

drop table IF EXISTS active_group;
CREATE TABLE active_group(
gid INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
main_aid INT(6),
uid  VARCHAR(100) default "1",
max_follower_no int,
phone VARCHAR(20),
info VARCHAR(100),
joinable int default 1
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

create table active_attach(
gid INT(6),
aid int(6),
index(gid)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

create table participate(
gid INT(6) ,
uid VARCHAR(100),
tm TIMESTAMP,
approved int,
index(gid)
)  ENGINE=InnoDB DEFAULT CHARSET=UTF8;

drop view IF EXISTS v_comment_count;
CREATE VIEW v_comment_count as
select 
active_group.gid,
count(cmt) as cmt_count
from 
active_group 
left join active_attach on active_group.gid=active_attach.gid
left join comment on active_attach.aid = comment.aid or active_group.main_aid=comment.aid
group by active_group.gid;

drop view IF EXISTS v_top3_active;
CREATE VIEW v_top3_active as
select 
active_group.gid,
concat(activity.cover_img, ",", group_concat(a1.cover_img SEPARATOR ",")) as imgs,
concat(activity.category, " +", group_concat(a1.category SEPARATOR " +")) as title
from 
active_group
left join activity on active_group.main_aid=activity.aid 
left join active_attach on active_group.gid = active_attach.gid
left join activity a1 on a1.aid = active_attach.aid
group by active_group.gid, activity.cover_img
;
select * from v_top3_active;

drop view IF EXISTS v_activity;
CREATE VIEW v_activity as 
select 
active_group.gid, 
active_group.main_aid,
v_top3_active.imgs, 
activity.follower_no, 
user_info.uid, 
user_info.name, 
user_info.img,
v_top3_active.title as title,
activity.tm, 
activity.longtitude,
activity.latitude,
IFNULL(v_comment_count.cmt_count, 0) as cmt_count,
activity.detail,
activity.addr
from 
active_group
left join v_comment_count on v_comment_count.gid = active_group.gid
left join user_info on active_group.uid=user_info.uid,
v_top3_active,
activity
where 
active_group.main_aid=activity.aid and
v_top3_active.gid = active_group.gid
;
select * from v_activity limit 10;

drop view IF EXISTS v_main_act_info;
CREATE VIEW v_main_act_info as
select
active_group.gid,
active_group.main_aid,
activity.aid,
activity.cover_img,
activity.follower_no,
activity.addr,
activity.detail,
user_info.uid,
user_info.name,
user_info.img,
v_comment_count.cmt_count
from 
active_group
left join v_comment_count on v_comment_count.gid = active_group.gid
left join user_info on active_group.uid=user_info.uid,
activity
where 
active_group.main_aid=activity.aid;


insert into activity(title, category, district,addr,detail,tm,longtitude,latitude,follower_no,cover_img)values('很高兴遇见你', '下午茶', '中山公园', '定西路1273号4楼(近安化路) ', '三层甜品拼盘口味一级棒', '2015/11/11', '3534231', '242334', '9', 'http://i1.s2.dpfile.com/pc/mc/2fef89a861e1d9fa7483841dc327c092(450c280)/aD0yODAmaz0vcGMvbWMvMmZlZjg5YTg2MWUxZDlmYTc0ODM4NDFkYzMyN2MwOTImbG9nbz0wJm09YyZ3PTQ1MA.cf9448f5a191268c780b5861f4e9f91e/thumb.jpg');

insert into activity(title, category, district,addr,detail,tm,longtitude,latitude,follower_no,cover_img)values('查厘士', '下午茶', '中山公园', '定西路1273号4楼(近安化路) ', '五店限用', '2015/11/11', '3534231', '242334', '19', 'http://i1.s2.dpfile.com/pc/mc/17378603f970b8f66d066b8301e8e812(450c280)/aD0yODAmaz0vcGMvbWMvMTczNzg2MDNmOTcwYjhmNjZkMDY2YjgzMDFlOGU4MTImbG9nbz0wJm09YyZ3PTQ1MA.6fb832234d2fc982ac6c5ed1e3bf805e/thumb.jpg');

insert into activity(title, category, district,addr,detail,tm,longtitude,latitude,follower_no,cover_img)values('绿茶餐厅(中山公园店)','聚餐', '中山公园', '长宁路823号巴黎春天2楼(近地铁2号线中山公园站5号出 ', '我也不知道这个好不好吃 你来试试吧', '2015/11/11', '3534544', '2423311', '19', 'http://i2.dpfile.com/pc/82656d378658cc0e69af20133e3938f1/29619112_m.jpg');

insert into activity(title, category, district,addr,detail,tm,longtitude,latitude,follower_no,cover_img)values('蝶舍', '下午茶', '静安寺', '胶州路149弄2-2号1楼(近北京西路)', '令人不安的安宁着终结', '2015/11/11', '3541425', '232354', '39', 'http://i3.s2.dpfile.com/pc/mc/a6143b8d355b32464d5bef9c94ae1c13(450c280)/aD0yODAmaz0vcGMvbWMvYTYxNDNiOGQzNTViMzI0NjRkNWJlZjljOTRhZTFjMTMmbG9nbz0wJm09YyZ3PTQ1MA.48c4a6b483739679a6f4530faa8978b5/thumb.jpg');

insert into activity(title, category, district,addr,detail,tm,longtitude,latitude,follower_no,cover_img)values('水货SEAHOOD(长宁龙之梦店)', '聚餐', '中山公园', '定西路1273号4楼(近安化路) ', '靠海吃海', '2015/11/11', '3534231', '242334', '42', 'http://i3.s2.dpfile.com/pc/ad7416070be33eafc4953eef97810c56(240c180)/thumb.jpg');

insert into activity(title, category, district,addr,detail,tm,longtitude,latitude,follower_no,cover_img)values('末日崩塌', '电影', '中山公园', '定西路1273号4楼(近安化路) ', '男主和女主拼胸，还有天理么?', '2015/11/11', '3534231', '242334', '9', 'http://img5.gewara.com/cw270h360/images/movie/201505/s_4b486449_14d7bf184ee__7b5c.jpg');

insert into activity(title, category, district,addr,detail,tm,longtitude,latitude,follower_no,cover_img)values('复仇者联盟2：奥创纪元', '电影','静安寺', '胶州路149弄2-2号1楼(近北京西路)', '土豪犯下的错，基友们帮着扛', '2015/11/11',  '3541425', '232354','9', 'http://img5.gewara.com/cw270h360/images/movie/201504/s_3fe11ad6_14d05656f13__7eb1.jpg');

insert into activity(title, category, district,addr,detail,tm,longtitude,latitude,follower_no,cover_img)values('松江大学城狼人杀', '桌游', '松江大学城', '文汇路746号2楼206室', '???', '2015/11/11', '4531231', '432334', '9', 'http://img3.douban.com/view/event_poster/large/public/f7987d0e3cf851b.jpg');

insert into activity(title, category, district,addr,detail,tm,longtitude,latitude,follower_no,cover_img)values('周五晚华师大附近狼人杀，快来嗨~', '桌游', '中山公园', '定西路12474号 ', '睡什么睡，起来嗨！', '2015/11/11', '3534211', '242314', '19', 'http://img3.douban.com/view/event_poster/large/public/6c2f9576bca6111.jpg');

insert into activity(title, category, district,addr,detail,tm,longtitude,latitude,follower_no,cover_img)values('长宁路小学足球聚会', '足球', '中山公园', '万航渡路2505弄 ', '黑旋风与白雪公主', '2015/11/11', '3534131', '242134', '9', 'http://pic9.nipic.com/20100830/668573_223804002911_2.jpg');


insert into user_info (uid, name, psw,gender,img,last_login)values("2", "悟饭", "hello123", "男 ", "http://ww3.sinaimg.cn/mw600/a00dfa2agw1esxle9msg3j20ci0g5abe.jpg", "201501111121");

insert into user_info (uid, name, psw,gender,img,last_login)values("3", "琪琪", "hello123", "女 ", "http://ww4.sinaimg.cn/mw600/a00dfa2agw1esxqqhf6p2j20f00min0d.jpg", "20150611102233");

insert into user_info (uid, name, psw,gender,img,last_login)values("4", "悟天", "hello123", "男 ", "http://ww1.sinaimg.cn/mw600/a00dfa2agw1esxqmyg89wj20f00820v5.jpg", "201511111111");

insert into user_info (uid, name, psw,gender,img,last_login)values("5", "布尔玛", "hello123", "女 ", "http://ww4.sinaimg.cn/mw600/a00dfa2agw1esxahslrpfj20eb0jg0u4.jpg", "20150411223622");
insert into user_info (uid, name, psw,gender,img,last_login)values("6", "悟空", "hello123", "男 ", "http://ww1.sinaimg.cn/mw600/a00dfa2agw1esxqmyg89wj20f00820v5.jpg", "20150911223622");


insert into active_group (gid,main_aid,uid, max_follower_no,phone,info,joinable)values("1","1", "1", "21", "13718999992 ", "没有蛀牙", "1");

insert into active_group (gid,main_aid,uid, max_follower_no,phone,info,joinable)values("2","1", "2", "11", "13718999392 ", "绝对没有蛀牙", "1");

insert into active_group (gid,main_aid,uid, max_follower_no,phone,info,joinable)values("3","3", "2", "211", "13718992992 ", "超级赛亚人的活动", "1");

insert into active_group (gid,main_aid,uid, max_follower_no,phone,info,joinable)values("4","5", "5", "21", "13718199992 ", "？？？", "0");

insert into active_group (gid,main_aid,uid, max_follower_no,phone,info,joinable)values("5","5", "", "21", "", "", "1");



insert into active_attach (gid,aid)values("1", "2");
insert into active_attach (gid,aid)values("1", "3");
insert into active_attach (gid,aid)values("2", "2");
insert into active_attach (gid,aid)values("3", "2");
insert into active_attach (gid,aid)values("4", "3");



insert into activity(title, category, district,addr,detail,tm,longtitude,latitude,follower_no,cover_img)values('三人行骨头王火锅(徐汇总店)', '聚餐', '徐家汇', '肇嘉浜路879号(近宛平南路) ', '三人行骨头王现已加入豪华套餐', '2015/11/11', '153', '142', '9', 'http://i3.dpfile.com/2011-08-22/9242945_m.jpg');

insert into activity(title, category, district,addr,detail,tm,longtitude,latitude,follower_no,cover_img)values('西堤厚牛排(上海天钥桥店)', '聚餐', '徐家汇', '天钥桥路315号4楼(南丹东路口) ', '全是肉 肉肉肉', '2015/11/11', '153', '143', '19', 'http://i3.dpfile.com/pc/0ee07991b3a0ab27244f8fe06c86d8ba/11870588_m.jpg');

insert into activity(title, category, district,addr,detail,tm,longtitude,latitude,follower_no,cover_img)values('[徐家汇] 橘子工房','下午茶', '徐家汇', '衡山路932号太平洋百货4楼(近华山路)', '我也不知道这个好不仅售85元，价值100元代金券，可叠加，不限时段通用！除午餐晚餐的特价菜品和下午茶套餐外全场通用，免费WiFi！', '2015/11/11', '151', '142', '19', 'http://i3.s2.dpfile.com/pc/mc/429b6b51a13ad3b79d5adbe880d712f8(450c280)/aD0yODAmaz0vcGMvbWMvNDI5YjZiNTFhMTNhZDNiNzlkNWFkYmU4ODBkNzEyZjgmbG9nbz0wJm09YyZ3PTQ1MA.01809e5e3c5911bee0941455f2829dde/thumb.jpg');

insert into activity(title, category, district,addr,detail,tm,longtitude,latitude,follower_no,cover_img)values('大蔬无界', '下午茶', '徐家汇', '衡山路932号太平洋百货4楼(近华山路)', '令人不安的安宁着终结', '2015/11/11', '151', '142', '39', 'http://i3.s2.dpfile.com/pc/mc/cd188ffe9d1645abfedc4872e6e3d852(450c280)/aD0yODAmaz0vcGMvbWMvY2QxODhmZmU5ZDE2NDVhYmZlZGM0ODcyZTZlM2Q4NTImbG9nbz0wJm09YyZ3PTQ1MA.f7f35b2c1b2431e5079ca954c96c43a9/thumb.jpg');

insert into activity(title, category, district,addr,detail,tm,longtitude,latitude,follower_no,cover_img)values('恋战伊甸园首家户外益智竞技主题公园6月14日免费测试场等你来战！！！', '会展', '中山公园', '定西路1273号4楼(近安化路) ', '烧死你的大脑', '2015/11/11', '3534231', '242334', '42', 'http://wimg.huodongxing.com/logo/201506/4285793520500/791984680043040_v2.jpg@!wmlogo');

insert into activity(title, category, district,addr,detail,tm,longtitude,latitude,follower_no,cover_img)values('老上海怀旧梭哈扑克', '桌游', '中山公园', '定西路1273号4楼(近安化路) ', '反正我不会', '2015/11/11', '3534231', '242334', '9', 'http://img3.douban.com/view/event_poster/large/public/bd34652ff4f9d13.jpg');

insert into activity(title, category, district,addr,detail,tm,longtitude,latitude,follower_no,cover_img)values('三宝粥铺(一号店金陵东路)', '聚餐','静安寺', '胶州路149弄2-2号1楼(近北京西路)', '就是喝个粥', '2015/11/11',  '3541425', '232354','9', 'http://i1.s2.dpfile.com/pc/e63a80cbdfa30f1eecf5d3a621dee8d2(700x700)/thumb.jpg');

insert into activity(title, category, district,addr,detail,tm,longtitude,latitude,follower_no,cover_img)values('三人行骨头王火锅(百联世茂店)', '聚餐', '松江大学城', '文汇路746号2楼206室', '???', '2015/11/11', '4531231', '432334', '9', 'http://i1.s2.dpfile.com/pc/131c975c4d33311c044bc8f98bebf007(240c180)/thumb.jpg');

insert into activity(title, category, district,addr,detail,tm,longtitude,latitude,follower_no,cover_img)values('港丽餐厅', '聚餐', '中山公园', '定西路121号 ', '随便吃点儿', '2015/11/11', '132', '211', '19', 'http://i1.s2.dpfile.com/pc/mc/f7e027e068739f432478adac5df68739(450c280)/aD0yODAmaz0vcGMvbWMvZjdlMDI3ZTA2ODczOWY0MzI0NzhhZGFjNWRmNjg3MzkmbG9nbz0wJm09YyZ3PTQ1MA.abaee74f648a572011ee16c54b4971ab/thumb.jpg');

insert into activity(title, category, district,addr,detail,tm,longtitude,latitude,follower_no,cover_img)values('天颐温泉', '温泉', '五角场', '翔殷路250弄 ', '这里鬼才有温泉啊！', '2015/11/11', '222', '212', '9', 'http://zmqnw-images.oss-cn-beijing.aliyuncs.com/product/2015/01/03/1453933594378a155.jpg');



insert into active_group (gid,main_aid,uid, max_follower_no,phone,info,joinable)values('6','8', '1', '21', '13892921009', '', '1');

insert into active_group (gid,main_aid,uid, max_follower_no,phone,info,joinable)values('7','7', '2', '21', '13892921011', '', '1');

insert into active_group (gid,main_aid,uid, max_follower_no,phone,info,joinable)values('8','6', '4', '21', '13892921021', '', '1');

insert into active_group (gid,main_aid,uid, max_follower_no,phone,info,joinable)values('9','5', '3', '21', '13892921041', '', '0');

insert into active_group (gid,main_aid,uid, max_follower_no,phone,info,joinable)values('10','3', '5', '21', '13892921051', '', '1');

insert into active_group (gid,main_aid,uid, max_follower_no,phone,info,joinable)values('11','9', '3', '21', '13892921411', '', '1');

insert into active_group (gid,main_aid,uid, max_follower_no,phone,info,joinable)values('12','3', '5', '21', '13893921008', '', '1');

insert into active_group (gid,main_aid,uid, max_follower_no,phone,info,joinable)values('13','6', '5', '21', '13812921008', '', '1');

insert into active_group (gid,main_aid,uid, max_follower_no,phone,info,joinable)values('14','1', '4', '21', '13892921008', 'no', '1');



insert into active_attach (gid,aid)values('12', '9');
insert into active_attach (gid,aid)values('12', '4');
insert into active_attach (gid,aid)values('12', '5');
insert into active_attach (gid,aid)values('12', '8');
insert into active_attach (gid,aid)values('8', '3');
insert into active_attach (gid,aid)values('8', '5');
insert into active_attach (gid,aid)values('5', '13');
insert into active_attach (gid,aid)values('5', '19');


insert into comment (uid,aid,cmt,tm)values('1','1','吃葡萄不吐葡萄皮','2011/11/11/1111');

insert into comment (uid,aid,cmt,tm)values('1','2','吃葡萄不吐葡萄皮','2011/11/11/1111');

insert into comment (uid,aid,cmt,tm)values('2','1','不吃葡萄到吐葡萄皮','2011/11/11/1111');

insert into comment (uid,aid,cmt,tm)values('2','4','南无阿弥陀佛','2011/11/11/1111');

insert into comment (uid,aid,cmt,tm)values('3','1','吃葡萄不吐葡萄皮','2011/11/11/1111');

insert into comment (uid,aid,cmt,tm)values('4','1','不要葡萄要葡挞','2011/11/11/1111');

insert into comment (uid,aid,cmt,tm)values('5','1','更喜欢芝士蛋挞','2011/11/11/1111');

insert into comment (uid,aid,cmt,tm)values('5','2','这个也不错','2011/11/11/1111');

insert into comment (uid,aid,cmt,tm)values('6','1','还是水晶香槟好','2011/11/11/1111');

insert into comment (uid,aid,cmt,tm)values('7','1','楼上都是二','2011/11/11/1111');
