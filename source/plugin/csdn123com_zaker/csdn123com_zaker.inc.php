<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
if (!isset($_G['cache']['plugin'])) {
	loadcache('plugin');
}
$csdn123_dingshi = $_G['cache']['plugin']['csdn123com_zaker']['csdn123_dingshi'];
$hzw_strict_dingshi = $_G['cache']['plugin']['csdn123com_zaker']['hzw_strict_dingshi'];
if ($csdn123_dingshi != 1) {
	echo '// ' . lang('plugin/csdn123com_zaker', 'cron_timing_acquisition');
	exit;
}
if(!defined('DISCUZ_VERSION')) {
	require_once './source/discuz_version.php';
}
if(DISCUZ_VERSION!='X2.5')
{
	$csdn123_cronid = C::t('common_cron')->get_cronid_by_filename('csdn123com_zaker:cron_csdn123com_zaker.inc.php');
}
if (DISCUZ_VERSION!='X2.5' && is_numeric($csdn123_cronid) && $csdn123_cronid > 0 && $hzw_strict_dingshi==1) {
	$csdn123_croninfo = C::t('common_cron')->fetch($csdn123_cronid);
	if (is_numeric($csdn123_croninfo['nextrun']) && $csdn123_croninfo['nextrun'] > 0 && $csdn123_croninfo['nextrun'] > TIMESTAMP) {
		
		$RemainingSeconds=$csdn123_croninfo['nextrun'] - TIMESTAMP;
		echo '// ' . lang('plugin/csdn123com_zaker', 'cron_remaining_seconds') . $RemainingSeconds;
		exit;
	}
}
require './source/plugin/csdn123com_zaker/function_common.php';
$csdn123_cron = DB::fetch_first("SELECT * FROM " . DB::table('csdn123zaker_cron') . " ORDER BY catchtime ASC LIMIT 1");
if (is_numeric($csdn123_cron['catchtime'])) {
	$csdn123_diffTime = time() - $csdn123_cron['catchtime'];
	if ($csdn123_diffTime < 600) {
		echo '// ' .  lang('plugin/csdn123com_zaker', 'cron_interval_seconds') . (600 - $csdn123_diffTime);
		exit;
	}
}
$csdn123com_zaker_chk = DB::fetch_first("SELECT send_datetime FROM " . DB::table('csdn123zaker_news') . " ORDER BY send_datetime DESC LIMIT 1");
if (is_numeric($csdn123com_zaker_chk['send_datetime'])) {
	$csdn123_diffTime2 = time() - $csdn123com_zaker_chk['send_datetime'];
	if ($csdn123_diffTime2 < 600) {
		echo '// ' .  lang('plugin/csdn123com_zaker', 'cron_interval_seconds') . (600 - $csdn123_diffTime2);
		exit;
	}
}
$csdn123_firt_news = DB::fetch_first("SELECT * FROM " . DB::table('csdn123zaker_news') . " WHERE tid_aid=0 AND del=0 ORDER BY ID DESC LIMIT 1");
if (empty($csdn123_cron) == false && empty($csdn123_firt_news) == true) {
	DB::query("UPDATE " . DB::table('csdn123zaker_cron') . " SET catchnum=catchnum+1,catchtime=" . time() . " WHERE ID=" . dintval($csdn123_cron["ID"]));
	$keyword = $csdn123_cron['keyword'];
	$forum_portal = $csdn123_cron['forum_portal'];
	$fid = $csdn123_cron['fid'];
	$threadtypeid = $csdn123_cron['threadtypeid'];
	$portal_catid = $csdn123_cron['portal_catid'];
	$first_uid = $csdn123_cron['first_uid'];
	$reply_uid = $csdn123_cron['reply_uid'];
	$replaynum = $csdn123_cron['replaynum'];
	$display_link = $csdn123_cron['display_link'];
	$image_localized = $csdn123_cron['image_localized'];
	$image_center = $csdn123_cron['image_center'];
	$filter_image = $csdn123_cron['filter_image'];
	$pseudo_original = $csdn123_cron['pseudo_original'];
	$chinese_encoding = $csdn123_cron['chinese_encoding'];
	$views = $csdn123_cron['views'];
	$group_fid = $csdn123_cron['group_fid'];	
	$keyword=diconv($keyword,CHARSET,"UTF-8");
	$keyword=urlencode($keyword);
	if (!isset($_G['cache']['plugin'])) {
		loadcache('plugin');
	}
	$zakerUrlInterface=$_G['cache']['plugin']['csdn123com_zaker']['hzw_interface_url'];
	if(strlen($zakerUrlInterface)<20)
	{
		$zakerUrl = "http://www.myzaker.com/news/search_new.php?f=myzaker_com&keyword=" . $keyword;
	} else {
		$zakerUrlInterfaceArr=explode('##',$zakerUrlInterface);
		if(is_array($zakerUrlInterfaceArr))
		{
			$zakerUrl = $zakerUrlInterfaceArr[0];
			$zakerUrl = str_replace('%%keyword%%',$keyword,$zakerUrl);
		} else {
			echo '// ' .  lang('plugin/csdn123com_zaker', 'interface_error');
		}
	}
	$hzw_cookies = $_G['cache']['plugin']['csdn123com_zaker']['hzw_cookies'];		
	$hzw_cookies = trim($hzw_cookies);
	$htmlcode = ext_dfsockopen($zakerUrl,$hzw_cookies);	
	if (strlen($htmlcode) < 200) {
		echo '// ' .  lang('plugin/csdn123com_zaker', 'collection_result_null');
		exit;
	}
	$htmlcode = base64_encode($htmlcode);
	$api_server="http://discuz.csdn123.net/catch/zaker201711/trueTime.php";
	$api_server_parameter = array();
	$api_server_parameter['SN'] = '2018110313xqb6xP4lLq';
	$api_server_parameter['RevisionID'] = '77932';
	$api_server_parameter['RevisionDateline'] = '1513566001';
	$api_server_parameter['SiteUrl'] = 'http://discuz.lvtian.ren/';
	$api_server_parameter['ClientUrl'] = 'http://discuz.lvtian.ren/';
	$api_server_parameter['SiteID'] = 'B79DF1D2-7315-5955-C05E-AA4A01812A85';
	$api_server_parameter['siteuri'] = $_SERVER['HTTP_REFERER'];
	$api_server_parameter['QQID'] = 'F8086F0E-40C4-E27B-F1C5-0BA12066E43E';
	$api_server_parameter['S1teurl'] = $_SERVER['SERVER_NAME'];
	$api_server_parameter['safecode'] = 'b9f5ad3ff6f0bbe602f394ea7d7c8778';
	$api_server_parameter['SlteUrl'] = $_G['siteurl'];
	$api_server_parameter['ip'] = $_SERVER['REMOTE_ADDR'];
	$api_server_parameter['siteur1'] = 'http://' . $_SERVER['HTTP_HOST'];
	$api_server_parameter['htmlcode'] = $htmlcode;
	$api_server_parameter['fromurl'] = $zakerUrl;
	$htmlcode = dfsockopen($api_server, 0, $api_server_parameter);
	if (strlen($htmlcode) < 50) {
		$htmlcode = dfsockopen($api_server, 0, $api_server_parameter, '', FALSE, '', 15, TRUE, 'URLENCODE', FALSE);
	}
	if (strlen($htmlcode) < 50) {
		echo '// ' .  lang('plugin/csdn123com_zaker', 'collection_result_null');
		exit;
	}
	$htmlcode = preg_replace('/^\s+|\s+$/', '', $htmlcode);
	$htmlcode = base64_decode($htmlcode);
	$resultLink = dunserialize($htmlcode);
	if (is_array($resultLink) == FALSE) {
		echo '// ' .  lang('plugin/csdn123com_zaker', 'collection_result_null');
		exit;
	}
	foreach ($resultLink as $resultLinkItem) {
		$newsArr = array();
		$title = diconv($resultLinkItem['title'], 'UTF-8', CHARSET);
		$newsArr['title'] = daddslashes($title);
		$source_link = $resultLinkItem['url'];
		$newsArr['source_link'] = daddslashes($source_link);
		$newsArr['forum_portal'] = $forum_portal;
		$newsArr['fid'] = $fid;
		$newsArr['threadtypeid'] = $threadtypeid;
		$newsArr['portal_catid'] = $portal_catid;
		$newsArr['first_uid']=$first_uid;
		$newsArr['reply_uid']=$reply_uid;
		$newsArr['display_link'] = $display_link;
		$newsArr['image_localized'] = $image_localized;
		$newsArr['pseudo_original'] = $pseudo_original;
		$newsArr['chinese_encoding'] = $chinese_encoding;
		$newsArr['views'] = rand(1, $views);
		$newsArr['group_fid'] = $group_fid;
		$newsArr['intval_time']=$intval_time;
		$newsArr['filter_image']=$filter_image;
		$newsArr['image_center']=$image_center;
		$newsArr['replaynum']=$replaynum;
		$newsArr['release_time']=time();
		$chk = DB::fetch_first("SELECT * FROM " . DB::table('csdn123zaker_news') . " WHERE source_link='" . daddslashes($source_link) . "' LIMIT 1");
		$chk2 = DB::fetch_first("SELECT * FROM " . DB::table('csdn123zaker_news') . " WHERE title='" . daddslashes($title) . "' LIMIT 1");
		if (count($chk) == 0 && count($chk2) == 0) {
			DB::insert('csdn123zaker_news', $newsArr);
		}
	}
	echo '// ' .  lang('plugin/csdn123com_zaker', 'cron_collection_keyword');
} elseif (empty($csdn123_firt_news) == false) {
	require_once './source/function/function_forum.php';
	$status_code = send_thread($csdn123_firt_news['ID']);
	if ($status_code == 'ok') {
		DB::update('csdn123zaker_news', array('catch_way' => 'autocatch'), array('ID' => $csdn123_firt_news['ID']));
	}
	$tid_aid_rs = DB::fetch_first("SELECT tid_aid FROM " . DB::table('csdn123zaker_news') . " WHERE ID=" . $csdn123_firt_news['ID']);
	echo '// ' . lang('plugin/csdn123com_zaker', 'collection_success') . preview_url($csdn123_firt_news['forum_portal'], $tid_aid_rs['tid_aid']);
}
?>