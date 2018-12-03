<?php
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$server_url='action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123_news&pmod=fixedTime';
$regvest_url='?action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123_news&pmod=adminVest&subpmod=regvest&formhash=' . FORMHASH;
require './source/plugin/csdn123_news/common.fun.php';
hzw_authorization(false,$pluginid);
if($_GET['formhash'] == FORMHASH && !empty($_GET['csdn123_agrs']) && $_GET['csdn123_agrs']=='yes')
{
	if(empty($_GET['keyword']))
	{
		cpmsg("csdn123_news:keywords_empty","","error");
	} else {
		$keyword=$_GET['keyword'];
	}
	if(empty($_GET['uid']))
	{
		$uid=$_G['uid'];
	} else {
		$uid=$_GET['uid'];
	}	
	if(preg_match('/[a-z]/i',$uid)==1)
	{
		cpmsg("csdn123_news:uid_error","","error");
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
	$views=intval($_GET['views']);
	$cronArr=array();
	$cronArr['keyword']=daddslashes($keyword);
	$cronArr['forum_portal']=$forum_portal;
	$cronArr['fid']=$fid;
	$cronArr['threadtypeid']=$threadtypeid;
	$cronArr['portal_catid']=$portal_catid;
	$cronArr['uid']=$uid;
	$cronArr['display_link']=$display_link;
	$cronArr['image_localized']=$image_localized;
	$cronArr['pseudo_original']=$pseudo_original;
	$cronArr['chinese_encoding']=$chinese_encoding;
	$cronArr['views']=$views;
	$cronArr['group_fid']=$group_fid;
	if(empty($_GET['update']))
	{
		DB::insert('csdn123zd_cron', $cronArr);
	} else {
		$news_id=intval($_GET['news_id']);
		DB::update('csdn123zd_cron',$cronArr,'ID=' . $news_id);
	}
	$succeed_url='action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123_news&pmod=fixedTime';
	cpmsg("csdn123_news:succeed",$succeed_url,"succeed");

} elseif(!empty($_GET['off_timer']) && $_GET['off_timer']=='yes' && $_GET['formhash'] == FORMHASH) {
	
	C::t('common_pluginvar')->update_by_variable($pluginid, 'csdn123_dingshi', array('value'=>0));
	updatecache(array('plugin', 'setting', 'styles'));
	cleartemplatecache();
	cpmsg("csdn123_news:succeed",$server_url,"succeed");

} elseif(!empty($_GET['del']) && is_numeric($_GET['del']) && $_GET['formhash'] == FORMHASH) {
	
	$id = intval($_GET['del']);
	DB::delete('csdn123zd_cron','ID=' . $id);
	cpmsg("csdn123_news:succeed",$server_url,"succeed");

} elseif(!empty($_GET['update_id']) && is_numeric($_GET['update_id']) && $_GET['formhash'] == FORMHASH) {
	
	$update_id = $_GET['update_id'];
	$update_id = intval($update_id);
	$update_rs=DB::fetch_first("SELECT * FROM " . DB::table("csdn123zd_cron") . " WHERE ID=" . $update_id);
	$typeclassArr = C::t('forum_threadclass')->fetch_all_by_fid($update_rs['fid']);
	require_once libfile('function/forumlist');
	require_once libfile('function/portalcp');
	require_once libfile('function/group');
	$grouplistArr=grouplist('displayorder',false,100);
	include template('csdn123_news:fixedTimeUpdate');

} elseif(!empty($_GET['clears_all']) && $_GET['clears_all']=='yes' && $_GET['formhash'] == FORMHASH) {
	
	DB::delete('csdn123zd_cron','ID>0');
	cpmsg('csdn123_news:succeed', $server_url, 'succeed');
		
} elseif(!empty($_GET['fixedTimeAdd']) && $_GET['fixedTimeAdd']=='yes' && $_GET['formhash'] == FORMHASH) {

	require_once libfile('function/forumlist');
	require_once libfile('function/portalcp');
	require_once libfile('function/group');
	$grouplistArr=grouplist('displayorder',false,100);
	$release_time=date('Y-m-d H:i:s',time());
	if(empty($_GET['keyword_page']) || is_numeric($_GET['keyword_page'])==false)
	{
		$keyword_page=1;
	} else {
		$keyword_page=$_GET['keyword_page'];
	}
	$keywordArr=show_keywords($keyword_page);
	$preKeywordPage=$server_url .  "&fixedTimeAdd=yes&keyword_page=" . ($keyword_page - 1) . '&formhash=' . FORMHASH;
	$nextKeywordPage=$server_url . "&fixedTimeAdd=yes&keyword_page=" . ($keyword_page + 1) . '&formhash=' . FORMHASH;
	include template('csdn123_news:fixedTimeAdd');
	
} else{
	
	if (!isset($_G['cache']['plugin'])) {
		loadcache('plugin');
	}
	$hzw_startcron = $_G['cache']['plugin']['csdn123_news']['csdn123_dingshi'];
	$hzw_strict_dingshi = $_G['cache']['plugin']['csdn123_news']['hzw_strict_dingshi'];
	if($hzw_startcron != 1)
	{
		echo '<div style="text-align:center;margin:64px;"><a href="?action=plugins&operation=config&do=' . $pluginid . '" style="font-size:24px;color:red;">' . lang('plugin/csdn123_news', 'open_timing_acquisition') . '</a></div>';
		
	} else {
		
		$fixed_success = lang('plugin/csdn123_news', 'fixed_success');
		$pendingCount = DB::result_first('SELECT count(*) FROM ' . DB::table('csdn123zd_news') . ' WHERE tid_aid<=0 AND del=0');
		$successCount = DB::result_first("SELECT count(*) FROM " . DB::table('csdn123zd_news') . " WHERE tid_aid>0 AND catch_way='autocatch' AND del=0");
		$fixed_success_url = 'action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123_news&pmod=released&autocatch=yes';
		$fixed_success = str_replace('x',$pendingCount,$fixed_success);
		$fixed_success = str_replace('y',$successCount,$fixed_success);
		if(!defined('DISCUZ_VERSION')) {
			require_once './source/discuz_version.php';
		}
		if(DISCUZ_VERSION!='X2.5')
		{
			$csdn123_cronid = C::t('common_cron')->get_cronid_by_filename('csdn123_news:cron_csdn123_news.inc.php');
			$csdn123_cronUrl=$_G['siteurl'] . ADMINSCRIPT . '?action=misc&operation=cron&edit=' . $csdn123_cronid;
		}	
		$cronList=DB::fetch_all("SELECT * FROM " . DB::table('csdn123zd_cron') . " ORDER BY catchtime DESC");
		include template('csdn123_news:fixedTime');
	
	}
	
}
?>