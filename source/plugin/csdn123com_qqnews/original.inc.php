<?php
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$server_url = 'action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123com_qqnews&pmod=original';
if ($_GET['formhash'] == FORMHASH && empty($_GET['weiyanchang_add']) == false && $_GET['weiyanchang_add'] == 'show') {
	
	include template('csdn123com_qqnews:original_add');
	
} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['weiyanchang_import']) == false && $_GET['weiyanchang_import'] == 'show') {
	
	include template('csdn123com_qqnews:original_import');

} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['weiyanchang_auto_import']) == false && $_GET['weiyanchang_auto_import'] == 'yes') {
	
	if(empty($_GET['n']) || is_numeric($_GET['n'])==false)
	{
		$n=1;
		
	} else {
		
		$n=$_GET['n'];
		
	}
	$autoImportDataUrl = "http://www.csdn123.net/zd_version/zd9/original_data/{$n}.php?" . urlencode($_G['siteurl']);
	$autoImportData = dfsockopen($autoImportDataUrl);
	if (strlen($autoImportData) < 100) {
		$autoImportData = dfsockopen($autoImportDataUrl, 0, '', '', FALSE, '', 15, TRUE, 'URLENCODE', FALSE);
	}
	$autoImportData = preg_replace('/\s+/','',$autoImportData);
	$autoImportData = base64_decode($autoImportData);
	$autoImportDataArr = dunserialize($autoImportData);
	if(is_array($autoImportDataArr)==false)
	{
		cpmsg('csdn123com_qqnews:data_wrong_format', '', 'error');
	}
	$word_charset=$autoImportDataArr['charset'];
	foreach($autoImportDataArr['word'] as $word_value)
	{
		$word1=diconv($word_value['word1'],$word_charset,CHARSET);
		$word2=diconv($word_value['word2'],$word_charset,CHARSET);
		$wordSql="REPLACE INTO " . DB::table('csdn123qqnews_weiyanchang') . "(word1,word2) VALUES('" . $word1 . "','" . $word2 . "')";
		DB::query($wordSql);
	}
	if($n>=6)
	{
		cpmsg('csdn123com_qqnews:succeed', $server_url, 'succeed');
	} else {
		$n = $n + 1;
	}
	$next_original_url = 'action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123com_qqnews&pmod=original&weiyanchang_auto_import=yes&formhash=' . FORMHASH . '&n=' . $n;
	$original_autoimport_info = lang('plugin/csdn123com_qqnews', 'original_autoimport_info');
	$original_autoimport_info = str_replace('n',$n,$original_autoimport_info);	
	cpmsg($original_autoimport_info, $next_original_url, 'loading', '', FALSE);
	
	
} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['import_word']) == false && $_GET['import_word'] == 'yes') {
	
	$word_data=$_GET['word_data'];
	$word_data=preg_replace('/\s+/','',$word_data);
	if(strpos($word_data,',')!=false && strpos($word_data,'=')!=false)
	{		
		$word_data_arr=explode(',',$word_data);
		foreach($word_data_arr as $word_value)
		{
			$word_value_arr=explode('=',$word_value);
			$word1=daddslashes($word_value_arr[0]);
			$word2=daddslashes($word_value_arr[1]);
			$wordSql="REPLACE INTO " . DB::table('csdn123qqnews_weiyanchang') . "(word1,word2) VALUES('" . $word1 . "','" . $word2 . "')";
			DB::query($wordSql);			
			
		}
		cpmsg('csdn123com_qqnews:succeed', $server_url, 'succeed');
	}
	$word_data=base64_decode($word_data);
	$word_arr=dunserialize($word_data);
	if(is_array($word_arr)==false)
	{
		cpmsg('csdn123com_qqnews:data_wrong_format', '', 'error');
	}
	$word_charset=$word_arr['charset'];
	foreach($word_arr['word'] as $word_value)
	{
		$word1=diconv($word_value['word1'],$word_charset,CHARSET);
		$word2=diconv($word_value['word2'],$word_charset,CHARSET);
		$wordSql="REPLACE INTO " . DB::table('csdn123qqnews_weiyanchang') . "(word1,word2) VALUES('" . $word1 . "','" . $word2 . "')";
		DB::query($wordSql);
	}
	cpmsg('csdn123com_qqnews:succeed', $server_url, 'succeed');
	
	
} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['weiyanchang_output']) == false && $_GET['weiyanchang_output'] == 'yes') {
	
	$weiyanchang_Data=array();
	$weiyanchang_Data['charset']=CHARSET;
	$weiyanchang_Rs=DB::fetch_all("SELECT word1,word2 FROM " . DB::table('csdn123qqnews_weiyanchang'));
	$weiyanchang_Data['word']=$weiyanchang_Rs;
	$weiyanchang_output=serialize($weiyanchang_Data);
	$weiyanchang_output=base64_encode($weiyanchang_output);
	include template('csdn123com_qqnews:original_output');

} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['delword']) == false && $_GET['delword'] == 'yes') {
	
	if(empty($_GET['idarray']))
	{	
		cpmsg('csdn123com_qqnews:select_empty', '' , 'error');		
	}
	$idstr = implode(',', $_GET['idarray']);
	$idstr = daddslashes($idstr);
	DB::delete('csdn123qqnews_weiyanchang','ID in (' . $idstr . ')');	
	cpmsg('csdn123com_qqnews:succeed',$server_url . '&page=' . $_GET['page'], 'succeed');

} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['addword']) == false && $_GET['addword'] == 'yes') {
	
	if(empty($_GET['word1']) || empty($_GET['word2']))
	{
		cpmsg('csdn123com_qqnews:keywords_empty', '', 'error');
	}
	$word1=daddslashes($_GET['word1']);
	$word2=daddslashes($_GET['word2']);	
	$wordSql="REPLACE INTO " . DB::table('csdn123qqnews_weiyanchang') . "(word1,word2) VALUES('" . $word1 . "','" . $word2 . "')";
	DB::query($wordSql);
	cpmsg('csdn123com_qqnews:succeed', $server_url, 'succeed');

} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['modify_id']) == false && is_numeric($_GET['modify_id']) == true) {
	
	$modify_rs=DB::fetch_first("SELECT * FROM " . DB::table('csdn123qqnews_weiyanchang') . " WHERE ID=" . $_GET['modify_id']);
	include template('csdn123com_qqnews:original_modify');

} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['modify_submit']) == false && $_GET['modify_submit'] == 'yes') {
	
	if(empty($_GET['word1']) || empty($_GET['word2']))
	{
		cpmsg('csdn123com_qqnews:word_add_err', '', 'error');
	}
	$ID=intval($_GET['ID']);
	$word1=daddslashes($_GET['word1']);
	$word2=daddslashes($_GET['word2']);
	$wordArr=array();
	$wordArr['word1']=$word1;
	$wordArr['word2']=$word2;
	DB::update('csdn123qqnews_weiyanchang',$wordArr,'ID=' . $ID);
	cpmsg('csdn123com_qqnews:succeed', $server_url . '&page=' . $page, 'succeed');

} elseif ($_GET['formhash'] == FORMHASH && empty($_GET['weiyanchang_clear']) == false && $_GET['weiyanchang_clear'] == 'yes') {
	
	DB::delete('csdn123qqnews_weiyanchang','ID>0');
	cpmsg('csdn123com_qqnews:succeed',$server_url , 'succeed');
		
} else {
	
	if(empty($_GET['page']) || is_numeric($_GET['page'])==false)
	{
		$page=1;
	} else {
		$page=$_GET['page'];
	}
	$page=max(1,$page);
	$weiyanchangCount = DB::result_first('SELECT count(*) FROM ' . DB::table('csdn123qqnews_weiyanchang'));
	if($weiyanchangCount>0)
	{
		$TotalNumber = lang('plugin/csdn123com_qqnews', 'total_number');
		$TotalPageNumber = $weiyanchangCount/20;
		$TotalPageNumber = @ceil($TotalPageNumber);
		$TotalNumber = str_replace('x',$TotalPageNumber,$TotalNumber);
		$TotalNumber = str_replace('y',$weiyanchangCount,$TotalNumber);
		$TotalNumber = str_replace('z',$page,$TotalNumber);
		$page = min($TotalPageNumber,$page);
		$startNum = ($page - 1) * 20;
		$homePage = $server_url . '&page=1';
		$nextPage = $server_url . '&page=' . ($page + 1);
		$prePage = $server_url . '&page=' . ($page - 1);
		$endPage = $server_url . '&page=' . $TotalPageNumber;	
		$weiyanchang_list = DB::fetch_all('SELECT * FROM ' . DB::table('csdn123qqnews_weiyanchang') . ' ORDER BY ID DESC LIMIT ' . $startNum . ',20');
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
	include template('csdn123com_qqnews:original');
	
}
