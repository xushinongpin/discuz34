<?php

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

runquery('DROP TABLE IF EXISTS `pre_csdn123zd_weiyanchang`');
runquery('DROP TABLE IF EXISTS `pre_csdn123zd_words`');
runquery('DROP TABLE IF EXISTS `pre_csdn123zd_news`');
runquery('DROP TABLE IF EXISTS `pre_csdn123zd_contribute`');
runquery('DROP TABLE IF EXISTS `pre_csdn123zd_cron`');
runquery('DROP TABLE IF EXISTS `pre_csdn123zd_reguser`');
runquery('DROP TABLE IF EXISTS `pre_csdn123zd_words`');
runquery('DROP TABLE IF EXISTS `pre_csdn123zd_rule`');

$finish = true;

