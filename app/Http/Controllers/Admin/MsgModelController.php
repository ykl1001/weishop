<?php 
namespace YiZan\Http\Controllers\Admin;
use View, Input, Form,Response,Lang;

/**
 * 消息模板管理
 */
class MsgModelController  extends AuthController {

	public function index() {

        $list = $this->requestApi('msgModel.lists');
        if($list['code'] == 0)
        {
            View::share('list', $list['data']['list']);
        }
        $data = [
            'order' => "订单消息模板管理",
            'seller' => "商家消息模板管理",
            'promotion' => "优惠券消息模板管理",
            'message' => "普通消息模板管理",
        ];
        View::share('data', $data);
        return $this->display();
    }

    public function getId() {
        $list = $this->requestApi('msgModel.getId',input::all());
        if($list['code'] == 0)
        {
            View::share('data', $list['data']);
        }
        $type = [
            'order' => "订单消息模板管理",
            'seller' => "商家消息模板管理",
            'promotion' => "优惠券消息模板管理",
        ];
        View::share('type', $type);
        return $this->display('item');
    }

    /**
     * 服务人员提现列表
     */
    public function save() {

        $args = Input::all();
        $result = $this->requestApi('msgModel.save',['data'=>$args]);
        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('api_system.success.handle'), u('MsgModel/index'), $result['data']);
    }
}
