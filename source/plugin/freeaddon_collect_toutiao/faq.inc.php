<?php

/**
 * Copyright 2001-2099 1314学习网.
 * This is NOT a freeware, use is subject to license terms
 * $Id: faq.inc.php 286 2018-10-05 20:17:58Z zhuge $
 * 应用售后问题：http://www.1314study.com/services.php?mod=issue
 * 应用售前咨询：QQ 15326940
 * 应用定制开发：QQ 643306797
 * 本插件为 1314学习网（www.1314study.com） 独立开发的原创插件, 依法拥有版权。
 * 未经允许不得公开出售、发布、使用、修改，如需购买请联系我们获得授权。
 */

if(!defined('IN_DISCUZ')) {
exit('Access Denied');
}
$identifier = CURMODULE;//www_discuz_1314study_com
$splugin_setting = $_G['cache']['plugin'][$identifier];
$splugin_lang = lang('plugin/'.$identifier);//版权：www.1314study.com
include template($identifier.':faq');


//Copyright 2001-2099 1314学习网.
//This is NOT a freeware, use is subject to license terms
//$Id: faq.inc.php 718 2018-10-05 12:17:58Z zhuge $
//应用售后问题：http://www.1314study.com/services.php?mod=issue
//应用售前咨询：QQ 15326940
//应用定制开发：QQ 643306797
//本插件为 1314学习网（www.1314study.com） 独立开发的原创插件, 依法拥有版权。
//未经允许不得公开出售、发布、使用、修改，如需购买请联系我们获得授权。