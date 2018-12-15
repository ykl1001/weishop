<?php 
namespace YiZan\Http\Controllers\Seller;

use View, Input, Lang, Route, Page, Response, Redirect;

/**
 * 营销活动
 */
class ActivityController extends AuthController {
	/**
	 * 活动列表
	 */
	public function index() {
		$args = Input::all();

		$result = $this->requestApi('activity.lists', $args);
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
        
		return $this->display();
	}

	/**
	 * 查看
	 */
	public function edit() {
		$args = Input::all();
		$result = $this->requestApi('activity.get', $args);

		if($result['code'] == 0)
		{
			View::share('data', $result['data']);
		}

		if($result['data']['type'] == 4)
		{
			return $this->display('newedit');
		}
		else if($result['data']['type'] == 5)
		{
			return $this->display('fulledit');
		}
		else if($result['data']['type'] == 6)
		{
			if( ! function_exists('array_column'))
            {
                $ids = \YiZan\Http\Controllers\YiZanViewController::array_column($result['data']['activityGoods'], 'goodsId');
            }
            else{
                $ids = array_column($result['data']['activityGoods'], 'goodsId');
            }
			$goodsList = $this->requestApi('goods.activityLists', ['ids'=>$ids]); //获取选择的商品信息

			if($goodsList['code'] == 0)
			{
				View::share('goodsList', $goodsList['data']);
			}
			return $this->display('specialedit');
		}
		else
		{
			return Redirect::to('Activity/index');
		}

	}

	/**
     * 作废
     */
    public function cancellation() {
        $args = Input::all();
        $result = $this->requestApi('activity.cancellation', $args);
        return Response::json($result);
    }

	
}
