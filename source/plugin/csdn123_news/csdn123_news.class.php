<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class plugin_csdn123_news {
	protected static $csdn123_conf = array();
	public function plugin_csdn123_news() {
		global $_G;
		if (!isset($_G['cache']['plugin'])) {
			loadcache('plugin');
		}
		self::$csdn123_conf = $_G['cache']['plugin']['csdn123_news'];
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
		$csdn123_news_cronUrl = $_G['siteurl'] . 'plugin.php?id=csdn123_news';
		return '<script defer="defer" src="' . $csdn123_news_cronUrl . '"></script>';
	}
}
class plugin_csdn123_news_forum extends plugin_csdn123_news {
	public function post_top_output() {
		global $_G;
		if(!self::$csdn123_conf['disable'])	{
						
			$csdn123_fid = self::$csdn123_conf['csdn123_bbsfid'];
			$csdn123_fid = (array)unserialize($csdn123_fid);
			if (empty($csdn123_fid[0]) || in_array($_GET['fid'], $csdn123_fid)) {
				
				$csdn123_keywords = self::$csdn123_conf['csdn123_keywords'];
				$csdn123_showmore = self::$csdn123_conf['csdn123_showmore'];
				include template('csdn123_news:post_forum');
				return $csdn123_news_return;
				
			}		
		}
	}
}
class plugin_csdn123_news_portal extends plugin_csdn123_news {
	public function portalcp_top_output() {
		global $_G;
		if(!self::$csdn123_conf['disable'])	{
						
			$csdn123_keywords = self::$csdn123_conf['csdn123_keywords'];
			$csdn123_showmore = self::$csdn123_conf['csdn123_showmore'];
			include template('csdn123_news:post_portal');
			return $csdn123_news_return;
		}
	}
}
class plugin_csdn123_news_group extends plugin_csdn123_news {
	public function post_top_output() {
		global $_G;
		if(!self::$csdn123_conf['disable'])	{
			
			$csdn123_keywords = self::$csdn123_conf['csdn123_keywords'];
			$csdn123_showmore = self::$csdn123_conf['csdn123_showmore'];
			include template('csdn123_news:post_group');
			return $csdn123_news_return;
		}
	}
}
?>