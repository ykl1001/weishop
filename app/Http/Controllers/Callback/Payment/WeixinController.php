<?php 
namespace YiZan\Http\Controllers\Callback\Payment;

use YiZan\Http\Controllers\Callback\BaseController;
use YiZan\Models\UserPayLog;
use YiZan\Models\SellerPayLog;
use YiZan\Models\Order;
use YiZan\Models\PropertyOrder; 
use YiZan\Models\PropertyUser; 
use YiZan\Models\PropertyOrderItem; 
use YiZan\Models\PropertyFee; 
use YiZan\Models\Payment;
use YiZan\Models\User;
use YiZan\Models\SellerExtend;
use YiZan\Models\SellerMoneyLog;

use YiZan\Services\SellerService;
use YiZan\Services\SellerMoneyLogService;
use YiZan\Services\PushMessageService;
use YiZan\Services\ActivityService;
use YiZan\Services\OrderService;
use Illuminate\Database\Query\Expression;

use YiZan\Models\LiveLog;
use YiZan\Services\LiveService;
use YiZan\Models\Refund;

use DB, Exception;

/**
 * 微信支付
 */
class WeixinController extends BaseController {
    public function notify() {
        $this->_notify('weixin');
    }

    public function notifys() {
        $this->_notifys('weixin');
    }

    public function jsnotify() {
        $this->_notify('weixinJs');
    } 

    public function propertynotify() {
        $this->_notify('weixinJs');
    }

    public function propertyappnotify() {
        $this->_notify('weixin');
    }

    private function _notify($type) {
        $request = file_get_contents('php://input', 'r');
        if (empty($request)) {
            die('<xml>
                   <return_code><![CDATA[FAIL]]></return_code>
                   <return_msg><![CDATA[参数错误]]></return_msg>
                </xml>');
        }

        $payment = Payment::where('code', $type)->first()->toArray();
        $payment = $payment['config'];

        $xml = (array)@simplexml_load_string($request, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$xml || 
            !isset($xml['appid']) || 
            !isset($xml['mch_id']) || 
            $xml['appid'] != $payment['appId'] || 
            $xml['mch_id'] != $payment['partnerId']) {
            die('<xml>
                   <return_code><![CDATA[FAIL]]></return_code>
                   <return_msg><![CDATA[参数错误]]></return_msg>
                </xml>');
        }

        if ($xml['result_code'] != 'SUCCESS') {
            die('<xml>
                   <return_code><![CDATA[SUCCESS]]></return_code>
                   <return_msg><![CDATA[OK]]></return_msg>
                </xml>');
        }

        $sign = $xml['sign'];
        $args = '';

        unset($xml['sign']);
        ksort($xml);
        foreach ($xml as $key => $data) {
            $args .= "{$key}={$data}&";
        }
        
        $args = strtoupper(md5("{$args}key={$payment['partnerKey']}"));
        if ($args != $sign) {
            die('<xml>
                   <return_code><![CDATA[FAIL]]></return_code>
                   <return_msg><![CDATA[签名错误]]></return_msg>
                </xml>');
        }

        $userPayLog = UserPayLog::where('sn', $xml['out_trade_no'])->first();
        if (!$userPayLog) {
            die('<xml>
                   <return_code><![CDATA[FAIL]]></return_code>
                   <return_msg><![CDATA[找不到支付日志]]></return_msg>
                </xml>');
        }
        if($userPayLog->status == 1){
            die('<xml>
                   <return_code><![CDATA[FAIL]]></return_code>
                   <return_msg><![CDATA[该订单已支付，请勿重复刷单]]></return_msg>
                </xml>');
        }

        if($userPayLog->is_fx == 1){
            $this->rechargefx($xml, $userPayLog); //充值
        }else{
            if($userPayLog->pay_type == 6){
                $this->live($xml, $userPayLog); //充值
            } elseif($userPayLog->pay_type == 8) {
                $this->propertyOrder($xml, $userPayLog); //物业订单
            }else{
                if($userPayLog->activity_id < 1){
                    if ($userPayLog->order_id < 1) {
                        $this->recharge($xml, $userPayLog); //充值
                    } else {
                        $this->order($xml, $userPayLog); //订单
                    }
                }else{
                    $this->activity($xml, $userPayLog); //活动
                }
            }
        }
        
    }

    /**
     * 商家支付回调通知
     */
    private function _notifys($type) {
        $request = file_get_contents('php://input', 'r');
        if (empty($request)) {
            die('<xml>
                   <return_code><![CDATA[FAIL]]></return_code>
                   <return_msg><![CDATA[参数错误]]></return_msg>
                </xml>');
        }

        $payment = Payment::where('code', $type)->first()->toArray();
        $payment = $payment['config'];

        $xml = (array)@simplexml_load_string($request, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$xml ||
            !isset($xml['appid']) ||
            !isset($xml['mch_id']) ||
            $xml['appid'] != $payment['appId'] ||
            $xml['mch_id'] != $payment['partnerId']) {
            die('<xml>
                   <return_code><![CDATA[FAIL]]></return_code>
                   <return_msg><![CDATA[参数错误]]></return_msg>
                </xml>');
        }

        if ($xml['result_code'] != 'SUCCESS') {
            die('<xml>
                   <return_code><![CDATA[SUCCESS]]></return_code>
                   <return_msg><![CDATA[OK]]></return_msg>
                </xml>');
        }

        $sign = $xml['sign'];
        $args = '';

        unset($xml['sign']);
        ksort($xml);
        foreach ($xml as $key => $data) {
            $args .= "{$key}={$data}&";
        }
        
        $args = strtoupper(md5("{$args}key={$payment['partnerKey']}"));
        if ($args != $sign) {
            die('<xml>
                   <return_code><![CDATA[FAIL]]></return_code>
                   <return_msg><![CDATA[签名错误]]></return_msg>
                </xml>');
        }

        $sellerPayLog = SellerPayLog::where('sn', $xml['out_trade_no'])->first();
        if (!$sellerPayLog) {
            die('<xml>
                   <return_code><![CDATA[FAIL]]></return_code>
                   <return_msg><![CDATA[找不到支付日志]]></return_msg>
                </xml>');
        }
        if($sellerPayLog->status == 1){
            die('<xml>
                   <return_code><![CDATA[FAIL]]></return_code>
                   <return_msg><![CDATA[该订单已支付，请勿重复刷单]]></return_msg>
                </xml>');
        }

        $sellerPayLog->pay_account  = $xml['openid'];
        $sellerPayLog->pay_time     = UTC_TIME;
        $sellerPayLog->pay_day      = UTC_DAY;
        $sellerPayLog->status       = 1;
        $sellerPayLog->trade_no     = $xml['transaction_id'];

        DB::beginTransaction();
        try {
            $sellerPayLog->save();
            //更新服务人员余额
            SellerExtend::where('seller_id', $sellerPayLog->seller_id)
                        ->update([
                            'money'       => new Expression("money + " . $sellerPayLog->money), 
                            'total_money' => new Expression("total_money + " . $sellerPayLog->money), 
                        ]); 

            // 写入日志
            SellerMoneyLogService::createLog(
                $sellerPayLog->seller_id, 
                SellerMoneyLog::TYPE_SELLER_RECHARGE, 
                $sellerPayLog->id, 
                $sellerPayLog->money, 
                '充值:'.$sellerPayLog->sn,
                1
                );
            DB::commit(); 
        } catch (Exception $e) {
            DB::rollback(); 
            die('<xml>
                   <return_code><![CDATA[FAIL]]></return_code>
                   <return_msg><![CDATA[更新订单失败]]></return_msg>
                </xml>');
        }
        die('<xml>
               <return_code><![CDATA[SUCCESS]]></return_code>
               <return_msg><![CDATA[OK]]></return_msg>
            </xml>');
        
    }

    /**
     * @param $xml
     * @param $userPayLog
     */
    public function live($xml,$userPayLog){
        DB::beginTransaction();
        try {
            //修改状态
            $status = UserPayLog::where('id', $userPayLog->id)->update([
                'pay_time'                  => UTC_TIME,
                'pay_day'                   => UTC_DAY,
                'status'                    => 1,
                'trade_no'                  => $xml['transaction_id']
            ]);

            if ($status) {
                $result = LiveService::getOrder($userPayLog->sn);
                if($result['Code'] != 0){//退款

                    $liveLog = liveLog::where('sn', $_REQUEST['orderid'])->with('user')->first();
                    $userPayLog = UserPayLog::where('sn', $_REQUEST['orderid'])->first()->toArray();
                    if($liveLog->money != $userPayLog->money && $liveLog->money >$userPayLog->money){
                        //加余额
                        $money = $liveLog->money-$userPayLog->money;
                        User::where('id',$userPayLog->user_id)->increment('balance', $money);
                    }
                    $refund = new Refund;
                    $refund->user_id        = $userPayLog['userId'];
                    $refund->order_id       = 0;
                    $refund->seller_id      = 0;
                    $refund->content        = '生活缴费';
                    $refund->money          = $userPayLog['money'];
                    $refund->create_time    = UTC_TIME;
                    $refund->create_day     = UTC_DAY;
                    $refund->status         = 0;
                    $refund->sn             = $userPayLog['sn'];
                    $refund->trade_no       = $userPayLog['tradeNo'];
                    $refund->payment_type   = $userPayLog['paymentType'];
                    $refund->save();
                    //修改状态失败
                    LiveLog::where('sn', $userPayLog->sn)->update([
                        'is_pay'                    => '-1'
                    ]);
                    //充值失败
                    $liveLog = $liveLog->toArray();
                    $extend = json_decode(base64_decode($liveLog['extend']),true);
                    if(empty($extend)){
                        die('fail');
                    }
                    $live_type = ['1'=>'水','2'=>'电','3'=>'燃气'];
                    $live_state = ['失败','成功'];
                    $live_arrs['type'] = $live_type[$extend['type']];
                    $live_arrs['account'] = $extend['account'];
                    $live_arrs['state'] = $live_state[0];
                    PushMessageService::notice($liveLog['user']['id'], $liveLog['user']['mobile'], 'order.live', $live_arrs,['app'],'buyer', 1, 0);

                }else{
                    //修改状态
                    LiveLog::where('sn', $xml['transaction_id'])->update([
                        'is_pay'                    => 1
                    ]);
                }
            }
            DB::commit();
        }
        catch (Exception $e) {
            DB::rollback();
            $status = false;
        }

    }

    /**
     * [order 订单]
     * @param  [type] $userPayLog [description]
     * @return [type]             [description]
     */
    private function order($xml, $userPayLog) {
        $order = Order::find($userPayLog->order_id);
        if (!$order) {
            die('<xml>
                       <return_code><![CDATA[FAIL]]></return_code>
                       <return_msg><![CDATA[找不到订单]]></return_msg>
                    </xml>');
        }

        if ($order->pay_status == 1) {
            die('<xml>
                       <return_code><![CDATA[FAIL]]></return_code>
                       <return_msg><![CDATA[订单已支付]]></return_msg>
                    </xml>');
        }

        /* if ($order->status != ORDER_STATUS_WAIT_PAY) {
        die('<xml>
        <return_code><![CDATA[FAIL]]></return_code>
        <return_msg><![CDATA[订单不能支付]]></return_msg>
        </xml>');
        }*/

        $userPayLog->pay_account = $xml['openid'];
        $userPayLog->pay_time    = UTC_TIME;
        $userPayLog->pay_day     = UTC_DAY;
        $userPayLog->status      = 1;
        $userPayLog->trade_no    = $xml['transaction_id'];

        DB::beginTransaction();
        try {
            $status = $userPayLog->save();
            if ($status) {
                $endTime    = UTC_TIME + (int)\YiZan\Services\SystemConfigService::getConfigByCode('system_seller_order_confirm') * 60;
                //修改状态
                $status = Order::where('id', $userPayLog->order_id)->update([
                        'pay_time'                  => UTC_TIME,
                        'pay_status'                => ORDER_PAY_STATUS_YES,
                        'status'                    => ORDER_STATUS_PAY_SUCCESS,
                        'pay_type'                  => $userPayLog->payment_type,
                        'cancel_remark'             => ''
                    ]);

                if ($status) {
                    //更新服务人员待到帐金额
                    SellerService::incrementExtend($order->seller_id, 'wait_confirm_money', $order->seller_fee);

                    //写入日志
                    SellerMoneyLogService::createLog(
                            $order->seller_id, 
                            'order_pay', 
                            $order->id, 
                            $order->pay_fee,
                            '订单支付:'.$order->sn,
                            1
                        );

                    if(IS_OPEN_FX){
                        $datas['user_id'] = $order->user_id;
                        \YiZan\Services\Buyer\OrderService::invitationOrder($order->id,$datas,$order->is_all,$order->share_user_id);
                    }
                }

            }
            DB::commit();
        }
        catch (Exception $e) {
            DB::rollback();
            $status = false;
        }

        if ($status) {
            $order = OrderService::getOrderById($order->user_id, $order->id);
            try {
                PushMessageService::notice( $order['seller']['userId'],  $order['seller']['mobile'], 'order.pay',  $order,['sms', 'app'],'seller','3',$order['id'], "neworder.caf");
                if($order['staff'] && $order['seller']['userId'] != $order['staff']['userId']){
                    PushMessageService::notice( $order['staff']['userId'],  $order['staff']['mobile'], 'order.pay',  $order,['sms', 'app'],'staff','3',$order['id'], "neworder.caf");
                }
            } catch (Exception $e) {

            }
            die('<xml>
                   <return_code><![CDATA[SUCCESS]]></return_code>
                   <return_msg><![CDATA[OK]]></return_msg>
                </xml>');
        } else {
            die('<xml>
                   <return_code><![CDATA[FAIL]]></return_code>
                   <return_msg><![CDATA[更新订单失败]]></return_msg>
                </xml>');
        }
    }

    /**
     * [propertyOrder 订单]
     * @param  [type] $userPayLog [description]
     * @return [type]             [description]
     */
    private function propertyOrder($xml, $userPayLog) {
        $order = PropertyOrder::find($userPayLog->order_id);
        if (!$order) {
            die('<xml>
                       <return_code><![CDATA[FAIL]]></return_code>
                       <return_msg><![CDATA[找不到订单]]></return_msg>
                    </xml>');
        }

        if ($order->pay_status == 1) {
            die('<xml>
                       <return_code><![CDATA[FAIL]]></return_code>
                       <return_msg><![CDATA[订单已支付]]></return_msg>
                    </xml>');
        }

        /* if ($order->status != ORDER_STATUS_WAIT_PAY) {
        die('<xml>
        <return_code><![CDATA[FAIL]]></return_code>
        <return_msg><![CDATA[订单不能支付]]></return_msg>
        </xml>');
        }*/

        $userPayLog->pay_account = $xml['openid'];
        $userPayLog->pay_time    = UTC_TIME;
        $userPayLog->pay_day     = UTC_DAY;
        $userPayLog->status      = 1;
        $userPayLog->trade_no    = $xml['transaction_id'];

        DB::beginTransaction();
        try {
            $status = $userPayLog->save();
            if ($status) {
                $endTime    = UTC_TIME + (int)\YiZan\Services\SystemConfigService::getConfigByCode('system_seller_order_confirm') * 60;
                //修改状态
                $status = PropertyOrder::where('id', $userPayLog->order_id)->update([
                        'pay_time'                  => UTC_TIME,
                        'pay_status'                => ORDER_PAY_STATUS_YES, 
                    ]);

                if ($status) {
                    $propertyUser = PropertyUser::where('user_id', $userPayLog->user_id)
                                                ->where('seller_id', $userPayLog->seller_id)
                                                ->first();
                    //修改物业费项目
                    $propertyFeeIds = PropertyOrderItem::where('order_id', $order->id)
                                                       ->lists('propertyfee_id');
                    PropertyFee::whereIn('id', $propertyFeeIds)
                               ->update(['status'=>1,'pay_time'=>UTC_TIME, 'puser_id'=>$propertyUser->id]);
                    //修改商家金额 
                    SellerService::incrementExtend($userPayLog->seller_id, 'money', $userPayLog->getMoney());
                    //添加商家资金日志 
                    SellerMoneyLogService::createLog(
                            $userPayLog->seller_id,
                            SellerMoneyLog::TYPE_PROPERTY_FEE,
                            $userPayLog->user_id,
                            $userPayLog->getMoney(),
                            '物业缴费',
                            1
                        );
                }

            }
            DB::commit();
        }
        catch (Exception $e) {
            DB::rollback();
            $status = false;
        }

        if ($status) { 
            die('<xml>
                   <return_code><![CDATA[SUCCESS]]></return_code>
                   <return_msg><![CDATA[OK]]></return_msg>
                </xml>');
        } else {
            die('<xml>
                   <return_code><![CDATA[FAIL]]></return_code>
                   <return_msg><![CDATA[更新订单失败]]></return_msg>
                </xml>');
        }
    }


    /**
     * [activity 活动]
     * @param  [type] $userPayLog [description]
     * @return [type]             [description]
     */
    private function activity($xml, $userPayLog) {
        $userPayLog->pay_account = $xml['openid'];
        $userPayLog->pay_time    = UTC_TIME;
        $userPayLog->pay_day     = UTC_DAY;
        $userPayLog->status      = 1;
        $userPayLog->trade_no    = $xml['transaction_id'];

        DB::beginTransaction();
        try {
            $status = $userPayLog->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $status = false;
        }

        if ($status) {
            ActivityService::payfinish($userPayLog->user_id, $userPayLog->sn, $userPayLog->activity_id);
            die('<xml>
                   <return_code><![CDATA[SUCCESS]]></return_code>
                   <return_msg><![CDATA[OK]]></return_msg>
                </xml>');
        } else {
            die('<xml>
                   <return_code><![CDATA[FAIL]]></return_code>
                   <return_msg><![CDATA[更新订单失败]]></return_msg>
                </xml>');
        }
    }

    /**
     * [recharge 充值]
     * @param  [type] $request    [description]
     * @param  [type] $userPayLog [description]
     * @return [type]             [description]
     */
    private function recharge($xml, $userPayLog) {
        if ($userPayLog->status == 1) {
            die('<xml>
                   <return_code><![CDATA[FAIL]]></return_code>
                   <return_msg><![CDATA[已充值]]></return_msg>
                </xml>');
        }
        $user = User::find($userPayLog->user_id);
        if (!$user) {
            die('<xml>
                   <return_code><![CDATA[FAIL]]></return_code>
                   <return_msg><![CDATA[找不到会员]]></return_msg>
                </xml>');
        }

        $userPayLog->pay_account = $xml['openid'];
        $userPayLog->pay_time    = UTC_TIME;
        $userPayLog->pay_day     = UTC_DAY;
        $userPayLog->status      = 1;
        $userPayLog->trade_no    = $xml['transaction_id'];
        $userPayLog->balance     = $user->balance + $userPayLog->money;

        DB::beginTransaction();
        try {
            $userPayLog->save();
            User::where('id', $userPayLog->user_id)->update([
                    'balance'       => new Expression("balance + " . $userPayLog->money), 
                    'total_money' => new Expression("total_money + " . $userPayLog->money), 
                ]);
            DB::commit();
            $status = true;
        } catch (Exception $e) {
            DB::rollback();
            $status = false;
        }
        if ($status) {
            die('<xml>
                   <return_code><![CDATA[SUCCESS]]></return_code>
                   <return_msg><![CDATA[OK]]></return_msg>
                </xml>');
        } else {
            die('<xml>
                   <return_code><![CDATA[FAIL]]></return_code>
                   <return_msg><![CDATA[充值失败]]></return_msg>
                </xml>');
        }
    }

    /**
     * [recharge 充值]
     * @param  [type] $request    [description]
     * @param  [type] $userPayLog [description]
     * @return [type]             [description]
     */
    private function rechargefx($xml, $userPayLog) {
        if ($userPayLog->status == 1) {
            die('<xml>
                   <return_code><![CDATA[FAIL]]></return_code>
                   <return_msg><![CDATA[已缴费]]></return_msg>
                </xml>');
        }
        $user = User::find($userPayLog->user_id);
        if (!$user) {
            die('<xml>
                   <return_code><![CDATA[FAIL]]></return_code>
                   <return_msg><![CDATA[找不到会员]]></return_msg>
                </xml>');
        }

        $userPayLog->pay_account = $xml['openid'];
        $userPayLog->pay_time    = UTC_TIME;
        $userPayLog->pay_day     = UTC_DAY;
        $userPayLog->status      = 1;
        $userPayLog->trade_no    = $xml['transaction_id'];

        DB::beginTransaction();
        try {
            $userPayLog->save();
            User::where('id', $userPayLog->user_id)->update(['is_pay'       => 1 ]);
            DB::commit();
            $status = true;
        } catch (Exception $e) {
            DB::rollback();
            $status = false;
        }
        if ($status) {
            die('<xml>
                   <return_code><![CDATA[SUCCESS]]></return_code>
                   <return_msg><![CDATA[OK]]></return_msg>
                </xml>');
        } else {
            die('<xml>
                   <return_code><![CDATA[FAIL]]></return_code>
                   <return_msg><![CDATA[缴费失败]]></return_msg>
                </xml>');
        }
    }

    
}
