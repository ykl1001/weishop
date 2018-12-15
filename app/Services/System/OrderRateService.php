<?php namespace YiZan\Services\System;

use YiZan\Models\OrderRate;
use YiZan\Models\Order;
use YiZan\Models\Seller;
use YiZan\Models\SellerStaff;
use YiZan\Models\User;
use YiZan\Models\Goods;
use YiZan\Models\SellerExtend;
use YiZan\Utils\Time;
use YiZan\Utils\String;

use Exception, Lang, Validator, DB;

class OrderRateService extends \YiZan\Services\OrderRateService 
{
    /**
     * 未回复
     */
    const REPLY_STATUS_NO = 1;
    /**
     * 已回复
     */
    const REPLY_STATUS_OK = 2;
    /**
     * 订单不存在
     */
    const ORDER_RATE_NOT_EXIST = 30401;
    /**
     * 回复内容不能为空
     */
    const CONTENT_EMPTY = 30402;
	/**
     * 评价列表
     * @param  string $userMobile 会员名称或手机号
     * @param  int $goodsName 服务名称
     * @param  string $sellerMobile 服务人员手机号
     * @param  string $staffMobile 员工手机号
     * @param  string $orderSn 订单号
     * @param  int $beginTime 开始时间
     * @param  int $endTime 结束时间
     * @param  int $result 评价结果
     * @param  int $replyStatus 回复状态
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          评价列表
     */
	public static function getSystemList($userMobile, $goodsName, $sellerMobile, $staffMobile, $orderSn, $beginTime, $endTime, $result, $replyStatus, $page, $pageSize) 
    {
        $list = OrderRate::orderBy('id', 'desc');
        
        if($userMobile == true)
        {
            $userId = User::where("mobile", $userMobile)->pluck("id");

            if($userId == false) return ["list"=>null, "totalCount"=>0];
            
            $list->where('user_id', $userId);
        }
        
        if($goodsName == true)
        {
            $goodsName = String::strToUnicode($goodsName,'+');
            
            $goods = Goods::whereRaw('MATCH(name_match) AGAINST(\'' . $goodsName . '\' IN BOOLEAN MODE)')->lists("id");
         
            if($goods == false) return ["list"=>null, "totalCount"=>0];
          
            $list->whereIn('goods_id', $goods);
        }
        
        if($sellerMobile == true)
        {
            $sellerId = Seller::where("mobile", $sellerMobile)->pluck("id");

            if($sellerId == false) return ["list"=>null, "totalCount"=>0];
            
            $list->where('seller_id', $sellerId);
        }
        
        if($staffMobile == true)
        {
            $staffId = SellerStaff::where("mobile", $staffMobile)->pluck("id");

            if($staffId == false) return ["list"=>null, "totalCount"=>0];
            
            $list->where('staff_id', $staffId);
        }

        if($orderSn == true)
        {
            $orderId = Order::where("sn", $orderSn)->pluck("id");

            if($orderId == false) return ["list"=>null, "totalCount"=>0];
            
            $list->where('order_id', $orderId);
        }
        
        if($beginTime == true)
        {
            $list->where('create_time', '>=', $beginTime);
        }
        
        if($endTime == true)
        {
            $list->where('create_time', '<=', $endTime);
        }
        
        if($result == true)
        {
            $list->where('result', $result);
        }
        
        if($replyStatus == self::REPLY_STATUS_NO)
        {
            $list->where('reply_time', 0);
        }
        else if($replyStatus == self::REPLY_STATUS_OK)
        {
            $list->where('reply_time', ">", 0);
        }
        
        $totalCount = $list->count();
        
		$list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->with('user','seller', 'order')
            ->get()
            ->toArray();
        
        return ["list"=>$list, "totalCount"=>$totalCount];
    }

    /**
     * 根据编号获取回复信息
     */
    public static function getOrderRateById($orderId){
        $rate = OrderRate::where('order_id', $orderId)
                         ->with('user', 'seller', 'staff', 'order', 'goods')
                         ->get()
                         ->toArray();
        return $rate;
    }

    /**
     * 保存回复信息
     */
     public static function saveOrderRate($data){
        $result = 
        [
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.update_info')
        ];

        DB::beginTransaction();
        try {
            foreach ($data as $key => $value) {
                $orderId = OrderRate::where('id', $value['id'])->pluck('order_id');
                $images = '';

                if (count($value['images']) > 0) {
                    foreach ($value['images'] as $k => $v) {
                        $images[$k] = self::moveOrderImage($orderId, $v);
                        if (!$images[$k]) {
                            $result['code'] = 50004;
                            return $result;
                        }
                    }
                    $images = implode(',', $images);
                }
                
                if($value['goodsStar'] > 0)
                {
                    $update['goods_star'] = $value['goodsStar'];
                }
                
                $update['content'] = $value['content'];
                $update['images'] = $images;
                $update['reply'] = $value['reply'];

                OrderRate::where('id', $value['id'])->update($update);
            }
            DB::commit();
        } catch(Exception $e) {
            $result['code'] = 80104;
            DB::rollback();
        }

        return $result;
    }

    /**
     * 评价回复
     * @param  int $id 回复id
     * @param  string $content 回复内容
     * @return array   回复结果
     */
    public static function replySystem($id, $content)
    {
        $result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.update_info')
		];

        if($content == false)
        {
            $result['code'] = self::CONTENT_EMPTY;
            
	    	return $result;
        }
        
        OrderRate::where('id', $id)->update(array('reply' => $content, 'reply_time'=>Time::getTime()));
        
		return $result;
    }
    /**
     * 删除评价回复
     * @param int  $id 回复id
     * @return array   删除结果
     */
	public static function deleteSystem($ids) 
    {
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.delete_info')
		];

        foreach ($ids as $key => $id) {
            $rate = OrderRate::find($id);
            //更新卖家扩展
            //SellerService::deleteComment($rate->seller_id, $rate->result, $rate->specialty_score, $rate->communicate_score, $rate->punctuality_score);

            SellerStaffService::deleteComment($rate->staff_id, $rate->result, $rate->specialty_score, $rate->communicate_score, $rate->punctuality_score);

            //更新服务扩展
            // GoodsService::deleteComment($rate->goods_id, $rate->result, $rate->specialty_score, $rate->communicate_score, $rate->punctuality_score);
        }

		OrderRate::whereIn('id', $ids)->delete();
        
		return $result;
	}
}
