<?php

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

runquery('DROP TABLE IF EXISTS `pre_csdn123qqnews_news`');
runquery('DROP TABLE IF EXISTS `pre_csdn123qqnews_cron`');
runquery('DROP TABLE IF EXISTS `pre_csdn123qqnews_reguser`');
runquery('DROP TABLE IF EXISTS `pre_csdn123qqnews_weiyanchang`');

$finish = true;

