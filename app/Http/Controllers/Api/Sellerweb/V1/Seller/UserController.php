<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb\Seller;

use YiZan\Services\SellerUserService;
use YiZan\Http\Controllers\Api\Sellerweb\BaseController;
use Lang, Validator;

/**
 * 管理员
 */
class UserController extends BaseController 
{
    /**
     * 帐号名称不能为空
     */
    const NAME_EMPTY = 10101;
    /**
     * 帐号不存在
     */
    const NAME_NOT_EXIST = 10102;
    /**
     * 密码不能为空
     */
    const PASSWORD_EMPTY = 10103;
    /**
     * 密码错误
     */
    const PASSWORD_ERROR = 10104;
    /**
     * 帐号不存在
     */
    const MOBILE_LOCK = 10105;
    /**
     * 帐号已经存在
     */
    const NAME_EXIST = 10106;
    /**
     * 角色不存在
     */
    const ROLE_NOT_EXIST = 10107;
    /**
     * 旧密码错误
     */
    const OLD_PASSWORD_ERROR = 10108;
    /**
     * 新密码不能为空
     */
    const NEW_PASSWORD_EMPTY = 10109;
    /**
     * 账号已锁定
     */
    const USER_LOGIN_LOCK = 10105;
    /**
     * 管理员登录
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
		    'name.required'	    => self::NAME_EMPTY,
		    'pwd.required' 		=> self::PASSWORD_EMPTY
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
        
        $user = SelllerUserService::getByName($name);

		if (!$user){
            //未找到会员时
			$result['code'] = self::NAME_NOT_EXIST;
	    	return $this->output($result);
		}

        $pwd = md5(md5($pwd) . $user->crypt);

        //登录密码错误
		if ($user->pwd != $pwd) {
			$result['code'] = self::PASSWORD_ERROR;
	    	return $this->output($result);
		}

        //账号锁定
        if($user->status == 0)
        {
            $result['code'] = self::USER_LOGIN_LOCK;
            return $this->output($result);
        }

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

    /**
     * 管理员列表
     */
    public function lists() {
        $data = SellerUserService::getAdminUserlist(
                $this->sellerId,
                max((int)$this->request('page'), 1), 
                max((int)$this->request('pageSize'), 20)
            );
		return $this->outputData($data);
    }
    /**
     * 添加管理员
     */
    public function create() {
        $result = SellerUserService::saveAdminUser(
            0,
            $this->request('name'),
            $this->request('pwd'),
            (int)$this->request('rid'),
            (int)$this->request('relation_id')
        );
        
		return $this->output($result);
    }

    /**
     * 获取管理员
     */
    public function get() {
        $data = SellerUserService::getById(
            $this->sellerId,
            (int)$this->request('id')
        );
        return $this->outputData($data == false ? [] : $data->toArray());
    }

    /**
     * 更新管理员
     */
    public function update() {
        $result = SellerUserService::saveAdminUser(
                (int)$this->request('id'), 
                $this->request('name'), 
                $this->request('pwd'), 
                (int)$this->request('rid'),
                (int)$this->request('relation_id')
            );
		return $this->output($result);
    }

    /**
     * 删除管理员
     */
    public function delete() {
        $result = array (
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.delete_info')
		);
        
        SellerUserService::deleteAdminUser(
            $this->request('id')
        );
        return $this->output($result);
    }

    /**
     * 修改密码
     */
    public function repwd() {
        $result = SellerUserService::updateAdminUserPassword(
                (int)$this->request('id'), 
                $this->request('oldPwd'), 
                $this->request('newPwd')
            );
        return $this->output($result);
    }
    /**
     * 修改密码
     */
    public function updatesql() {
        $result = SellerUserService::updatesql(
            $this->adminId,
            $this->request('sysVersion')

        );
        return $this->output($result);
    }
}