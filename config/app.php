<?php
return [
 /**
    * 运营版本
    * common       通用版
    * personal     个人加盟版
    * organization 机构加盟版
    * oneself		自营版
    */
   'operation_version' => 'common',

   'tpl_version' => '1.0',//模板版本

    'sys_version' => 'v2.0',//系统版本
    
    'is_open_property' => false,//是否开通物业

    'seller_type_is_all' => true,//是否是商家经营类型多选
	
	'is_local_request' => true,//是否本地请求
	
	

    'oneself_seller_id' => 1,//自营商家
	
	'is_open_fx' => false,//分销主开关     dsy
	
	'is_wx_version' => false,//是否是微信版本 zxs

    /**
     * 是否使用方维分销
     */
    'fanwefx_system' => false, //true:调用方维分销平台 false:调用本地分销平台

    'fanwefx' => [
        'appsys_id' => 'o2o-sq',
        'saas_app_id' => 'fwf2e6926f5efebe64',
        'saas_app_secret' => 'e72d9b6302aef611ba92ba47d8846bb5',
        'fx_host' => 'http://single.fxtest.fanwe.cn',
    ],

   /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */
    /**
     * 方维分销参数配置
     */


    /**
     * 互动验证码参数
     */
    'verify' => [
        'url' => 'https://api.geetest.com/register.php',
        'captcha_id'  => 'b46d1900d0a894591916ea94ea91bd2c',
        'private_key'  => '36fc3fe98530eea08dfc6ce76e3d24c4',
        'mobile_captcha_id'  => '7c25da6fe21944cfe507d2f9876775a9',
        'mobile_private_key'  => 'f5883f4ee3bd4fa8caec67941de1b903',
    ],



    'url' => 'http://wap.o2osq.fanwe.com',
    'domain' => 'o2osq.fanwe.com',

    'callback_url' => 'http://callback.o2osq.fanwe.com/',

	'lock_sdk_api' => [
		'install_lock_url_test' => 'http://121.40.204.191:8180/mdserver/service/installLock',
		'install_lock_url' => 'http://121.40.204.191:8180/mdserver/service/installLock',
		'qry_keys_url_test' => 'http://121.40.204.191:8180/mdserver/service/qryKeys',
		'qry_keys_url' => 'http://121.40.204.191:8180/mdserver/service/qryKeys',
		'get_shequ_url' => 'http://121.40.204.191:8180/mdserver/service/getCommunity',
		'add_shequ_url' => 'http://121.40.204.191:8180/mdserver/service/addCommunity',
		'app_key' => '4bb16b42b9c0354682f6eb4943abbe9a',//服务端认证key
		'agt_num' => '10043',//服务端认证编号
		// 'departid' => '10043001',//安装组织机构编号
	],

    'api_url' => [
        'buyer' => 'http://api.o2osq.fanwe.com/buyer/v1/',
        'system' => 'http://api.o2osq.fanwe.com/system/v1/',
        'seller' => 'http://api.o2osq.fanwe.com/sellerweb/v1/',
        'staff' => 'http://api.o2osq.fanwe.com/staff/v1/',
    ],

    /**
     * 短信默认配置
     */
    'sms' => [
          'url'=> 'http://sms.fanwe.com/post',
             'user_name'=>'ceshi',
             'user_pwd'=>'ceshio0',
        ],

    'security_ips' => [
        '127.0.0.1'
    ],

    'image_type' => 'Oss',
    'image_config' => [

        'oss' => [
            'host' 			=> 'oss-cn-hangzhou.aliyuncs.com',
            'access_id'		=> 'LTAIgcJam2xm9pAE',
            'access_key'	=> '5pbyBzWrEdlc71mO1myqqsVWHSfQAr',
            'bucket' 		=> 'liangdaola',
            'url' 			=> 'http://liangdaola.oss-cn-hangzhou.aliyuncs.com/',
            'callback'		=> 'http://admin.liangdaola.com/Resource/callback',
        ],
        'server' =>[
            'upload_url'	=> 'http://resource.liangdaola.com/image/upload', //上传地址
            'remove_url'	=> 'http://resource.liangdaola.com/image/remove', //删除地址
            'token'			=> 'yn2CisXgPjf8',//授权TOKEN
            'url'			=> 'http://resource.liangdaola.com/upload/',//图片访问路径
            'max_size'		=> '5242880', //最大图片上传 5 M
            'save_path'		=> 'public/upload/',//图片保存路径
        ],
    ],
    'image_url'	=> 'http://liangdaola.oss-cn-hangzhou.aliyuncs.com/',
     'qq_map' => [
            'key' => '2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O',
            'address' => '北京市',
            'point' => '39.916527,116.397128',
            ],
     'debug' => true,
    /*
        |--------------------------------------------------------------------------
        | Application Timezone
        |--------------------------------------------------------------------------
        |
        | Here you may specify the default timezone for your application, which
        | will be used by the PHP date and date-time functions. We have gone
        | ahead and set this to a sensible default for you out of the box.
        |
        */

        'timezone' => 'PRC',

        /*
        |--------------------------------------------------------------------------
        | Application Locale Configuration
        |--------------------------------------------------------------------------
        |
        | The application locale determines the default locale that will be used
        | by the translation service provider. You are free to set this value
        | to any of the locales which will be supported by the application.
        |
        */

        'locale' => 'zh-cn',

        /*
        |--------------------------------------------------------------------------
        | Application Fallback Locale
        |--------------------------------------------------------------------------
        |
        | The fallback locale determines the locale to use when the current one
        | is not available. You may change the value to correspond to any of
        | the language folders that are provided through your application.
        |
        */

        'fallback_locale' => 'zh-cn',

        /*
        |--------------------------------------------------------------------------
        | Encryption Key
        |--------------------------------------------------------------------------
        |
        | This key is used by the Illuminate encrypter service and should be set
        | to a random, 32 character string, otherwise these encrypted strings
        | will not be safe. Please do this before deploying an application!
        |
        */
        'key' => env('APP_KEY', 'jkzyyzvs'),

        'cipher' => MCRYPT_RIJNDAEL_128,

        'iv_rule' => 73685596,

        /*
        |--------------------------------------------------------------------------
        | Logging Configuration
        |--------------------------------------------------------------------------
        |
        | Here you may configure the log settings for your application. Out of
        | the box, Laravel uses the Monolog PHP logging library. This gives
        | you a variety of powerful log handlers / formatters to utilize.
        |
        | Available Settings: 'single', 'daily', 'syslog', 'errorlog'
        |
        */

        'log' => 'daily',
		/*
        |--------------------------------------------------------------------------
        | Autoloaded Service Providers
        |--------------------------------------------------------------------------
        |
        | The service providers listed here will be automatically loaded on the
        | request to your application. Feel free to add your own services to
        | this array to grant expanded functionality to your applications.
        |
        */

    'providers' => [
        'Illuminate\Foundation\Providers\ArtisanServiceProvider',
        'Illuminate\Auth\AuthServiceProvider',
        'Illuminate\Bus\BusServiceProvider',
        'Illuminate\Cache\CacheServiceProvider',
        'Illuminate\Foundation\Providers\ConsoleSupportServiceProvider',
        'Illuminate\Routing\ControllerServiceProvider',
        'Illuminate\Cookie\CookieServiceProvider',
        'Illuminate\Database\DatabaseServiceProvider',
        'Illuminate\Encryption\EncryptionServiceProvider',
        'Illuminate\Filesystem\FilesystemServiceProvider',
        'Illuminate\Foundation\Providers\FoundationServiceProvider',
        'Illuminate\Hashing\HashServiceProvider',
        'Illuminate\Mail\MailServiceProvider',
        'Illuminate\Pagination\PaginationServiceProvider',
        'Illuminate\Pipeline\PipelineServiceProvider',
        'Illuminate\Queue\QueueServiceProvider',
        'Illuminate\Redis\RedisServiceProvider',
        'Illuminate\Auth\Passwords\PasswordResetServiceProvider',
        'Illuminate\Session\SessionServiceProvider',
        'Illuminate\Translation\TranslationServiceProvider',
        'Illuminate\Validation\ValidationServiceProvider',
        'Illuminate\View\ViewServiceProvider',
        'Intervention\Image\ImageServiceProvider',
        /*
         * Application Service Providers...
         */
        'YiZan\Providers\AppServiceProvider',
        'YiZan\Providers\BusServiceProvider',
        'YiZan\Providers\ConfigServiceProvider',
        'YiZan\Providers\EventServiceProvider',
        'YiZan\Providers\RouteServiceProvider',
        'YiZan\Providers\UserServiceProvider',
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are lazy loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App'       => 'Illuminate\Support\Facades\App',
        'Artisan'   => 'Illuminate\Support\Facades\Artisan',
        'Auth'      => 'Illuminate\Support\Facades\Auth',
        'Blade'     => 'Illuminate\Support\Facades\Blade',
        'Bus'       => 'Illuminate\Support\Facades\Bus',
        'Cache'     => 'Illuminate\Support\Facades\Cache',
        'Config'    => 'Illuminate\Support\Facades\Config',
        'Cookie'    => 'Illuminate\Support\Facades\Cookie',
        'Crypt'     => 'Illuminate\Support\Facades\Crypt',
        'DB'        => 'Illuminate\Support\Facades\DB',
        'Eloquent'  => 'Illuminate\Database\Eloquent\Model',
        'Event'     => 'Illuminate\Support\Facades\Event',
        'File'      => 'Illuminate\Support\Facades\File',
        'Hash'      => 'Illuminate\Support\Facades\Hash',
        'Input'     => 'Illuminate\Support\Facades\Input',
        'Inspiring' => 'Illuminate\Foundation\Inspiring',
        'Lang'      => 'Illuminate\Support\Facades\Lang',
        'Log'       => 'Illuminate\Support\Facades\Log',
        'Mail'      => 'Illuminate\Support\Facades\Mail',
        'Password'  => 'Illuminate\Support\Facades\Password',
        'Queue'     => 'Illuminate\Support\Facades\Queue',
        'Redirect'  => 'Illuminate\Support\Facades\Redirect',
        'Redis'     => 'Illuminate\Support\Facades\Redis',
        'Request'   => 'Illuminate\Support\Facades\Request',
        'Response'  => 'Illuminate\Support\Facades\Response',
        'Route'     => 'Illuminate\Support\Facades\Route',
        'Schema'    => 'Illuminate\Support\Facades\Schema',
        'Session'   => 'Illuminate\Support\Facades\Session',
        'Storage'   => 'Illuminate\Support\Facades\Storage',
        'URL'       => 'Illuminate\Support\Facades\URL',
        'Validator' => 'Illuminate\Support\Facades\Validator',
        'View'      => 'Illuminate\Support\Facades\View',
        'Time'      => 'YiZan\Utils\Time',
		'Pinyin'    => 'YiZan\Utils\Pinyin',
        'Image' => 'Intervention\Image\Facades\Image',
    ],

    ];
