<?php namespace YiZan\Services\Buyer;

use YiZan\Models\Sellerweb\PropertyOrder; 
use YiZan\Models\Sellerweb\PropertyOrderItem; 
use YiZan\Models\Sellerweb\PropertyFee; 
use YiZan\Models\Sellerweb\PropertyItem; 
use YiZan\Models\PropertyUser; 
use YiZan\Models\Payment; 
use YiZan\Models\UserPayLog;
use YiZan\Services\SellerService as baseSellerService;
use YiZan\Services\SellerMoneyLogService;
use YiZan\Models\SellerMoneyLog;

use YiZan\Utils\String;
use YiZan\Utils\Helper; 
use YiZan\Utils\Http;
use Lang, DB, Validator, Config, Time;
class PropertyOrderService extends \YiZan\Services\PropertyOrderService {

    /**
     * 创建物业订单
     * @param object        $user   会员信息 
     * @param array/int $propertyFeeId  物业费项目编号
     * @param string $payment       支付方式
     */
    public static function createOrder($user, $propertyFeeId, $payment){ 
        DB::connection()->enableQueryLog();
        $result =
        [
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        ]; 
        $propertyFeeId = explode(',', $propertyFeeId);

        $propertyOrderItemInfo = PropertyOrderItem::whereIn('id', $propertyFeeId)
                                                  ->get()
                                                  ->toArray();  

        if($propertyOrderItemInfo){
            $result['code'] = 80311;
            return $result;
        }

        //判断物业费项目是否都存在
        $propertyFeeArr = [];   

        $totalFee = 0;
        foreach ($propertyFeeId as $key => $value) {
            
            $propertyFeeInfo = PropertyFee::leftJoin('property_user', function($join) use($user){
                                                $join->on('property_user.id', '=', 'property_fee.puser_id')
                                                    ->where('property_user.user_id', '=', $user->id);
                                            })
                                          ->where('property_fee.id', $value)
                                          ->where('property_fee.status', 0)
                                          ->select('property_fee.*')
                                          ->with('district', 'puser.user', 'seller', 'build', 'room', 'roomfee')
                                          ->first();   
            if(!$propertyFeeInfo){
                $result['code'] = 80309;
                return $result;
            }
            $totalFee += $propertyFeeInfo->fee;
            $propertyFeeArr[] = $propertyFeeInfo->toArray();
        }

        //判断物业费项目中是否只存在一个商家
        $sellerIds = PropertyFee::leftJoin('property_user', function($join) use($user){
                                    $join->on('property_user.id', '=', 'property_fee.puser_id')
                                        ->where('property_user.user_id', '=', $user->id);
                                })
                                ->whereIn('property_fee.id', $propertyFeeId)
                                ->groupBy('property_fee.seller_id')
                                ->select('property_fee.*')
                                ->with('seller')
                                ->get()
                                ->toArray();   

        $puserId = PropertyUser::where('user_id', $user->id)
                               ->where('seller_id', $sellerIds[0]['sellerId'])
                               ->pluck('id');
        if(count($sellerIds) > 1){
            $result['code'] = 80310;
            return $result;
        }
        DB::beginTransaction();
        try {
            //创建订单
            $propertyOrder = new PropertyOrder();
            $propertyOrder->sn = Helper::getSn();
            $propertyOrder->seller_id = $sellerIds[0]['sellerId'];
            $propertyOrder->user_id = $user->id;
            $propertyOrder->puser_id = $puserId;
            $propertyOrder->district_id = $sellerIds[0]['districtId']; 
            $propertyOrder->pay_fee = $totalFee;
            $propertyOrder->pay_type = $payment;//
            $propertyOrder->pay_status = 0;//
            $propertyOrder->create_time = UTC_TIME;
            $propertyOrder->first_level = $sellerIds[0]['seller']['firstLevel'];
            $propertyOrder->second_level = $sellerIds[0]['seller']['secondLevel'];
            $propertyOrder->third_level = $sellerIds[0]['seller']['thirdLevel'];
            $propertyOrder->save();
            //创建订单明细
            foreach ($propertyFeeArr as $key => $value) {
                $propertyOrderItem = new PropertyOrderItem();
                $propertyOrderItem->seller_id = $value['sellerId'];
                $propertyOrderItem->order_id = $propertyOrder->id;
                $propertyOrderItem->propertyfee_id = $value['id'];
                $propertyOrderItem->price = $value['fee'];
                $propertyOrderItem->num = 1;
                $propertyOrderItem->save();
            }   
            $paymentInfo = Payment::where('code', $payment)->where('status', 1)->first();
            if (!$paymentInfo) {
                $result['code'] = 60019;
                return $result;
            }
            $userPayLog = new UserPayLog;
            $userPayLog->user_id        = $user->id;
            $userPayLog->payment_type   = $payment;
            $userPayLog->order_id       = $propertyOrder->id;
            $userPayLog->activity_id    = 0;
            $userPayLog->seller_id      = $propertyOrder->seller_id;
            $userPayLog->money          = $propertyOrder->pay_fee; 
            $userPayLog->content        = '物业缴费';
            $userPayLog->pay_type       = 8;  //物业缴费  
            $userPayLog->create_time    = UTC_TIME;
            $userPayLog->create_day     = UTC_DAY;
            $userPayLog->status         = 0;
            $userPayLog->sn = Helper::getSn();
            $userPayLog->save();
            $result['data'] = $propertyOrder;
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }  
        return $result;
    }

    /**
     * 获取支付信息
     */
    public static function getPaymentInfo($user, $orderId, $extend){
        $result =
        [
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        ]; 
        $propertyOrder = PropertyOrder::where('user_id', $user->id)
                                      ->where('id', $orderId)
                                      ->first();

        $userPayLog = UserPayLog::where('user_id', $user->id)
                                      ->where('order_id', $orderId)
                                      ->orderBy('id', 'DESC')
                                      ->first();
        if(empty($propertyOrder)){
            $result['code'] = 80401;
            return $result;
        }
        if(empty($userPayLog)){
            $result['code'] = 80401;
            return $result;
        }
        $payment = $userPayLog->payment_type;
        
        $paymentInfo = Payment::where('code', $payment)->where('status', 1)->first();
        
       //获取支付信息
        $createPayInfo = self::$payment($userPayLog, $paymentInfo, $extend, $user, $propertyOrder);
        if (is_numeric($createPayInfo)) { 
            $result['code'] = abs($createPayInfo); 
            return $result;
        } 
        
        $porder = $propertyOrder->toArray();
        $porder['paymentType'] = $paymentInfo->code;
        $porder['payRequest'] = $createPayInfo;

        $result['data'] = $porder;
        return $result;
    }

    /**
     * 取消订单
     */
    public static function cancelOrder($user, $id){
        $result =
        [
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        ]; 
        PropertyOrder::where('user_id', $user->id)
                     ->where('id', $id)
                     ->delete();
        PropertyOrderItem::where('order_id', $id)
                         ->delete();
        return $result;
    }

    /**
     * 余额支付
     */
    private static function balancePay($userPayLog, $payment, $extend, $user,  $propertyOrder){ 
        if($user->balance >= $userPayLog->getMoney()){ 
            //扣除会员余额
            $balance = $user->balance - $userPayLog->getMoney();
            //修改会员余额
            $user->balance = $balance;
            $user->save();
            //修改支付日志状态
            $userPayLog->balance = $balance;
            $userPayLog->status = 1;
            $userPayLog->save(); 
            //修改订单状态
            $propertyOrder->pay_status = 1;
            $propertyOrder->pay_time = UTC_TIME;
            $propertyOrder->save();
            $propertyUser = PropertyUser::where('user_id', $userPayLog->user_id)
                                        ->where('seller_id', $userPayLog->seller_id)
                                        ->first();
            //修改物业费项目
            $propertyFeeIds = PropertyOrderItem::where('order_id', $propertyOrder->id)
                                               ->lists('propertyfee_id');
            PropertyFee::whereIn('id', $propertyFeeIds)
                       ->update(['status'=>1,'pay_time'=>UTC_TIME, 'puser_id'=>$propertyUser->id]);
            //修改商家金额 
            baseSellerService::incrementExtend($userPayLog->seller_id, 'money', $userPayLog->getMoney());
            //添加商家资金日志 
            SellerMoneyLogService::createLog(
                    $userPayLog->seller_id,
                    SellerMoneyLog::TYPE_PROPERTY_FEE,
                    $userPayLog->user_id,
                    $userPayLog->getMoney(),
                    '物业缴费',
                    1
                );
            return ['status' => 1];           
        } else { 
            return 60318;
        }
    }

    /**
     * 银联支付
     */
    private static function unionpay($userPayLog, $payment, $extend){
        //服务器异步通知页面路径
        $notify_url = Config::get('app.callback_url').'payment/unionpay/propertynotify';
        $config = $payment->config;
        require_once base_path().'/vendor/unionpay/utf8/func/common.php';
        $params = array(

            //以下信息非特殊情况不需要改动
            'version' => '5.0.0',                 //版本号
            'encoding' => 'utf-8',                //编码方式
            'certId' => getSignCertId(),          //证书ID
            'txnType' => '01',                    //交易类型
            'txnSubType' => '01',                 //交易子类
            'bizType' => '000201',                //业务类型
            'frontUrl' =>  $notify_url,           //前台通知地址
            'backUrl' => $notify_url,             //后台通知地址
            'signMethod' => '01',                 //签名方法
            'channelType' => '08',                //渠道类型，07-PC，08-手机
            'accessType' => '0',                  //接入类型
            'currencyCode' => '156',              //交易币种，境内商户固定156

            //TODO 以下信息需要填写
            'merId' => $config['merId'],        //商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
            'orderId' => $userPayLog->sn,   //商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
            'txnTime' => Time::toDate(UTC_TIME, 'YmdHis'),  //订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
            'txnAmt' => $userPayLog->money*100, //交易金额，单位分，此处默认取demo演示页面传递的参数
            'reqReserved' => $payment->code,        //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据
        );
        sign ($params);

        $uri = SDK_FRONT_TRANS_URL;
        $html_form = create_html ( $params, $uri );
        return ['html' => $html_form];
    }
    
    /**
     * 创建订单支付参数（会员订单，商家订单）
     * @param array $userPayLog     支付日志
     * @param array $payment        支付方式
     * @param array $extend         扩展
     * @param int   $isSeller       是否是商家
     */
    private static function unionapp($userPayLog, $payment, $extend = []) {
        //服务器异步通知页面路径
        $notify_url = Config::get('app.callback_url').'payment/unionpay/propertynotify';
        $config = $payment->config;
        require_once base_path().'/vendor/unionpay/utf8/func/common.php';
        $params = array(

            //以下信息非特殊情况不需要改动
            'version' => '5.0.0',                 //版本号
            'encoding' => 'utf-8',                //编码方式
            'certId' => getSignCertId(),          //证书ID
            'txnType' => '01',                    //交易类型
            'txnSubType' => '01',                 //交易子类
            'bizType' => '000201',                //业务类型
            'frontUrl' =>  $notify_url,           //前台通知地址
            'backUrl' => $notify_url,             //后台通知地址
            'signMethod' => '01',                 //签名方法
            'channelType' => '08',                //渠道类型，07-PC，08-手机
            'accessType' => '0',                  //接入类型
            'currencyCode' => '156',              //交易币种，境内商户固定156

            //TODO 以下信息需要填写
            'merId' => $config['merId'],        //商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
            'orderId' => $userPayLog->sn,   //商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
            'txnTime' => Time::toDate(UTC_TIME, 'YmdHis'),  //订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
            'txnAmt' => $userPayLog->money*100, //交易金额，单位分，此处默认取demo演示页面传递的参数
            'reqReserved' => $payment->code,        //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据
        );
        sign ($params);

        $uri = SDK_App_Request_Url;
        $result = Http::post($uri, $params);
        parse_str($result, $result_arr);
        $pay_request['tn'] = $result_arr['tn'];
        $pay_request['packages'] = $result;
        return $pay_request;
    }

    /**
     * 支付宝支付
     */
    private static function alipayWap($userPayLog, $payment, $extend){
        require_once base_path().'/vendor/alipay/alipay_submit.class.php';

        //服务器异步通知页面路径 
        $notify_url = Config::get('app.callback_url').'payment/alipay/propertynotify'; 
        //页面跳转同步通知页面路径
        $call_back_url = urlencode('http://wap.' . Config::get('app.domain') . '/PropertyFee/log'); 
        //操作中断返回地址
        $merchant_url = urlencode('http://wap.' . Config::get('app.domain') . '/PropertyFee/log'); 

        $config = $payment->config;

        $req_data = '<direct_trade_create_req>
                        <notify_url>' . $notify_url . '</notify_url>
                        <call_back_url>' . $call_back_url . '</call_back_url>
                        <seller_account_name>' . $config['sellerId'] . '</seller_account_name>
                        <out_trade_no>' . $userPayLog->sn . '</out_trade_no>
                        <subject>' . str_replace('&', '', $userPayLog->content) . '</subject>
                        <total_fee>' . $userPayLog->money . '</total_fee>
                        <merchant_url>' . $merchant_url . '</merchant_url>
                    </direct_trade_create_req>';


        $alipay_config['partner']       = $config['partnerId'];
        $alipay_config['sellerEmail']   = $config['sellerId'];
        $alipay_config['key']           = $config['partnerKey'];
        $alipay_config['sign_type']     = 'MD5';
        $alipay_config['input_charset'] = 'utf-8';
        $alipay_config['cacert']        = base_path().'/vendor/alipay/cacert.pem';
        $alipay_config['transport']     = 'https';

        //构造要请求的参数数组，无需改动
        $para_token = array(
            "service"        => "alipay.wap.trade.create.direct",
            "partner"        => $alipay_config['partner'],
            "sec_id"         => $alipay_config['sign_type'],
            "format"         => 'xml',
            "v"              => '2.0',
            "req_id"         => $userPayLog->sn,
            "req_data"       => $req_data,
            "_input_charset" => $alipay_config['input_charset']
        );

        $alipaySubmit = new \AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestHttp($para_token);
        $html_text = urldecode($html_text);
        $para_html_text = $alipaySubmit->parseResponse($html_text);
        $request_token = $para_html_text['request_token'];
        $req_data = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';
        $parameter = array(
            "service"        => "alipay.wap.auth.authAndExecute",
            "partner"        => $alipay_config['partner'],
            "sec_id"         => $alipay_config['sign_type'],
            "format"         => 'xml',
            "v"              => '2.0',
            "req_id"         => $userPayLog->sn,
            "req_data"       => $req_data,
            "_input_charset" => $alipay_config['input_charset']
        );


        $alipaySubmit = new \AlipaySubmit($alipay_config);
        return ['html' => $alipaySubmit->buildRequestForm($parameter, 'get', '确认')];
    }
    
    /**
     * 支付宝
     */
    private static function alipay($userPayLog, $payment, $extend){
        //服务器异步通知页面路径 
        $notify_url = Config::get('app.callback_url').'payment/alipay/propertyappnotify'; 
        $config = $payment->config;
        $parameters = [
            'service' => 'mobile.securitypay.pay',
            'partner' => $config['partnerId'],
            '_input_charset' => 'utf-8',
            'notify_url' => $notify_url,
            'out_trade_no' => $userPayLog->sn,
            'subject' => str_replace('&', '', $userPayLog->content),
            'payment_type' => '1',
            'seller_id' => $config['sellerId'],
            'total_fee' => $userPayLog->money,
            'body' => str_replace('&', '', $userPayLog->content)
        ];

        ksort($parameters);
        reset($parameters);

        $packages = '';
        foreach ($parameters as $key => $val) {
            $packages .= '&'.$key.'="'.$val.'"';
        }
        $packages = substr($packages, 1);

        require_once base_path().'/vendor/alipay/alipay_rsa.function.php';
        $sign = urlencode(rsaSign($packages, $config['partnerPrivKey'], false));
        $pay_request = [];
        $pay_request['packages'] = $packages.'&sign_type="RSA"&sign="'.$sign.'"';

        return $pay_request;
    }

    /**
     * 微信支付
     */
    private static function weixinJs($userPayLog, $payment, $extend){
        $config = $payment->config;
        $order_data = array(
            'appid' => $config['appId'],
            'body' => $userPayLog->content,
            'mch_id' => $config['partnerId'],
            'nonce_str' => md5(String::randString(16)),
            'notify_url' => Config::get('app.callback_url').'payment/weixin/propertynotify',
            'openid'     => $extend['openId'],
            'out_trade_no' => $userPayLog->sn,
            'spbill_create_ip' => CLIENT_IP,
            'total_fee' => round($userPayLog->money * 100),
            'trade_type' => 'JSAPI'
        );
        $xml  = '<xml>';
        $sign = '';
        foreach ($order_data as $key => $data) {
            if ($key == 'body') {
                $xml .= "\n<{$key}><![CDATA[{$data}]]></{$key}>";
            } else {
                $xml .= "\n<{$key}>{$data}</{$key}>";
            }
            $sign .= "{$key}={$data}&";
        }
        $sign = strtoupper(md5("{$sign}key={$config['partnerKey']}"));
        $xml .= "\n<sign>{$sign}</sign>\n</xml>";

        $response = Http::post('https://api.mch.weixin.qq.com/pay/unifiedorder', $xml);
        
        if (empty($response)) {
            return false;
        }

        $response = @simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$response || !isset($response->return_code) || $response->return_code != 'SUCCESS') {
            return false;
        }

        $weixinConfig = SystemConfigService::getConfigByGroup('weixin');
        if (UTC_TIME + 1800 < $weixinConfig['access_token_expired']) {
            $access_token = $weixinConfig['access_token'];
        } else {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' .
                $config['appId'] . '&secret=' .$config['appSecret'];
            $token_json = Http::get($url);
            $token_json = empty($token_json) ? false : @json_decode($token_json, true);
            if(!$token_json || empty($token_json['access_token'])){
                return false;
            }
            $access_token = $token_json['access_token'];
            SystemConfigService::updateConfig('access_token', $access_token);
            SystemConfigService::updateConfig('access_token_expired', UTC_TIME + (int)$token_json['expires_in']);
        }

        if (UTC_TIME + 1800 < $weixinConfig['jsapi_ticket_expired']) {
            $jsapi_ticket = $weixinConfig['jsapi_ticket'];
        } else {
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi';
            $jsapi_json = Http::get($url);
            $jsapi_json = empty($jsapi_json) ? false : @json_decode($jsapi_json, true);
            if(!$jsapi_json || empty($jsapi_json['ticket'])){
                return false;
            }
            $jsapi_ticket = $jsapi_json['ticket'];
            SystemConfigService::updateConfig('jsapi_ticket', $jsapi_ticket);
            SystemConfigService::updateConfig('jsapi_ticket_expired', UTC_TIME + (int)$jsapi_json['expires_in']);
        }

        //js-sdk接口配置信息
        $jsapi_request = array(
            'jsapi_ticket' => $jsapi_ticket,
            'noncestr' => md5(String::randString(16)),
            'timestamp' => UTC_TIME,
            'url' => $extend['url']
        );
        $jsapi_request['signature'] = self::weixinSign($jsapi_request, '', 'sha1');
        $jsapi_request['appId'] = $config['appId'];

        //支付配置
        $pay_request = [
            'appId'     => $config['appId'],
            'nonceStr'  => md5(String::randString(16)),
            'package'   => 'prepay_id='.$response->prepay_id,
            'signType'  => 'MD5',
            'timeStamp' => UTC_TIME + date('Z')
        ];

        $pay_request['paySign'] = strtoupper(self::weixinSign($pay_request, $config['partnerKey']));
        $pay_request['jsapi']   = $jsapi_request;
        return $pay_request;
    }
    
    /**
     * 创建订单支付参数（会员订单）
     * @param array $userPayLog     支付日志
     * @param array $payment        支付方式
     * @param array $extend         扩展
     */
    private static function weixin($userPayLog, $payment, $extend = []) {
        $config = $payment->config;
        $order_data = array(
            'appid' => $config['appId'],
            'body' => $userPayLog->content,
            'mch_id' => $config['partnerId'],
            'nonce_str' => md5(String::randString(16)),
            'notify_url' => Config::get('app.callback_url').'payment/weixin/propertyappnotify',
            'out_trade_no' => $userPayLog->sn,
            'spbill_create_ip' => CLIENT_IP,
            'total_fee' => round($userPayLog->money * 100),
            'trade_type' => 'APP'
        );
        $xml  = '<xml>';
        $sign = '';
        foreach ($order_data as $key => $data) {
            if ($key == 'body') {
                $xml .= "\n<{$key}><![CDATA[{$data}]]></{$key}>";
            } else {
                $xml .= "\n<{$key}>{$data}</{$key}>";
            }
            $sign .= "{$key}={$data}&";
        }
        $sign = strtoupper(md5("{$sign}key={$config['partnerKey']}"));
        $xml .= "\n<sign>{$sign}</sign>\n</xml>";

        $response = Http::post('https://api.mch.weixin.qq.com/pay/unifiedorder', $xml);
        if (empty($response)) {
            return false;
        }

        $response = @simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$response || !isset($response->return_code) || $response->return_code != 'SUCCESS') {
            return false;
        }

        $pay_request = [
            'appid'     => $config['appId'],
            'noncestr'  => md5(String::randString(16)),
            'package'   => 'Sign=WXPay',
            'partnerid' => $config['partnerId'],
            'prepayid'  => strval($response->prepay_id),
            'timestamp' => UTC_TIME + date('Z')
        ];

        $sign = '';
        foreach ($pay_request as $key => $data) {
            $sign .= "{$key}={$data}&";
        }
        $pay_request['sign'] = strtoupper(md5("{$sign}key={$config['partnerKey']}"));
        $pay_request['packages'] = 'Sign=WXPay';
        return $pay_request;
    }

    private static function weixinSign($args, $partnerKey = '', $type = 'md5') {
        $sign = '';
        foreach ($args as $key => $data) {
            $sign .= "{$key}={$data}&";
        }
        if (!empty($partnerKey)) {
            $sign .= "key={$partnerKey}";
        } else {
            $sign = substr($sign, 0, -1);
        }
        return $type($sign);
    }
}
