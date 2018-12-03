<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

require_once(DISCUZ_ROOT . '/source/plugin/targetany/targetany.config.php');

global $ta_lang_utf8;
$ac = $_GET['ac'];
if (!empty($ac)) {
    $_POST_DATA = decodeForBase64($_POST);
    $postData = array();
    foreach ($_POST_DATA as $k => $v) {
        if (is_string($v)) {
            $v = ta_filter_utf8($v);
            $postData[$k] = diconv($v, 'utf-8');
        }
        else {
            $postData[$k] = $v;
        }
    }
    if (!in_array($ac, array('question_post', 'details', 'version', 'article_post', 'validate'))) {
        ta_fail(TA_ERROR_ERROR, $ac, $ta_lang_utf8['error_request_url']);
    }

    try {
        $ac = "ta_" . $ac . "_action";
        $ac($postData);
    } catch (Exception $e) {
        $msg = $e->getMessage();
        $trace = $e->getTraceAsString();
        ta_fail(TA_ERROR_ERROR, "{$msg}\n{$trace}", $msg);
    }
}

/**
 * check user Access
 * @param type $postData
 * @return boolean
 */
function ta_check_user($postData) {
    global $ta_lang_utf8;
    $validated = false;
    $message = $ta_lang_utf8['error_admin_validation'];
    $code = TA_ERROR_INVALID_USERNAME;
    if (!isset($postData['diz_username']) || $postData['diz_username'] == '') {
        $message = $ta_lang_utf8['error_admin_not_exist'];
        $code = TA_ERROR_INVALID_USERNAME;
    }

    if (!isset($postData['diz_password']) || $postData['diz_password'] == '') {
        $message = $ta_lang_utf8['error_admin_empty_pwd'];
        $code = TA_ERROR_INVALID_USER_PWD;
    }

    $questionid = isset($postData['diz_question']) && $postData['diz_question'] ? $postData['diz_question'] : '';
    $answer = isset($postData['diz_answer']) && $postData['diz_answer'] ? $postData['diz_answer'] : '';

    $result = userlogin($postData['diz_username'], $postData['diz_password'], $questionid, $answer);

    if ($result && !empty($result['ucresult']) && $result['ucresult']['uid'] > 0 && $result['status'] == 1) {
        $member = getuserbyuid($result['ucresult']['uid'], 1);
        if ($member['adminid'] > 0 && $member['freeze'] == 0 && $member['allowadmincp'] > 0) {
            $validated = true;
        }
    } else if ($result && !empty($result['ucresult']) && !empty($result['ucresult']['uid']) && $result['ucresult']['uid'] == -3) {
        $message = $ta_lang_utf8['error_admin_validate_question'];
        $code = TA_ERROR_INVALID_USER_ACCESS_ANSWER;
    }

    if (!$validated) {
        $data = array("username" => $postData["diz_username"],
            "password" => $postData["diz_password"],
            "question" => $postData["diz_question"],
            "answer" => $postData["diz_answer"]);
        ta_fail($code, $data, $message);
    }

    return $validated;
}

function ta_validation($postData) {
    global $ta_lang_utf8;
    global $_G;
    if (!($postData && isset($postData['__sign']) && $postData['__sign']) || !(isset($_G['cache']['plugin']['targetany']) && isset($_G['cache']['plugin']['targetany']['anytarget_token']) && $_G['cache']['plugin']['targetany']['anytarget_token'] == $postData['__sign'])) {
        ta_fail(TA_ERROR_INVALID_PWD, "password is wrong", $ta_lang_utf8['error_publish_pwd']);
    }
}

function ta_details_action($postData) {
    global $_G;
    ta_validation($postData);
    ta_check_user($postData);
    $returnData = array();
    if (isset($postData['forumsType']) && $postData["forumsType"] === "cate") {
        loadcache('forums');
        $forums = $_G['cache']['forums'];
        foreach ($forums as $forum) {
            if ($forum['type'] == 'forum' || $forum['type'] == 'sub') {
                $parents = getForumParentName($forums, $forum['fup']);
//                $text = $forum['name'].(empty($parents) ? '' : '('.implode('-', $parents).')');
                $parents[] = $forum['name'];
                $text = implode(' > ', $parents);
                $returnData[] = array('value' => $forum['fid'], 'text' => iconv(CHARSET, 'utf-8', $text));
            }
        }
    } else if ($postData["portalType"] === "cate") {
        loadcache('portalcategory');
        $categorys = $_G['cache']['portalcategory'];
        foreach ($categorys as $category) {
            $parents = getParentName($categorys, $category['upid']);
            $parents[] = $category['catname'];
            $text = implode(' > ', $parents);
            $returnData[] = array('value' => $category['catid'], 'text' => iconv(CHARSET, 'utf-8', $text));
        }
    }
    ta_success($returnData);
}

function getForumParentName($forums, $pid, $names = array()) {
    if ($pid && isset($forums[$pid])) {
        array_unshift($names, $forums[$pid]['name']);
        if (isset($forums[$pid]['fup']) && $forums[$pid]['fup']) {
            $names = getForumParentName($forums, $forums[$pid]['fup'], $names);
        }
    }
    return $names;
}

function getParentName($categorys, $pid, $names = array()) {
    if ($pid && isset($categorys[$pid])) {
        array_unshift($names, $categorys[$pid]['catname']);
        if (isset($categorys[$pid]['upid']) && $categorys[$pid]['upid']) {
            $names = getParentName($categorys, $categorys[$pid]['upid'], $names);
        }
    }
    return $names;
}

function ta_question_post_action($postData) {
    global $_G;
    ta_validation($postData);
    $ta_unique = $_G['cache']['plugin']['targetany']['anytarget_unique'];
    if($ta_unique){
        $tid = DB::result_first("SELECT tid FROM ".DB::table('forum_post')." where subject='{$postData['question_title']}'");
        if($tid){
            ta_success(array("url" => $_G['siteurl'] . "forum.php?mod=viewthread&tid={$tid}"));
        }
    }
    ta_check_user($postData);
    $question = new targetany_question($postData);
    $question->processData();
}

function ta_article_post_action($postData) {
    global $_G;
    ta_validation($postData);
    $ta_unique = $_G['cache']['plugin']['targetany']['anytarget_unique'];
    if($ta_unique){
        $aid = DB::result_first("SELECT aid FROM ".DB::table('portal_article_title')." where title='{$postData['article_title']}'");
        if($aid){
            ta_success(array("url" => $_G['siteurl'] . "portal.php?mod=view&aid={$aid}"));
        }
    }
    ta_check_user($postData);
    $article = new targetany_article($postData);
    $article->processData();
}

function ta_version_action($postData = array()) {
    $reply = ta_get_version();
    ta_success($reply);
}

function ta_validate_action($postData = array()) {
    global $ta_lang_utf8;
    ta_validation($postData);
    ta_success('access key is ok.', $ta_lang_utf8['info_publish_pwd']);
}
