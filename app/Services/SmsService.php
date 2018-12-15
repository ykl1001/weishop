<?php namespace YiZan\Services;

use YiZan\Models\SystemConfig;
use YiZan\Utils\Http;
use Config;

class SmsService extends BaseService {
    /**
     * [getSmsUrl 获取短信发送地址]
     * @return [type] [description]
     */
    public static function getSmsUrl() {
        return SystemConfig::where('code', 'sms_url')->pluck('val');
    } 

    /**
     * [getSmsUserName 获取短信账号]
     * @return [type] [description]
     */
    public static function getSmsUserName() {
        return SystemConfig::where('code', 'sms_user_name')->pluck('val');
    } 

    /**
     * [getSmsPasswold 获取短信密码]
     * @return [type] [description]
     */
    public static function getSmsPasswold() {
        return SystemConfig::where('code', 'sms_password')->pluck('val');
    }

    /**
     * 发送验证码
     * @param  string $code   发送数字
     * @param  string $mobile 手机号码
     * @return array          发送结果
     */
    public static function sendCode($code, $mobile) {
        $content = "{$code} （短信验证码，请勿泄露）";
        // return self::httpSend($content, $mobile, Config::get('app.sms.user_name'), Config::get('app.sms.user_pwd'), 2);
        return self::httpSend($content, $mobile, self::getSmsUserName(), self::getSmsPasswold(), 2);
    }

    /**
     * 发送文本
     * @param  string $content 文本内容
     * @param  string $mobile  手机号码
     * @return array           发送结果
     */
    public static function sendSms($content, $mobile) {
        // return self::httpSend($content, $mobile, Config::get('app.sms.user_name'), Config::get('app.sms.user_pwd'));
        return self::httpSend($content, $mobile, self::getSmsUserName(), self::getSmsPasswold());
    }

    /**
     * 提交短信
     * @param  string $content 发送内容
     * @param  string $mobile  手机号码
     * @param  string $user    用户名
     * @param  string $pwd     密码
     * @return array           发送结果
     */
    private static function httpSend($content, $mobile, $user, $pwd, $is_adv = 0){
        $data = array(
            'user_name' => $user,
            'password'  => $pwd,
            'content'   => $content,
            'mobile'    => $mobile,
			'is_adv'    => $is_adv
        ); 
        $params = json_encode($data);
        // return json_decode(Http::post(Config::get('app.sms.url'), $params),true);
        return json_decode(Http::post(self::getSmsUrl(), $params),true);
    }
}