<?php
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
require './source/plugin/csdn123_news/common.fun.php';
hzw_authorization(false,$pluginid);
$server_url = 'action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123_news&pmod=pending';
$success_Url = 'action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123_news&pmod=released';
$all_catch_url = 'action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123_news&pmod=url';
if ($_GET['formhash'] == FORMHASH && $_GET['selall'] == 'yes') {
	
	if (empty($_GET['idarray'])) {
		cpmsg('csdn123_news:select_empty', '' , 'error');
	}
	if (empty($_GET['seldelete']) == false) {
		$idstr = implode(',', $_GET['idarray']);
		$idstr = daddslashes($idstr);
		DB::update('csdn123zd_news', array('del' => 1), 'ID in (' . $idstr . ')');
		cpmsg('csdn123_news:succeed', $server_url . '&page=' . $_GET['page'], 'succeed');
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
			cpmsg('csdn123_news:succeed', $success_Url, 'succeed');
		}
		$recode = send_thread($ID);
		if ($recode == 'ok') {
			$num++;
			$ID = $idarray[$num];
			if (is_numeric($ID) == false) {
				cpmsg('csdn123_news:succeed', $success_Url, 'succeed');
			}
			$nextCatchUrl = '?' . $server_url . '&selimport=yes&selall=yes&formhash=' . FORMHASH . '&idarray=' . $idstr . '&num=' . $num;
			$statusStr = lang('plugin/csdn123_news', 'import_status');
			$statusStr = str_replace('count', $count_num, $statusStr);
			$statusStr = str_replace('num', $num, $statusStr);
			echo '<div style="font-size:20px;margin-top:64px;text-align:center;color:red">' . $statusStr . '</div>';
			echo '<script>setTimeout(function(){ window.location.href="' . $nextCatchUrl . '" },10000);</script>';
		} else {
			$num++;
			$ID = $idarray[$num];
			if (is_numeric($ID) == false) {
				cpmsg('csdn123_news:succeed', $success_Url, 'succeed');
			}
			$nextCatchUrl = '?' . $server_url . '&selimport=yes&selall=yes&formhash=' . FORMHASH . '&idarray=' . $idstr . '&num=' . $num;
			$statusStr = lang('plugin/csdn123_news', 'import_status_error');
			$statusStr = str_replace('num', $num, $statusStr);
			echo '<div style="font-size:20px;margin-top:64px;text-align:center;color:green">' . dhtmlspecialchars($statusStr) . '</div>';
			echo '<script>setTimeout(function(){ window.location.href="' . $nextCatchUrl . '" },5000);</script>';
		}
	}

} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['hand_input']) == false && $_GET['hand_input']=='yes') {
	
	$hand_input_id = $_GET['hand_input_id'];
	$source_link = DB::result_first('SELECT source_link FROM ' . DB::table("csdn123zd_news") . ' WHERE ID=' . $hand_input_id);
	include template("csdn123_news:catch_htmlcode");	
	
} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['import_id']) == false && is_numeric($_GET['import_id'])) {
	
	if(!empty($_GET['htmlcode']) && strlen($_GET['htmlcode'])>50)
	{
		$hand_htmlcode = $_GET['htmlcode'];
	} else {
		$hand_htmlcode = '';
	}
	$recode = send_thread($_GET['import_id'],'',$hand_htmlcode);
	if ($recode == 'ok') {		
		
		cpmsg('csdn123_news:succeed', $success_Url, 'succeed');
		
	} else {
		
		$hand_input_url = $server_url . '&hand_input=yes&formhash=' . FORMHASH . '&hand_input_id=' . $_GET['import_id'];
		cpmsg('csdn123_news:hand_input_htmlcode',$hand_input_url, 'error');
		
	}
	
} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['update_id']) == false && is_numeric($_GET['update_id'])) {
	
	$regvest_url='?action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123_news&pmod=adminVest&subpmod=regvest&formhash=' . FORMHASH;
	$update_id = intval($_GET['update_id']);
	$postRs = DB::fetch_first("SELECT * FROM " . DB::table("csdn123zd_news") . " WHERE ID=" . $update_id);
	$typeclassArr = C::t('forum_threadclass')->fetch_all_by_fid($postRs['fid']);
	require_once libfile('function/forumlist');
	require_once libfile('function/portalcp');
	require_once libfile('function/group');
	$grouplistArr=grouplist('displayorder',false,100);
	include template("csdn123_news:pending_update");
	
} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['update']) == false && $_GET['update'] == 'yes') {

	$newsArr = array();
	$_GET['title'] = dhtmlspecialchars($_GET['title']);
	$newsArr['title'] = daddslashes($_GET['title']);
	$newsArr['forum_portal'] = daddslashes($_GET['forum_portal']);
	$newsArr['fid'] = intval($_GET['fid']);
	$newsArr['threadtypeid'] = intval($_GET['threadtypeid']);
	$newsArr['portal_catid'] = intval($_GET['portal_catid']);
	$newsArr['uid'] = intval($_GET['uid']);
	$newsArr['display_link'] = intval($_GET['display_link']);
	$newsArr['image_localized'] = intval($_GET['image_localized']);
	$newsArr['pseudo_original'] = intval($_GET['pseudo_original']);
	$newsArr['chinese_encoding'] = intval($_GET['chinese_encoding']);
	$newsArr['views'] = intval($_GET['views']);
	$release_time = strtotime($_GET['release_time']);
	if(is_numeric($release_time)==false)
	{
		$release_time = time();
	}
	$newsArr['release_time'] = $release_time;
	$newsArr['group_fid'] = intval($_GET['group_fid']);
	DB::update('csdn123zd_news', $newsArr, 'ID=' . intval($_GET['news_id']));
	$update_backUrl = $server_url . '&page=' . $page;
	cpmsg('csdn123_news:succeed', $update_backUrl, 'succeed');

} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['sendfail']) == false && $_GET['sendfail'] == 'yes') {
	
	DB::delete('csdn123zd_news','tid_aid=-1');
	cpmsg('csdn123_news:succeed', $server_url, 'succeed');

} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['clears_all']) == false && $_GET['clears_all'] == 'yes') {
	
	DB::delete('csdn123zd_news','tid_aid<=0');
	cpmsg('csdn123_news:succeed', $server_url, 'succeed');
	
} else {
	
	if(empty($_GET['page']) || is_numeric($_GET['page'])==false)
	{
		$page=1;
	} else {
		$page=$_GET['page'];
	}
	$page=max(1,$page);
	$newsCount = DB::result_first('SELECT count(*) FROM ' . DB::table('csdn123zd_news') . ' WHERE tid_aid<=0 and del=0');
	if($newsCount>0)
	{
		$TotalNumber = lang('plugin/csdn123_news', 'total_number');
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
		$news_list = DB::fetch_all('SELECT * FROM ' . DB::table('csdn123zd_news') . ' WHERE tid_aid<=0 and del=0 ORDER BY ID DESC LIMIT ' . $startNum . ',20');
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
	include template("csdn123_news:pending");
	
}
