<?php

if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
if (!isset($_G['cache']['plugin'])) {
	loadcache('plugin');
}
$hzw_appid=$_G['cache']['plugin']['csdn123_news']['hzw_appid'];
if (!empty($_GET["getremoteurl"])) {
	
	$csdn123_url = 'http://www.csdn123.net/zd_version/zd9_5/getContent.php?hzw_appid=' . $hzw_appid . '&siteurl=' . urlencode($_G['siteurl']) . '&charset=' . CHARSET . '&'. $_SERVER['QUERY_STRING'];
	$csdn123_return = dfsockopen($csdn123_url);
	echo $csdn123_return;
	
}
if (!empty($_GET["csdn123_jianTextData"])) {
	
	$csdn123_url = "http://www.csdn123.net/zd_version/zd9_5/convert_GBK_BIG_onearticle.php";
	$csdn123_data = array();
	$csdn123_data['convertType'] = $_GET['convertType'];
	$csdn123_data['textdata'] = urlencode($_GET["csdn123_jianTextData"]);
	$csdn123_data['siteurl'] = urlencode($_G["siteurl"]);
	$csdn123_data['charset'] = CHARSET;
	$csdn123_data['hzw_appid'] = $hzw_appid;
	$csdn123_return = dfsockopen($csdn123_url, 0, $csdn123_data);
	echo $csdn123_return;
	
}
if (!empty($_GET["search_query"])) {
	
	$csdn123_url = 'http://www.csdn123.net/zd_version/zd9_5/main_news.php?hzw_appid=' . $hzw_appid . '&siteurl=' . urlencode($_G['siteurl']) . '&charset=' . CHARSET . '&'. $_SERVER['QUERY_STRING'];
	$csdn123_return = dfsockopen($csdn123_url);
	echo $csdn123_return;
	
}
if (!empty($_GET["likearticleData"])) {
	$csdn123_url = "http://www.csdn123.net/zd_version/zd9_5/getKeywords.php";
	$likearticleData = $_GET["likearticleData"];
	$csdn123_data = array();
	$csdn123_data['likearticleData'] = $likearticleData;
	$csdn123_data['siteurl'] = urlencode($_G["siteurl"]);
	$csdn123_data['charset'] = CHARSET;
	$csdn123_data['hzw_appid'] = $hzw_appid;
	$csdn123_return = dfsockopen($csdn123_url, 0, $csdn123_data);
	echo $csdn123_return;
}
if (!empty($_GET["originality"])) {
	
	$wordRs = DB::fetch_all("SELECT word1,word2 FROM " . DB::table('csdn123zd_weiyanchang'));
	$wordStr = "";
	foreach ($wordRs as $wordValue) {
		$word1 = $wordValue['word1'];
		$word2 = $wordValue['word2'];
		$word2OneStr = mb_substr($word2,1,1,CHARSET);
		$word2hzw = '_hzw_' . $word2OneStr;
		$word2 = mb_ereg_replace($word2OneStr,$word2hzw,$word2);
		$wordStr = $word1 . '=' . $word2 . ',' . $wordStr;
	}
	echo substr($wordStr,0,-1);
	
}
if ($_GET["csdn123_localimg"] == "yes") {
	$csdn123_localimgUrl = $_GET["csdn123_localimgUrl"];
	$csdn123_localimgUrl = urldecode($csdn123_localimgUrl);
	$csdn123_localimgUrl = preg_replace('/^\/\//','http://',$csdn123_localimgUrl);
	if(stripos($csdn123_localimgUrl,'display_picture')==false)
	{	
		$csdn123_return = $_G['siteurl'] . "source/plugin/csdn123_news/display_picture.php?url=" . urlencode($csdn123_localimgUrl);
	} else {
		$csdn123_return = $csdn123_localimgUrl;
	}
	if(stripos($csdn123_localimgUrl,'.jp')===false && stripos($csdn123_localimgUrl,'gif')===false && stripos($csdn123_localimgUrl,'png')===false)
	{
		$csdn123_return=$csdn123_return . '&' . uniqid() . '.jpg';
	}
	echo $csdn123_return;
}

?>