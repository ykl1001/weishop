<?php 
namespace YiZan\Http\Controllers\Admin; 

use Input,View, Response, Lang;

/**
 * 验证码设置
 */
class VcodeSetController extends AuthController {
	/**
	 * 设置验证码
	 */
	public function index() {
		$result = $this->requestApi('config.get',['code'=>'vcode_type']);
		if( $result['code'] == 0 ) {
			View::share('data', $result['data']); 
		}
        return $this->display();
	}

	/**
	 * 保存验证码
	 */
	public function save() {
		$args = Input::all();
		$result = $this->requestApi('config.updateconfig',['code'=>'vcode_type','val'=>$args['vcodeType']]);
		$result = [
			'status' => true,
			'data' => '',
			'msg' => Lang::get('api.success.update_info'),
		];

		return Response::json($result);
	}

}
