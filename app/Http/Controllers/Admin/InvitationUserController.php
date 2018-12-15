<?php 
namespace YiZan\Http\Controllers\Admin;
use Input, View;
/**
 * 邀请会员列表
 */
class InvitationUserController extends AuthController {
	/**
	 * [index 邀请会员列表]
	 */
	public function index() {
		$args = Input::all();
		if($args['userName'] || $args['invitationName']){
			$args['page'] = 1;
		}
		$list = $this->requestApi('invitation.userlist', $args);

		if($list['code'] == 0)
		{
			View::share('lists', $list['data']['list']);
		}

		return $this->display();
	}

	/**
	 * 佣金明细
	 */
	public function invitationList() {
		$args = Input::all();
		$list = $this->requestApi('invitation.invitationlist', $args);

		if($list['code'] == 0)
		{
			View::share('list', $list['data']['list']);
		}

		return $this->display();
	}

}
