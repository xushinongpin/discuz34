<?php
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$user_list = DB::fetch_all('SELECT * FROM ' . DB::table('csdn123qqnews_reguser') . ' ORDER BY uid DESC');
foreach ($user_list as $uidvalue) {
	$uidstr = $uidvalue['uid'] . ',' . $uidstr;
}
$uidstr = substr($uidstr, 0, -1);
include template('csdn123com_qqnews:exportVest');
