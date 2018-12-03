<?php
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
require './source/plugin/csdn123com_qqnews/function_common.php';
$server_url = 'action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123com_qqnews&pmod=recycle';
if ($_GET['formhash'] == FORMHASH && empty($_GET['canceldel']) == false) {
	
	$delid = intval($_GET['canceldel']);
	DB::update('csdn123qqnews_news', array('del' => 0), 'ID=' . $delid);
	$canceldel_url = $server_url . '&page=' . $page;
	cpmsg('csdn123com_qqnews:succeed', $canceldel_url, 'succeed');
	
} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['del']) == false) {
	
	$delid = intval($_GET['del']);
	DB::delete('csdn123qqnews_news', 'ID=' . $delid);
	$del_backurl = $server_url . '&page=' . $page;
	cpmsg('csdn123com_qqnews:succeed', $del_backurl, 'succeed');
	
} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['seldelsmt']) == false) {
	
	if (empty($_GET['clearall']) == false) {
		
		DB::delete('csdn123qqnews_news','del=1');
		cpmsg('csdn123com_qqnews:succeed', $server_url, 'succeed');
		
	}
	if (empty($_GET['restoreall']) == false) {
		
		DB::update('csdn123qqnews_news',array('del'=>0),'del=1');
		cpmsg('csdn123com_qqnews:succeed', $server_url, 'succeed');
		
	}
	if (empty($_GET['idarray'])) {
		cpmsg('csdn123com_qqnews:select_empty', '', 'error');
	}
	if (empty($_GET['seldelete']) == false) {
		$idstr = implode(',', $_GET['idarray']);
		$idstr = daddslashes($idstr);
		DB::delete('csdn123qqnews_news', 'ID in (' . $idstr . ')');
		cpmsg('csdn123com_qqnews:succeed', $server_url . '&page=' . $_GET['page'], 'succeed');
	}
	if (empty($_GET['selcancel']) == false) {
		$idstr = implode(',', $_GET['idarray']);
		$idstr = daddslashes($idstr);
		DB::update('csdn123qqnews_news', array('del' => 0), 'ID in (' . $idstr . ')');
		cpmsg('csdn123com_qqnews:succeed', $server_url . '&page=' . $_GET['page'], 'succeed');
	}
	
} else {
	
	if(empty($_GET['page']) || is_numeric($_GET['page'])==false)
	{
		$page=1;
	} else {
		$page=$_GET['page'];
	}
	$page=max(1,$page);
	$newsCount = DB::result_first("SELECT COUNT(*) FROM " . DB::table('csdn123qqnews_news') . " WHERE tid_aid=0 and del=1");
	if($newsCount>0)
	{
		$TotalNumber = lang('plugin/csdn123com_qqnews', 'total_number');
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
		$news_list = DB::fetch_all('SELECT * FROM ' . DB::table('csdn123qqnews_news') . ' WHERE tid_aid=0 and del=1 ORDER BY ID DESC LIMIT ' . $startNum . ',20');
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
	include template("csdn123com_qqnews:recycle");	
	
}
