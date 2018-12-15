<?php namespace YiZan\Http\Controllers\Wap;
use YiZan\Utils\ImgVerify;
use YiZan\Utils\Http;
use View, Route, Input, Lang, Session, Redirect, Response, Request, Time, Cache;

/**
 * 用户登录注册控制器
 */
class UserController extends BaseController {
	public function __construct() {
		parent::__construct();
		if($this->userId > 0 && !in_array(ACTION_NAME,['app','verify','guide','isShareAlertShow'])) {
			header('Location:'.u('UserCenter/index'));
			exit;
		}
	}  

	/**
	 * 用户中心首页
	 */
	public function index() {
        View::share('user',$this->user);
        $result = $this->requestApi('seller.check', ['id'=>$this->userId]); 
        View::share('seller',$result['data']);
		View::share('title',"- 用户中心");
		return $this->display();
	}

	/**
	 * 用户登录
	 */
	public function login() {
		$args = Input::all();
	   	if (!isset($args['quicklogin'])) {
	   		$args['quicklogin'] = 2;
	   	}
        $return_url = Session::get('return_url');
        if (empty($return_url) || strpos($return_url, 'UserCenter/logout') !== false) {
            $return_url = u('UserCenter/index');
        }
        //是否是帖子详情
        if(!empty($args['setForum'])){
            Session::set('setForum',$args['setForum']);
            $return_url = u('Forum/detail',['id'=>$args['setForum']]);
        }
        //跳到分销平台
        $goshareping = $args['goshareping'];
        if($goshareping > 0){
            Session::put('goshareping', 1);
            Session::save();
            $return_url = u('UserCenter/wapcenter');
        }else{
            $goshareping = Session::get('goshareping');
            if($goshareping > 0){
                $return_url = u('UserCenter/wapcenter');
            }
        }

        View::share('return_url', $return_url);
        if ($this->tpl != 'wap.run') {
            View::share('is_show_top',false);
        }
        View::share('quicklogin',$args['quicklogin']);
        View::share('top_title','登录');
        if($args['setSellerReg'] == 1){
            Session::set('setSellerReg',1);
        }
        //邀请注册
        if( Input::get('shareUserId') > 0)
        {
            Session::put('invitationType', 'user');
            Session::put('invitationId', Input::get('shareUserId'));
        }
        Session::save();

        //获取验证码类型
        $vcodeType = $this->requestApi('config.configByCode',['code'=>'vcode_type']);
        View::share('vcodeType', $vcodeType['data']);

        return $this->display();
	}

	/**
	 * 执行登录
	 */
	public function dologin() {
		$data = Input::all();
     	$result = $this->requestApi('user.login',$data);
		if ($result['code'] == 0) {

            $config = $this->getConfig();
           
            Session::put('goshareping',0);

            $this->setUser($result['data']);
            $this->setSecurityToken($result['token']);
            Session::set('return_url','');
            Session::save();
		}
		return Response::json($result);
	}

	/**
	 * [reg 用户注册]
	 */
	public function reg() {

        $return_url = Session::get('return_url');
        if (empty($return_url) || strpos($return_url, 'UserCenter/logout') !== false) {
            $return_url = u('UserCenter/index');
        }

        //跳到分销平台
        $args =  Input::all();
        $goshareping = $args['goshareping'];
        if($goshareping > 0){
            Session::put('goshareping', 1);
            Session::save();
            $return_url = u('UserCenter/wapcenter');
        }else{
            $goshareping = Session::get('goshareping');
            if($goshareping > 0){
                $return_url = u('UserCenter/wapcenter');
            }
        }

        View::share('return_url', $return_url);
        $args = Input::all();

        //邀请注册
        $invitationType = Session::get('invitationType');
        $invitationId = Session::get('invitationId');
        if( !empty($invitationType) && !empty($invitationId) )
        {
        	$args['type'] = $invitationType;
        	$args['id']   = $invitationId;
        }

        //获取验证码类型
        $vcodeType = $this->requestApi('config.configByCode',['code'=>'vcode_type']);
        View::share('vcodeType', $vcodeType['data']);

        View::share('args', $args);
		return $this->display();
	}

	/**
	 * [doreg 执行注册]
	 */
	public function doreg() {
		$data = Input::all();
        $data['type'] = 'reg';

        $recommend_user = Session::get('recommend_user');
        $data['recommendUser'] = $recommend_user;

		$result = $this->requestApi('user.reg',$data);
		if($result['code'] == 0){
            $config = $this->getConfig();
            //如果方维运行cz
            if(FANWEFX_SYSTEM && $config['fx_user_check'] == 0){ //登录
                $args['username'] = $result['data']['mobile'];
                $args['password'] = $data['password'];
                $args['nickname'] = $result['data']['name'];
                $args['photo'] = $result['data']['avatar'];
                $args['mobile'] = $result['data']['mobile'];

                if($recommend_user > 0){
                    $args['recommender_id'] = $recommend_user;
                }
                $res = $this->requestApi('fx.api', ['path'=>'register', 'args'=>$args]);
                if(!empty($res['data'])){
                    //修改status为1
                    $args2['user_status'] = 1;
                    $args2['user_id'] = $res['data']['user_id'];
                    $this->requestApi('fx.api', ['path'=>'set_user_status', 'args'=>$args2]);
                    //把这fanwe_id修改好
                    $this->requestApi('user.changeuserinfo', ['userId'=>$result['data']['id'], 'fanweId'=>$res['data']['user_id']]);
                    $result['data']['fanweId'] = $res['data']['user_id'];
                }
            }
            Session::put('goshareping',0);

			$this->setUser($result['data']);
			$this->setSecurityToken($result['token']);
			Session::set('return_url','');
			Session::save();

			//注册成功 清空邀请注册的session
			$invitationType = ucfirst(Session::get('invitationType'));
	        $invitationId = ucfirst(Session::get('invitationId'));
	        if( !empty($invitationType) && !empty($invitationId) )
	        {
                Session::set('is_share_alert_show','1');
	        	Session::put('invitationType', null);
	            Session::put('invitationId', null);
	            Session::save();
	        }
		}

		return Response::json($result);
	}

	/**
	 * [reg 修改密码]
	 */
	public function repwd() {
        //获取验证码类型
        $vcodeType = $this->requestApi('config.configByCode',['code'=>'vcode_type']);
        View::share('vcodeType', $vcodeType['data']);
        
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
			$this->setUser($result['data']);
			$this->setSecurityToken($result['token']);
		}
		return Response::json($result);
	}

	/**
	 * 生成验证码
	 */
	public function verify() {
        $vcodeType = $this->requestApi('config.configByCode',['code'=>'vcode_type']);

		if($this->checkVerify() || $vcodeType['data'] == 1)
        {
            $mobile = Input::get('mobile');
			$type = Input::get('type') ? Input::get('type') : 'reg';
			$result = $this->requestApi('user.mobileverify',array('mobile'=>$mobile, 'type'=>$type));
            return Response::json($result);
        }else{
        	return Response::json(['code'=>1,'msg'=>Session::get("check_error")]);
        }
	}
    /**
     * 生成验证码
     */
	public function voiceverify() {
		$mobile = Input::get('mobile');
		$result = $this->requestApi('user.mobileverify',array('mobile'=>$mobile, "voice"=>true));
		return Response::json($result);
	}

    public function app(){
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $iphone = (strpos($agent, 'iphone')) ? true : false;
        $ipad = (strpos($agent, 'ipad')) ? true : false;
        $android = (strpos($agent, 'android')) ? true : false;
        $config = $this->getConfig();
        if($iphone || $ipad) {
            if(strpos($agent, 'micromessenger')){
                return $this->display();
            } else {
                Redirect::to($config['buyer_app_down_url'])->send();
            }
        }
        if($android) {
            Redirect::to($config['buyer_android_app_down_url'])->send();
        }
    }

    /**
	 * 生成图形验证码
	 */
	public function imgverify() {
        $this->createVerify();
		$imgVerify = new ImgVerify();
		$imgVerify->doimg();	
		$code = $imgVerify->getCode();
		Session::set('imgVerify', $code);
		Session::save();
		exit;
	}
    public function createVerify() 
    {	
		Session::set("user_reg", md5(Request::getClientIp().$_SERVER['HTTP_USER_AGENT']));
		Session::set("user_reg_time", UTC_TIME);
		Session::save();
	}

	private function checkVerify() 
    {
		if (!Request::ajax()) 
        {
			Session::put("check_error", '非法请求！1');
			Session::save();        	
			return false;
		}
		
		$referer = Request::header('REFERER');

        if ($referer != u('User/reg') && $referer != u('User/login',['quicklogin'=>1]) && $referer != u('User/repwd') && $referer != u('User/reg',['isGuide'=>1]))
        {
			Session::put("check_error", '非法请求！2');
			Session::save();
			return false;
		}

		$imgVerify = Session::get('imgVerify');

		if (strtolower($imgVerify) !== strtolower(Input::get('imgverify')) ) 
        {
			Session::put("check_error", '验证码不正确！');
			Session::save();
			return false;
		}
        
        Session::set('imgverify', "");
        
        Session::save();

		$userRegTime 	= Session::get('user_reg_time');

		$userRegTimeRe 	= (int)Session::get('user_reg_time_re');

		/*if(Session::get('user_reg') != md5(Request::getClientIp().$_SERVER['HTTP_USER_AGENT']) ||
			($userRegTimeRe > Time::getTime() - 60) || $userRegTime >= Time::getTime()) {
			//die('33');
			return false;
		}*/

		Session::put("user_reg_time_re", Time::getTime());

		Session::save();

		return true;
	}

    //设置电话 密码
    public function weixin(){
        $args = Input::all();
        $user_info = Session::get('wxlogin_userinfo');
        if(!empty($user_info)){
            $args['openid'] = $user_info['openid'];
            $args['unionid'] = $user_info['unionid'];
        }else{
            if(empty($args['openid'])){
                $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                if (empty($url)) {
                    return $this->error('参数错误');
                }
				if($args['wxlogin'] == 1){
					/**     不用三方数据 关闭注释  */
					$url = $url."?id=0";
					//$url = 'http://www.niusns.com/callback.php?m=Weixin&a=publicauthdsy&url='.$url;	
					return Redirect::to($url);
				}				
                $result = $this->requestApi('config.getpayment',['code' => 'weixinJs']);
                if (!$result || $result['code'] != 0) {
                    return $this->error('获取微信配置信息失败', $url);
                }

                $payment = $result['data'];
                $config = $payment['config'];

                Session::put('authorize_login_url', $url);
               // $config['appId'] = 'wxdec1e10223f8e4be';
                $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$config['appId'].
                    '&redirect_uri='.urlencode(u('User/accesstoken'))
                    .'&response_type=code&scope=snsapi_userinfo&state=YZ#wechat_redirect';

                return Redirect::to($url);
            }
        }
        //判断是否有这个openId 如果没有就加载模板 有了登录
        $is_user = $this->requestApi('user.getbyopenid',array('unionid'=>$args['unionid'],'openid'=>$args['openid']));
        //邀请注册
        $invitationType = Session::get('invitationType');
        $invitationId = Session::get('invitationId');
        if( !empty($invitationType) && !empty($invitationId) )
        {
            $data['invitationType'] = $invitationType;
            $data['invitationId']   = $invitationId;
        }
        if(empty($is_user['data']['address']) && empty($is_user['data']['id'])){
            //如果没有就注册呗
            if($user_info) {
				if($args['wxlogin'] == 1){
				/**     不用三方数据 关闭注释  */
					$data['openId'] =   $args['openid'];
					$data['unionid'] =  $args['unionid'];
					$data['name'] = 	$args['nickname'];
					$data['avatar'] = 	$args['headimgurl'];				
					$user_info['unionid'] =  $args['unionid'];				
					$user_info['openid']  =  $args['openid'];	
				}else{
					/**不用三方数据 关闭注释  */
					$data['openId'] = $user_info['openid'];
					$data['unionid'] = $user_info['unionid'];
					$data['name'] = $user_info['nickname'];
					$data['avatar'] = $user_info['headimgurl'];
				}
				
                $reg = $this->requestApi('user.regweixin',$data);

                if($data['invitationId']){
                    Session::set('is_share_alert_show','1');
                }
                $is_user = $this->requestApi('user.getbyopenid',array('unionid'=>$user_info['unionid'],'openid'=>$user_info['openid']));
				
            }else{
                return Redirect::to(u('User/login'));
            }
        }

        $this->setUser($is_user['data']);
        $this->setSecurityToken($is_user['token']);
        Session::set('return_url','');
        Session::save();
        return Redirect::to(u('UserCenter/index'));

    }

    public function accesstoken() {
        $code = $_REQUEST['code'];
        $url = Session::get('authorize_login_url');
        $url = !empty($url) ? $url : u("User/weixin");

        if (empty($code)) {
            return $this->error('授权失败', $url);
        }

        $state = Input::get('state');
        if($state == "fwapp"){
            $result = $this->requestApi('config.getpayment',['code' => 'weixin']);
            $payment = $result['data'];
            $config = $payment['config'];
        }else{
            $result = $this->requestApi('config.getpayment',['code' => 'weixinJs']);
            $payment = $result['data'];
            $config = $payment['config'];
        }

        if (!$result || $result['code'] != 0) {
            return $this->error('获取微信配置信息失败', $url);
        }

        $wxurl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$config['appId'].
            '&secret='.$config['appSecret'].'&code='.$code.'&grant_type=authorization_code';
        $result = @file_get_contents($wxurl);
        $result = empty($result) ? false : @json_decode($result, true);
        if (!$result) {
            return $this->error('授权失败', u("User/login"));
        } elseif (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->error('授权失败:'.$result['errmsg'], u("User/login"));
        }

        $openid = $result['openid'];
        $wxurl = "https://api.weixin.qq.com/sns/userinfo?access_token={$result['access_token']}&openid={$result['openid']}&lang=zh_CN";
        $result = @file_get_contents($wxurl);
        $result = empty($result) ? false : @json_decode($result, true);

        Session::put('wxlogin_userinfo',$result);
        Session::save();

        $unionid = $result['unionid'];
        $openid = $result['openid'];
        $url = $url.'?unionid='.$unionid."&openid=".$openid;

        return Redirect::to($url);
    }

    //通过微信注册
    public function doregbyweixin(){
        $user_info = Session::get('wxlogin_userinfo');

        $data = Input::all();
        $data['type'] = 'reg';

        if(!empty($user_info)) {
            $data['openId'] = $user_info['openid'];
            $data['unionid'] = $user_info['unionid'];
            $data['name'] = $user_info['nickname'];
            $data['avatar'] = $user_info['headimgurl'];
        }else{
            $result['code'] = 1;
            $result['msg'] = '微信授权有误';
            return Response::json($result);
        }

        $result = $this->requestApi('user.regweixin',$data);
        if($result['code'] == 0){
            $this->setUser($result['data']);
            $this->setSecurityToken($result['token']);
            Session::set('return_url','');
            Session::save();
        }

        return Response::json($result);
    }

    public function guide(){ 
        //邀请注册
        if( strtolower(Input::get('type')) == 'user' && Input::get('id') > 0)
        {
            Session::put('invitationType', Input::get('type'));
            Session::put('invitationId', Input::get('id'));
        }else{
            Session::put('invitationType', "");
            Session::put('invitationId', "");
        }
        Session::save();
        $getWeixinUser = $this->requestApi('Useractive.getWeixinUser',['openShareUserId'=>Input::get('id')]);
        View::share('weixinUserDsy', $getWeixinUser['data']);
        $invitation = Cache::get('invitation');
        View::share('invitation', $invitation);
        return $this->display();
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
                return Redirect::to(u('wap#User/login',['goshareping'=>1]));
            }

//            $result = $this->requestApi('user.loginsaas',['fnaweId'=>$result['user_id']]);
//            $this->setUser($result['data']);
//            $this->setSecurityToken($result['token']);
//            Session::set('return_url','');
//            Session::save();
//            return Redirect::to(u('wap#UserCenter/index'));
        }
    }

    public function saasregister(){
        //cz
        require_once(base_path() . '/Saas/SAASAPIServer.php');
        $server = new \SAASAPIServer(FANWESAAS_APP_ID, FANWESAAS_APP_SECRET);
        $result = $server->decodeSecurityParams(Input::get('_saas_params'));
        if ($result === false) {
            return false;
        } else {
            $user = Session::get('user');

            if(empty($user) && $result['appid'] == FANWESAAS_APP_ID){
                return Redirect::to(u('wap#User/reg',['goshareping'=>1]));
            }

//            $data['mobile'] = $result['user_username'];
//            $data['pwd'] = $result['password'];
//            $data['fanweId'] = $result['user_id'];
//            $data['type'] = 'reg';
//            $result = $this->requestApi('user.reg',$data);
//            $this->setUser($result['data']);
//            $this->setSecurityToken($result['token']);
//            Session::set('return_url','');
//            Session::save();
//
//            return Redirect::to(u('wap#UserCenter/index'));
        }
    }
    public function isShareAlertShow(){
        Session::set('is_share_alert_show','0');
        Session::save();
    }

}
