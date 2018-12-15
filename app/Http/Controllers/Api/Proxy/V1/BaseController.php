<?php 
namespace YiZan\Http\Controllers\Api\Proxy;

use YiZan\Http\Controllers\YiZanApiController;
use YiZan\Services\Proxy\ProxyService;
use YiZan\Services\RegionService;
use Input, Lang, DB;

abstract class BaseController extends YiZanApiController {
	protected $proxy = null;
	protected $proxyId = 0;
	protected $cityIds = [];
	protected $allowAction = ['config.init', 'config.token'];
	protected $isEncrypterData = false;

	/**
	 * 初始化信息
	 * @return boolean
	 */
	protected function initialize() {
		$this->tokenId = $this->proxyId	= (int)Input::get('proxyId');
		$this->tokenPwd = '';
		if ($this->proxyId > 0) {
			$this->proxy = ProxyService::getById($this->proxyId);
			if (!$this->proxy) {//找不到管理员信息,则返回false
				return false;
			} 
            $provinces = RegionService::getProvinces();
            foreach($provinces as $province) {
                $this->cityIds[] = $province['id'];
            }
			$this->tokenPwd = $this->proxy->pwd;
		}
        
		return parent::initialize();
	}

	/**
	 * 创建Token
	 * @param  integer $proxyId 代理编号
	 * @param  string  $pwd     密码
	 * @return string           生成的TOKEN
	 */
	protected function createToken($proxyId = 0, $pwd = '') {
		$this->proxyId = $proxyId;
		return parent::createToken($proxyId, $pwd);
	}

	protected function getApiMsg($code){
		return Lang::get('api_proxy.code.'.$code);
	}
 
}