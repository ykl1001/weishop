<?php 
namespace YiZan\Http\Controllers\Admin;

use View, Input, Lang, Response, Redirect;

/**
 * 开通城市管理
 */
class DispatchOrderController extends AuthController {
	public function index() {
        $post = Input::all();
        if(empty($post['status']))
        {
            return Redirect::to('DispatchOrder/index?status=10&nav=nav2');
        }

        $args = [
            'orderType' => 1,
            'sn' => trim($post['sn']),
            'mobile' => trim($post['mobile']),
            'payStatus' => $post['payStatus'] != '-1' ?  $post['payStatus'] : '',
            'status' => (int)$post['status'] > 0 ?  $post['status'] : 9,
            'sellerName' => trim($post['sellerName']),
            'page' => (int)$post['page'],
            'provinceId' => (int)$post['provinceId'] > 0 ?  $post['provinceId'] : '',
            'cityId' => (int)$post['cityId'] > 0 ?  $post['cityId'] : '',
            'areaId' => (int)$post['areaId'] > 0 ?  $post['areaId'] : '',
            'isIntegralGoods' => 0
        ];
        !empty($post['beginTime']) ? $args['beginTime'] = Time::toTime(strval($post['beginTime'])) : 0;
        !empty($post['endTime']) ? $args['endTime'] = (Time::toTime(strval($post['endTime'])) + 24 * 60 * 60 - 1 ) : 0;
        $args['isAll'] = 0;
        $args['sendFee'] = $args['status'] == 12 ? 1 : 0;
        $result = $this->requestApi('order.lists', $args);
        if( $result['code'] == 0) {
            View::share('list', $result['data']['list']);
        }

        !empty($post['nav']) ? $post['nav'] : 'nav1';
        View::share('nav',$post['nav']);

        unset($args['page']);
        View::share('args',$args);
        View::share('searchUrl', u('DispatchOrder/index',['status' => $post['status'], 'nav'=>$post['nav']]));

        return $this->display();
    }
}
