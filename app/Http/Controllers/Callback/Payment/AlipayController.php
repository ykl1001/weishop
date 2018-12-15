<?php 
namespace YiZan\Http\Controllers\Callback\Payment;

use YiZan\Http\Controllers\Callback\BaseController;
use YiZan\Models\UserPayLog;
use YiZan\Models\SellerPayLog;
use YiZan\Models\Order;
use YiZan\Models\PropertyOrder; 
use YiZan\Models\PropertyOrderItem; 
use YiZan\Models\PropertyFee; 
use YiZan\Models\PropertyUser; 
use YiZan\Models\Payment;
use YiZan\Models\User;
use YiZan\Models\SellerExtend;
use YiZan\Models\SellerMoneyLog;

use YiZan\Models\LiveLog;
use YiZan\Services\LiveService;
use YiZan\Models\Refund;

use YiZan\Services\PaymentService;
use YiZan\Services\SellerService;
use YiZan\Services\SellerMoneyLogService;
use YiZan\Services\PushMessageService;
use YiZan\Services\ActivityService;
use YiZan\Services\OrderService;
use Illuminate\Database\Query\Expression;

use DB, 
    Exception,
    YiZan\Models\UserRefundLog;

/**
 * 微信支付
 */
class AlipayController extends BaseController {

    /**
     * 会员订单手机端回调
     */
    public function appnotify() {
        
        
        if (empty($_REQUEST['notify_id']) || 
            empty($_REQUEST['seller_id']) || 
            empty($_REQUEST['out_trade_no']) || 
            empty($_REQUEST['sign'])) {
            die('参数不全');
        }

        if ($_REQUEST['trade_status'] != 'TRADE_SUCCESS' && $_REQUEST['trade_status'] != 'TRADE_FINISHED') {
            die('success');
        }

        $payment = Payment::where('code', 'alipay')->first()->toArray();
        $payment = $payment['config'];
       
        if ($_REQUEST['seller_id'] != $payment['partnerId']) {
            die('非法请求');
        }

        $check_status = trim(@file_get_contents('https://mapi.alipay.com/gateway.do?service=notify_verify&partner='.$payment['partnerId'].'&notify_id='.$_REQUEST['notify_id']));
        if ($check_status !== 'true') {
            die('不是支付宝请求');
        }

        
        require_once base_path().'/vendor/alipay/alipay_core.function.php';
        require_once base_path().'/vendor/alipay/alipay_rsa.function.php';
        
        $check_status = rsaVerify(createLinkstring(argSort(paraFilter($_REQUEST))), $payment['partnerPubKey'], $_REQUEST['sign'], false);
        if (!$check_status) {
            die('签名错误');
        }
        $this->_notify($_REQUEST);
    }

    /**
     * 商家订单手机端回调
     */
    public function appnotifys() {


        if (empty($_REQUEST['notify_id']) || 
            empty($_REQUEST['seller_id']) || 
            empty($_REQUEST['out_trade_no']) || 
            empty($_REQUEST['sign'])) {
            die('参数不全');
        }

        if ($_REQUEST['trade_status'] != 'TRADE_SUCCESS' && $_REQUEST['trade_status'] != 'TRADE_FINISHED') {
            die('success');
        }

        $payment = Payment::where('code', 'alipay')->first()->toArray();
        $payment = $payment['config'];
       
        if ($_REQUEST['seller_id'] != $payment['partnerId']) {
            die('非法请求');
        }

        $check_status = trim(@file_get_contents('https://mapi.alipay.com/gateway.do?service=notify_verify&partner='.$payment['partnerId'].'&notify_id='.$_REQUEST['notify_id']));
        if ($check_status !== 'true') {
            die('不是支付宝请求');
        }

        
        require_once base_path().'/vendor/alipay/alipay_core.function.php';
        require_once base_path().'/vendor/alipay/alipay_rsa.function.php';
        
        $check_status = rsaVerify(createLinkstring(argSort(paraFilter($_REQUEST))), $payment['partnerPubKey'], $_REQUEST['sign'], false);
        if (!$check_status) {
            die('签名错误');
        }
        $this->_notifys($_REQUEST);
    }

    /**
     * 商家订单手机端回调
     */
    public function livenotifys() {

        if (empty($_REQUEST['notify_id']) ||
            empty($_REQUEST['out_trade_no']) ||
            empty($_REQUEST['sign'])) {
            die('参数不全');
        }

        if ($_REQUEST['trade_status'] != 'TRADE_SUCCESS' && $_REQUEST['trade_status'] != 'TRADE_FINISHED') {
            die('success');
        }

        $payment = Payment::where('code', 'alipay')->first()->toArray();
        $payment = $payment['config'];

        if ($_REQUEST['seller_id'] != $payment['partnerId']) {
            die('非法请求');
        }

        $check_status = trim(@file_get_contents('https://mapi.alipay.com/gateway.do?service=notify_verify&partner='.$payment['partnerId'].'&notify_id='.$_REQUEST['notify_id']));
        if ($check_status !== 'true') {
            die('不是支付宝请求');
        }

        require_once base_path().'/vendor/alipay/alipay_core.function.php';
        require_once base_path().'/vendor/alipay/alipay_rsa.function.php';

        $check_status = rsaVerify(createLinkstring(argSort(paraFilter($_REQUEST))), $payment['partnerPubKey'], $_REQUEST['sign'], false);
        if (!$check_status) {
            die('签名错误');
        }
        $this->_livenotifys2($_REQUEST);
    }

    /**
     * 生活缴费
     */
    public function weblivecnotifys()
    {
        $request = (array)@simplexml_load_string($_REQUEST['notify_data'], 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$request) {
            die('参数不全');
        }

        if (!isset($request['seller_id']) ||
            !isset($request['trade_status']) ||
            !isset($request['out_trade_no']) ||
            !isset($request['notify_id']) ||
            !isset($_REQUEST['sign'])) {
            die('参数不全');
        }

        if ($request['trade_status'] != 'TRADE_SUCCESS' && $request['trade_status'] != 'TRADE_FINISHED') {
            die('success');
        }

        $payment = Payment::where('code', 'alipayWap')->first()->toArray();
        $payment = $payment['config'];

        if ($request['seller_id'] != $payment['partnerId']) {
            die('非法请求');
        }

        $check_status = trim(@file_get_contents('https://mapi.alipay.com/gateway.do?service=notify_verify&partner='.$payment['partnerId'].'&notify_id='.$request['notify_id']));
        if ($check_status !== 'true') {
            die('不是支付宝请求');
        }

        $para_sort = [];
        $para_sort['service'] = $_REQUEST['service'];
        $para_sort['v'] = $_REQUEST['v'];
        $para_sort['sec_id'] = $_REQUEST['sec_id'];
        $para_sort['notify_data'] = $_REQUEST['notify_data'];

        require_once base_path().'/vendor/alipay/alipay_core.function.php';
        require_once base_path().'/vendor/alipay/alipay_md5.function.php';
        $check_status = md5Verify(createLinkstring($para_sort), $_REQUEST['sign'], $payment['partnerKey']);
        if (!$check_status) {
            die('签名错误');
        }

        $this->_livenotifys2($request);
    } 

    /**
     * 商家支付公共回调处理方法
     */
    private function _livenotifys2($request) {
        $userPayLog = UserPayLog::where('sn', $request['out_trade_no'])->first();
        if (!$userPayLog) {
            die('找不到支付日志');
        }
        if($userPayLog->status == 1){
            die('该订单已支付，请勿重复刷单');
        }
        //修改状态
        $status = UserPayLog::where('id', $userPayLog->id)->update([
            'pay_time'                  => UTC_TIME,
            'pay_day'                   => UTC_DAY,
            'status'                    => 1,
            'trade_no'                  => $request['trade_no']
        ]);
        if($status){
            $result = LiveService::getOrder($userPayLog->sn);

            if($result['Code'] != 0){//退款
                $liveLog = liveLog::where('sn', $request['out_trade_no'])->with('user')->first();
                $userPayLog = UserPayLog::where('sn', $request['out_trade_no'])->first()->toArray();
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
                LiveLog::where('sn', $request['out_trade_no'])->update([
                    'is_pay'                    => 1
                ]);
            }
            die('success');
        }else{
            die('fail');
        }
    }

    /**
     * 物业缴费
     */
    public function propertynotify()
    { 
        // file_put_contents('/mnt/test/shequ/storage/logs/alipay.log', var_export($_REQUEST, true), FILE_APPEND);
        $request = (array)@simplexml_load_string($_REQUEST['notify_data'], 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$request) {
            die('参数不全');
        }

        if (!isset($request['seller_id']) ||
            !isset($request['trade_status']) ||
            !isset($request['out_trade_no']) ||
            !isset($request['notify_id']) ||
            !isset($_REQUEST['sign'])) {
            die('参数不全');
        }

        if ($request['trade_status'] != 'TRADE_SUCCESS' && $request['trade_status'] != 'TRADE_FINISHED') {
            die('success');
        }

        $payment = Payment::where('code', 'alipayWap')->first()->toArray();
        $payment = $payment['config'];

        if ($request['seller_id'] != $payment['partnerId']) {
            die('非法请求');
        }

        $check_status = trim(@file_get_contents('https://mapi.alipay.com/gateway.do?service=notify_verify&partner='.$payment['partnerId'].'&notify_id='.$request['notify_id']));
        if ($check_status !== 'true') {
            die('不是支付宝请求');
        }

        $para_sort = [];
        $para_sort['service'] = $_REQUEST['service'];
        $para_sort['v'] = $_REQUEST['v'];
        $para_sort['sec_id'] = $_REQUEST['sec_id'];
        $para_sort['notify_data'] = $_REQUEST['notify_data'];

        require_once base_path().'/vendor/alipay/alipay_core.function.php';
        require_once base_path().'/vendor/alipay/alipay_md5.function.php';
        $check_status = md5Verify(createLinkstring($para_sort), $_REQUEST['sign'], $payment['partnerKey']);
        if (!$check_status) {
            die('签名错误');
        }

        $this->_propertyOrder($request);
    }
    
    /**
     * 物业缴费
     */
    public function propertyappnotify()
    { 
        // file_put_contents(storage_path().'/logs/alipay.log', var_export($_REQUEST, true), FILE_APPEND);
        $request = $_REQUEST; 
        if (!$request) {
            die('参数不全2');
        }  
        
        if (!isset($request['seller_id']) ||
            !isset($request['trade_status']) ||
            !isset($request['out_trade_no']) ||
            !isset($request['notify_id']) ||
            !isset($request['sign'])) {
            die('参数不全1');
        }

        if ($request['trade_status'] != 'TRADE_SUCCESS' && $request['trade_status'] != 'TRADE_FINISHED') {
            die('success');
        }

        $payment = Payment::where('code', 'alipay')->first()->toArray();
        $payment = $payment['config'];

        if ($request['seller_id'] != $payment['partnerId']) {
            die('非法请求');
        }

        $check_status = trim(@file_get_contents('https://mapi.alipay.com/gateway.do?service=notify_verify&partner='.$payment['partnerId'].'&notify_id='.$request['notify_id']));
        if ($check_status !== 'true') {
            die('不是支付宝请求');
        }

        $para_sort = [];
        $para_sort['service'] = $request['service'];
        $para_sort['v'] = $request['v'];
        $para_sort['sec_id'] = $request['sec_id'];
        $para_sort['notify_data'] = $request['notify_data'];

        require_once base_path().'/vendor/alipay/alipay_core.function.php';
        require_once base_path().'/vendor/alipay/alipay_rsa.function.php';
        
        $check_status = rsaVerify(createLinkstring(argSort(paraFilter($request))), $payment['partnerPubKey'], $request['sign'], false);
        if (!$check_status) {
            die('签名错误');
        } 
        $this->_propertyOrder($request);
    }

    /**
     * 物业缴费业务回调逻辑处理
     */
    private function _propertyOrder($request) {
        $userPayLog = UserPayLog::where('sn', $request['out_trade_no'])->first(); 
        
        if (!$userPayLog) {
            die('找不到日志');
        }
        
        $order = PropertyOrder::find($userPayLog->order_id);

        if (!$order) {
            die('找不到订单');
        }

        if ($order->pay_status == 1) {
            die('订单已支付');
        }
        // if ($order->status != ORDER_STATUS_WAIT_PAY) {
        // die('订单不能支付');
        // }

        $userPayLog->pay_account = $request['buyer_email'];
        $userPayLog->pay_time    = UTC_TIME;
        $userPayLog->pay_day     = UTC_DAY;
        $userPayLog->status      = 1;
        $userPayLog->trade_no    = $request['trade_no'];

        DB::beginTransaction();
        try {
            $status = $userPayLog->save();
            //$endTime  = UTC_TIME + (int)\YiZan\Services\SystemConfigService::getConfigByCode('system_seller_order_confirm') * 60;
            
            //修改状态
            $status = PropertyOrder::where('id', $userPayLog->order_id)->update([
                    'pay_time'                  => UTC_TIME,
                    'pay_status'                => ORDER_PAY_STATUS_YES 
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
            DB::commit();
        } 
        catch (Exception $e) 
        {
            DB::rollback();
            $status = false;
        }


        if ($status) { 

            die('success');
        } else {
            die('更新订单失败');
        }
    }

    /**
     * 会员订单网页回调
     */
    public function webpcnotify() 
    {
        //file_put_contents('/mnt/www/paotui/storage/logs/pay.log',print_r($_REQUEST,true));
        
        $payment = Payment::where('code', 'alipayWeb')->first()->toArray();
        
        $payment = $payment['config'];

        if ($_REQUEST['seller_id'] != $payment['partnerId']) 
        {
            die('fail');
        }
        
        require_once base_path().'/vendor/alipay/alipay_pc_notify.class.php';
        
        $alipay_config['partner']       = $payment['partnerId'];
        $alipay_config['seller_email']  = $payment['sellerId'];
        $alipay_config['key']           = $payment['partnerKey'];
        $alipay_config['sign_type']     = 'MD5';
        $alipay_config['input_charset'] = 'utf-8';
        $alipay_config['cacert']        = base_path().'/vendor/alipay/cacert.pem';
        $alipay_config['transport']     = 'https';
        
        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($alipay_config);
        
        $verify_result = $alipayNotify->verifyNotify();
        
        if($verify_result == false) 
        {
            die('fail');
        }
        
        $this->_notify($_REQUEST);
    }

    /**
     * 商家订单网页回调
     */
    public function webpcnotifys() {		
		$request = (array)@simplexml_load_string($_REQUEST['notify_data'], 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$request) {
            die('参数不全');
        }
        if (!isset($request['seller_id']) || 
            !isset($request['trade_status']) || 
            !isset($request['out_trade_no']) || 
            !isset($request['notify_id']) || 
            !isset($_REQUEST['sign'])) {
            die('参数不全');
        }

        if ($request['trade_status'] != 'TRADE_SUCCESS' && $request['trade_status'] != 'TRADE_FINISHED') {
            die('success');
        }
		
        $payment = Payment::where('code', 'alipayWap')->first()->toArray();
        $payment = $payment['config'];
		
        if ($request['seller_id'] != $payment['partnerId']) 
        {
            die('非法请求');
        }
		
		$check_status = trim(@file_get_contents('https://mapi.alipay.com/gateway.do?service=notify_verify&partner='.$payment['partnerId'].'&notify_id='.$request['notify_id']));
        if ($check_status !== 'true') {
            die('不是支付宝请求');
        }

        $para_sort = [];
        $para_sort['service'] = $_REQUEST['service'];
        $para_sort['v'] = $_REQUEST['v'];
        $para_sort['sec_id'] = $_REQUEST['sec_id'];
        $para_sort['notify_data'] = $_REQUEST['notify_data'];

        require_once base_path().'/vendor/alipay/alipay_core.function.php';
        require_once base_path().'/vendor/alipay/alipay_md5.function.php';
        $check_status = md5Verify(createLinkstring($para_sort), $_REQUEST['sign'], $payment['partnerKey']);
        if (!$check_status) {
            die('签名错误');
        }
        
        $this->_notifys($request);
    }

    /**
     * 退款回调
     */
    public function refundnotify()
    {
        //file_put_contents('/mnt/www/paotui/storage/logs/pay.log',print_r($_REQUEST,true));
        
        $batch_no = $_POST['batch_no'];

        //批量退款数据中转账成功的笔数
        $success_num = $_POST['success_num'];
        
        $userRefundLog = UserRefundLog::where('sn', $batch_no)->first();
        if($userRefundLog){
            $userPayLog = UserPayLog::where('trade_no', $userRefundLog->trade_no)->first();
            if($userPayLog->pay_type == 6){
                $this->liverefund($userRefundLog,$userPayLog);
            }
        }

        $payment = Payment::where('code', $userRefundLog->payment_type)->first()->toArray();
        
        $payment = $payment['config'];

        require_once base_path().'/vendor/alipay/alipay_pc_notify.class.php';
        
        $alipay_config['partner']       = $payment['partnerId'];
        $alipay_config['seller_email']  = $payment['sellerId'];
        $alipay_config['key']           = $payment['partnerKey'];
        $alipay_config['sign_type']     = 'MD5';
        $alipay_config['input_charset'] = 'utf-8';
        $alipay_config['cacert']        = base_path().'/vendor/alipay/cacert.pem';
        $alipay_config['transport']     = 'https';
        
        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($alipay_config);
        
        $verify_result = $alipayNotify->verifyNotify();
        
        if($verify_result == false) 
        {
            die('fail');
        }
        
        // 都是单笔退款
        if($success_num == 1)
        {
            $userRefundLog->status = 1;
            
            $userRefundLog->save();
            
            $refund = Refund::where("id", $userRefundLog->refund_id)->first();
            
            $refund->status = 1;
            
            $refund->save();
            
            $order = Order::where("id", $refund->order_id)->with('user')->first();
            
            $order->status = ORDER_STATUS_REFUND_SUCCESS;
            
            $order->save();
            
            $order = $order->toArray();
            
            try 
            {
                //更新商家扩展,资金日志
                SellerMoneyLog::where('type','order_refund')->where('related_id', $order->id)->update(['status' => 1]);
                SellerMoneyLog::where('type','order_pay')->where('related_id', $order->id)->update(['status' => 4]);
                //SellerService::decrementExtend($order->seller_id, 'wait_confirm_money', $order->seller_fee);

                //通知客户
                PushMessageService::notice($order['user']['id'], $order['user']['mobile'], 'order.refundpay', $order);
            } 
            catch (Exception $e) 
            {
            }
        }
        
        die('success');  
    }

    /**
     * 处理生活缴费退款
     */
    public function liverefund($userRefundLog){
        UserRefundLog::where('sn', $userRefundLog->sn)->update(['status'=>1]);
        Refund::where("id", $userRefundLog->refund_id)->update(['status'=>1]);
    }


    public function webnotify() {
        $request = (array)@simplexml_load_string($_REQUEST['notify_data'], 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$request) {
            die('参数不全');
        }
        if (!isset($request['seller_id']) || 
            !isset($request['trade_status']) || 
            !isset($request['out_trade_no']) || 
            !isset($request['notify_id']) || 
            !isset($_REQUEST['sign'])) {
            die('参数不全');
        }

        if ($request['trade_status'] != 'TRADE_SUCCESS' && $request['trade_status'] != 'TRADE_FINISHED') {
            die('success');
        }

        $payment = Payment::where('code', 'alipayWap')->first()->toArray();
        $payment = $payment['config'];

        if ($request['seller_id'] != $payment['partnerId']) {
            die('非法请求');
        }

        $check_status = trim(@file_get_contents('https://mapi.alipay.com/gateway.do?service=notify_verify&partner='.$payment['partnerId'].'&notify_id='.$request['notify_id']));
        if ($check_status !== 'true') {
            die('不是支付宝请求');
        }

        $para_sort = [];
        $para_sort['service'] = $_REQUEST['service'];
        $para_sort['v'] = $_REQUEST['v'];
        $para_sort['sec_id'] = $_REQUEST['sec_id'];
        $para_sort['notify_data'] = $_REQUEST['notify_data'];

        require_once base_path().'/vendor/alipay/alipay_core.function.php';
        require_once base_path().'/vendor/alipay/alipay_md5.function.php';
        $check_status = md5Verify(createLinkstring($para_sort), $_REQUEST['sign'], $payment['partnerKey']);
        if (!$check_status) {
            die('签名错误');
        }

        $this->_notify($request);
    }

    /**
     * 会员支付公共回调处理方法
     */
    private function _notify($request) {
        $userPayLog = UserPayLog::where('sn', $request['out_trade_no'])->first();

        if (!$userPayLog) {
            die('找不到支付日志');
        }

        if($userPayLog->status == 1){
            die('该订单已支付，请勿重复刷单');
        }
        if ($userPayLog->is_fx == 1) {
            $this->rechargefx($request, $userPayLog); //充值
        }else{
            if ($userPayLog->activity_id < 1) {
                if ($userPayLog->order_id < 1) {
                    $this->recharge($request, $userPayLog); //充值
                } else {
                    $this->order($request, $userPayLog); //订单
                }
            } else {
                $this->activity($request, $userPayLog); //活动
            }
        }
    }

    /**
     * 商家支付公共回调处理方法
     */
    private function _notifys($request) {
        $sellerPayLog = SellerPayLog::where('sn', $request['out_trade_no'])->first();

        if (!$sellerPayLog) {
            die('找不到支付日志');
        }

        if($sellerPayLog->status == 1){
            die('该订单已支付，请勿重复刷单');
        }
        
        $this->orders($request, $sellerPayLog); //订单

        // if ($sellerPayLog->activity_id < 1) {
        //     $this->orders($request, $userPayLog); //订单
        // } else {
        //     $this->activity($request, $userPayLog); //活动
        // }
    }

    /**
     * [order 处理会员订单]
     * @param  [type] $request    [description]
     * @param  [type] $userPayLog [description]
     * @return [type]             [description]
     */
    private function order($request, $userPayLog) {
        $order = Order::find($userPayLog->order_id);

        if (!$order) {
            die('找不到订单');
        }

        if ($order->pay_status == 1) {
            die('订单已支付');
        }
        // if ($order->status != ORDER_STATUS_WAIT_PAY) {
        // die('订单不能支付');
        // }

        $userPayLog->pay_account = $request['buyer_email'];
        $userPayLog->pay_time    = UTC_TIME;
        $userPayLog->pay_day     = UTC_DAY;
        $userPayLog->status      = 1;
        $userPayLog->trade_no    = $request['trade_no'];

        DB::beginTransaction();
        try {
            $status = $userPayLog->save();
            //$endTime  = UTC_TIME + (int)\YiZan\Services\SystemConfigService::getConfigByCode('system_seller_order_confirm') * 60;
            
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

            // 写入日志
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
            DB::commit();
        } 
        catch (Exception $e) 
        {
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

            die('success');
        } else {
            die('更新订单失败');
        }
    }

    /**
     * [orders 处理商家订单]
     * @param  [type] $request    [description]
     * @param  [type] $userPayLog [description]
     * @return [type]             [description]
     */
    private function orders($request, $sellerPayLog) { 

        $sellerPayLog->pay_account = $request['buyer_email'];
        $sellerPayLog->pay_time    = UTC_TIME;
        $sellerPayLog->pay_day     = UTC_DAY;
        $sellerPayLog->status      = 1;
        $sellerPayLog->trade_no    = $request['trade_no'];

        DB::beginTransaction();
        try {
            $status = $sellerPayLog->save();

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
        } 
        catch (Exception $e) 
        {
            DB::rollback();
            $status = false;
        }

        die('success');
        // if ($status) {
        //     $order = OrderService::getOrderById($order->user_id, $order->id);
        //     try {
        //         PushMessageService::notice( $order['seller']['userId'],  $order['seller']['mobile'], 'order.create',  $order,['sms', 'app'],'seller','3',$order['id'], "music1.caf");
        //         if($order['staff'] && $order['seller']['userId'] != $order['staff']['userId']){
        //             PushMessageService::notice( $order['staff']['userId'],  $order['staff']['mobile'], 'order.create',  $order,['sms', 'app'],'staff','3',$order['id'], "music1.caf");
        //         }
        //     } catch (Exception $e) {

        //     }

        //     die('success');
        // } else {
        //     die('更新订单失败');
        // }
    }

    /**
     * [activity 处理活动]
     * @param  [type] $request    [description]
     * @param  [type] $userPayLog [description]
     * @return [type]             [description]
     */
    private function activity($request, $userPayLog) {
        $userPayLog->pay_account = $request['buyer_email'];
        $userPayLog->pay_time    = UTC_TIME;
        $userPayLog->pay_day     = UTC_DAY;
        $userPayLog->status      = 1;
        $userPayLog->trade_no    = $request['trade_no'];

        DB::beginTransaction();
        try {
            $status = $userPayLog->save();
            DB::commit();
        }
        catch (Exception $e) {
            DB::rollback();
            $status = false;
        }

        if ($status) {
            ActivityService::payfinish($userPayLog->user_id, $userPayLog->sn, $userPayLog->activity_id);
            die('success');
        } else {
            die('更新活动失败');
        }
    }

    /**
     * [recharge 充值]
     * @param  [type] $request    [description]
     * @param  [type] $userPayLog [description]
     * @return [type]             [description]
     */
    private function recharge($request, $userPayLog) {
        $user = User::find($userPayLog->user_id);
        if (!$user) {
            die('找不到会员');
        }
        
        $userPayLog->pay_account = $request['buyer_email'];
        $userPayLog->pay_time    = UTC_TIME;
        $userPayLog->pay_day     = UTC_DAY;
        $userPayLog->status      = 1;
        $userPayLog->trade_no    = $request['trade_no'];
        $userPayLog->balance     = $user->balance + $userPayLog->money;

        DB::beginTransaction();
        try {
            $status = $userPayLog->save();
            User::where('id', $userPayLog->user_id)->update([
                    'balance'       => new Expression("balance + " . $userPayLog->money), 
                    'total_money' => new Expression("total_money + " . $userPayLog->money), 
                ]);
            DB::commit();
            die('success');
        } catch (Exception $e) {
            DB::rollback();
            die('更新余额失败');
        } 
    }
    /**
     * [recharge 充值缴费]
     * @param  [type] $request    [description]
     * @param  [type] $userPayLog [description]
     * @return [type]             [description]
     */
    private function rechargefx($request, $userPayLog) {
        $user = User::find($userPayLog->user_id);
        if (!$user) {
            die('找不到会员');
        }

        $userPayLog->pay_account = $request['buyer_email'];
        $userPayLog->pay_time    = UTC_TIME;
        $userPayLog->pay_day     = UTC_DAY;
        $userPayLog->status      = 1;
        $userPayLog->trade_no    = $request['trade_no'];
        $userPayLog->balance     = $user->balance;

        DB::beginTransaction();
        try {
            $userPayLog->save();
            User::where('id', $userPayLog->user_id)->update([ 'is_pay'=> 1]);
            DB::commit();
            die('success');
        } catch (Exception $e) {
            DB::rollback();
            die('更新余额失败');
        }
    }

}
