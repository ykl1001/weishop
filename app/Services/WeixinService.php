<?php namespace YiZan\Services;

use YiZan\Models\Payment;
use YiZan\Models\User;
use YiZan\Utils\String;
use YiZan\Utils\Http;
use YiZan\Utils\Image;
use DB,Log;

class WeixinService extends BaseService {

    protected $wxPublic = null;

    public function wxPublic() {
        $config = Payment::where('code', 'weixinJs')->where('status', 1)->first();
        $payment['appId'] = $config->config['appId'];
        $payment['appSecret'] = $config->config['appSecret'];
        $payment['originalId'] = $config->config['originalId'];
        return $payment;
    }
    /**
     * 获取access_token
     * @return string
     */
    public function getAccessToken($force = false) {
       $wxPublic =  self::wxPublic();
		// $wxPublic['app_id'] = 'wxdec1e10223f8e4be';
		// $wxPublic['app_secret'] = '4af43812ccce2ff4df1071b931a504c2';
        // if (UTC_TIME + 1800 < $wxPublic['access_token_expired'] && !$force) {
            // $access_token = $wxPublic['access_token'];
        // } else {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' .$wxPublic['appId'] . '&secret=' .$wxPublic['appSecret'];
            $token_json = Http::get($url);
            $token_json = empty($token_json) ? false : @json_decode($token_json, true);
            if(!$token_json || empty($token_json['access_token'])){
                return $token_json;
            }
            $access_token = $token_json['access_token'];
            // SystemConfigService::updateConfig('access_token', $access_token);
            // SystemConfigService::updateConfig('access_token_expired', UTC_TIME + (int)$token_json['expires_in']);
        //}
        return $access_token;
    }

    //微信Js配置
    public function getweixin($extend_url){

        $config = Payment::where('code', 'weixinJs')->where('status', 1)->first();
        $payment['appId'] = $config->config['appId'];
        $payment['appSecret'] = $config->config['appSecret'];
        $payment['originalId'] = $config->config['originalId'];

       // $payment['appId'] = 'wxdec1e10223f8e4be';
       // $payment['appSecret'] = '4af43812ccce2ff4df1071b931a504c2';
       // $payment['originalId'] = 'gh_fc3e63540e87';
        $config = $payment;

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
            'url' => $extend_url
        );

        $jsapi_request['signature'] = self::weixinSign($jsapi_request, '', 'sha1');
        $jsapi_request['appId'] = $config['appId'];
        $jsapi_request['originalId'] =  $config['originalId'];
        return $jsapi_request;
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
     * 获取微信用户信息
     * @param  string $openid 用户openid
     * @return array
     */
    public function getUser($mobile,$nickname,$avatar,$openId) {
        $user = User::where('mobile',$mobile)->first(); //先用电话找 如果没找到
        $user2 = User::where('openid',$openId)->first(); //再用用openId找 如果没找到

        if(empty($user) && empty($user2)) {
            $crypt  = String::randString(6);
            $pwd    = md5(md5(String::randString(6,1)) . $crypt);

            $location = RegionService::getCityByIp(CLIENT_IP);
            $user = new User();
            $user['name']   = !empty($nickname) ? $nickname : substr($mobile,0,6).'****'.substr($mobile,-1,1);
            $user['name_match']   = String::strToUnicode($user['name']);
            $user['group_id']   = 1;
            $user['mobile']     = $mobile;
            $user['province_id']        = $location['province'];
            $user['city_id'] = $location['city'];
            $user['reg_ip']  = CLIENT_IP;
            $user['reg_province_id'] = $location['province'];
            $user['reg_city_id'] = $location['city'];
            $user['openid'] = $openId;

            $image_url = $avatar;

            $ch = curl_init();
            //设置选项，包括URL
            curl_setopt($ch, CURLOPT_URL, $image_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            //执行并获取HTML文档内容
            $content = curl_exec($ch);
            //释放curl句柄
            curl_close($ch);
            //打印获得的数据

            // $content = \YiZan\Utils\Http::get($image_url);
            $name = Image::getFormArgs(1);
            $path = $name['image_url'];
            $imageUrl = Image::upload($content, $path,1);
            $user['avatar']     = !empty($imageUrl) ? $imageUrl : '';

            $user['reg_time']= UTC_TIME;
            $user['crypt']= $crypt;
            $user['pwd']= $pwd;
            $user->save();

            $user = User::where('id',$user->id)->first()->toArray();
        }

        if(!empty($user2)){
            return $user2;
        }else{
            return $user;
        }
    }

    public function getWeixinUrl($url, $args = '') {
        $result = self::requestWeixinUrl($url, 'get', $args);
        return $result;
    }

    public function postWeixinUrl($url, $args = array()) {
        return self::requestWeixinUrl($url, 'post', $args);
    }

    /**
     * 获取微信用户的URL请求路径
     * @param  string $url 请求分组的方法
     * @return array
     */
    public function getUserFirst($openid,$openShareUserId = 0) {
        $result['nickname'] = SystemConfigService::getConfigByGroup('admin')['site_name'];
        if($openShareUserId > 0){
            $openid = User::where('id',$openShareUserId)->pluck("openid");
        }
        if(!$openid){
            return $result;
        }
        $result['data'] = 0;
        $access_token = self::getAccessToken();
        if (!empty($access_token)) {
            $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$openid}&lang=zh_CN";
            $result = Http::get($url);
            $result = empty($result) ? false :@json_decode($result, true);
            if ($result && !empty($result['nickname'])) {
                $result['headimgurl'] = empty($result['headimgurl']) ? '' : substr($result['headimgurl'], 0, -1).'132';
            }
        }
        return $result;
    }

    /**
     * 获取微信用户的URL请求路径
     * @param  string $url 请求分组的方法
     * @return array
     */
    private function requestWeixinUrl($url, $type = 'get', $args = '') {
        $return = ['status' => 0, 'data' => false,'code'=>0];
        $access_token =self::getAccessToken();
        if (!empty($access_token)) {
            set_time_limit(0);
            $get_count = 0;
            do{
                $url = "https://api.weixin.qq.com/cgi-bin/{$url}?access_token={$access_token}";
                if ('get' == $type) {
                    $result = Http::get($url,$args,30);
                } else {
                    $result = Http::post($url,$args,false,30);
                }
                $result = empty($result) ? false : @json_decode($result, true);
                if($result['errcode'] == '40001'){
                    $return['code'] = $result['errcode'];
                    return $return;
                }
                if ($result) {
                    if (isset($result['errcode']) && $result['errcode'] != 0) {
                        if ($result['errcode'] != 40001) {
                            $get_count = 1;
                            $return['code'] = $result['errcode'];
                        }
                    } else {
                        $get_count = 1;
                        $result['status'] = 1;
                        $return = $result;
                    }
                } else {
                    $return['data'] = null;
                    $return['code'] = $result['errcode'];
                }

                if ($get_count < 1) {
                    $access_token = self::getAccessToken(true);
                }
                $get_count++;
            }while($get_count < 2);
        }
        return $return;
    }
}