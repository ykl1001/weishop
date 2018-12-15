<?php 
namespace YiZan\Services\Staff;

use YiZan\Models\GoodsStaff;
use YiZan\Services\SystemConfigService;
use YiZan\Models\Staff\Order;
use YiZan\Models\SellerStaff;
use YiZan\Models\Seller;
use YiZan\Models\Goods;
use YiZan\Models\PromotionSn;
use YiZan\Models\SellerExtend;
use YiZan\Models\UserPayLog;
use YiZan\Models\SellerMoneyLog;
use YiZan\Models\User;
use YiZan\Models\InvitationBackLog;
use YiZan\Models\OrderTrack;
use YiZan\Services\PushMessageService;
use YiZan\Services\System\SystemConfigService as baseSystemConfigService;

use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Utils\String;
use Exception, DB, Lang, Validator, App;

class  OrderService extends \YiZan\Services\OrderService
{

    /**
     * 订单列表
     * @param int $sellerId 商家编号
     * @param int $staffId 员工编号
     * @param int $status 订单状态 1:新订单 2:进行中 3:已完成 4:已取消
     * @param string $date 日期(格式 20151028)
     * @param string $keywords 搜索关键字
     * @param int $page 页码
     */
    public static function getList($sellerId, $staffId, $status, $date, $keywords, $page) {
        self::endOrder();
        $data = [
            'count' => 0,
            'ingCount' => 0,
            'amount' => 0,
            'orders' => []
        ];
        //新订单状态
        $newStatus = [
            ORDER_STATUS_BEGIN_USER,
            ORDER_STATUS_PAY_SUCCESS,
            ORDER_STATUS_PAY_DELIVERY,
            ORDER_STATUS_SYSTEM_SEND,
            ORDER_STATUS_CALL_SYSTEM_SEND,
            ORDER_STATUS_GET_SYSTEM_SEND
        ];
        //配送/服务中的订单状态
        $ingStatus = [
			ORDER_STATUS_START_SERVICE,
            ORDER_STATUS_PAY_SUCCESS,
            ORDER_STATUS_PAY_DELIVERY,
            ORDER_STATUS_AFFIRM_SELLER
        ];

        $list = Order::orderBy('id', 'desc');
        if ($sellerId > 0) {
            $list->where('seller_id', $sellerId);
            if ($status == 1) {
                $newStatus = [
                    ORDER_STATUS_BEGIN_USER,
                    ORDER_STATUS_PAY_SUCCESS,
                    ORDER_STATUS_PAY_DELIVERY,
                    ORDER_STATUS_SYSTEM_SEND,
                    ORDER_STATUS_CALL_SYSTEM_SEND,
                    ORDER_STATUS_GET_SYSTEM_SEND
                ];
            }
        } elseif ($staffId > 0) {
            $list->where('seller_staff_id', $staffId);
        }

        //日期搜索
        if ($date != '') {
            $beginTime = Time::toTime($date);
            $endTime = $beginTime + 24 * 3600 - 1;
            $list->whereBetween('create_time', [$beginTime, $endTime]);
        }
        //关键字搜索
        if ($keywords != '') {
            $list->where(function($query) use ($keywords){
                $name = String::strToUnicode($keywords,'+');
                $query->orWhere('sn', 'like', '%'.$keywords.'%')
                    ->orWhereRaw('MATCH(name_match) AGAINST(\'' . $name . '\' IN BOOLEAN MODE)')
                    ->orWhere('mobile', 'like', '%'.$keywords.'%')
                    ->orWhereIn('id', function($child) use ($keywords){
                        $child->select('order_id')
                            ->from('order_goods')
                            ->where('goods_name', 'like', '%'.$keywords.'%');
                    });
            });
        }

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

        $countObj = clone $list;
        $data['count'] = $countObj->whereNotIn('status', [ORDER_STATUS_SELLER_DELETE,ORDER_STATUS_ADMIN_DELETE])->count(); //全部


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
        } else if ($status == 9) { //进行中
            $list->whereIn('status',$ingStatus);
        } else {
            $list->whereNotIn('status', [
                ORDER_STATUS_SELLER_DELETE,
                ORDER_STATUS_ADMIN_DELETE
            ]);
        }


        //$data['count'] = $list->count();
        $data['amount'] = $list->sum('pay_fee');
        $list = $list->with('seller','orderGoods.goodsNorms','user')->skip(($page - 1) * 20)->take(20)->get()->toArray();

        foreach ($list as $key => $val) {
            $data['orders'][$key] = [
                'id' => $val['id'],
                'sn' => $val['sn'],
                'address' => $val['address'],
                'distance' =>self::getdistance($val['mapPoint'], $val['seller']['mapPoint']),
                'orderStatusStr' => $val['orderStatusStr'],
                'totalFee' => $val['totalFee'],
                'sellerFee' => $val['sellerFee'],
                'payFee' => $val['payFee'],
                'discountFee' => $val['discountFee'],
                'drawnFee' => $val['drawnFee'],
                'freight' => $val['freight'],
                'createTime' => $val['createTime'],
                'isFinished' => $val['isFinished'],
                'shopName' => $val['seller']['name'],
                'userName' => $val['user']['name'],
                'userMobile' => $val['user']['mobile'],
                'payStatusStr' => $val['payStatusStr'],
                'goods' => $val['orderGoods'],
                'isCanAccept' => $val['isCanAccept'], //发货按钮
                'isPay' => $val['isPay'],//关闭订单按钮
                'newOrderStatusStr' => $val['newOrderStatusStr'],//关闭订单按钮
                'isLogistics' => $val['isLogistics'],//查看物流按钮
                'isRefund' => $val['isRefund'],//查看退款申请
                'orderNewStatusStr' => $val['orderNewStatusStr'],//查看退款申请
                'isCancfOrder' => $val['isCancfOrder'],//确定取消
                'sendWay' => $val['sendWay'],//配送方式
                'sellerAddress' => $val['seller']['address'],

            ];
            foreach($val['orderGoods'] as $goods){
                $data['orders'][$key]['images'][] = $goods['goodsImages'];
                $data['orders'][$key]['num'] += $goods['num'];
            }
        }
        return  $data;
    }
    /**
     * 订单列表(old)
     * @param  int $staffId 卖家
     * @param  int $status 订单状态
     * @param  int $page 页码
     * @return array          订单列表
     */
    /*public static function getStaffList($staffId, $page, $pageSize = 20)
    {
        $list = Order::orderBy('status', 'desc')
              ->where('seller_staff_id', $staffId)
              ->with('cartSellers')
              ->where("status",ORDER_STATUS_AFFIRM_SELLER)
              ->skip(($page - 1) * $pageSize)
              ->take($pageSize)
              ->get()
              ->toArray();
        $data =[];
        foreach ( $list as $key => $val){
          $data[$key]['id'] =  $val['id'];
          $data[$key]['serviceName'] =  $val['cartSellers'][0]['goodsName'];
          $data[$key]['address'] =  $val['address'];
          $data[$key]['orderStatusStr'] =  $val['orderStatusStr'];
          $data[$key]['price'] = $val['totalFee'];
          $data[$key]['appTime'] =  $val['appTime'];
          $data[$key]['duration'] = $val['duration'];
          $data[$key]['name'] =  $val['name'];
          $data[$key]['mobile'] = $val['mobile'];
          $data[$key]['mapPoint'] = $val['mapPoint'];
          $data[$key]['isCanFinishService'] = $val['isCanFinishService'];
        }
        return $data;
    }*/

    /**
     * 获取订单详情
     * @param int $sellerId 商家编号
     * @param int $staffId 服务人员编号
     * @param int $orderId 订单编号
     * @return array
     */
    public static function getOrderById($sellerId, $staffId, $orderId)
    {
        self::endOrder();
      if ($sellerId > 0) {
          $data = Order::where('id', $orderId)->where('seller_id', $sellerId)->with('orderGoods.goodsNorms', 'seller','staff')->first();
      } elseif($staffId > 0) {
          $data = Order::where('id', $orderId)->where('seller_staff_id', $staffId)->with('orderGoods.goodsNorms', 'seller','staff')->first();
      }
      if ($data) {
          $data = $data->toArray();
          $data['distance'] = self::getdistance($data['mapPoint'], $data['seller']['mapPoint']);
          $data['sellerName'] =  $data['seller']['name'];
          $data['sellerSendWay'] =  $data['seller']['sendType'];
          unset($data['seller']);
      }
      return $data;
    }


    /**
     *求两个已知经纬度之间的距离,单位为千米
     *@return int 距离，单位千米
     **/
    public static function getdistance($mapPointBegin,$mapPointEnd){
        //将角度转为狐度
        $radLat1 = deg2rad($mapPointBegin['y']);//deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($mapPointEnd['y']);
        $radLng1 = deg2rad($mapPointBegin['x']);
        $radLng2 = deg2rad($mapPointEnd['x']);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s= 2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137*1000;
        return round($s/1000);
    }
    /**
     * 订单状态改变
     * @param int $sellerId 商家编号
     * @param int $staffId 员工编号
     * @param int $id 订单编号
     * @param int $status 订单更改状态 1:取消订单 2:确认订单 3:订单完成 4:开始服务与开始配送 5:呼叫配送 6:取消呼叫配送
     * @param string $remark 取消订单备注
     */
    public static function updateOrder($sellerId, $staffId, $id, $status, $remark,$express = ''){

        $result = [
                'code'  => 0,
                'data'  => null,
                'msg'   => Lang::get('api.success.update_info')
            ];

        $updateStatus = [
            '1' => ORDER_STATUS_CANCEL_SELLER,
            '2' => ORDER_STATUS_AFFIRM_SELLER,
            '3' => ORDER_STATUS_FINISH_STAFF,
            '4' => ORDER_STATUS_START_SERVICE,
            '5' => ORDER_STATUS_CANCEL_USER_SELLER,
        ];
        if (!in_array($status, array_keys($updateStatus))) {
            $result['code'] = 20002;
            return $result;
        }

        if ($sellerId == 0 && $staffId > 0) {
            if ($status == 1 || $status == 2) {
                $result['code'] = 99995;
                return $result;
            }
            $order = Order::where('id', $id)->where('seller_staff_id', $staffId)->with('user', 'seller', 'orderGoods', 'staff')->first();
        } else {
            $order = Order::where('id', $id)->where('seller_id', $sellerId)->with('user', 'seller', 'orderGoods', 'staff')->first();
        }

        //没有订单
        if ($order == false)
        {
            $result['code'] = 20001; // 没有找到相关订单
            return $result;
        }
        if ($status == 1) {
            //当为取消订单的时候,订单状态不合法
            if (!$order->isCanCancel) {
                $result['code'] = 20002;
                return $result;
            }
           /* if ($remark == '') {
                $result['code'] = 20008;
                return $result;
            }*/
            $data = [
                'status' => $updateStatus[$status],
                'cancel_time' => UTC_TIME,
                'cancel_remark' => $remark
            ];
            //取消订单且为已支付的时候,退款
            /*if( $order->pay_status == ORDER_PAY_STATUS_YES && $order->pay_fee >= 0.0001 && $order->isCashOnDelivery === false){
                $data['status'] = ORDER_STATUS_CANCEL_REFUNDING;
                $data['refund_time'] = UTC_TIME;
            }*/

            //是否是返现订单
           if ($order->is_invitation) {
                InvitationBackLog::where('order_id',$order->id )->where('user_id',$order->user_id)->update([
                    'is_refund' => 1,
                    'update_time' => UTC_TIME
                ]);
           }
        }

        if ($status == 2) {
            //当为接单的时候,订单状态不合法
            if (!$order->isCanAccept) {
                $result['code'] = 20002;
                return $result;
            }

            $data = [
                'status' => $updateStatus[$status],
                'seller_confirm_time' => UTC_TIME,
            ];

            //全国店，发货时候写入自动完成时间参数
            if($order->is_all == 1)
            {
                $autoFinishDay = baseSystemConfigService::getConfigByCode('system_buyer_order_confirm_all');
                $autoFinishTime = $autoFinishDay * 24 * 3600 + UTC_TIME;
                $data['auto_finish_time'] = $autoFinishTime;
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

            //自动评价时间
            // $systemOrderSelfMotion = baseSystemConfigService::getConfigByCode('system_order_self_motion');
            // $systemOrderSelfMotion = $systemOrderSelfMotion * 86400 + UTC_TIME; 
            // $data['auto_rate_time'] = $systemOrderSelfMotion;

        }

        if ($status == 3) {
            //当为完成订单的时候,订单状态不合法
            if (!$order->isCanFinish) {
                $result['code'] = 20002;
                return $result;
            }

            $data = [
                'status' => $updateStatus[$status],
                'staff_finish_time' => UTC_TIME
            ];

            //周边店，服务人员确认完成写入自动完成时间参数
            if($order->is_all == 0)
            {
                $autoFinishDay = baseSystemConfigService::getConfigByCode('system_buyer_order_confirm');
                $autoFinishTime = $autoFinishDay * 24 * 3600 + UTC_TIME;
                $data['auto_finish_time'] = $autoFinishTime;
            }

            //如果是到店，则更新消费码
            if( in_array($order->send_way, [2,3]) )
            {
                $data['auth_code_use'] = -1;
                $data['auth_code_use_time'] = UTC_TIME;
            }

        }
        
        if ($status == 4)
        {
            //当为开始服务的时候,订单状态不合法
            if (!$order->isCanStartService) {
                $result['code'] = 20002;
                return $result;
            }
            
            $data = [
                'status' => $updateStatus[$status]
            ];
        }

        if ($status == 5) {
            //当呼叫配送的时候
            if (!$order->isCanCall) {
                $result['code'] = 20002;
                return $result;
            }
            //服务配送费 服务人员得的服务配送费 平台得的服务配送费
            $sendcenter = SystemConfigService::getConfigByGroup('sendcenter');
            $send_fee = $sendcenter['system_send_staff_fee'];
            $send_staff_fee = $sendcenter['system_send_staff_fee']-$sendcenter['system_send_fee'];
            $send_system_fee = $sendcenter['system_send_fee'];

            //看看自动分配是否有人
            $newstaffId = OrderService::autopei($id);

            if($newstaffId > 0){
                $data = [
                    'status' => ORDER_STATUS_GET_SYSTEM_SEND,
                    'seller_staff_id' => $newstaffId,
                    'send_fee' => $send_fee,
                    'send_staff_fee' => $send_staff_fee,
                    'send_system_fee' => $send_system_fee
                ];
            }else{
                $data = [
                    'status' => $updateStatus[$status],
                    'send_fee' => $send_fee,
                    'send_staff_fee' => $send_staff_fee,
                    'send_system_fee' => $send_system_fee
                ];
            }

        }

        if ($status == 6) {
            //当取消呼叫配送
            if (!$order->isCanCancelCall) {
                $result['code'] = 20002;
                return $result;
            }

            $data = [
                'status' => $updateStatus[$status],
                'send_fee' => 0,
                'send_staff_fee' => 0,
                'send_system_fee' => 0,
                'is_cancel_call' => 1
            ];
        }

        //（接单时处理）判断是否是到店
        if($status == 2 && in_array($order->send_way, [2,3])) {
            $data['auth_code'] = Helper::getCode(1,12);
            $data['auth_code_use'] = 1; //-1：已使用  1：未使用
        }

        if ($status == ORDER_STATUS_CANCEL_USER_SELLER)
        {
            //当为开始服务的时候,订单状态不合法
            if (!$order->isCancfOrder) {
                $result['code'] = 20002;
                return $result;
            }

            $data = [
                'status' => ORDER_STATUS_CANCEL_USER
            ];
        }
        DB::beginTransaction();
        try {
            $ble = Order::where('id', $id)->update($data);
            if ($status == ORDER_STATUS_CANCEL_USER_SELLER){
                parent::userR($order);
            }else {
                $order = $order->toArray();

            if ($status == 1 || $status == 2) {
                //还原库存
                if ($status == 1) {
                    self::cancelOrderStock($id);
                    //如果是货到付款则退还商家支付的抽成金额
                    $sellerMoneyLog = SellerMoneyLog::where('related_id', $id)
                        ->where('type', SellerMoneyLog::TYPE_DELIVERY_MONEY)
                        ->first();
                    if($sellerMoneyLog){
                        $sellerMoneyLog->status = 2;
                        $sellerMoneyLog->save();
                        //增加商家金额
                        SellerExtend::where('seller_id', $order['sellerId'])
                            ->increment('money', $sellerMoneyLog->money);
                        //写入增加金额日志
                        \YiZan\Services\SellerMoneyLogService::createLog(
                            $order['sellerId'],
                            SellerMoneyLog::TYPE_DELIVERY_MONEY,
                            $id,
                            $order['drawnFee'],
                            '现金支付订单' . $order['sn'] . '取消，佣金返还',
                            1
                        );
                    }

                    //退还优惠券
                    if ((int)$order['promotionSnId'] > 0) {
                        PromotionSn::where('id', $order['promotionSnId'])->update(['use_time'=>0]);
                    }

                    //退还积分
                    if ((int)$order['integral'] > 0) {
                        \YiZan\Services\UserIntegralService::createIntegralLog($order['userId'], 1, 7, $id, 0, $order['integral']);
                    }
                }




                $isRefundBalance = \YiZan\Models\SystemConfig::where('code', 'is_refund_balance')->pluck('val');

                //如果未支付订单 包含余额支付金额 则退款
                if($status == 1  && $order['payStatus'] == ORDER_PAY_STATUS_NO && $order['payMoney'] > 0.0001){
                    //返还支付金额给会员余额
                    $user = User::find($order['userId']);
                    $user->balance = $user->balance + abs($order->pay_money);
                    $user->save();
                    //创建退款日志
                    $userPayLog = new UserPayLog;
                    $userPayLog->payment_type   = 'balancePay';
                    $userPayLog->pay_type       = 3;//退款
                    $userPayLog->user_id        = $order['userId'];
                    $userPayLog->order_id       = $order['id'];
                    $userPayLog->activity_id    = $order['activityId']> 0 ? $order['activityId'] : 0;
                    $userPayLog->seller_id      = $order['sellerId'];
                    $userPayLog->money          = $order['payMoney'];
                    $userPayLog->balance        = $user->balance;
                    $userPayLog->content        = '商家取消订单退款';
                    $userPayLog->create_time    = UTC_TIME;
                    $userPayLog->create_day     = UTC_DAY;
                    $userPayLog->status         = 1;
                    $userPayLog->sn = Helper::getSn();
                    $userPayLog->save();

                }

                //当为取消且支付,退款金额大于0的时候写入退款日志
                if ($status == 1 &&
                    $order['payStatus'] == ORDER_PAY_STATUS_YES &&
                    $order['payFee'] >= 0.0001 &&
                    $order['isCashOnDelivery'] === false
                ){

                    //返还支付金额给会员余额
                    $user = User::find($order['userId']);
                    if ($isRefundBalance == 1) {
                        $user->balance = $user->balance + abs($order['payFee']);
                        $user->save();
                    } elseif($isRefundBalance == 0 && $order['payMoney'] > 0.0001) {
                        $user->balance = $user->balance + abs($order['payMoney']);
                        $user->save();
                    }

                    $userPayLogs = UserPayLog::where('order_id', $order['id'])
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
                                    "sn" => $order['sn'],
                                    "user_id" => $order['userId'],
                                    "order_id" => $order['id'],
                                    "trade_no" => $v['tradeNo'],
                                    "seller_id" => $order['sellerId'],
                                    "payment_type" => $v['paymentType'],
                                    "money" => $v['money'],
                                    "content" => "商家取消",
                                    "create_time" => UTC_TIME,
                                    "create_day" => UTC_DAY,
                                    "status" => 1
                                ];
                            } else {
                                $userRefundLog[] = [
                                    "sn" => $order['sn'],
                                    "user_id" => $order['userId'],
                                    "order_id" => $order['id'],
                                    "trade_no" => $v['tradeNo'],
                                    "seller_id" => $order['sellerId'],
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
                                    'order_id'       => $order['id'],
                                    'activity_id'    => $order['activityId'] > 0 ? $order['activityId'] : 0,
                                    'seller_id'      => $order['sellerId'],
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
                                    'order_id'       => $order['id'],
                                    'activity_id'    => $order['activityId'] > 0 ? $order['activityId'] : 0,
                                    'seller_id'      => $order['sellerId'],
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
                        
                        \YiZan\Services\SellerMoneyLogService::createLog(
                                $order['sellerId'],
                                \YiZan\Models\SellerMoneyLog::TYPE_ORDER_REFUND,
                                $id,
                                $order['payFee'],
                                '订单取消退款：'.$order['sn']
                            );
                    }


                }

                //当为取消且支付,商家金额大于0的时候扣除商家金额
                if ($status == 1 &&
                    $order['payStatus'] == ORDER_PAY_STATUS_YES &&
                    $order['sellerFee'] >= 0.0001 &&
                    $order['isCashOnDelivery'] === false
                ){
                    \YiZan\Services\SellerService::decrementExtend($order['sellerId'], 'wait_confirm_money', $order['sellerFee']);
                }

                }
                $result['data'] = self::getOrderById($sellerId, $staffId, $id);
            }
            DB::commit();
        } catch(Exception $e) {
            $result['code'] = 99999;
            DB::rollback();
        }        

        if ($sellerId == 0 && $staffId > 0) {
            $order = Order::where('id', $id)->where('seller_staff_id', $staffId)->with('user', 'seller', 'orderGoods', 'staff')->first()->toArray();
        } else {
            $order = Order::where('id', $id)->where('seller_id', $sellerId)->with('user', 'seller', 'orderGoods', 'staff')->first()->toArray();
        }
        if($status == ORDER_STATUS_CANCEL_USER_SELLER){
            //通知服务人员
            if (count($order['staff']) > 0) {
                PushMessageService::notice($data['staff']['userId'], $order['staff']['mobile'], 'order.cancel', $order,['sms','app'],'staff',3, $order['id']);
            }
            PushMessageService::notice($order['user']['id'], $order['user']['mobile'], 'order.usercancel', $order,['sms','app'],'buyer', 3, $order['id']);
        }else{
            //推送
            if ($status != 1 && $status != 2 ) {
                if($status != 3){
                    PushMessageService::notice($order['user']['id'], $order['user']['mobile'], 'order.staff', $order, ['sms','app'], 'buyer', '3', $id , $updateStatus[$status] == ORDER_STATUS_START_SERVICE ? "startorder.caf" : "");
                }
            } else {
                //通知会员
                $noticeTpe = $updateStatus[$status] == ORDER_STATUS_AFFIRM_SELLER ? 'order.accept' : 'order.refund';
                $sound = $updateStatus[$status] == ORDER_STATUS_AFFIRM_SELLER ? 'acceptorder.caf' : '';
                PushMessageService::notice($order['user']['id'],$order['user']['mobile'], $noticeTpe,$order,['sms','app'],'buyer',"3", $id, $sound,$updateStatus[$status]);
            }

        }

        return  $result;
    }
    /**
     * 服务人员完成订单(old)
     * @param  int $id 订单id
     * @param  int $staffId 商家员工
     * @param  int $status 状态
     * @return array   更新结果
     */
    /*public static function updateStaffOrder($id, $staffId, $status)
    {
        $result = 
        [
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.update_info')
        ];
        
        $order = Order::where('id', $id)->where('seller_staff_id', $staffId)->with('user')->first();
        
        //没有订单
        if ($order == false) 
        {
            $result['code'] = 20001; // 没有找到相关订单
            
            return $result;
        }

        if($order->status != ORDER_STATUS_AFFIRM_SELLER && $order->status == ORDER_STATUS_FINISH_STAFF)
        {
            $result['code'] = 20002; // 订单状态不合法
            
            return $result;
        }
        
        if($status == ORDER_STATUS_FINISH_STAFF)
        {
            $autoFinishDay = SystemConfigService::getConfigByCode('system_buyer_order_confirm');
            $autoFinishTime = $autoFinishDay * 24 * 3600 + UTC_TIME;
            Order::where('id', $id)
                ->where('seller_staff_id', $staffId)
                ->update([
                    'status' => $status,
                    'staff_finish_time' => UTC_TIME,
                    'auto_finish_time' => $autoFinishTime
                ]);
            $result["data"] = self::getStaffOrderById($staffId,$id);
            $ble = true;
        }else{
            $ble = false;
            $result['code'] = 20002; // 订单状态不合法
        }        
        if($ble == true){
            //通知服务人员
            $order = $order->toArray();
            PushMessageService::notice($order['userId'], $order['user']['mobile'], 'order.staff', $order, ['sms','app'], 'buyer', '3', $id);
        }
        return $result;
    }*/
    
    /**
     * 服务人员完成订单
     * @param  [type] $userId  [description]
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     */
    public static function completeOrder($staffId, $orderId,$reservationCode) {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.user_confirm_order')
        );
        $order = Order::where('id', $orderId)->where('seller_staff_id', $staffId)->first();
        if (!$order) {//没有订单
            $result['code'] = 60014;
            return $result;
        }
        if($order->reservation_code != $reservationCode){
            $result['code'] = 60115; //预约码不正确
            return $result;
        }       
        //当订单状态为 会员确认,订单已经确认过
        if ($order->status == ORDER_USER_CONFIRM_SERVICE)
        {
            $result['code'] = 60022;
            return $result;
        }
    
       //当订单状态不为 服务完成,订单不能确认
        if ($order->status != ORDER_STATUS_AFFIRM_SERVICE &&
            $order->status != ORDER_STATUS_AFFIRM_ASSIGN_SERVICE &&
            $order->status != ORDER_STATUS_ASSIGN_SERVICE) 
        {
            $result['code'] = 60021;
            return $result;
        }
    
        $order->buyer_confirm_time = UTC_TIME;
        $order->status       = ORDER_USER_CONFIRM_SERVICE;//会员确认

    
    
        DB::beginTransaction();
        try
        {
            $order->save();
//          //更新服务人员余额
//          SellerService::incrementExtend($order->seller_id, 'total_money', $order->seller_fee);
//          SellerService::incrementExtend($order->seller_id, 'money', $order->seller_fee);
//          //减少待到帐金额
//          SellerService::decrementExtend($order->seller_id, 'wait_confirm_money', $order->seller_fee);
    
//          //写入日志
//          SellerMoneyLogService::createLog(
//              $order->seller_id,
//              SellerMoneyLog::TYPE_ORDER_CONFIRM,
//              $orderId,
//              $order->seller_fee,
//              '完成订单:'.$order->sn
//          );
            DB::commit();
            $bln = true;
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
            $bln = false;
        }
    
        if ($bln) {
//           $order = $order->toArray();
//           $result['data'] = $order;
             $result["data"] = self::getOrderById(0, $staffId, $orderId);
//          try {
//              //通知服务人员
//              PushMessageService::notice($order['staff']['userId'], $order['staff']['mobile'], 'order.confirm', $order);
//          } catch (Exception $e) {
//          }
        }
        return $result;
    }


    /**
     * 订单列表
     * @param int $staffId 员工编号
     * @param int $type 订单类型 1:待完成订单 2:历史订单
     * @param int $page 历史订单页码 每页返回5天的数据
     */
    public static function getSchedule($staffId,$type, $page) {
        $lists = [];
        if ($type == 1) {
            //待完成订单状态
            $status = [
                ORDER_STATUS_PAY_SUCCESS,
                ORDER_STATUS_PAY_DELIVERY,
                ORDER_STATUS_AFFIRM_SELLER
            ];
            $data = Order::where('seller_staff_id', $staffId)
                    ->where('order_type', '2')
                    ->whereIn('status', $status)
                    ->with('orderGoods')
                    ->get()->toArray();
        } else {
            $appoint_days = Order::where('seller_staff_id', $staffId)
                            ->where('order_type', '2')
                            ->whereNotIn('status', [
                                ORDER_STATUS_USER_DELETE,
                                ORDER_STATUS_SELLER_DELETE,
                                ORDER_STATUS_ADMIN_DELETE
                            ])->groupBy('app_day')
                            ->orderBy('app_day', 'desc')
                            ->lists('app_day');
            if (count($appoint_days) >= ($page-1)*5) {
                $appoint_days = array_slice($appoint_days,($page-1)*5,5);
                $data = Order::where('seller_staff_id', $staffId)
                    ->where('order_type', '2')
                    ->whereNotIn('status', [
                        ORDER_STATUS_USER_DELETE,
                        ORDER_STATUS_SELLER_DELETE,
                        ORDER_STATUS_ADMIN_DELETE
                    ])->whereIn('app_day', $appoint_days)
                    ->with('orderGoods')->get()->toArray();
            }
        }

        if (count($data) > 0) {
            foreach ($data as $key=>$val) {
                //$isCanMap = $val['map_point'] == '' ? false : true;
                $day = Time::toDate($val['appDay'],'Ymd');
                $list[$day][] = [
                    'id' => $val['id'],
                    'serviceName' => $val['orderGoods'][0]['goodsName'],
                    'name' => $val['name'],
                    'address' => $val['address'],
                    'mobile' => $val['mobile'],
                    'duration' => $val['duration'],
                    'appTime' => Time::toDate($val['appTime'],'Y-m-d H:i:s'),
                    'isCanFinishService' => $val['isCanFinishService'],
                    'orderStatusStr' => $val['orderStatusStr'],
                    'price' => $val['totalFee'],
                    'mapPoint' => $val['map_point']
                ];
            }
            foreach ($list as $k => $v) {
                $lists[] = ['day' => $k, 'list' => $v];
            }


        }
        return $lists;
    }

    /**
     * 订单指派人员
     * @param int $sellerId 商家编号
     * @param int $id 订单编号
     * @param int $staffId 员工编号
     */
    public static function designate($sellerId, $id, $staffId) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg'  => Lang::get('api_staff.success.handle')
        ];

        //允许更改的订单状态
        $order_list = Order::where('id', $id)->where('seller_id', $sellerId)->with('orderGoods')->first();

        if (!$order_list) {
            $result['code'] = 20001;
            return $result;
        }
        $allow_status = [
            ORDER_STATUS_BEGIN_USER,
            ORDER_STATUS_PAY_SUCCESS,
            ORDER_STATUS_PAY_DELIVERY,
            ORDER_STATUS_AFFIRM_SELLER
        ];
        if (!in_array($order_list->status, $allow_status)) {
            $result['code'] = 20004; // 不能指派
            return  $result;
        }
        $check_staff = SellerStaff::where('id', $staffId)->where('status', 1)->where("seller_id", $sellerId)->first();
        if (!$check_staff) {
            $result['code'] = 20005; // 服务人员不存在
            return  $result;
        }
        // 既是服务人员又是配送人员
        if($check_staff->type != 0 && $check_staff->type != 3)
        {
            if ($order_list->order_type != $check_staff->type) {
                $result['code'] = 20006; //服务人员类型错误
                return $result;
            }
        }
        $order = $order_list->toArray();
        if ($order_list->order_type == 2) {
            $goodsIds = GoodsStaff::where('staff_id', $staffId)->lists('goods_id');
            if (!in_array($order['orderGoods'][0]['goodsId'], $goodsIds)) {
                $result['code'] = 20007; //不在服务人员服务范围内
                return $result;
            }
        }
        $is_ok = false;
        DB::beginTransaction();
        try {
            $staff = $check_staff->toArray();
            Order::where('id', $id)->update(['seller_staff_id' => $staffId]);
            DB::commit();
            $is_ok = true;
        } catch(Exception $e) {
            $result['code'] = 99999;
            DB::rollback();
        }
        if($is_ok){
            $old_staff = SellerStaff::where('id', $order['sellerStaffId'])->where('status', 1)->where("seller_id", $sellerId)->first()->toArray();
            if($old_staff &&  $staff['id'] != $old_staff['id']){
                $url = u('staff#Index/index',['id'=>$id,'staffUserId'=>$old_staff['userId'],'newStaffId'=>$old_staff['id'],'isChange'=>2]);
                PushMessageService::notice($old_staff['userId'], $old_staff['mobile'], 'order.changesellerstaff', $order, ['sms','app'], 'staff', 6, $url);//修改前的服务人员推送
            }
            if($staff){
                PushMessageService::notice($staff['userId'], $staff['mobile'], 'order.designate', $order, ['sms','app'], 'staff', 3, $id,'neworder.caf');//修改后的服务人员推送
            }

            $result['data'] = self::getOrderById($sellerId, 0, $id);
        }
        return $result;
    }

    /**
     * 商家经营分析
     * @param int $sellerId 商家编号
     * @param  int $days [类型 最近N天]
     */
    public static function businessStat($sellerId, $days) {
        if ($days > 1) {
            $end_time = UTC_DAY + 24 * 3600 - 1;
            $begin_time = $end_time - (($days - 1) * 24 * 3600);
        } else {
            $begin_time = UTC_DAY;
            $end_time = $begin_time + 24 * 3600 - 1;
        }
        $list = [];
        // $status = array(
        //     ORDER_STATUS_PAY_SUCCESS,
        //     ORDER_STATUS_START_SERVICE,
        //     ORDER_STATUS_PAY_DELIVERY,
        //     ORDER_STATUS_AFFIRM_SELLER,
        //     ORDER_STATUS_FINISH_STAFF,
        //     ORDER_STATUS_FINISH_SYSTEM,
        //     ORDER_STATUS_FINISH_USER
        //     );
        $status = [
            ORDER_STATUS_FINISH_SYSTEM,
            ORDER_STATUS_FINISH_USER,
            ORDER_STATUS_USER_DELETE
        ];
        $result = DB::table('order')->whereBetween('create_time', [$begin_time, $end_time])
                ->whereIn('status', $status)
                ->where('seller_id', $sellerId)
                ->groupBy('create_day')
                // ->select(DB::raw('count(id) as total,sum(seller_fee) as money'),'create_day')
                ->select(DB::raw('count(id) as total,sum(pay_fee + system_full_subsidy + activity_new_money + discount_fee + integral_fee) as money'),'create_day')
                ->get();

        for ($i = 0; $i < $days; $i++) {
            $day = $begin_time + $i * 24 * 3600;
            $day_time = Time::toDate($day, 'd');
            $list[$day_time] = [
                'date' => Time::toDate($day, 'Y-m-d'),
                'num' => 0,
                'money' => sprintf("%.2f",0)
            ];
        }

        foreach ($result as $k=>$v) {
            $day = Time::toDate($v->create_day, 'd');
            $list[$day] = [
                'date' => Time::toDate($v->create_day, 'Y-m-d'),
                'num' => $v->total,
                'money' => sprintf("%.2f", $v->money)
            ];
        }

        return $list;
    }

    /**
     * [checkcode 通过消费码获取订单详细]
     * @param  [type] $sellerId [description]
     * @param  [type] $code     [description]
     * @return [type]           [description]
     */
    public static function checkcode($sellerId, $code) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg'  => null
        ];

        $data = Order::where('seller_id', $sellerId)
                       ->where('auth_code', trim($code))
                       ->where('pay_status', 1)
                       ->where('auth_code_use', 1)
                       ->whereIn('send_way', [2,3])
                       ->first();

        if(empty($data)){
            $result['code'] = 60014; //没有找到相关订单
            return $result;
        }
        else
        {
            //修改订单状态
            try{
                Order::where('auth_code', trim($code))
                       ->where('auth_code_use', 1)
                       ->where('id', $data->id)
                       ->update([
                            'auth_code_use_time' => UTC_TIME,
                            'status' => ORDER_STATUS_AUTH_CODE,
                        ]);
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                $result['code'] = 99999;
                return $result;
            }
            
        }

        $result['data'] = $data;
        return $result;
    }
}