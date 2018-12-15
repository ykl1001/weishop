<?php namespace YiZan\Http\Controllers\Wap;

use View, Input, Lang, Route, Page,Session,Response,Redirect;
/**
 *   开门
 */
class UnlockController extends BaseController {

    //
    public function __construct() {
        parent::__construct();
        View::share('nav','index');
        View::share('is_show_top',false);
    }
    public function index(){
        $args = Input::all();
        if($args['openId'] != ""){
            Session::set('openId', $args['openId']);
            Session::save();
        }
        if(!Session::get('openId')){
            return Redirect::to(u('WeixinOpenDoor/authorize'));
        }
        $data = $this->requestApi('district.getdistrict', $args);
        View::share('data', $data['data']);
        View::share('args', $args);
        View::share('user', $this->user);

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $protocol.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
        $weixin_arrs = $this->requestApi('invitation.getweixin',array('url' => $url));
        View::share('weixin',$weixin_arrs['data']);

        $args['openid'] = Session::get('openId');
        $result = $this->requestApi('property.qryAllKeys',$args);
        if($result['data']['code'] == 0){
            View::share('qryAllKeys',$result['data']);
        }else{
            View::share('qryAllKeys',null);
        }
        return $this->display();
    }
    //绑定
    public function bindDeivce(){
        $args = Input::all();
        $args['openid'] = Session::get('openId');
        $result = $this->requestApi('property.bindDeivce',$args);
        return Response::json($result);
    }
    //查看是否绑定
    public function isbindDeivce(){
        $args = Input::all();
        $args['openid'] = Session::get('openId');
        $result = $this->requestApi('property.isBindDeivce',$args);


        //file_put_contents('/mnt/demo/sq/storage/logs/app1.log', print_r($result, true) . '---' . print_r($data, true), FILE_APPEND);
        return Response::json($result['data']);
    }

    //开门
    public function openDoor(){
        $args = Input::all();
        $args['openid'] = Session::get('openId');
        $result = $this->requestApi('property.openDoor',$args);
        //file_put_contents('/mnt/demo/sq/storage/logs/app2.log', print_r($result, true) . '---' . print_r($data, true), FILE_APPEND);
        return Response::json($result['data']);
    }

    //开门日志
    public function openDoorLog(){
        $args = Input::all();
        $result = $this->requestApi('user.opendoor',$args);
        return Response::json($result['data']);
    }

}