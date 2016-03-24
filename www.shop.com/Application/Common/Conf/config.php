<?php

define('DOMAIN', 'http://www.shop.com');
return array(
    //'配置项'=>'配置值'
    'TMPL_PARSE_STRING'  => array(
        '__CSS__'               => DOMAIN . '/Public/css',
        '__JS__'                => DOMAIN . '/Public/js',
        '__IMG__'               => DOMAIN . '/Public/images',
        '__JQUERY_VALIDATION__' => DOMAIN . '/Public/ext/jquery_validation/dist',
    ),
    'SHOW_PAGE_TRACE'    => true, //开启页面调试
    /* 数据库设置 */
    'DB_TYPE'            => 'mysql', // 数据库类型
    'DB_HOST'            => '127.0.0.1', // 服务器地址
    'DB_NAME'            => 'tp1029', // 数据库名
    'DB_USER'            => 'root', // 用户名
    'DB_PWD'             => '123456', // 密码
    'DB_PORT'            => '3306', // 端口
    'DB_PREFIX'          => '', // 数据库表前缀
    'DB_PARAMS'          => array(), // 数据库连接参数    
    'DB_DEBUG'           => TRUE, // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'    => false, // 启用字段缓存
    'DB_CHARSET'         => 'utf8', // 数据库编码默认采用utf8
    'EMAIL_SETTING'      => array(
        'Host'     => 'smtp.126.com', // Specify main and backup SMTP servers
        'Username' => 'kunx_edu@126.com', // SMTP username
        'Password' => 'iam4ge', // SMTP password
    ),
    //Redis Session配置
    'SESSION_AUTO_START' => true, // 是否自动开启Session
    'SESSION_TYPE'       => 'Redis', //session类型
    'SESSION_PERSISTENT' => 1, //是否长连接(对于php来说0和1都一样)
    'SESSION_CACHE_TIME' => 1, //连接超时时间(秒)
    'SESSION_EXPIRE'     => 0, //session有效期(单位:秒) 0表示永久缓存
    'SESSION_PREFIX'     => 'sess_', //session前缀
    'SESSION_REDIS_HOST' => '127.0.0.1', //分布式Redis,默认第一个为主服务器
    'SESSION_REDIS_PORT' => '6379', //端口,如果相同只填一个,用英文逗号分隔
    'SESSION_REDIS_AUTH' => '', //Redis auth认证(密钥中不能有逗号),如果相同只填一个,用英文逗号分隔



    /* 数据缓存设置 */
    'DATA_CACHE_TIME'     => 0, // 数据缓存有效期 0表示永久缓存
    'DATA_CACHE_COMPRESS' => false, // 数据缓存是否压缩缓存
    'DATA_CACHE_CHECK'    => false, // 数据缓存是否校验缓存
    'DATA_CACHE_PREFIX'   => '', // 缓存前缀
    'DATA_CACHE_TYPE'     => 'Redis', // 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite|Xcache|Apachenote|Eaccelerator
    'DATA_CACHE_PATH'     => TEMP_PATH, // 缓存路径设置 (仅对File方式缓存有效)
    'DATA_CACHE_KEY'      => '', // 缓存文件KEY (仅对File方式缓存有效)    
    'DATA_CACHE_SUBDIR'   => false, // 使用子目录缓存 (自动根据缓存标识的哈希创建子目录)
    'DATA_PATH_LEVEL'     => 1, // 子目录缓存级别
    'REDIS_HOST'          => '127.0.0.1',
    'REDIS_PORT'          => 6379,
    'DATA_CACHE_TIMEOUT'  => 3,
    
    //静态页面缓存
    'HTML_CACHE_ON'    => false, // 开启静态缓存
    'HTML_CACHE_TIME'  => 60, // 全局静态缓存有效期（秒）
    'HTML_FILE_SUFFIX' => '.shtml', // 设置静态缓存文件后缀
    'HTML_CACHE_RULES' => array(// 定义静态缓存规则
        // 定义格式1 数组方式
        'Index:index' => array('index', 3600),
        'Index:goods' => array('goods-{id}', 3600),
    ),
    'URL_MODULE'=>2,
    'TMPL_CACHE_ON'=>false,
);
