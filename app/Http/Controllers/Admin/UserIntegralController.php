<?php 
namespace YiZan\Http\Controllers\Admin; 

use Input,View;

/**
 * 会员积分日志
 */
class UserIntegralController extends AuthController {
	/**
	 * 日志列表
	 */
	public function index() {
        $args = Input::all();
		$result = $this->requestApi('userintegral.lists',$args);
		if( $result['code'] == 0 ) {
			View::share('list', $result['data']['list']); 
		}
        return $this->display();
	}


}
