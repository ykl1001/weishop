<?php 
namespace YiZan\Services\SellerWeb;

use YiZan\Models\Sellerweb\OrderRate;
use YiZan\Models\Order;
use YiZan\Models\Seller;
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
     * @param  int $sellerId 卖家Id 
     * @param  string $userMobile 会员名称或手机号
     * @param  int $goodsName 服务名称
     * @param  string $sellerMobile 服务人员手机号
     * @param  string $orderSn 订单号
     * @param  int $beginTime 开始时间
     * @param  int $endTime 结束时间
     * @param  int $result 评价结果
     * @param  int $replyStatus 回复状态
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          评价列表
     */
	public static function getSystemList($sellerId,$userMobile, $goodsName, $sellerMobile, $orderSn, $beginTime, $endTime, $result, $replyStatus, $page, $pageSize) 
    {
        $list = OrderRate::where('seller_id',$sellerId)->orderBy('id', 'desc');
        
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
        
        if($orderSn == true)
        {
            $orderId = Order::where("sn", 'like', '%'.$orderSn.'%')->lists("id");

            if($orderId == false) return ["list"=>null, "totalCount"=>0];
            
            $list->whereIn('order_id', $orderId);
        }
        
        if(!empty($beginTime))
        {
            $beginTime = Time::toTime($beginTime);
            $list->where('create_time', '>=', $beginTime);
        }

        if(!empty($endTime))
        {
            $endTime = Time::toTime($endTime) + 24 * 60 * 60 - 1;
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
            ->with('user', 'staff', 'order')
            ->get()
            ->toArray();
        
        return ["list"=>$list, "totalCount"=>$totalCount];
    }
    /**
     * 评价回复
     * @param  int $id 回复id
     * @param  string $content 回复内容
     * @return array   回复结果
     */
    public static function replySystem($sellerId, $id, $content)
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
        
        OrderRate::where('id', $id)
                    ->where('seller_id', $sellerId)
                    ->update(array('reply' => $content, 'reply_time'=>Time::getTime()));
        
		return $result;
    }
    /**
     * 删除评价回复
     * @param int  $id 回复id
     * @return array   删除结果
     */
	public static function deleteSystem($id) 
    {
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.delete_info')
		];

        $rate = OrderRate::find($id);
        //更新卖家扩展
        SellerService::deleteComment($rate->seller_id, $rate->result, $rate->specialty_score, $rate->communicate_score, $rate->punctuality_score);

        SellerStaffService::deleteComment($rate->staff_id, $rate->result, $rate->specialty_score, $rate->communicate_score, $rate->punctuality_score);

        //更新服务扩展
        GoodsService::deleteComment($rate->goods_id, $rate->result, $rate->specialty_score, $rate->communicate_score, $rate->punctuality_score);

		OrderRate::where('id', $id)->delete();
        
		return $result;
	}

    /**
     * 获取评价
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public static function getReply($sellerId, $id){
        $data = OrderRate::where('id', $id)
                        ->where('seller_id', $sellerId)
                        ->with('order','user')
                        ->first();    
        return $data ? $data->toArray() : [];
    }

    /**
     * [orderlists 全国店评价列表]
     * @param  [type] $sellerId [description]
     * @param  [type] $page     [description]
     * @param  [type] $pageSize [description]
     * @return [type]           [description]
     */
    public static function orderlists($sellerId, $star, $sn, $beginTime, $endTime, $page, $pageSize) {
        $list = OrderRate::where('seller_id',$sellerId)->where('is_all', 1)->orderBy('id', 'desc')->groupBy('order_id');

        if($star > 0)
        {
            $list->where('star', $star);
        }
        if($sn)
        {

            $orderId = Order::where('sn', $sn)->pluck('id');
            $list->where('order_id', $orderId);
        }
        if($beginTime && $endTime)
        {

            $list->where('create_time', '>=', $beginTime)->where('create_time', '<=', $endTime);
        }

        $totalCount = $list->count();
        
        $list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->with('user','order')
            ->get()
            ->toArray();
        
        return ["list"=>$list, "totalCount"=>$totalCount];
    }

    /**
     * [alldetail 全国店订单评价详情]
     * @param  [type] $sellerId [description]
     * @param  [type] $orderId  [description]
     * @return [type]           [description]
     */
    public static function alldetail($sellerId, $orderId) {
        $lists = OrderRate::where('seller_id',$sellerId)->where('order_id', $orderId)->where('is_all', 1)->with('user','order','goods')->get()->toArray();
        return $lists;
    }

    /**
     * [alldetail 商家回复全国店订单]
     * @param  [type] $sellerId [description]
     * @param  [type] $data     [description]
     * @return [type]           [description]
     */
    public static function allreply($sellerId, $data) {

        $result = array(
            'code'  => 0,
            'data'  => '',
            'msg'   => Lang::get('api_sellerweb.code.30503')
        );

        try {
            foreach ($data as $key => $value) {
                $update = [
                    'reply' => $value['content'],
                    'reply_time'=>Time::getTime()
                ];

                $res = OrderRate::where('seller_id', $sellerId)->where('id', $value['id'])->update($update);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }

       return $result;
    }


}
