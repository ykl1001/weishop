<?php 
namespace YiZan\Http\Controllers\Proxy;

use YiZan\Http\Controllers\YiZanController;
use View, Route, Input, Lang, Session, Redirect;
/**
 * 后台公共页面控制器
 */
class PublicController extends BaseController {
	/**
	 * 管理员登录
	 */
	public function login() {
		if( Session::get('proxy_token') && Session::get('proxy') ) {
			return Redirect::to('Index/index');
		}
		return $this->display();
	}

	/**
	 * 检查登录提交信息
	 */
	public function dologin() {
		$args = Input::all();
		if (empty($args['name'])) {
			return $this->error(Lang::get('proxy.code.11000'), u('Public/login'), $args);
		}

		if (empty($args['pwd'])) {
			return $this->error(Lang::get('proxy.code.11001'), u('Public/login'), $args);
		}

		$result = $this->requestApi('public.login',$args);  

		if( $result['code'] > 0 ) {
			return $this->error($result['msg'], u('Public/login'));
		}
		$this->setSecurityToken($result['data']['token']);
		$this->setProxy($result['data']['data']);
		
		return $this->success($result['msg'], u('Index/index'), $result['data']);
	} 



	/**
	 * 管理员登出
	 */
	public function logout() {
		Session::put('proxy', null);
		$this->setSecurityToken(null);
		return Redirect::to('Public/login');
	}
}
