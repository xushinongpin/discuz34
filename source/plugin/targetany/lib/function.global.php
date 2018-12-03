<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

define('TA_ERROR_NONE', 1);
define('TA_ERROR_ERROR', 2);
define('TA_ERROR_PLUGIN_ERROR', 3);
define('TA_ERROR_INVALID_PWD', 100);
define('TA_ERROR_INVALID_USERNAME', 1001);
define('TA_ERROR_INVALID_USER_PWD', 1002);
define('TA_ERROR_INVALID_USER_ACCESS_ANSWER', 1003);
define('TA_ERROR_MISSING_FIELD', 101);

function ta_get_version() {
    $version = array(
        'php' => PHP_VERSION,
        'plugin' => TA_VERSION,
        'discuz' => DISCUZ_VERSION,
        'discuz_min' => DISCUZ_MIN,
    );
    return $version;
}

function ta_success($data = "", $message = "") {
    ta_result(1, $data, $message);
}

function ta_fail($code = 2, $data = "", $message = "") {
    ta_result($code, $data, $message);
}

function ta_result($result = 1, $data = "", $message = "") {
    die(json_encode(array("result" => $result, "data" => $data, "message" => $message)));
}

// Get Real Url for 302 URL
function ta_redirect_url($url) {
    if (empty($url)) {
        return false;
    }
    if(stripos($url, "static.shenjianshou.cn") === false || stripos($url, "static.shenjian.io") === false){
        //if not hosted by shenjian.io
        return array('realurl' => $url, 'referer' => "");
    }
    $result = ta_curl_headers($url.'-dl');
    if ($result !== false && strpos($result, "302 Moved Temporarily")) {
        $headers = preg_split("/\r\n+/", $result);
        if (is_array($headers)) {
            $real_url = null;
            $referer = '';
            foreach ($headers as $header) {
                $header = trim($header);
                $locpos = stripos($header, "location");
                $refererpos = stripos($header, "X-Referer");
                if ($locpos === 0) {
                    $pp = strpos($header, ":");
                    $real_url = trim(substr($header, $pp + 1));
                }
                if ($refererpos === 0) {
                    $pp = strpos($header, ":");
                    $referer = trim(substr($header, $pp + 1));
                }
            }
            if (!empty($real_url) && stripos($real_url, "http") === 0) {
                return array('realurl' => $real_url, 'referer' => $referer);
            }
        }
    }
    return false;
}

function decodeForBase64(&$post) {
    if (count($post)) {
        foreach ($post as $key => $value) {
            $post[$key] = base64_decode(str_replace(" ", "+", $value));
        }
    }

    return $post;
}

function ta_curl_headers($url) {
    // Curl
    $ch = curl_init();
    // header
    curl_setopt($ch, CURLOPT_HEADER, true);
    // 
    curl_setopt($ch, CURLOPT_NOBODY, true);
    // 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    // 
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    // 
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    // URL
    curl_setopt($ch, CURLOPT_URL, $url);
    // SSL
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // 
    return curl_exec($ch);
}

function ta_log($data) {
    if ($data && (is_array($data) || is_object($data))) {
        if (method_exists($data, 'jsonSerialize')) {
            $data = $data->jsonSerialize();
        }
        $str = json_encode($data);
    } else {
        $str = $data;
    }
    $myfile = fopen("ta_log.txt", "a") or die("Unable to open file!");
    fwrite($myfile, $str);
    fclose($myfile);
}

/*
 * utf8 编码表：
 * Unicode符号范围           | UTF-8编码方式
 * u0000 0000 - u0000 007F   | 0xxxxxxx
 * u0000 0080 - u0000 07FF   | 110xxxxx 10xxxxxx
 * u0000 0800 - u0000 FFFF   | 1110xxxx 10xxxxxx 10xxxxxx
 *
 */
function ta_filter_utf8($str) {
    $re = '';
    $str = str_split(bin2hex($str), 2);
    $mo = 1 << 7;
    $mo2 = $mo | (1 << 6);
    $mo3 = $mo2 | (1 << 5);         //三个字节
    $mo4 = $mo3 | (1 << 4);          //四个字节
    $mo5 = $mo4 | (1 << 3);          //五个字节
    $mo6 = $mo5 | (1 << 2);          //六个字节
    for ($i = 0; $i < count($str); $i++) {
        if ((hexdec($str[$i]) & ($mo)) == 0) {
            $re .= chr(hexdec($str[$i]));
            continue;
        }
        //4字节 及其以上舍去
        if ((hexdec($str[$i]) & ($mo6) ) == $mo6) {
            $i = $i + 5;
            continue;
        }
        if ((hexdec($str[$i]) & ($mo5) ) == $mo5) {
            $i = $i + 4;
            continue;
        }
        if ((hexdec($str[$i]) & ($mo4) ) == $mo4) {
            $i = $i + 3;
            continue;
        }
        if ((hexdec($str[$i]) & ($mo3) ) == $mo3) {
            $i = $i + 2;
            if (((hexdec($str[$i]) & ($mo) ) == $mo) && ((hexdec($str[$i - 1]) & ($mo) ) == $mo)) {
                $r = chr(hexdec($str[$i - 2])) .
                    chr(hexdec($str[$i - 1])) .
                    chr(hexdec($str[$i]));
                $re .= $r;
            }
            continue;
        }
        if ((hexdec($str[$i]) & ($mo2) ) == $mo2) {
            $i = $i + 1;
            if ((hexdec($str[$i]) & ($mo) ) == $mo) {
                $re .= chr(hexdec($str[$i - 1])) . chr(hexdec($str[$i]));
            }
            continue;
        }
    }
    return $re;
}
