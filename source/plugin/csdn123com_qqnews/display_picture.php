<?php
if (empty($_GET["url"])) {
	$img = "http://img.csdn123.net/csdn123_img_error.jpg";
} else {
	$img = $_GET["url"];
	$img = urldecode($img);
}
if (function_exists('curl_init') && function_exists('curl_exec')) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $img);
	curl_setopt($ch, CURLOPT_REFERER, 'https://www.qq.com');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
	$return = curl_exec($ch);
	curl_close($ch);
}
?> 