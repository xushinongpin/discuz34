<?php
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$server_url='action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123_news&pmod=trueTime';
require './source/plugin/csdn123_news/common.fun.php';
hzw_authorization(false,$pluginid);
if($_GET['formhash'] == FORMHASH && !empty($_GET['csdn123_agrs']) && $_GET['csdn123_agrs']=='yes')
{
	$data_sources = $_GET['data_sources'];
	if($data_sources==2)
	{
		$_GET['keyword']='kaibao';
	}	
	if(empty($_GET['keyword']))
	{
		cpmsg("csdn123_news:keywords_empty","","error");
	} else {
		$keyword=$_GET['keyword'];
	}
	if(empty($_GET['inputcookie']))
	{
		$inputcookie='';
	} else {
		$inputcookie=$_GET['inputcookie'];		
	}
	$inputcookie=trim($inputcookie);
	setcookie('inputcookie',$inputcookie,time()+3600);
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
	$release_time=$_GET['release_time'];
	$release_time=strtotime($release_time);
	if(is_numeric($release_time)==FALSE || $release_time<10000)
	{
		$release_time_start=time() - 3600;
		$release_time=rand($release_time_start,time());
	}
	$htmlcode = catch_data_source($keyword,$data_sources,$inputcookie);	
	if (is_array($htmlcode) ==false) {
		cpmsg("csdn123_news:collection_result_null","","error");
	}
	if($data_sources!=6)
	{
		$api_server="http://www.csdn123.net/zd_version/zd9_5/trueTime.php";
		$api_server_parameter=array();
		$api_server_parameter['siteurl'] = $_G['siteurl'];
		$api_server_parameter['htmlcode'] = base64_encode($htmlcode['htmlcode']);
		$api_server_parameter['catchUrl'] = urlencode($htmlcode['catchUrl']);
		$api_server_parameter['hzw_appid'] = hzw_appid(1);
		$api_server_parameter['hzw_sign'] = hzw_sign($api_server_parameter);		
		$htmlcode = dfsockopen($api_server,0,$api_server_parameter);
		if (strlen($htmlcode) < 50) {
			$htmlcode = dfsockopen($api_server, 0, $api_server_parameter, '', FALSE, '', 15, TRUE, 'URLENCODE', FALSE);
		}
		if (strlen($htmlcode) < 50) {
			cpmsg("csdn123_news:collection_result_null","","error");
		}
		
	} else {
		
		$htmlcode = $htmlcode['htmlcode'];		
	}
	$htmlcode = base64_decode($htmlcode);
	$resultLink = dunserialize($htmlcode);
	if(is_array($resultLink)==FALSE)
	{
		cpmsg("csdn123_news:collection_result_null","","error");
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
	foreach($resultLink as $resultLinkItem)
	{
		$newsArr=array();
		$title=diconv($resultLinkItem['title'],'UTF-8',CHARSET);
		$title=dhtmlspecialchars($title);
		$newsArr['title']=daddslashes($title);
		$source_link=$resultLinkItem['url'];
		$newsArr['source_link']=daddslashes($source_link);
		$newsArr['forum_portal']=$forum_portal;
		$newsArr['fid']=$fid;
		$newsArr['threadtypeid']=$threadtypeid;
		$newsArr['portal_catid']=$portal_catid;
		$newsArr['uid']=getOneUid($uid);
		$newsArr['display_link']=$display_link;
		$newsArr['image_localized']=$image_localized;
		$newsArr['pseudo_original']=$pseudo_original;
		$newsArr['chinese_encoding']=$chinese_encoding;
		$newsArr['views']=rand(1,$views);
		$newsArr['group_fid']=$group_fid;
		$newsArr['release_time']=$release_time-rand(-1800,1800);
		if($data_sources==6)
		{
			$fromurl=$resultLinkItem['fromurl'];
			$newsArr['fromurl']=daddslashes($fromurl);
		}
		$chk = DB::fetch_first("SELECT * FROM " . DB::table('csdn123zd_news') . " WHERE source_link='" . daddslashes($source_link) . "' LIMIT 1");
		$chk2 = DB::fetch_first("SELECT * FROM " . DB::table('csdn123zd_news') . " WHERE title='" . daddslashes($title) . "' LIMIT 1");
		if (count($chk) == 0 && count($chk2) == 0) {
			DB::insert('csdn123zd_news', $newsArr);
		}
		
	}
	$succeed_url='action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123_news&pmod=pending';
	cpmsg("csdn123_news:collection_success",$succeed_url,"succeed");
	
} else {
	
	$regvest_url='?action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123_news&pmod=adminVest&subpmod=regvest&formhash=' . FORMHASH;
	require_once libfile('function/forumlist');
	require_once libfile('function/portalcp');
	require_once libfile('function/group');
	$grouplistArr=grouplist('displayorder',false,100);
	$release_time=rand(1,3600);
	$release_time=time() - $release_time;
	$release_time=date('Y-m-d H:i:s',$release_time);
	if(empty($_GET['keyword_page']) || is_numeric($_GET['keyword_page'])==false)
	{
		$keyword_page=1;
	} else {
		$keyword_page=$_GET['keyword_page'];
	}
	$keywordArr=show_keywords($keyword_page);
	$preKeywordPage=$server_url . "&keyword_page=" . ($keyword_page - 1);
	$nextKeywordPage=$server_url . "&keyword_page=" . ($keyword_page + 1);
	include template('csdn123_news:trueTime');
	
}
?>