<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
function str_replace_once($needle, $replace, $haystack) {
	$pos = strpos($haystack, $needle);
	if ($pos === false) {
		return $haystack;
	}
	return substr_replace($haystack, $replace, $pos, strlen($needle));
}
function weixin_bbcode($htmlcode)
{
	$htmlcode = preg_replace('/<section[^<>]+?>|<\/section>/i','',$htmlcode);
	$htmlcode = str_ireplace('data-src','src',$htmlcode);
	$htmlcode = str_ireplace('data-w=','width=',$htmlcode);
	$htmlcode = str_ireplace('height="auto"','',$htmlcode);
	$htmlcode = preg_replace('/data\-\w+=(["\']?).+?\1/i','',$htmlcode);
	$htmlcode = preg_replace('/\.\d+?px/i','px',$htmlcode);
	$htmlcode = preg_replace('/\.\d+?em/i','em',$htmlcode);
	$htmlcode = str_ireplace('&amp;','&',$htmlcode);
	$htmlcode = str_ireplace('[img=100%,auto]','[img]',$htmlcode);
	$htmlcode = preg_replace('/\[img=(\d+)px,(\d+)px\]/i','[img=$1,$2]',$htmlcode);
	$htmlcode = preg_replace('/\[img=(\d+),(\d+)px\]/i','[img=$1,$2]',$htmlcode);
	$htmlcode = preg_replace('/\[img=(\d+)px,(\d+)\]/i','[img=$1,$2]',$htmlcode);
	$htmlcode = preg_replace('/0\.\d{5,}/i','1',$htmlcode);
	return $htmlcode;
}
function show_keywords($page=1)
{
	$page=max(1,$page);
	$start_num = ($page-1) * 5;
	$keywordArr=DB::fetch_all("SELECT word_str FROM " . DB::table('csdn123zd_words') . " ORDER BY orderby_num ASC,ID DESC LIMIT " . $start_num . ",5");
	return $keywordArr;
	
}
function ext_getuserbyuid($uid) {
	$uid = intval($uid);
	$userInfo = getuserbyuid($uid, 1);
	if (empty($userInfo['uid']) || is_numeric($userInfo['uid']) == false) {
		$userInfo = DB::fetch_first("SELECT uid,username FROM " . DB::table('csdn123zd_reguser') . " WHERE uid=" . $uid);
	}
	if (empty($userInfo['uid']) || is_numeric($userInfo['uid']) == false) {
		$userInfo = DB::fetch_first("SELECT uid,username FROM " . DB::table('csdn123zd_reguser') . " ORDER BY RAND() LIMIT 1");
	}
	return $userInfo;
}
function save_img($imgurl) {
	global $_G;
	$imgurl = preg_replace('/^\/\//','http://',$imgurl);
	$Original_imgurl = $imgurl;
	$display_picture = "{$_G['siteurl']}source/plugin/csdn123_news/display_picture.php";
	if(stripos($imgurl,'csdn123.net')===false && stripos($imgurl,'display_picture')===false)
	{
		$imgurl = $display_picture . '?url=' . urlencode($imgurl);
	}
	$filename = date('His') . strtolower(random(16));
	$dir = !getglobal('setting/attachdir') ? (DISCUZ_ROOT . './data/attachment/') : getglobal('setting/attachdir');
	$dir = $dir . 'forum';
	list($y, $m, $d) = explode('-', date('Y-m-d', time()));
	!is_dir("$dir/$y$m") && mkdir("$dir/$y$m", 0777);
	!is_dir("$dir/$y$m/$d") && mkdir("$dir/$y$m/$d", 0777);
	$savefilename = "$dir/$y$m/$d/";
	if (strpos(strtolower($imgurl), 'gif') !== false) {
		$filename = $filename . '.gif';
	} elseif (strpos(strtolower($imgurl), 'png') !== false) {
		$filename = $filename . '.png';
	} elseif (strpos(strtolower($imgurl), 'jpeg') !== false) {
		$filename = $filename . '.jpeg';	
	} else {
		$filename = $filename . '.jpg';
	}
	$savefilename = $savefilename . $filename;
	$imgData = ext_dfsockopen($imgurl,'',true);
	if (strlen($imgData) <2) {
		$imgData = ext_dfsockopen($Original_imgurl);
	}
	if (strlen($imgData) <2) {
		$imgData = dfsockopen($imgurl, 0, '', '', FALSE, '', 15, TRUE, 'URLENCODE', FALSE);
	}
	if (strlen($imgData) <2 && stripos($imgurl,'https')>5) {
		$imgurl = str_ireplace('url=https','url=http',$imgurl);
		$imgData = ext_dfsockopen($imgurl);
	}
	file_put_contents($savefilename, $imgData);
	require_once libfile('class/image');
	$image = new image;
	$image->Watermark($savefilename);
	$imginfo = @getimagesize($savefilename);
	if ($imginfo !== FALSE) {
		$imgwidth = $imginfo[0];
		$imagesize = @filesize($savefilename);
	}
	if ($imagesize == 0 || is_numeric($imagesize) === false) {
		$imagesize = strlen($imgData);
	}
	if ($_G['setting']['ftp']['on'] == 1) {
		$ftp = & discuz_ftp::instance();
		$ftp->connect();
		$ftpfilename = "forum/$y$m/$d/" . $filename;
		if ($ftp->connectid && $ftp->ftp_size($ftpfilename) > 0 || $ftp->upload($savefilename, $ftpfilename)) {
			@unlink($savefilename);
		}
	}
	return array("filename" => "$y$m/$d/$filename", "imagesize" => $imagesize, "imgwidth" => $imgwidth);
}
function convert_img($tid, $pid, $uid, $message, $allowhtml = 0) {
	global $_G;
	if ($allowhtml == 0) {
		preg_match_all("/(\[img\]|\[img=\d{1,4}[x|\,]\d{1,4}\])\s*([^\[\<\r\n]+?)\s*\[\/img\]/is", $message, $imglist, PREG_SET_ORDER);
	} else {
		preg_match_all('/<img[^<>]*src\s*=\s*([\'"]?)([^\'">]*)\1[^<>]*>/i', $message, $imglist, PREG_SET_ORDER);
	}
	$HasForumThreadimage=true;
	if (count($imglist) > 0) {
		foreach ($imglist as $img) {
			$imgUrl = $img[2];
			$savefilename = save_img($imgUrl);
			if($HasForumThreadimage && $savefilename['imagesize']>900)
			{
				$threadimage = array();
				$threadimage['tid'] = $tid;
				$threadimage['attachment'] = $savefilename['filename'];
				$threadimage['remote'] = 0;
				DB::insert("forum_threadimage", $threadimage);
				$HasForumThreadimage=false;
			}			
			$attachment = array();
			$attachment['tid'] = $tid;
			$attachment['pid'] = $pid;
			$attachment['uid'] = $uid;
			$attachment['tableid'] = getattachtableid($tid);
			$attachment['downloads'] = 0;
			$aid = DB::insert("forum_attachment", $attachment, true);
			$forum_attachment_n = array();
			$forum_attachment_n['aid'] = $aid;
			$forum_attachment_n['tid'] = $tid;
			$forum_attachment_n['pid'] = $pid;
			$forum_attachment_n['uid'] = $uid;
			$forum_attachment_n['dateline'] = time();
			$forum_attachment_n['filename'] = random(16) . '.jpg';
			$forum_attachment_n['filesize'] = $savefilename['imagesize'];
			$forum_attachment_n['attachment'] = $savefilename['filename'];
			if ($_G['setting']['ftp']['on'] == 1) {
				$forum_attachment_n['remote'] = 1;
			} else {
				$forum_attachment_n['remote'] = 0;
			}
			$forum_attachment_n['readperm'] = 0;
			$forum_attachment_n['price'] = 0;
			$forum_attachment_n['isimage'] = 1;
			$forum_attachment_n['width'] = $savefilename['imgwidth'];
			$forum_attachment_n['thumb'] = 0;
			$forum_attachment_n['picid'] = 0;
			C::t("forum_attachment_n")->insert("tid:" . $tid, $forum_attachment_n);
			$message = str_replace_once($img[0], '[attach]' . $aid . '[/attach]', $message);
		}
		return $message;
	} else {
		return $message;
	}
}
if(!function_exists('portalcp_article_pre_next')){
	function portalcp_article_pre_next($catid, $aid) {
		$data = array(
			'preaid' => C::t('portal_article_title')->fetch_preaid_by_catid_aid($catid, $aid),
			'nextaid' => C::t('portal_article_title')->fetch_nextaid_by_catid_aid($catid, $aid),
		);
		if($data['preaid']) {
			C::t('portal_article_title')->update($data['preaid'], array(
				'preaid' => C::t('portal_article_title')->fetch_preaid_by_catid_aid($catid, $data['preaid']),
				'nextaid' => C::t('portal_article_title')->fetch_nextaid_by_catid_aid($catid, $data['preaid']),
				)
			);
		}
		return $data;
	}
}
function send_thread($ID = '', $source_link = '',$hand_htmlcode='') {
	
	global $_G;
	@set_time_limit(200);
	if (is_null($source_link) && is_null($ID)) {
		return 'no1';
	}
	if (is_numeric($ID) && $ID > 0) {
		$hzw_news = DB::fetch_first("SELECT * FROM " . DB::table('csdn123zd_news') . " WHERE ID=" . $ID);
		$source_link = $hzw_news['source_link'];
	} else {
		$hzw_news = DB::fetch_first("SELECT * FROM " . DB::table('csdn123zd_news') . " WHERE source_link='" . $source_link . "'");
		$ID = $hzw_news['ID'];
	}
	if (count($hzw_news) > 0 && $hzw_news['tid_aid'] > 0) {
		return 'ok';
	}
	if(empty($hzw_news['release_time']) || !is_numeric($hzw_news['release_time']) || $hzw_news['release_time']>time())
	{
		$reduce_time = rand(1,1800);
		$hzw_news['release_time'] = time() - $reduce_time;
	}	
	DB::update('csdn123zd_news',array('tid_aid'=>-1,'send_datetime'=>time()),array('ID'=>$ID));	
	$remoteUrl = array();
	$remoteUrl['siteurl'] = $_G['siteurl'];
	$remoteUrl['url'] = $source_link;
	$remoteUrl['chinese_encoding'] = $hzw_news['chinese_encoding'];
	$remoteUrl['charset'] = CHARSET;
	$remoteUrl['hzw_appid'] = hzw_appid(1);
	$remoteUrl['hzw_sign'] = hzw_sign($remoteUrl);
	$fetchUrl = "http://www.csdn123.net/zd_version/zd9_5/catch_content.php";
	$htmlcode = dfsockopen($fetchUrl, 0, $remoteUrl);
	if ($htmlcode == 'htmlcode_empty')
	{
		
		if($hzw_news['model_catch']==2 && is_numeric($hzw_news['rule_id']))
		{
			$rule_id = $hzw_news['rule_id'];
			$ruleDataStr = DB::result_first('SELECT step2 FROM ' . DB::table("csdn123zd_rule") . ' WHERE ID=' . $rule_id);
			$remoteUrl['ruleDataStr'] = $ruleDataStr;
			$ruleDataStr = base64_decode($ruleDataStr);
			$ruleDataArr = dunserialize($ruleDataStr);
		}
		if($hand_htmlcode=='')
		{
			
			if($hzw_news['model_catch']==2 && is_numeric($hzw_news['rule_id']))
			{				
				$htmlcode = ext_dfsockopen($source_link,$ruleDataArr['inputcookie']);
				
			} else {
				
				$htmlcode = ext_dfsockopen($source_link);
			}	
			if (strlen($htmlcode) < 100) {
				$htmlcode = dfsockopen($source_link, 0, '', '', FALSE, '', 15, TRUE, 'URLENCODE', FALSE);
			}
			if (strlen($htmlcode) < 100) {
				return 'htmlcode_empty_2';
			}		
			$htmlcode = base64_encode($htmlcode);
		
		} else {			
				
			$remoteUrl['hand_charset'] = CHARSET;	
			$htmlcode = $hand_htmlcode;
			
		}
		$remoteUrl['htmlcode'] = $htmlcode;
		$remoteUrl['hzw_sign'] = hzw_sign($remoteUrl);	
		$htmlcode = dfsockopen($fetchUrl, 0, $remoteUrl);

	}
	if (strlen($htmlcode) < 100) {
		return 'no4';
	}
	$htmlcode = preg_replace('/^\s+|\s+$/', '', $htmlcode);
	$htmlcode = dunserialize($htmlcode);
	if (is_array($htmlcode) === false) {
		return 'no5';
	}	
	$title = diconv($htmlcode['title'], 'UTF-8');
	$post_text = $htmlcode['firstPost'];
	$post_text = diconv($post_text, 'UTF-8');
	if($hzw_news['model_catch']==2 && is_numeric($hzw_news['rule_id']))
	{
		
		if(strlen($ruleDataArr['title_replace01_start'])>0 && preg_match('/^\/.+?\/$/',$ruleDataArr['title_replace01_start'])==0)
		{	
			$title = str_replace($ruleDataArr['title_replace01_start'],$ruleDataArr['title_replace01_end'],$title);
			
		} elseif (strlen($ruleDataArr['title_replace01_start'])>0 && preg_match('/^\/.+?\/$/',$ruleDataArr['title_replace01_start'])==1) {
			
			$title = preg_replace($ruleDataArr['title_replace01_start'],$ruleDataArr['title_replace01_end'],$title);
		}
		if(strlen($ruleDataArr['title_replace02_start'])>0 && preg_match('/^\/.+?\/$/',$ruleDataArr['title_replace02_start'])==0)
		{	
			$title = str_replace($ruleDataArr['title_replace02_start'],$ruleDataArr['title_replace02_end'],$title);
			
		} elseif (strlen($ruleDataArr['title_replace02_start'])>0 && preg_match('/^\/.+?\/$/',$ruleDataArr['title_replace02_start'])==1) {
			
			$title = preg_replace($ruleDataArr['title_replace02_start'],$ruleDataArr['title_replace02_end'],$title);
		}
		if(strlen($ruleDataArr['title_replace03_start'])>0 && preg_match('/^\/.+?\/$/',$ruleDataArr['title_replace03_start'])==0)
		{	
			$title = str_replace($ruleDataArr['title_replace03_start'],$ruleDataArr['title_replace03_end'],$title);
			
		} elseif (strlen($ruleDataArr['title_replace03_start'])>0 && preg_match('/^\/.+?\/$/',$ruleDataArr['title_replace03_start'])==1) {
			
			$title = preg_replace($ruleDataArr['title_replace03_start'],$ruleDataArr['title_replace03_end'],$title);
		}
		if(strlen($ruleDataArr['title_replace04_start'])>0 && preg_match('/^\/.+?\/$/',$ruleDataArr['title_replace04_start'])==0)
		{	
			$title = str_replace($ruleDataArr['title_replace04_start'],$ruleDataArr['title_replace04_end'],$title);
			
		} elseif (strlen($ruleDataArr['title_replace04_start'])>0 && preg_match('/^\/.+?\/$/',$ruleDataArr['title_replace04_start'])==1) {
			
			$title = preg_replace($ruleDataArr['title_replace04_start'],$ruleDataArr['title_replace04_end'],$title);
		}
		if(strlen($ruleDataArr['title_replace05_start'])>0 && preg_match('/^\/.+?\/$/',$ruleDataArr['title_replace05_start'])==0)
		{	
			$title = str_replace($ruleDataArr['title_replace05_start'],$ruleDataArr['title_replace05_end'],$title);
			
		} elseif (strlen($ruleDataArr['title_replace05_start'])>0 && preg_match('/^\/.+?\/$/',$ruleDataArr['title_replace05_start'])==1) {
			
			$title = preg_replace($ruleDataArr['title_replace05_start'],$ruleDataArr['title_replace05_end'],$title);
		}	
		if(strlen($ruleDataArr['content_replace01_start'])>0 && preg_match('/^\/.+?\/$/',$ruleDataArr['content_replace01_start'])==0)
		{
			$post_text = str_replace($ruleDataArr['content_replace01_start'],$ruleDataArr['content_replace01_end'],$post_text);
			
		} elseif (strlen($ruleDataArr['content_replace01_start'])>0 && preg_match('/^\/.+?\/$/',$ruleDataArr['content_replace01_start'])==1) {
			
			$post_text = preg_replace($ruleDataArr['content_replace01_start'],$ruleDataArr['content_replace01_end'],$post_text);
		}
		if(strlen($ruleDataArr['content_replace02_start'])>0 && preg_match('/^\/.+?\/$/',$ruleDataArr['content_replace02_start'])==0)
		{
			$post_text = str_replace($ruleDataArr['content_replace02_start'],$ruleDataArr['content_replace02_end'],$post_text);
			
		} elseif (strlen($ruleDataArr['content_replace02_start'])>0 && preg_match('/^\/.+?\/$/',$ruleDataArr['content_replace02_start'])==1) {
			
			$post_text = preg_replace($ruleDataArr['content_replace02_start'],$ruleDataArr['content_replace02_end'],$post_text);
		}
		if(strlen($ruleDataArr['content_replace03_start'])>0 && preg_match('/^\/.+?\/$/',$ruleDataArr['content_replace03_start'])==0)
		{
			$post_text = str_replace($ruleDataArr['content_replace03_start'],$ruleDataArr['content_replace03_end'],$post_text);
			
		} elseif (strlen($ruleDataArr['content_replace03_start'])>0 && preg_match('/^\/.+?\/$/',$ruleDataArr['content_replace03_start'])==1) {
			
			$post_text = preg_replace($ruleDataArr['content_replace03_start'],$ruleDataArr['content_replace03_end'],$post_text);
		}
		if(strlen($ruleDataArr['content_replace04_start'])>0 && preg_match('/^\/.+?\/$/',$ruleDataArr['content_replace04_start'])==0)
		{
			$post_text = str_replace($ruleDataArr['content_replace04_start'],$ruleDataArr['content_replace04_end'],$post_text);
			
		} elseif (strlen($ruleDataArr['content_replace04_start'])>0 && preg_match('/^\/.+?\/$/',$ruleDataArr['content_replace04_start'])==1) {
			
			$post_text = preg_replace($ruleDataArr['content_replace04_start'],$ruleDataArr['content_replace04_end'],$post_text);
		}
		if(strlen($ruleDataArr['content_replace05_start'])>0 && preg_match('/^\/.+?\/$/',$ruleDataArr['content_replace05_start'])==0)
		{
			$post_text = str_replace($ruleDataArr['content_replace05_start'],$ruleDataArr['content_replace05_end'],$post_text);
			
		} elseif (strlen($ruleDataArr['content_replace05_start'])>0 && preg_match('/^\/.+?\/$/',$ruleDataArr['content_replace05_start'])==1) {
			
			$post_text = preg_replace($ruleDataArr['content_replace05_start'],$ruleDataArr['content_replace05_end'],$post_text);
		}
		
	}
	$UserInfo = ext_getuserbyuid($hzw_news['uid']);
	$post_text = str_ireplace('https://', 'http://', $post_text);
	$post_text = str_ireplace('src="//', 'src="http://', $post_text);
	$post_text = str_ireplace("src='//", "src='http://", $post_text);
	$post_text = html_entity_decode($post_text);
	$post_text = preg_replace('/(go2yd\.com\/image\.php\?url=\w+)/','$1&zhiwu55.jpg',$post_text);
	$display_picture = "{$_G['siteurl']}source/plugin/csdn123_news/display_picture.php";
    $post_text = str_replace('http://www.csdn123.net/mydata/showimg.php', $display_picture, $post_text);
    $post_text = str_replace('http://www.csdn123.net/mydata/zhihuimg.php', $display_picture, $post_text);
    $post_text = str_replace('http://www.csdn123.net/mydata/nicimg.php', $display_picture, $post_text);
    $post_text = str_replace('http://www.csdn123.net/mydata/showbaiduimg.php', $display_picture, $post_text);
	$post_text = str_replace('http://www.csdn123.net/zd_version/zd9_5/display_picture.php', $display_picture, $post_text);
	$post_text = str_replace('http://www.csdn123.net/zd_version/zd9/display_picture.php', $display_picture, $post_text);
	if($hzw_news['display_link']==1)
	{
		if(stripos($hzw_news['source_link'],'csdn123')==false)
		{
			$post_text=$post_text . '<br><br><br>' . lang('plugin/csdn123_news', 'source_link') . $hzw_news['source_link'];
			
		} else {
				
			$post_text=$post_text . '<br><br><br>' . lang('plugin/csdn123_news', 'source_link') . $hzw_news['fromurl'];
		}
	}
	if (empty($hzw_news['title']) || stripos($hzw_news['title'],'temporary title')!=false || isset($ruleDataStr)) {	
		DB::update('csdn123zd_news', array('title' => $title), 'ID=' . $hzw_news['ID']);
	}
	if(empty($title)){
		$title = $hzw_news['title'];
	}
	$title = html_entity_decode($title);
	$forum_portal = $hzw_news['forum_portal'];
	if(!defined('DISCUZ_VERSION')) {
		require_once './source/discuz_version.php';
	}
	if ($forum_portal == 'forum' || $forum_portal=='group') {
		
		if($forum_portal == 'forum')
		{
			$fid = $hzw_news['fid'];
		} else {
			$fid = $hzw_news['group_fid'];
		}
		$_G['forum']['fid'] = $fid;
		$forumInfo = C::t('forum_forum')->fetch_info_by_fid($fid);		
		$threadtypeid = $hzw_news['typeid'];
		require_once libfile('function/editor');				
		if ($forumInfo['allowhtml'] != 1) {			
			if(stripos($hzw_news['source_link'],'weixin.qq.com')!=false || stripos($hzw_news['fromurl'],'weixin.qq.com')!=false)
			{
				$post_text = weixin_bbcode($post_text);
			}
			$post_text = html2bbcode($post_text);
		}
		if(DISCUZ_VERSION=='X2.5')
		{
				$newthread = array(
					'fid' => $fid,
					'posttableid' => 0,
					'readperm' => 0,
					'price' => 0,
					'typeid' => $hzw_news['threadtypeid'],
					'sortid' => 0,
					'author' => $UserInfo['username'],
					'authorid' => $UserInfo['uid'],
					'subject' => $title,
					'dateline' => time(),
					'lastpost' => time(),
					'lastposter' =>  $PostUserInfo['username'],
					'displayorder' => 0,
					'digest' => 0,
					'special' => 0,
					'attachment' => 0,
					'moderated' => 0,
					'status' => 32,
					'isgroup' => 0,
					'replycredit' => 0,
					'closed' => 0
				);
				$tid = C::t('forum_thread')->insert($newthread, true);
				$pid = insertpost(array(
					'fid' => $fid,
					'tid' => $tid,
					'first' => '1',
					'author' => $UserInfo['username'],
					'authorid' => $UserInfo['uid'],
					'subject' => $title,
					'dateline' => time(),
					'message' => '[Just a minute,It is loading]',
					'useip' => $_G['clientip'],
					'invisible' => 0,
					'anonymous' => 0,
					'usesig' => 1,
					'htmlon' => 0,
					'bbcodeoff' => -1,
					'smileyoff' => -1,
					'parseurloff' => 0,
					'attachment' => '0',
					'tags' => '',
					'replycredit' => 0,
					'status' => 0
				));
	
		}else {
			$modthread = C::m('forum_thread');
		}		
		$params = array();
		$title = cutstr($title, 70);
		if ($hzw_news['pseudo_original'] == 1) {
			$title = rep_weiyanchang($title);
		}		
		$params['subject'] = $title;
		$params['message'] = '[Just a minute,It is loading]';
		$params['typeid'] = $hzw_news['threadtypeid'];
		$params['sortid'] = 0;
		$params['special'] = 0;
		$params['publishdate'] = $hzw_news['release_time'];
		$params['readperm'] = 0;
		$params['allownoticeauthor'] = 1;
		$params['usesig'] = 1;
		$params['replycredit'] = 0;
		if(DISCUZ_VERSION!='X2.5')
		{
			$modthread->newthread($params);
			$tid = $modthread->tid;
			$pid = $modthread->pid;
		}		
		$threadData = array();
		$threadData['author'] = $UserInfo['username'];
		$threadData['authorid'] = $UserInfo['uid'];
		$threadData['typeid'] = $hzw_news['threadtypeid'];
		$threadData['subject'] = $title;
		$icon=$_G['setting']['newbie'];
		$threadData['icon'] = -1;
		DB::update('forum_thread', $threadData, 'tid=' . $tid);
		$postData = array();
		if($hzw_news['image_localized'] == 1)
		{
			$post_text = convert_img($tid, $pid, $UserInfo['uid'], $post_text, $forumInfo['allowhtml']);

		}
		if ($hzw_news['pseudo_original'] == 1) {
			$post_text = rep_weiyanchang($post_text);
		}	
		$postData['message'] = $post_text;
		if (strpos($post_text, '[attach]') !== false) {
			$postData['attachment'] = 2;
			$threadFirstPost_attachment = 2;
			require_once libfile('function/post');
			$temp_uid = $_G['uid'];
			$temp_ismoderator = $_G['forum']['ismoderator'];
			$temp_forumpicstyle = $_G['setting']['forumpicstyle'];
			$temp_thumbwidth = $_G['setting']['forumpicstyle']['thumbwidth'];
			$temp_thumbheight = $_G['setting']['forumpicstyle']['thumbheight'];
			$_G['uid'] = $UserInfo['uid'];
			$_G['forum']['ismoderator'] = 1;
			$_G['setting']['forumpicstyle'] = array();
			$_G['setting']['forumpicstyle']['thumbwidth'] = 160;
			$_G['setting']['forumpicstyle']['thumbheight'] = 160;
			setthreadcover($pid, $tid);
			$_G['uid'] = $temp_uid;
			$_G['forum']['ismoderator'] = $temp_ismoderator;
			$_G['setting']['forumpicstyle'] = $temp_forumpicstyle;
			$_G['setting']['forumpicstyle']['thumbwidth'] = $temp_thumbwidth;
			$_G['setting']['forumpicstyle']['thumbheight'] = $temp_thumbheight;
		}
		$postData['author'] = $UserInfo['username'];
		$postData['authorid'] = $UserInfo['uid'];
		$postData['bbcodeoff'] = 0;
		$class_tag = new tag();
		$tagstr = diconv($htmlcode['tags_word'],'UTF-8');
		$tagstr = $class_tag->add_tag($tagstr, $tid, 'tid');
		$postData['tags'] = $tagstr;
		if ($forumInfo['allowhtml'] == 1) {
			$postData['htmlon'] = 1;
		}
		DB::update('forum_post', $postData, 'pid=' . $pid);
		unset($postData);
		unset($params);		
		if (is_numeric($UserInfo['uid'])) {
			DB::query('update ' . DB::table('common_member_count') . ' set threads=threads+1,posts=posts+1,extcredits2=extcredits2+2 where uid=' . $UserInfo['uid']);
			C::t('common_member_status')->update($UserInfo['uid'], array('lastpost' => time(),'lastvisit'=>time(),'lastactivity'=>time()), 'UNBUFFERED');
		}
		$lastpostArr = array();
		$lastpostArr['lastpost'] = time();
		$PostUserInfo['username']=$UserInfo['username'];
		$lastpostArr['lastposter'] = $PostUserInfo['username'];
		$lastpostArr['views'] = $hzw_news['views'];
		if ($threadFirstPost_attachment == 2) {
			$lastpostArr['attachment'] = 2;
		}
		$replies = C::t('forum_post')->count_visiblepost_by_tid($tid);
		$lastpostArr['replies'] = 0;
		$lastpostArr['maxposition'] = 1;
		if($forum_portal=='group')
		{
			$lastpostArr['isgroup'] = 1;
		}
		DB::update('forum_thread', $lastpostArr, 'tid=' . $tid);
		if ($tid > 0) {
			if (is_numeric($ID)) {
				DB::update('csdn123zd_news', array('tid_aid' => $tid,'send_datetime'=>time()), "ID=" . $ID);
			} else {
				DB::update('csdn123zd_news', array('tid_aid' => $tid,'send_datetime'=>time()), "source_link='" . $source_link . "'");
			}
			$lastUserName = $PostUserInfo['username'];
			$lastUserName = $tid . "\t" . daddslashes($title) . "\t" . time() . "\t" . daddslashes($lastUserName);
			DB::query("update " . DB::table('forum_forum') . " set threads=threads+1,posts=posts + 1,lastpost='" . $lastUserName . "',todayposts=todayposts + 1 where fid=" . $fid);
			return 'ok';
		} else {
			return 'no5';
		}
		
	} elseif ($forum_portal=='portal') {

		$title = cutstr($title, 70);
		if ($hzw_news['pseudo_original'] == 1) {
			$title = rep_weiyanchang($title);
		}		
		$setarr = array();
		$setarr['title'] = $title;
		if($hzw_news['display_link']==1)
		{
			$portalFromurl=show_sourcelink($hzw_news['fromurl'],$hzw_news['source_link']);
			$setarr['fromurl'] = daddslashes($portalFromurl);
		} else {
			$setarr['fromurl'] = '';
		}
		$setarr['dateline'] = time();
		$setarr['allowcomment'] = 1;
		$csdn123_content = $post_text;
		if ($hzw_news['pseudo_original'] == 1) {
			$csdn123_content = rep_weiyanchang($csdn123_content);
		}
		$summary = $csdn123_content;
		$summary = preg_replace('/\s+/','',$summary);
		$summary = preg_replace('/<script.+?<\/script>/i','',$summary);
		$summary = preg_replace('/<style.+?<\/style>/i','',$summary);
		$summary = cutstr(strip_tags($summary), 200);
		$summary = censor($summary);
		$setarr['summary'] = $summary;
		$setarr['catid'] = $hzw_news['portal_catid'];
		$setarr['highlight'] = '|||';
		$setarr['uid'] = $UserInfo['uid'];
		$setarr['username'] = $UserInfo['username'];
		$setarr['contents'] = 1;
		$aid = C::t('portal_article_title')->insert($setarr, 1);
		C::t('common_member_status')->update($UserInfo['uid'], array('lastpost' => time(),'lastvisit'=>time(),'lastactivity'=>time()), 'UNBUFFERED');
		C::t('portal_category')->increase($hzw_news['portal_catid'], array('articles' => 1));
		if(DISCUZ_VERSION!='X2.5')
		{
			C::t('portal_category')->update($hzw_news['portal_catid'], array('lastpublish' => time()));
		}		
		C::t('portal_article_count')->insert(array('aid' => $aid, 'catid' => $hzw_news['portal_catid'], 'viewnum' => $hzw_news['views']));
		$upload = new discuz_upload();
		$arrayimageurl = $temp = $imagereplace = array();
		preg_match_all("/\<img.+src=('|\"|)?(.*)(\\1)[^<>]*?>/ismUe", $csdn123_content, $temp, PREG_SET_ORDER);
		if (is_array($temp) && !empty($temp) && $hzw_news['image_localized'] == 1) {
			foreach ($temp as $tempvalue) {
				$tempvalue[2] = str_replace('\"', '', $tempvalue[2]);
				if (strlen($tempvalue[2])) {
					$arrayimageurl[] = $tempvalue[2];
				}
			}
			$arrayimageurl = array_unique($arrayimageurl);
			if ($arrayimageurl) {
				foreach ($arrayimageurl as $tempvalue) {
					$imageurl = $tempvalue;
					$attach['oldimageurl'] = $imageurl;
					$attach['ext'] = GetImgFileExt($imageurl);
					if (!$upload->is_image_ext($attach['ext'])) {
						continue;
					}
					$htmlcode['firstPost'] = '';
					if (preg_match('/^(http:\/\/|\.)/i', $imageurl)) {
						$htmlcode['firstPost'] = dfsockopen($imageurl);
					}
					if (empty($htmlcode['firstPost'])) continue;
					$temp = explode('/', $imageurl);
					$attach['name'] = trim($temp[count($temp) - 1]);
					$attach['thumb'] = '';
					$attach['isimage'] = $upload->is_image_ext($attach['ext']);
					$attach['extension'] = $upload->get_target_extension($attach['ext']);
					$attach['attachdir'] = $upload->get_target_dir('portal');
					$attach['attachment'] = $attach['attachdir'] . $upload->get_target_filename('portal') . '.' . $attach['extension'];
					$attach['target'] = getglobal('setting/attachdir') . './portal/' . $attach['attachment'];
					if (!@$fp = fopen($attach['target'], 'wb')) {
						continue;
					} else {
						flock($fp, 2);
						fwrite($fp, $htmlcode['firstPost']);
						fclose($fp);
					}
					if (!$upload->get_image_info($attach['target'])) {
						@unlink($attach['target']);
						continue;
					}
					$attach['size'] = filesize($attach['target']);
					if (empty($conver_pic) && stripos($attach['attachment'], 'jp') != false && filesize($attach['target']) > 12000) {
						$conver_pic = 'portal/' . $attach['attachment'];
					}
					$attachs[] = daddslashes($attach);
				}
			}
		}
		if (is_array($attachs) && count($attachs)>0 && $hzw_news['image_localized'] == 1) {
			foreach ($attachs as $attach) {
				if ($attach['isimage'] && empty($_G['setting']['portalarticleimgthumbclosed'])) {
					require_once libfile('class/image');
					$image = new image();
					$thumbimgwidth = $_G['setting']['portalarticleimgthumbwidth'] ? $_G['setting']['portalarticleimgthumbwidth'] : 300;
					$thumbimgheight = $_G['setting']['portalarticleimgthumbheight'] ? $_G['setting']['portalarticleimgthumbheight'] : 300;
					$attach['thumb'] = $image->Thumb($attach['target'], '', $thumbimgwidth, $thumbimgheight, 2);
					$image->Watermark($attach['target'], '', 'portal');
				}
				if (getglobal('setting/ftp/on') && ((!$_G['setting']['ftp']['allowedexts'] && !$_G['setting']['ftp']['disallowedexts']) || ($_G['setting']['ftp']['allowedexts'] && in_array($attach['ext'], $_G['setting']['ftp']['allowedexts'])) || ($_G['setting']['ftp']['disallowedexts'] && !in_array($attach['ext'], $_G['setting']['ftp']['disallowedexts']))) && (!$_G['setting']['ftp']['minsize'] || $attach['size'] >= $_G['setting']['ftp']['minsize'] * 1024)) {
					if (ftpcmd('upload', 'portal/' . $attach['attachment']) && (!$attach['thumb'] || ftpcmd('upload', 'portal/' . getimgthumbname($attach['attachment'])))) {
						@unlink($_G['setting']['attachdir'] . '/portal/' . $attach['attachment']);
						@unlink($_G['setting']['attachdir'] . '/portal/' . getimgthumbname($attach['attachment']));
						$attach['remote'] = 1;
					} else {
						if (getglobal('setting/ftp/mirror')) {
							@unlink($attach['target']);
							@unlink(getimgthumbname($attach['target']));
							portal_upload_error(lang('portalcp', 'upload_remote_failed'));
						}
					}
				}
				$setarr = array('uid' => $UserInfo['uid'], 'filename' => random(16) . 'jpg', 'attachment' => $attach['attachment'], 'filesize' => $attach['size'], 'isimage' => $attach['isimage'], 'thumb' => $attach['thumb'], 'remote' => $attach['remote'], 'filetype' => $attach['extension'], 'dateline' => time(), 'aid' => $aid);
				C::t('portal_attachment')->insert($setarr, true);
				$attach['url'] = ($attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl']) . 'portal/';
				$newimageurl = $attach['url'] . $attach['attachment'];
				$csdn123_content = str_replace($attach['oldimageurl'], $newimageurl, $csdn123_content);
			}
		}
		C::t('portal_article_content')->insert(array('aid' => $aid, 'id' => 0, 'title' => '', 'content' => $csdn123_content, 'pageorder' => 1, 'dateline' => time()));
		if (empty($conver_pic) == false && strlen($conver_pic) > 3) {
			C::t('portal_article_title')->update($aid, array('pic' => $conver_pic, 'thumb' => 1));
		}
		if(DISCUZ_VERSION!='X2.5')
		{
			portalcp_article_pre_next($hzw_news['portal_catid'],$aid);
		}
		if (is_numeric($ID)) {
			DB::update('csdn123zd_news', array('tid_aid' => $aid,'send_datetime'=>time()), "ID=" . $ID);
		} else {
			DB::update('csdn123zd_news', array('tid_aid' => $aid,'send_datetime'=>time()), "source_link='" . $source_link . "'");
		}
		return 'ok';
	
	}
}
function getRndUid($num = 80) {
	global $_G;
	$uidarray = DB::fetch_all('SELECT uid FROM ' . DB::table('csdn123zd_reguser') . ' ORDER BY RAND() LIMIT ' . $num);
	foreach ($uidarray as $uidvalue) {
		$uidstr = $uidvalue['uid'] . ',' . $uidstr;
	}
	$uidstr = substr($uidstr, 0, -1);
	if ($uidstr == "" || empty($uidstr)) {
		return $_G['uid'];
	} else {
		return $uidstr;
	}
}
function getOneUid($str)
{
	if(strpos($str,',')==false && is_numeric($str))
	{
		return $str;
	} else {
		$strArr=explode(',',$str);
		shuffle($strArr);
		return $strArr[1];
	}
}
function rep_weiyanchang($str) {
	$wordRs = DB::fetch_all("SELECT word1,word2 FROM " . DB::table('csdn123zd_weiyanchang'));
	foreach ($wordRs as $wordValue) {
		$word1 = $wordValue['word1'];
		$word2 = $wordValue['word2'];
		$word2 = preg_replace('/(.)/', '$1_hzw_', $word2);
		$str = str_replace($word1, $word2, $str);
	}
	$str = str_replace('_hzw_', '', $str);
	return $str;
}
function GetImgFileExt($imgurl)
{
	if(stripos($imgurl,'gif')!=false)
	{
		return 'gif';
	}elseif(stripos($imgurl,'jpg')!=false) {
		return 'jpg';
	}elseif(stripos($imgurl,'jpeg')!=false) {
		return 'jpeg';
	}elseif(stripos($imgurl,'png')!=false) {
		return 'png';
	}else{
		return 'jpg';
	}
}
function getclassname($typeid, $fid,$portal_catid,$forum_portal,$group_fid) {
	
	global $_G;
	$restr="";
	if($forum_portal=='forum')
	{
		$restr=lang('plugin/csdn123_news', 'forum');
		$fidinfo = C::t('forum_forum')->fetch_info_by_fid($fid);
		$restr=$restr . ' -- ' . $fidinfo['name'];
		if ($typeid > 0) {
			$typeidInfo = C::t('forum_threadclass')->fetch_all_by_typeid_fid($typeid, $fid);
			if(empty($typeidInfo)==false)
			{
				$restr=$restr . ' -- ' . $typeidInfo[$typeid]['name'];
			}
		}
		
	} elseif($forum_portal=='portal') {
		
		$restr=lang('plugin/csdn123_news', 'portal');
		loadcache('portalcategory');
		$category = $_G['cache']['portalcategory'];
		if(empty($category[$portal_catid])==false)
		{
			$restr=$restr . ' -- ' . $category[$portal_catid]['catname'];
		}
		
	} elseif($forum_portal=='group') {
		
		$restr=lang('plugin/csdn123_news', 'group');
		require_once libfile('function/group');
		$groupName=grouplist('displayorder',array('name'),1,array($group_fid));
		if(count($groupName)>0)
		{
			$restr=$restr . ' -- ' . $groupName[$group_fid]['name'];
		}		
		
	}
	return $restr;
}
function preview_url($forum_portal,$tid_aid)
{
	global $_G;
	if (!isset($_G['cache']['plugin'])) {
		loadcache('plugin');
	}
	$csdn123_news_setting=$_G['cache']['plugin']['csdn123_news'];
	$hzw_forum_url=$csdn123_news_setting['hzw_forum_url'];
	if(empty($hzw_forum_url) || strlen($hzw_forum_url)<3)
	{
		$hzw_forum_url='forum.php?mod=viewthread&tid={ID}';
	}
	$hzw_forum_url=str_replace('{ID}',$tid_aid,$hzw_forum_url);
	$hzw_portal_url=$csdn123_news_setting['hzw_portal_url'];
	if(empty($hzw_portal_url) || strlen($hzw_portal_url)<3)
	{
		$hzw_portal_url='portal.php?mod=view&aid={ID}';
	}
	$hzw_portal_url=str_replace('{ID}',$tid_aid,$hzw_portal_url);
	if($forum_portal=='portal')
	{
		return $_G['siteurl'] . $hzw_portal_url;		
	} else {
		return $_G['siteurl'] . $hzw_forum_url;
	}
}
function majia_chk()
{
	$chk=DB::fetch_first("SELECT * FROM " . DB::table('csdn123zd_reguser') ." LIMIT 1");
	if(count($chk)==0)
	{
		return true;
	} else {
		return false;
	}
}
function show_sourcelink($url1,$url2)
{

	if(strlen($url1)>5 && stripos($url1,'csdn123')==false)
	{
		return $url1;
	} else {
		return $url2;
	}
	
}
function interfaceUrl($indexNum,$keyword)
{
	global $_G;
	if (!isset($_G['cache']['plugin'])) {
		loadcache('plugin');
	}		
	$faceUrlArr=array();
	$faceUrlArr[1] = 'https://www.toutiao.com/search_content/?offset=%%page%%&format=json&keyword=%%keyword%%&autoload=true&count=20&cur_tab=1';
	$faceUrlArr[2] = 'Jump to kaibao search';
	$faceUrlArr[3] = 'Jump to yidianzixun search';
	$faceUrlArr[4] = 'Jump to WeChat search';
	$faceUrlArr[5] = 'http://www.myzaker.com/news/search_new.php?f=myzaker_com&keyword=%%keyword%%';
	$faceUrlArr[6] = 'http://www.csdn123.net/zd_version/zd9_5/server_batch.php?query=%%keyword%%&hzw_appid=' . hzw_appid(1);
	$faceUrlArr[7] = 'https://weixin.sogou.com/weixin?type=2&s_from=input&query=%%keyword%%&ie=utf8&_sug_=n&_sug_type_=';
	$faceUrlArr[8] = 'https://weixin.sogou.com/weixin?type=2&ie=utf8&query=%%keyword%%&tsn=1&ft=&et=&interation=&wxid=&usip=';
	$faceUrlArr[9] = 'https://weixin.sogou.com/weixin?type=2&ie=utf8&query=%%keyword%%&tsn=2&ft=&et=&interation=&wxid=&usip=';
	$faceUrlArr[10] = 'https://weixin.sogou.com/weixin?type=2&ie=utf8&query=%%keyword%%&tsn=3&ft=&et=&interation=&wxid=&usip=';
	$faceUrlArr[11] = 'https://weixin.sogou.com/weixin?query=%%keyword%%&_sug_type_=&s_from=input&_sug_=n&type=2&page=%%page%%&ie=utf8';
	$faceUrlArr[12] = 'http://i.match.qq.com/pac/kuaibao?callback=recommendlist&p=1&limit=100&channel=kb_news_tech&_=' . time();
	$faceUrlArr[13] = 'http://i.match.qq.com/pac/kuaibao?callback=recommendlist&p=1&limit=100&channel=kb_news_sports&_=' . time();
	$faceUrlArr[14] = 'http://i.match.qq.com/pac/kuaibao?callback=recommendlist&p=1&limit=100&channel=kb_news_bagua&_=' . time();
	$faceUrlArr[15] = 'http://i.match.qq.com/pac/kuaibao?callback=recommendlist&p=1&limit=100&channel=kb_news_qipa&_=' . time();
	$faceUrlArr[16] = 'http://i.match.qq.com/pac/kuaibao?callback=recommendlist&p=1&limit=100&channel=kb_photo_news&_=' . time();
	$faceUrlArr[17] = 'http://i.match.qq.com/pac/kuaibao?callback=recommendlist&p=1&limit=100&channel=kb_news_chaobao&_=' . time();
	$faceUrlArr[18] = 'http://i.match.qq.com/pac/kuaibao?callback=recommendlist&p=1&limit=100&channel=kb_news_finance&_=' . time();
	$faceUrlArr[19] = 'http://i.match.qq.com/pac/kuaibao?callback=recommendlist&p=1&limit=100&channel=kb_news_nba&_=' . time();
	$faceUrlArr[20] = 'http://www.yidianzixun.com/home/q/news_list_for_keyword?display=%%keyword%%&word_type=token&cstart=0&cend=9';
	$faceUrlArr[21] = 'http://www.yidianzixun.com/home/q/news_list_for_keyword?display=%%keyword%%&cstart=0&cend=10&word_type=token&multi=5&appid=web_yidian&_=' . time();
	$faceUrlArr[22] = 'http://www.yidianzixun.com/home/q/news_list_for_keyword?display=%%keyword%%&cstart=%%cstart%%&cend=%%cend%%&word_type=token&multi=5&appid=web_yidian&_=' . time();
	if($indexNum==2)
	{
		$indexNum=rand(12,19);
	}
	if($indexNum==3)
	{
		$indexNum=rand(20,22);
	}
	if($indexNum==4)
	{
		$indexNum=rand(7,11);
	}
	$catchUrl=$faceUrlArr[$indexNum];
	$keyword = diconv($keyword, CHARSET, 'UTF-8');
	$keyword = urlencode($keyword);
	$catchUrl = str_replace('%%keyword%%',$keyword,$catchUrl);
	$page_number = rand(-10,5);
	$page_number = max(1,$page_number);
	if($indexNum==1)
	{	
		$page_number = $page_number -1;
		$page_number = $page_number * 20;
		$catchUrl = str_replace('%%page%%',$page_number,$catchUrl);
		
	}elseif($indexNum==22){
		
		$cstart = $page_number * 10;
		$cend = $cstart + 10;
		$catchUrl = str_replace('%%cstart%%',$cstart,$catchUrl);
		$catchUrl = str_replace('%%cend%%',$cend,$catchUrl);
		
	} else {
		
		$catchUrl = str_replace('%%page%%',$page_number,$catchUrl);
		
	}
	return $catchUrl;
	
}
function catch_data_source($keyword,$datasource=0,$inputcookie='')
{

	if(empty($keyword))
	{
		return 'keyword_empty';
	}		
	$datasource=intval($datasource);
	if(is_numeric($datasource)==false || $datasource<=0 || $datasource>6)
	{
		$datasource=rand(1,6);
	}
	$catchUrl = interfaceUrl($datasource,$keyword);	
	$htmlcode = ext_dfsockopen($catchUrl,$inputcookie,true);		
	if (strlen($htmlcode) < 200) {
			
		$htmlcode = dfsockopen($catchUrl, 0, '', '', FALSE, '', 15, TRUE, 'URLENCODE', FALSE);
			
	}
	if (strlen($htmlcode) < 200) {
		
		return "htmlcode_empty";
		
	} else {
		
		return array('htmlcode'=>$htmlcode,'catchUrl'=>$catchUrl);
		
	}

}
function ext_dfsockopen($url,$cookie = '',$hzw_referer=false)
{
	if($hzw_referer==false)
	{	
		if(strlen($cookie)>10)
		{
			$dataStr = dfsockopen($url,0,'',$cookie);
		} else {
			$dataStr = dfsockopen($url);		
		}
	}	
	if(!empty($dataStr) && strlen($dataStr)>10)
	{
		return $dataStr;
		
	} else {
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_REFERER, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);		
		if(strlen($cookie)>5) {
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		}		
		$dataStr = curl_exec($ch);
		$status = curl_getinfo($ch);
		$errno = curl_errno($ch);
		curl_close($ch);
		if($errno || $status['http_code'] != 200) {
			
			return file_get_contents($url);
			
		} else {
			
			return $dataStr;
			
		}
		
	}
	
}
function hzw_appid($returnType=1)
{
	global $_G;
	if (!isset($_G['cache']['csdn123_news_appid'])) {
		loadcache('csdn123_news_appid');
	}
	if((empty($_COOKIE['csdn123_news_appid']) && empty($_G['cache']['csdn123_news_appid'])) || $returnType==3)
	{		
		$dataArr = array();
		$dataArr['plugin'] = 'csdn123_news';
		$dataArr['siteurl'] = $_G['siteurl'];
		$dataArr['savepath'] = __DIR__;
		$dataArr['system_info'] = php_uname();
		$dataArr['http_server'] = $_SERVER['SERVER_SOFTWARE'];
		$returnStr = serialize($dataArr);
		$returnStr=base64_encode($returnStr);
		setcookie('csdn123_news_appid',$returnStr,time()+99999);
		savecache('csdn123_news_appid',$htmlcode);
		
	}
	if(!empty($_COOKIE['csdn123_news_appid']) && empty($returnStr))
	{		
		$returnStr = $_COOKIE['csdn123_news_appid'];
	}
	if(!empty($_G['cache']['csdn123_news_appid']) && empty($returnStr))
	{	
		$returnStr = $_G['cache']['csdn123_news_appid'];
	}
	if($returnType==1)
	{
		return strtoupper(md5($returnStr));
		
	} else {
		
		return $returnStr;
	}

}
function hzw_sign($dataArr)
{
	if(!is_array($dataArr))
	{
		return 'no';
		
	} else {
		
		global $_G;
		if (!isset($_G['cache']['plugin'])) {
			loadcache('plugin');
		}
		$hzw_appsecret=$_G['cache']['plugin']['csdn123_news']['hzw_appsecret'];		
		ksort($dataArr, SORT_STRING);
		$string1 = '';
		foreach ($dataArr as $k => $v) {
			$string1 .= "{$k}={$v}&";
		}
		$string1 .= "key={$hzw_appsecret}";
		return strtoupper(md5($string1));
	
	}
	
}
function hzw_authorization($hzw_update=false,$pluginid)
{	
	global $_G;
	if (!isset($_G['cache']['csdn123_news_authorization'])) {
		loadcache('csdn123_news_authorization');
	}
	if((empty($_COOKIE['csdn123_news_authorization']) && empty($_G['cache']['csdn123_news_authorization'])) || $hzw_update==true)
	{
		$remoteUrl = array();
		$remoteUrl['hzw_appid'] = hzw_appid(1);
		$remoteUrl['hzw_appid_data'] = hzw_appid(3);
		$fetchUrl = "http://www.zhiwu55.com/authorization/empowerment.php";
		$htmlcode = dfsockopen($fetchUrl, 0, $remoteUrl);
		setcookie('csdn123_news_authorization',$htmlcode,time()+99999);
		savecache('csdn123_news_authorization',$htmlcode);
		$csdn123_arr = preg_replace('/^\s+|\s+$/','',$htmlcode);
		$csdn123_arr = base64_decode($csdn123_arr);
		$csdn123_arr = dunserialize($csdn123_arr);
		if(!empty($csdn123_arr['hzw_appid']) && !empty($csdn123_arr['hzw_appsecret']))
		{
			C::t('common_pluginvar')->update_by_variable($pluginid, 'hzw_appid', array('value'=>$csdn123_arr['hzw_appid']));
			C::t('common_pluginvar')->update_by_variable($pluginid, 'hzw_appsecret', array('value'=>$csdn123_arr['hzw_appsecret']));
			updatecache(array('plugin', 'setting', 'styles'));
			cleartemplatecache();
			
		}		
	
	}

}