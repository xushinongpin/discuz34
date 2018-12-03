<?php
chdir('../');
define('SUB_DIR', '/news/');
$_GET['mod'] = 'list';
$_GET['catid'] = '1';
require_once './portal.php';
?>