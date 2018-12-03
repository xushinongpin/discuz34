<?php

define('DISABLEXSSCHECK', true);
loadcache('plugin', 'forums');
//if (!defined('DEBUG_MODE'))
//    define('DEBUG_MODE', $_G['cache']['plugin']['targetany']['show_error']);
//if (DEBUG_MODE) {
//    ini_set("display_errors", "On");
//    error_reporting(E_ALL | E_STRICT);
//}
ini_set("display_errors", "On");
error_reporting(E_ERROR | E_PARSE);

if (!defined('DISCUZ_VERSION')) {
    require_once(DISCUZ_ROOT . '/source/discuz_version.php');
}

require_once(DISCUZ_ROOT . '/source/plugin/targetany/version.php');
require_once(DISCUZ_ROOT . '/source/plugin/targetany/lang/lang-'.currentlang().'.php');
require_once(DISCUZ_ROOT . '/source/plugin/targetany/lib/function.global.php');
require_once(DISCUZ_ROOT . '/source/plugin/targetany/class/basic.class.php');
require_once(DISCUZ_ROOT . '/source/plugin/targetany/class/question.class.php');
require_once(DISCUZ_ROOT . '/source/plugin/targetany/class/article.class.php');
require_once(DISCUZ_ROOT . '/source/function/function_portalcp.php');
require_once(DISCUZ_ROOT . '/source/plugin/targetany/class/member.class.php');
require_once(DISCUZ_ROOT . '/source/class/discuz/discuz_upload.php');
require_once(DISCUZ_ROOT . '/source/function/function_core.php');
require_once(DISCUZ_ROOT . '/source/function/function_member.php');
