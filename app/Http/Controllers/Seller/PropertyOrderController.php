<?php namespace YiZan\Http\Controllers\Seller;

use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Validator, Session, DB, Response, Redirect;
/**
 * 物业
 */
class PropertyOrderController extends AuthController {

	/**
	 * 物业订单列表
	 */
	public function index() {
		$args = Input::all();  
        $propertybuildinglist = $this->requestApi('propertybuilding.lists');
        View::share('builds', $propertybuildinglist['data']['list']);
        $result = $this->requestApi('propertyorder.lists', $args); 
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
		return $this->display();
	}

	/**
	 * 物业订单详情
	 */
	public function detail() {
		$args = Input::all();   
        $result = $this->requestApi('propertyorder.get', $args); 
        if( $result['code'] == 0 ){
            View::share('data', $result['data']);
            View::share('list', $result['data']['orderItem']);
        }
		return $this->display();
	}

    public function searchroom() {
        $args = Input::all();
        $args['pageSize'] = 9999;
        $result = $this->requestApi('propertyroom.lists',$args);
        return Response::json($result);
    }

}
