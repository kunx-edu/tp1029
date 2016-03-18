<?php

define('DOMAIN', 'http://www.shop.com');
return array(
    //'配置项'=>'配置值'
    'TMPL_PARSE_STRING' => array(
        '__CSS__'               => DOMAIN . '/Public/css',
        '__JS__'                => DOMAIN . '/Public/js',
        '__IMG__'               => DOMAIN . '/Public/images',
        '__JQUERY_VALIDATION__' => DOMAIN . '/Public/ext/jquery_validation/dist',
    ),
    'SHOW_PAGE_TRACE' => true, //开启页面调试
    /* 数据库设置 */
    'DB_TYPE'         => 'mysql', // 数据库类型
    'DB_HOST'         => '127.0.0.1', // 服务器地址
    'DB_NAME'         => 'tp1029', // 数据库名
    'DB_USER'         => 'root', // 用户名
    'DB_PWD'          => '123456', // 密码
    'DB_PORT'         => '3306', // 端口
    'DB_PREFIX'       => '', // 数据库表前缀
    'DB_PARAMS'       => array(), // 数据库连接参数    
    'DB_DEBUG'        => TRUE, // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE' => false, // 启用字段缓存
    'DB_CHARSET'      => 'utf8', // 数据库编码默认采用utf8
    'EMAIL_SETTING' => array(
        'Host'     => 'smtp.126.com', // Specify main and backup SMTP servers
        'Username' => 'kunx_edu@126.com', // SMTP username
        'Password' => 'iam4ge', // SMTP password
    ),
);
