<?php namespace YiZan\Http\Controllers\Wap;
use YiZan\Utils\Http;
use YiZan\Utils\String;
use Session, Redirect, Cache, View;

/**
 * 微信控制器
 */
class WeixinOpenDoorController extends BaseController {
    public function index() {
        $weixinConfig = $this->getWeixinConfig($_REQUEST['openId']);
        View::share('config', $weixinConfig);
        return $this->display();
    }
    public function authorize(){
        $url = u('Unlock/index');
        if (empty($url)) {
            return $this->error('参数错误');
        }
        $result = $this->requestApi('config.getpayment',['code' => 'weixinJs']);
        if (!$result || $result['code'] != 0) {
            return $this->error('获取微信配置信息失败', $url);
        }

        $payment = $result['data'];
        $config = $payment['config'];
        Session::set('authorize_return_url', $url);
        Session::save();

        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$config['appId'].
            '&redirect_uri='.urlencode(u('WeixinOpenDoor/accesstoken'))
            .'&response_type=code&scope=snsapi_base&state=YZ#wechat_redirect';

        return Redirect::to($url);
    }

    public function accesstoken() {
        $code = $_REQUEST['code'];
        $url = Session::get('authorize_return_url');
        if (empty($code)) {
            return $this->error('授权失败', $url);
        }
        $result = $this->requestApi('config.getpayment',['code' => 'weixinJs']);
        if (!$result || $result['code'] != 0) {
            return $this->error('获取微信配置信息失败', $url);
        }

        $payment = $result['data'];
        $config = $payment['config'];

        $wxurl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$config['appId'].
            '&secret='.$config['appSecret'].'&code='.$code.'&grant_type=authorization_code';
        $result = @file_get_contents($wxurl);
        $result = empty($result) ? false : @json_decode($result, true);
        if (!$result) {
            return $this->error('授权失败', $url);
        } elseif (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->error('授权失败:'.$result['errmsg'], $url);
        }
        $openid = $result['openid'];
        $url = $url.'?openId='.$openid;
        return Redirect::to($url);
    }

    public function getWeixinConfig($openId) {

        $result = $this->requestApi('config.getpayment',['code' => 'weixinJs']);
        $payment = $result['data'];
        $config = $payment['config'];
        $weixinConfig = Cache::has('weixinConfig') ? Cache::get('weixinConfig') : ['access_token_expired' => 0, 'access_token' => ''];
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
            $access = [
                'access_token' => $access_token,
                'access_token_expired' => UTC_TIME + (int)$token_json['expires_in']
            ];
            Cache::forever('weixinConfig', $access);
        }

        $weixinTickets = Cache::has('weixinTickets') ? Cache::get('weixinTickets') : ['jsapi_ticket_expired' => 0, 'jsapi_ticket' => ''];
        if (UTC_TIME + 1800 < $weixinTickets['jsapi_ticket_expired']) {
            $jsapi_ticket = $weixinTickets['jsapi_ticket'];
        } else {
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi';
            $jsapi_json = Http::get($url);
            $jsapi_json = empty($jsapi_json) ? false : @json_decode($jsapi_json, true);
            if(!$jsapi_json || empty($jsapi_json['ticket'])){
                return false;
            }
            $jsapi_ticket = $jsapi_json['ticket'];
            $tickets = [
                'access_token' => $access_token,
                'access_token_expired' => UTC_TIME + (int)$token_json['expires_in']
            ];
            Cache::forever('weixinTickets', $tickets);
        }
        $url = Session::get('authorize_return_url');
        //js-sdk接口配置信息
        $jsapi_request = array(
            'jsapi_ticket' => $jsapi_ticket,
            'noncestr' => md5(String::randString(16)),
            'timestamp' => UTC_TIME,
            'url' => $url . '?openId='. $openId
        );
        $jsapi_request['signature'] = self::weixinSign($jsapi_request, '', 'sha1');
        $jsapi_request['appId'] = $config['appId'];
        $jsapi_request['openId'] = $openId;
        return $jsapi_request;
    }

    private static function weixinSign($args, $partnerKey = '', $type = 'md5') {
        ksort($args);
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