<?php
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
require './source/plugin/csdn123_news/common.fun.php';
hzw_authorization(false,$pluginid);
$server_url = 'action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123_news&pmod=contribute';
if ($_GET['formhash'] == FORMHASH && !empty($_GET['delid']) && is_numeric($_GET['delid'])) {
	
	$delid=intval($_GET['delid']);
	DB::delete('csdn123zd_contribute','ID=' . $delid);
	cpmsg("csdn123_news:succeed",$server_url,"succeed");

} elseif ($_GET['formhash'] == FORMHASH && !empty($_GET['clears_all']) && $_GET['clears_all']=='yes') {
	
	DB::delete('csdn123zd_contribute','ID>0');
	cpmsg("csdn123_news:succeed",$server_url,"succeed");

} elseif ($_GET['formhash'] == FORMHASH && !empty($_GET['contribute_close']) && $_GET['contribute_close']=='yes') {
	
	C::t('common_pluginvar')->update_by_variable($pluginid, 'csdn123_sendwx', array('value'=>0));
	updatecache(array('plugin', 'setting', 'styles'));
	cleartemplatecache();
	cpmsg("csdn123_news:succeed",$server_url,"succeed");
	
} else {

	if (!isset($_G['cache']['plugin'])) {
		loadcache('plugin');
	}
	$csdn123_sendwx = $_G['cache']['plugin']['csdn123_news']['csdn123_sendwx'];
	if($csdn123_sendwx != 1)
	{
		echo '<div style="text-align:center;margin:64px;"><a href="?action=plugins&operation=config&do=' . $pluginid . '" style="font-size:24px;color:red;">' . lang('plugin/csdn123_news', 'no_online_contributions') . '</a></div>';
		
	} else {
		
		$server_single_url = 'action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123_news&pmod=url';
		$qr_code=$_G['siteurl'] . 'plugin.php?id=csdn123_news:qrcode_img&qrUrl=&qrUrl=' . urlencode($_G['siteurl'] . 'plugin.php?id=csdn123_news:user_contribution&mobile=no');
		$contribute_list = DB::fetch_all('SELECT * FROM ' . DB::table('csdn123zd_contribute') . ' ORDER BY ID DESC LIMIT 100');
		include template("csdn123_news:contribute");
	
	}
	
}
