<?php 
namespace YiZan\Http\Controllers\Admin;
use Input, View;
/**
 * 返现订单
 */
class InvitationOrderController extends AuthController {
	/**
	 * [index 返现订单]
	 */
	public function index() {
		$args = Input::all();

        $args['orderType'] = 2 ;
		$list = $this->requestApi('invitation.orderlist', $args);

		if($list['code'] == 0)
		{
			View::share('list', $list['data']['list']);
		}
		return $this->display();
	}

}
