<?php 
namespace YiZan\Http\Controllers\Staff;
use Input, Lang, Session, View, Redirect, Response;
/**
 * 员工控制器
 */
class StaffController extends BaseController {

    /**
     * 初始化信息
     */
    public function __construct() {
        parent::__construct();
        $return_url = Session::get('return_url');
        View::share('return_url', !empty($return_url) ? $return_url : u('Index/index'));
        View::share('show_top_preloader', false);

    }

	/**
	 * 员工登录
	 */
	public function login() {
        if ($this->tpl != 'staff.run') {
            View::share('is_show_top',false);
        }
        View::share('title','登录');
		return $this->display();
	}

	/**
	 * 执行登录
	 */
	public function dologin() {
        $result = $this->requestApi('user.login',Input::all());
		if ($result['code'] == 0) {
			$this->setStaff($result['data']);
			$this->setSecurityToken($result['token']);
			Session::set('return_url','');
			Session::save();
		}
        View::share('title','登录中');
		return Response::json($result);
	}

	/**
	 * 员工登出
	 */
	public function logout() {
		$this->staff = '';
        $this->staffId = 0;
        $this->role = 0;
		Session::set('staff','');
		$this->setSecurityToken(null);
        return Response::json(u('Staff/login'));
//		return Redirect::to(u('Staff/login'))->send();
	} 

	/**
	 * [reg 用户注册]
	 */
	public function reg() {
		return $this->display();
	}

	/**
	 * [reg 修改密码]
	 */
	public function repwd() {
        View::share('title','找回密码');
		return $this->display();
	}

    /**
     * [reg 修改密码]
     */
    public function repwds() {
        View::share('title','重置密码');
        return $this->display();
    }

	/**
	 * [doreg 执行修改密码]
	 */
	public function dorepwd() {
		$data = Input::all();
		$data['type'] = 'repwd';
		$result = $this->requestApi('user.repwd',$data);
		if($result['code'] == 0){
			$this->setStaff($result['data']);
			$this->setSecurityToken($result['token']);
		}
        return Response::json($result);
	}

	/**
	 * 生成验证码
	 */
	public function verify() {
		$mobile = Input::get('mobile');
		$result = $this->requestApi('user.mobileverify',array('mobile'=>$mobile));
        return Response::json($result);
	}
    /**
     * 生成验证码
     */
	public function voiceverify() {
		$mobile = Input::get('mobile');
		$result = $this->requestApi('user.mobileverify',array('mobile'=>$mobile, "voice"=>true));
        return Response::json($result);
	}
	
    /**
     * 修改手机号
     */
	public function updateMobile() {
		$args = Input::all();
		$result = $this->requestApi('user.info.mobile', $args);
        return Response::json($result);
	}

    public function regpush(){
        $data = Input::all();
        $result = $this->requestApi('user.regpush',$data);
    }

    public function downimage()
    {
        $args = Input::all();

        $name = iconv("utf-8", "gb2312", 'logo');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment;filename="' . $name . '.png"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        header("Expires: 0");

        $images = 'StaffD2' . $args['sellerId'] . '.png';
        $outfile = base_path() . '/public/code/' . $images; //输出图片位置
        echo file_get_contents($outfile);
        exit;
    }


}
