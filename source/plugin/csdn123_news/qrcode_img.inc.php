<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$qrUrl = urldecode($_GET['qrUrl']);
if(stripos($qrUrl,$_G['siteurl'])===false)
{
	echo "Only allow the site's address to be converted into a two-dimensional code.";
	exit;
}	
include_once './source/plugin/csdn123_news/phpqrcode.php';
QRcode::png($qrUrl,false,QR_ECLEVEL_L,10);

?>