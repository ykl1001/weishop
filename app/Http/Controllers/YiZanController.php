<?php namespace YiZan\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use YiZan\Utils\Time;
use YiZan\Utils\ValidatorExtend;
use Validator, Config, View, Redirect, Request;

abstract class YiZanController extends Controller {

	use DispatchesCommands, ValidatesRequests;
	/**
	 * 运营版本
	 * @var string
	 */
	protected $operationVersion;

	public function __construct() {

        $this->getFileInstall();
		$this->operationVersion = Config::get('app.operation_version');

        //运营版本常量
        define('OPERATION_VERSION', $this->operationVersion);

        //是否是商家经营类型多选
        define('SELLER_TYPE_IS_ALL', Config::get('app.seller_type_is_all'));

		View::share('tpl_version', Config::get('app.tpl_version'));

		//站点根目录路径
		define('APP_PATH', str_replace('\\', '/', base_path()).'/');

		//当前UTC时间(秒)
		define('UTC_TIME', Time::getTime());

		//当前UTC分钟(分)
		define('UTC_MINUTE', Time::getNowMinute());

		//当前UTC小时(小时)
		define('UTC_HOUR', Time::getNowHour());

		//今天 00：00：00 UTC时间
		define('UTC_DAY', Time::getNowDay());

        //自营商家
        define('ONESELF_SELLER_ID',Config::get('app.oneself_seller_id'));



        //是否开启分销(本地)
        define('IS_OPEN_FX', Config::get('app.is_open_fx'));

        if(Config::get('app.is_open_fx') === true){
            //是否调用方维分销平台（false本地分销平台）
            define('FANWEFX_SYSTEM', Config::get('app.fanwefx_system'));
        }else{
            //是否调用方维分销平台（false本地分销平台）
            define('FANWEFX_SYSTEM',false);
        }

        //方维分销平台参数
        define('FANWESAAS_APP_ID', Config::get('app.fanwefx.saas_app_id'));
		define('FANWESAAS_APP_SECRET', Config::get('app.fanwefx.saas_app_secret'));
		
		define('CLIENT_IP', getClientIp());

		//扩展验证类
		Validator::resolver(function($translator, $data, $rules, $messages) {
		    return new ValidatorExtend($translator, $data, $rules, $messages);
		});

        //互动验证码
        define("VERIFY_URL", Config::get('app.verify.url'));
        define("CAPTCHA_ID", Config::get('app.verify.captcha_id'));
        define("PRIVATE_KEY", Config::get('app.verify.private_key'));
        define("MOBILE_CAPTCHA_ID", Config::get('app.verify.mobile_captcha_id'));
        define("MOBILE_PRIVATE_KEY", Config::get('app.verify.mobile_private_key'));
    }



    public function getFileInstall() {
        if(!file_exists(base_path()."/install/install.lock")) {
            $host = explode('.', Request::server('HTTP_HOST'));
            $domain = array_shift($host);
            $host   = implode('.', $host);
            if ($domain != 'install') {
                Redirect::to(Request::getScheme() . '://install.' . $host)->send();
            }
        }
    }
}
