<?php namespace YiZan\Http\Controllers;

use YiZan\Http\Controllers\YiZanController;
use YiZan\Utils\Encrypter;
use Illuminate\Contracts\Encryption\DecryptException;
use Request, Config, Input, Lang;

abstract class YiZanApiController extends YiZanController {
	/**
     * 成功
     */
    const SUCCESS = 0;

	protected $token;
	protected $tokenId = 0;
	protected $tokenPwd = "";

	/**
	 * 不需要验证的ACTION
	 * @var [type]
	 */
	protected $allowAction = [];

	/**
	 * 是否为安全IP
	 * @var boolean
	 */
	protected $isSecurityIp = false;

	/**
	 * 是否加密数据
	 * @var boolean
	 */
	protected $isEncrypterData = true;

	private $_request = [];

	public function __construct(){

		//请求不为POST退出
		if (!Request::isMethod('post')) {
			//$this->outputCode(99998);
		}

		//检测是否为安全IP
		if (!$this->isSecurityIp) {
			$this->isSecurityIp = in_array(Request::ip(), Config::get('app.security_ips'));
		}

		//初始化信息
		if($tokenStatus = $this->initialize()){
			//验证Token
			$tokenStatus = $this->checkToken();
		}

		//检测TOKEN
		if (!$this->isSecurityIp && //不为安全IP时
			!in_array(API_PATH, $this->allowAction) && //接口需要验证安全时
			!$tokenStatus) { //Token验证失败时
			//$this->outputCode(99997);
		}
		parent::__construct();
	}

	/**
	 * 获取请求的数据
	 */
	protected function request($key, $default = null) {
		return isset($this->_request[$key]) ? $this->_request[$key] : $default;
	}

	/**
	 * 初始化信息
	 * @return boolean
	 */
	protected function initialize() {
		$this->token	= Input::get('token');
		$this->_request = Input::all();

        $data = Input::get('data');
        if(!empty($data)){
            unset($this->_request['data']);

            $json_data = @json_decode($data, 1);

            if ($json_data) {
                $this->_request = array_merge($this->_request, $json_data);
                $data = '';
            }
        }

		if (!$this->isSecurityIp) {
			$encrypter = new Encrypter(md5($this->token . $this->tokenId));
			if(!empty($data)){
				$data = $encrypter->decrypt($data);
			}
            if ($data) {
                $this->_request = array_merge($this->_request, $data);
            }
		}
		if(!defined('CLIENT_IP')){
			define('CLIENT_IP', $this->request('ip', Request::ip()));
		}
        
		return true;
	}

	/**
	 * 检测Token
	 * @return boolean
	 */
	protected function checkToken() {
		try {
            if (!Request::isMethod('post')) {
                $this->token = str_replace(' ', '+',$this->token);
            }
			$encrypter = $this->getEncrypter($this->tokenId, $this->tokenPwd);
			$data = $encrypter->decrypt($this->token);

			if (!isset($data['id']) ||
				!isset($data['pwd']) ||
				$data['id'] != $this->tokenId ||
				$data['pwd'] != $this->tokenPwd) {

				return false;
			}
			return true;
		} catch(DecryptException $e) {
			return false;
		}
	}

	/**
	 * 创建Token
	 * @param  integer $id 	   编号
	 * @param  string  $pwd    密码
	 * @return string          生成的TOKEN
	 */
	protected function createToken($id = 0, $pwd = '') {
		$this->tokenId  = $id;
		$this->tokenPwd = $pwd;
		$encrypter = $this->getEncrypter($id, $pwd);
		$data = [
			'id'  => $id,
			'pwd' => $pwd,
			'ip'  => Request::ip()
		];
		$this->token = $encrypter->encrypt($data);

		return $this->token;
	}

	/**
	 * 获取加密解密处理类
	 * @param  integer $id 	   编号
	 * @param  string  $pwd    密码
	 * @return Encrypter       加解密处理类
	 */
	private function getEncrypter($id = 0, $pwd = '') {
		$seed = base_convert($id . Config::get('app.key') . $pwd, 16, 35);
		$encrypter = new Encrypter(md5($seed));
		return $encrypter;
	}

	/**
	 * 输出状态码
	 * @param  int $code 状态码
	 * @return void
	 */
	protected function outputCode($code, $msg = '') {
		$info = array(
			'code' 	=> $code,
			'data' 	=> null,
			'msg'	=> $msg
		);
        return $this->output($info);
	}

	/**
	 * 输出数据
	 * @param  array $data 数据
	 * @return void
	 */
	protected function outputData($data = null, $msg = '') {
		$info = array(
			'code' 	=> 0,
			'data' 	=> $data,
			'msg'	=> $msg
		);
		return $this->output($info);
	}

	/**
	 * 根据状态码获取提示
	 * @param  int $code 状态码
	 * @return string    提示
	 */
	protected function getApiMsg($code){
		return Lang::get('api.code.'.$code);
	}

	/**
	 * 输出API数据
	 * @param  array $info 输出信息
	 * @return void
	 */
	protected function output($info) {
		if($info['code'] > 0 && !$info['flag']){
			$info['msg'] = $this->getApiMsg($info['code']);
		}

		if (Config::get('app.debug')) {
			$info['debug'] = "REQUEST：".print_r($_REQUEST, true);
		}
        if (Config::get('app.is_local_request')){
            $info = json_decode(json_encode($info), true);

            return $info;
        } else {
            //加密数据
            if ($info['data'] && $this->isEncrypterData) {
                $encrypter = new Encrypter(md5($this->token . $this->tokenId));
                $info['data'] = $encrypter->encrypt($info['data']);
            }
            header("Content-type:text/json");
            //ob_clean();
            die(json_encode($info));
        }

	}
}