<?php 
namespace YiZan\Http\Controllers\Admin;
use Input, View, Time;
/**
 * 返现统计
 */
class InvitationStatisticsController extends AuthController {
	/**
	 * [index 返现统计]
	 */
	public function index() {
		$args = Input::all(); 
        $args['year'] = ($args['year'] > -99) ? $args['year'] : Time::toDate(UTC_TIME, 'Y');
        $args['month'] = ($args['month'] > -99) ? $args['month'] : Time::toDate(UTC_TIME, 'm');
        //获取订单列表中的年份
        $orderyear = $this->requestApi('seller.statistics.year');  
        View::share('orderyear',$orderyear['data']);
		$list = $this->requestApi('invitation.statistics', $args); 
		if($list['code'] == 0) {
			View::share('lists', $list['data']['list']);
			View::share('sum', $list['data']['sum']);
		} 
		View::share('args', $args);
		return $this->display();
	} 

}
