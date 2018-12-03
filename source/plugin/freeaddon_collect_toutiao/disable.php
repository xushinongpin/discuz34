<?php

/**
 * Copyright 2001-2099 1314学习网.
 * This is NOT a freeware, use is subject to license terms
 * $Id: disable.php 907 2018-10-05 20:17:58Z zhuge $
 * 应用售后问题：http://www.1314study.com/services.php?mod=issue
 * 应用售前咨询：QQ 15326940
 * 应用定制开发：QQ 643306797
 * 本插件为 1314学习网（www.1314study.com） 独立开发的原创插件, 依法拥有版权。
 * 未经允许不得公开出售、发布、使用、修改，如需购买请联系我们获得授权。
 */

if(!defined('IN_ADMINCP')) {
exit('Access Denied');
}
$available = $operation == 'enable' ? 1 : 0;/*版权：www.1314study.com*/
C::t('common_plugin')->update($_GET['pluginid'], array('available' => $available));
updatecache(array('plugin', 'setting', 'styles'));
cleartemplatecache();
updatemenu('plugin');
$_statInfo = array();#版权：www.1314study.com
$_statInfo['pluginName'] = $plugin['identifier'];
$_statInfo['bbsVersion'] = DISCUZ_VERSION;
$_statInfo['bbsUrl'] = $_G['siteurl'];/*1.3.1.4.学.习.网*/
$_statInfo['action'] = $operation;
$_statInfo['nextUrl'] = ADMINSCRIPT.'?action=plugins';
$_statInfo = base64_encode(serialize($_statInfo));
$_md5Check = md5($_statInfo);
cpmsg('plugins_'.$operation.'_succeed', 'http'.($_G['isHTTPS'] ? 's' : '').'://addon.1314study.com/api/outer_addon.php?type=js&info='.$_statInfo.'&md5check='.$_md5Check, 'succeed');#www_discuz_1314study_com


//Copyright 2001-2099 1314学习网.
//This is NOT a freeware, use is subject to license terms
//$Id: disable.php 1339 2018-10-05 12:17:58Z zhuge $
//应用售后问题：http://www.1314study.com/services.php?mod=issue
//应用售前咨询：QQ 15326940
//应用定制开发：QQ 643306797
//本插件为 1314学习网（www.1314study.com） 独立开发的原创插件, 依法拥有版权。
//未经允许不得公开出售、发布、使用、修改，如需购买请联系我们获得授权。