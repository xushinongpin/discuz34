<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
if (!isset($_G['cache']['plugin'])) {
	loadcache('plugin');
}
$csdn123_dingshi = $_G['cache']['plugin']['csdn123_news']['csdn123_dingshi'];
$hzw_strict_dingshi = $_G['cache']['plugin']['csdn123_news']['hzw_strict_dingshi'];
$hzw_appid = $_G['cache']['plugin']['csdn123_news']['hzw_appid'];
if ($csdn123_dingshi != 1) {
	echo '// ' . lang('plugin/csdn123_news', 'cron_timing_acquisition');
	exit;
}
if(!defined('DISCUZ_VERSION')) {
	require_once './source/discuz_version.php';
}
if(DISCUZ_VERSION!='X2.5')
{
	$csdn123_cronid = C::t('common_cron')->get_cronid_by_filename('csdn123_news:cron_csdn123_news.inc.php');
}
if (DISCUZ_VERSION!='X2.5' && is_numeric($csdn123_cronid) && $csdn123_cronid > 0 && $hzw_strict_dingshi==1) {
	$csdn123_croninfo = C::t('common_cron')->fetch($csdn123_cronid);
	if (is_numeric($csdn123_croninfo['nextrun']) && $csdn123_croninfo['nextrun'] > 0 && $csdn123_croninfo['nextrun'] > TIMESTAMP) {
		
		$RemainingSeconds=$csdn123_croninfo['nextrun'] - TIMESTAMP;
		if(600 < $RemainingSeconds)
		{
			echo '// ' . lang('plugin/csdn123_news', 'cron_remaining_seconds') . $RemainingSeconds;
			exit;
		}
	}
}
require './source/plugin/csdn123_news/common.fun.php';
$csdn123_cron = DB::fetch_first("SELECT * FROM " . DB::table("csdn123zd_cron") . " ORDER BY catchtime ASC LIMIT 1");
if (is_numeric($csdn123_cron['catchtime'])) {
	$csdn123_diffTime = time() - $csdn123_cron['catchtime'];
	if ($csdn123_diffTime < 600) {
		echo '// ' .  lang('plugin/csdn123_news', 'cron_interval_seconds');
		exit;
	}
}
$csdn123_news_chk = DB::fetch_first("SELECT send_datetime FROM " . DB::table("csdn123zd_news") . " ORDER BY send_datetime DESC LIMIT 1");
if (is_numeric($csdn123_news_chk['send_datetime'])) {
	$csdn123_diffTime2 = time() - $csdn123_news_chk['send_datetime'];
	if ($csdn123_diffTime2 < 600) {
		echo '// ' .  lang('plugin/csdn123_news', 'cron_interval_seconds');
		exit;
	}
}
$csdn123_firt_news = DB::fetch_first("SELECT * FROM " . DB::table("csdn123zd_news") . " WHERE tid_aid=0 AND del=0 ORDER BY ID DESC LIMIT 1");
if (empty($csdn123_cron) == false && empty($csdn123_firt_news) == true) {
	DB::query("UPDATE " . DB::table("csdn123zd_cron") . " SET catchnum=catchnum+1,catchtime=" . time() . " WHERE ID=" . dintval($csdn123_cron["ID"]));
	$keyword = $csdn123_cron['keyword'];
	$forum_portal = $csdn123_cron['forum_portal'];
	$fid = $csdn123_cron['fid'];
	$threadtypeid = $csdn123_cron['threadtypeid'];
	$portal_catid = $csdn123_cron['portal_catid'];
	$uid = $csdn123_cron['uid'];
	$display_link = $csdn123_cron['display_link'];
	$image_localized = $csdn123_cron['image_localized'];
	$pseudo_original = $csdn123_cron['pseudo_original'];
	$chinese_encoding = $csdn123_cron['chinese_encoding'];
	$views = $csdn123_cron['views'];
	$group_fid = $csdn123_cron['group_fid'];
	$catchnum = $csdn123_cron['catchnum'];
	$catchnum = $catchnum % 6;
	if($catchnum==5)
	{
		
		$ruleRs = DB::fetch_first('SELECT * FROM ' . DB::table('csdn123zd_rule') . ' ORDER BY catch_time DESC LIMIT 1');	
		$updateArr = array();
		$updateArr['catch_num'] = $ruleRs['catch_num'] + 1;
		$updateArr['catch_time'] = time();
		DB::update('csdn123zd_rule',$updateArr,array('ID'=>$ruleRs['ID']));
		$step1Str = $ruleRs['step1'];
		$step1Str = base64_decode($step1Str);
		$step1Arr = dunserialize($step1Str);	
		$htmlcode = ext_dfsockopen($step1Arr['start_url'],$step1Arr['inputcookie']);
		if(strlen($htmlcode)<50)
		{
			$pureHandUrl = $server_url . '&purehand=yes&formhash=' . FORMHASH . '&start_url=' . urlencode($step1Arr['start_url']) . '&start_run=' . $id;
			echo '// ' .  lang('plugin/csdn123_news', 'fail_purehand');
			exit;
		}	
		$dataArr = array();
		$dataArr['htmlcode'] = $htmlcode;
		$dataArr['start_url'] = $step1Arr['start_url'];
		$dataArr['url_regex'] = $step1Arr['url_regex'];
		$dataArr['url_regex2'] = $step1Arr['url_regex2'];
		$dataArr['hzw_appid'] = $hzw_appid;
		$dataArr['hzw_sign'] = hzw_sign($dataArr);
		$dataStr = serialize($dataArr);
		$dataStr = base64_encode($dataStr);
		$resultStr = dfsockopen('http://www.csdn123.net/zd_version/zd9_5/url_step1.php',0,array('dataStr'=>$dataStr));
		$resultStr = base64_decode($resultStr);
		$resultLink = dunserialize($resultStr);
		if(is_array($resultLink))
		{
			$step3Str = $ruleRs['step3'];
			$step3Str = base64_decode($step3Str);
			$step3Arr = dunserialize($step3Str);
			$y=0;
			$z=0;
			foreach($resultLink as $resultLinkItem)
			{
				$newsArr=array();
				$newsArr['title']=daddslashes($resultLinkItem);
				$source_link=$resultLinkItem;
				$newsArr['source_link']=daddslashes($source_link);
				$newsArr['fromurl']=daddslashes($source_link);
				$newsArr['rule_id']=intval($ruleRs['ID']);
				$newsArr['model_catch']=2;
				$newsArr['forum_portal']=$step3Arr['forum_portal'];
				$newsArr['fid']=$step3Arr['fid'];
				$newsArr['threadtypeid']=$step3Arr['threadtypeid'];
				$newsArr['portal_catid']=$step3Arr['portal_catid'];
				$newsArr['uid']=getOneUid($step3Arr['uid']);
				$newsArr['display_link']=$step3Arr['display_link'];
				$newsArr['image_localized']=$step3Arr['image_localized'];
				$newsArr['pseudo_original']=$step3Arr['pseudo_original'];
				$newsArr['chinese_encoding']=$step3Arr['chinese_encoding'];
				$newsArr['views']=rand(1,$step3Arr['views']);
				$newsArr['group_fid']=$step3Arr['group_fid'];
				$release_time = strtotime($step3Arr['release_time']);
				if(is_numeric($release_time)==false)
				{
					$release_time = time();
				}
				$release_time = $release_time-rand(-1800,1800);
				$newsArr['release_time'] = $release_time;
				$chk = DB::fetch_first("SELECT * FROM " . DB::table('csdn123zd_news') . " WHERE source_link='" . daddslashes($source_link) . "' LIMIT 1");
				if (count($chk) == 0) {
					DB::insert('csdn123zd_news', $newsArr);
					$y++;
				} else {
					$z++;
				}			
			}
			$succeed_catch_rule = lang('plugin/csdn123_news', 'succeed_catch_rule');
			$x = $y + $z;
			$succeed_catch_rule = str_replace('x',$x,$succeed_catch_rule);
			$succeed_catch_rule = str_replace('y',$y,$succeed_catch_rule);
			$succeed_catch_rule = str_replace('z',$z,$succeed_catch_rule);
			$pendingUrl = 'action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123_news&pmod=pending';
			echo '// ' .  $succeed_catch_rule;
			exit;
			
		} else {
			
			echo '// all_catch_empty';
			exit;
			
		}	

	
	} else {

		$htmlcode = catch_data_source($keyword, $catchnum);
		if (is_array($htmlcode) == false) {
			echo '// ' .  lang('plugin/csdn123_news', 'collection_result_null');
			exit;
		}
		$api_server = "http://www.csdn123.net/zd_version/zd9_5/trueTime.php";
		$api_server_parameter = array();
		$api_server_parameter['siteurl'] = $_G['siteurl'];
		$api_server_parameter['htmlcode'] = base64_encode($htmlcode['htmlcode']);
		$api_server_parameter['catchUrl'] = urlencode($htmlcode['catchUrl']);
		$api_server_parameter['hzw_appid'] = $hzw_appid;
		$api_server_parameter['hzw_sign'] = hzw_sign($api_server_parameter);
		$htmlcode = dfsockopen($api_server, 0, $api_server_parameter);
		if (strlen($htmlcode) < 50) {
			$htmlcode = dfsockopen($api_server, 0, $api_server_parameter, '', FALSE, '', 15, TRUE, 'URLENCODE', FALSE);
		}
		if (strlen($htmlcode) < 50) {
			echo '// ' .  lang('plugin/csdn123_news', 'collection_result_null');
			exit;
		}
		$htmlcode = base64_decode($htmlcode);		
		$resultLink = dunserialize($htmlcode);
		if (is_array($resultLink) == FALSE) {
			echo '// ' .  lang('plugin/csdn123_news', 'collection_result_null');
			exit;
		}
		foreach ($resultLink as $resultLinkItem) {
			
			$newsArr = array();
			$title = diconv($resultLinkItem['title'], 'UTF-8', CHARSET);
			$title = dhtmlspecialchars($title);
			$newsArr['title'] = daddslashes($title);
			$source_link = $resultLinkItem['url'];
			$newsArr['source_link'] = daddslashes($source_link);
			$newsArr['forum_portal'] = $forum_portal;
			$newsArr['fid'] = $fid;
			$newsArr['threadtypeid'] = $threadtypeid;
			$newsArr['portal_catid'] = $portal_catid;
			$newsArr['uid'] = getOneUid($uid);
			$newsArr['display_link'] = $display_link;
			$newsArr['image_localized'] = $image_localized;
			$newsArr['pseudo_original'] = $pseudo_original;
			$newsArr['chinese_encoding'] = $chinese_encoding;
			$newsArr['views'] = rand(1, $views);
			$newsArr['group_fid'] = $group_fid;
			$newsArr['release_time'] = time() - rand(0, 1800);
			if(!empty($resultLinkItem['fromurl']) && strlen($resultLinkItem['fromurl'])>8)
			{
				$newsArr['fromurl'] = daddslashes($resultLinkItem['fromurl']);
			}
			$chk = DB::fetch_first("SELECT * FROM " . DB::table('csdn123zd_news') . " WHERE source_link='" . daddslashes($source_link) . "' LIMIT 1");
			$chk2 = DB::fetch_first("SELECT * FROM " . DB::table('csdn123zd_news') . " WHERE title='" . daddslashes($title) . "' LIMIT 1");
			if (count($chk) == 0 && count($chk2) == 0) {
				DB::insert('csdn123zd_news', $newsArr);
			}
			
		}
		echo '// ' .  lang('plugin/csdn123_news', 'cron_collection_keyword');		
		
	}	

} elseif (empty($csdn123_firt_news) == false) {
	require_once './source/function/function_forum.php';
	$status_code = send_thread($csdn123_firt_news['ID']);
	if ($status_code == 'ok') {
		DB::update('csdn123zd_news', array('catch_way' => 'autocatch'), array('ID' => $csdn123_firt_news['ID']));
	}
	$tid_aid_rs = DB::fetch_first("SELECT tid_aid FROM " . DB::table('csdn123zd_news') . " WHERE ID=" . $csdn123_firt_news['ID']);
	echo '// ' . lang('plugin/csdn123_news', 'collection_success') . preview_url($csdn123_firt_news['forum_portal'], $tid_aid_rs['tid_aid']);
}
?>