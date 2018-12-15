<?php
namespace YiZan\Http\Controllers\Admin;
use  Config;
use View, Input, Lang, Route, Page, Validator, Session, Response, Time;
/**
 * 自营商家
 */
class OneselfConfigController extends AuthController{

    public function index(){
        $data = $this->requestApi('seller.get', ['id'=>ONESELF_SELLER_ID]);

        //开通的城市列表
        $citys = $this->requestApi('city.getcitylists');
        View::share('citys', $citys['data']);
        View::share('data',  $data['data']);
        $staffstime = $this->requestApi('seller.oneselfSellerLists',['id'=>$data['data']['id']]);
        if($staffstime['code'] == 0)
            View::share('stime', $staffstime['data']);
        return $this->display();
    }
    public function save(){
        $args = Input::all();
        $args['businessScope'] = [];
        if($args['cityLists'] != ''){
            $args['businessScope'] = explode(',', $args['cityLists']);
            unset($args['cityLists']);
        }
        $data = $this->requestApi('seller.oneselfsave', $args);
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98008'), url('OneselfConfig/index'));
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98008'), url('OneselfConfig/index'), $data['data']);
    }

    /*
    *更新预约时间
    */
    public function updatetime(){
        $args = Input::all();
        $data  = $this->requestApi('seller.oneselfSellerUpdate',$args);
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'), url('OneselfConfig/index'));
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), url('OneselfConfig/index'), $data['data']);
    }
    /*
   *添加预约时间
   */
    public function addtime(){
        $args = Input::all();
        $data  = $this->requestApi('seller.oneselfSellerInsert',$args);
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'), url('OneselfConfig/index'));
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), url('OneselfConfig/index'), $data['data']);
    }
    /**
     * [destroy 删除时间]
     */
    public function deldatatime(){
        $args = Input::all();
        $result  = $this->requestApi('seller.oneselfSellerDelete',$args);
        return Response::json($result);
    }

    public function gettimes()
    {
        $args['id'] = (int)Input::get('id');
        $staffstime = $this->requestApi('seller.oneselfSellerLists',$args);

        return Response::json($staffstime);
    }
    public function showtime()
    {
        $staffstime = $this->requestApi('seller.oneselfSellerEdit',Input::all());
        return Response::json($staffstime);
    }
}  