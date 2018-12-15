<?php 
namespace YiZan\Http\Controllers\Api\Proxy;

use YiZan\Services\Proxy\ProxyService;
use YiZan\Http\Controllers\Api\Proxy\BaseController;
use Lang, Validator;

/**
 * 代理
 */
class PublicController extends BaseController 
{
    
    /**
     * 代理登录
     */
    public function login() {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.user_login')
		);

	    $rules = array(
		    'name' => ['required'],
		    'pwd' 	 => ['required']
		);

		$messages = array(
		    'name.required'	    => 10101,
		    'pwd.required' 		=> 10103
		);

		$name = $this->request('name');
        
		$pwd = strval($this->request('pwd'));

		$validator = Validator::make([
			'name' => $name,
			'pwd'  => $pwd
		], $rules, $messages);
        
        // 验证信息
		if ($validator->fails()) {
	    	$messages = $validator->messages();
	    	$result['code'] = $messages->first();
	    	return $this->output($result);
	    }
        
        $user = ProxyService::getByName($name);

		if (!$user){
            //未找到会员时
			$result['code'] = 10102;
	    	return $this->output($result);
		}

        $pwd = md5(md5($pwd) . $user->crypt);

        //错误次数验证
        // if($user->error_login_times >= 5){
        //     $user->status = 0;
        //     $user->save(); 
        //     $result['code'] = 10112;
        //     return $this->output($result);
        // }

        //登录密码错误
		if ($user->pwd != $pwd) {
            $user->error_login_times += 1;
            $user->save(); 
			$result['code'] = 10104;
	    	return $this->output($result);
		}

        if(!$user->status){
            $result['code'] = 10111;
            return $this->output($result);
        }
        
        if(!$user->is_check){
            $result['code'] = 10113;
            return $this->output($result);
        }
        $user->error_login_times = 0;
        $user->login_time   = UTC_TIME;
        $user->login_ip     = CLIENT_IP;
        $user->login_count++;
        $user->save();
		
		$this->createToken($user->id, $user->pwd);
		$user = $user->toArray();
        
		$result['data'] = [
	    	'data'	=> $user,
	    	'token'	=> $this->token
	    ];
		return $this->output($result);
    }  

}