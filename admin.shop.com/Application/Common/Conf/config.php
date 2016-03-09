<?php

define('DOMAIN', 'http://admin.shop.com');
return array(
    //'配置项'=>'配置值'
    'TMPL_PARSE_STRING' => array(
        '__CSS__'        => DOMAIN . '/Public/css',
        '__JS__'         => DOMAIN . '/Public/js',
        '__IMG__'        => DOMAIN . '/Public/images',
        '__UPLOAD_URL__' => DOMAIN . '/Uploads', //上传文件的访问路径
        '__UPLOADIFY__'  => DOMAIN . '/Public/ext/uploadify', //uploadify插件路径
        '__LAYER__'      => DOMAIN . '/Public/ext/layer', //uploadify插件路径
    ),
    'SHOW_PAGE_TRACE'   => true, //开启页面调试
    /* 数据库设置 */
    'DB_TYPE'           => 'mysql', // 数据库类型
    'DB_HOST'           => '127.0.0.1', // 服务器地址
    'DB_NAME'           => 'tp1029', // 数据库名
    'DB_USER'           => 'root', // 用户名
    'DB_PWD'            => '123456', // 密码
    'DB_PORT'           => '3306', // 端口
    'DB_PREFIX'         => '', // 数据库表前缀
    'DB_PARAMS'         => array(), // 数据库连接参数    
    'DB_DEBUG'          => TRUE, // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'   => false, // 启用字段缓存
    'DB_CHARSET'        => 'utf8', // 数据库编码默认采用utf8
    'PAGE_SIZE'         => 3, //每页显示多少个
    'UPLOAD_SETTING'    => array(
//        'mimes'        => array('image/jpeg', 'image/png', 'image/gif'), //允许上传的文件MiMe类型
        'maxSize'      => 1048576, //上传的文件大小限制 (0-不做限制)
        'exts'         => array('jpg', 'jpeg', 'png', 'gif'), //允许上传的文件后缀
        'autoSub'      => true, //自动子目录保存文件
        'subName'      => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath'     => './Uploads/', //保存根路径
        'savePath'     => '', //保存路径
        'saveName'     => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'      => '', //文件保存后缀，空则使用原后缀
        'replace'      => false, //存在同名是否覆盖
        'hash'         => true, //是否生成hash编码
        'callback'     => false, //检测文件是否存在回调，如果存在返回文件信息数组
        'driver'       => 'Qiniu', // 文件上传驱动
//        'driver'       => 'Upyun', // 文件上传驱动
        'driverConfig' => array(
            'secrectKey' => 'KBYoPnqTbgX4a65rXNI9f-6_kCKwwnHMSnLOGLNk', //七牛服务器
            'accessKey'  => 'qJHe4wo24q6X6AWSXsv-syl8PkhHjo6i5WXc-to5', //七牛用户
            'domain'     => '7xrol9.com1.z0.glb.clouddn.com', //七牛密码
            'bucket'     => 'tp1029', //空间名称
            'timeout'    => 300, //超时时间
//            'host'       => 'v0.api.upyun.com', //又拍云服务器
//            'username'   => 'tp1029', //又拍云用户
//            'password'   => '123456!@#', //又拍云密码
//            'bucket'     => 'tp1029', //空间名称
//            'timeout'    => 90, //超时时间
        ), // 上传驱动配置
    ),
     'TMPL_CACHE_ON' => false,
);
