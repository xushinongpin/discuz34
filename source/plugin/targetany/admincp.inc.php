<?php

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

require_once(DISCUZ_ROOT . '/source/plugin/targetany/targetany.config.php');

if ($_G['adminid'] < 1) {
    exit('Access Denied:0032');
}

$version = ta_get_version();
// 判断地址是否为内网
function isIntranet($addr){
    //验证是否是 IPv4
    if(!filter_var($addr, FILTER_VALIDATE_IP,  FILTER_FLAG_IPV4)){
        return false;
    }
    //是否为 内网
    if(!filter_var($addr, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)){
        return true;
    }
    return false;
}
$basic_web_address = str_replace('\\', '/', $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']));
$is_intranet = isIntranet($_SERVER['HTTP_HOST']);
include template('targetany:settings');
