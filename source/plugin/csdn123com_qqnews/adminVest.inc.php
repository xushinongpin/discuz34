<?php
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
if (empty($_GET['page']) || is_numeric($_GET['page']) === false) {
	$page = 1;
} else {
	$page = intval($_GET['page']);
	$page = max(1, $page);
}
$server_url = 'action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123com_qqnews&pmod=adminVest';
if ($_GET['formhash'] == FORMHASH && empty($_GET['deluser']) == false && $_GET['deluser'] == 'yes') {
	
	if(empty($_GET['clears_all'])==false)
	{
		DB::delete('csdn123qqnews_reguser', 'uid>0');
		cpmsg('csdn123com_qqnews:succeed', $server_url , 'succeed');
	}
	if(empty($_GET['uidarray']))
	{
		cpmsg('csdn123com_qqnews:select_empty', '' , 'error');
	}
	$uidarray = $_GET['uidarray'];
	foreach ($uidarray as $uid) {
		DB::delete('csdn123qqnews_reguser', 'uid=' . intval($uid));
	}
	include template('csdn123com_qqnews:delete_vest_list');
	
} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['modifypic']) == false && is_numeric($_GET['modifypic']) == true) {
	
	loaducenter();
	$avatarhtml = uc_avatar($_GET['modifypic']);
	include template('csdn123com_qqnews:uc_avatar');
	
} elseif($_GET['formhash'] == FORMHASH && !empty($_GET['subpmod']) && $_GET['subpmod']=='regvest') {
	
	require './source/plugin/csdn123com_qqnews/regvest.inc.php';	

} elseif($_GET['formhash'] == FORMHASH && !empty($_GET['subpmod']) && $_GET['subpmod']=='importVest') {
	
	require './source/plugin/csdn123com_qqnews/importVest.inc.php';
	
} elseif($_GET['formhash'] == FORMHASH && !empty($_GET['subpmod']) && $_GET['subpmod']=='exportVest') {
	
	require './source/plugin/csdn123com_qqnews/exportVest.inc.php';
	
} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['output']) == false && $_GET['output'] == 'yes') {
	
	
	$uidarray = DB::fetch_all('select uid from ' . DB::table('csdn123qqnews_reguser') . ' order by uid desc');
	foreach ($uidarray as $uidvalue) {
		$uidstr = $uidvalue['uid'] . ',' . $uidstr;
	}
	$uidstr = substr($uidstr, 0, -1);
	include template('csdn123com_qqnews:output_user_list');
	
	
} else {
	
	
	$VestCount = DB::result_first('SELECT count(*) FROM ' . DB::table('csdn123qqnews_reguser'));
	if($VestCount>0)
	{
		$TotalNumber = lang('plugin/csdn123com_qqnews', 'total_number');
		$TotalPageNumber = $VestCount/20;
		$TotalPageNumber = @ceil($TotalPageNumber);
		$TotalNumber = str_replace('x',$TotalPageNumber,$TotalNumber);
		$TotalNumber = str_replace('y',$VestCount,$TotalNumber);
		$TotalNumber = str_replace('z',$page,$TotalNumber);
		$page = min($TotalPageNumber,$page);
		$startNum = ($page - 1) * 20;
		$homePage = $server_url . '&page=1';
		$nextPage = $server_url . '&page=' . ($page + 1);
		$prePage = $server_url . '&page=' . ($page - 1);
		$endPage = $server_url . '&page=' . $TotalPageNumber;	
		$user_list = DB::fetch_all('SELECT * FROM ' . DB::table('csdn123qqnews_reguser') . ' ORDER BY uid DESC LIMIT ' . $startNum . ',20');
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
	include template('csdn123com_qqnews:adminVest');
	
}
