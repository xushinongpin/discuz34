<?php

$ta_lang = array(
    'sjs_info' => '神箭手Discuz!数据发布插件，可以将神箭手上爬虫采集的数据、购买的数据、导入的数据、清洗后的数据、机器学习的数据等一键发布到该网站。只需要简单的配置即可实现做自动化批量发布，功能丰富强大',
    'sjs_website' => '神箭手云',
    'plugin_version' => '当前插件版本',
    'publish_url' => '发布网址',
    'publish_url_error' => '(当前页面是通过内网访问，无法获取当前公网IP或域名，神箭手不支持发布到内网，请切换至外部网络获取网站发布地址)',
    'publish_url_tip' => '您可以复制该地址并填入神箭手后台 Discuz！3.X 发布接口配置中的发布网址输入框内。',
    'use_help' => '说明：',
    'tip_detail' => '<p>1、数据采集爬取请在神箭手官网操作（<a href="http://docs.shenjian.io/overview/guide/collect.html" target="_blank" >如何爬取数据</a>），采集的数据可以通过该插件发布到discuz网站</p><p>2、神箭手<a href="http://www.shenjian.io/index.php?r=market/productList" target="_blank" >大数据市场</a>内有很多热门网站的爬虫（包括 <a href="http://www.shenjian.io/index.php?r=market/search&keyword=%E5%BE%AE%E4%BF%A1%E5%85%AC%E4%BC%97%E5%8F%B7" target="_blank" >微信公众号文章采集</a>、 <a href="http://www.shenjian.io/index.php?r=market/search&keyword=%E5%BE%AE%E5%8D%9A" target="_blank" >微博采集</a>、 <a href="http://www.shenjian.io/index.php?r=market/search&keyword=%E4%BB%8A%E6%97%A5%E5%A4%B4%E6%9D%A1" target="_blank" >今日头条采集</a>、 <a href="http://www.shenjian.io/index.php?r=market/search&keyword=%E7%BE%8E%E5%9B%A2" target="_blank" >美团采集</a>、 <a href="http://www.shenjian.io/index.php?r=market/search&keyword=%E6%B7%98%E5%AE%9D" target="_blank" >淘宝</a>等电商采集等），您可以免开发直接使用</p>',

    'error_request_url' => '请求地址异常',
    'error_plugin_system' => '插件系统异常',
    'error_admin_validation' => '管理员身份校验失败',
    'error_admin_not_exist' => '填写的管理员用户名不存在',
    'error_admin_empty_pwd' => '填写的管理员密码不能为空',
    'error_admin_validate_question' => '管理员验证问题不正确',
    'error_publish_pwd' => '发布密码填写错误',
    'info_publish_pwd' => '发布密码填写正确',

    'anonymous_user' => '匿名用户',
    'error_missing_article_comment_author' => '缺少回复人名称字段',
    'error_missing_article_title' => '缺少帖子标题',
    'error_missing_article_author' => '缺少发帖人名称字段',
    'error_no_post' => '没有可以发布的内容',

    'default_username' => '欢乐的小箭箭',
    'error_insert_user' => '用户插入数据库失败',

    'error_missing_question_answer_author' => '缺少回复人名称字段',
    'error_missing_question_title' => '缺少帖子标题',
    'error_missing_question_author' => '缺少发帖人名称字段',
	'error_img_ext' => 'Discuz用户头像仅支持jpg和gif格式图片，不支持%s格式'
);

$ta_lang_utf8 = array();
foreach ($ta_lang as $k => $v) {
    $ta_lang_utf8[$k] = iconv('gbk', 'utf-8', $v);
}