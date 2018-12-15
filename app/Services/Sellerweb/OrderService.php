<?php 
namespace YiZan\Services\Sellerweb;

use YiZan\Models\Sellerweb\Order;
use YiZan\Models\InvitationBackLog;
use YiZan\Services\SystemConfigService;
use YiZan\Models\User;
use YiZan\Models\Seller;
use YiZan\Models\Goods;
use YiZan\Models\PromotionSn;
use YiZan\Models\UserPayLog;
use YiZan\Models\OrderPromotion;
use YiZan\Models\SellerStaff;
use YiZan\Models\StaffLeave;
use YiZan\Models\SellerExtend;
use YiZan\Services\System\PushMessageService;
use YiZan\Services\PromotionService;
use YiZan\Services\SystemConfigService as baseSystemConfigService;
use YiZan\Services\SellerMoneyLogService;
use YiZan\Models\SellerMoneyLog;

use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Utils\String;
use Exception, DB, Lang, Validator, App;

class OrderService extends \YiZan\Services\OrderService 
{
    /**
     * 订单不存在
     */
    const ORDER_NOT_EXIST = 50101;
    /**
     * 订单状态错误
     */
    const ORDER_STATUS_ERROR = 50103;
    /**
     * 该订单不允许删除
     */
    const ORDER_NOT_DELETE = 50104;
	/**
     * 订单列表
     * @param  int $sellerId 卖家
     * @param  int $status 订单状态
     * @param  int $page 页码
     * @return array          订单列表
	 */
	public static function getSellerList($sellerId, $sn, $orderType,$provinceId, $cityId, $areaId, $status, $beginTime, $endTime,$mobile,$staffName, $name, $payTypeStatus=0, $page, $pageSize = 20)
    {
        self::endOrder();
        $list = Order::orderBy('id', 'desc');
        $list->where("seller_id",$sellerId)->with('staff');
        if(!empty($orderType) > 0 ){
            $list->where('order_type',$orderType);
        }
        if(!empty($sn)){
            $list->where('sn',$sn);
        }
        if($beginTime == true)
        {
            $list->where('create_time', '>=', $beginTime);
        }
        
        if($endTime == true)
        {
            $list->where('create_time', '<=', $endTime);
        }
        if($mobile != '')
        {
//             $list->where('mobile', $mobile);
            $list->where('mobile', 'like', '%'.$mobile.'%');
        }

        if ($name != '') {
            $list->where('name', 'like', '%'.$name.'%');
        }

        switch ($payTypeStatus) {
            case 1:
                $list->where('pay_status', 1);   //在线支付
                break;

            case 2:
                $list->where('pay_type', 'cashOnDelivery');   //货到付款
                break;

            case 3:
                $list->where('pay_status', 0)->where('pay_type', '<>', 'cashOnDelivery');   //未支付
                break;

            default:
                # code...
                break;
        }

        if(STORE_TYPE == 1){
            $list->with('user','orderTrack');
            if ($staffName != '') {
                $list->whereIn('id', function($query) use ($staffName){
                    $query->select('order_id')
                        ->from('order_track')
                        ->where('express_number', 'like', '%'.$staffName.'%');
                });
            }
            //新订单状态
            $newStatus = [
                ORDER_STATUS_BEGIN_USER,
                ORDER_STATUS_PAY_SUCCESS,
                ORDER_STATUS_PAY_DELIVERY
            ];
            //配送/服务中的订单状态
            $ingStatus = [
                ORDER_STATUS_START_SERVICE,
                ORDER_STATUS_PAY_SUCCESS,
                ORDER_STATUS_PAY_DELIVERY,
                ORDER_STATUS_AFFIRM_SELLER
            ];

            $paymentwhere = [ORDER_STATUS_BEGIN_USER]; //待支付
            $shippedwhere = [ORDER_STATUS_PAY_SUCCESS,ORDER_STATUS_PAY_DELIVERY]; //待发货
            $ratewhere = [ORDER_STATUS_FINISH_SYSTEM,ORDER_STATUS_FINISH_USER,ORDER_STATUS_REFUND_SUCCESS,ORDER_REFUND_ADMIN_AGREE]; //待评价
            $refundwhere = [
//			ORDER_STATUS_CANCEL_ADMIN, //拒绝订单
//			ORDER_STATUS_CANCEL_SELLER, //商家拒绝
                ORDER_STATUS_REFUND_AUDITING,
                ORDER_STATUS_CANCEL_REFUNDING,
                ORDER_STATUS_REFUND_HANDLE,
//			ORDER_STATUS_REFUND_FAIL,    //退款失败
//			ORDER_STATUS_REFUND_SUCCESS, //退款中
                ORDER_REFUND_SELLER_AGREE,
//			ORDER_REFUND_SELLER_REFUSE,//商家拒绝
                ORDER_REFUND_ADMIN_AGREE,
//            ORDER_REFUND_ADMIN_REFUSE, //后台拒绝
                ORDER_REFUND_USER_REFUSE_LOGISTICS,
                ORDER_REFUND_SELLER_REFUSE_LOGISTICS
            ];
            $affirm = [ORDER_STATUS_AFFIRM_SELLER];//已发货
            $cancel = [ORDER_STATUS_CANCEL_USER,ORDER_STATUS_CANCEL_AUTO,ORDER_STATUS_CANCEL_SELLER,ORDER_STATUS_CANCEL_ADMIN,ORDER_STATUS_USER_DELETE,ORDER_STATUS_SELLER_DELETE,ORDER_STATUS_ADMIN_DELETE];//已关闭


            $ingObj = clone $list;
            $data['ingCount'] = $ingObj->whereIn('status', $ingStatus)->count(); //进行中...

            $rate = clone $list;
            $data['rate'] = $rate->whereIn('status',$ratewhere)->count(); //待评价

            $payment = clone $list;
            $data['payment'] = $payment->whereIn('status',$paymentwhere)->where('pay_status', 0)->count();//待付款


            $shipped = clone $list;
            $data['shipped'] = $shipped->whereIn('status', $shippedwhere)->count();//待发货

            $refund = clone $list;
            $data['refund'] = $refund->whereIn('status', $refundwhere)->count();//退款

            $affirmCont = clone $list;
            $data['affirmCont'] = $affirmCont->whereIn('status', $affirm)->count(); //已发货

            $cancelCount = clone $list;
            $data['cancelCount'] = $cancelCount->whereIn('status', $cancel)->count();//已关闭
            if ($status == 1) {
                $list->whereIn('status', $ingStatus);
            } elseif ($status == 2) {
                $list->whereIn('status', $newStatus);
                $data['count'] = $list->whereIn('status', $newStatus)->count();
            }  elseif ($status == 3) {  //待付款
                $list->whereIn('status',$paymentwhere)->where('pay_status', 0);
            }elseif ($status == 4) {  //待发货
                $list->whereIn('status', $shippedwhere);
            }elseif ($status == 5) {  //退款中
                $list->whereIn('status', $refundwhere);
            }else if ($status == 6) { //待评价
                $list->whereIn('status',$ratewhere);
            } else if ($status == 7) { //已发货
                $list->whereIn('status',$affirm);
            } else if ($status == 8) { //关闭
                $list->whereIn('status',$cancel);
            } else {
                $list->whereNotIn('status', [
                    ORDER_STATUS_SELLER_DELETE,
                    ORDER_STATUS_ADMIN_DELETE
                ]);
            }
        }else{

            $list->where("status",'<>',ORDER_STATUS_SELLER_DELETE);
            $list->where("status",'<>',ORDER_STATUS_ADMIN_DELETE);
            if ($staffName != '') {
                $staffName = empty($staffName) ? '' : String::strToUnicode($staffName,'+');
                $list->whereIn('seller_staff_id', function($query) use ($staffName){
                    $query->select('id')
                        ->from('seller_staff')
                        ->whereRaw("MATCH(name_match) AGAINST('{$staffName}' IN BOOLEAN MODE)");
                });
            }
            switch ($status) {
                case '1': //待发货  102 110
                    $list->whereIn('status',
                    [
                        ORDER_STATUS_BEGIN_USER,
                        ORDER_STATUS_PAY_SUCCESS,
                        ORDER_STATUS_PAY_DELIVERY,
                        ORDER_STATUS_AFFIRM_SELLER
                    ]);
                    break;
                case '2'://待完成 106 107
                    $list->whereIn('status',
                    [
                        ORDER_STATUS_START_SERVICE,
                        ORDER_STATUS_FINISH_STAFF
                    ]) ;
                    break;
                case '3':// 已完成
                   $list->whereIn('status',
                    [
                       ORDER_STATUS_FINISH_SYSTEM,
                       ORDER_STATUS_FINISH_USER,
                       ORDER_STATUS_USER_DELETE
                    ])->where(function($query){
                       $query->where('buyer_finish_time', '>', 0)
                           ->orWhereBetween('auto_finish_time', [1,UTC_TIME]);
                    });
                    break;
               case '4'://拒绝 301 302 303 400 401 402 403 404 311 312
                        $list->whereIn('status',
                        [
                            ORDER_STATUS_CANCEL_USER,
                            ORDER_STATUS_CANCEL_AUTO,
                            ORDER_STATUS_CANCEL_SELLER,
                            ORDER_STATUS_CANCEL_ADMIN
                        ]) ;
                        break;
               case '5'://退款
                    $list->whereIn('status',
                        [
                            ORDER_STATUS_REFUND_AUDITING,
                            ORDER_STATUS_REFUND_HANDLE,
                            ORDER_STATUS_REFUND_FAIL,
                            ORDER_STATUS_REFUND_SUCCESS
                        ]) ;
                    break;
                default:
                    break;
            }
        }
        $result['orderStatus'] = $data;
        $result['totalCount'] = $list->count();  
        $result['list'] = $list->skip(($page - 1) * $pageSize)
                ->take($pageSize)
                ->with('orderGoods')
                ->get()
                ->toArray();
		return $result;
    }
    /**
     * 订单列表
     * @param  int $staffId 员工
     * @param  int $status 订单状态
     * @param  int $page 页码
     * @return array          订单列表
     */
    public static function getCarList($staffId, $status, $page, $pageSize = 20)
    {
        $list = Order::orderBy('id', 'desc');
        
        $list->where('seller_staff_id', $staffId);

        return $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->with('seller','OrderGoods.goods', 'user.restaurant')
            ->get()
            ->toArray();
    }
    /**
     * 获取订单
     * @param  int $id 订单id
     * @return array   订单
     */
	public static function getSellerOrderById($id)
    {
        self::endOrder();
		$goods = Order::where('id', $id)->with('OrderGoods','user','staff')->first();
		return $goods;
	}


    /**
     * 获取打印订单
     * @param  int $sellerId 商家编号
     * @param  int $id 订单id
     * @return array   订单
     */
    public static function printer($sellerId, $id)
    {
        self::endOrder();
        $goods = Order::where('id', $id)
            ->where('seller_id', $sellerId)
            ->with('OrderGoods.goods')
            ->first()->toArray();
        return $goods;
    }

    /**
     * 获取订单
     * @param  int $sellerId 商家编号
     * @param  int $id 订单id
     * @return array   订单
     */
    public static function getSellerOrderDetail($sellerId, $id)
    {
        self::endOrder();
        $goods = Order::where('id', $id)
            ->where('seller_id', $sellerId)
            ->with('orderGoods','user','staff','refund','seller')
            ->first();
        if (!empty($goods)) {
            $goods =   $goods->toArray();
            $goods['staffList'] = [];
            $staff = SellerStaff::where('seller_id', $sellerId)
                ->whereIn('type', [0, 3, $goods['orderType']])
                ->where('status', 1)
                ->whereNotIn('id', function($query) use ($sellerId){
                    $query->select('staff_id')
                        ->from('staff_leave')
                        ->where('begin_time', '<=', UTC_TIME)
                        ->where('end_time', '>=', UTC_TIME)
                        ->where('is_agree', 1)
                        ->where('status', 1);
                });
            if ($goods['orderType'] == 2) {
                $goodsId = $goods['OrderGoods'][0]['goodsId'];
                $staff->whereIn('id', function($query) use ($goodsId){
                    $query->select('staff_id')
                        ->from('goods_staff')
                        ->where('goods_id', $goodsId);
                });
            }
            $staff = $staff->get();
            if ($staff) {
                $goods['staffList'] = $staff->toArray();
            }
        }
        return $goods;
    }
	/**
	 * 获取餐厅订单的详情
	 * @param  int $id 订单id
	 * @return array   订单
	 */
	public static function getRestaurantOrderById($restaurantId,$id)
	{
	    $order = Order::where('id', $id)->where("restaurant_id",$restaurantId)->first();
	    if($order->order_type >= 3){
	        $goods =  Order::where('id', $id)->where("restaurant_id",$restaurantId)->with('goods.extend','user','restaurant')->first();
	    }else{
	        $goods = Order::where('id', $id)->where("restaurant_id",$restaurantId)->with('OrderGoods.goods','user','restaurant')->first();
	    }
	    return $goods;
	}
	
	/**
	 * 更新订单
	 * @param  int $id 订单id
	 * @param  int $status 状态
	 * @param  string $content 处理结果
	 * @return array   更新结果
	 */
	public static function updateSellerOrder($id,$sellerId, $status,$refuseContent)
	{
	    $result =
	    [
	        'code'	=> 0,
	        'data'	=> null,
	        'msg'	=> Lang::get('api.success.update_info')
	    ];
	    if(
	        $status == ORDER_STATUS_AFFIRM_SELLER  &&
	        $status == ORDER_STATUS_CANCEL_SELLER  &&
	        $status == ORDER_REFUND_SELLER_REFUSE  &&
	        $status == ORDER_REFUND_SELLER_AGREE
	    )
	    {
	        $result['code'] = self::ORDER_STATUS_ERROR;
	         
	        return $result;
	    }
	    $order = Order::where('id', $id)->where('seller_id',$sellerId)->first();
	   
	    //没有订单
	    if ($order == false)
	    {
	        $result['code'] = self::ORDER_NOT_EXIST;
	
	        return $result;
	    }

	    if($order->isCanRefundSeller)
	    {
	        $result['code'] = self::ORDER_STATUS_ERROR;
	
	        return $result;
	    }

        if($order->status == ORDER_REFUND_SELLER_AGREE || $order->status == ORDER_REFUND_SELLER_REFUSE )
	    {
	        $result['code'] = self::ORDER_STATUS_ERROR;
	    
	        return $result;
	    }

	    if($status == ORDER_STATUS_CANCEL_SELLER)
	    {	      
	        if($refuseContent == ""){
	            $result['code'] = 50201; 
	            return $result;
	        }else{  
	            if($order->pay_fee != 0 && $order->isCashOnDelivery() === false && $order->pay_status == ORDER_PAY_STATUS_YES){
	                $order->status = ORDER_STATUS_CANCEL_SELLER;
	                $order->refund_time = UTC_TIME;
                    $order->cancel_time = UTC_TIME;
                    $order->cancel_remark = $refuseContent;
                }else{
    	            $order->status = ORDER_STATUS_CANCEL_SELLER;
    	            $order->cancel_time = UTC_TIME;
        	        $order->cancel_remark = $refuseContent;	                
	            }
                //是否是返现订单
                if ($order->is_invitation) {
                    InvitationBackLog::where('order_id',$order->id )->where('user_id',$order->user_id)->update([
                        'is_refund' => 1,
                        'update_time' => UTC_TIME
                    ]);
                }
	        }
	    }
	    else if($status == ORDER_STATUS_AFFIRM_SELLER)
	    {
            if($order->status == ORDER_STATUS_CALL_SYSTEM_SEND){
                $order->status = ORDER_STATUS_AFFIRM_SELLER;
                $order->send_fee = 0;
                $order->send_staff_fee = 0;
                $order->send_system_fee = 0;
                $order->is_cancel_call = 1;
            }else{
                $order->status = ORDER_STATUS_AFFIRM_SELLER;
                $order->seller_confirm_time = UTC_TIME;

                //全国店，发货时候写入自动完成时间参数
                if($order->is_all == 1)
                {
                    $autoFinishDay = baseSystemConfigService::getConfigByCode('system_buyer_order_confirm_all');
                    $autoFinishTime = $autoFinishDay * 24 * 3600 + UTC_TIME;
                    $order->auto_finish_time = $autoFinishTime;
                }else{					
                    //cz周边店
                    $send_type = Seller::where('id',$order->seller_id)->pluck('send_type');
                    if(($send_type == 1 && !$order->IsCashOnDelivery) && $order->order_type == 1 ){
                        $order->status = ORDER_STATUS_SYSTEM_SEND;
                        //看看自动分配是否有人
                        if($order->order_type == 2 || ($order->send_way == 1 && $order->order_type == 1))
                        {
                            $newstaffId = OrderService::autopei($order->id);
                            if($newstaffId > 0){
                                $order->status = ORDER_STATUS_GET_SYSTEM_SEND;
                                $order->seller_staff_id = $newstaffId;
                            }else{
                                $result['code'] = 60808;
                                return $result;
                            }
                        }
                    }
                }
            }
	    }
	    else if($status == ORDER_REFUND_SELLER_AGREE)
	    {
	        $order->status = ORDER_REFUND_SELLER_AGREE;
	        $order->dispose_refund_seller_time = UTC_TIME;
	    }
	    else if($status == ORDER_REFUND_SELLER_REFUSE)
	    {
	        $order->status = ORDER_REFUND_SELLER_REFUSE;
	        $order->dispose_refund_seller_time = UTC_TIME;
    	    $order->dispose_refund_seller_remark = $refuseContent;
	    }
        else if($status == ORDER_STATUS_START_SERVICE)
        {
            $order->status = ORDER_STATUS_START_SERVICE;
        }
        else if($status == ORDER_STATUS_CANCEL_USER)
        {
            $order->status = ORDER_STATUS_CANCEL_USER;
        }
        else if($status == ORDER_STATUS_FINISH_STAFF){
            $order->status = ORDER_STATUS_FINISH_STAFF; 
            $order->staff_finish_time = UTC_TIME;

            //周边店，服务人员确认完成写入自动完成时间参数
            if($order->is_all == 0)
            {
                $autoFinishDay = baseSystemConfigService::getConfigByCode('system_buyer_order_confirm');
                $autoFinishTime = $autoFinishDay * 24 * 3600 + UTC_TIME;
                $order->auto_finish_time = $autoFinishTime;
            }

            //（完成订单时处理）如果是到店，则更新消费码
            if( in_array($order->send_way, [2,3]) )
            {
                $order->auth_code_use = -1;
            }
        }else if($status == ORDER_STATUS_CALL_SYSTEM_SEND){
            //当呼叫配送的时候
            //服务配送费 服务人员得的服务配送费 平台得的服务配送费
            $sendcenter = SystemConfigService::getConfigByGroup('sendcenter');
            $send_fee = $sendcenter['system_send_staff_fee'];
            $send_staff_fee = $sendcenter['system_send_staff_fee']-$sendcenter['system_send_fee'];
            $send_system_fee = $sendcenter['system_send_fee'];

            //看看自动分配是否有人
            $newstaffId = OrderService::autopei($id);
            if($newstaffId > 0){
                $order->status = ORDER_STATUS_GET_SYSTEM_SEND;
                $order->seller_staff_id = $newstaffId;
            }else{
                $order->status = ORDER_STATUS_CALL_SYSTEM_SEND;
            }

            $order->send_fee = $send_fee;
            $order->send_staff_fee = $send_staff_fee;
            $order->send_system_fee = $send_system_fee;
        }
	    else{
	        $result['code'] = self::ORDER_NOT_EXIST;	        
	        return $result;
	    }

        //（接单时处理）判断是否是到店
        if($status == ORDER_STATUS_AFFIRM_SELLER && in_array($order->send_way, [2,3])) {
            $order->auth_code = Helper::getCode(1,12);
            $order->auth_code_use = 1; //-1：已使用  1：未使用
        }

        DB::beginTransaction();
	    try {
            if($status == ORDER_STATUS_CANCEL_SELLER) {
                Seller::where('id', $order->seller_id)->increment('seller_cancel');
            }

        	$order->save();

        	$data = self::getSellerOrderById($id)->toArray();
            if($status == ORDER_STATUS_CANCEL_USER) {
                parent::userR($order);
            }else{

                if ($status == ORDER_STATUS_CANCEL_SELLER) {
                    self::cancelOrderStock($id);

                    if ((int)$data['promotionSnId'] > 0) {
                        PromotionSn::where('id', $data['promotionSnId'])->update(['use_time'=>0]);
                    }

                    //退还积分
                    if ((int)$data['integral'] > 0) {
                        \YiZan\Services\UserIntegralService::createIntegralLog($data['userId'], 1, 7, $id, 0, $data['integral']);
                    }

                    //如果是货到付款则退还商家支付的抽成金额
                    if($data['isCashOnDelivery'] === true){
                        $sellerMoneyLog = SellerMoneyLog::where('related_id', $id)
                            ->where('type', SellerMoneyLog::TYPE_DELIVERY_MONEY)
                            ->first();
                        if($sellerMoneyLog){
                            //增加商家金额
                            SellerExtend::where('seller_id', $order->seller_id)
                                ->increment('money', abs($sellerMoneyLog->money));
                            //写入增加金额日志
                            SellerMoneyLogService::createLog(
                                $order->seller_id,
                                SellerMoneyLog::TYPE_DELIVERY_MONEY,
                                $id,
                                $order->drawn_fee,
                                '现金支付订单' . $order->sn . '取消，佣金返还',
                                1
                            );
                        }
                    }
                }


                $isRefundBalance = \YiZan\Models\SystemConfig::where('code', 'is_refund_balance')->pluck('val');

                //如果未支付订单 包含余额支付金额 则退款
                if($status == ORDER_STATUS_CANCEL_SELLER  && $order->pay_status == ORDER_PAY_STATUS_NO && $order->pay_money > 0.0001){
                    //返还支付金额给会员余额
                    $user = User::find($order->user_id);
                    $user->balance = $user->balance + abs($order->pay_money);
                    $user->save();
                    //创建退款日志
                    $userPayLog = new UserPayLog;
                    $userPayLog->payment_type   = 'balancePay';
                    $userPayLog->pay_type       = 3;//退款
                    $userPayLog->user_id        = $order->user_id;
                    $userPayLog->order_id       = $order->id;
                    $userPayLog->activity_id    = $order->activity_id > 0 ? $order->activity_id : 0;
                    $userPayLog->seller_id      = $order->seller_id;
                    $userPayLog->money          = $order->pay_money;
                    $userPayLog->balance        = $user->balance;
                    $userPayLog->content        = '商家取消订单退款';
                    $userPayLog->create_time    = UTC_TIME;
                    $userPayLog->create_day     = UTC_DAY;
                    $userPayLog->status         = 1;
                    $userPayLog->sn = Helper::getSn();
                    $userPayLog->save();

                }

                if (
                    $status == ORDER_STATUS_CANCEL_SELLER  &&
                    $data['payFee'] >= 0.0001 &&
                    $data['payStatus'] == ORDER_PAY_STATUS_YES &&
                    $data['isCashOnDelivery'] === false
                )
                {

                    //返还支付金额给会员余额
                    $user = User::find($order->user_id);
                    if ($isRefundBalance == 1) {
                        $user->balance = $user->balance + abs($order->pay_fee);
                        $user->save();
                    } elseif($isRefundBalance == 0 && $order->pay_money > 0.0001) {
                        $user->balance = $user->balance + abs($order->pay_money);
                        $user->save();
                    }

                    $pay_type = $order->getPayType();

                    //统一退款
                    $userPayLogs = UserPayLog::where('order_id', $order->id)
                        ->where('pay_type', 1)
                        ->where('status', 1)
                        ->get()
                        ->toArray();

                    if (count($userPayLogs) > 0) {
                        $userRefundLog = [];
                        $userPayLog = [];

                        foreach($userPayLogs as $k=>$v) {
                            if ($v['paymentType'] == 'balancePay') {
                                $userRefundLog[] = [
                                    "sn" => $order->sn,
                                    "user_id" => $order->user_id,
                                    "order_id" => $order->id,
                                    "trade_no" => $v['tradeNo'],
                                    "seller_id" => $order->seller_id,
                                    "payment_type" => $v['paymentType'],
                                    "money" => $v['money'],
                                    "content" => "商家取消",
                                    "create_time" => UTC_TIME,
                                    "create_day" => UTC_DAY,
                                    "status" => 1
                                ];
                            } else {
                                $userRefundLog[] = [
                                    "sn" => $order->sn,
                                    "user_id" => $order->user_id,
                                    "order_id" => $order->id,
                                    "trade_no" => $v['tradeNo'],
                                    "seller_id" => $order->seller_id,
                                    "payment_type" => $v['paymentType'],
                                    "money" => $v['money'],
                                    "content" => "商家取消",
                                    "create_time" => UTC_TIME,
                                    "create_day" => UTC_DAY,
                                    "status" => 0
                                ];
                            }

                            if ($isRefundBalance == 1) {
                                $userRefundLog[$k]['status'] = 1;
                                $userRefundLog[$k]['content'] = '商家取消,退回用户余额';
                                $userPayLog[$k] = [
                                    'payment_type'  => $v['paymentType'],
                                    'pay_type'       => 3,//退款
                                    'user_id'        => $v['userId'],
                                    'order_id'       => $order->id,
                                    'activity_id'    => $order->activity_id > 0 ? $order->activity_id : 0,
                                    'seller_id'      => $order->seller_id,
                                    'money'           => $v['money'],
                                    'balance'         => $user->balance,
                                    'content'         => '商家取消订单,退回用户余额',
                                    'create_time'    => UTC_TIME,
                                    'create_day'     => UTC_DAY,
                                    'status'          => 1,
                                    'sn'              => Helper::getSn()
                                ];
                            } elseif ($v['paymentType'] == 'balancePay') {
                                $userPayLog = [
                                    'payment_type'  => $v['paymentType'],
                                    'pay_type'       => 3,//退款
                                    'user_id'        => $v['userId'],
                                    'order_id'       => $order->id,
                                    'activity_id'    => $order->activity_id > 0 ? $order->activity_id : 0,
                                    'seller_id'      => $order->seller_id,
                                    'money'           => $v['money'],
                                    'balance'         => $user->balance,
                                    'content'         => '商家取消订单退款',
                                    'create_time'    => UTC_TIME,
                                    'create_day'     => UTC_DAY,
                                    'status'          => 1,
                                    'sn'              => Helper::getSn()
                                ];
                            }
                        }

                        if (!empty($userPayLog)) {
                            UserPayLog::insert($userPayLog);
                        }

                        DB::table('refund')->insert($userRefundLog);

                        SellerMoneyLogService::createLog(
                            $order->seller_id,
                            SellerMoneyLog::TYPE_ORDER_REFUND,
                            $id,
                            $order->pay_fee,
                            '订单取消，退款：'.$order->sn
                        );
                    }
                }

                if (
                    $status == ORDER_STATUS_CANCEL_SELLER  &&
                    $data['sellerFee'] >= 0.0001 &&
                    $data['payStatus'] == ORDER_PAY_STATUS_YES &&
                    $data['isCashOnDelivery'] === false
                ) {
                    //更新商家扩展表
                    \YiZan\Services\SellerService::decrementExtend($order->seller_id, 'wait_confirm_money', $order->seller_fee);
                }
            }
            $bl = true;
	        DB::commit();
	    } catch(Exception $e) {  
	        $bl = false;
            $result['code'] = 99999;
	        DB::rollback();
	    }
	   if($bl){
	       //当状态为接受订单或者拒绝订单的时候,推送消息
	       if ($status == ORDER_STATUS_CANCEL_SELLER || $status == ORDER_STATUS_AFFIRM_SELLER) {
	           $noticeTpe = $status == ORDER_STATUS_AFFIRM_SELLER ? 'order.accept' : 'order.refund';
	           PushMessageService::notice($data['user']['id'],$data['user']['mobile'], $noticeTpe,['sn' => $data['sn']],['sms','app'],'buyer','3', $id, $status == ORDER_STATUS_AFFIRM_SELLER ? "acceptorder.caf" : "");

	               //cz 如果订单在staff没有sellerid给系统配送人员发短信
	               if($data['staff']['sellerId'] == 0 && $noticeTpe == 'order.refund'){
	                   $url = u('staff#Index/index',['id'=>$data['id'],'staffUserId'=>$data['staff']['userId'],'newStaffId'=>$data['staff']['id'],'isChange'=>1]);
	                   PushMessageService::notice($data['staff']['userId'],'', 'order.refund', $data,['app'], 'staff',6, $url, '');
	               }
	       }
	   }
       if($status == ORDER_STATUS_CANCEL_USER){
           //通知服务人员
           if (count($data['staff']) > 0) {
               PushMessageService::notice($data['staff']['userId'], $data['staff']['mobile'], 'order.cancel', $data,['sms','app'],'staff',3, $data['id']);
           }
           PushMessageService::notice($data['user']['id'], $data['user']['mobile'], 'order.usercancel', $data,['sms','app'],'buyer', 3, $data['id']);
       }
        return $result;
	}

	/**
	 * 指定指派人员
	 * @param array $orderIds 订单编号数组
	 * @param int $staffId 员工编号
	 */
	public static function designate($orderId, $staffId,$sellerId) {
	    $result = [
	        'code' => 0,
	        'data' => null,
	        'msg'  => Lang::get('api_system.success.update_info')
	    ];
	
	    //允许更改的订单状态
	    $order_list = Order::where('id', $orderId)->where('status','<=', ORDER_STATUS_FINISH_STAFF)->first();
	    if (!$order_list) {
	        $result['code'] = 80107; // 不能指派
	        return  $result;
	    }
        $rules = array(
            'orderId'	      => ['required'],
            'staffId'	      => ['required'],
        );	        
        $messages = array(
            'orderId.required'	=> '8010911',
            'staffId.required'	=> '8010912',
        );
        $validator = Validator::make([
            'orderId'     =>$orderId,
            'staffId'     =>$staffId,
        ], $rules, $messages);
        
	    if ($validator->fails()) {//验证信息
	        $messages = $validator->messages();
	        $result['code'] = $messages->first();
	        return $result;
	    }
	    $check_staff = SellerStaff::where('id', $staffId)->where('status', 1)->where("seller_id",$sellerId)->first();
	    if (!$check_staff) {
	        $result['code'] = 80102; // 服务人员不存在
	        return  $result;
	    }

        if (!in_array($check_staff->type,['0','3',$order_list->order_type])) {
            $result['code'] = 80114; //服务人员类型错误
            return $result;
        }

        $check_leave = StaffLeave::where('staff_id', $staffId)
            ->where('begin_time', '<=', UTC_TIME)
            ->where('end_time', '>=', UTC_TIME)
            ->where('is_agree', 1)
            ->where('status', 1)
            ->first();

        if ($check_leave) {
            $result['code'] = 80115;//服务人员在请假期间,不能指派
            return $result;
        }
        $is_ok = false;
	    DB::beginTransaction();
	    try {
            Order::where('id', $orderId)->update([
                'seller_staff_id' => $staffId
            ]);
	        $result['code'] = 80000;
	        DB::commit();
            $is_ok = true;
	    } catch(Exception $e) {
	        $result['code'] = 80104;
	        DB::rollback();
	    }

        if($is_ok){
            $data = $order_list->toArray();
            $old_staff = SellerStaff::where('id', $order_list->seller_staff_id)->where('status', 1)->where("seller_id", $sellerId)->first()->toArray();
            if($old_staff &&  $check_staff->id != $old_staff['id']){
                $url = u('staff#Index/index',['id'=>$orderId,'staffUserId'=>$old_staff['userId'],'newStaffId'=>$old_staff['id'],'isChange'=>2]);
                PushMessageService::notice($old_staff['userId'], $old_staff['mobile'], 'order.changesellerstaff', $data, ['sms','app'], 'staff', 6, $url);//修改前的服务人员推送
            }
            $staff = $check_staff->toArray();
            if($staff){
                PushMessageService::notice($staff['userId'], $staff['mobile'], 'order.designate', $data, ['sms','app'], 'staff', 3, $orderId,'neworder.caf');//修改后的服务人员推送
            }
        }
	    return $result;
	}
	
	/**
	 * 随机指派人员
	 * @param $orderId 订单编号数组
	 */
	public static function ranUpdate($orderId,$serviceContent,$money) {
	    $result = [
	        'code' => 0,
	        'data' => null,
	        'msg'  => Lang::get('api_system.success.update_info')
	    ];
	
	    //允许更改的订单状态
	    $allow_status = [ORDER_STATUS_BEGIN_SERVICE,ORDER_STATUS_PAY_SUCCESS,ORDER_STATUS_AFFIRM_SERVICE,ORDER_PAY_STATUS_YES];
	    $order_list = Order::where('id', $orderId)->whereIn('status', $allow_status)->first();
	    if (!$order_list) {
	        $result['code'] = ORDER_NOT_EXIST; // 订单不存在
	        return  $result;
	    }
	    if (!$order_list) {
	        $result['code'] = 80107; // 不能指派
	        return  $result;
	    }
	    if($order_list->order_type > 2){
	        $rules = array(
	            'orderId'	      => ['required'],
	            'serviceContent'  => ['required'],
	            'money'	          => ['required','regex:/^-?\d+$/'],
	        );
	
	        $messages = array(
	            'orderId.required'	=> '8010911',
	            'serviceContent.required'	 => '80108',
	            'money.required'	         => '80109',
	            'money.regex'	             => '80113',
	        );
	        $validator = Validator::make([
	            'orderId'     =>$orderId,
	            'serviceContent'=>$serviceContent,
	            'money'=>$money,
	        ], $rules, $messages);
	      
	    }else{
	        
	        $rules = array(
	            'orderId'	      => ['required'],
	        );
	        
	        $messages = array(
	            'orderId.required'	=> '8010911',
	        );
	        $validator = Validator::make([
	            'orderId'     =>$orderId,
	        ], $rules, $messages);
	    }
	    if ($validator->fails()) {//验证信息
	        $messages = $validator->messages();
	        $result['code'] = $messages->first();
	        return $result;
	    }
	
	    $check_staff = SellerStaff::where('status',1)->get()->toArray();
	    $staff = $check_staff[array_rand($check_staff,1)];
	    if (!$staff) {
	        $result['code'] = 80106; // 服务人员不存在
	        return  $result;
	    }
	    DB::beginTransaction();
	    try {
	
	        if($order_list->order_type > 2){
	            //更新订单表
	            Order::where('id', $orderId)->update([
	                'seller_staff_id' =>$staff['id'],
	                'seller_id' => $staff['seller_id'],
	                'status'    => ORDER_STATUS_AFFIRM_ASSIGN_SERVICE,
	                'total_fee' =>$money,
	                'service_content'=>$serviceContent,
	                'seller_confirm_time' => UTC_TIME,
	            ]);
	             
	        }else{
	           
	            //更新订单表
	            Order::where('id', $orderId)->update([
	                'seller_staff_id' => $staff['id'],
	                'seller_id' =>$staff['seller_id'],
	                'status'    => ORDER_STATUS_AFFIRM_ASSIGN_SERVICE,
	                'seller_confirm_time' => UTC_TIME,
	            ]);
	        }
	        $result['code'] = 80000;
	        DB::commit();
	    } catch(Exception $e) {
	        $result['code'] = 80104;
	        DB::rollback();
	    }
	    return $result;
	}
	/**
	 * 更新订单
	 * @param  int $id 订单id
	 * @param  int $sellerId 商家
	 * @param  int $status 状态
	 * @param  int $content 内容
	 * @return array   更新结果
	 */
	public static function updateRestaurantOrder($id, $restaurantId, $status,$refuseContent )
	{
	    $result =
	    [
	        'code'	=> 0,
	        'data'	=> null,
	        'msg'	=> Lang::get('api.success.update_info')
	    ];
	
	   
	    $order = Order::where('id', $id)->where('restaurant_id', $restaurantId)->first();
	
	    //没有订单
	    if ($order == false)
	    {
	        $result['code'] = self::ORDER_NOT_EXIST; // 没有找到相关订单
	
	        return $result;
	    }
	    
	    if($order->status == ORDER_STATUS_AFFIRM_SERVICE)
	    {
	        $result['code'] = 50200; // 订单已接受
	        return $result;
	    }
	    
	    if($status == ORDER_STATUS_AFFIRM_SERVICE)
	    {
	        $order->status = ORDER_STATUS_AFFIRM_SERVICE;
	
	        $order->buyer_confirm_time = UTC_TIME;
	    }
	    else if ($status == ORDER_RESTAURANT_REFUSE_SERVICE)
	    {
	        if($refuseContent == ""){
	            $result['code'] = 50201; 
	            return $result;
	        }else{
    	        $order->status = ORDER_RESTAURANT_REFUSE_SERVICE;
    	        $order->refuse_content = $refuseContent;
    	        $order->seller_confirm_time = UTC_TIME;
	        }
	    }
	    /*else
	    {
	        if( $order->getIsCanCancelAttribute() == false &&
	            $order->getIsCanAcceptAttribute() == false)
	        {
	            $result['code'] = self::ORDER_STATUS_ERROR;
	
	            return $result;
	        }
	
	        if($status == ORDER_USER_CANCEL_SERVICE)
	        {
	            $order->status = ORDER_USER_CANCEL_SERVICE;
	
	            $order->buyer_cancel_time = UTC_TIME;
	
	            //有优惠券，则退回优惠券
	            $return_promotion = PromotionService::returnPromotion($order);
	
	            if(!$return_promotion){
	                $result['code'] = 50115;
	                return $result;
	            }
	
	        }
	        else if($status == ORDER_STATUS_REFUND_HANDLE)
	        {
	            $order->status = ORDER_STATUS_REFUND_HANDLE;
	
	            $order->refund_time = UTC_TIME;
	
	            $order->deposit_refund_time = UTC_TIME;
	
	            //有优惠券，则退回优惠券
	            $return_promotion = PromotionService::returnPromotion($order);
	
	            if(!$return_promotion){
	                $result['code'] = 50115;
	                return $result;
	            }
	        }
	        else if($status == ORDER_RESTAURANT_REFUSE_SERVICE ||
	            $status == ORDER_RESTAURANT_REFUSE_SERVICE)
	        {
	            $order->status = $status;
	
	            $order->buyer_confirm_end_time = UTC_TIME;
	
	            if($status == ORDER_STATUS_SELLER_REFUSE){
	
	                //有优惠券，则退回优惠券
	                $return_promotion = PromotionService::returnPromotion($order);
	
	                if(!$return_promotion){
	                    $result['code'] = 50115;
	                    return $result;
	                }
	            }
	        }
	    }*/
	
	    $order->save();
	    $data = self::getRestaurantOrderById($restaurantId,$id)->toArray();
	
	    //当状态为接受订单或者拒绝订单的时候,推送消息
	   // if ($status == ORDER_STATUS_AFFIRM_SERVICE ||
// 	        $status == ORDER_RESTAURANT_REFUSE_SERVICE) {
// 	            $noticeTpe = $status == ORDER_STATUS_SELLER_ACCEPT ? 'order.accept' : 'order.refund';
// 	            PushMessageService::notice($data['userId'],$data['mobile'], $noticeTpe,['sn' => $data['sn']],['sms','app'],'buyer',4,$id);
// 	        }
	
	        $result["data"] = $data;
	
	        return $result;
	}
	
	/**
     * 删除订单
     * @param  int $sellerId 卖家
     * @param int  $id 订单id
     * @return array   删除结果
     */
	public static function deleteOrder($sellerId, $id) 
    {
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.delete_info')
		];

		$order = Order::where('id', $id)->where('seller_id', $sellerId)->first();
        
        //没有订单
		if ($order == false) 
        {
			$result['code'] = self::ORDER_NOT_EXIST;
            
	    	return $result;
		}

		//当订单状态不为卖家删除,订单不能再删除
		if (    $order->status == ORDER_STATUS_ADMIN_DELETE     || 
                $order->status == ORDER_STATUS_FINISH_USER      || 
                $order->status == ORDER_STATUS_CANCEL_USER      || 
                $order->status == ORDER_STATUS_CANCEL_AUTO      || 
                $order->status == ORDER_STATUS_CANCEL_SELLER    || 
                $order->status == ORDER_STATUS_CANCEL_ADMIN     || 
                $order->status == ORDER_STATUS_USER_DELETE      || 
                $order->status == ORDER_STATUS_ADMIN_DELETE ) 
        {
           $order->status =  ORDER_STATUS_SELLER_DELETE;
           $order->save();
		}else{
		    $result['code'] = self::ORDER_NOT_DELETE;		    
		    return $result;
		}
		return $result;
	}
}
