<?php
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$server_url = 'action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123_news&pmod=help';
require './source/plugin/csdn123_news/common.fun.php';
if(!empty($_GET['update']) && $_GET['update']=='yes')
{	
	$update_auth_ok = lang('plugin/csdn123_news', 'update_auth_ok');
	hzw_authorization(true,$pluginid);	
	cpmsg($update_auth_ok,$server_url,"succeed");
	
} else {
	
	hzw_authorization(false,$pluginid);
	
}
if(empty($_COOKIE['csdn123_news_authorization']))
{
	if (!isset($_G['cache']['csdn123_news_authorization'])) {
		loadcache('csdn123_news_authorization');
	}
	$htmlcode = $_G['cache']['csdn123_news_authorization'];
	
} else {
	
	$htmlcode = $_COOKIE['csdn123_news_authorization'];
	
}
$csdn123_arr = preg_replace('/^\s+|\s+$/','',$htmlcode);
$csdn123_arr = base64_decode($csdn123_arr);
$csdn123_arr = dunserialize($csdn123_arr);
if (!isset($_G['cache']['plugin'])) {
	loadcache('plugin');
}
$hzw_appid=hzw_appid(1);
$hzw_appsecret=$_G['cache']['plugin']['csdn123_news']['hzw_appsecret'];
include template('csdn123_news:help');
?>