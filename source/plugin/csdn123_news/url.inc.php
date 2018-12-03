<?php
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$server_url='action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123_news&pmod=url';
$add_rule_url = 'action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123_news&pmod=url&add_rule=yes&formhash=' . FORMHASH;
$regvest_url='?action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123_news&pmod=adminVest&subpmod=regvest&formhash=' . FORMHASH;
require './source/plugin/csdn123_news/common.fun.php';
hzw_authorization(false,$pluginid);
function hzw_rep(&$hzw_item,$key)
{
	$hzw_item = str_ireplace('"','&quot;',$hzw_item);
}
if($_GET['formhash'] == FORMHASH && !empty($_GET['ruledel']) && is_numeric($_GET['ruledel'])) {
	
	$_GET['ruledel'] = daddslashes($_GET['ruledel']);
	DB::delete("csdn123zd_rule","ID=" . $_GET['ruledel']);
	cpmsg("csdn123_news:succeed",$server_url . '&admin_rule=yes&formhash=' . FORMHASH,"succeed");
	
}elseif($_GET['formhash'] == FORMHASH && !empty($_GET['clears_all_rule']) && $_GET['clears_all_rule']=='yes') {
	
	DB::delete('csdn123zd_rule','ID>0');
	cpmsg("csdn123_news:succeed",$server_url . '&admin_rule=yes&formhash=' . FORMHASH,"succeed");
	
} elseif($_GET['formhash'] == FORMHASH && !empty($_GET['import_rule']) && $_GET['import_rule']=='yes') {
	
	include template("csdn123_news:urlRuleImport");
	
}elseif($_GET['formhash'] == FORMHASH && !empty($_GET['rule_input']) && $_GET['rule_input']=='yes') {
	
	if(!empty($_GET['ruleDataStr']) && strlen($_GET['ruleDataStr'])>20)
	{
		$ruleDataStr = $_GET['ruleDataStr'];
		$ruleDataStr = base64_decode($ruleDataStr);
		$ruleDataArr = dunserialize($ruleDataStr);
		if(!is_array($ruleDataArr))
		{
			cpmsg("csdn123_news:fail",$server_url . '&rule_input=yes&formhash=' . FORMHASH,"error");
		}
		$step1Str = $ruleDataArr['step1'];
		$step2Str = $ruleDataArr['step2'];
		if($ruleDataArr['charset']!=CHARSET)
		{			
			$step1Str = base64_decode($step1Str);
			$step1Arr = dunserialize($step1Str);
			foreach($step1Arr as $k=>$v)
			{
				$step1Arr[$k] = diconv($v,$ruleDataArr['charset']);
			}
			$step1Str = serialize($step1Arr);
			$step1Str = base64_encode($step1Str);
			$ruleDataArr['step1'] = daddslashes($step1Str);
			$step2Str = base64_decode($step2Str);
			$step2Arr = dunserialize($step2Str);
			foreach($step2Arr as $k=>$v)
			{
				$step2Arr[$k] = diconv($v,$ruleDataArr['charset']);
			}
			$step2Str = serialize($step2Arr);
			$step2Str = base64_encode($step2Str);
			$ruleDataArr['step2'] = daddslashes($step2Str);
		}
		unset($ruleDataArr['charset']);
		$id = DB::insert('csdn123zd_rule',$ruleDataArr,true);
		require_once libfile('function/forumlist');
		require_once libfile('function/portalcp');
		require_once libfile('function/group');
		$grouplistArr=grouplist('displayorder',false,100);
		$release_time=rand(1,3600);
		$release_time=time() - $release_time;
		$release_time=date('Y-m-d H:i:s',$release_time);
		$step3Arr['release_time']=$release_time;
		$step3Arr['uid'] = getRndUid();
		$step3Arr['forum_portal']='forum';
		$step3Arr['views']=rand(1,1000);
		include template("csdn123_news:url_step4_modify");
		
		
	} else {
		
		include template("csdn123_news:urlRuleInput");
		
	}
	
}elseif($_GET['formhash'] == FORMHASH && !empty($_GET['export_rule']) && is_numeric($_GET['export_rule'])) {
	
	$id = $_GET['export_rule'];
	$id = intval($id);
	$ruleRs=DB::fetch_first('SELECT start_url,step1,step2 FROM ' . DB::table('csdn123zd_rule') . ' WHERE ID=' . $id);
	$exportRuleArr=array();
	$exportRuleArr['charset']=CHARSET;
	$exportRuleArr['start_url']=$ruleRs['start_url'];
	$exportRuleArr['step1']=$ruleRs['step1'];
	$exportRuleArr['step2']=$ruleRs['step2'];
	$exportRuleStr=serialize($exportRuleArr);
	$exportRuleStr=base64_encode($exportRuleStr);
	include template("csdn123_news:urlRuleOutput");
	
}elseif($_GET['formhash'] == FORMHASH && !empty($_GET['admin_rule']) && $_GET['admin_rule']=='yes') {
	
	$ruleRs=DB::fetch_all('SELECT * FROM ' . DB::table('csdn123zd_rule') . ' ORDER BY ID DESC');
	include template("csdn123_news:urlAdminRule");
	
}elseif($_GET['formhash'] == FORMHASH && !empty($_GET['add_rule']) && $_GET['add_rule']=='yes') {
	
	include template("csdn123_news:urlAddRule");
	
}elseif($_GET['formhash'] == FORMHASH && !empty($_GET['single_page_content']) && $_GET['single_page_content']=='yes') {

	if(empty($_GET['url']) || stripos($_GET['url'],'http')===false)
	{
		cpmsg("csdn123_news:contribute_url_error","","error");
	} else {
		$url=$_GET['url'];
		$url=trim($url);
	}
	if(!is_numeric($_GET['uid']))
	{
		cpmsg("csdn123_news:uid_error","","error");
	} else {
		$uid=$_GET['uid'];
	}
	$release_time=$_GET['release_time'];
	$release_time=strtotime($release_time);
	if(is_numeric($release_time)==FALSE || $release_time<10000)
	{
		$release_time=time();
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
	$rule_id=intval($_GET['rule_id']);
	$newsArr=array();
	$title=$url;
	$newsArr['title']=$title;
	$newsArr['source_link']=daddslashes($url);
	$newsArr['fromurl']=daddslashes($url);
	$newsArr['forum_portal']=$forum_portal;
	$newsArr['fid']=$fid;
	$newsArr['threadtypeid']=$threadtypeid;
	$newsArr['portal_catid']=$portal_catid;
	$newsArr['uid']=$uid;
	$newsArr['display_link']=$display_link;
	$newsArr['image_localized']=$image_localized;
	$newsArr['pseudo_original']=$pseudo_original;
	$newsArr['chinese_encoding']=$chinese_encoding;
	$newsArr['views']=$views;
	$newsArr['rule_id']=$rule_id;
	$newsArr['group_fid']=$group_fid;
	$newsArr['model_catch']=2;
	$newsArr['release_time']=$release_time;
	$chk = DB::fetch_first("SELECT * FROM " . DB::table('csdn123zd_news') . " WHERE source_link='" . daddslashes($url) . "' LIMIT 1");
	$chk2 = DB::fetch_first("SELECT * FROM " . DB::table('csdn123zd_news') . " WHERE title='" . daddslashes($title) . "' LIMIT 1");
	$success_url='action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123_news&pmod=released';
	if(!empty($_GET['sendid']) && is_numeric($_GET['sendid']))
	{
		$sendid=intval($_GET['sendid']);
		DB::delete('csdn123zd_contribute','ID=' . $sendid);
	}
	if (count($chk) == 0 && count($chk2) == 0) {
		
		$news_id=DB::insert('csdn123zd_news', $newsArr,true);		
		$recode = send_thread($news_id);
		if($recode=='ok')
		{
			cpmsg("csdn123_news:succeed",$success_url,"succeed");
		} else {
			$failUrl = "action=plugins&operation=config&do=" . $pluginid . "&identifier=csdn123_news&pmod=pending&hand_input=yes&formhash=" . FORMHASH . "&hand_input_id=" . $news_id;
			cpmsg("csdn123_news:fail",$failUrl,"error");
		}
		
	} elseif(count($chk)>1 && $chk["tid_aid"]==0) {
		
		$news_id=$chk["ID"];				
		$recode = send_thread($news_id);		
		if($recode=='ok')
		{
			cpmsg("csdn123_news:succeed",$success_url,"succeed");
		} else {
			$failUrl = "action=plugins&operation=config&do=" . $pluginid . "&identifier=csdn123_news&pmod=pending&hand_input=yes&formhash=" . FORMHASH . "&hand_input_id=" . $news_id;			
			cpmsg("csdn123_news:fail",$server_url,"error");
		}
		
	} elseif(count($chk)>1 && $chk["tid_aid"]>0) {
		
		$already_send = lang('plugin/csdn123_news', 'already_send');
		echo '<div style="font-size:18px;color:red;margin-top:32px;text-align:center">' . $already_send . preview_url($chk['forum_portal'],$chk['tid_aid']) . '</div>';
	
	} else {
	
		cpmsg("csdn123_news:fail",$server_url,"error");
	}

} elseif ($_GET['formhash'] == FORMHASH && !empty($_GET['sendid'])) {

	if(is_numeric($_GET['sendid']))
	{	
		$sendid=intval($_GET['sendid']);
		$url=DB::result_first("SELECT url FROM " . DB::table('csdn123zd_contribute') . " WHERE ID=" . $sendid);
	}
	require_once libfile('function/forumlist');
	require_once libfile('function/portalcp');
	require_once libfile('function/group');
	$grouplistArr=grouplist('displayorder',false,100);
	$release_time=rand(1,1800);
	$release_time=time() - $release_time;
	$release_time=date('Y-m-d H:i:s',$release_time);
	$ruleRs=DB::fetch_all('SELECT ID,start_url FROM ' . DB::table('csdn123zd_rule'));
	include template("csdn123_news:url_single");
	
} elseif ($_GET['formhash'] == FORMHASH && !empty($_GET['purehand']) && $_GET['purehand']=='yes') {
	
	$start_url = urldecode($_GET['start_url']);
	$start_run = $_GET['start_run'];
	include template("csdn123_news:pureHand");
	
} elseif ($_GET['formhash'] == FORMHASH && !empty($_GET['start_run']) && is_numeric($_GET['start_run'])) {
	
	$id=intval($_GET['start_run']);
	$ruleRs = DB::fetch_first('SELECT * FROM ' . DB::table('csdn123zd_rule') . ' WHERE ID=' . $id);	
	$updateArr = array();
	$updateArr['catch_num'] = $ruleRs['catch_num'] + 1;
	$updateArr['catch_time'] = time();
	DB::update('csdn123zd_rule',$updateArr,array('ID'=>$id));
	$step1Str = $ruleRs['step1'];
	$step1Str = base64_decode($step1Str);
	$step1Arr = dunserialize($step1Str);
	if(empty($_GET['htmlcode']))
	{	
		$htmlcode = ext_dfsockopen($step1Arr['start_url'],$step1Arr['inputcookie']);
		if(strlen($htmlcode)<50)
		{
			$pureHandUrl = $server_url . '&purehand=yes&formhash=' . FORMHASH . '&start_url=' . urlencode($step1Arr['start_url']) . '&start_run=' . $id;
			cpmsg("csdn123_news:fail_purehand",$pureHandUrl,"error");
		}
		
	} else {
		
		$htmlcode = $_GET['htmlcode'];
		$htmlcode = base64_decode($htmlcode);
		
	}
	$dataArr = array();
	$dataArr['htmlcode'] = $htmlcode;
	$dataArr['start_url'] = $step1Arr['start_url'];
	$dataArr['url_regex'] = $step1Arr['url_regex'];
	$dataArr['url_regex2'] = $step1Arr['url_regex2'];
	$dataArr['hzw_appid'] = hzw_appid(1);
	$dataArr['hzw_sign'] = hzw_sign($dataArr);
	$dataStr = serialize($dataArr);
	$dataStr = base64_encode($dataStr);
	$resultStr = dfsockopen('http://www.csdn123.net/zd_version/zd9_5/url_step1.php',0,array('dataStr'=>$dataStr));
	$resultStr = base64_decode($resultStr);
	$resultLink = dunserialize($resultStr);
	if(is_array($resultLink) && count($resultLink)>2)
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
			$newsArr['rule_id']=intval($id);
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
		cpmsg($succeed_catch_rule,$pendingUrl,"succeed");
		
	} else {
		
		cpmsg("csdn123_news:fail",'',"error");
		
	}	
	
} elseif ($_GET['formhash'] == FORMHASH && !empty($_GET['step5']) && $_GET['step5']=='yes') {
	
	$step1Str = $_GET['step1Str'];
	$step2Str = $_GET['step2Str'];
	$step3Arr = array();
	$step3Arr['forum_portal'] = $_GET['forum_portal'];
	$step3Arr['fid'] = $_GET['fid'];
	$step3Arr['threadtypeid'] = $_GET['threadtypeid'];
	$step3Arr['portal_catid'] = $_GET['portal_catid'];
	$step3Arr['group_fid'] = $_GET['group_fid'];
	$step3Arr['uid'] = $_GET['uid'];
	$step3Arr['display_link'] = $_GET['display_link'];
	$step3Arr['image_localized'] = $_GET['image_localized'];
	$step3Arr['pseudo_original'] = $_GET['pseudo_original'];
	$step3Arr['chinese_encoding'] = $_GET['chinese_encoding'];
	$step3Arr['views'] = $_GET['views'];
	$step3Arr['release_time'] = $_GET['release_time'];
	$step3Str = serialize($step3Arr);
	$step3Str = base64_encode($step3Str);
	$start_url_str = base64_decode($step1Str);
	$start_url_Arr = dunserialize($start_url_str);
	$start_url = $start_url_Arr['start_url'];
	$ruleArr=array();
	$ruleArr['start_url'] = daddslashes($start_url);
	$ruleArr['step1'] = daddslashes($step1Str);
	$ruleArr['step2'] = daddslashes($step2Str);
	$ruleArr['step3'] = daddslashes($step3Str);
	if(!empty($_GET['rulemodify']) && is_numeric($_GET['rulemodify']))
	{	
		$id = intval($_GET['rulemodify']);
		DB::update('csdn123zd_rule', $ruleArr,array('ID'=>$id));
	} else {	
		DB::insert('csdn123zd_rule', $ruleArr);
	}
	cpmsg("csdn123_news:succeed",$server_url,"succeed");
	
} elseif ($_GET['formhash'] == FORMHASH && !empty($_GET['step4']) && $_GET['step4']=='yes') {
	
	$step1Str = $_GET['step1Str'];
	$step2Arr=array();
	$step2Arr['catch_mode'] = $_GET['catch_mode'];
	$step2Arr['webCodeCorrection'] = $_GET['webCodeCorrection'];
	$step2Arr['inputcookie'] = $_GET['inputcookie'];
	$step2Arr['title01_start'] = $_GET['title01_start'];
	$step2Arr['title01_end'] = $_GET['title01_end'];
	$step2Arr['title02_start'] = $_GET['title02_start'];
	$step2Arr['title02_end'] = $_GET['title02_end'];
	$step2Arr['title03_start'] = $_GET['title03_start'];
	$step2Arr['title03_end'] = $_GET['title03_end'];
	$step2Arr['title04_start'] = $_GET['title04_start'];
	$step2Arr['title04_end'] = $_GET['title04_end'];
	$step2Arr['title05_start'] = $_GET['title05_start'];
	$step2Arr['title05_end'] = $_GET['title05_end'];	
	$step2Arr['title_replace01_start'] = $_GET['title_replace01_start'];
	$step2Arr['title_replace01_end'] = $_GET['title_replace01_end'];
	$step2Arr['title_replace02_start'] = $_GET['title_replace02_start'];
	$step2Arr['title_replace02_end'] = $_GET['title_replace02_end'];
	$step2Arr['title_replace03_start'] = $_GET['title_replace03_start'];
	$step2Arr['title_replace03_end'] = $_GET['title_replace03_end'];
	$step2Arr['title_replace04_start'] = $_GET['title_replace04_start'];
	$step2Arr['title_replace04_end'] = $_GET['title_replace04_end'];
	$step2Arr['title_replace05_start'] = $_GET['title_replace05_start'];
	$step2Arr['title_replace05_end'] = $_GET['title_replace05_end'];
	$step2Arr['content01_start'] = $_GET['content01_start'];
	$step2Arr['content01_end'] = $_GET['content01_end'];
	$step2Arr['content02_start'] = $_GET['content02_start'];
	$step2Arr['content02_end'] = $_GET['content02_end'];
	$step2Arr['content03_start'] = $_GET['content03_start'];
	$step2Arr['content03_end'] = $_GET['content03_end'];
	$step2Arr['content04_start'] = $_GET['content04_start'];
	$step2Arr['content04_end'] = $_GET['content04_end'];
	$step2Arr['content05_start'] = $_GET['content05_start'];
	$step2Arr['content05_end'] = $_GET['content05_end'];
	$step2Arr['content_replace01_start'] = $_GET['content_replace01_start'];
	$step2Arr['content_replace01_end'] = $_GET['content_replace01_end'];
	$step2Arr['content_replace02_start'] = $_GET['content_replace02_start'];
	$step2Arr['content_replace02_end'] = $_GET['content_replace02_end'];
	$step2Arr['content_replace03_start'] = $_GET['content_replace03_start'];
	$step2Arr['content_replace03_end'] = $_GET['content_replace03_end'];
	$step2Arr['content_replace04_start'] = $_GET['content_replace04_start'];
	$step2Arr['content_replace04_end'] = $_GET['content_replace04_end'];
	$step2Arr['content_replace05_start'] = $_GET['content_replace05_start'];
	$step2Arr['content_replace05_end'] = $_GET['content_replace05_end'];
	$step2Str = serialize($step2Arr);
	$step2Str = base64_encode($step2Str);
	require_once libfile('function/forumlist');
	require_once libfile('function/portalcp');
	require_once libfile('function/group');
	$grouplistArr=grouplist('displayorder',false,100);
	$release_time=rand(1,3600);
	$release_time=time() - $release_time;
	$release_time=date('Y-m-d H:i:s',$release_time);
	if(!empty($_GET['rulemodify']) && is_numeric($_GET['rulemodify']))
	{	
		$id = intval($_GET['rulemodify']);
		$ruleRs = DB::fetch_first('SELECT * FROM ' . DB::table('csdn123zd_rule') . ' WHERE ID=' . $id);
		$step3Str = $ruleRs['step3'];
		$step3Str = base64_decode($step3Str);
		$step3Arr = dunserialize($step3Str);
		$typeclassArr = C::t('forum_threadclass')->fetch_all_by_fid($step3Arr['fid']);	
		include template('csdn123_news:url_step4_modify');
		
	} else {
		
		include template('csdn123_news:url_step4');
		
	}
	
} elseif ($_GET['formhash'] == FORMHASH && !empty($_GET['step3']) && $_GET['step3']=='yes') {
	
	$step1Str = $_GET['step1Str'];
	if(!empty($_GET['rulemodify']) && is_numeric($_GET['rulemodify']))
	{	
		$id = intval($_GET['rulemodify']);
		$ruleRs = DB::fetch_first('SELECT * FROM ' . DB::table('csdn123zd_rule') . ' WHERE ID=' . $id);
		$step2Str = $ruleRs['step2'];
		$step2Str = base64_decode($step2Str);
		$step2Arr = dunserialize($step2Str);
		array_walk($step2Arr,'hzw_rep');
		include template('csdn123_news:url_step3_modify');
		
	} else {
		
		include template('csdn123_news:url_step3');
	}
	
} elseif ($_GET['formhash'] == FORMHASH && !empty($_GET['step2']) && $_GET['step2']=='yes') {
	
	$inputcookie = $_GET['inputcookie'];
	if(strlen($_GET['start_url'])<10)
	{
		cpmsg("csdn123_news:start_url_error","","error");
	} else {	
		$start_url = $_GET['start_url'];
	}
	if(empty($_GET['next_step_skip']))
	{	
		if(empty($_GET['htmlcode']) || strlen($_GET['htmlcode'])<100)
		{	
			$htmlcode = ext_dfsockopen($start_url,$inputcookie);

		} else {
			
			$htmlcode = $_GET['htmlcode'];
			$htmlcode = base64_decode($htmlcode);
			
		}
		if(strlen($htmlcode)<50)
		{
			cpmsg("csdn123_news:fail","","error");
		}
		$dataArr = array();
		$dataArr['htmlcode'] = $htmlcode;
		$dataArr['start_url'] = $start_url;
		$dataArr['url_regex'] = $_GET['url_regex'];
		$dataArr['url_regex2'] = $_GET['url_regex2'];
		$dataArr['hzw_appid'] = hzw_appid(1);
		$dataArr['hzw_sign'] = hzw_sign($dataArr);
		$dataStr = serialize($dataArr);
		$dataStr = base64_encode($dataStr);
		$resultStr = dfsockopen('http://www.csdn123.net/zd_version/zd9_5/url_step1.php',0,array('dataStr'=>$dataStr));
		$resultStr = base64_decode($resultStr);
		$resultArr = dunserialize($resultStr);
	}
	$step1Arr=array();
	$step1Arr['start_url'] = $start_url;
	$step1Arr['url_regex'] = $_GET['url_regex'];
	$step1Arr['url_regex2'] = $_GET['url_regex2'];
	$step1Arr['inputcookie'] = $inputcookie;
	$step1Str = serialize($step1Arr);
	$step1Str = base64_encode($step1Str);
	if(empty($_GET['next_step_skip']))
	{
		include template('csdn123_news:url_step2');
		
	} elseif(!empty($_GET['next_step_skip']) && empty($_GET['rulemodify'])) {
		
		include template('csdn123_news:url_step3');	
		
	} elseif(!empty($_GET['next_step_skip']) && !empty($_GET['rulemodify'])) {
		
		$id = intval($_GET['rulemodify']);
		$ruleRs = DB::fetch_first('SELECT * FROM ' . DB::table('csdn123zd_rule') . ' WHERE ID=' . $id);
		$step2Str = $ruleRs['step2'];
		$step2Str = base64_decode($step2Str);
		$step2Arr = dunserialize($step2Str);
		array_walk($step2Arr,'hzw_rep');
		include template('csdn123_news:url_step3_modify');
	}
	
} elseif ($_GET['formhash'] == FORMHASH && !empty($_GET['step1']) && $_GET['step1']=='yes') {
	
	if(!empty($_GET['rulemodify']) && is_numeric($_GET['rulemodify']))
	{	
		$id = intval($_GET['rulemodify']);
		$ruleRs = DB::fetch_first('SELECT * FROM ' . DB::table('csdn123zd_rule') . ' WHERE ID=' . $id);
		$step1Str = $ruleRs['step1'];
		$step1Str = base64_decode($step1Str);
		$step1Arr = dunserialize($step1Str);
		include template('csdn123_news:url_step1_modify');
		
	} else {
		
		include template('csdn123_news:url_step1');
		
	}	
	
} else {

	$ruleRs=DB::fetch_all('SELECT * FROM ' . DB::table('csdn123zd_rule') . ' ORDER BY ID DESC');
	include template('csdn123_news:urlAdminRule');	
	
}
?>