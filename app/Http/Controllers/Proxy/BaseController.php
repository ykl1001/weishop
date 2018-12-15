<?php namespace YiZan\Http\Controllers\Proxy;

use YiZan\Http\Controllers\YiZanViewController;
use View, Session, Input, Response;

class BaseController extends YiZanViewController {
	/**
	 * API调用类型
	 * @var string
	 */
	protected $apiType = 'proxy';

	/**
	 * 调用模板
	 * @var string
	 */
	protected $tpl = 'proxy';

	/**
	 * 代理信息
	 * @var array
	 */
	protected $proxy;

	/**
	 * 代理编号
	 * @var int
	 */
	protected $proxyId;

	/**
	 * 树形无限极分类数组
	 * @var Array
	 */
	protected $_cates = [];

	/**
	 * 初始化信息
	 */
	public function __construct() {
		parent::__construct();

		//设置代理
		$this->setProxy(Session::get('proxy'));
		View::share('site_config', $this->getConfig());
	}

	/**
	 * 设置代理
	 * @param array $proxy 代理信息
	 */
	protected function setProxy($proxy) {
		if ($proxy) {
			$this->proxy 	= $proxy;
			$this->proxyId 		= $proxy['id'];
			View::share('login_proxy', $this->proxy);
		}
		Session::put('proxy', $proxy);
	}

	/**
	 * 调用API
	 * @param  string 	$method 接口名称
	 * @param  array  	$args   参数
	 * @param  array  	$data   提交数据
	 * @return array          	API返回数据
	 */
	protected function requestApi($method, $args = [], $data = []){
		$data['proxyId'] = $this->proxyId;
		return parent::requestApi($method, $args, $data);
	} 
}
