<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class plugin_csdn123com_qqnews {
	protected static $csdn123_conf = array();
	public function plugin_csdn123com_qqnews() {
		global $_G;
		if (!isset($_G['cache']['plugin'])) {
			loadcache('plugin');
		}
		self::$csdn123_conf = $_G['cache']['plugin']['csdn123com_qqnews'];
		$csdn123_uid = self::$csdn123_conf['csdn123_uid'];
		if (strpos($csdn123_uid, ',') === false) {
			$csdn123_uid = array($csdn123_uid);
		} else {
			$csdn123_uid = explode(',', $csdn123_uid);
		}
		if (in_array($_G['uid'],$csdn123_uid)) {
			
			self::$csdn123_conf['disable']=false;			
			
		} else {
			
			self::$csdn123_conf['disable']=true;
		}
	}
	function global_footer()
	{
		global $_G;
		$csdn123com_qqnews_cronUrl = $_G['siteurl'] . 'plugin.php?id=csdn123com_qqnews';
		return '<script defer="defer" src="' . $csdn123com_qqnews_cronUrl . '"></script>';
	}
}
class plugin_csdn123com_qqnews_forum extends plugin_csdn123com_qqnews {
	public function post_top_output() {
		global $_G;
		if(!self::$csdn123_conf['disable'])	{		
			
			require './source/plugin/csdn123com_qqnews/function_common.php';
			$reply_uid=getRndUid();
			$first_uid=getRndUid(30);
			$first_uid=getOneUid($first_uid);
			$views=rand(1,1000);
			$csdn123_showmore = self::$csdn123_conf['csdn123_showmore'];
			include template('csdn123com_qqnews:qqnews_forum');
			return $csdn123com_qqnews_return;				
					
		}
	}
}
class plugin_csdn123com_qqnews_portal extends plugin_csdn123com_qqnews {
	public function portalcp_top_output() {
		global $_G;
		if(!self::$csdn123_conf['disable'])	{					
			
			require './source/plugin/csdn123com_qqnews/function_common.php';
			$reply_uid=getRndUid();
			$first_uid=getRndUid(30);
			$first_uid=getOneUid($first_uid);
			$views=rand(1,1000);
			$csdn123_showmore = self::$csdn123_conf['csdn123_showmore'];
			include template('csdn123com_qqnews:qqnews_portal');
			return $csdn123com_qqnews_return;
		}
	}
}
class plugin_csdn123com_qqnews_group extends plugin_csdn123com_qqnews {
	public function post_top_output() {
		global $_G;
		if(!self::$csdn123_conf['disable'])	{
			
			require './source/plugin/csdn123com_qqnews/function_common.php';
			$reply_uid=getRndUid();
			$first_uid=getRndUid(30);
			$first_uid=getOneUid($first_uid);
			$views=rand(1,1000);
			$csdn123_showmore = self::$csdn123_conf['csdn123_showmore'];
			include template('csdn123com_qqnews:qqnews_group');
			return $csdn123com_qqnews_return;
		}
	}
}
?>