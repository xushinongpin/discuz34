<?php

/**
 * Copyright 2001-2099 1314ѧϰ��.
 * This is NOT a freeware, use is subject to license terms
 * $Id: hook.class.php 1235 2018-10-05 20:17:58Z zhuge $
 * Ӧ���ۺ����⣺http://www.1314study.com/services.php?mod=issue
 * Ӧ����ǰ��ѯ��QQ 15326940
 * Ӧ�ö��ƿ�����QQ 643306797
 * �����Ϊ 1314ѧϰ����www.1314study.com�� ����������ԭ�����, ����ӵ�а�Ȩ��
 * δ�������ù������ۡ�������ʹ�á��޸ģ����蹺������ϵ���ǻ����Ȩ��
 */

if(!defined('IN_DISCUZ')) {
exit('Access Denied');
}
class plugin_freeaddon_collect_toutiao {	
	
}

class plugin_freeaddon_collect_toutiao_forum extends plugin_freeaddon_collect_toutiao {

	public function post_top_output() {
		global $_G;
		$return = '';
		$splugin_setting = $_G['cache']['plugin']['freeaddon_collect_toutiao'];
		$study_fids = (array)unserialize($splugin_setting['study_fids']);
		$study_gids = (array)unserialize($splugin_setting['study_gids']);
		if(in_array($_G['groupid'],$study_gids) && in_array($_G['fid'],$study_fids)){
			if(!$_GET['special'] && $_GET['action']=='newthread') {
				include template('freeaddon_collect_toutiao:gather_post');
			}
		}
		return $return;
	}
	
	public function post_editorctrl_left($param){
			global $_G;
			$return = '';
			$splugin_setting = $_G['cache']['plugin']['freeaddon_collect_toutiao'];
			$study_fids = (array)unserialize($splugin_setting['study_fids']);
			$study_gids = (array)unserialize($splugin_setting['study_gids']);
			if(in_array($_G['groupid'],$study_gids) && in_array($_G['fid'],$study_fids) && !$splugin_setting['showpost']){
					include template('freeaddon_collect_toutiao:box');
			}
			return $return;
	}
}


//Copyright 2001-2099 1314ѧϰ��.
//This is NOT a freeware, use is subject to license terms
//$Id: hook.class.php 1671 2018-10-05 12:17:58Z zhuge $
//Ӧ���ۺ����⣺http://www.1314study.com/services.php?mod=issue
//Ӧ����ǰ��ѯ��QQ 15326940
//Ӧ�ö��ƿ�����QQ 643306797
//�����Ϊ 1314ѧϰ����www.1314study.com�� ����������ԭ�����, ����ӵ�а�Ȩ��
//δ�������ù������ۡ�������ʹ�á��޸ģ����蹺������ϵ���ǻ����Ȩ��