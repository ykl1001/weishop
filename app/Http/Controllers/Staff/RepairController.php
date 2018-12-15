<?php
namespace YiZan\Http\Controllers\Staff;

use Redirect,View,Input,Response;
/**
 * 首页
 */
class RepairController extends AuthController {
    public function __construct() {
        parent::__construct();
        View::share('active',"repair");
        View::share('show_top_preloader',true);
    }

    /**
     * 新订单
     */
    public function index() {
        $args = Input::all();
        $args['page'] = $args['page']?$args['page']:1;
        $args['status'] = $args['status'] ? $args['status'] : 0;
        $list = $this->requestApi('repair.lists',$args);

        if($list['code'] == 0) {
            View::share('list', $list['data']);
        }

        View::share('status', $args['status']);
        View::share('args', $args);
        if($args['tpl']){
            return $this->display($args['tpl']);
        }
        return $this->display();
    }

    /**
     * 订单详情
     */
    public function detail() {
        $result = $this->requestApi('repair.detail',Input::all());

        if($result['data']){
            View::share('data',$result['data']);
        }else{
            return $this->error("订单获取失败");
        }
        View::share('title','维修详情');
        View::share('tpl','Order');
        View::share('nav_back_url', u('repair/index'));
        View::share('url_css',"#repair_index_view_1");

        return $this->display();
    }

    public function complte(){
        $result = $this->requestApi('repair.status',Input::all());
        return Response::json($result);
    }

}