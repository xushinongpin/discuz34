<?php
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
require './source/plugin/csdn123com_zaker/function_common.php';
$server_url = 'action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123com_zaker&pmod=pending';
if ($_GET['formhash'] == FORMHASH && $_GET['selall'] == 'yes') {
	
	if (empty($_GET['idarray'])) {
		cpmsg('csdn123com_zaker:select_empty', '' , 'error');
	}
	if (empty($_GET['seldelete']) == false) {
		$idstr = implode(',', $_GET['idarray']);
		$idstr = daddslashes($idstr);
		DB::update('csdn123zaker_news', array('del' => 1), 'ID in (' . $idstr . ')');
		cpmsg('csdn123com_zaker:succeed', $server_url . '&page=' . $_GET['page'], 'succeed');
	}
	if (empty($_GET['selimport']) == false) {
		if (empty($_GET['num'])) {
			$num = 0;
		} else {
			$num = $_GET['num'];
			$num = intval($num);
		}
		if (is_array($_GET['idarray'])) {
			$idarray = $_GET['idarray'];
			$count_num = count($idarray);
			$idstr = implode(',', $_GET['idarray']);
		} else {
			$idstr = $_GET['idarray'];
			$idarray = explode(',', $idstr);
			$count_num = count($idarray);
		}
		$ID = $idarray[$num];
		if (is_numeric($ID) == false) {
			$success_Url = 'action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123com_zaker&pmod=released';
			cpmsg('csdn123com_zaker:succeed', $success_Url, 'succeed');
		}
		$recode = send_thread($ID);
		if ($recode == 'ok') {
			$num++;
			$ID = $idarray[$num];
			if (is_numeric($ID) == false) {
				$success_Url = 'action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123com_zaker&pmod=released';
				cpmsg('csdn123com_zaker:succeed', $success_Url, 'succeed');
			}
			$nextCatchUrl = '?' . $server_url . '&selimport=yes&selall=yes&formhash=' . FORMHASH . '&idarray=' . $idstr . '&num=' . $num;
			$statusStr = lang('plugin/csdn123com_zaker', 'import_status');
			$statusStr = str_replace('count', $count_num, $statusStr);
			$statusStr = str_replace('num', $num, $statusStr);
			echo '<div style="font-size:20px;margin-top:64px;text-align:center;color:red">' . $statusStr . '</div>';
			echo '<script>setTimeout(function(){ window.location.href="' . $nextCatchUrl . '" },10000);</script>';
		} else {
			$num++;
			$ID = $idarray[$num];
			if (is_numeric($ID) == false) {
				$success_Url = 'action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123com_zaker&pmod=released';
				cpmsg('csdn123com_zaker:succeed', $success_Url, 'succeed');
			}
			$nextCatchUrl = '?' . $server_url . '&selimport=yes&selall=yes&formhash=' . FORMHASH . '&idarray=' . $idstr . '&num=' . $num;
			$statusStr = lang('plugin/csdn123com_zaker', 'import_status_error');
			$statusStr = str_replace('num', $num, $statusStr);
			echo '<div style="font-size:20px;margin-top:64px;text-align:center;color:green">' . dhtmlspecialchars($statusStr) . '</div>';
			echo '<script>setTimeout(function(){ window.location.href="' . $nextCatchUrl . '" },5000);</script>';
		}
	}

} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['catchrandnews']) == false && $_GET['catchrandnews']=='yes') {	
	
	$thread = dfsockopen("http://discuz.csdn123.net/catch/zaker201711/random_new.php");
	$thread = unserialize($thread);
	$release_time=$_GET['release_time'];
	$release_time=strtotime($release_time);
	if(is_numeric($release_time)==FALSE || $release_time<10000)
	{
		$release_time_start=time() - 3600;
		$release_time=rand($release_time_start,time());
	}
	if(is_array($thread))
	{
		foreach ($thread as $resultLinkItem) 
		{
			$newsArr=array();
			$title=diconv($resultLinkItem['title'],'UTF-8',CHARSET);
			$newsArr['title']=daddslashes($title);
			$source_link=$resultLinkItem['url'];
			$newsArr['source_link']=daddslashes($source_link);
			$newsArr['forum_portal']=daddslashes($_GET['forum_portal']);
			$newsArr['fid']=intval($_GET['fid']);
			$newsArr['threadtypeid']=intval($_GET['threadtypeid']);
			$newsArr['portal_catid']=intval($_GET['portal_catid']);
			$newsArr['first_uid']=daddslashes($_GET['first_uid']);
			$newsArr['reply_uid']=daddslashes($_GET['reply_uid']);
			$newsArr['display_link']=intval($_GET['display_link']);
			$newsArr['image_localized']=intval($_GET['image_localized']);
			$newsArr['pseudo_original']=intval($_GET['pseudo_original']);
			$newsArr['chinese_encoding']=intval($_GET['chinese_encoding']);
			$views=intval($_GET['views']);
			$newsArr['views']=rand(1,$views);
			$newsArr['group_fid']=intval($_GET['group_fid']);
			$newsArr['intval_time']=intval($_GET['intval_time']);
			$newsArr['filter_image']=intval($_GET['filter_image']);
			$newsArr['image_center']=intval($_GET['image_center']);
			$newsArr['replaynum']=intval($_GET['replaynum']);
			$newsArr['release_time']=$release_time-rand(-1800,1800);
			$chk = DB::fetch_first("SELECT * FROM " . DB::table('csdn123zaker_news') . " WHERE source_link='" . daddslashes($source_link) . "' LIMIT 1");
			$chk2 = DB::fetch_first("SELECT * FROM " . DB::table('csdn123zaker_news') . " WHERE title='" . daddslashes($title) . "' LIMIT 1");		
			if (count($chk) == 0 && count($chk2) == 0) {
				DB::insert('csdn123zaker_news', $newsArr);
			}		
		}
		cpmsg("csdn123com_zaker:collection_success",$server_url,"succeed");
	}	
	
		
} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['random_content']) == false && $_GET['random_content']=='yes') {
	
	require_once libfile('function/forumlist');
	require_once libfile('function/portalcp');
	require_once libfile('function/group');
	$grouplistArr=grouplist('displayorder',false,100);
	$release_time=rand(1,3600);
	$release_time=time() - $release_time;
	$release_time=date('Y-m-d H:i:s',$release_time);
	include template("csdn123com_zaker:pending_randNews");
	
} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['import_id']) == false && is_numeric($_GET['import_id'])) {
	
	$recode = send_thread($_GET['import_id']);
	if ($recode == 'ok') {
		
		$success_Url = 'action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123com_zaker&pmod=released';
		cpmsg('csdn123com_zaker:succeed', $success_Url, 'succeed');
		
	} else {
		
		cpmsg('csdn123com_zaker:fail', '', 'error');
		
	}
	
} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['update_id']) == false && is_numeric($_GET['update_id'])) {
	
	$regvest_url='?action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123com_zaker&pmod=adminVest&subpmod=regvest&formhash=' . FORMHASH;
	$update_id = intval($_GET['update_id']);
	$postRs = DB::fetch_first("SELECT * FROM " . DB::table('csdn123zaker_news') . " WHERE ID=" . $update_id);
	$typeclassArr = C::t('forum_threadclass')->fetch_all_by_fid($postRs['fid']);
	require_once libfile('function/forumlist');
	require_once libfile('function/portalcp');
	require_once libfile('function/group');
	$grouplistArr=grouplist('displayorder',false,100);
	include template("csdn123com_zaker:pending_update");
	
} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['update']) == false && $_GET['update'] == 'yes') {

	$newsArr = array();
	$newsArr['title']=daddslashes($_GET['title']);
	$newsArr['forum_portal']=daddslashes($_GET['forum_portal']);
	$newsArr['fid']=intval($_GET['fid']);
	$newsArr['threadtypeid']=intval($_GET['threadtypeid']);
	$newsArr['portal_catid']=intval($_GET['portal_catid']);
	$newsArr['first_uid']=daddslashes($_GET['first_uid']);
	$newsArr['reply_uid']=daddslashes($_GET['reply_uid']);
	$newsArr['display_link']=intval($_GET['display_link']);
	$newsArr['image_localized']=intval($_GET['image_localized']);
	$newsArr['pseudo_original']=intval($_GET['pseudo_original']);
	$newsArr['chinese_encoding']=intval($_GET['chinese_encoding']);
	$newsArr['views']=intval($_GET['views']);
	$newsArr['group_fid']=intval($_GET['group_fid']);
	$newsArr['intval_time']=intval($_GET['intval_time']);
	$newsArr['filter_image']=intval($_GET['filter_image']);
	$newsArr['image_center']=intval($_GET['image_center']);
	$newsArr['replaynum']=intval($_GET['replaynum']);
	$release_time=strtotime($_GET['release_time']);
	if(is_numeric($release_time)==false)
	{
		$release_time = time();
	}
	$newsArr['release_time'] = $release_time;
	DB::update('csdn123zaker_news', $newsArr, 'ID=' . intval($_GET['news_id']));
	$update_backUrl = $server_url . '&page=' . $page;
	cpmsg('csdn123com_zaker:succeed', $update_backUrl, 'succeed');

} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['sendfail']) == false && $_GET['sendfail'] == 'yes') {
	
	DB::delete('csdn123zaker_news','tid_aid=-1');
	cpmsg('csdn123com_zaker:succeed', $server_url, 'succeed');

} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['clears_all']) == false && $_GET['clears_all'] == 'yes') {
	
	DB::delete('csdn123zaker_news','tid_aid<=0');
	cpmsg('csdn123com_zaker:succeed', $server_url, 'succeed');
	
} else {
	
	if(empty($_GET['page']) || is_numeric($_GET['page'])==false)
	{
		$page=1;
	} else {
		$page=$_GET['page'];
	}
	$page=max(1,$page);
	$newsCount = DB::result_first('SELECT count(*) FROM ' . DB::table('csdn123zaker_news') . ' WHERE tid_aid<=0 and del=0');
	if($newsCount>0)
	{
		$TotalNumber = lang('plugin/csdn123com_zaker', 'total_number');
		$TotalPageNumber = $newsCount/20;
		$TotalPageNumber = @ceil($TotalPageNumber);
		$TotalNumber = str_replace('x',$TotalPageNumber,$TotalNumber);
		$TotalNumber = str_replace('y',$newsCount,$TotalNumber);
		$TotalNumber = str_replace('z',$page,$TotalNumber);
		$page = min($TotalPageNumber,$page);
		$startNum = ($page - 1) * 20;
		$homePage = $server_url . '&page=1';
		$nextPage = $server_url . '&page=' . ($page + 1);
		$prePage = $server_url . '&page=' . ($page - 1);
		$endPage = $server_url . '&page=' . $TotalPageNumber;	
		$news_list = DB::fetch_all('SELECT * FROM ' . DB::table('csdn123zaker_news') . ' WHERE tid_aid<=0 and del=0 ORDER BY ID DESC LIMIT ' . $startNum . ',20');
		$start = $page - 4;		
		$start = min($TotalPageNumber - 8,$start);
		$start = max($start,1);
		$end = $start + 8;
		$end = min($end,$TotalPageNumber);
		$showPage="";
		for($i=$start;$i<=$end;$i++)
		{
			if($i==$page)
			{
				$showPage=$showPage . '<strong>' . $i . '</strong>';
			} else {
				$showPage=$showPage . '<a href="?' . $server_url . '&page=' . $i . '&formhash=' . FORMHASH . '">' . $i . '</a>';
			}
		}
	}	
	include template("csdn123com_zaker:pending");
	
}
