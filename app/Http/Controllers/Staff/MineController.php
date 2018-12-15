<?php
namespace YiZan\Http\Controllers\Staff;

use View, Input, Lang, Route, Page ,Session,Response,Config;
/**
 * 我的
 */
class MineController extends AuthController {

	public function __construct() {
		parent::__construct();
        View::share('active',"mine");
	}
	
	/**
	 * 首页信息 
	 */
	public function index() {
		//获取是否有未读消息
		$result = $this->requestApi('msg.status');
		if($result['code'] == 0)
			View::share('hasNewMsg',$result['data']['hasNewMessage']);
        View::share('title','个人中心');
		View::share('staff',$this->staff);

        //分享返现
        $invitation = $this->requestApi('invitation.get',['id'=>1]);
        if($invitation['code'] == 0)
        {
            View::share('invitation', $invitation['data']);
        }

        //获取服务人员信息
        $staff = $this->requestApi('staff.getbyid',['extend'=>1]);
        View::share('staff', $staff); 

		return $this->display();
	}


    /**
     * 设置
     * 来源 Order/createMoreInfo
     */
    public function set() {
        View::share('title','设置');
        return $this->display();
    }
	
	/**
	 * 员工详细
	 */
	public function info() {
		View::share('staff',$this->staff);
		return $this->display();
	}
    /**
     * 我的消息
     */
    public function message() {
        $args = Input::all();
        $args['page'] = $args['page']?$args['page']:1;
        $list = $this->requestApi('msg.lists',$args);
        if($list['code'] == 0){
            View::share('list',$list['data']);
        }
        View::share('nav', 'msg');
        unset( $args['page']);
        View::share('args',$args);
        View::share('title','我的消息');
        if( count($list['data']) == 20){
            View::share('show_preloader',true);
        }
        if($args['tpl']){
            return $this->display('message_'.$args['tpl']);
        }
        return $this->display();
    }

    /**
     * 我的消息
     */
    public function ajaxmessage() {
        $args = Input::all();
        $args['page'] = $args['page']?$args['page']:1;
        $result = $this->requestApi('msg.lists',$args);
        return Response::json($result['data']);
    }

	/**
	 * 更改用户信息
	 */
	public function updateinfo() {
		$avatar = trim(Input::get('avatar'));
		$result = $this->requestApi('user.info.update',['avatar'=>$avatar]);
		if ($result['code'] == 0){
			$this->setStaff($result['data']);
		}
        return Response::json($result);
	}

	/**
	 * 阅读单条消息
	 */
	public function msgshow() {
		$args = Input::all();
		if( !is_array($args['id']) ) {
			$args['id'] = (int)$args['id'];
		}
        $this->requestApi('msg.read',$args);
        $list = $this->requestApi('msg.getdata',$args);

		View::share('data',$list);
        View::share('title','消息详情');

        View::share('role',$this->role);

        return $this->display();
	}

	/**
	 * 批量阅读消息
	 */
	public function readMsg() {
		$args = Input::all();
		if( !is_array($args['id']) ) {
			$args['id'] = (int)$args['id'];
		}
		$result = $this->requestApi('msg.read',$args);
		die(json_encode($result));
	}

	/**
	 * 批量删除消息
	 */
	public function deleteMsg() {
		$args = Input::all();
		if( !is_array($args['id']) ) {
			$args['id'] = (int)$args['id'];
		}
		$result = $this->requestApi('msg.delete',$args);
		die(json_encode($result));
	}

	/**
	 * 意见反馈
	 */
	public function feedback() {
        View::share('title', '意见反馈');
        View::share('nav_back_url', u('Mine/index'));
		return $this->display();
	}

	/**
	 * 增加意见反馈
	 */
	public function addfeedback() {
		$content = strip_tags(Input::get('content'));
		$result = $this->requestApi('feedback.create',['content'=>$content,'deviceType'=>'wap']);
		die(json_encode($result));
	}


    /**
     * [reg 修改密码]
     */
    public function repwd() {
        View::share('staff', $this->staff);
        View::share('title', '重置密码');
        View::share('nav_back_url', u('Mine/account'));
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
            Session::set('staff','');
            $this->setSecurityToken(null);
        }
        die(json_encode($result));
    }
    /**
     *
     */
    public function account2() {

        View::share('title', '帐号设置');
        View::share('staff',$this->staff);
        $config = Config::get("app.image_config")['server'];
        View::share('config',$config);
        return $this->display();
    }
    /**
     *
     */
    public function mobile() {
        View::share('title', '修改手机号');
        View::share('staff',$this->staff);
        return $this->display();
    }

    /**
     * 更改用户信息
     */
    public function updatework() {
        $is_work = trim(Input::get('is_work'));
        $result = $this->requestApi('user.info.updatework',['is_work'=>$is_work]);
        return Response::json($result);
    }

    /**
     * 编辑分类
     */
    public function account()
    {
        $args = Input::all();
        $args['status'] = $args['status'] ? $args['status'] : 0;
        $args['type'] = $args['type'] ? $args['type'] : 1;
        $args['page'] = $args['page'] ? $args['page'] : 1;
        $result = $this->requestApi('shop.staffbill', $args);
        View::share('account', $result['data']);

        //获取服务人员信息
        $staff = $this->requestApi('staff.getbyid',['extend'=>1]);
        View::share('staff', $staff);

        //判断是否有银行卡
        $result = $this->requestApi('shop.staffinfo');
        View::share('bank', $result['data']);

        View::share('ajaxurl_page', "_" . $args['status']);

        View::share('acut', $args);
        unset($args['page']);
        View::share('args', $args);

        if ($args['tpl']) {
            return $this->display('account_' . $args['tpl']);
        }
        View::share('title', '我的账单');
        return $this->display();
    }

    /**
     * 编辑分类
     */
    public function ajaxaccount()
    {
        $args = Input::all();
        $args['type'] = 1;
        $result = $this->requestApi('shop.staffbill', $args);
        return Response::json($result['data']);
    }

    /**
     * 银行卡
     */
    public function bank()
    {
        $args = Input::all();
        if ($args['id'] == "") {
            View::share('title', "绑定银行卡");
            View::share('url', 'account');
        } else {
            $result = $this->requestApi('staff.getbankinfo', $args);
            if ($result['code'] == 0) {
                if ($args['verifyCode']) {
                    View::share('verifyCode', $args['verifyCode']);
                    View::share('data', $result['data']['old']);
                    View::share('old', false);
                } else {
                    unset($result['data']['old']);
                    View::share('data', $result['data']);
                    View::share('old', true);
                }
                View::share('title', "编辑银行卡");
                View::share('url', 'carry');
            } else {
                View::share('title', "绑定银行卡");
                View::share('url', 'account');
            }
        }
        return $this->display();
    }

    /**
     * 银行卡
     */
    public function bankSve()
    {
        $args = Input::all();
        $result = $this->requestApi('staff.savebankinfo', $args);
        return Response::json($result);
    }

    /**
     * 我要提现
     */
    public function carry()
    {
        $args = Input::all();
        View::share('title', '提现');

        //获取服务人员信息
        $staff = $this->requestApi('staff.getbyid',['extend'=>1]);
        View::share('staff', $staff);

        //判断是否有银行卡
        $result = $this->requestApi('shop.staffinfo');
        View::share('bank', $result['data']);

        if ($args['tpl']) {
            return $this->display('carry_' . $args['tpl']);
        }
        return $this->display();
    }

    /**
     * 提现记录
     */
    public function withdrawlog()
    {
        $args = Input::all();
        $args['type'] = $args['type'] ? $args['type'] : 2;
        $args['status'] = $args['status'] ? $args['status'] : 2;
        $result = $this->requestApi('shop.staffbill', $args);
        View::share('acut', $args);
        unset($args['page']);
        View::share('args', $args);
        View::share('account', $result['data']);
        View::share('title', '提现记录');

        if ($args['tpl']) {
            return $this->display('withdrawlog_' . $args['tpl']);
        }
        if (count($result['data']) == 20) {
            View::share('show_preloader', true);
        }
        return $this->display();
    }

    /**
     *
     */
    public function verifyCode()
    {
        $args = Input::all();
        $result = $this->requestApi('staff.getbankinfo', $args);
        View::share('data', $result['data']);
        View::share('title', "短信验证");
        return $this->display();
    }

    /**
     * 检查银行卡短信
     */
    public function verifyCodeCk()
    {
        $args = Input::all();
        $result = $this->requestApi('seller.verifyCodeCk', $args);
        return Response::json($result);
    }

    /**
     * 提现
     */
    public function withdraw()
    {
        $args = Input::all();
        $result = $this->requestApi('user.staffwithdraw', $args);
        return Response::json($result);
    }

}