<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
require_once DISCUZ_ROOT . './config/config_ucenter.php';
require_once DISCUZ_ROOT . './uc_client/client.php';

/**
 * Description of member
 *
 * @author Carry
 */
class targetany_basic {

    public $value = array();

    public function __construct() {}

    protected function randTime($min = '', $max = '') {
        if (!$max) {
            $max = time();
        }
        return rand($min, $max);
    }

    protected function paddslashes($data) {
        if (DISCUZ_VERSION != 'X2')
            return $data;
        return daddslashes($data);
    }

    protected function randEmail($username) {
        $emailSps = array('163.com', 'qq.com', 'gmail.com', 'sina.com', 'weibo.com', 'yahoo.cn', '139.com');
        $f = substr(md5($username),8,rand(6, 12));

        return $f . '@' . $emailSps[rand(0, 6)];
    }

    protected function getPassword() {
        $pw = '123456';
        //TODO 默认密码
        return $pw;
    }

    protected function randIp() {
        $ip_long = array(
            array('607649792', '608174079'), //36.56.0.0-36.63.255.255
            array('1038614528', '1039007743'), //61.232.0.0-61.237.255.255
            array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255
            array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255
            array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255
            array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255
            array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255
            array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255
            array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255
            array('-569376768', '-564133889'), //222.16.0.0-222.95.255.255
        );
        $rand_key = mt_rand(0, 9);
        return long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
    }

    protected function _value($value, $default) {
        if (empty($value))
            return $default;
        return $value;
    }

    protected function parse_html($text) {
        $recursive_tags = array(
            //'#\s*?<div(( .*?)?)>(.*?)</div>\s*?#si' => '\\3',
            //'#<span(( .*?)?)>(.*?)</span>#si' => '\\3',
            '#<ul(( .*?)?)>(.*?)</ul>#si' => '[list]\\3[/list]',
            '#<ol(( .*?)?)>(.*?)</ol>#si' => '[list=1]\\3[/list]',
                //'#<font(.*?)>(.*?)</font>#si' => '\\2',
        );
        $tags = array(
            '#\s*?<strong>(.*?)</strong>\s*?#si' => '[b]\\1[/b]',
            '#<b(( .*?)?)>(.*?)</b>#si' => '[b]\\3[/b]',
            '#<em(( .*?)?)>(.*?)</em>#si' => '[i]\\3[/i]',
            '#<i(( .*?)?)>(.*?)</i>#si' => '[i]\\3[/i]',
            '#<u(( .*?)?)>(.*?)</u>#si' => '[u]\\3[/u]',
            '#<s(( .*?)?)>(.*?)</s>#si' => '[s]\\3[/s]',
            //'#<small(.*?)>(.*?)</small>#si' => '\\2',
            //'#<big(.*?)>(.*?)</big>#si' => '\\2',
            '#<img(.*?) src=["\'](.*?)["\'](.*?)>#si' => "[img]\\2[/img]\n",
            //'#<a (.*?)href=["\'](.*?)mailto:(.*?)["\'](.*?)>(.*?)</a>#si' => '\\3',
            '#<a (.*?)href=["\'](.*?)["\'](.*?)>(.*?)</a>#si' => '[url=\\2]\\4[/url]',
            '#<code(( .*?)?)>(.*?)</code>#si' => '[code]\\3[/code]',
            '#<iframe style=["\'](.*?)["\'] id=["\']ytplayer["\'] type=["\']text/html["\'] width="534" height="401" src="(.*?)/embed/(.*?)" frameborder="0"/></iframe>#si' => '[youtube]\\3[/youtube]',
            '#\s*?<br(.*?)>\s*?#si' => "\n",
            '#<h2(( .*?)?)>(.*?)</h2>#si' => '[h1]\\3[/h1]',
            '#<h3(( .*?)?)>(.*?)</h3>#si' => '[h2]\\3[/h2]',
            '#<h4(( .*?)?)>(.*?)</h4>#si' => '[h3]\\3[/h3]',
            '#<li(( .*?)?)>(.*?)</li>#si' => '[*]\\3',
            '#<center(( .*?)?)>(.*?)</center>#si' => '[align=center]\\3[/align]',
            '#<p(( .*?)?)>(.*?)</p>#si' => "\\3\n",
            '#<blockquote(( .*?)?)>(.*?)</blockquote>#si' => '[quote]\\3[/quote]',
            //'#<pre>(.*?)</pre>#si' => '\\1',
            '#<noscript(( .*?)?)>(.*?)</noscript>#si' => '\\3',
            '#<object(.*?)>.*?<param .*?name=["\']movie["\'][^<]*?value=["\'](.*?)["\'].*?(></param>|/>|>).*?</object>#si' => '[flash]\\2[/flash]',
            '#<object(.*?)>.*?<param .*?value=["\'](.*?)["\'][^<]*?name=["\']movie["\'].*?(></param>|/>|>).*?</object>#si' => '[flash]\\2[/flash]',
            '#<embed (.*?)src=["\']([^<]*?)["\'][^<]*?flashvars=["\']([^<]*?)["\']([^<]*?)(></embed>|/>|>)#si' => '[flash]\\2?\\3[/flash]',
            '#<embed (.*?)src=["\']([^<]*?)["\']([^<]*?)(></embed>|/>|>)#si' => '[flash]\\2[/flash]',
            '#<iframe.*src=[\'"]([^"\']*)[\'"].*></iframe>#si' => '[iframe]\\1[/iframe]',
            '#<iframe.*src=[\'"]([^"\']*)[\'"].*>#si' => '[iframe]\\1[/iframe]',
        );
        $query = DB::query("SELECT * FROM ". DB::table('forum_bbcode'));
        while($row = DB::fetch($query)) {
            if(!in_array($row['tag'],array('strong','b','em','i','u','s','img','a','code','h2','p','h3','h4','li','object','noscript','embed','iframe','blockquote'))) {
                $tags['#<' . $row['tag'] . '(( .*?)?)>(.*?)</' . $row['tag'] . '>#si'] = '[' . $row['tag'] . ']\\3[/' . $row['tag'] . ']';
            }
        }
        foreach ($recursive_tags as $search => $replace) {
            $text2 = $text;
            do {
                $text = $text2;
                $text2 = preg_replace($search, $replace, $text);
            } while ($text2 != $text);
        }
        foreach ($tags as $search => $replace) {
            if($search == '#<a (.*?)href=["\'](.*?)["\'](.*?)>(.*?)</a>#si' && version_compare(PHP_VERSION,"5.4",">=")){
                $text = preg_replace_callback($search,function($matches){
                    $url = str_replace('[','%5B',$matches[2]);
                    $url = str_replace(']','%5D',$url);
                    return '[url='.$url.']'.$matches[4].'[/url]';
                },$text);
            }else {
                $text = preg_replace($search, $replace, $text);
            }
        }

        $html = $this->decode_html($text);
        return strip_tags($html);
    }

    protected function decode_html($text) {
        $text2 = $text;
        do {
            $text = $text2;
            $text2 = html_entity_decode($text, ENT_COMPAT, "UTF-8");
        } while ($text2 != $text);
        return $text;
    }

}