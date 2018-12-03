<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
function zaker_getRndUid($num = 80) {
    global $_G;
    $uidarray = DB::fetch_all('SELECT uid FROM ' . DB::table('csdn123zaker_reguser') . ' ORDER BY RAND() LIMIT ' . $num);
    foreach ($uidarray as $uidvalue) {
        $uidstr = $uidvalue['uid'] . ',' . $uidstr;
    } 
    $uidstr = substr($uidstr, 0, .1);
    if ($uidstr == "" || empty($uidstr)) {
        return $_G['uid'];
    } else {
        return $uidstr;
    } 
}
function zaker_getOneUid($str) {
    if (strpos($str, ',') == false && is_numeric($str)) {
        return $str;
    } else {
        $strArr = explode(',', $str);
        shuffle($strArr);
        return $strArr[1];
    } 
}
class plugin_csdn123com_zaker {
	protected static $csdn123_zaker_conf = array();
	public function plugin_csdn123com_zaker() {
		global $_G;
		if (!isset($_G['cache']['plugin'])) {
			loadcache('plugin');
		}
		self::$csdn123_zaker_conf = $_G['cache']['plugin']['csdn123com_zaker'];
		$csdn123_uid = self::$csdn123_zaker_conf['csdn123_uid'];
		if (strpos($csdn123_uid, ',') === false) {
			$csdn123_uid = array($csdn123_uid);
		} else {
			$csdn123_uid = explode(',', $csdn123_uid);
		}
		if (in_array($_G['uid'],$csdn123_uid)) {
			
			self::$csdn123_zaker_conf['disable']=false;			
			
		} else {
			
			self::$csdn123_zaker_conf['disable']=true;
		}
	}
	function global_footer()
	{
		global $_G;
		$csdn123com_zaker_cronUrl = $_G['siteurl'] . 'plugin.php?id=csdn123com_zaker';
		return '<script defer="defer" src="' . $csdn123com_zaker_cronUrl . '"></script>';
	}
}
class plugin_csdn123com_zaker_forum extends plugin_csdn123com_zaker {
	public function post_top_output() {
		global $_G;
		if(!self::$csdn123_zaker_conf['disable'])	{		
			
			$reply_uid=zaker_getRndUid();
			$first_uid=zaker_getRndUid(30);
			$first_uid=zaker_getOneUid($first_uid);
			$views=rand(1,1000);
			$csdn123_showmore = self::$csdn123_zaker_conf['csdn123_showmore'];
			include template('csdn123com_zaker:zaker_forum');
			return $csdn123com_zaker_return;				
					
		}
	}
}
class plugin_csdn123com_zaker_portal extends plugin_csdn123com_zaker {
	public function portalcp_top_output() {
		global $_G;
		if(!self::$csdn123_zaker_conf['disable'])	{					
			
			$reply_uid=zaker_getRndUid();
			$first_uid=zaker_getRndUid(30);
			$first_uid=zaker_getOneUid($first_uid);
			$views=rand(1,1000);
			$csdn123_showmore = self::$csdn123_zaker_conf['csdn123_showmore'];
			include template('csdn123com_zaker:zaker_portal');
			return $csdn123com_zaker_return;
		}
	}
}
class plugin_csdn123com_zaker_group extends plugin_csdn123com_zaker {
	public function post_top_output() {
		global $_G;
		if(!self::$csdn123_zaker_conf['disable'])	{
			
			$reply_uid=zaker_getRndUid();
			$first_uid=zaker_getRndUid(30);
			$first_uid=zaker_getOneUid($first_uid);
			$views=rand(1,1000);
			$csdn123_showmore = self::$csdn123_zaker_conf['csdn123_showmore'];
			include template('csdn123com_zaker:zaker_group');
			return $csdn123com_zaker_return;
		}
	}
}
?>