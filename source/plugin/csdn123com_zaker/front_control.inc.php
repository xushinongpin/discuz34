<?php if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
} 
if (empty($_GET['hzwzaker_fromurl'])) {
    echo 'no';
    exit;
} 
if (!isset($_G['cache']['plugin'])) {
    loadcache('plugin');
} 
$args = $_G['cache']['plugin']['csdn123com_zaker']['csdn123_uid'];
if (strpos($args, ',') === false) {
    $argsArr = array($args);
} else {
    $argsArr = explode(',', $args);
} 
if (in_array($_G['uid'], $argsArr)) {
    require './source/plugin/csdn123com_zaker/function_common.php';
    $fromurl = urldecode($_GET['hzwzaker_fromurl']);
	if (strpos($fromurl, 'myzaker.com') === false) {
        echo 'no1';
        exit;
    }
	$chk = DB::fetch_first("SELECT ID,tid_aid,forum_portal FROM " . DB::table('csdn123zaker_news') . " WHERE source_link='" . $fromurl . "'");
    if (count($chk) > 0 && $chk['tid_aid'] > 0) {
        echo preview_url($chk['forum_portal'], $chk['tid_aid']);
        exit;
    }
    $replaynum = $_GET['hzwzaker_replaynum'];
    if (empty($_GET['hzwzaker_fid'])) {
        $fid = 0;
    } else {
        $fid = $_GET['hzwzaker_fid'];
    } 
    if (empty($_GET['hzwzaker_typeid'])) {
        $typeid = 0;
    } else {
        $typeid = $_GET['hzwzaker_typeid'];
    } 
    if (empty($_GET['hzwzaker_catid'])) {
        $portal = 0;
    } else {
        $portal = $_GET['hzwzaker_catid'];
    } 
    $first_uid = $_GET['hzwzaker_first_uid'];
    $reply_uid = $_GET['hzwzaker_reply_uid'];
    $weiyanchang = $_GET['hzwzaker_weiyanchang'];
    $intval_time = $_GET['hzwzaker_intval_time'];
    $simplified = $_GET['hzwzaker_simplified'];
    $views = $_GET['hzwzaker_views'];
    $forum_portal = $_GET['hzwzaker_forum_portal'];     
    if (count($chk) == 0) {
        $threadValue = array();
		$title=md5(fromurl) . 'temporary title';
		$threadValue['title'] = daddslashes($title);
        $threadValue['source_link'] = daddslashes($fromurl);
        $threadValue['replaynum'] = intval($replaynum);
        $threadValue['fid'] = intval($fid);
        $threadValue['threadtypeid'] = intval($typeid);
        $threadValue['portal_catid'] = intval($portal);
        $threadValue['replaynum'] = intval($replaynum);
        $threadValue['first_uid'] = intval($first_uid);
        $threadValue['reply_uid'] = daddslashes($reply_uid);
        $threadValue['pseudo_original'] = intval($weiyanchang);
        $threadValue['intval_time'] = intval($intval_time);
        $threadValue['chinese_encoding'] = intval($simplified);
        $threadValue['views'] = intval($views);
        $threadValue['forum_portal'] = daddslashes($forum_portal);
        $threadValue['display_link'] = 0;
        $threadValue['image_localized'] = 1;
        $threadValue['filter_image'] = 0;	
        $threadValue['image_center'] = 0;		
        $threadValue['release_time'] = time();
        $threadValue['group_fid'] = intval($fid);		
        $ID = DB::insert('csdn123zaker_news', $threadValue, true);
    } else {
        $ID = $chk["ID"];
    } 
    require_once './source/function/function_forum.php';
    if (!defined('DISCUZ_VERSION')) {
        require './source/discuz_version.php';
    } 
    $recode = send_thread($ID);
    if ($recode == 'ok') {
        $chk = DB::fetch_first("SELECT ID,tid_aid,forum_portal FROM " . DB::table('csdn123zaker_news') . " WHERE ID=" . intval($ID));
        if ($chk['tid_aid'] > 0) {
            echo preview_url($chk['forum_portal'], $chk['tid_aid']);
        } else {
            echo "no2";
        } 
    } 
} 
