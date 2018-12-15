<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\Order;
use YiZan\Utils\Time;
use YiZan\Http\Requests\Admin\OrderCreatePostRequest;
use View, Input, Lang, Route, Page, Form, Format, Response, Cache;
/**
 * 订单管理
 */
class OneselfOrderController  extends OrderController {
    /**
     * 订单列表
     */
    public function index() {
        $post = Input::all();
        $args = [
            'orderType' => 1,
            'sn' => trim($post['sn']),
            'mobile' => trim($post['mobile']),
            'beginTime' => trim($post['beginTime']) != '' ?  Time::toTime($post['beginTime']) : '',
            'endTime' => trim($post['endTime']) != '' ?  Time::toTime($post['endTime']) : '',
            'payStatus' => $post['payStatus'] != '-1' ?  $post['payStatus'] : '',
            'status' => (int)$post['status'] > 0 ?  $post['status'] : 0,
            'sellerName' => trim($post['sellerName']),
            'page' => (int)$post['page'],
            'isIntegralGoods' => 0,
            'payTypeStatus' => $post['payTypeStatus'],
			
        ];
        $args['isSeller'] = true;
        $result = $this->requestApi('order.lists', $args);
        if( $result['code'] == 0 ) {
            View::share('list', $result['data']['list']);
        }
        View::share('nav',$post['nav']);
        unset($args['page']);
        View::share('excel',http_build_query($args));
        View::share('args',$args);
        View::share('searchUrl', u('Order/index',['status' => $post['status'], 'nav'=>$post['nav']]));
        return $this->display();
    }
}
