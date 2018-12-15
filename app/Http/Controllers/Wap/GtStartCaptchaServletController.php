<?php namespace YiZan\Http\Controllers\Wap;
/**
 * 使用Get的方式返回：challenge和capthca_id 此方式以实现前后端完全分离的开发模式 专门实现failback
 * @author Tanxu
 */
//error_reporting(0);
use YiZan\Http\Controllers\Wap\GtClassGeetestlibController;

use Input, Response;

class GtStartCaptchaServletController extends BaseController
{
	
	function __construct()
	{
		parent::__construct();
	}

	public function once()
	{
		$args = Input::all();

		if($args['type'] == 'pc'){
			$GtSdk = new GtClassGeetestlibController(CAPTCHA_ID, PRIVATE_KEY);
		}elseif ($args['type'] == 'mobile') {
			$GtSdk = new GtClassGeetestlibController(MOBILE_CAPTCHA_ID, MOBILE_PRIVATE_KEY);
		}
		session_start();
		$user_id = "test";
		$status = $GtSdk->pre_process($user_id);
		$_SESSION['gtserver'] = $status;
		$_SESSION['user_id'] = $user_id;

		return Response::json($GtSdk->get_response_str());
	}
}

