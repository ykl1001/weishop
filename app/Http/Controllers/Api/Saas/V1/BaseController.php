<?php 
namespace YiZan\Http\Controllers\Api\Saas;

use YiZan\Http\Controllers\YiZanApiController;
use Input, Lang, DB;

abstract class BaseController extends YiZanApiController {
	protected $admin = null;
	protected $adminId = 0;
	protected $cityIds = [];
	protected $allowAction = ['config.init', 'config.token'];
	protected $isEncrypterData = false;

	/**
	 * 初始化信息
	 * @return boolean
	 */
	protected function initialize() {
		return parent::initialize();
	}

	protected function getApiMsg($code){
		return Lang::get('api_saas.code.'.$code);
	}

}