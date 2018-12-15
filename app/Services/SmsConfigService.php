<?php
namespace YiZan\Services;

use YiZan\Models\SystemConfig;
use Validator, DB;

class SmsConfigService extends BaseService{
    /**
     * [save 短信配置]
     * @param  [type] $SmsUserName [短信账号]
     * @param  [type] $SmsPassword [短信密码]
     * @return [type]              [description]
     */
    public static function save($SmsUserName, $SmsPassword) { 
    	$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> null
		);

		$rules = array(
		    'SmsUserName' => ['required'],
		    'SmsPassword' => ['required'],
		);

		$messages = array(
		    'SmsUserName.required'	=> '11001',
		    'SmsPassword.required' 	=> '11002',
		);
		$validator = Validator::make([
				'SmsUserName' => $SmsUserName,
				'SmsPassword' => $SmsPassword,
			], $rules, $messages);

		if ($validator->fails()) {//验证信息
	    	$messages = $validator->messages();
	    	$result['code'] = $messages->first();
	    	return $result;
	    }

	    DB::beginTransaction();
        try{
            SystemConfig::where('code', 'sms_user_name')->update(array('val' => $SmsUserName));
	    	SystemConfig::where('code', 'sms_password')->update(array('val' => $SmsPassword));
            DB::commit();
        } catch(Exception $e) {
            DB::rollback();
            $result['code'] = 30217;
        }
	    return $result;
    }

    /**
     * [get 获取短信配置信息]
     * @return [type] [description]
     */
    public static function get() {
    	$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> null
		);

    	$result['data']['sms_user_name'] =  SystemConfig::where('code', 'sms_user_name')->pluck('val');
	    $result['data']['sms_password']  =  SystemConfig::where('code', 'sms_password')->pluck('val');

	    return $result;
    }

}