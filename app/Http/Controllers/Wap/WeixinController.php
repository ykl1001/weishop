<?php namespace YiZan\Http\Controllers\Wap;

use YiZan\Services\SystemConfigService;
use YiZan\Utils\String;
use YiZan\Utils\Http;
use Session, Redirect;

/**
 * 微信控制器
 */
class WeixinController extends BaseController {
	public function authorize(){
        $url = urldecode($_REQUEST['url']);
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
                '&redirect_uri='.urlencode(u('Weixin/accesstoken'))
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
        $url = $url.'&openId='.$openid;
        return Redirect::to($url);
    }


    public function saas_authorize(){
        $url = urldecode($_REQUEST['redirect_uri']);
        if (empty($url)) {
            return $this->error('参数错误');
        }

        $result = $this->requestApi('config.getpayment',['code' => 'weixinJs']);
        if (!$result || $result['code'] != 0) {
            return $this->error('获取微信配置信息失败', $url);
        }

        $payment = $result['data'];
        $config = $payment['config'];

        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$config['appId'].
            '&redirect_uri='.$url
            .'&response_type=code&scope='.$_REQUEST['scope'].'&state=YZ#wechat_redirect';

        $info = array(
            'code' 	=> 0,
            'data' 	=> $url,
            'msg'	=> ''
        );

        header("Content-type:text/json");
        die(json_encode($info));
    }


    public function user(){
        $code = $_REQUEST['code'];

        $result = $this->requestApi('config.getpayment',['code' => 'weixinJs']);
        $payment = $result['data'];
        $config = $payment['config'];

        $wxurl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$config['appId'].
            '&secret='.$config['appSecret'].'&code='.$code.'&grant_type=authorization_code';
        $result = @file_get_contents($wxurl);
        $result = empty($result) ? false : @json_decode($result, true);

        if (!$result) {
            return $this->error('授权失败', u("User/login"));
        } elseif (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->error('授权失败:'.$result['errmsg'], u("User/login"));
        }

        $wxurl = "https://api.weixin.qq.com/sns/userinfo?access_token={$result['access_token']}&openid={$result['openid']}&lang=zh_CN";
        $result = @file_get_contents($wxurl);
        $result = empty($result) ? false : @json_decode($result, true);

        $info = array(
            'code' 	=> 0,
            'data' 	=> $result,
            'msg'	=> ''
        );
        header("Content-type:text/json");
        die(json_encode($info));
    }

    public function jsapi() {
        $request_url = $this->request('url');
        if (empty($request_url)) {
            $this->error('url地址为空!');
        }

        $result = $this->requestApi('config.getpayment',['code' => 'weixinJs']);
        $payment = $result['data'];
        $config = $payment['config'];

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
            'url' => $request_url
        );
        $jsapi_request['signature'] = self::weixinSign($jsapi_request, '', 'sha1');
        $jsapi_request['appId'] = $config['appId'];

        $info = array(
            'code' 	=> 0,
            'data' 	=> $jsapi_request,
            'msg'	=> ''
        );
        header("Content-type:text/json");
        die(json_encode($info));
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
	/**
     * 签名验证
     * @return boolean
     */
    private function checkSignature(){
        $signature =Input::get('signature');
        $timestamp =Input::get('timestamp');
        $nonce = Input::get('nonce');
		//token   SystemConfigService::getConfigByCode("api_token"); 为写入数据库 注意哦
		$token = "SDFGDFJCXBGJDSFDS458D4AS5D4AS";
		
        $tmpArr = [$token, $timestamp, $nonce];
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if($tmpStr == $signature){
            return true;
        }else{
            return false;
        }
    }
	function jsToken(){
        if(Input::get('echostr')){//网址接入
            $echostr = trim(Input::get('echostr'));
            if(self::checkSignature()){
                die($echostr);
            }
        }
		die("非法访问！！！");
    }
}