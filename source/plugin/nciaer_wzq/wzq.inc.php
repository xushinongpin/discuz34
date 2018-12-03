<?php
if(!defined('IN_DISCUZ')) {
   exit('Access Deined');
}
if(!$_G["cache"]["plugin"]["nciaer_wzq"]["freeuser"]) {
	//登录才能使用
	if(empty($_G["uid"])) {
		
		showmessage('to_login', 'member.php?mod=logging&action=login', array(), array('showmsg' => true, 'login' => 1));
		
	}
	
}

include template('nciaer_wzq:index');