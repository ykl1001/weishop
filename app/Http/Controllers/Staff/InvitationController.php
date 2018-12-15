<?php namespace YiZan\Http\Controllers\Staff;

use View,Redirect,Input;
/**
 * 邀请返现
 */
class InvitationController extends AuthController {
    public function __construct() {
        parent::__construct();
        //获取分享信息
        $invitation = $this->requestApi('invitation.get',['id'=>1]);
        if($invitation['data']['sellerStatus'] == 0){
            return Redirect::to(u('Mine/index'))->send();
        }
    }
	public function index() {
        $role = $this->role;
        if($role == 1 || $role == 3|| $role == 5|| $role == 7){
            $args['id'] = $this->staff['sellerId'];
            $args['type'] = "seller";
        }else{
            $args['id'] = $this->staffId;
            $args['type'] = "staff";
        }

		//生成二维码
        $invitation = $this->requestApi('invitation.cancode',$args);
        if($invitation['code'] == 0){
            View::share('images', $invitation['data']);
            //成功邀请人数
            //已经赚到金额

            //获取分享信息
            $invitation = $this->requestApi('invitation.get',['id'=>1]);
            if($invitation['code'] == 0)
            {
                View::share('invitation', $invitation['data']);
            }
            if($invitation['data']['sellerStatus'] == 1){
                $args['urltype'] = 1;
                $link_url = u('wap#Seller/detail',$args);
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
        $args = Input::all();
        $args['page'] = $args['page']?$args['page']:1;
        $list = $this->requestApi('invitation.getuserlists',$args);
        if($list['code'] == 0){
            View::share('lists',$list['data']);
        }
        View::share('nav', 'msg');
        unset( $args['page']);
        View::share('args',$args);
        View::share('title','我的好友');
        if( count($list['data']) == 20){
            View::share('show_preloader',true);
        }
        if($args['tpl']){
            return $this->display('userlists_'.$args['tpl']);
        }
        return $this->display();
    } 

    /**
     * 返现列表
     */
    public function records(){
        $args = Input::all();
        $args['page'] = $args['page']?$args['page']:1;
        $list = $this->requestApi('invitation.getrecords',$args);
        if($list['code'] == 0){
            View::share('lists',$list['data']);
        }
        View::share('nav', 'msg');
        unset( $args['page']);
        View::share('args',$args);
        View::share('title','奖励记录');
        if( count($list['data']) == 20){
            View::share('show_preloader',true);
        }
        if($args['tpl']){
            return $this->display('records_'.$args['tpl']);
        }
        return $this->display();
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
