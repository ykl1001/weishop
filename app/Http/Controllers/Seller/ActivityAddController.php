<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Models\Goods;
use View, Input, Lang, Route, Page, Response, Session;

/**
 * 添加营销活动
 */
class ActivityAddController extends AuthController {
	/**
	 * 添加活动
	 */
	public function index() {
		$args = Input::all();
		
		$data = Session::get('ActivityAdd.all');  //活动信息
        $ids = Session::get('ActivityAdd.checkGoodsIds'); //选择的商品ID
        $result = $this->requestApi('goods.activityLists', ['ids'=>$ids]); //获取选择的商品信息

        //返回保存的活动信息
        if($data)
        {
            View::share('data', $data['form']); //历史表单
            View::share('salePrice', $data['salePrice']);  //历史折扣
        }
        //返回已选择的商家列表
        if($result['code'] == 0)
        {
            View::share('goodsLists', $result['data']);

        }

		return $this->display();
	}

	/**
	 * 选择商品
	 */
	public function addGoods() {
		$args = Input::all();  
		// $args['type'] = $option['type'] = Goods::SELLER_GOODS;
        $args['type'] = 0;  //查询所有
        $notIds = Session::get('ActivityAdd.checkGoodsIds');
		$args['notIds'] = !empty($notIds) ? $notIds : null;

		$result = $this->requestApi('goods.lists', $args); 
        $hasGoods = $this->requestApi('goods.hasActivityGoodsIds'); 
        $hasGoods = $hasGoods['data'];
        //排除已经存在的商品
        foreach ($result['data']['list'] as $key => $value) {
            if( in_array($value['id'], $hasGoods))
            {
                $result['data']['list'][$key]['checkedDisabled'] = 1;
            }
        }

		if($result['code'] == 0)
		{
			View::share('list', $result['data']['list']);
		}
		return $this->display();
	}

	/**
	 * 保存添加商品闪存数据
	 */
	public function save_special_data() {
		$args = Input::all();
        foreach ($args['form'] as $key => $value) {
           $data['form'][$value['name']] = $value['value'];
        }
        //$data['goodsIds'] = $args['goodsIds'];  //商品编号
        //$data['salePrice'] = $args['salePrice']; //商品对应的折扣价

        foreach ($args['goodsIds'] as $key => $value) {
            $data['salePrice'][$value] = $args['salePrice'][$key];  //商品编号=>对应的优惠金额
        }

		Session::put('ActivityAdd.all', $data);
        Session::save();
	}

	/**
	 * 保存活动
	 */
	public function save() {
		$args = Input::all();
		$result = $this->requestApi('goods.saveActivity', $args); 
        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }

        //清空Session
        Session::put('ActivityAdd.all', null);
        Session::put('ActivityAdd.checkGoodsIds', null);

        return $this->success(Lang::get('admin.code.98008'), u('ActivityAdd/index'), $result['data']);
	}

	/**
     * 保存已经选择的商品编号数据
     */
    public function saveGoodsIds() {
        $data = Input::all();

        foreach ($data['goodsIds'] as $key => $value) {
            $newData[$value] = $value;
        }
        
        if(empty($data))
        {
            exit;
        }

        $oldData = Session::get('ActivityAdd.checkGoodsIds');

        if(!empty($oldData))
        {
            $allData = $newData + $oldData;
        }
        else
        {
            $allData = $newData;
        }
        
        Session::put('ActivityAdd.checkGoodsIds', $allData);
        Session::save();
    }

    /**
     * 删除已经选择的商品编号
     */
    public function deleteGoodsIds() {
        $args = Input::all();
        $ids = Session::get('ActivityAdd.checkGoodsIds');
        unset($ids[$args['id']]);

        Session::put('ActivityAdd.checkGoodsIds', $ids);
        Session::save();

        return 1;
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
