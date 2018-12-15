<?php namespace YiZan\Services;

use YiZan\Services\SystemConfigService as baseSystemConfigService;

use YiZan\Models\Buyer\Seller;
use YiZan\Models\Order;
use YiZan\Models\Goods;
use YiZan\Models\System\SellerStaffExtend;
use YiZan\Models\UserAddress;
use YiZan\Models\UserMobile;
use YiZan\Models\OrderGoods;
use YiZan\Models\User;
use YiZan\Models\GoodsExtend;
use YiZan\Models\Payment;
use YiZan\Models\Restaurant;
use YiZan\Models\PromotionSn;
use YiZan\Models\UserPayLog;
use YiZan\Models\SellerMoneyLog;
use YiZan\Models\SellerStaffMoneyLog;
use YiZan\Models\SellerStaff;
use YiZan\Models\SellerExtend;
use YiZan\Models\InvitationBackLog;
use YiZan\Models\OrderRate;

use YiZan\Services\OrderRateService;

use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Utils\String;
use Illuminate\Database\Query\Expression;
use Exception, DB, Lang, Validator, App,Config;

class OrderService extends BaseService
{
    /**
     * 获取订单
     * @param  [type] $userId  [description]
     * @param  [type] $orderId [description] 825722
     * @return [type]          [description]
     */
    public static function getOrder($userId, $orderId) {
        //检测超时支付订单
        self::endOrder();

        return Order::where('id', $orderId)
            ->where('user_id', $userId)
            ->with('seller','goods','promotion', 'staff', 'userRefund', 'orderComplain')
            ->first();
    }

    /**
     * 根据订单编号获取订单
     * @param  [int] $userId  	 [会员编号]
     * @param  [int] $orderId  	 [订单编号]
     * @return [object]          [订单对象]
     */
    public static function getOrderById($userId, $orderId) {
        //检测超时支付订单
        self::endOrder();
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> ''
        );
        $res = Order::where('user_id', $userId)->where('id', $orderId)->with('cartSellers','seller','staff','user')->first()->toArray();
        if($res['id'] == false){
            return  false;
        }
        $res['invoiceTitle']   = $res['invoiceRemark'];
        $res['price']          = $res['totalFee'];
        $res['giftContent']          = $res['giftRemark'];
        $res['sellerName']          = $res['seller']['name'];
        $res['sellerTel']          = $res['seller']['serviceTel'];
        $res['staffName']          = $res['staff']['name'];
        $res['staffMobile']          = $res['staff']['mobile'];
        return $res;
    }

    /**
     * 社区订单列表
     * @param  [type] $userId [description]
     * @param  [type] $status [description]
     * @param  [type] $page   [description]
     * @return [type]         [description]
     */
    public static function getList($userId, $status, $page)
    {
        self::endOrder();
		
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> ''
        );

        $rules = array(
            'userId'	      => ['required'],
        );

        $messages = array(
            'userId.required'	=> '60404',
        );

        $validator = Validator::make([
            'userId'     =>$userId,
        ], $rules, $messages);

        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        $data = ['commentNum' => 0, 'orderList' => []];
        $data['commentNum'] = Order::where('user_id', $userId)
            ->whereIn('status', [
                ORDER_STATUS_FINISH_SYSTEM,
                ORDER_STATUS_FINISH_USER
            ])->where('is_rate',0)
            ->count();
        $list = Order::where('user_id', $userId);

        $paymentwhere = [ORDER_STATUS_BEGIN_USER]; //待支付
        $shippedwhere = [ORDER_STATUS_PAY_SUCCESS,ORDER_STATUS_PAY_DELIVERY]; //待发货
        //$receiptwhere = [ORDER_STATUS_AFFIRM_SELLER]; //待收货
        $receiptwhere = [ORDER_STATUS_AFFIRM_SELLER,ORDER_REFUND_ADMIN_REFUSE,ORDER_REFUND_SELLER_REFUSE,ORDER_STATUS_FINISH_STAFF]; //待收货
        $ratewhere = [ORDER_STATUS_FINISH_SYSTEM,ORDER_STATUS_FINISH_USER]; //待评价
        $refundwhere = [ORDER_REFUND_SELLER_REFUSE_LOGISTICS,ORDER_REFUND_USER_REFUSE_LOGISTICS,ORDER_STATUS_REFUND_AUDITING,ORDER_STATUS_CANCEL_REFUNDING,ORDER_STATUS_REFUND_HANDLE,ORDER_STATUS_REFUND_FAIL,ORDER_STATUS_REFUND_SUCCESS,ORDER_REFUND_SELLER_AGREE,ORDER_REFUND_SELLER_REFUSE,ORDER_REFUND_ADMIN_AGREE,ORDER_REFUND_ADMIN_REFUSE];//退款


        $list->orderBy('id', 'desc');
        if ($status == 1) { //待评价
            $list->whereIn('status',$ratewhere)->where('is_rate', 0);
        }else if ($status == 2) { //待支付
            $list->whereIn('status',$paymentwhere)->where('pay_status', 0);
        } else if ($status == 3) { //待发货
            $list->whereIn('status',$shippedwhere);
        } else if ($status == 4) { //待收货
            //$list->whereIn('status',$receiptwhere);
            $list->where(function($where) use($receiptwhere,$userId){
                $where->where('is_all',1)
                    ->whereIn('status',$receiptwhere)
                    ->where('user_id', $userId);
            });
            $list->orWhere(function($where) use($userId){
                $where->where('is_all',0)
                    ->whereIn('status',[ORDER_STATUS_AFFIRM_SELLER,ORDER_STATUS_START_SERVICE,ORDER_STATUS_FINISH_STAFF])
                    ->where('user_id', $userId);
            });
        }  else if ($status == 5) { //退款
            $list->whereIn('status',$refundwhere);
        } else { //全部
            $list->whereNotIn('status',[
                ORDER_STATUS_USER_DELETE,
                ORDER_STATUS_SELLER_DELETE,
                ORDER_STATUS_ADMIN_DELETE,
                ORDER_STATUS_REFUND_AUDITING,
                ORDER_STATUS_CANCEL_REFUNDING,
                ORDER_STATUS_REFUND_HANDLE,
                ORDER_STATUS_REFUND_FAIL,
                ORDER_STATUS_REFUND_SUCCESS,
                ORDER_REFUND_SELLER_AGREE,
                ORDER_REFUND_SELLER_REFUSE,
                ORDER_REFUND_ADMIN_AGREE,
                ORDER_REFUND_ADMIN_REFUSE,
                ORDER_REFUND_USER_REFUSE_LOGISTICS,
                ORDER_REFUND_SELLER_REFUSE_LOGISTICS
            ]);
        }
        $list = $list->skip(($page - 1) * 20)->take(20)->with('orderGoods','cartSellers', 'seller')->get()->toArray();
        $data['orderList'] = [];
        foreach ($list as $key => $vo){
            $data['orderList'][$key] = $vo;

            if($vo['isCanPay'])
            {
                //可以支付的订单验证是否存在过期商品
                $activityGoodsIsChange = \YiZan\Services\Buyer\OrderService::activityGoodsIsChange($userId, $vo['id']);
                $data['orderList'][$key]['activityGoodsIsChange'] = $activityGoodsIsChange;
            }

            $data['orderList'][$key]['shopName'] = $vo['seller']['name'];
            $data['orderList'][$key]['sellerTel'] = $vo['seller']['serviceTel'];
            $data['orderList'][$key]['storeType'] = $vo['seller']['storeType'];
            $data['orderList'][$key]['countGoods'] = Goods::where('type', 1)->where('seller_id', $vo['seller']['id'])->where('status', 1)->count('id');
            $data['orderList'][$key]['countService'] = Goods::where('type', 2)->where('seller_id', $vo['seller']['id'])->where('status', 1)->count('id');
            $data['orderList'][$key]['goodsImages'] = [];
            foreach ($vo['cartSellers'] as $k => $v) {
                $data['orderList'][$key]['goodsImages'][$k] = $v['goodsImages'];
            }
            unset($data['orderList'][$key]['cartSellers']);
            if($data['orderList'][$key]['seller'] == ""){
                $data['orderList'][$key]['sellerId'] = 0;
            }
            unset($data['orderList'][$key]['seller']);

        }
        return $data;
    }

    public static function endOrder($type = 0) {
        if($type == 0){
            return false;
        }
        DB::beginTransaction();
        try {
            $promotionSnId = Order::where('auto_cancel_time', '<', UTC_TIME)
                ->where('status', ORDER_STATUS_BEGIN_USER)
                ->where('pay_status', ORDER_PAY_STATUS_NO)
                ->lists('promotion_sn_id');

            //超时自动取消,优惠券自动返还
            if(count($promotionSnId) > 0) {
                PromotionSn::whereIn('id',$promotionSnId)->update(['use_time'=>0]);
            }

            $integralOrderLists = Order::where('auto_cancel_time', '<', UTC_TIME)
                ->where('status', ORDER_STATUS_BEGIN_USER)//未处理
                ->where('pay_status', ORDER_PAY_STATUS_NO)//未支付
                ->where('integral', '>', 0)
                ->get()->toArray();

            foreach($integralOrderLists as $val) {
                \YiZan\Services\UserIntegralService::createIntegralLog($val['userId'], '1', '7', $val['id'], $val['payFee'], $val['integral']);
            }


           /* 测试时使用，避免订单过期
            Order::where('auto_cancel_time', '<', UTC_TIME)
                ->where('status', ORDER_STATUS_BEGIN_USER)//未处理
                ->where('pay_status', ORDER_PAY_STATUS_NO)//未支付
                ->where('pay_money', 0)//余额未支付
                ->update(['status' => ORDER_STATUS_CANCEL_AUTO, 'cancel_remark' => '支付超时自动取消订单']);*/
            //设为支付超时
			
            //查询已经部分支付的订单
            $balanceOrderLists = Order::where('auto_cancel_time', '<', UTC_TIME)
                ->where('status', ORDER_STATUS_BEGIN_USER)//未处理
                ->where('pay_status', ORDER_PAY_STATUS_NO)//未支付
                //->where('pay_money', '>', 0)//余额部分支付
                ->get();
				
            // var_dump($balanceOrderLists->toArray());exit;
            foreach ($balanceOrderLists as $order) {
				
				if($order->pay_money > 0 ){
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
					$userPayLog->content        = '支付超时，系统退款';
					$userPayLog->create_time    = UTC_TIME;
					$userPayLog->create_day     = UTC_DAY;
					$userPayLog->status         = 1;
					$userPayLog->sn = Helper::getSn();
					$userPayLog->save();
					
					//修改订单状态
					$order->status = ORDER_STATUS_CANCEL_AUTO;
					$order->cancel_remark = '支付超时自动取消订单';
					$order->save();
				}else{
					//修改订单状态
					$order->status = ORDER_STATUS_CANCEL_AUTO;
					$order->cancel_remark = '支付超时自动取消订单';
					$order->save();
				}

                //支付超时取消-分销通知结算
                if(Config::get('app.fanwefx_system'))
                {
                    $path = 'notify_settlement';
                    $args['order_no'] = $order->sn;
                    $args['settlement_status'] = 2; //已取消
                    $fxres = \YiZan\Services\FxBaseService::requestApi($path, $args);
                    if($fxres)
                    {
                        //已取消
                        Order::where('id', $order->id)->where('fanwefx_status', 0)->update(['fanwefx_status'=>2]);
                    }
                }
                self::cancelOrderStock($order->id);
            }


            $withdrawDay = \YiZan\Models\SystemConfig::where('code','seller_withdraw_day')->pluck('val'); //商家到账时间(天)
            //更新订单状态
//            Order::where('auto_finish_time', '<', UTC_TIME)
//                ->where('pay_status', ORDER_PAY_STATUS_YES)//已支付
//                ->where('status', ORDER_STATUS_FINISH_STAFF)// 已接受
//                ->update([
//                    'status' => ORDER_STATUS_FINISH_SYSTEM,
//                    'buyer_finish_time' => UTC_TIME,
//                    'seller_withdraw_time' => UTC_TIME + $withdrawDay * 24 * 3600
//                ]);//设为系统到期确认


            //自动评价时间
            $systemOrderSelfMotion = baseSystemConfigService::getConfigByCode('system_order_self_motion');
            $systemOrderSelfMotion = $systemOrderSelfMotion * 86400 + UTC_TIME;

            $datatime = [
                'status' => ORDER_STATUS_FINISH_SYSTEM,
                'buyer_finish_time' => UTC_TIME,
                'seller_withdraw_time' => $withdrawDay == 0 ? 0 : UTC_TIME + $withdrawDay * 24 * 3600,
                'auto_rate_time' => $systemOrderSelfMotion
            ];
            //更新订单状态
            // $invitation =  Order::where('auto_finish_time', '<', UTC_TIME)
            //     ->where('pay_status', ORDER_PAY_STATUS_YES)//已支付
            //     ->where('status', ORDER_STATUS_FINISH_STAFF)
            //     ->get()
            //     ->toArray();

            $invitation = Order::where('auto_finish_time', '<', UTC_TIME)
                ->where('pay_status', ORDER_PAY_STATUS_YES)//已支付
                ->where(function($query){
                    $query->where('status', ORDER_STATUS_FINISH_STAFF)
                        ->orWhere(function($sql){
                            $sql->where('status', ORDER_STATUS_AFFIRM_SELLER)
                                ->where('is_all', 1);
                        });
                })
                ->get()
                ->toArray();

            $aoutOrderIds = [];
            foreach( $invitation as $k =>$v){
                $aoutOrderIds[$k] = $v['id'];

                //自动完成-分销通知结算
                if(Config::get('app.fanwefx_system'))
                {
                    $path = 'notify_settlement';
                    $args['order_no'] = $v['sn'];
                    $args['settlement_status'] = 1; //已结算
                    $fxres = \YiZan\Services\FxBaseService::requestApi($path, $args);
                    if($fxres)
                    {
                        //已结算
                        Order::where('id', $v['id'])->where("fanwefx_status", 0)->update(['fanwefx_status'=>1]);
                    }

                    //消费全返分销模式
                    $order_fanwefx_data = unserialize($order->fanwefx_data);
                    $order_fanwefx_data = $order_fanwefx_data[0];
                    if($order_fanwefx_data['passage_id'] == 'return')
                    {
                        $fanwe_id = User::where('id', $order->user_id)->pluck('fanwe_id');

                        $path = 'add_consume_log';
                        $args['user_id'] = $fanwe_id;
                        $args['order_no'] = $order->sn;
                        $args['order_desc'] = '消费全返';
                        $args['order_time'] = Time::todate($order->create_time);
                        $args['order_money'] = $order->pay_fee;
                        //消费记录
                        \YiZan\Services\FxBaseService::requestApi($path, $args);
                    }
                }
            }
            $autoSellerTime = Order::where('auto_seller_time', '<', UTC_DAY)
                ->where('pay_status', ORDER_PAY_STATUS_YES)
                ->where('is_all', 1)
                ->where('status', ORDER_STATUS_CANCEL_USER_SELLER)
                ->get();


            foreach ($autoSellerTime as $k => $v) {
                self::userR($v);
            }

            self::autoinvitationBackLog($aoutOrderIds);
            self::sendFee($aoutOrderIds);
            Order::whereIn('id',$aoutOrderIds)->update($datatime);



            //更新服务人员扩展表
            $sql = "UPDATE " . env('DB_PREFIX') . "seller_staff_extend AS E
            INNER JOIN
            (
                    SELECT seller_staff_id,sum(freight) as freight,count(id) as order_count
                        FROM " . env('DB_PREFIX') . "order
                        WHERE (seller_withdraw_time <= " . UTC_TIME . "
                        OR seller_withdraw_time = 0)
                         AND pay_status = " . ORDER_PAY_STATUS_YES . "
                         AND status in (" . ORDER_STATUS_FINISH_SYSTEM . "," .ORDER_STATUS_FINISH_USER . ")
                         AND id NOT in (
                            SELECT related_id FROM ". env('DB_PREFIX') ."seller_staff_money_log WHERE type = 'send_fee' AND status = 1
                         )
                         GROUP BY seller_staff_id
            ) AS T ON E.staff_id = T.seller_staff_id
            SET E.withdraw_money = IFNULL(E.withdraw_money, 0) + IFNULL(T.freight, 0),
            E.total_money = IFNULL(E.total_money, 0) + IFNULL(T.freight, 0),
            E.order_count = IFNULL(E.order_count, 0) + IFNULL(T.order_count, 0)";

            DB::unprepared($sql);


            //更新商家扩展表
            $sql = "UPDATE " . env('DB_PREFIX') . "seller_extend AS E
            INNER JOIN
            (
                    SELECT seller_id,sum(pay_fee) as pay_fee,sum(seller_fee) as seller_fee,count(id) as order_count
                        FROM " . env('DB_PREFIX') . "order
                        WHERE (seller_withdraw_time <= " . UTC_TIME . " 
                        OR seller_withdraw_time = 0)
                         AND pay_status = " . ORDER_PAY_STATUS_YES . "
                         AND status in (" . ORDER_STATUS_FINISH_SYSTEM . "," .ORDER_STATUS_FINISH_USER . ")
                         AND pay_type <> 'cashOnDelivery' 
						 AND id NOT in (
							SELECT related_id FROM ". env('DB_PREFIX') ."seller_money_log WHERE type = '".SellerMoneyLog::TYPE_ORDER_CONFIRM."'
						 )
						 GROUP BY seller_id
            ) AS T ON E.seller_id = T.seller_id
            SET E.total_money = IFNULL(E.total_money, 0) + IFNULL(T.seller_fee, 0),
            E.money = IFNULL(E.money, 0) + IFNULL(T.seller_fee, 0),
            /*E.order_count = IFNULL(E.order_count, 0) + IFNULL(T.order_count, 0),*/
            E.wait_confirm_money = (
            CASE
                WHEN (IFNULL(E.wait_confirm_money, 0) - IFNULL(T.seller_fee, 0)) > 0 THEN
                    (IFNULL(E.wait_confirm_money, 0) - IFNULL(T.seller_fee, 0))
                ELSE
                    0
            END
                    )";

            DB::unprepared($sql);

            //自动确认之后写入商家资金日志
            $orders = Order::whereIn('status', [ORDER_STATUS_FINISH_USER, ORDER_STATUS_FINISH_SYSTEM])
                ->where(function ($query) {
                    $query->where('seller_withdraw_time', '<=', UTC_TIME)
                        ->orWhere('seller_withdraw_time', '=', 0);
                })
                ->where('pay_type', '<>', 'cashOnDelivery')
                ->whereNotIn('id', function ($query) {
                    $query->select('related_id')
                        ->from('seller_money_log')
                        ->where('type', SellerMoneyLog::TYPE_ORDER_CONFIRM);
                })->select(DB::raw('*,(select money from ' . env('DB_PREFIX') . 'seller_extend where ' . env('DB_PREFIX') . 'order.seller_id = ' . env('DB_PREFIX') . 'seller_extend.seller_id) as balance'))
                ->get()->toArray();
			//自动确认之后写入商家资金日志
            $allCountSellerFee = Order::whereIn('status', [ORDER_STATUS_FINISH_USER, ORDER_STATUS_FINISH_SYSTEM])
                ->where(function ($query) {
                    $query->where('seller_withdraw_time', '<=', UTC_TIME)
                        ->orWhere('seller_withdraw_time', '=', 0);
                })
                ->where('pay_type', '<>', 'cashOnDelivery')
                ->whereNotIn('id', function ($query) {
                    $query->select('related_id')
                        ->from('seller_money_log')
                        ->where('type', SellerMoneyLog::TYPE_ORDER_CONFIRM);
                })->selectRaw('seller_id, SUM(seller_fee) as totalSellerFee')
                ->groupBy('seller_id')->lists('totalSellerFee','seller_id');
            $data = [];
            $orderIds = [];
            $countSellerFee = [];
            $balance = [];
            foreach ($orders as $k => $v) {
				$countSellerFee[$v['sellerId']] = (double)$countSellerFee[$v['sellerId']] + $v['sellerFee'];
				$balance[$v['sellerId']] = $v['balance'] - $allCountSellerFee[$v['sellerId']];
				$balance[$v['sellerId']] += $countSellerFee[$v['sellerId']];
                $data[$k] = [
                    'sn' => Helper::getSn(),
                    'seller_id' => $v['sellerId'],
                    'type' => SellerMoneyLog::TYPE_ORDER_CONFIRM,
                    'related_id' => $v['id'],
                    'money' => $v['sellerFee'],
                    'balance' => $balance[$v['sellerId']],
                    'content' => '订单' . $v['sn'] . '结算入余额',
                    'create_time' => UTC_TIME,
                    'create_day' => UTC_DAY,
                    'status' => 1
                ];
                $orderIds[$k] = $v['id'];
            }

            // 自动评价
            $orderRates = Order::where('auto_rate_time', '>', 0)
                ->where('auto_rate_time', '<=', UTC_TIME)
                ->where('is_rate', 0)->get()->toArray();

            if(count($orderRates) > 0)
            {
                foreach ($orderRates as $key => $value) {
                    Order::where('id', $value['id'])->update(['is_rate' => 1, 'auto_rate_time' => null]);

                    if($value['isAll'] == 1)
                    {
                        //获取全国店订单商品
                        $orderGoods = OrderGoods::where('order_id', $value['id'])->get()->toArray();
                        //全国店评价
                        foreach($orderGoods as $k => $v) {
                            $comment = null;
                            $comment[] = [
                                'content' => '好评！',
                                'id' => $v['goodsId'],
                                'star' => 5,
                            ];
                            OrderRateService::createRateAll($value['userId'], 1, $value['id'], 5, $comment, 2);
                        }
                    }
                    else
                    {
                        // 周边店评价
                        OrderRateService::createRate($value['userId'], $value['id'],  [], '好评！', 5, 2);
                    }

                }
            }


            if (!empty($data)) {
                SellerMoneyLog::insert($data);
                SellerMoneyLog::where('type', 'order_pay')->whereIn('related_id',$orderIds)->update(['status' => 3]);
            }
            DB::commit();
            return 'ok';
        }catch (Exception $e){
            DB::rollback();
            return 'Error';
        }

    }

    /**
     * 取消订单
     * @param  [type] $userId  [description]
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     */
    public static function cancelOrder($userId, $orderId,$cancelRemark) {

        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> Lang::get('api.success.user_cancel_order')
        );
        $order = Order::with('staff')->where('id', $orderId)->where('user_id', $userId)->first();

        /*if($cancelRemark == ""){
            $result['code'] = 60116;
            return $result;
        }*/
        //没有订单
        if (!$order)
        {
            $result['code'] = 60014;
            return $result;
        }

        //不能取消
        if (!in_array($order->status, [
            ORDER_STATUS_BEGIN_USER,
            ORDER_STATUS_PAY_SUCCESS,
            ORDER_STATUS_PAY_DELIVERY
        ]))
        {
            $result['code'] = 60015;
            return $result;
        }
        DB::beginTransaction();
        try {
            //更改订单状态
            if ($order->is_all == 1) {
                $order->status = ORDER_STATUS_CANCEL_USER_SELLER;
                $order->auto_seller_time = UTC_DAY + 2 * 24 * 3600;
                $order->cancel_remark = $cancelRemark;
                $order->cancel_time = UTC_TIME;
                $order->save();
                $result = array(
                    'code'	=> 0,
                    'data'	=> null,
                    'msg'	=> Lang::get('api.success.user_cancel_order_all')
                );
            }else{
                //更改订单状态
                $order->status = ORDER_STATUS_CANCEL_USER;
                $order->cancel_time = UTC_TIME;
                $order->cancel_remark = $cancelRemark;
                $order->save();
                Seller::where('id', $order->seller_id)->increment('user_cancel');

            //退还优惠券
            if ($order->promotion_sn_id > 0) {
                PromotionSn::where('id', $order->promotion_sn_id)->update(['use_time' => 0]);
            }

            //退还积分
            if ((int)$order->integral > 0) {
                \YiZan\Services\UserIntegralService::createIntegralLog($userId, 1, 7, $orderId, 0, $order->integral);
            }


            if($order->pay_status == ORDER_PAY_STATUS_YES && $order->isCashOnDelivery()) {
                //如果是货到付款则退还商家支付的抽成金额
                $sellerMoneyLog = SellerMoneyLog::where('related_id', $order->id)
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
                        $orderId,
                        $order->drawn_fee,
                        '现金支付订单' . $order->sn . '取消，佣金返还',
                        1
                    );
                }
            }
            //是否设置退还到余额
            $isRefundBalance = \YiZan\Models\SystemConfig::where('code', 'is_refund_balance')->pluck('val');

            //如果未支付订单 包含余额支付金额 则退款
            if($order->pay_status == ORDER_PAY_STATUS_NO && $order->pay_money > 0.0001){
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
                $userPayLog->content        = '会员取消订单退款';
                $userPayLog->create_time    = UTC_TIME;
                $userPayLog->create_day     = UTC_DAY;
                $userPayLog->status         = 1;
                $userPayLog->sn = Helper::getSn();
                $userPayLog->save();

            }

            //是否是返现订单
            if ((int)$order->is_invitation > 0) {
                InvitationBackLog::where('order_id',$order->id)->where('user_id',$order->user_id)->update([
                    'is_refund' => 1,
                    'update_time' => UTC_TIME
                ]);

            }

            //写入退款日志
            if (
                $order->pay_fee >= 0.0001 &&
                $order->pay_status == ORDER_PAY_STATUS_YES &&
                $order->isCashOnDelivery === false
            ) {

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
                            $userRefundLog[$k] = [
                                "sn" => $order->sn,
                                "user_id" => $order->user_id,
                                "order_id" => $order->id,
                                "trade_no" => $v['tradeNo'],
                                "seller_id" => $order->seller_id,
                                "payment_type" => $v['paymentType'],
                                "money" => $v['money'],
                                "content" => "用户取消",
                                "create_time" => UTC_TIME,
                                "create_day" => UTC_DAY,
                                "status" => 1
                            ];
                        } else {
                            $userRefundLog[$k] = [
                                "sn" => $order->sn,
                                "user_id" => $order->user_id,
                                "order_id" => $order->id,
                                "trade_no" => $v['tradeNo'],
                                "seller_id" => $order->seller_id,
                                "payment_type" => $v['paymentType'],
                                "money" => $v['money'],
                                "content" => "用户取消",
                                "create_time" => UTC_TIME,
                                "create_day" => UTC_DAY,
                                "status" => 0
                            ];
                        }

                        if ($isRefundBalance == 1) {
                            $userRefundLog[$k]['status'] = 1;
                            $userRefundLog[$k]['content'] = '用户取消,退回用户余额';
                            $userPayLog[$k] = [
                                'payment_type'  => $v['paymentType'],
                                'pay_type'       => 3,//退款
                                'user_id'        => $v['userId'],
                                'order_id'       => $order->id,
                                'activity_id'    => $order->activity_id > 0 ? $order->activity_id : 0,
                                'seller_id'      => $order->seller_id,
                                'money'           => $v['money'],
                                'balance'         => $user->balance,
                                'content'         => '会员取消订单退款,退回用户余额',
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
                                'content'         => '会员取消订单退款',
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
                        $orderId,
                        $order->pay_fee,
                        '订单取消，退款：' . $order->sn
                    );
                }

            }

            //减少商家待到账金额
            if (
                $order->seller_fee >= 0.0001 &&
                $order->pay_status == ORDER_PAY_STATUS_YES &&
                $order->isCashOnDelivery === false
            ) {
                \YiZan\Services\SellerService::decrementExtend($order->seller_id, 'wait_confirm_money', $order->seller_fee);
            }

                self::cancelOrderStock($orderId);
            }

            DB::commit();
        }catch (Exception $e){
            DB::rollback();
            $result['code'] = 99999;
            return $result;
        }

        try
        {
	if($order->is_all == 0){
            $result['data'] = self::getOrderById($userId,$order->id);
            $order = $result['data'];
            //通知服务人员
            if (count($order['staff']) > 0) {
                //cz 如果订单在staff没有sellerid给系统配送人员发短信
                if($order['staff']['sellerId'] == 0){
                    $url = u('staff#Index/index',['id'=>$order['id'],'staffUserId'=>$order['staff']['userId'],'newStaffId'=>$order['staff']['id'],'isChange'=>1]);
                    PushMessageService::notice($order['staff']['userId'],'', 'order.refund', $order,['app'], 'staff',6, $url, '');
                }else{
                    PushMessageService::notice($order['staff']['userId'], $order['staff']['mobile'], 'order.cancel', $order,['sms','app'],'staff',3, $order['id']);
                }
            }
            PushMessageService::notice($order['user']['id'], $order['user']['mobile'], 'order.usercancel', $order,['sms','app'],'buyer', 3, $order['id']);
	}
        }
        catch (Exception $e)
        {
        }

        //取消-分销通知结算
        if(Config::get('app.fanwefx_system'))
        {
            $path = 'notify_settlement';
            $args['order_no'] = $order->sn;
            $args['settlement_status'] = 2; //已取消
            $fxres = \YiZan\Services\FxBaseService::requestApi($path, $args);
            if($fxres)
            {
                //已取消
                Order::where('id', $order->id)->where("fanwefx_status", 0)->update(['fanwefx_status'=>2]);
            }
        }

        return $result;
    }

    /**
     * 取消订单还原库存
     * @param int $orderId 订单编号
     */
    public static function cancelOrderStock($orderId) {

        $sql = "UPDATE ".env('DB_PREFIX')."order_goods AS A
		LEFT OUTER JOIN ".env('DB_PREFIX')."goods AS G ON G.id = A.goods_id AND A.sku_sn IS NULL
		LEFT OUTER JOIN ".env('DB_PREFIX')."goods_stock AS GN ON A.sku_sn = GN.sku_sn
	SET G.stock = G.stock + A.num,
			GN.stock_count = GN.stock_count + A.num,
			GN.sale_count = GN.sale_count - A.num
	WHERE A.order_id = ".$orderId;

        DB::unprepared($sql);

    }
    /**
     * 支付订单
     * @param  [type] $userId  [description]
     * @param  [type] $orderId [description]
     * @param  [type] $payment [description]
     * @return [type]          [description]
     */
    public static function payOrder($userId, $orderId, $payment, $extend = []) {

        if($payment == "cashOnDelivery"){
            $result = self::delivery($userId, $orderId);
            return $result;
        }
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> Lang::get('api.success.user_pay_order')
        );

        $order = Order::where('id', $orderId)->where('user_id', $userId)->with('seller','staff')->first();
        if (!$order) {//没有订单
            $result['code'] = 60014;
            return $result;
        }

        if ($order->pay_status != 0) {//已经支付过了
            $result['code'] = 60018;
            return $result;
        }

        if ($order->status != ORDER_STATUS_BEGIN_USER) {//订单不能再进行支付
            $result['code'] = 60017;
            return $result;
        }
        $order->goods_name = "社区服务";
        $payLog = PaymentService::createPayLog($order, $payment, $extend, 0);

        if (is_numeric($payLog))
        {
            $result['code'] = abs($payLog);

            return $result;
        }

        $result['data'] = $payLog;
        return $result;
    }

    /**
     * 余额支付订单
     * @param  [type] $order  [description]
     * @param  [type] $userPayLog [description]
     * @return [type]          [description]
     */
    public static function balanceOrder($order, $userPayLog) {
        $order = Order::with('staff', 'goods', 'user')->find($order->id);
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );

        if (!$order) {//没有订单
            $result['code'] = 60014;
            return $result;
        }

        if ($order->pay_status != 0) {//已经支付过了
            $result['code'] = 60018;
            return $result;
        }

        if ($order->status != ORDER_STATUS_BEGIN_USER) {//订单不能再进行支付
            $result['code'] = 60017;
            return $result;
        }
        //更新用户余额 
        $userbalance = $order->user->balance - $order->pay_fee;
        if($userbalance < 0){
            $result['code'] = 60318;
            return $result;
        }
        // var_dump($userPayLog);
        // exit;
        DB::beginTransaction();
        try {
            $status = UserPayLog::where('id',$userPayLog['id'])->update([
                'pay_time'                  => UTC_TIME,
                'pay_day'                   => UTC_DAY,
                'balance'                   => $userbalance,
                'status'                    => 1
            ]);

            //修改状态
            $status = Order::where('id', $order->id)->update([
                'pay_time'                  => UTC_TIME,
                'pay_status'                => ORDER_PAY_STATUS_YES,
                'status'                    => ORDER_STATUS_PAY_SUCCESS,
                'pay_type'                  => $userPayLog['paymentType'],
                'pay_money'                 => $order->pay_fee,
            ]);

            if ($status) {
                //更新服务人员待到帐金额
                SellerService::incrementExtend($order->seller_id, 'wait_confirm_money', $order->pay_fee);

                //写入日志
                SellerMoneyLogService::createLog(
                    $order->seller_id,
                    'order_pay',
                    $order->id,
                    $order->pay_fee,
                    '订单支付:'.$order->sn
                );

                if(IS_OPEN_FX){
                    $datas['user_id'] = $order->user_id;
                    \YiZan\Services\Buyer\OrderService::invitationOrder($order->id,$datas,$order->is_all,$order->share_user_id);
                }

                User::where('id', $order->user_id)->update(['balance'=>$userbalance]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $status = false;
        }

        if ($status) {
            $order = self::getOrderById($order->user_id, $order->id);
            try {
                PushMessageService::notice( $order['seller']['userId'],  $order['seller']['mobile'], 'order.pay',  $order,['sms', 'app'],'seller','3',$order['id'], "neworder.caf");
                if($order['staff'] && $order['seller']['userId'] != $order['staff']['userId']){
                    PushMessageService::notice( $order['staff']['userId'],  $order['staff']['mobile'], 'order.pay',  $order,['sms', 'app'],'staff','3',$order['id'], "neworder.caf");
                }
            } catch (Exception $e) {

            }

        } else {
            $result['code'] = 60019;
        }
    }

    /**
     * 删除订单
     * @param  [type] $userId  [description]
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     */
    public static function deleteOrder($userId, $orderId) {
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> Lang::get('api.success.user_delete_order')
        );

        $order = Order::where('id', $orderId)->where('user_id', $userId)->first();
        if (!$order) {//没有订单
            $result['code'] = 60014;
            return $result;
        }
        //当订单状态不为以下状态时
        if ($order->isCanDelete == false)
        {
            $result['code'] = 60020;

            return $result;
        }
        //会员已删除订单
        $order->status = ORDER_STATUS_USER_DELETE;
        $order->save();
        return $result;
    }
    /**
     * 货到付款
     * @param  [type] $userId  [description]
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     */
    public  static  function delivery($userId,$orderId){

        $order = Order::where('id', $orderId)->where('user_id', $userId)->first();
        if (!$order) {//没有订单
            $result['code'] = 60014;
            return $result;
        }

        if ($order->pay_status != 0) {//已经支付过了
            $result['code'] = 60018;
            return $result;
        }

        if ($order->status != ORDER_STATUS_BEGIN_SERVICE) {//订单不能再进行支付
            $result['code'] = 60017;
            return $result;
        }
        $order->pay_time        = UTC_TIME;                        // 支付时间
        $order->pay_type 		 = 2;                               // 货到付款
        $order->pay_status 	 = ORDER_PAY_STATUS_YES;            // 已支付
        $order->status 	     = ORDER_STATUS_PAY_DELIVERY;       // 货到付款


        DB::beginTransaction();
        try
        {
            $order->save();
            DB::commit();
            $bln = true;
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
            $bln = false;
        }
        if ($bln) {
            $order = self::getOrderById($userId,$orderId);
            $result['data'] = $order;
        }
        return $result;
    }
    /**
     * 确认订单
     * @param  [type] $userId  [description]
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     */
    public static function confirmOrder($userId, $orderId) {

        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> Lang::get('api.success.user_confirm_order')
        );
        $order = Order::with('staff','seller')->where('id', $orderId)->where('user_id', $userId)->first();
        if (!$order) {//没有订单
            $result['code'] = 60014;
            return $result;
        }

        //当订单状态为 会员确认,订单已经确认过
        if ($order->status == ORDER_STATUS_FINISH_SYSTEM || $order->status == ORDER_STATUS_FINISH_USER)
        {
            $result['code'] = 60022;
            return $result;
        }
        if($order->is_all ==  1){
            //不满足这不能完成
            if (!in_array($order->status,[ORDER_STATUS_AFFIRM_SELLER ,ORDER_REFUND_ADMIN_REFUSE,ORDER_REFUND_SELLER_REFUSE]))
            {
                $result['code'] = 60021;
                return $result;
            }
        }else{
            //当订单状态不为 服务完成,订单不能确认
            if (!in_array($order->status,[
                ORDER_STATUS_FINISH_STAFF,
                ORDER_STATUS_START_SERVICE
            ]))
            {
                $result['code'] = 60021;
                return $result;
            }
        }
        $withdrawDay = \YiZan\Models\SystemConfig::where('code','seller_withdraw_day')->pluck('val');
        $order->buyer_finish_time = UTC_TIME;
        $order->status 	= ORDER_STATUS_FINISH_USER;//会员确认
        $order->seller_withdraw_time = $withdrawDay == 0 ?  0:  UTC_DAY + $withdrawDay * 24 * 3600;

        //自动评价时间
        $systemOrderSelfMotion = baseSystemConfigService::getConfigByCode('system_order_self_motion');
        $systemOrderSelfMotion = $systemOrderSelfMotion * 86400 + UTC_TIME;
        $order->auto_rate_time = $systemOrderSelfMotion;

        DB::beginTransaction();
        try
        {
            self::invitationBackLog($userId, $orderId);
            $order->save();
            DB::commit();
            $bln = true;
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
            $bln = false;
        }

        if ($bln) {
            $order = $order->toArray();

            $newarrs[] = $order['id'];
            self::sendFee($newarrs);
            try {
                //通知服务人员
                PushMessageService::notice( $order['seller']['userId'],  $order['seller']['mobile'], 'order.confirm',  $order,['sms', 'app'],'seller','3',$orderId,$order['status']);
                if($order['staff'] && $order['seller']['userId'] != $order['staff']['userId']){
                    PushMessageService::notice( $order['staff']['userId'],  $order['staff']['mobile'], 'order.confirm',  $order,['sms', 'app'],'staff','3',$orderId,$order['status']);
                }
            } catch (Exception $e) {
            }

            $order = self::getOrderById($userId,$orderId);
            $result['data'] = $order;

            //分销通知结算
            if(Config::get('app.fanwefx_system'))
            {
                $path = 'notify_settlement';
                $args['order_no'] = $order['sn'];
                $args['settlement_status'] = 1; //已结算
                $fxres = \YiZan\Services\FxBaseService::requestApi($path, $args);
                if($fxres)
                {
                    //已结算
                    Order::where('id', $order['id'])->where('fanwefx_status', 0)->update(['fanwefx_status'=>1]);
                }

                //消费全返分销模式
                $order_fanwefx_data = unserialize($order['fanwefx_data']);
                $order_fanwefx_data = $order_fanwefx_data[0];
                if($order_fanwefx_data['passage_id'] == 'return')
                {
                    $fanwe_id = User::where('id', $order['user_id'])->pluck('fanwe_id');

                    $path = 'add_consume_log';
                    $args['user_id'] = $fanwe_id;
                    $args['order_no'] = $order['sn'];
                    $args['order_desc'] = '消费全返';
                    $args['order_time'] = Time::todate($order['create_time']);
                    $args['order_money'] = $order['pay_fee'];
                    //消费记录
                    \YiZan\Services\FxBaseService::requestApi($path, $args);
                }
            }
        }

        return $result;
    }

    /**
     * [refund 退款]
     * @param  [int] $userId            [会员编号]
     * @param  [int] $orderId           [订单编号]
     * @param  [string] $refundImages   [退款举证图片]
     * @param  [string] $refundContent  [退款理由]
     * @return [array]                  [返回代码]
     */
    public static function refund($userId, $orderId, $refundImages, $refundContent){
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg' => Lang::get('api.success.order_refund_create')
        );

        $order = OrderService::getOrderById($userId, $orderId);

        if(!$order->isCanRefund){
            $result['code'] = 60306;
            return $result;
        }

        if (!$order) {
            $result['code'] = 60301;
            return $result;
        }

        if ($refundContent == '') {
            $result['code'] = 60305;
            return $result;
        }

        if (count($refundImages) > 0) {
            foreach ($refundImages as $key => $image) {
                $refundImages[$key] = self::moveOrderImage($orderId, $image);
                if (!$refundImages[$key]) {
                    $result['code'] = 60308;
                    return $result;
                }
            }
            $refundImages = implode(',', $refundImages);
        } else {
            $refundImages = '';
        }

        $order_refund_info = [
            'status'			=> ORDER_STATUS_REFUND_AUDITING,
            'refund_images' 	=> $refundImages,
            'refund_content' 	=> $refundContent,
            'refund_time' 		=> UTC_TIME,
        ];

        try{
            Order::where('user_id', $userId)
                ->where('id', $orderId)
                ->update($order_refund_info);
        } catch( Exception $e ){
            $result['code'] = 60307;
        }
        return $result;
    }

    /**
     * [refund 返现日志]
     * @param  [int] $userId            [会员编号]
     * @param  [int] $orderId           [订单编号]
     * @return [array]                  [返回代码]
     */
    public static function invitationBackLog($userId, $orderId){

        if(!IS_OPEN_FX){
            return false;
        }
        $order = Order::where('user_id', $userId)->where('id', $orderId)->first()->toArray();
        if (!$order) {
            return false;
        }

        if($order['isInvitation'] == 1){
            $logs = InvitationBackLog::where('user_id',$userId)->where('order_id',$order['id'])->where('status',0)->get();
            DB::beginTransaction();
            try{
                foreach ($logs as $log) {
                    if($log->id > 0){
                        $type = $log->invitation_type;
                        $id = $log->invitation_id;
                        $returnFee = $log->return_fee;
                        if($type == 'seller'){
                            $sellerMoneyLog = new SellerMoneyLog;
                            $sellerMoneyLog->sn          = Helper::getSn();
                            $sellerMoneyLog->seller_id   = $id;
                            $sellerMoneyLog->type        = SellerMoneyLog::TYPE_INVITATION_BACK;
                            $sellerMoneyLog->related_id  = $orderId;
                            $sellerMoneyLog->money       = $returnFee;
                            $sellerMoneyLog->content     = '邀请返现';
                            $sellerMoneyLog->create_time = UTC_TIME;
                            $sellerMoneyLog->create_day  = UTC_DAY;
                            $sellerMoneyLog->balance     = SellerExtend::where('seller_id',$id)->pluck('money');
                            $sellerMoneyLog->status      = 1;
                            $sellerMoneyLog->save();
                            //增加商家金额
                            SellerExtend::where('seller_id', $id)->increment('money', abs($returnFee));
                        }else if($type == 'user'){
                            $userPayLog = new UserPayLog;
                            $userPayLog->payment_type   = $order['PaymentType'];
                            $userPayLog->pay_type       = 7;//邀请返现',
                            $userPayLog->user_id        = $id;
                            $userPayLog->order_id       = $orderId;
                            $userPayLog->activity_id    = 0;
                            $userPayLog->seller_id      = 0;
                            $userPayLog->money          = $returnFee;
                            $userPayLog->content        = '邀请返现';
                            $userPayLog->create_time    = UTC_TIME;
                            $userPayLog->create_day     = UTC_DAY;
                            $userPayLog->status         = 1;
                            $userPayLog->sn = Helper::getSn();
                            //邀请返现
                            $userPayLog->balance        = User::where('id',$id)->pluck('balance');
                            $userPayLog->save();
                            User::where('id', $id)->increment('balance', abs($returnFee));
                        }
                        $log_data = [];
                        $log->status = 1;
                        $log->save();
                    }
                }
                DB::commit();
                return true;
            } catch( Exception $e ) {
                DB::rollback();
            }
        }
        return false;
    }

    public static function autoinvitationBackLog($orderId){

        $order = Order::select('order.user_id', 'order.id', 'order.pay_type')
            ->whereIn('id',$orderId)
            ->where('is_invitation',1)
            ->get()
            ->toArray();

        $ids = [];
        $orderids = [];
        $ptype = [];
        foreach($order as $k => $v){
            $ids[$k] = $v['userId'];
            $orderids[$k]  = $v['id'];
            $ptype[$v['id']]  = $v['payType'];
            self::invitationBackLog($v['userId'], $v['id']);
            sleep(0.5);
        }
        return;
    }

    /**
     * 订单统计
     * @param string $duration 统计周期
     * @return mixed
     */
    public static function ordersCount($duration = 'day'){

        $start = 0;
        if($duration == 'day'){
            $start = UTC_DAY;
        }elseif($duration == 'month'){
            $start = mktime(0, 0, 0, date('m'), 1, date('Y')) - date('Z');
        }

        $res = Order::whereIn('status',['200','201','202'])
            ->where('create_time' ,'>=', $start)
            ->where('is_integral_goods' , 0)/*排除积分订单*/
            ->where('seller_id' ,'!=', ONESELF_SELLER_ID)
            ->count();

        return $res;

    }

    /**
     * 营业额统计
     * @param string $duration 统计周期
     * @return mixed
     */
    public static function salesAmountSum($duration = 'day'){
        $start = 0;
        if($duration == 'day'){
            $start = UTC_DAY;
        }elseif($duration == 'month'){
            $start = mktime(0, 0, 0, date('m'), 1, date('Y')) - date('Z');
        }
        $res = Order::whereIn('status',['200','201','202'])
            ->where('pay_status' ,1)
            ->where('create_time' ,'>=', $start)
            ->where('seller_id' ,'!=', ONESELF_SELLER_ID)
            ->sum('pay_fee');
        return $res;

    }

    /**
     * 商城订单统计
     * @param string $duration 统计周期
     * @return mixed
     */
    public static function storeOrdersCount($duration = 'day'){

        $start = 0;
        if($duration == 'day'){
            $start = UTC_DAY;
        }elseif($duration == 'month'){
            $start = mktime(0, 0, 0, date('m'), 1, date('Y')) - date('Z');
        }
        $res = Order::whereIn('status',['200','201','202'])
            ->where('create_time' ,'>=', $start)
            ->where('is_integral_goods' , 0)/*排除积分订单*/
            ->where('seller_id' , ONESELF_SELLER_ID)
            ->count();

        return $res;

    }

    /**
     * 商城营业额统计
     * @param string $duration 统计周期
     * @return mixed
     */
    public static function storeSalesAmountSum($duration = 'day'){

        $start = 0;
        if($duration == 'day'){
            $start = UTC_DAY;
        }elseif($duration == 'month'){
            $start = mktime(0, 0, 0, date('m'), 1, date('Y')) - date('Z');
        }
        $res = Order::whereIn('status',['200','201','202'])
            ->where('create_time' ,'>=', $start)
            ->where('seller_id' , ONESELF_SELLER_ID)
            ->sum('pay_fee');

        return $res;

    }

    /**
     * 退款详情
     * @param int $userId 用户编号
     * @param int $orderId 订单编号
     */
    public function refundDetail($userId, $orderId) {
        $data = [
            'money' => 0,
            'time' => '',
            'payment' => '',
            'status' => '成功',
            'stepOne' => [
                'status' => 0,
                'name' => '退款申请',
                'brief' => '',
                'time' => ''
            ],
            'stepTwo' => [
                'status' => 0,
                'name' => '平台审核通过',
                'brief' => '',
                'time' => ''
            ],
            'stepThree' => [
                'status' => 0,
                'name' => '资金到账',
                'brief' => '',
                'time' => ''
            ]
        ];
        $order = Order::where('user_id',$userId)->where('id',$orderId)->with('userRefund','refundCount')->first();
        if ($order) {
            $order = $order->toArray();
            if ($order['refundCount'] > 0) {
                $data['time'] = Time::toDate($order['cancelTime'],'Y-m-d H:i:s');
                $data['stepOne'] = [
                    'status' => 1,
                    'name' => '退款申请',
                    'brief' => $order['userRefund'][0]['content'],
                    'time' => Time::toDate($order['userRefund'][0]['createTime'],'Y-m-d H:i:s')
                ];

            }
            foreach($order['userRefund'] as $k=>$v){
                $payment = Lang::get('admin.payments.'.$v['paymentType']);
                $data['money']  = (double)round(($v['money'] + $data['money']),2);
                if ($v['paymentType'] != 'balancePay') {
                    $data['status'] = $v['status'] == 1 ? '成功' : '退款中';
                }
                if ($order['refundCount'] > 1) {
                    $data['payment'] .= '￥'.$v['money'].'退回至'.$payment.' ';
                    if ($v['paymentType'] != 'balancePay' && $v['status'] == 1) {
                        $data['stepTwo'] = [
                            'status' => 1,
                            'name' => '平台审核通过',
                            'brief' => '平台已审核通过，等待资金到账',
                            'time' => Time::toDate($v['createTime'],'Y-m-d H:i:s')
                        ];
                        $data['stepThree'] = [
                            'status' => 1,
                            'name' => '资金到账',
                            'brief' => '您的资金￥'.$v['money'].'已退回至'.$payment.'账号',
                            'time' => Time::toDate($v['createTime'],'Y-m-d H:i:s')
                        ];
                    }
                } else {
                    $data['payment'] = $payment;
                    if ($v['status'] == 1) {
                        $data['stepTwo'] = [
                            'status' => 1,
                            'name' => '平台审核通过',
                            'brief' => '平台已审核通过，等待资金到账',
                            'time' => Time::toDate($v['createTime'],'Y-m-d H:i:s')
                        ];
                        $data['stepThree'] = [
                            'status' => 1,
                            'name' => '资金到账',
                            'brief' => '您的资金￥'.$v['money'].'已退回至'.$payment.'账号',
                            'time' => Time::toDate($v['createTime'],'Y-m-d H:i:s')
                        ];
                    }
                }
            }
        }

        return $data;
    }

    /**
     * 配送服务费
     */
    public function sendFee($orderId){
        $orders = Order::select('order.id', 'sn', 'order.send_fee', 'order.seller_staff_id', 'order.seller_id','order.send_fee', 'order.send_staff_fee')
            ->whereIn('id',$orderId)
            ->where('send_fee','>',0)
            ->get()
            ->toArray();

        foreach($orders as $k=>$order){
            if(empty($order) && empty($order['sendFee'])){
                return '';
            }

            //获取服务人员信息
            $staff = SellerStaff::where("id", $order['sellerStaffId'])->first();
            //判断有没有服务人员拓展表
            if(SellerStaffExtend::where("staff_id", $order['sellerStaffId'])->first() == false)
            {
                $sellerStaffExtend = new SellerStaffExtend();
                $sellerStaffExtend->staff_id = $order['sellerStaffId'];
                $sellerStaffExtend->seller_id = $staff->seller_id;
                $sellerStaffExtend->save();
            }

            DB::beginTransaction();
            try{
                //商家减去余额平台的钱
                SellerExtend::where('seller_id', $order['sellerId'])->decrement('money', $order['sendFee']);
                SellerExtend::where('seller_id', $order['sellerId'])->decrement('total_money', $order['sendFee']);
                $data = [
                    'sn' => Helper::getSn(),
                    'seller_id' => $order['sellerId'],
                    'type' => SellerMoneyLog::TYPE_SEND_FEE,
                    'related_id' => $order['id'],
                    'money' => $order['sendFee'],
                    'content' => '在线支付订单'.$order['sn'].'，配送服务费',
                    'create_time' => UTC_TIME,
                    'create_day' => UTC_DAY,
                    'balance'=>   SellerExtend::where('seller_id',$order['sellerId'])->pluck('money'),
                    'status' => 1
                ];
                SellerMoneyLog::insert($data);

                //服务人员加钱
                SellerStaffExtend::where('staff_id', $order['sellerStaffId'])->increment('withdraw_money', $order['sendStaffFee']);
                SellerStaffExtend::where('staff_id', $order['sellerStaffId'])->increment('total_money', $order['sendStaffFee']);
                $data2 = [
                    'sn' => Helper::getSn(),
                    'staff_id' => $order['sellerStaffId'],
                    'type' => 'send_fee',
                    'related_id' => $order['id'],
                    'money' => $order['sendStaffFee'],
                    'content' => '在线支付订单'.$order['sn'].'，配送服务费',
                    'create_time' => UTC_TIME,
                    'create_day' => UTC_DAY,
                    'balance'=>   SellerStaffExtend::where('staff_id',$order['sellerStaffId'])->pluck('withdraw_money'),
                    'status' => 1
                ];
                SellerStaffMoneyLog::insert($data2);
                DB::commit();
            } catch( Exception $e ) {
                DB::rollback();
            }
        }
    }

    /**
     * 自动分配
     */
    public function autopei($id){
        $order = Order::where('id', $id)->first();
        if($order == ""){
            return '';
        }
        $order = $order->toArray();

        $mapPoint = $order['mapPoint'];
        if($mapPoint == ""){
            return '';
        }
        $mapPoint = $mapPoint['x'].' '.$mapPoint['y'];

        $tablePrefix = DB::getTablePrefix();
        $sqlseller = "SELECT staff_id FROM (SELECT * FROM (SELECT * FROM `{$tablePrefix}seller_staff_work` ORDER BY id DESC) AS T GROUP BY staff_id) AS T1 WHERE T1.is_work = 1";
        $sellerStaffIds = DB::select($sqlseller);
        if(empty($sellerStaffIds)){
            return '';
        }
        $staffarrs = [];
        foreach($sellerStaffIds as $key=>$val){
            $staffarrs[] = $val->staff_id;
        }
        $staffstring = implode(',',$staffarrs);
        $sql = "SELECT ss.id, COUNT(o.id) AS getorder_num FROM {$tablePrefix}seller_staff AS ss
                LEFT JOIN {$tablePrefix}order AS o ON o.seller_staff_id = ss.id AND o.status = ".ORDER_STATUS_GET_SYSTEM_SEND."
                WHERE ss.id in({$staffstring}) AND ss.is_system = 1 AND ss.status = 1 AND ST_Contains(ss.map_pos, GeomFromText ('Point(".$mapPoint.")')) GROUP BY ss.id
                ORDER BY `getorder_num` ASC LIMIT 1";

        $result = DB::select($sql);
        if(empty($result)){
            return '';
        }
        $result = $result[0]->id;

        if($result > 0){
            $staff = SellerStaff::where('id',$result)->first()->toArray();
            PushMessageService::notice( $staff['userId'],  $staff['mobile'], 'order.create',  $order,['sms', 'app'],'staff','3',$id, "neworder.caf");
        }

        return $result;
    }


    /**
     * 退款详情
     * @param int $userId 用户编号
     * @param int $orderId 订单编号
     */
    public function userR($order) {
        Seller::where('id', $order->seller_id)->increment('user_cancel');
        //退还优惠券
        if ($order->promotion_sn_id > 0) {
            PromotionSn::where('id', $order->promotion_sn_id)->update(['use_time' => 0]);
        }

        //退还积分
        if ((int)$order->integral > 0) {
            \YiZan\Services\UserIntegralService::createIntegralLog($order->user_id, 1, 7, $order->id, 0, $order->integral);
        }


        if($order->pay_status == ORDER_PAY_STATUS_YES && $order->isCashOnDelivery()) {
            //如果是货到付款则退还商家支付的抽成金额
            $sellerMoneyLog = SellerMoneyLog::where('related_id', $order->id)
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
                    $order->id,
                    $order->drawn_fee,
                    '现金支付订单' . $order->sn . '取消，佣金返还',
                    1
                );
            }
        }
        //是否设置退还到余额
        $isRefundBalance = \YiZan\Models\SystemConfig::where('code', 'is_refund_balance')->pluck('val');

        //如果未支付订单 包含余额支付金额 则退款
        if($order->pay_status == ORDER_PAY_STATUS_NO && $order->pay_money > 0.0001){
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
            $userPayLog->content        = '会员取消订单退款';
            $userPayLog->create_time    = UTC_TIME;
            $userPayLog->create_day     = UTC_DAY;
            $userPayLog->status         = 1;
            $userPayLog->sn = Helper::getSn();
            $userPayLog->save();

        }

        //是否是返现订单
        if ((int)$order->is_invitation > 0) {
            InvitationBackLog::where('order_id',$order->id)->where('user_id',$order->user_id)->update([
                'is_refund' => 1,
                'update_time' => UTC_TIME
            ]);

        }

        //写入退款日志
        if (
            $order->pay_fee >= 0.0001 &&
            $order->pay_status == ORDER_PAY_STATUS_YES &&
            $order->isCashOnDelivery === false
        ) {

            //返还支付金额给会员余额
            $user = User::find($order->user_id);
            if ($isRefundBalance == 1) {
                $user->balance = $user->balance + abs($order->pay_fee);
                $user->save();
            } elseif($isRefundBalance == 0 && $order->pay_money > 0.0001) {
                $user->balance = $user->balance + abs($order->pay_money);
                $user->save();
            }

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
                        $userRefundLog[$k] = [
                            "sn" => $order->sn,
                            "user_id" => $order->user_id,
                            "order_id" => $order->id,
                            "trade_no" => $v['tradeNo'],
                            "seller_id" => $order->seller_id,
                            "payment_type" => $v['paymentType'],
                            "money" => $v['money'],
                            "content" => "用户取消",
                            "create_time" => UTC_TIME,
                            "create_day" => UTC_DAY,
                            "status" => 1
                        ];
                    } else {
                        $userRefundLog[$k] = [
                            "sn" => $order->sn,
                            "user_id" => $order->user_id,
                            "order_id" => $order->id,
                            "trade_no" => $v['tradeNo'],
                            "seller_id" => $order->seller_id,
                            "payment_type" => $v['paymentType'],
                            "money" => $v['money'],
                            "content" => "用户取消",
                            "create_time" => UTC_TIME,
                            "create_day" => UTC_DAY,
                            "status" => 0
                        ];
                    }

                    if ($isRefundBalance == 1) {
                        $userRefundLog[$k]['status'] = 1;
                        $userRefundLog[$k]['content'] = '用户取消,退回用户余额';
                        $userPayLog[$k] = [
                            'payment_type'  => $v['paymentType'],
                            'pay_type'       => 3,//退款
                            'user_id'        => $v['userId'],
                            'order_id'       => $order->id,
                            'activity_id'    => $order->activity_id > 0 ? $order->activity_id : 0,
                            'seller_id'      => $order->seller_id,
                            'money'           => $v['money'],
                            'balance'         => $user->balance,
                            'content'         => '会员取消订单退款,退回用户余额',
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
                            'content'         => '会员取消订单退款',
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
                    $order->id,
                    $order->pay_fee,
                    '订单取消，退款：' . $order->sn
                );
            }
			$order->status = ORDER_STATUS_REFUND_SUCCESS;
			$order->save();
        }
		
        //减少商家待到账金额
        if (
            $order->seller_fee >= 0.0001 &&
            $order->pay_status == ORDER_PAY_STATUS_YES &&
            $order->isCashOnDelivery === false
        ) {
            \YiZan\Services\SellerService::decrementExtend($order->seller_id, 'wait_confirm_money', $order->seller_fee);
        }
        self::cancelOrderStock($order->id);
    }
}