<?php
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
function csdn123_avatar_by_uid($uid, $img) {
	
	global $_G;
	C::t('common_member')->update($uid, array('avatarstatus' => 1));
	$uc_server = basename($_G['setting']['ucenterurl']);
	$dir = DISCUZ_ROOT . './' . $uc_server . '/data/avatar';
	$uid = sprintf("%09d", $uid);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	!is_dir($dir . '/' . $dir1) && mkdir($dir . '/' . $dir1, 0777);
	!is_dir($dir . '/' . $dir1 . '/' . $dir2) && mkdir($dir . '/' . $dir1 . '/' . $dir2, 0777);
	!is_dir($dir . '/' . $dir1 . '/' . $dir2 . '/' . $dir3) && mkdir($dir . '/' . $dir1 . '/' . $dir2 . '/' . $dir3, 0777);
	$avatar_small = $dir . '/' . $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($uid, -2) . "_avatar_small.jpg";
	$avatar_middle = $dir . '/' . $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($uid, -2) . "_avatar_middle.jpg";
	$avatar_big = $dir . '/' . $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($uid, -2) . "_avatar_big.jpg";
	$imgData = dfsockopen($img);
	file_put_contents($avatar_small, $imgData);
	file_put_contents($avatar_middle, $imgData);
	file_put_contents($avatar_big, $imgData);
	
}
function ip_random()
{

	$ip_long = array(
	array('607649792', '608174079'),
	array('1038614528', '1039007743'),
	array('1783627776', '1784676351'),
	array('2035023872', '2035154943'),
	array('2078801920', '2079064063'),
	array('-1950089216', '-1948778497'),
	array('-1425539072', '-1425014785'),
	array('-1236271104', '-1235419137'),
	array('-770113536', '-768606209'),
	array('-569376768', '-564133889'),
	 );
	$rand_key = mt_rand(0, 9);
	$ip= long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
	return  $ip;
	
}
function reg_dateline()
{
	$startTime=time()-500000;
	$endTime=time();
	$regdate=rand($startTime,$endTime);
	return $regdate;
	
}
$server_url = 'action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123_news&pmod=adminVest&subpmod=regvest&formhash=' . FORMHASH;
if ($_GET['formhash'] == FORMHASH && empty($_GET['adduser']) == false && $_GET['adduser'] == 'yes') {
	
	$regData = dfsockopen('http://discuz.csdn123.net/avatar/get_user.php?siteurl=' . urlencode($_G['siteurl']) . '&ip=' . $_SERVER['REMOTE_ADDR'] . '&siteur1=' . urlencode($_SERVER['HTTP_HOST']));
	$regData = dunserialize($regData);
	if(is_array($regData)==false)
	{
		cpmsg("csdn123_news:reg_err", '', "error");
	}
	$newgroupid = $_GET['newgroupid'];
	$newgroupid = intval($newgroupid);
	$credits=$_GET['credits'];
	$credits=intval($credits);
	foreach ($regData as $regValue) {
		$csdn123_username = diconv($regValue['u'], 'UTF-8');
		$csdn123_username = addslashes($csdn123_username);
		$csdn123_img = $regValue['p'];
		if ($_GET['pws'] == 'xxxxxxxxxx') {
			$csdn123_password = random(10);
		} else {
			$csdn123_password = daddslashes($_GET['pws']);
		}
		$csdn123_email = addslashes($regValue['m']);
		$csdn123_ip = ip_random();
		if (C::t('common_member')->fetch_uid_by_username($csdn123_username) || C::t('common_member_archive')->fetch_uid_by_username($csdn123_username)) {
			continue;
		}
		loaducenter();
		$uid = uc_user_register($csdn123_username, $csdn123_password, $csdn123_email,'','',$csdn123_ip);
		if ($uid <= 0) {
			continue;
		}
		loadcache('fields_register');
		if($credits==1)
		{	
			$credits2=rand(1,20);
			$credits2=$credits2 * 2;
			$init_arr=array();
			$init_arr['credits'][0]=$credits2;
			$init_arr['credits'][1]=0;
			$init_arr['credits'][2]=$credits2;
			$init_arr['credits'][3]=0;
			$init_arr['credits'][4]=0;
			$init_arr['credits'][5]=0;
			$init_arr['credits'][6]=0;
			$init_arr['credits'][7]=0;
			$init_arr['credits'][8]=0;
		
		} else {
		
			$init_arr = explode(',', $_G['setting']['initcredits']); 
		
		}
		$common_member_password = md5(random(10));
		C::t('common_member')->insert($uid, $csdn123_username, $common_member_password, $csdn123_email, $csdn123_ip, $newgroupid, $init_arr, 0);
		$update_member=array();		
		$regdate=reg_dateline();		
		$update_member['regdate']=$regdate;		
		DB::update('common_member',$update_member,array('uid'=>$uid));		
		$update_common_member_status=array();		
		$update_common_member_status['lastip']=ip_random();		
		$lastvisit=$update_member['regdate'] + rand(86400,8640000);		
		if($lastvisit>time())
		{
			$lastvisit=time();
		}		
		$lastactivity = $lastvisit - rand(1800,43200);		
		$update_common_member_status['lastvisit']=$lastvisit;		
		$update_common_member_status['lastactivity']=$lastactivity;		
		DB::update('common_member_status',$update_common_member_status,array('uid'=>$uid));		
		$avatar = csdn123_avatar_by_uid($uid, $csdn123_img);		
		$addUser = array();
		$addUser['uid'] = $uid;
		$addUser['username'] = $csdn123_username;
		$addUser['username_pwd'] = $csdn123_password;
		$addUser['username_mail'] = $csdn123_email;
		DB::insert('csdn123zd_reguser', $addUser);
		
	}
	$user_list = DB::fetch_all('select * from ' . DB::table('csdn123zd_reguser') . ' order by uid desc');
	$server_url = 'action=plugins&operation=config&do=' . $pluginid . '&identifier=csdn123_news&pmod=adminVest';
	$totalmembers = C::t('common_member')->count();
	$data = array('totalmembers' => $totalmembers, 'newsetuser' => $csdn123_username);
	savecache('userstats', $data);
	cpmsg("csdn123_news:registered_successfully", $server_url, "succeed");

} else {
	
	$groupselect = array();
	$query = C::t('common_usergroup')->fetch_all_by_not_groupid(array(5, 6, 7));
	foreach ($query as $group) {
		$group['type'] = $group['type'] == 'special' && $group['radminid'] ? 'specialadmin' : $group['type'];
		if ($group['type'] == 'member' && $group['creditshigher'] == 0) {
			$groupselect[$group['type']].= "<option value=\"$group[groupid]\" selected>$group[grouptitle]</option>\n";
		} else {
			$groupselect[$group['type']].= "<option value=\"$group[groupid]\">$group[grouptitle]</option>\n";
		}
	}
	$groupselect = '<optgroup label="' . $lang['usergroups_member'] . '">' . $groupselect['member'] . '</optgroup>' . ($groupselect['special'] ? '<optgroup label="' . $lang['usergroups_special'] . '">' . $groupselect['special'] . '</optgroup>' : '') . ($groupselect['specialadmin'] ? '<optgroup label="' . $lang['usergroups_specialadmin'] . '">' . $groupselect['specialadmin'] . '</optgroup>' : '') . '<optgroup label="' . $lang['usergroups_system'] . '">' . $groupselect['system'] . '</optgroup>';
	include template('csdn123_news:regvest');
	
}
