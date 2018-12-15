<?php namespace YiZan\Http\Controllers\Wap;

use YiZan\Utils\Time;
use View, Input, Lang, Route, Page ,Session,Log,Redirect, Cache,Response;
/**
 * 赚钱首页
 */
class MakeMoneyController extends UserAuthController {

    //
    public function __construct() {
        parent::__construct();
        View::share('nav','makemoney');

        if(!IS_OPEN_FX){
            return Redirect::to(u('UserCenter/index'))->send();
        }
    }
    /**
     * 首页信息
     */
    public function index() {
        //新加看看check
        $data = $this->requestApi('make.money');
        View::share('data', $data['data']);
        return $this->display();
    }

    public function order(){
        $args = Input::all();
        $args['status'] == $args['status'] ? $args['status'] : 0;
        $list = $this->requestApi('make.order', $args);
        View::share('list', $list['data']);
        View::share('args', $args);
        return $this->display();
    }

    public function orderList(){
        $args = Input::all();
        $args['status'] == $args['status'] ? $args['status'] : 0;
        $list = $this->requestApi('make.order', $args);
        View::share('list', $list['data']);
        View::share('args', $args);
        return $this->display("order_item");
    }

    public function detail(){
        $args = Input::all();
        $data = $this->requestApi('make.detail', $args);
        if($data['code'] == 0){
            View::share('data', $data['data']);
        }

        View::share('args', $args);
        return $this->display();
    }
}
