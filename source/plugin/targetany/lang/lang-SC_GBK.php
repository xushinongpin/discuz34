<?php

$ta_lang = array(
    'sjs_info' => '�����Discuz!���ݷ�����������Խ������������ɼ������ݡ���������ݡ���������ݡ���ϴ������ݡ�����ѧϰ�����ݵ�һ������������վ��ֻ��Ҫ�򵥵����ü���ʵ�����Զ����������������ܷḻǿ��',
    'sjs_website' => '�������',
    'plugin_version' => '��ǰ����汾',
    'publish_url' => '������ַ',
    'publish_url_error' => '(��ǰҳ����ͨ���������ʣ��޷���ȡ��ǰ����IP������������ֲ�֧�ַ��������������л����ⲿ�����ȡ��վ������ַ)',
    'publish_url_tip' => '�����Ը��Ƹõ�ַ����������ֺ�̨ Discuz��3.X �����ӿ������еķ�����ַ������ڡ�',
    'use_help' => '˵����',
    'tip_detail' => '<p>1�����ݲɼ���ȡ��������ֹ���������<a href="http://docs.shenjian.io/overview/guide/collect.html" target="_blank" >�����ȡ����</a>�����ɼ������ݿ���ͨ���ò��������discuz��վ</p><p>2�������<a href="http://www.shenjian.io/index.php?r=market/productList" target="_blank" >�������г�</a>���кܶ�������վ�����棨���� <a href="http://www.shenjian.io/index.php?r=market/search&keyword=%E5%BE%AE%E4%BF%A1%E5%85%AC%E4%BC%97%E5%8F%B7" target="_blank" >΢�Ź��ں����²ɼ�</a>�� <a href="http://www.shenjian.io/index.php?r=market/search&keyword=%E5%BE%AE%E5%8D%9A" target="_blank" >΢���ɼ�</a>�� <a href="http://www.shenjian.io/index.php?r=market/search&keyword=%E4%BB%8A%E6%97%A5%E5%A4%B4%E6%9D%A1" target="_blank" >����ͷ���ɼ�</a>�� <a href="http://www.shenjian.io/index.php?r=market/search&keyword=%E7%BE%8E%E5%9B%A2" target="_blank" >���Ųɼ�</a>�� <a href="http://www.shenjian.io/index.php?r=market/search&keyword=%E6%B7%98%E5%AE%9D" target="_blank" >�Ա�</a>�ȵ��̲ɼ��ȣ����������⿪��ֱ��ʹ��</p>',

    'error_request_url' => '�����ַ�쳣',
    'error_plugin_system' => '���ϵͳ�쳣',
    'error_admin_validation' => '����Ա���У��ʧ��',
    'error_admin_not_exist' => '��д�Ĺ���Ա�û���������',
    'error_admin_empty_pwd' => '��д�Ĺ���Ա���벻��Ϊ��',
    'error_admin_validate_question' => '����Ա��֤���ⲻ��ȷ',
    'error_publish_pwd' => '����������д����',
    'info_publish_pwd' => '����������д��ȷ',

    'anonymous_user' => '�����û�',
    'error_missing_article_comment_author' => 'ȱ�ٻظ��������ֶ�',
    'error_missing_article_title' => 'ȱ�����ӱ���',
    'error_missing_article_author' => 'ȱ�ٷ����������ֶ�',
    'error_no_post' => 'û�п��Է���������',

    'default_username' => '���ֵ�С����',
    'error_insert_user' => '�û��������ݿ�ʧ��',

    'error_missing_question_answer_author' => 'ȱ�ٻظ��������ֶ�',
    'error_missing_question_title' => 'ȱ�����ӱ���',
    'error_missing_question_author' => 'ȱ�ٷ����������ֶ�',
	'error_img_ext' => 'Discuz�û�ͷ���֧��jpg��gif��ʽͼƬ����֧��%s��ʽ'
);

$ta_lang_utf8 = array();
foreach ($ta_lang as $k => $v) {
    $ta_lang_utf8[$k] = iconv('gbk', 'utf-8', $v);
}