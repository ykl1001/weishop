<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Http\Controllers\YiZanController;
use View, Route, Input, Lang, Session, Redirect,Cache;
/**
 * 后台公共页面控制器
 */
class PublicController extends BaseController {
	/**
	 * 管理员登录
	 */
	public function login() {
		if( Session::get('admin_token') && Session::get('admin_user') ) {
			return Redirect::to('Index/index');
		}

        //跳到分销平台
        $args = Input::all();
        $goshareping = $args['goshareping'];
        if($goshareping > 0){
            Session::put('goshareping', 1);
            Session::save();
            $return_url = u('FxManageUrl/index',['direct'=>1]);
            View::share('return_url', $return_url);
        }else{
            $goshareping = Session::get('goshareping');
            if($goshareping > 0){
                $return_url = u('FxManageUrl/index',['direct'=>1]);
                View::share('return_url', $return_url);
            }
        }


        return $this->display();
	}

	/**
	 * 检查登录提交信息
	 */
	public function dologin() {
		$args = Input::all();
		if (empty($args['name'])) {
			return $this->error(Lang::get('admin.code.11000'), u('Public/login'), $args);
		}

		if (empty($args['pwd'])) {
			return $this->error(Lang::get('admin.code.11001'), u('Public/login'), $args);
		}

		$result = $this->requestApi('admin.user.login',$args);
		if( $result['code'] > 0 ) {
			return $this->error($result['msg'], u('Public/login'));
		}
		$this->setSecurityToken($result['data']['token']);
		$this->setAdminUser($result['data']['data']);
//        Cache::forever("_admin_controller_action_navs", $result['data']['data']["role"]['access']);
        Cache::forever("_admin_controller_action_navs_".$result['data']['data']["role"]['id'], "");
		return $this->success($result['msg'], u('Index/index'), $result['data']);
	}

	/**
	 * 生成验证码
	 */
	public function verify() {
		
	}

	/**
	 * 管理员登出
	 */
	public function logout() {
		Session::put('admin_user', null);
		$this->setSecurityToken(null);
		return Redirect::to('Public/login');
	}

    public function saaslogin(){
        //cz
        require_once(base_path() . '/Saas/SAASAPIServer.php');
        $server = new \SAASAPIServer(FANWESAAS_APP_ID, FANWESAAS_APP_SECRET);
        $result = $server->decodeSecurityParams(Input::get('_saas_params'));
        if ($result === false) {
            return false;
        } else {
            $user = Session::get('user');
            if(empty($user) && $result['appid'] == FANWESAAS_APP_ID){
                return Redirect::to(u('admin#Public/login',['goshareping'=>1]));
            }

//            $result = $this->requestApi('user.loginsaas',['fnaweId'=>$result['user_id']]);
//            $this->setUser($result['data']);
//            $this->setSecurityToken($result['token']);
//            Session::set('return_url','');
//            Session::save();
//            return Redirect::to(u('wap#UserCenter/index'));
        }
    }

}
