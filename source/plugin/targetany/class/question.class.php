<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
require_once(DISCUZ_ROOT . '/source/plugin/targetany/class/basic.class.php');

class targetany_question extends targetany_basic {

    public $memberModel;
    public $postData;
    private $htmlon = 0;

    public static function getQuestionKeys() {
        global $ta_lang;
        return array(
            'question_title' => '',
            'question_author' => $ta_lang['anonymous_user'],
            'question_detail' => '',
            'question_topics' => '',
            'question_visit_count' => 0,
            'question_answer' => '',
            'question_publish_time' => time(),
            'question_author_avatar' => '',
            'question_categories' => ''
        );
    }

    public static function getAnswerKeys() {
        global $ta_lang;
        return array(
            'question_answer_content' => '',
            'question_answer_author' => $ta_lang['anonymous_user'],
            'question_answer_agree_count' => 0,
            'question_answer_publish_time' => 0,
            'question_answer_author_avatar' => '',
            'question_answer_comment' => ''
        );
    }

    public static function getCommentKeys() {
        return array(
            'question_answer_comment_author',
            'question_answer_comment_author_avatar',
            'question_answer_comment_content',
            'question_answer_comment_publish_time'
        );
    }

    public function __construct($postData) {
        parent::__construct();

        $this->postData = $postData;

        return $this;
    }

    public function getAnswersToPost($answerJson) {
        global $ta_lang_utf8;
        $answerJson = iconv(CHARSET, 'utf-8', $answerJson);
        $answers = json_decode($answerJson, true);
        $posters = array();
        if (!($answers && count($answers))) {
            return array();
        }
        $keys = $this->getAnswerKeys();
        foreach ($answers as $answer) {
            $post = array();
            foreach ($answer as $akey => $value) {
                if (is_string($value)) {
                    $value = diconv($value, 'utf-8');
                }
                if (isset($keys[$akey])) {
                    if ($akey == 'question_answer_author') {
                        if (!$value) {
                            ta_fail(TA_ERROR_MISSING_FIELD, "Missing question_answer_author", $ta_lang_utf8['error_missing_question_answer_author']);
                        }
                        $user = $this->memberModel->getMember($value);
                        $post['author'] = $user["username"];
                        $post['authorid'] = $user["uid"];
                    } else if ($akey == 'question_answer_content') {
                        $post['message'] = $this->processMessage($value);
                    } else if ($akey == 'question_answer_publish_time') {
                        $post['dateline'] = $this->_value($value, $keys[$akey]);
                    } else if ($akey == 'question_answer_author_avatar') {
                        $post['avatar'] = $this->_value($value, $keys[$akey]);
                    } else if ($akey == 'question_answer_comment') {
                        //$post['avatar'] =  $this->_value($value, $keys[$qkey]);
                    } else if ($akey == 'question_answer_agree_count') {
                        //$post['avatar'] =  $this->_value($value, $keys[$qkey]);
                    }
                }
            }
            if (isset($post['avatar']) && $post['avatar']) {
                $this->memberModel->insertMemberAvatar($post['authorid'], $post['avatar']);
            }
            $post['first'] = 0;

            unset($post['avatar']);
            if (!empty($post)) {
                $posters[] = $post;
            }
        }

        return $posters;
    }

    public function processMessage($content) {
        if ($this->htmlon) {
            return $content;
        }
        return $this->parse_html($content);
    }

    public function loadPostData() {
        global $ta_lang_utf8;
        $post = array();
        $otherPosts = array();
        $keys = $this->getQuestionKeys();
        if (isset($this->postData['htmlon']) && $this->postData['htmlon']) {
            $this->htmlon = 1;
        }
        foreach ($this->postData as $qkey => $value) {
            if (isset($keys[$qkey])) {
                if ($qkey == 'question_answer') {
                    $otherPosts = $this->getAnswersToPost($value);
                } else if ($qkey == 'question_topics' || $qkey == 'question_categories') {
                    //$questionItem = json_decode($value, true);
                } else if ($qkey == 'question_title') {
                    if (!$value) {
                        ta_fail(TA_ERROR_MISSING_FIELD, "Missing question_title", $ta_lang_utf8['error_missing_question_title']);
                    }
                    $post['subject'] = $this->_value($value, $keys[$qkey]);
                } else if ($qkey == 'question_author') {
                    if (!$value) {
                        ta_fail(TA_ERROR_MISSING_FIELD, "Missing question_author", $ta_lang_utf8['error_missing_question_author']);
                    }
                    $user = $this->memberModel->getMember($value);
                    $post['author'] = $user["username"];
                    $post['authorid'] = $user["uid"];
                } else if ($qkey == 'question_detail') {
                    $post['message'] = $this->processMessage($value);
                } else if ($qkey == 'question_visit_count') {
                    $post['views'] = $this->_value($value, $keys[$qkey]);
                } else if ($qkey == 'question_publish_time') {
                    $post['dateline'] = $this->_value($value, $keys[$qkey]);
                } else if ($qkey == 'question_author_avatar') {
                    $post['avatar'] = $this->_value($value, $keys[$qkey]);
                }
            }
        }

        if (isset($post['avatar']) && $post['avatar']) {
            $this->memberModel->insertMemberAvatar($post['authorid'], $post['avatar']);
        }
        $post['first'] = 1;

        unset($post['avatar']);
        return array_merge(array($post), $otherPosts);
    }

    private function findFid($post_forum) {
        $post_forum_id = 0;
        if (is_numeric($post_forum)) {
            $post_forum_id = 0 + $post_forum;
        }
        global $_G;
        loadcache('forums');
        $forums = $_G['cache']['forums'];
        if (empty($forums)) return 0;
        $fs = array_values($forums);
        $first_fid = $fs[0]['fid'];
        if (empty($post_forum) || !is_string($post_forum)) return $first_fid;
        $post_forums = json_decode($post_forum, true);
        if (!is_array($post_forums)) {
            $post_forums = array($post_forum);
        }
        foreach ($post_forums as $f) {
            foreach ($forums as $forum) {
                if ($post_forum_id == $forum['fid']) return $post_forum_id;
                $forum_name = iconv(CHARSET, 'utf-8', $forum['name']);
                if ($f == $forum_name) {
                    return $forum['fid'];
                }
            }
        }
        return $first_fid;
    }

    public function insertThread($posts, $fid) {
        $price = 0;
        $typeid = 0;
        $sortid = 0;
        $isgroup = 0;
        $replycredit = 0;
        $displayorder = 0;
        $digest = 0;
        $special = 0;
        if ($posts && count($posts) && isset($posts[0]['author'])) {
            $author = $posts[0]['author'];
            $uid = intval($posts[0]['authorid']);
            $subject = $posts[0]['subject'];
            $dateline = intval($posts[0]['dateline']);
            $count = count($posts);
            if (!empty($posts[$count - 1]) && isset($posts[$count - 1]['author'])) {
                $lastpost = intval($posts[$count - 1]['dateline']);
                $lastposter = $posts[$count - 1]['author'];
            }
            $replies_num = ($count - 1);
            if (isset($posts[0]['views']) && $posts[0]['views']) {
                $view_num = $posts[0]['views'];
            } else {
                $view_num = rand($replies_num, ($replies_num) * 2);
            }
        }

        try {
            DB::query("INSERT INTO " . DB::table('forum_thread') . " (fid, posttableid, readperm, price, typeid, sortid, author, authorid, subject, dateline, lastpost, lastposter, views, displayorder, digest, special, attachment, moderated, status, isgroup, replycredit, closed, replies, maxposition) VALUES ('" . $fid . "', '0', '0', '$price', '$typeid', '$sortid', '" . daddslashes($author) . "', '$uid', '" . daddslashes($subject) . "', '$dateline', '$lastpost', '" . daddslashes($lastposter) . "', '$view_num', '$displayorder', '$digest', '$special', '0', '0', '32', '$isgroup', '$replycredit', '0', '$replies_num', '$count')");
            $tid = DB::insert_id();
            if ($tid) {
                DB::query("INSERT INTO " . DB::table('forum_threadpartake') . " (tid, uid, dateline) VALUES ('" . $tid . "', '" . $uid . "', '" . $dateline . "')");
                DB::query("INSERT INTO " . DB::table('forum_newthread') . " (tid, fid, dateline) VALUES ('" . $tid . "', '" . $fid . "', '" . $dateline . "')");
            }
        } catch (Exception $e) {
            if($tid){
                DB::query("delete from ".DB::table('forum_thread')." where tid = {$tid}");
                DB::query("delete from ".DB::table('forum_newthread')." where tid = {$tid}");
                DB::query("delete from ".DB::table('forum_threadpartake')." where tid = {$tid}");
            }
            throw $e;
        }
        if($tid){
            DB::query("UPDATE " . DB::table('common_member_count') . " SET threads=threads+1 WHERE uid='$uid'");
            DB::query("UPDATE " . DB::table('forum_forum') . " SET threads=threads+1 WHERE fid='" . $fid . "'");
        }
        return $tid;
    }

    public function insertPosts($tid, $posts, $fid) {
        $htmlon = $this->htmlon;
        foreach ($posts as $post) {
            //获取时间戳微秒，防止pid 重复
            $microtime = intval(microtime(true) * 1000);
            $pid = substr($microtime,4);
            try {
                DB::query("INSERT INTO " . DB::table('forum_post') . " (`pid`, `fid`, `tid`, `first`, `author`, `authorid`, `subject`, `dateline`, `message`, `useip`, `port`, `invisible`, `anonymous`, `usesig`, `htmlon`, `bbcodeoff`, `smileyoff`, `parseurloff`, `attachment`, `rate`, `ratetimes`, `status`, `tags`, `comment`, `replycredit`, `position`) VALUES ('" . $pid . "', '" . $fid . "', '" . $tid . "', '" . ($post['first']) . "', '" . daddslashes($post['author']) . "', '" . intval($post['authorid']) . "', '" . (isset($post['subject']) ? daddslashes($post['subject']) : '') . "', '" . intval($post['dateline']) . "', '" . daddslashes($post['message']) . "', '::1', '0', '0', '0', '1', '" . $htmlon . "', '" . $htmlon . "', '-1', '0', '0', '0', '0', '0', '0', '0', '0', NULL)");
                DB::query("INSERT INTO " . DB::table('forum_post_tableid') . " (`pid`) VALUES ('%d')", array($pid));
            }catch (Exception $e){
                $msg = $e->getMessage();
                if(stripos($msg,"Duplicate entry") === 0){
                    $pid_max = DB::result_first("SELECT max(pid) FROM ".DB::table('forum_post')." LIMIT 1");
                    $pid = $pid_max + 1;
                    DB::query("INSERT INTO " . DB::table('forum_post') . " (`pid`, `fid`, `tid`, `first`, `author`, `authorid`, `subject`, `dateline`, `message`, `useip`, `port`, `invisible`, `anonymous`, `usesig`, `htmlon`, `bbcodeoff`, `smileyoff`, `parseurloff`, `attachment`, `rate`, `ratetimes`, `status`, `tags`, `comment`, `replycredit`, `position`) VALUES ('" . $pid . "', '" . $fid . "', '" . $tid . "', '" . ($post['first']) . "', '" . daddslashes($post['author']) . "', '" . intval($post['authorid']) . "', '" . (isset($post['subject']) ? daddslashes($post['subject']) : '') . "', '" . intval($post['dateline']) . "', '" . daddslashes($post['message']) . "', '::1', '0', '0', '0', '1', '" . $htmlon . "', '" . $htmlon . "', '-1', '0', '0', '0', '0', '0', '0', '0', '0', NULL)");
                    DB::query("INSERT INTO " . DB::table('forum_post_tableid') . " (`pid`) VALUES ('%d')", array($pid));
                }else {

                    DB::query("delete from " . DB::table('forum_thread') . " where tid = {$tid}");
                    DB::query("delete from " . DB::table('forum_newthread') . " where tid = {$tid}");
                    DB::query("delete from " . DB::table('forum_threadpartake') . " where tid = {$tid}");

                    DB::query("delete from " . DB::table('forum_post') . " where tid = {$tid}");
                    DB::query("delete from " . DB::table('forum_post_tableid') . " where pid = " . $pid);
                    throw $e;
                }
            }
            DB::query("UPDATE " . DB::table('common_member_count') . " SET posts=posts+1 WHERE uid=%d", array(intval($post['authorid'])));
            DB::query("UPDATE " . DB::table('forum_forum') . " SET posts=posts+1 WHERE fid=%d", array(intval($fid)));
        }
    }

    public function insertData($posts, $fid) {
        global $ta_lang_utf8;
        global $_G;
        if (empty($posts)) {
            ta_fail(TA_ERROR_ERROR, "Nothing to insert", $ta_lang_utf8['error_no_post']);
        }
        $tid = $this->insertThread($posts, $fid);
        $this->insertPosts($tid, $posts, $fid);
        ta_success(array("url" => $_G['siteurl'] . "forum.php?mod=viewthread&tid=" . $tid));
    }

    public function processData() {
        $this->memberModel = new targetany_member();
        $fid = $this->findFid($this->postData['default_forum']);
        $posts = $this->loadPostData();
        $this->insertData($posts, $fid);
    }

}
