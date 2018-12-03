<?php
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
require './source/plugin/csdn123_news/common.fun.php';
hzw_authorization(false,$pluginid);
$server_url = 'action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123_news&pmod=released';
function return_seo($data,$url)
{
	$data_arr=json_decode($data,true);
	$restr=lang('plugin/csdn123_news', 'baiduseo_url') . $url . '<br>';
	$restr=$restr . lang('plugin/csdn123_news', 'baiduseo_remain') . $data_arr['remain'] . '<br>';
	$restr=$restr . '<span onClick="document.getElementById(\'showbaiduseoinfo\').style.display=\'block\'" style="cursor:pointer;color:red">' . lang('plugin/csdn123_news', 'baiduseo_info') . '</span><br>';
	$restr=$restr . '<textarea style="display:none;width:550px;height:300px;" id="showbaiduseoinfo">' . $data . '</textarea>';
	return $restr;	
}
function edit_url($post)
{
	global $_G;
	if($post['forum_portal']=='forum' || $post['forum_portal']=='group')
	{
		$pid=C::t('forum_post')->fetch_threadpost_by_tid_invisible($post['tid_aid']);
		$pid=$pid['pid'];
		return $_G['siteurl'] . "forum.php?mod=post&action=edit&fid={$post['fid']}&tid={$post['tid_aid']}&pid={$pid}&page=1";
		
	} else {
		
		return $_G['siteurl'] . "portal.php?mod=portalcp&ac=article&op=edit&aid={$post['tid_aid']}";
		
	}
	
}
if ($_GET['formhash'] == FORMHASH && empty($_GET['clears_all']) == false && $_GET['clears_all'] == 'yes') {
	
	DB::delete('csdn123zd_news', 'tid_aid>0');
	cpmsg('csdn123_news:succeed', $server_url , 'succeed');

} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['baidu_seo_all']) == false && $_GET['baidu_seo_all'] == 'yes') {
	
	if(empty($_GET['id']) || !is_numeric($_GET['id']))
	{
		$id=0;
	} else {
		$id=$_GET['id'];
	}
	if(empty($_GET['num']) || !is_numeric($_GET['num']))
	{
		$num=0;
	} else {
		$num=$_GET['num'];
	}
	if(empty($_GET['count_num']) || !is_numeric($_GET['count_num']))
	{
		$count_num = DB::result_first('SELECT count(*) FROM ' . DB::table('csdn123zd_news') . ' WHERE tid_aid>0');		
	} else {		
		$count_num = $_GET['count_num'];	
	}
	if (!isset($_G['cache']['plugin'])) {
		loadcache('plugin');
	}
	if(empty($_G['cache']['plugin']['csdn123_news']['csdn123_seo']))
	{
		cpmsg('csdn123_news:baidu_seo_error', '', 'error');
	} else {
		$csdn123_seo=$_G['cache']['plugin']['csdn123_news']['csdn123_seo'];
	}
	$post_seo_url_RS=DB::fetch_first("SELECT * FROM " . DB::table('csdn123zd_news') . " WHERE tid_aid>0 AND ID>" . $id . " ORDER BY ID ASC LIMIT 1");
	if(empty($post_seo_url_RS))
	{	
		cpmsg('csdn123_news:succeed', $server_url , 'succeed');		
		
	} else {
		
		$csdn123_seo=trim($csdn123_seo);
		$post_seo_url=preview_url($post_seo_url_RS['forum_portal'],$post_seo_url_RS['tid_aid']);
		if(function_exists('curl_init') && function_exists('curl_exec')) {
			
			$urls = array($post_seo_url);
			$api = $csdn123_seo;
			$ch = curl_init();
			$options =  array(
				CURLOPT_URL => $api,
				CURLOPT_POST => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POSTFIELDS => implode("\n", $urls),
				CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
			);
			curl_setopt_array($ch, $options);
			$return_status = curl_exec($ch);
			
		} else {
			
			echo "Please turn on PHP's curl";
			exit;
			
		}
		$statusStr = lang('plugin/csdn123_news', 'baiduseo_status');		
		$statusStr = str_replace('count', $count_num, $statusStr);
		$num++;
		$statusStr = str_replace('num', $num, $statusStr);
		echo '<div style="margin:32px;text-align:left;line-height:36px;font-size:18px;">';
		echo '<br><span style="font-size:20px;color:red">' . $statusStr . '</span><br>';
		echo  return_seo($return_status,$post_seo_url);
		echo '</div>';
		$nextSeoUrl = '?' . $server_url . '&baidu_seo_all=yes&formhash=' . FORMHASH . '&id=' . $post_seo_url_RS['ID'] . '&num=' . $num . '&count_num=' . $count_num;
		echo '<script>setTimeout(function(){ window.location.href="' . $nextSeoUrl . '" },10000);</script>';
		
	}
	
} elseif ($_GET['formhash'] == FORMHASH && $_GET['selall'] == 'yes') {
	
	if (empty($_GET['idarray'])) {
		cpmsg('csdn123_news:select_empty', '' , 'error');
	}
	if(empty($_GET['seldelete'])==false)
	{
	
		$idstr = implode(',', $_GET['idarray']);
		$idstr = daddslashes($idstr);
		DB::delete('csdn123zd_news', 'ID in (' . $idstr . ')');
		cpmsg('csdn123_news:succeed', $server_url . '&page=' . $_GET['page'], 'succeed');
	
	}
	if(empty($_GET['selpostbaidu'])==false)
	{
		
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
			cpmsg('csdn123_news:succeed', $server_url , 'succeed');
		}
		if (!isset($_G['cache']['plugin'])) {
			loadcache('plugin');
		}
		if(empty($_G['cache']['plugin']['csdn123_news']['csdn123_seo']))
		{
			cpmsg('csdn123_news:baidu_seo_error', '', 'error');
		} else {
			$csdn123_seo=$_G['cache']['plugin']['csdn123_news']['csdn123_seo'];
		}
		$csdn123_seo=trim($csdn123_seo);		
		$post_seo_url_RS=DB::fetch_first("SELECT * FROM " . DB::table('csdn123zd_news') . " WHERE tid_aid>0 AND ID=" . $ID);
		$post_seo_url=preview_url($post_seo_url_RS['forum_portal'],$post_seo_url_RS['tid_aid']);
		if(function_exists('curl_init') && function_exists('curl_exec')) {
			
			$urls = array($post_seo_url);
			$api = $csdn123_seo;
			$ch = curl_init();
			$options =  array(
				CURLOPT_URL => $api,
				CURLOPT_POST => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POSTFIELDS => implode("\n", $urls),
				CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
			);
			curl_setopt_array($ch, $options);
			$return_status = curl_exec($ch);
			
		} else {
			
			echo "Please turn on PHP's curl";
			exit;
			
		}
		$statusStr = lang('plugin/csdn123_news', 'baiduseo_status');
		$statusStr = str_replace('count', $count_num, $statusStr);
		$statusStr = str_replace('num', $num+1, $statusStr);
		echo '<div style="margin:32px;text-align:left;line-height:36px;font-size:18px;">';
		echo '<br><span style="font-size:20px;color:red">' . $statusStr . '</span><br>';
		echo  return_seo($return_status,$post_seo_url);
		echo '</div>';
		$num++;
		$nextSeoUrl = '?' . $server_url . '&selpostbaidu=yes&selall=yes&formhash=' . FORMHASH . '&idarray=' . $idstr . '&num=' . $num;
		echo '<script>setTimeout(function(){ window.location.href="' . $nextSeoUrl . '" },10000);</script>';
		
	}
	
} else {
	
	if(empty($_GET['page']) || is_numeric($_GET['page'])==false)
	{
		$page=1;
	} else {
		$page=$_GET['page'];
	}
	$page=max(1,$page);
	if(empty($_GET['autocatch']))
	{	
		$newsCount = DB::result_first('SELECT count(*) FROM ' . DB::table('csdn123zd_news') . ' WHERE tid_aid>0');
	} else {
		$newsCount = DB::result_first("SELECT count(*) FROM " . DB::table('csdn123zd_news') . " WHERE catch_way='autocatch' AND tid_aid>0");
	}
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
		if(empty($_GET['autocatch']))
		{	
			$homePage = $server_url . '&page=1';
			$nextPage = $server_url . '&page=' . ($page + 1);
			$prePage = $server_url . '&page=' . ($page - 1);
			$endPage = $server_url . '&page=' . $TotalPageNumber;
			$news_list = DB::fetch_all('SELECT * FROM ' . DB::table('csdn123zd_news') . ' WHERE tid_aid>0 ORDER BY send_datetime DESC LIMIT ' . $startNum . ',20');
			
		} else {
			
			$homePage = $server_url . '&autocatch=yes&page=1';
			$nextPage = $server_url . '&autocatch=yes&page=' . ($page + 1);
			$prePage = $server_url . '&autocatch=yes&page=' . ($page - 1);
			$endPage = $server_url . '&autocatch=yes&page=' . $TotalPageNumber;
			$news_list = DB::fetch_all("SELECT * FROM " . DB::table('csdn123zd_news') . " WHERE tid_aid>0 AND catch_way='autocatch' ORDER BY send_datetime DESC LIMIT " . $startNum . ",20");
		}
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
	include template("csdn123_news:released");
	
}
