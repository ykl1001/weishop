<?php

return [
    'FROM_ITEMS' => [
        'dbinfo' => [
            'DB_HOST' => array('type' => 'text','required' => 1, 'reg' => '/^.+$/', 'value' => '127.0.0.1','name'=>'数据库主机名/IP','error'=>0,'notice'=>'数据库主机名, 一般为 localhost','msg'=>'数据库主机名为空，或者格式错误，请检查'),
            'DB_NAME' => array('type' => 'text','required' => 1, 'reg' => '/^.+$/', 'value' => '','name'=>'数据库名','error'=>0,'notice'=>'','msg'=>'数据库名为空，或者格式错误，请检查'),
            'DB_USER' => array('type' => 'text','required' => 1, 'reg' => '/^.+$/', 'value' => 'root','name'=>'数据库用户名','error'=>0,'notice'=>'','msg'=>'数据库用户名为空，或者格式错误，请检查'),
            'DB_PWD' => array('type' => 'password','required' => 0, 'reg' => '/^.*$/', 'value' => '','name'=>'数据库密码','error'=>0,'notice'=>'本地数据库登录密码','msg'=>''),
            'DB_PORT' => array('type' => 'text','required' => 1, 'reg' => '/^\d+$/', 'value' => '3306','name'=>'端口号','error'=>0,'notice'=>'','msg'=>'端口号为空，或者格式错误，请检查'),
            'DB_PREFIX' => array('type' => 'text','required' => 1, 'reg' => '/^[a-z0-9_]+$/', 'value' => 'yz_','name'=>'表前缀','error'=>0,'notice'=>'同一数据库运行多个程序时请确保前缀为唯一，安装时将会覆盖相同名称的数据表','msg'=>'表前缀为空，或者格式错误（字母均为小写)，请检查'),
        ],
        'admin' => [
            'ADM_NAME' => ['type' => 'text','required' => 1, 'reg' => '/^.+$/', 'value' => 'admin','name'=>'管理员账号','error'=>0,'notice'=>'','msg'=>'管理员账号为空，或者格式错误，请检查'],
            'ADM_PWD' => ['type' => 'password','required' => 1, 'reg' => '/^.+$/', 'value' => '123123','name'=>'管理员密码','error'=>0,'notice'=>'默认密码为123123','msg'=>'管理员密码为空，请检查'],
            'ADM_PWD2' => ['type' => 'password','required' => 1, 'reg' => '/^.+$/', 'value' => '123123','name'=>'管理员重复密码','error'=>0,'notice'=>'','msg'=>'两次密码不一致，请检查'],
        ],
    ],
    'FROM_APP' => [
        'sms' => [
            'user_name' => ['type' => 'text','required' => 1, 'reg' => '/^.+$/', 'value' => '','name'=>'短信账号','error'=>0,'notice'=>'','msg'=>'短信帐号为空，请检查'],
            'user_pwd' => ['type' => 'password','required' => 1, 'reg' => '/^.+$/', 'value' => '','name'=>'短信密码','error'=>0,'notice'=>'','msg'=>'短信密码为空，请检查'],
        ],
        'oss' => [
            'host' => ['type' => 'text','required' => 0, 'reg' => '/^.+$/', 'value' => 'hangzhou','name'=>'所在存储区域','error'=>0,'notice'=>'如所在存储区域为杭州就填写:hangzhou','msg'=>''],
            'access_id' => ['type' => 'text','required' => 0, 'reg' => '/^.+$/', 'value' => '','name'=>'Oss账号ID','error'=>0,'notice'=>'','msg'=>'Oss账号ID为空，请检查'],
            'access_key' => ['type' => 'password','required' => 0, 'reg' => '/^.+$/', 'value' => '','name'=>'Oss账号KEY','error'=>0,'notice'=>'','msg'=>'Oss账号KEY为空，请检查'],
            'bucket' => ['type' => 'text','required' => 0, 'reg' => '/^.+$/', 'value' => '','name'=>'BUCKET','error'=>0,'notice'=>'','msg'=>'访问组为空或者格式错误，请检查'],
            'url' => ['type' => 'text','required' => 0, 'reg' => '/^.+$/', 'value' => '','name'=>'Oss访问路径','error'=>0,'notice'=>'格式：http://image.jikesoft.com ;Oss访问路径不能为空','msg'=>'Oss访问路径为空，请检查'],
        ],
        'server' => [
            'upload_url' => ['type' => 'text','required' => 0, 'reg' => '/^.+$/', 'value' => 'image/upload','name'=>'本地上传地址','error'=>0,'notice'=>'默认为:image/upload 不建议修改','msg'=>'本地上传地址为空，或者格式错误，请检查'],
            'remove_url' => ['type' => 'text','required' => 0, 'reg' => '/^.+$/', 'value' => 'image/remove','name'=>'本地删除地址','error'=>0,'notice'=>'默认为:image/remove  不建议修改','msg'=>'本地删除地址为空，或者格式错误，请检查'],
            'url' => ['type' => 'text','required' => 0, 'reg' => '/^.+$/', 'value' => 'public/upload','name'=>'本地图片访问路径','error'=>0,'notice'=>'默认为:public/upload  不建议修改','msg'=>'本地图片访问路径为空，或者格式错误，请检查'],
            'max_size' => ['type' => 'text','required' => 0, 'reg' => '/^.+$/', 'value' => '5242880','name'=>'最大图片上传 5 M','error'=>0,'notice'=>'','msg'=>'最大图片上传为空，或者格式错误，请检查'],
            'save_path' => ['type' => 'text','required' => 0, 'reg' => '/^.+$/', 'value' => 'public/upload/','name'=>'图片保存路径','error'=>0,'notice'=>'','msg'=>'图片保存路径地址为空，或者格式错误，请检查'],
            'token' => ['type' => 'password','required' => 0, 'reg' => '/^.+$/', 'value' => 'yn2CisXgPjf8','name'=>'授权TOKEN','error'=>0,'notice'=>'授权TOKEN必填','msg'=>'授权TOKEN为空，或者格式错误，请检查'],
        ],
        'upload' => [
            'upload' => ['type' => 'radio','required' => 1, 'reg' => '/^.+$/', 'value' => '','name'=>'图片上传方式','error'=>0,'notice'=>'请选择上传方式','msg'=>'选择上传方式'],
        ],
    ],
    //以下是Install项目
    'DIRS_CHECK' => [
        [
            'type'=>'file',
            'path'=> '/config/app.php',
        ],[
            'type'=>'file',
            'path'=> '/storage',
        ],[
            'type'=>'file',
            'path'=> '/storage/framework',
        ],[
            'type'=>'file',
            'path'=> '/storage/framework/cache',
        ], [
            'type'=>'file',
            'path'=> '/storage/framework/sessions',
        ], [
            'type'=>'file',
            'path'=> '/storage/framework/views'
        ],[
            'type'=>'file',
            'path'=> '/public/upload'
        ],[
            'type'=>'file',
            'path'=> '/install'
        ],[
            'type'=>'file',
            'path'=> '/public/code'
        ],[
            'type'=>'file',
            'path'=> '/install/app_config.bak'
        ],[
            'type'=>'file',
            'path'=> '/install/sql/install_0.sql'
        ],[
            'type'=>'file',
            'path'=> '/.env'
        ]
    ],
    'DB_CHECK'	=> [
        //该系统需要检测的文件夹权限
        'CREATE TEMPORARY TABLES',
        'ALTER ROUTINE',
        'CREATE ROUTINE',
        'EXECUTE',
    ],
    'FUNCTiON_CHECK'	=> [
        'mysql_connect',
        'fsockopen',
        'gethostbyname',
        'file_get_contents',
        'xml_parser_create',
        'mb_strlen',
        'curl_exec',
    ],
];
