<?php
namespace YiZan\Services;

use YiZan\Models\LogisticsRefund;
use YiZan\Models\Order;
use YiZan\Models\Refund;
use YiZan\Models\User;
use YiZan\Models\UserPayLog;
use YiZan\Models\SellerMoneyLog;

use YiZan\Utils\Time;
use YiZan\Utils\String;
use YiZan\Utils\Helper;
use DB, Exception, Validator, Lang ;

/**
 * 全国店申请退款 udb.dsy
 */
class LogisticsService extends BaseService {
    /**
     * 申请退款
     * @param  [type] $userId [description]
     * @param  [type] $page   [description]
     * @return [type]         [description]
     */
    public static function refund($userId, $orderId,$refundType,$content,$refundExplain,$images,$type = 0) {

        DB::beginTransaction();

        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg' => null,
        );

        $rules = array(
            'userId'          => ['required'],
            'orderId'          => ['required'],
            'refundType'          => ['required'],
            'content'          => ['required']
        );

        $messages = array (
            'userId.required'     => 10000,   // 请填写分类名称
            'orderId.required'     => 10000,   // 请输入排序
            'refundType.required'   => 81000,   // 请选择状态
            'content.required'      => 81001,   // 请选择所属标签
        );

        $validator = Validator::make([
            'userId'          => $userId,
            'orderId'          => $orderId,
            'refundType'          => $refundType,
            'content'        => $content
        ], $rules, $messages);

        //验证信息
        if ($validator->fails())
        {
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        $image = implode(',',$images);
        $order = Order::where('user_id',$userId)->where('id',$orderId)->first();
        if(!$order){
            $result['code'] = '50001';
            return $result;
        }
        $refund = LogisticsRefund::where('user_id',$userId)->where('order_id',$orderId)->first();
        if($refund){
            if($type == 0 && $refund->status == 0){
                $result['code'] = '81002';
                return $result;
            }
        }
        if($type == 0) {
            $whre = [
                ORDER_STATUS_BEGIN_USER,
                ORDER_STATUS_REFUND_AUDITING,
                ORDER_STATUS_CANCEL_REFUNDING,
                ORDER_STATUS_REFUND_HANDLE,
                ORDER_STATUS_REFUND_FAIL,
                ORDER_STATUS_REFUND_SUCCESS,
                ORDER_REFUND_SELLER_AGREE,
                ORDER_REFUND_ADMIN_AGREE
            ];
            if(in_array($order->status,$whre)){
                $result['code'] = '81004';
                return $result;
            }
        }else if($type == 1){
            $whre = [
                ORDER_STATUS_REFUND_FAIL,
                ORDER_REFUND_SELLER_REFUSE,
                ORDER_REFUND_ADMIN_REFUSE
            ];
            if(!in_array($order->status,$whre)){
                $result['code'] = '81004';
                return $result;
            }
        }else{
            $result['code'] = '81004';
            return $result;
        }

        $data = [
            'user_id'           => $userId,
            'sn'                => $order->sn,
            'money'                => $order->pay_fee,
            'order_id'          => $orderId,
            'seller_id'          =>  $order->seller_id,
            'refund_type'       => $refundType == 2 ? 0 :$refundType,
            'content'           => preg_replace('/[\x{10000}-\x{10FFFF}]/u', '',$content),
            'refund_explain'    => preg_replace('/[\x{10000}-\x{10FFFF}]/u', '',$refundExplain),
            'status'            =>   0 ,
            'create_time'       =>  UTC_TIME,
            'create_day'        => UTC_DAY,
            'images'            => $image,
            'order_status'      => $type == 1 ? ORDER_STATUS_AFFIRM_SELLER : $order->status,
        ];
        try {
            if($type == 1) {
                LogisticsRefund::where('user_id',$userId)->where('order_id',$orderId)->delete();
            }
            $id = LogisticsRefund::insertGetId($data);
            if($id){
                $order->refund_images = $image;
                $order->status =  ORDER_STATUS_REFUND_AUDITING;
                $order->refund_time = UTC_TIME;
                $order->refund_content = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '',$content).preg_replace('/[\x{10000}-\x{10FFFF}]/u', '',$refundExplain);
                $order->save();
            }
            self::create($id);
            $result['code'] = '81003';
            DB::commit();
        }catch (Exception $e){
            $result['code'] = '60307';
            DB::rollback();
        }
        return $result;

    }
    /**
     * 申请退款
     * @param  [type] $userId [description]
     * @param  [type] $page   [description]
     * @return [type]         [description]
     */
    public static function refundById($userId, $orderId) {
        $refund = LogisticsRefund::where('user_id',$userId)->with('order.users')->where('order_id',$orderId)->first();
        if($refund){
            $refund = $refund->toArray();
        }
        return $refund;
    }


    /**
     * 申请退款
     * @param  [type] $userId [description]
     * @param  [type] $page   [description]
     * @return [type]         [description]
     */
    public static function refundStaffById($sellerId, $orderId) {

        $refund = LogisticsRefund::where('seller_id',$sellerId)->with('order.users')->where('order_id',$orderId)->first();
        if($refund){
            $refund = $refund->toArray();
        }
        return $refund;
    }

    /**
     * 申请退款
     * @param  [type] $userId [description]
     * @param  [type] $page   [description]
     * @return [type]         [description]
     */
    public static function refundsave($sellerId, $id,$orderId,$status) {

        $result = [
            'code' => 0,
            'data' => null,
            'msg' => null
        ];
        $order = Order::where('seller_id',$sellerId)->with("seller")->where('id',$orderId)->first();
        if(!$order){
            $result['code'] = '20001';
            return $result;
        }
        $refund = LogisticsRefund::where('seller_id',$sellerId)->where('order_id',$orderId)->where('id',$id);

        if($status == 1){
            $refund ->where('status',0);
        };
        $refund = $refund->first();
        if(!$refund){
            $result['code'] = '81002';
            return $result;
        }else{
            DB::beginTransaction();
            try {
                if($status == 4){
                    $refund->status = $status;
                    $refund->staff_dispose_time = UTC_TIME;
                    $refund->save();
                    $order->status =  ORDER_REFUND_SELLER_REFUSE_LOGISTICS;
                    $order->dispose_refund_seller_time = UTC_TIME;
                    $order->save();
                    //self::orderrund($order,$refund,$order->id);
                }else{
                    if($status == 1){
                        if($refund->refund_type == 0){
                            //self::orderrund($order,$refund,$order->id);
                        }
                        $order->status =  ORDER_REFUND_SELLER_AGREE;
                    }else{
                        $order->status =  ORDER_REFUND_SELLER_REFUSE;
                    }
                    $refund->status = $status;
                    $refund->seller_address = $order->seller['contacts'].','.$order->seller['mobile'].','.$order->seller->refund_address;
                    $refund->seller_dispose_time = UTC_TIME;
                    $refund->save();

                    $order->dispose_refund_seller_time = UTC_TIME;
                    $order->save();
                    self::create($refund->id);
                }
                DB::commit();
            }catch (Exception $e){
                $result['code'] = '81005';
                DB::rollback();
            }
            return $result;
        }

    }
    public function  orderrund($id,$status,$content=""){

        $result = [
            'code' =>0,
            'msg' => '',
            'data' => ''
        ];
        $refund = LogisticsRefund::where('id',$id)->first();

        if($refund->refund_type == 0){
            if($refund->status != 1){
                $result['code'] = '10000';
                return $result;
            }
        }
        if($refund->refund_type == 1){
            if($refund->status != 4){
                $result['code'] = '10000';
                return $result;
            }
        }

        $order = Order::where('id',$refund->order_id)->first();
        if(!$order){
            $result['code'] = '50001';
            return $result;
        }
        if($refund->refund_type == 1){

            if($order->status != ORDER_REFUND_SELLER_REFUSE_LOGISTICS){
                $result['code'] = '10000';
                return $result;
            }
        }

        if($refund->refund_type == 0){

            if($order->status != ORDER_REFUND_SELLER_AGREE){
                $result['code'] = '10000';
                return $result;
            }
        }
        if($status == 6){

            if($content == ""){
                $result['code'] = '10000';
                return $result;
            }
            DB::beginTransaction();
            try {
                $refund->admin_dispose_content = $content;
                $refund->admin_dispose_time = UTC_TIME;
                $refund->status  = 6;
                $refund->save();
                $order->status = ORDER_REFUND_ADMIN_REFUSE;


                $order->dispose_refund_time = UTC_TIME;
                $order->dispose_refund_remark = $content;
                $order->save();
                self::create($refund->id);
                DB::commit();
            }catch (Exception $e){
                $result['code'] = '40104';
                DB::rollback();
            }

            $result['code'] = 0;
            return $result;
        }
        $orderId = $refund->order_id;
        //是否设置退还到余额
        $isRefundBalance = \YiZan\Models\SystemConfig::where('code', 'is_refund_balance')->pluck('val');
        DB::beginTransaction();
        try {
//            //如果未支付订单 包含余额支付金额 则退款
//            if($order->status == ORDER_REFUND_SELLER_AGREE && $order->pay_money > 0.0001){
//                //返还支付金额给会员余额
//                $user = User::find($order->user_id);
//                $user->balance = $user->balance + abs($order->pay_money);
//                $user->save();
//                //创建退款日志
//                $userPayLog = new UserPayLog;
//                $userPayLog->payment_type   = 'balancePay';
//                $userPayLog->pay_type       = 3;//退款
//                $userPayLog->user_id        = $order->user_id;
//                $userPayLog->order_id       = $order->id;
//                $userPayLog->activity_id    = $order->activity_id > 0 ? $order->activity_id : 0;
//                $userPayLog->seller_id      = $order->seller_id;
//                $userPayLog->money          = $order->pay_money;
//                $userPayLog->balance        = $user->balance;
//                $userPayLog->content        = $refund->content;
//                $userPayLog->create_time    = UTC_TIME;
//                $userPayLog->create_day     = UTC_DAY;
//                $userPayLog->status         = 1;
//                $userPayLog->sn = Helper::getSn();
//                $userPayLog->save();
//            }

            //写入退款日志
            if (
                $order->pay_fee >= 0.0001 &&
                $order->pay_status == ORDER_PAY_STATUS_YES &&
                $order->isCashOnDelivery === false &&
                $order->status == ORDER_REFUND_SELLER_AGREE
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
                                "content" => $refund->content,
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
                                "content" => $refund->content,
                                "create_time" => UTC_TIME,
                                "create_day" => UTC_DAY,
                                "status" => 0
                            ];
                        }

                        if ($isRefundBalance == 1) {
                            $userRefundLog[$k]['status'] = 1;
                            $userRefundLog[$k]['content'] = '用户取消,'.$refund->content;
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
                            $userPayLog[$k] = [
                                'payment_type'  => $v['paymentType'],
                                'pay_type'       => 3,//退款
                                'user_id'        => $v['userId'],
                                'order_id'       => $order->id,
                                'activity_id'    => $order->activity_id > 0 ? $order->activity_id : 0,
                                'seller_id'      => $order->seller_id,
                                'money'           => $v['money'],
                                'balance'         => $user->balance,
                                'content'         =>  $refund->content,//'会员取消订单退款',
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

                    $refundId = DB::table('refund')->insertGetId($userRefundLog[0]);

                    SellerMoneyLogService::createLog(
                        $order->seller_id,
                        SellerMoneyLog::TYPE_ORDER_REFUND,
                        $orderId,
                        $order->pay_fee,
                        '订单取消，退款：' . $order->sn
                    );

                    $refund->admin_dispose_time = UTC_TIME;
                    $refund->admin_dispose_content = $content;
                    $refund->status = 5;
                    $refund->refund_id = $refundId;
                    $refund->save();

                    if($userRefundLog[0]['status'] == 1){
                        $order->status = ORDER_STATUS_REFUND_SUCCESS;
                    }else{
                        $order->status = ORDER_REFUND_ADMIN_AGREE;
                    }
                    $order->dispose_refund_time = UTC_TIME;
                    $order->dispose_refund_remark = $content;
                    self::create($refund->id);
                    $order->save();
                }else{
                    $result['code'] = '40104';
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
            DB::commit();
        }catch (Exception $e){
            $result['code'] = '40104';
            DB::rollback();
        }
        $result['data'] = Refund::where('id',$refundId)->first();
        return $result;
    }
    /**
     * 申请退款
     * @param  [type] $userId [description]
     * @param  [type] $page   [description]
     * @return [type]         [description]
     */



    public static function refunddispose($sellerId,$id, $orderId,$status,$content,$refundExplain,$images) {


        DB::beginTransaction();

        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg' => null,
        );

        $rules = array(
            'sellerId'          => ['required'],
            'orderId'          => ['required'],
            'content'          => ['required']
        );

        $messages = array (
            'sellerId.required'     => 10000,   // 请填写分类名称
            'orderId.required'     => 10000,   // 请输入排序
            'content.required'      => 81001,   // 请选择所属标签
        );

        $validator = Validator::make([
            'sellerId'          => $sellerId,
            'orderId'          => $orderId,
            'content'        => $content
        ], $rules, $messages);

        //验证信息
        if ($validator->fails())
        {
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        $order = Order::where('seller_id',$sellerId)->where('id',$orderId)->first();
        if(!$order){
            $result['code'] = '50001';
            return $result;
        }
        $refund = LogisticsRefund::where('seller_id',$sellerId)->where('order_id',$orderId)->where('id',$id)->first();
        if(!$refund){
            $result['code'] = '81002';
            return $result;
        }else{

            $image = implode(',',$images);
            try {
                if($status == 2){
                    $order->status =  ORDER_REFUND_SELLER_REFUSE;
                    $order->dispose_refund_seller_time = UTC_TIME;
                    $order->dispose_refund_seller_remark = $content.','.$refundExplain;
                    $order->save();

                    $refund->seller_dispose_time = UTC_TIME;
                    $refund->seller_dispose_content = $content;
                    $refund->seller_dispose_explain = $refundExplain;
                    $refund->seller_dispose_images = $image;
                    $refund->status = $status;
                    $refund->save();
                    $result['code'] = '81006';
                }
                DB::commit();
            }catch (Exception $e){
                $result['code'] = '60307';
                DB::rollback();
            }
            return $result;
        }

    }

    /**
     * 发货
     * @param  [type] $userId [description]
     * @param  [type] $page   [description]
     * @return [type]         [description]
     */

    public static function userrefunddispose($userId,$id, $orderId,$status,$company,$code,$number,$images) {


        DB::beginTransaction();

        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg' => null,
        );

        $rules = array(
            'userId'          => ['required'],
            'orderId'          => ['required']
        );

        $messages = array (
            'userId.required'     => 10000,   // 请填写分类名称
            'orderId.required'     => 10000,   // 请输入排序
        );

        $validator = Validator::make([
            'userId'          => $userId,
            'orderId'          => $orderId,
        ], $rules, $messages);

        //验证信息
        if ($validator->fails())
        {
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        $image = implode(',',$images);
        $order = Order::where('user_id',$userId)->where('id',$orderId)->first();
        if(!$order){
            $result['code'] = '50001';
            return $result;
        }
        $refund = LogisticsRefund::where('user_id',$userId)->where('order_id',$orderId)->where('id',$id)->first();
        if(!$refund){
            $result['code'] = '81002';
            return $result;
        }else{

            try {
                if($status == 3){
                    $order->status =  ORDER_REFUND_USER_REFUSE_LOGISTICS;
                    $order->save();

                    $refund->user_dispose_time = UTC_TIME;
                    $refund->user_dispose_name = $company;
                    $refund->user_dispose_code = $code;
                    $refund->user_dispose_number = $number;
                    $refund->user_dispose_images = $image;
                    $refund->status = $status;

                    $refund->save();
                }
                $result['code'] = '81003';
                DB::commit();
            }catch (Exception $e){
                $result['code'] = '60307';
                DB::rollback();
            }
            return $result;
        }

    }

    public static function refundDel($userId,$id) {


        DB::beginTransaction();

        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg' => null,
        );

        $rules = array(
            'userId'          => ['required'],
            'id'          => ['required'],
        );

        $messages = array (
            'userId.required'     => 10000,   // 请填写分类名称
            'id.required'     => 10000,   // 请输入排序
        );

        $validator = Validator::make([
            'userId'          => $userId,
            'id'          => $id
        ], $rules, $messages);

        //验证信息
        if ($validator->fails())
        {
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        $refund = LogisticsRefund::where('user_id',$userId)->where('id',$id)->first();
        $order = Order::where('user_id',$userId)->where('seller_id',$refund->seller_id)->where('id',$refund->order_id)->first();
        if(!$order){
            $result['code'] = '50001';
            return $result;
        }

        if(!$refund){
            $result['code'] = '81002';
            return $result;
        }else{
            try {
                $order->status = $refund->order_status;
                $order->save();
                $refund->delete();
                $result['code'] = '81003';
                DB::commit();
            }catch (Exception $e){
                $result['code'] = '60307';
                DB::rollback();
            }
            return $result;
        }

    }

    /**
     * 添加推送信息
     * @param string $type 类型 buyer 买家 seller卖家
     * @param string $title 标题
     * @param string $content 内容
     * @param int $userType 要推送的会员类型
     * @param string $users 要推送的会员编号
     * @param string $args 推送参数
     * @param string $sendType 推送类型 1:普通信息 2:html args为url地址 3:订单信息 args为订单ID
     * @return array   创建结果
     */
    public static function create($id) {

        $refund = LogisticsRefund::where('id',$id)->first();
        $title = Lang::get("api_system.refund.".$refund->status);
        $type = [
            0 => '您申请了退款请求，等待商家确认',
            1 => '退货地址：'.$refund->seller_address,
            2 => '买家拒绝了本次申请退款;拒绝原因'.$refund->seller_dispose_content,
            3 => '等待商家收货',
            4 => '商家确同意退款，等到平台确认',
            5 => '平台同意退款',
            6 => '平台拒绝退款;拒绝原因'.$refund->admin_dispose_content,
        ];
        $content = $type[$refund->status];
        $types = "buyer";

        $urls = [
            0 => u('wap#Order/detail',['id'=>$refund->order_id]),
            1 => u('wap#Logistics/refundview',['orderId'=>$refund->order_id]),
            2 => u('wap#Logistics/refundview',['orderId'=>$refund->order_id]),
            3 => u('wap#Logistics/refundview',['orderId'=>$refund->order_id]),
            4 => u('wap#Logistics/refundview',['orderId'=>$refund->order_id]),
            5 => u('wap#Logistics/refundview',['orderId'=>$refund->order_id]),
            6 => u('wap#Logistics/refundview',['orderId'=>$refund->order_id]),
        ];
        $url =  $urls[$refund->status];


        $order = Order::where('id',$refund->order_id)->first();
        PushMessageService::create($types, $title, $content, 1, $refund->user_id,$url,3,$order->id,$order->status);
    }
}
