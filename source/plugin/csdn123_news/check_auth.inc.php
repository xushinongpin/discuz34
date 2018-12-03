<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
if(empty($_GET['hzw_appid']))
{
	echo 'hzw_appid_empty';
	exit;
} else {
	$hzw_appid=$_GET['hzw_appid'];
	$hzw_appid = preg_replace('/\W+/','',$hzw_appid);
}
$ajaxUrl='http://www.zhiwu55.com/authorization/pay/check_auth.php?hzw_appid=' . $hzw_appid;
$result=dfsockopen($ajaxUrl);
echo $result;

?>