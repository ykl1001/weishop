<?php 
namespace YiZan\Http\Controllers\Admin;
use Input, View, Response;
/**
 * 分享返现
 */
class InvitationSetController extends AuthController {
	/**
	 * [index 分享返现设置]
	 */
	public function index() {
		$result = $this->requestApi('invitation.get');

		if($result['code'] == 0)
		{
			View::share('data', $result['data']);
		}
		return $this->display();
	}

	/**
	 * 保存分享返现设置
	 */
	public function save() {
		$args = Input::all();
		$result = $this->requestApi('invitation.save', $args);
		$url = u('InvitationSet/index');

		if ($result['code'] == 0) {
            return  $this->success($result['msg'], $url, $result['data']);
        } else {
            return $this->error($result['msg']);
        }
	}

}
