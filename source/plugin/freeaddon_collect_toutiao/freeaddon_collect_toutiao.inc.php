<?php

/**
 * Copyright 2001-2099 1314ѧϰ��.
 * This is NOT a freeware, use is subject to license terms
 * $Id: freeaddon_collect_toutiao.inc.php 420 2018-10-05 20:17:58Z zhuge $
 * Ӧ���ۺ����⣺http://www.1314study.com/services.php?mod=issue
 * Ӧ����ǰ��ѯ��QQ 15326940
 * Ӧ�ö��ƿ�����QQ 643306797
 * �����Ϊ 1314ѧϰ����www.1314study.com�� ����������ԭ�����, ����ӵ�а�Ȩ��
 * δ�������ù������ۡ�������ʹ�á��޸ģ����蹺������ϵ���ǻ����Ȩ��
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

//Copyright 2001-2099 1314ѧϰ��.
//This is NOT a freeware, use is subject to license terms
//$Id: freeaddon_collect_toutiao.inc.php 874 2018-10-05 12:17:58Z zhuge $
//Ӧ���ۺ����⣺http://www.1314study.com/services.php?mod=issue
//Ӧ����ǰ��ѯ��QQ 15326940
//Ӧ�ö��ƿ�����QQ 643306797
//�����Ϊ 1314ѧϰ����www.1314study.com�� ����������ԭ�����, ����ӵ�а�Ȩ��
//δ�������ù������ۡ�������ʹ�á��޸ģ����蹺������ϵ���ǻ����Ȩ��