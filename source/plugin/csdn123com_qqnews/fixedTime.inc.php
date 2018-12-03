<?php
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$server_url='action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123com_qqnews&pmod=fixedTime';
$regvest_url='?action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123com_qqnews&pmod=adminVest&subpmod=regvest&formhash=' . FORMHASH;
require './source/plugin/csdn123com_qqnews/function_common.php';
if($_GET['formhash'] == FORMHASH && !empty($_GET['csdn123_agrs']) && $_GET['csdn123_agrs']=='yes')
{
	if(empty($_GET['keyword']))
	{
		cpmsg("csdn123com_qqnews:keywords_empty","","error");
	} else {
		$keyword=$_GET['keyword'];
	}
	if(empty($_GET['first_uid']))
	{
		cpmsg("csdn123com_qqnews:uid_error","","error");
	} else {
		$first_uid=$_GET['first_uid'];
	}
	if(empty($_GET['reply_uid']))
	{
		cpmsg("csdn123com_qqnews:uid_error","","error");
	} else {
		$reply_uid=$_GET['reply_uid'];
	}	
	if(preg_match('/[a-z]/i',$first_uid)==1 || preg_match('/[a-z]/i',$reply_uid)==1)
	{
		cpmsg("csdn123com_qqnews:uid_error","","error");
	}	
	$forum_portal=daddslashes($_GET['forum_portal']);
	$fid=intval($_GET['fid']);
	$threadtypeid=intval($_GET['threadtypeid']);
	$portal_catid=intval($_GET['portal_catid']);
	$display_link=intval($_GET['display_link']);
	$image_localized=intval($_GET['image_localized']);
	$pseudo_original=intval($_GET['pseudo_original']);
	$chinese_encoding=intval($_GET['chinese_encoding']);
	$group_fid=intval($_GET['group_fid']);
	$replaynum=intval($_GET['replaynum']);
	$first_uid=daddslashes($_GET['first_uid']);
	$reply_uid=daddslashes($_GET['reply_uid']);
	$intval_time=intval($_GET['intval_time']);
	$filter_image=intval($_GET['filter_image']);
	$image_center=intval($_GET['image_center']);
	$views=intval($_GET['views']);
	$cronArr=array();
	$cronArr['keyword']=daddslashes($keyword);
	$cronArr['forum_portal']=$forum_portal;
	$cronArr['fid']=$fid;
	$cronArr['threadtypeid']=$threadtypeid;
	$cronArr['portal_catid']=$portal_catid;
	$cronArr['first_uid']=$first_uid;
	$cronArr['reply_uid']=$reply_uid;
	$cronArr['display_link']=$display_link;
	$cronArr['image_localized']=$image_localized;
	$cronArr['pseudo_original']=$pseudo_original;
	$cronArr['chinese_encoding']=$chinese_encoding;
	$cronArr['image_localized']=$image_localized;
	$cronArr['pseudo_original']=$pseudo_original;
	$cronArr['views']=$views;
	$cronArr['group_fid']=$group_fid;
	$cronArr['intval_time']=$intval_time;
	$cronArr['filter_image']=$filter_image;
	$cronArr['image_center']=$image_center;
	$cronArr['replaynum']=$replaynum;
	if(empty($_GET['update']))
	{
		DB::insert('csdn123qqnews_cron', $cronArr);
	} else {
		$news_id=intval($_GET['news_id']);
		DB::update('csdn123qqnews_cron',$cronArr,'ID=' . $news_id);
	}
	$succeed_url='action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123com_qqnews&pmod=fixedTime';
	cpmsg("csdn123com_qqnews:succeed",$succeed_url,"succeed");

} elseif(!empty($_GET['off_timer']) && $_GET['off_timer']=='yes' && $_GET['formhash'] == FORMHASH) {
	
	C::t('common_pluginvar')->update_by_variable($pluginid, 'csdn123_dingshi', array('value'=>0));
	updatecache(array('plugin', 'setting', 'styles'));
	cleartemplatecache();
	cpmsg("csdn123com_qqnews:succeed",$server_url,"succeed");

} elseif(!empty($_GET['del']) && is_numeric($_GET['del']) && $_GET['formhash'] == FORMHASH) {
	
	$id = intval($_GET['del']);
	DB::delete('csdn123qqnews_cron','ID=' . $id);
	cpmsg("csdn123com_qqnews:succeed",$server_url,"succeed");

} elseif(!empty($_GET['update_id']) && is_numeric($_GET['update_id']) && $_GET['formhash'] == FORMHASH) {
	
	$update_id = $_GET['update_id'];
	$update_id = intval($update_id);
	$update_rs=DB::fetch_first("SELECT * FROM " . DB::table('csdn123qqnews_cron') . " WHERE ID=" . $update_id);
	$typeclassArr = C::t('forum_threadclass')->fetch_all_by_fid($update_rs['fid']);
	require_once libfile('function/forumlist');
	require_once libfile('function/portalcp');
	require_once libfile('function/group');
	$grouplistArr=grouplist('displayorder',false,100);
	include template('csdn123com_qqnews:fixedTimeUpdate');

} elseif(!empty($_GET['clears_all']) && $_GET['clears_all']=='yes' && $_GET['formhash'] == FORMHASH) {
	
	DB::delete('csdn123qqnews_cron','ID>0');
	cpmsg('csdn123com_qqnews:succeed', $server_url, 'succeed');
		
} elseif(!empty($_GET['fixedTimeAdd']) && $_GET['fixedTimeAdd']=='yes' && $_GET['formhash'] == FORMHASH) {

	require_once libfile('function/forumlist');
	require_once libfile('function/portalcp');
	require_once libfile('function/group');
	$grouplistArr=grouplist('displayorder',false,100);
	$release_time=date('Y-m-d H:i:s',time());	
	include template('csdn123com_qqnews:fixedTimeAdd');
	
} else{
	
	if (!isset($_G['cache']['plugin'])) {
		loadcache('plugin');
	}
	$hzw_startcron = $_G['cache']['plugin']['csdn123com_qqnews']['csdn123_dingshi'];
	if($hzw_startcron != 1)
	{
		echo '<div style="text-align:center;margin:64px;"><a href="?action=plugins&operation=config&do=' . $pluginid . '" style="font-size:24px;color:red;">' . lang('plugin/csdn123com_qqnews', 'open_timing_acquisition') . '</a></div>';
		
	} else {
		
		if(!defined('DISCUZ_VERSION')) {
			require_once './source/discuz_version.php';
		}
		if(DISCUZ_VERSION!='X2.5')
		{
			$csdn123_cronid = C::t('common_cron')->get_cronid_by_filename('csdn123com_qqnews:cron_csdn123com_qqnews.inc.php');
			$csdn123_cronUrl=$_G['siteurl'] . ADMINSCRIPT . '?action=misc&operation=cron&edit=' . $csdn123_cronid;
		}
		$pendingUrl='action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123com_qqnews&pmod=pending';
		$cronList=DB::fetch_all("SELECT * FROM " . DB::table('csdn123qqnews_cron') . " ORDER BY catchtime DESC");
		$pending_count = lang('plugin/csdn123com_qqnews', 'pending_count');
		$newsCount = DB::result_first('SELECT count(*) FROM ' . DB::table('csdn123qqnews_news') . ' WHERE tid_aid<=0 and del=0');
		$pending_count = str_replace('x',$newsCount,$pending_count);
		include template('csdn123com_qqnews:fixedTime');
	
	}
	
}
?>