<?php 
namespace YiZan\Http\Controllers\Proxy;
 
use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Form, Config;

/**
 * 代理首页
 */
class IndexController extends AuthController {

	/**
	 * 代理
	 */
	public function index() {
        $args = Input::all();
        $args['type'] = !empty($args['type']) ? $args['type'] : 1;
        $total = $this->requestApi('totalview.total');
        $data = $this->requestApi('order.ordercount.total',$args);
        if($total['code'] == 0)
            View::share('total', $total['data']);
        if($data['code'] == 0)
            View::share('data', $data['data']);

		return $this->display();
	}  

}
