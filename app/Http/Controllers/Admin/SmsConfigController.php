<?php 
namespace YiZan\Http\Controllers\Admin; 

use YiZan\Utils\Http; 
use Cache, View, Input, Lang, Response;
/**
* 短信配置
*/
class SmsConfigController extends UserAppAdvController {  
	/**
	 * [index 短信账号配置]
	 * @return [type] [description]
	 */
	public function index() {

        $result = $this->requestApi('smsConfig.get');
        $data = [
            'SmsUserName'	=>	$result['data']['sms_user_name'],
            'SmsPassword'	=>	$result['data']['sms_password'],
        ];

        View::share('data', $data);
        return $this->display();
	}

	/**
	 * [save 保存短信配置]
	 * @return [type] [description]
	 */
	public function save() {
		$args = Input::all();
		$result = $this->requestApi('smsConfig.save',$args);

		if ($result['code'] == 0) {
            return  $this->success(Lang::get('admin.code.98008'), $url, $result['data']);
        } else {
            return $this->error($result['msg']);
        }
	}

	/**
	 * [surplus 查询剩余短息条数]
	 * @return [type] [description]
	 */
    public function surplus() {
        $res = [
            'data'=>null,
            'msg'=>"查询成功",
            'code'=>0
        ];
        $smsConfig = $this->requestApi('smsConfig.get');

        if( empty($smsConfig['data']['sms_user_name']) )
        {
            $res['code'] = "-1";
            $res['msg'] = "请设置短信账号";
            return Response::json($res);
        }

        if( empty($smsConfig['data']['sms_password']) )
        {
            $res['code'] = "-1";
            $res['msg'] = "请设置短信密码";
            return Response::json($res);
        }
        $data = json_encode(['user_name' => $smsConfig['data']['sms_user_name'], 'password' =>$smsConfig['data']['sms_password'] ]);
        $info = json_decode(Http::post('http://sms.fanwe.com/get', $data),1);

        $res['data'] = $info;
        return Response::json($res);
    }

}
