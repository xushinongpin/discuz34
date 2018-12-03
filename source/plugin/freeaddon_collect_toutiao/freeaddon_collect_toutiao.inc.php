<?php

/**
 * Copyright 2001-2099 1314学习网.
 * This is NOT a freeware, use is subject to license terms
 * $Id: freeaddon_collect_toutiao.inc.php 420 2018-10-05 20:17:58Z zhuge $
 * 应用售后问题：http://www.1314study.com/services.php?mod=issue
 * 应用售前咨询：QQ 15326940
 * 应用定制开发：QQ 643306797
 * 本插件为 1314学习网（www.1314study.com） 独立开发的原创插件, 依法拥有版权。
 * 未经允许不得公开出售、发布、使用、修改，如需购买请联系我们获得授权。
 */
if (!defined('IN_DISCUZ')) {
exit('Access Denied');
}
if(!isset($_G['cache']['plugin'])){
loadcache('plugin');
}
if(in_array('addon_coll'.'ect_toutiao', $_G['setting']['plugins']['available'])) {
	include_once libfile('class/rule', 'plugin/addon_coll'.'ect_toutiao/source');
}

require_once libfile('function/core', 'plugin/freeaddon_collect_toutiao/source');

freeaddon_collect_toutiao_init();
exit;

//Copyright 2001-2099 1314学习网.
//This is NOT a freeware, use is subject to license terms
//$Id: freeaddon_collect_toutiao.inc.php 874 2018-10-05 12:17:58Z zhuge $
//应用售后问题：http://www.1314study.com/services.php?mod=issue
//应用售前咨询：QQ 15326940
//应用定制开发：QQ 643306797
//本插件为 1314学习网（www.1314study.com） 独立开发的原创插件, 依法拥有版权。
//未经允许不得公开出售、发布、使用、修改，如需购买请联系我们获得授权。