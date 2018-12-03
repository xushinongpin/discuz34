<?php

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$server_url = 'action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123com_qqnews&pmod=help';
if($_GET['formhash'] == FORMHASH && $_GET['repair'] == 'yes')
{
	
	$lang_data=dfsockopen("http://discuz.csdn123.net/catch/qqnews201711/language_data.php");
	$lang_data=base64_decode($lang_data);
	$lang_data=preg_replace('/^\s+|\s+$/', '', $lang_data);
	$lang_data_arr=dunserialize($lang_data);
	if(is_array($lang_data_arr))
	{
		if($lang_data_arr['charset']!=CHARSET)
		{
			foreach($lang_data_arr['pluginlanguage_template'] as $key=>$value)
			{
				$lang_data_arr['pluginlanguage_template'][$key]=diconv($value,$lang_data_arr['charset'],CHARSET);
			}
			foreach($lang_data_arr['pluginlanguage_script'] as $key=>$value)
			{
				$lang_data_arr['pluginlanguage_script'][$key]=diconv($value,$lang_data_arr['charset'],CHARSET);
			}
			
		}		
		$_G['cache']['pluginlanguage_template'][$lang_data_arr['identifier']]=$lang_data_arr['pluginlanguage_template'];
		$_G['cache']['pluginlanguage_script'][$lang_data_arr['identifier']]=$lang_data_arr['pluginlanguage_script'];
		savecache('pluginlanguage_template',$_G['cache']['pluginlanguage_template']);
		savecache('pluginlanguage_script',$_G['cache']['pluginlanguage_script']);
		cpmsg('csdn123com_qqnews:language_import_success','', 'succeed');	
		
	} else {
		cpmsg('csdn123com_qqnews:fail','', 'error');
	}

} else {	

	include template('csdn123com_qqnews:help');

}

?>