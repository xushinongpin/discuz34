<?php
if (empty($_GET["url"])) {	if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)	{		$ishttps = true;			} else {				$ishttps = false;			}	if(isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off')	{		$ishttps = true;	}	if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https')	{		$ishttps = true;	}		if(isset($_SERVER['HTTP_X_CLIENT_SCHEME']) && strtolower($_SERVER['HTTP_X_CLIENT_SCHEME']) == 'https')	{		$ishttps = true;	}	
	$httpscheme = $ishttps?'https://':'http://';	$sitepath = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));	$siteroot = $httpscheme . $_SERVER['HTTP_HOST'] . $sitepath;	$img = $siteroot . '/template/error.png';	
} else {
	$img = $_GET["url"];
}$img = urldecode($img);
if (strpos(strtolower($img), 'gif') !== false) {
	header('Content-Type: image/gif');
} elseif (strpos(strtolower($img), 'png') !== false) {
	header('Content-Type: image/png');
} else {
	header('Content-Type: image/jpeg');
}
$urlinfo = parse_url($img);
$refererUrl = 'http://' . $urlinfo["host"];
if ($refererUrl == 'http://mmbiz.qpic.cn') {
	$refererUrl = 'http://www.qq.com';	$img = str_replace('https://','http://',$img);
}
if ($refererUrl == 'http://mmbiz.qlogo.cn') {
	$refererUrl = 'http://www.qq.com';	$img = str_replace('https://','http://',$img);
}
if (strpos($refererUrl, 'zhihu.com') !== false) {
	$refererUrl = 'http://www.zhihu.com/';
}
if (strpos($refererUrl, 'nipic.com') !== false) {
	$refererUrl = 'http://www.nipic.com/';
}
if (strpos($refererUrl, 'baidu.com') !== false) {
	$refererUrl = 'http://www.baidu.com/';
}
if (strpos($refererUrl, 'inews.gtimg.com') !== false) {
	$refererUrl = '';
}
if (function_exists('curl_init') && function_exists('curl_exec')) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $img);
	if ($refererUrl != '') {
		curl_setopt($ch, CURLOPT_REFERER, $refererUrl);
	}	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
	$return = curl_exec($ch);
	curl_close($ch);
}
?> 