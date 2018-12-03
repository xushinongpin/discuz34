<?php
/**
 *	[BON]在线听歌(bon_songs) (C) 2018 Powered by 西安黑橙网络科技有限公司.
 *	Version: 1.3
 *	Date: 2018-7-3
 *	From: www.bontc.com
 */

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

global $_G;
$bon['songs'] = $_G['cache']['plugin']['bon_songs'];

$navtitle = $bon['songs']['name'];

if(!$bon['songs']['switch'] && !$_G['adminid']){
    showmessage($bon['songs']['closetext']);
}

$songs['url'] = array(
    'jiuku'     => 'http://app.9ku.com/hao123/',
    'kuwo'      => 'http://player.kuwo.cn/webmusic/webmusic2011/hao123',
    'kugou'     => 'http://web.kugou.com/sogou.html',
    'baidu'     => 'http://fm.baidu.com/',
    'music163'  => 'http://music.163.com/embedapp',
    'yiting'    => 'http://www.1ting.com/api/sogou/',
    'yinyuetai' => 'http://www.yinyuetai.com/baidu/',
    'xiami'     => 'http://www.xiami.com/player',
    'kuwodt'    => 'http://player.kuwo.cn/webmusic/kuwodt/diantai123.html',
    'beva'      => 'http://app.beva.com/2345/fm/navfm.html'
);

include_once template('bon_songs:module');