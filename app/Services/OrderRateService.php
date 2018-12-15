<?php namespace YiZan\Services;

use YiZan\Models\OrderRate;
use YiZan\Models\Order;
use YiZan\Models\OrderGoods;
use YiZan\Models\GoodsExtend;

use YiZan\Utils\String;
use YiZan\Utils\Time;
use Exception, Lang, Validator, DB;

class OrderRateService extends BaseService {
    
    /**
     * [createRate 周边店评价]
     * @param  [type] $userId  [description]
     * @param  [type] $orderId [description]
     * @param  [type] $images  [description]
     * @param  [type] $content [description]
     * @param  [type] $star    [description]
     * @param  [type] $isAno   [description]
     * @return [type]          [description]
     */
	public static function createRate($userId, $orderId,  $images, $content, $star, $isAno) {
	    
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.create_order_rate')
		);

		$rules = array(
		    'orderId' => ['required'],
		    'content' => ['required'],
		    'star' => ['required']
		);

		$messages = array(
		    'orderId.required'	=> '50007',
		    'content.required'	=> '50003',
		    'star.required'	    => '50006'
		);

		$validator = Validator::make([
				'orderId' => $orderId,
				'content' => $content,
				'star' => $star
			], $rules, $messages);
		if ($validator->fails()) {//验证信息
	    	$messages = $validator->messages();
	    	$result['code'] = $messages->first();
	    	return $result;
	    }

        //评价星级不在1-5之间
        if ($star < 1 || $star > 5) {
            $result['code'] = '50010';
            return $result;
        }

        $order = Order::where('id', $orderId)->where('user_id', $userId)->first();
        if (!$order) {//没有订单
            $result['code'] = 50001;
            return $result;
        }

        if ($order->status <> ORDER_STATUS_FINISH_SYSTEM && $order->status <> ORDER_STATUS_FINISH_USER) {//未确认,不能评价
            $result['code'] = 50002;
            return $result;
        }
        //订单已评价
        if ($order->isRate) {
            $result['code'] = 50011;
            return $result;
        }


        if (count($images) > 0) {
            foreach ($images as $key => $image) {
                $images[$key] = self::moveOrderImage($orderId, $image);
                if (!$images[$key]) {
                    $result['code'] = 50004;
                    return $result;
                }
            }
            $images = implode(',', $images);
        } else {
            $images = '';
        }

        DB::beginTransaction();
        try{
            $orderRate = new OrderRate;
            $orderRate->order_id 			= $order->id;
            $orderRate->seller_id 			= $order->seller_id;
            $orderRate->staff_id 		    = (int)$order->seller_staff_id;
            $orderRate->user_id 			= $order->user_id;
            $orderRate->content 			= $content;
            $orderRate->images 				= $images;
            $orderRate->create_time			= UTC_TIME;
            $orderRate->star                = $star;
            $orderRate->is_ano              = $isAno;   //2系统自评 1 匿名 0不匿名
            if ($orderRate->star == 5) {//好评
                $orderRate->result = 'good';
            }elseif ($orderRate->star == 1) {//差评
            	$orderRate->result = 'bad';
            }else{//中评
                $orderRate->result = 'neutral';
            }
            $orderRate->save();

            //更新卖家扩展
		    SellerService::incrementExtend($order->seller_id, 'comment_'.$orderRate->result.'_count', 1);

            //更新员工扩展
            //SellerStaffService::updateComment($order->seller_staff_id, $orderRate->result, 0, 0, 0);

            //更新服务扩展
            //GoodsService::updateComment($order->goods_id, $orderRate->result, 0, 0, 0);

            //更新订单为已评
            $order->is_rate = 1;
            $order->save();


            //积分活动
            if ($order->pay_status == 1 && $order->pay_type != 'cashOnDelivery' && $order->pay_fee > 0.001) {
                \YiZan\Services\UserIntegralService::createIntegralLog($userId, 1, 3, $orderId, $order->pay_fee);
            }

            DB::commit();

            $result['data'] = OrderService::getOrderById($userId, $orderId);
        } catch (Exception $e) {
            throw $e;

            DB::rollback();
            $result['code'] = 50005;
        }
	    return $result;
	}

    /**
     * [createRateAll 全国店评价]
     * @param  [type] $userId   [description]
     * @param  [type] $isAll    [description]
     * @param  [type] $orderId  [description]
     * @param  [type] $shopStar [description]
     * @param  [type] $comment  [description]
     * @param  [type] $isAno    [description]
     * @return [type]           [description]
     */
    public static function createRateAll($userId, $isAll, $orderId, $shopStar, $comment, $isAno) {

        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.create_order_rate')
        );

        $rules = array(
            'orderId' => ['required'],
            'shopStar' => ['required']
        );

        $messages = array(
            'orderId.required'  => '50007',
            'shopStar.required'     => '50006'
        );

        $validator = Validator::make([
                'orderId' => $orderId,
                'shopStar' => $shopStar
            ], $rules, $messages);

        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        //评价星级不在1-5之间
        if ($shopStar < 1 || $shopStar > 5) {
            $result['code'] = '50010';
            return $result;
        }

        $order = Order::where('id', $orderId)->where('user_id', $userId)->first();
        //没有订单
        if (!$order) {
            $result['code'] = 50001;
            return $result;
        }

        //订单不是全国店
        if($order->is_all != $isAll)
        {
            $result['code'] = 50012;
            return $result;
        }

        //未确认,不能评价
        if ($order->status <> ORDER_STATUS_FINISH_SYSTEM && $order->status <> ORDER_STATUS_FINISH_USER) {
            $result['code'] = 50002;
            return $result;
        }

        //订单已评价
        if ($order->isRate) {
            $result['code'] = 50011;
            return $result;
        }

        DB::beginTransaction();
        try{
            foreach ($comment as $key => $value) {
                //图片
                if (count($value['images']) > 0) {
                    foreach ($value['images'] as $key => $image) {
                        $value['images'][$key] = self::moveOrderImage($orderId, $image);
                        if (!$value['images'][$key]) {
                            $result['code'] = 50004;
                            return $result;
                        }
                    }
                    $images = implode(',', $value['images']);
                } else {
                    $images = '';
                }

                $orderRate = new OrderRate;
                $orderRate->order_id            = $order->id;
                $orderRate->seller_id           = $order->seller_id;
                $orderRate->staff_id            = (int)$order->seller_staff_id;
                $orderRate->user_id             = $order->user_id;
                $orderRate->content             = $value['content'];
                $orderRate->images              = $images;
                $orderRate->create_time         = UTC_TIME;
                $orderRate->star                = $shopStar;    //店铺评分
                $orderRate->is_ano              = $isAno;    //2系统自评 1 匿名 0不匿名
                $orderRate->is_all              = 1;
                $orderRate->goods_id            = $value['id'];
                $orderRate->goods_star          = $value['star'];   //商品评分

                if ($orderRate->star == 5)
                {
                    $orderRate->result = 'good';    //好评
                }elseif ($orderRate->star == 1)
                {
                    $orderRate->result = 'bad'; //差评
                }else
                {
                    $orderRate->result = 'neutral'; //中评
                }
                
                $orderRate->save();

                //更新卖家扩展
                SellerService::incrementExtend($order->seller_id, 'comment_'.$orderRate->result.'_count', 1);

                //更新商品扩展
                GoodsService::incrementExtend($orderRate->goods_id, 'comment_'.$orderRate->result.'_count', 1);
                
            }

            //更新订单为已评
            $order->is_rate = 1;
            $order->save();

            //积分活动
            if ($order->pay_status == 1 && $order->pay_type != 'cashOnDelivery' && $order->pay_fee > 0.001) {
                \YiZan\Services\UserIntegralService::createIntegralLog($userId, 1, 3, $orderId, $order->pay_fee);
            }

            DB::commit();

            $result['data'] = OrderService::getOrderById($userId, $orderId);
        } catch (Exception $e) {
            throw $e;

            DB::rollback();
            $result['code'] = 50005;
        }
        return $result;
    }

    /**
     * 获取评价列表
     * @param $sellerId
     * @param $type 1:好评 2: 中评 3:差评
     * @param $page
     * @return array
     */
	public static function getList($sellerId, $type, $page) {
	    $list = OrderRate::where('seller_id', $sellerId);
        switch($type){
            case '1' : $list->where('result', 'good'); break;
            case '2' : $list->where('result', 'neutral'); break;
            case '3' : $list->where('result', 'bad'); break;
        }
	    $list = $list->orderBy('id', 'desc')->with('user')->skip(($page - 1) * 20)->take(20)->get()->toArray();
	    foreach ($list as $key => $value) {
	    	$list[$key] = $value;
	    	$list[$key]['userName'] = $value['user']['name'];
            $list[$key]['avatar'] = $value['user']['avatar'];
	    	$list[$key]['replyTime'] = Time::toDate($value['replyTime'],'Y-m-d');
	    	$list[$key]['createTime'] = Time::toDate($value['createTime'],'Y-m-d');
            if ($value['isAno'] == 1) {
                $firstStr = String::msubstr($value['user']['name'], 0, 1, 'utf-8',false);
                $lastStr = String::msubstr($value['user']['name'], -1, 1, 'utf-8',false);
                $list[$key]['userName'] = $firstStr.'***'.$lastStr;
            }
            unset($list[$key]['user']);
	    }
	    return $list;
	}

    /**
     * 评价统计
     * @param $sellerId
     */
    public static function getCount($sellerId) {
        $data = [
            'star' => 5,
            'totalCount' => 0,
            'goodCount' => 0,
            'neutralCount' => 0,
            'badCount' => 0
        ];
        $star =OrderRate::where('seller_id', $sellerId)->selectRaw('ROUND(SUM(star)/COUNT(id),1) as score')->pluck('score');
        $data['star'] = (double)$star > 0 ? $star : 5;
        $data['totalCount'] = OrderRate::where('seller_id', $sellerId)->count();
        $data['goodCount'] = OrderRate::where('seller_id', $sellerId)->where('result', 'good')->count();
        $data['neutralCount'] = OrderRate::where('seller_id', $sellerId)->where('result', 'neutral')->count();
        $data['badCount'] = OrderRate::where('seller_id', $sellerId)->where('result', 'bad')->count();
        return  $data;
    }
    /**
     * 获取评价列表
     * @param $sellerId
     * @param $type 1:好评 2: 中评 3:差评
     * @param $page
     * @return array
     */
    public static function getUserList($userId,  $page) {
        $list = OrderRate::where('user_id', $userId);
        $list = $list->orderBy('id', 'desc')->with('goods', 'seller')->skip(($page - 1) * 20)->take(20)->get()->toArray();
        foreach ($list as $key => $value) {
            $list[$key] = $value;
            $list[$key]['sellerName'] = $value['seller']['name'];
            $list[$key]['sellerId'] = $value['seller']['id'];
            $list[$key]['avatar'] = $value['seller']['avatar'];
            $list[$key]['isAll'] = $value['isAll'];
            $list[$key]['replyTime'] = Time::toDate($value['replyTime'],'Y-m-d');
            $list[$key]['createTime'] = Time::toDate($value['createTime'],'Y-m-d');

            $list[$key]['goods'] = $value['goods'];
            unset($list[$key]['seller']);
        }
        return $list;
    }

    /**
     * [goodsStatistics 商品总体评分]
     * @param  [type] $goodsId [description]
     * @return [type]          [description]
     */
    public static function goodsStatistics($goodsId) {
        $data = [
            'star' => 5,
            'totalCount' => 0,
            'goodCount' => 0,
            'neutralCount' => 0,
            'badCount' => 0
        ];
        $star =OrderRate::where('goods_id', $goodsId)->selectRaw('ROUND(SUM(star)/COUNT(id),1) as score')->pluck('score');
        $data['star'] = (double)$star > 0 ? $star : 5;
        $data['totalCount'] = OrderRate::where('goods_id', $goodsId)->count();
        $data['goodCount'] = OrderRate::where('goods_id', $goodsId)->where('result', 'good')->count();
        $data['neutralCount'] = OrderRate::where('goods_id', $goodsId)->where('result', 'neutral')->count();
        $data['badCount'] = OrderRate::where('goods_id', $goodsId)->where('result', 'bad')->count();
        return  $data;
    }

    /**
     * [goodsRates 商品评价列表]
     * @param  [type] $goodsId [description]
     * @param  [type] $type    [description]
     * @param  [type] $page    [description]
     * @return [type]          [description]
     */
    public static function goodsRates($goodsId, $type, $page) {
        $list = OrderRate::where('goods_id', $goodsId);
        switch($type){
            case '1' : $list->where('result', 'good'); break;
            case '2' : $list->where('result', 'neutral'); break;
            case '3' : $list->where('result', 'bad'); break;
        }
        $list = $list->orderBy('id', 'desc')->with('user')->skip(($page - 1) * 20)->take(20)->get()->toArray();
        foreach ($list as $key => $value) {
            $list[$key] = $value;
            $list[$key]['userName'] = $value['user']['name'];
            $list[$key]['avatar'] = $value['user']['avatar'];
            $list[$key]['replyTime'] = Time::toDate($value['replyTime'],'Y-m-d');
            $list[$key]['createTime'] = Time::toDate($value['createTime'],'Y-m-d');
            if ($value['isAno'] == 1) {
                $firstStr = String::msubstr($value['user']['name'], 0, 1, 'utf-8',false);
                $lastStr = String::msubstr($value['user']['name'], -1, 1, 'utf-8',false);
                $list[$key]['userName'] = $firstStr.'***'.$lastStr;
            }
            unset($list[$key]['user']);
        }
        return $list;
    }

}
