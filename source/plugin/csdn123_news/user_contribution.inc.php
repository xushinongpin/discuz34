<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
if (!isset($_G['cache']['plugin'])) {
	loadcache('plugin');
}
$csdn123_sendwx = $_G['cache']['plugin']['csdn123_news']['csdn123_sendwx'];
if($csdn123_sendwx != 1)
{
	echo "<div style=\"text-align:center;margin:64px;\">I'm sorry! You do not open online contributions, please go to the plug-in background settings, where open.</div>";
	exit;
		
}
if ($_GET['formhash'] == FORMHASH && !empty($_GET['contribution']) && $_GET['contribution'] == 'yes') {
	
	if(empty($_GET['url']) || stripos($_GET['url'],'http')===false)
	{
		$fail=true;	
		
	} else {
		
		$url=$_GET['url'];
		$url=daddslashes($url);
		$ID=DB::insert('csdn123zd_contribute',array('url'=>$url),true,true);
		if($ID>0)
		{
			$fail=false;
		} else {
			$fail=true;
		}
	}
	include template("csdn123_news:contribution_result");
	
} else {
	
	include template("csdn123_news:user_contribution");

}
