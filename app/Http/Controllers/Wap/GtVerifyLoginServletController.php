<?php namespace YiZan\Http\Controllers\Wap;
/**
 * 输出二次验证结果,本文件示例只是简单的输出 Yes or No
 */
// error_reporting(0);
use YiZan\Http\Controllers\Wap\GtClassGeetestlibController;
use Input;

class GtVerifyLoginServletController extends BaseController
{
    
    function __construct()
    {
        parent::__construct();
    }

    public function second()
    {
        session_start();
        $args = Input::all();

        if($args['type'] == 'pc'){
            $GtSdk = new GtClassGeetestlibController(CAPTCHA_ID, PRIVATE_KEY);
        }elseif ($args['type'] == 'mobile') {
            $GtSdk = new GtClassGeetestlibController(MOBILE_CAPTCHA_ID, MOBILE_PRIVATE_KEY);
        }

        $user_id = $_SESSION['user_id'];
        if ($_SESSION['gtserver'] == 1) {   //服务器正常
            $result = $GtSdk->success_validate($args['geetest_challenge'], $args['geetest_validate'], $args['geetest_seccode'], $user_id);
            if ($result) {
                return '{"status":"success"}';
            } else{
                return '{"status":"fail"}';
            }
        }else{  //服务器宕机,走failback模式
            if ($GtSdk->fail_validate($args['geetest_challenge'],$args['geetest_validate'],$args['geetest_seccode'])) {
                return '{"status":"success"}';
            }else{
                return '{"status":"fail"}';
            }
        }
    }
}

