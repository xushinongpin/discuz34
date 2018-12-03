<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

/**
 * Description of member
 *
 * @author Carry
 */
class targetany_member extends targetany_basic {

    public function getUsername($username) {
        $md5author = substr(md5($username), 7, 13);
        return $md5author;
    }

    public function getMember($username) {
        global $ta_lang;
        $this->_table = "common_member";
        try {
            if($username == "author_web_master"){
                $user = DB::fetch_first('SELECT `uid`,`username` FROM %t WHERE `groupid`=%d limit 1', array($this->_table, "1"));
            }else if($username == "author_existing_users"){
                $user = DB::fetch_first('SELECT `uid`,`username` FROM %t ORDER BY rand() limit 1', array($this->_table));
            }else{
                $user = DB::fetch_first("SELECT `uid`,`username` FROM %t WHERE `username`=%s ", array($this->_table, $username));
            }
        } catch (Exception $exc) {
            throw $exc;
        }
        if (!$user) {
            try {
                $user = $this->insertMember($username);
            } catch (Exception $exc) {
                $info = json_decode($exc->getMessage(), true);
                if (!empty($info) && isset($info['code'])) {
                    ta_fail($info['code'], $info['data'], $info['message']);
                } else {
                    ta_fail(TA_ERROR_ERROR, $exc->getMessage(), $exc->getMessage());
                }
            }
        }
        return array("uid" => $user["uid"], "username" => $user["username"]);
    }

    public function insertMemberAvatar($uid, $remoteAvatar) {
        $this->get_user_avatar($uid, $remoteAvatar);
    }

    public function insertMember($username) {
        global $ta_lang_utf8;
        $email = $this->randEmail($username);

        $md5author = $this->getUsername($username);
        $uid = uc_user_register($md5author, $this->getPassword(), $email);

        if($uid <= 0){
            $uid =  DB::result(DB::query("SELECT uid FROM ".UC_DBTABLEPRE."members WHERE username='$md5author'"),0);
        }
        if ($uid <= 0) {
            throw New Exception(json_encode(array('code' => TA_ERROR_ERROR, 'data' => array('position' => 'insertmember', 'uid' => $uid, 'username' => $md5author, 'o_name' => $username, 'email' => $email, 'password' => $this->getPassword()), 'message' => $ta_lang_utf8['error_insert_user'])));
        }
        $gid = rand(10, 13);
        $credits = 0;
        try {
            $usergroupQuery = DB::query("SELECT creditshigher, creditslower FROM " . DB::table('common_usergroup') . " WHERE  groupid ='%d'", array($gid));

            while ($usergroup = DB::fetch($usergroupQuery)) {
                $credits = rand($usergroup['creditslower'], $usergroup['creditshigher']);
            }
        } catch (Exception $exc) {
            throw $exc;
        }

        $userdata = array(
            'uid' => $uid,
            'username' => addslashes($username),
            'password' => md5(md5($this->getPassword())),
            'email' => addslashes($email),
            'adminid' => 0,
            'groupid' => $gid,
            'regdate' => $this->randTime(time()-3600000, time()-360000),
            'credits' => $credits,
            'timeoffset' => 9999
        );

        $status_data = array(
            'uid' => $uid,
            'regip' => '127.0.0.1',
            'lastip' => $this->randIp(),
            'lastvisit' => 0,
            'lastactivity' => 0,
            'lastpost' => 0,
            'lastsendmail' => 0,
        );

        $profile['uid'] = $uid;
        $field_forum['uid'] = $uid;
        $field_forum['sightml'] = '';

        $field_home['uid'] = $uid;

        $count_data['uid'] = $uid;

        try {

            DB::insert('common_member', daddslashes($userdata));
            DB::insert('common_member_status', daddslashes($status_data));
            DB::insert('common_member_profile', daddslashes($profile));
            DB::insert('common_member_field_forum', daddslashes($field_forum));
            DB::insert('common_member_field_home', daddslashes($field_home));
            DB::insert('common_member_count', daddslashes($count_data));
        } catch (Exception $e) {
//            throw $e;
        }

        return array("uid" => $uid, "username" => addslashes($username));
    }


    public function get_user_avatar($user_id, $avatar) {
        global $ta_lang_utf8;
        $imgloc = ta_redirect_url($avatar);
        if ($imgloc['realurl'] !== false && isset($imgloc['realurl']) && $imgloc['realurl']) {
            $sizes = array('big', 'middle', 'small');
            $ext = pathinfo($imgloc['realurl'],PATHINFO_EXTENSION);
            if(empty($ext)){
                $ext = "jpg";
            }
            if(!in_array(strtolower($ext),array('jpg','gif'))){
                $ext = "jpg";
            }
            foreach ($sizes as $size) {
                $avatar = $this->get_uc_server_dir() . 'data/avatar/' . $this->get_avatar($user_id, $size,"",$ext);
                if(file_exists($avatar) && !unlink($avatar)){
                    throw new Exception("download avatar failed");
                }
                $avatar_dir = dirname($avatar);
                if(!is_dir($avatar_dir) && !mkdir($avatar_dir,0777,true)){
                    throw new Exception("创建头像存放目录失败，需要您手动将该目录 {$avatar_dir} 创建，然后赋予它读写权限");
                }
                file_put_contents($avatar, file_get_contents($imgloc['realurl']));

            }
        }
    }

    function get_uc_server_dir() {
        $str = str_replace('uc_client', 'uc_server', UC_ROOT);
        $str = str_replace('\\', '/', $str);
        return $str;
    }

    public function get_avatar($uid, $size = 'middle', $type = '',$ext='jpg') {
        $size = in_array($size, array('big', 'middle', 'small')) ? $size : 'middle';
        $uid = abs(intval($uid));
        $uid = sprintf("%09d", $uid);
        $dir1 = substr($uid, 0, 3);
        $dir2 = substr($uid, 3, 2);
        $dir3 = substr($uid, 5, 2);
        $typeadd = $type == 'real' ? '_real' : '';
        return $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($uid, -2) . $typeadd . "_avatar_$size.".$ext;
    }

}
