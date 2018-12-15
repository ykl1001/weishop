<?php namespace YiZan\Http\Controllers\Wap;

use View, Input,Redirect,Config,Session;
/**
 * 邀请返现
 */
class InvitationController extends AuthController {

     public function __construct() {
         parent::__construct();
         if(!IS_OPEN_FX){
             return Redirect::to(u('UserCenter/index'))->send();
         }
     }
	public function index() {
        $user = $this->user;
        if(!empty($user['fanweId']) && FANWEFX_SYSTEM){
            $fx_user = $this->user;
            $url = sprintf(Config::get('app.fanwefx.fx_host').'/wap/distribution_ewm?id=%s&site=%s', $fx_user['fanweId'], base64_encode(u('UserCenter/index')));
            Redirect::to($url)->send();
        }

		$userId = $this->userId;
        $args['id'] = $userId;
        $args['type'] = "User";
        //生成二维码
        $invitation = $this->requestApi('invitation.cancode',$args);
        if($invitation['code'] == 0){
            View::share('images', $invitation['data']);
            //成功邀请人数
            //已经赚到金额

            //获取分享信息
            $invitation = $this->requestApi('invitation.get');

            if($this->user['isPay'] == 0 && IS_OPEN_FX && $invitation['data']['userStatus'] != 1){
                return Redirect::to(u('UserCenter/userhelp',['isFx' => 1]))->send();
            }

            if($invitation['code'] == 0)
            {
                $getWeixinUser = $this->requestApi('Useractive.getWeixinUser',['openid'=>$this->user['openid']]);
                $newtitle = str_replace("上门服务","",$getWeixinUser['data']['nickname'].$invitation['data']['shareTitle']);
                $invitation['data']['shareTitle'] = $newtitle;
                View::share('invitation', $invitation['data']);
				View::share("weiXinData",  $getWeixinUser['data']);

				$weiXinUserData = Session::get("user");
				View::share('weiXinUserData',$weiXinUserData);
            }
            if($invitation['data']['userStatus'] == 1){

                $link_url = u('User/guide', $args);

                View::share('link_url',$link_url);
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                $url = $protocol.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
                $weixin_arrs = $this->requestApi('invitation.getweixin',array('url' => $url));
                if($weixin_arrs['code'] == 0){
                    View::share('weixin',$weixin_arrs['data']);
                }
                //生成二维码
                $userc = $this->requestApi('invitation.userc',$args);
                if($userc['code'] == 0){
                    View::share('userc',$userc['data']);
                }
            }
        }
		return $this->display();
	}

    /**
     * 二维码
     */
    public function cancode(){
        $args = Input::all();
        $val = $args['val'];
        $errorCorrectionLevel = 'L';//容错级别
        $matrixPointSize = 14;//生成图片大小
        $backColor = 0xFFFFFF; //背景色
        $foreColor = 0x000000; //前景色
        $logo = asset('images/fenx.jpg');
        $margin = 1; //边距
        $QR = '';
        include base_path().'/vendor/code/Code.class.php';
        $QRcode = new \QRcode();
        //生成二维码图片
        $QRcode->png($val, false, $errorCorrectionLevel, $matrixPointSize, $margin,$saveandprint=false,$backColor,$foreColor);
        $QR = imagecreatefromstring(file_get_contents($QR));
        if ($logo !== FALSE) {
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);//二维码图片宽度
            $QR_height = imagesy($QR);//二维码图片高度
            $logo_width = imagesx($logo);//logo图片宽度
            $logo_height = imagesy($logo);//logo图片高度
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width/$logo_qr_width;
            $logo_qr_height = $logo_height/$scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            //重新组合图片并调整大小
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,$logo_qr_height, $logo_width, $logo_height);
        }
        echo $QR;
    }
    /**
     * 好友列表
     */
    public function userlists(){
        return $this->userinfoList('userlists');
    }

    public function userinfoList($tpl='userlists_item') {
        $args = Input::all();
        $result = $this->requestApi('invitation.getuserlists',$args);
        View::share('lists', $result['data']['list']);
        View::share('count', $result['data']['count']);
        View::share('args', $args);
        return $this->display($tpl);
    }

    /**
     * 返现列表
     */
    public function records(){
        return $this->recordsList('records');
    }

    public function recordsList($tpl='records_item') {
        $args = Input::all();
        $result = $this->requestApi('invitation.getrecords', $args);
        View::share('lists', $result['data']);
        return $this->display($tpl);
    }

	public function explain() {
		//分享返现
        $invitation = $this->requestApi('invitation.get',['id'=>1]);

        if($invitation['code'] == 0)
        {
        	View::share('explain', $invitation['data']['shareExplain']);
        }

		return $this->display();
	}

}
