<?php namespace YiZan\Http\Controllers;
use YiZan\Template\YiZanTemplate;
use YiZan\Utils\Http;
use YiZan\Utils\Page;
use View, Request, Lang, Input, Session, Cache, Config, Response,Redirect;

class YiZanViewController extends YiZanController {
	/**
	 * 基础TOKEN
	 * @var string
	 */
	private $_token;

	/**
	 * 安全TOKEN
	 * @var string
	 */
	private $_securityToken;

	/**
	 * API调用类型
	 * @var string
	 */
	protected $apiType;

	/**
	 * 调用模板
	 * @var string
	 */
	protected $tpl;

	/**
	 * 初始化信息
	 */
	public function __construct() {
		parent::__construct();
		//检测是否有基础信息
		if($this->tpl != 'install') {
			if (!Cache::has('system_config') || !Cache::has('payments')) {
				$result = $this->requestApi('config.init');
				if ($result['code'] > 0) {
					$this->error($result['msg']);
				} else {
					//保存网站配置信息
					$this->configInit($result['data']);

					//保存基础Token
					$this->baseTokenInit($result);
					//是否强制刷新缓存
					Session::put('is_clear_cache', true);
				}
			}elseif (!Session::has('base_token')) {//没有基础TOKEN时
				$result = $this->requestApi('config.token');
				if ($result['code'] > 0) {
					$this->error($result['msg']);
				} else {
					//保存基础Token
					$this->baseTokenInit($result);
				}
				Session::put('is_clear_cache', false);
			} else { 
				Session::put('is_clear_cache', false);
			}

			//设置基础TOKEN
			$this->_token = Session::get('base_token');
			
			//设置管理员TOKEN
			$this->setSecurityToken(Session::get('security_token'));
			Session::save();
		}
	}

	/**
	 * 系统加载Conifg信息
	 * @return [type] [description]
	 */
	protected function configInit($data) {
		Cache::forever('system_config', $data['configs']);
		Cache::forever('invitation', $data['invitation']);
		Cache::forever('indexnav', $data['indexnav']);
	}

	/**
	 * 系统加载基础Token信息
	 * @return [type] [description]
	 */
	protected function baseTokenInit($result) {
		Session::put('base_token', $result['token']);
		Session::save();
	}

	/**
	 * 获取相关配置信息
	 * @return [type] [description]
	 */
	protected function getConfig($code = '') {
		$site_config = Cache::get('system_config');
		if (empty($code)) {
			return $site_config;
		}
		return isset($site_config[$code]) ? $site_config[$code] : null;
	}

	/**
	 * 设置管理员TOKEN
	 * @param [type] $token [description]
	 */
	protected function setSecurityToken($token) {
		$this->_securityToken = $token;
		Session::put('security_token', $token);
		Session::save();
	}

	/**
	 * 调用API
	 * @param  string 	$method 接口名称
	 * @param  array  	$args   参数
	 * @param  array  	$data   提交数据
	 * @return array          	API返回数据
	 */
	protected function requestApi($method, $args = [], $data = []){

		if (empty($method)) {
			return [
				'code'=> 99999,
				'msg' => '接口名称不能为空',
			];
		}

		$data['token'] 	 	= empty($this->_securityToken) ? $this->_token : $this->_securityToken;
		$data['ip']			= CLIENT_IP;
		$data['userAgent'] 	= Request::header('USER_AGENT');
		$data['data'] 	 	= json_encode($args);
        $data["NotEncrypterData"] = "true";

        if(Config::get('app.is_local_request')){
            $response =  $this->localRequest($method, $data);
        } else {
            $url = Config::get('app.api_url.'.$this->apiType).$method;
            $response_html = $response = Http::post($url, $data);
            $response = empty($response) ? false : @json_decode($response, true);
            if (!$response) {
                if (Config::get('app.debug')) {
                    print_r($response_html);
                }
                return [
                    'code'=> 99999,
                    'msg' => '调用接口失败',
                ];
            }
        }
		//已经被禁用，则强制退出登录
		if(isset($response['code']) && ($response['code']==10115 && $this->apiType == 'wap.') || ($response['code']==99996 && $this->apiType == 'wap.') ){
			//Session::flush();//清除所有session
			Session::put('user', null);
			Session::save();
			dd($response);
		};
		//检测是否有分页
		if (isset($response['data']['list']) && isset($response['data']['totalCount'])) {
			$pageSize = isset($args['pageSize']) ? $args['pageSize'] : 20;
			$page_args = Input::all();
			$page_args['ca'] = CONTROLLER_NAME.'/'.ACTION_NAME;
			$pager = new Page($response['data']['totalCount'], $page_args, $pageSize);

			View::share('pager', $pager->nums());
		}
		
		return $response;
	}


    /**
     * 本地请求,不用调用curl
     * @param $method 方法
     * @param $data 数据
     * @return mixed
     */
    protected function localRequest($method, $data) {

        spl_autoload_register(function($class){
            if (strpos($class, 'YiZan\Http\Controllers\Api') !== 0) {
                return;
            }

            $class = str_replace('YiZan\Http\Controllers\Api\\', '', $class);
            $paths = explode('\\', $class);
            $type = array_shift($paths);
            $controller = array_pop($paths);
            if (count($paths) > 0) {
                $controller = implode('/', $paths) . '/' . $controller;
            }
            $class = base_path() . '/app/Http/Controllers/Api/' . $type . '/V1/' . $controller . '.php';
            if (is_file($class) && file_exists($class)) {
                include_once $class;
            }
        });
        error_reporting(E_ERROR);
        $old_request_args = Request::all();
        Request::replace($data);
        $paths = explode('.', $method);
        $action = array_pop($paths);
        $controller = ucfirst(array_pop($paths)) .'Controller';//取得控件器名称
        foreach($paths as $path){
            $controller = ucfirst($path).'\\'.$controller;
        }
        $controller = '\YiZan\Http\Controllers\Api\\' . ucfirst($this->apiType) . '\\' . $controller;
        $controller = new $controller;
        $result = $controller->$action();
        Request::replace($old_request_args);
        return $result;
    }
	/**
	 * 输出错误信息
	 * @param  string $msg  提示信息
	 * @param  string $url  跳转链接
	 * @param  array  $data 相关参数
	 * @return void
	 */
	protected function error($msg = '', $url = '', $data = array()) {

		if (empty($msg)) {
			$msg = '操作失败';
		}
		return $this->_output(false, $msg, $url, $data);
	}

	/**
	 * 输出成功信息
	 * @param  string $msg  提示信息
	 * @param  string $url  跳转链接
	 * @param  array  $data 相关参数
	 * @return void
	 */
	protected function success($msg = '', $url = '', $data = array()) {
		if (empty($msg)) {
			$msg = '操作成功';
		}
		return $this->_output(true, $msg, $url, $data);
	}

	private function _output($status, $msg = '', $url = '', $data = array()) {
		if (Input::ajax()) {
			$info = [];
			$info['status'] = $status;
			$info['msg'] 	= $msg;
			$info['url'] 	= $url;
			$info['data'] 	= $data;
			return Response::json($info);
		}else {
			View::share('msg', $msg);
			View::share('url', $url);
			View::share('data', $data);

			if ($status) {
				return View::make("{$this->tpl}._layouts.success");
			} else {
				return View::make("{$this->tpl}._layouts.error");
			}
		}
	}

	/**
	 * 获取模板路径
	 * @param  string $controllerName 控制器名称
	 * @param  string $actionName     方法名称
	 * @return string                 模板路径
	 */
	protected function getDisplayPath($controllerName, $actionName) {

		return "{$this->tpl}.{$controllerName}.{$actionName}";
	}

	/**
	 * 显示页面
	 * @param  string $actionName     控制器名称
	 * @param  string $controllerName 方法名称
	 * @return string                 页面内容
	 */
	protected function display($actionName = '', $controllerName = '') {
		$yiZanTemplate = new YiZanTemplate;
		$yiZanTemplate->init();

		if ($controllerName === '') {
			$controllerName = CONTROLLER_NAME;
		}

		if ($actionName === '') {
			$actionName = ACTION_NAME;
		}
		error_reporting(E_ERROR);
		$controllerName = strtolower($controllerName);
		$actionName 	= strtolower($actionName);
        ob_start("ob_gzhandler");
		return View::make($this->getDisplayPath($controllerName, $actionName));
	}



	// array_column 兼容 PHP 5.5以下版本
	public static function array_column($input, $columnKey, $indexKey = NULL)
	{
	    $columnKeyIsNumber = (is_numeric($columnKey)) ? TRUE : FALSE;
	    $indexKeyIsNull = (is_null($indexKey)) ? TRUE : FALSE;
	    $indexKeyIsNumber = (is_numeric($indexKey)) ? TRUE : FALSE;
	    $result = array();

	    foreach ((array)$input AS $key => $row)
	    { 
	      if ($columnKeyIsNumber)
	      {
	        $tmp = array_slice($row, $columnKey, 1);
	        $tmp = (is_array($tmp) && !empty($tmp)) ? current($tmp) : NULL;
	      }
	      else
	      {
	        $tmp = isset($row[$columnKey]) ? $row[$columnKey] : NULL;
	      }
	      if ( ! $indexKeyIsNull)
	      {
	        if ($indexKeyIsNumber)
	        {
	          $key = array_slice($row, $indexKey, 1);
	          $key = (is_array($key) && ! empty($key)) ? current($key) : NULL;
	          $key = is_null($key) ? 0 : $key;
	        }
	        else
	        {
	          $key = isset($row[$indexKey]) ? $row[$indexKey] : 0;
	        }
	      }

	      $result[$key] = $tmp;
	    }

	    return $result;
	}



}
