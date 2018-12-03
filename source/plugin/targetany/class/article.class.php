<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
require_once(DISCUZ_ROOT . 'source/plugin/targetany/class/basic.class.php');

/**
 * Class targetany_article
 */
class targetany_article extends targetany_basic {

    public $memberModel;
    public $postData;

    public static function getPostFormat() {
        return array(
            'title' => '',
            'author' => '',
            'from' => '',
            'fromurl' => '',
            'dateline' => '',
            'url' => '',
            'allowcomment' => '1',
            'summary' => '',
            'catid' => 0,
//            'tag' => array(), //article_make_tag($_POST['tag']),
            'status' => 0,
            'highlight' => '',
            'showinnernav' => '0',
            'pic' => '',
            'thumb' => '',
            'remote' => '0',
            'id' => '',
        );
    }

    public static function getArticleKeys() {
        global $ta_lang;
        return array(
            'article_title' => '',
            'article_content' => '',
            'article_author' => $ta_lang['anonymous_user'],
            'article_origin_from' => '',
            'article_topics' => '',
            'article_categories' => '',
            'article_origin_url' => '',
            'article_publish_time' => time(),
            'article_brief' => '',
            'article_thumbnail' => '',
            'article_avatar' => '',
            'article_comment' => ''
        );
    }

    public static function getCommentKeys() {
        global $ta_lang;
        return array(
            'article_comment_content' => '',
            'article_comment_author' => $ta_lang['anonymous_user'],
            'article_comment_publish_time' => time(),
            'article_comment_author_avatar' => 0,
            'article_comment_agree_count' => ''
        );
    }

    public function __construct($postData) {
        parent::__construct();

        $this->postData = $postData;

        return $this;
    }

    public function getCommentsToPost($commentJson) {
        global $ta_lang_utf8;
        $comments = json_decode($commentJson, true);
        $posters = array();
        if (!($comments && count($comments))) {
            return array();
        }
        $keys = $this->getCommentKeys();
        foreach ($comments as $comment) {
            $post = array();
            foreach ($comment as $akey => $value) {
                if (is_string($value)) {
                    $value = diconv($value, 'utf-8');
                }
                if (isset($keys[$akey])) {
                    if ($akey == 'article_comment_author') {
                        if (!$value) {
                            ta_fail(TA_ERROR_MISSING_FIELD, "Missing article_comment_author", $ta_lang_utf8['error_missing_article_comment_author']);
                        }
                        $user = $this->memberModel->getMember($value);
                        $post['author'] = $user["username"];
                        $post['authorid'] = $user["uid"];
                    } else if ($akey == 'article_comment_content') {
                        $post['message'] = $value; //$this->processMessage($value);
                    } else if ($akey == 'article_comment_publish_time') {
                        $post['dateline'] = $this->_value($value, $keys[$akey]);
                    } else if ($akey == 'article_comment_author_avatar') {
                        $post['avatar'] = $this->_value($value, $keys[$akey]);
                    } else if ($akey == 'article_comment_agree_count') {
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
        return $content;
    }

    public function loadPostData() {
        global $ta_lang_utf8;
        $post = self::getPostFormat();
        $otherPosts = array();
        $keys = $this->getArticleKeys();
        foreach ($this->postData as $qkey => $value) {
            if (isset($keys[$qkey])) {
                if ($qkey == 'article_comment') {
                    $otherPosts = $this->getCommentsToPost($value);
                } else if ($qkey == 'article_title') {
                    if (!$value) {
                        ta_fail(TA_ERROR_MISSING_FIELD, "Missing article_title", $ta_lang_utf8['error_missing_article_title']);
                    }
                    $post['title'] = $this->_value($value, $keys[$qkey]);
                } else if ($qkey == 'article_author') {
                    if (!$value) {
                        ta_fail(TA_ERROR_MISSING_FIELD, "Missing article_author", $ta_lang_utf8['error_missing_article_author']);
                    }
                    $user = $this->memberModel->getMember($value);
                    $post['author'] = $user["username"];
                    $post['uid'] = $user["uid"];
                    $post['username'] = $post['author'];
                } else if ($qkey == 'article_content') {
                    $post['message'] = $value;
                } else if ($qkey == 'article_publish_time') {
                    $post['dateline'] = $this->_value($value, $keys[$qkey]);
                } else if ($qkey == 'article_brief') {
                    $post['summary'] = $this->getSummary($this->_value($value, $keys[$qkey]));
                } else if ($qkey == 'article_thumbnail') {
                    $pic = $this->_value($value, $keys[$qkey]);
                    $pic = $this->downloadRemoteImage($pic);
                    if ($pic) {
                        $post['pic'] = $pic;
                    }
                } else if ($qkey == 'article_origin_from') {
                    $post['from'] = $this->_value($value, $keys[$qkey]);
                } else if ($qkey == 'article_origin_url') {
                    $post['fromurl'] = $this->_value($value, $keys[$qkey]);
                } else if ($qkey == 'article_avatar') {
                    $post['avatar'] = $this->_value($value, $keys[$qkey]);
                }
            }
        }
        $post['catid'] = $this->findCid($this->postData['default_forum']);

        if (!(isset($post['summary']) && $post['summary'])) {
            $post['summary'] = $this->getSummary($post['message']);
        }

        if (isset($post['avatar']) && $post['avatar']) {
            $this->memberModel->insertMemberAvatar($post['uid'], $post['avatar']);
        }

        unset($post['avatar']);
        return array($post, $otherPosts);
    }

    private function findCid($post_cate) {
        $post_cate_id = 0;
        if (is_numeric($post_cate)) {
            $post_cate_id = 0 + $post_cate;
        }
        global $_G;
        loadcache('portalcategory');
        $category_list = $_G['cache']['portalcategory'];
        if (empty($category_list)) return 0;
        $cs = array_values($category_list);
        $first_cid = $cs[0]['catid'];
        if (empty($post_cate) || !is_string($post_cate)) return $first_cid;
        $post_cate_list = json_decode($post_cate, true);
        if (!is_array($post_cate_list)) {
            $post_cate_list = array($post_cate);
        }
        foreach ($post_cate_list as $cate) {
            foreach ($category_list as $category) {
                if ($post_cate_id == $category['catid']) return $post_cate_id;
                $category_name = iconv(CHARSET, 'utf-8', $category['catname']);
                if ($cate == $category_name) {
                    return $category['catid'];
                }
            }
        }
        return $first_cid;
    }

    public function insertArticle($post) {
        $post['title'] = trim($post['title']);
//        $post['author'] = dhtmlspecialchars($post['author']);
        $post['url'] = str_replace('&amp;', '&', dhtmlspecialchars($post['url']));
        $post['from'] = dhtmlspecialchars($post['from']);
        $post['fromurl'] = str_replace('&amp;', '&', dhtmlspecialchars($post['fromurl']));
        $post['dateline'] = !empty($post['dateline']) ? intval($post['dateline']) : TIMESTAMP;
        if (substr($post['fromurl'], 0, 7) !== 'http://') {
            $post['fromurl'] = '';
        }

        $content = $post['message'];
        unset($post['message']);
        try {
            $post['id'] = 0;
            $post['htmlname'] = '';
            $aid = C::t('portal_article_title')->insert($post, 1);
            C::t('common_member_status')->update($post['uid'], array('lastpost' => TIMESTAMP), 'UNBUFFERED');
            C::t('portal_category')->increase($post['catid'], array('articles' => 1));
            C::t('portal_category')->update($post['catid'], array('lastpublish' => TIMESTAMP));
            C::t('portal_article_count')->insert(array('aid' => $aid, 'catid' => $post['catid'], 'viewnum' => 1));

            $regexp = '/(\<strong\>##########NextPage(\[title=(.*?)\])?##########\<\/strong\>)+/is';
            preg_match_all($regexp, $content, $arr);
            $pagetitle = !empty($arr[3]) ? $arr[3] : array();
            $pagetitle = array_map('trim', $pagetitle);
            array_unshift($pagetitle, $post['pagetitle']);
            $contents = preg_split($regexp, $content);
            $cpostcount = count($contents);
            $dbcontents = C::t('portal_article_content')->fetch_all($aid);

            $pagecount = $cdbcount = count($dbcontents);
            if ($cdbcount > $cpostcount) {
                $cdelete = array();
                foreach (array_splice($dbcontents, $cpostcount) as $value) {
                    $cdelete[$value['cid']] = $value['cid'];
                }
                if (!empty($cdelete)) {
                    C::t('portal_article_content')->delete($cdelete);
                }
                $pagecount = $cpostcount;
            }
            foreach ($dbcontents as $key => $value) {
                C::t('portal_article_content')->update($value['cid'], array('title' => $pagetitle[$key], 'content' => $contents[$key], 'pageorder' => $key + 1));
                unset($pagetitle[$key], $contents[$key]);
            }

            if ($cdbcount < $cpostcount) {
                foreach ($contents as $key => $value) {
                    C::t('portal_article_content')->insert(array('aid' => $aid, 'id' => $post['id'], 'idtype' => $post['idtype'], 'title' => $pagetitle[$key], 'content' => $contents[$key], 'pageorder' => $key + 1, 'dateline' => $post['dateline']));
                }
                $pagecount = $cpostcount;
            }

            $updatearticle = array('contents' => $pagecount);
            $updatearticle = array_merge($updatearticle, $this->portalcp_article_pre_next($post['catid'], $aid));
            C::t('portal_article_title')->update($aid, $updatearticle);
        } catch (Exception $e) {
            throw $e;
        }

        return $aid;
    }

    public function insertComments($aid, $comments) {
        global $_G;
        foreach ($comments as $comment) {
            $setarr = array(
                'uid' => $comment['authorid'],
                'username' => $comment['author'],
                'id' => $aid,
                'idtype' => 'aid',
                'postip' => $_G['clientip'],
                'port' => $_G['remoteport'],
                'dateline' => $comment['dateline'],
                'status' => 0,
                'message' => $comment['message']
            );
            $pcid = C::t('portal_comment')->insert($setarr, true);
            C::t('portal_article_count')->increase($aid, array('commentnum' => 1));
            C::t('common_member_status')->update($comment['authorid'], array('lastpost' => $comment['dateline']), 'UNBUFFERED');
            updatecreditbyaction('portalcomment', 0, array(), 'aid' . $aid);
        }
    }

    public function insertData($post, $comments) {
        global $ta_lang_utf8;
        global $_G;
        if (empty($post)) {
            ta_fail(TA_ERROR_ERROR, "Nothing to insert", $ta_lang_utf8['error_no_post']);
        }
        $aid = $this->insertArticle($post);
        $this->insertComments($aid, $comments);
        ta_success(array("url" => $_G['siteurl'] . "portal.php?mod=view&aid=" . $aid));
    }

    public function processData() {
        $this->memberModel = new targetany_member();
        list($post, $comments) = $this->loadPostData();
        $this->insertData($post, $comments);
    }

    private function getSummary($message) {
        $message = preg_replace(array("/\[attach\].*?\[\/attach\]/", "/\&[a-z]+\;/i", "/\<script.*?\<\/script\>/"), '', $message);
        $message = preg_replace("/\[.*?\]/", '', $message);

        $message = strip_tags($message);
        require_once libfile('function/home');
        $message = getstr(strip_tags($message), 200);
        return $message;
    }

    private function portalcp_article_pre_next($catid, $aid) {
        $data = array(
            'preaid' => C::t('portal_article_title')->fetch_preaid_by_catid_aid($catid, $aid),
            'nextaid' => C::t('portal_article_title')->fetch_nextaid_by_catid_aid($catid, $aid),
        );
        if ($data['preaid']) {
            C::t('portal_article_title')->update($data['preaid'], array(
                'preaid' => C::t('portal_article_title')->fetch_preaid_by_catid_aid($catid, $data['preaid']),
                'nextaid' => C::t('portal_article_title')->fetch_nextaid_by_catid_aid($catid, $data['preaid']),
                    )
            );
        }
        return $data;
    }

    private function downloadRemoteImage ($url) {
        global $_G;
        try {
            $upload = new discuz_upload();
            $dir = $upload->get_target_dir('portal', 0);

            $image_data = file_get_contents($url); // Get image data
            if (!$image_data) {
                return false;
            }
            $suffix = "jpg";
            $filename = md5($url) . "." . $suffix; // Create image file name
            $file = 'portal/' . $dir . $filename;
            @file_put_contents($_G['setting']['attachdir'].$file.'.jpg', $image_data);
            @file_put_contents($_G['setting']['attachdir'].$file.'.middle.jpg', $image_data);
            @file_put_contents($_G['setting']['attachdir'].$file.'.thumb.jpg', $image_data);
        } catch (Exception $e) {
            throw $e;
        }

        return $file.'.jpg';
    }
}
