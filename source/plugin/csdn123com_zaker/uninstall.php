<?php

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

runquery('DROP TABLE IF EXISTS `pre_csdn123zaker_news`');
runquery('DROP TABLE IF EXISTS `pre_csdn123zaker_cron`');
runquery('DROP TABLE IF EXISTS `pre_csdn123zaker_reguser`');
runquery('DROP TABLE IF EXISTS `pre_csdn123zaker_weiyanchang`');
runquery('DROP TABLE IF EXISTS `pre_csdn123zaker_words`');

$finish = true;

