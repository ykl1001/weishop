<?php 
namespace YiZan\Http\Controllers\Seller;

use View, Request, Lang, Cache, Redirect,Session;

abstract class AuthController extends BaseController {
	public function __construct() {
		parent::__construct();
		if ($this->sellerId < 1) {//如果未登录时,则退出
			Redirect::to(u('Public/login'))->send();
		}
		View::share('login_seller', $this->seller);
		
		$sauir = Session::get('seller_admin_user');
		if(!empty($sauir['id'])){
		    if(!$this->checkAuth()){
		        if(Request::ajax()){
		            die('{"status":false,"msg":"没有访问权限，请重新选择操作","url":"","data":[]}');
		        } else {
		            die($this->error("没有访问权限，请重新选择操作")->render());
		        }
		    }
		}
        View::share('sauir', $sauir);

    }

	
	/**
	 * Summary of checkAuth
	 * @return mixed
	 */
	protected function checkAuth()
	{   
	    $this->adminUser =  Session::get('seller_admin_user');
	    $this->adminId =  $this->adminUser['id'];
	    $role_auths = $this->getRoleControllerActionNavs($this->adminUser["role"]['id'], $this->adminUser["role"]["access"]);
	    if (isset($role_auths['actions'][strtolower(CONTROLLER_NAME . '/' . ACTION_NAME)])) {
	        return true;
	    }
	    return false;
	}
	
	/**
	 * [display description]
	 * @param  string $actionName     [description]
	 * @param  string $controllerName [description]
	 * @return [type]                 [description]
	 */
	protected function display($actionName = '', $controllerName = '') {
		//当不为AJAX请求时,获取页面菜单
		if (!Request::ajax() && !Request::wantsJson()) {
			$this->_menuInit();
		}
		return parent::display($actionName, $controllerName);
	}

	/**
	 * 初始化页面菜单
	 * @return [type] [description]
	 */
	private function _menuInit() {
		if ($this->seller['type'] == 3) { //物业公司单独模板
			$seller_auth = Lang::get('_seller_auth_property');
		}else {
            $seller_auth = Lang::get('_seller_auth');
        }

        //cz 有点长 看起头痛
        $sauir = Session::get('seller_admin_user');

        if(!empty($sauir['id'])){
            $this->adminUser =  Session::get('seller_admin_user');
            $this->adminId =  $this->adminUser['id'];
            $role_auths = $this->getRoleControllerActionNavs($this->adminUser["role"]['id'], $this->adminUser["role"]["access"]);
        }else{
            $role_auths = [];
        }
//        print_r($role_auths);exit;

        if (false) {
            $seller_controller_navs = Cache::get('_seller_controller_navs');
        } else {
            $seller_controller_navs = [];
            foreach ($seller_auth as $key => $nav) {
                if (isset($nav['nodes']) || isset($nav['controllers'])) {
                    foreach ($nav['nodes'] as $nkey => $node) {
                        foreach ($node['controllers'] as $ckey => $controller) {
                            $seller_controller_navs[$ckey] = ['keys' => [$key, $nkey], 'controller' => $controller];
                        }
                    }
                    foreach ($nav['controllers'] as $ckey => $controller) {
                        $seller_controller_navs[$ckey] = ['keys' => [$key], 'controller' => $controller];
                    }
                } else {
                    $seller_controller_navs[$nav['code']] = ['keys' => [$key]];
                }
            }
            Cache::forever('_seller_controller_navs', $seller_controller_navs);
        }
        //获取当前操作器导航
        $keys = $seller_controller_navs[CONTROLLER_NAME]['keys'];
        $controller_navs = [];
        $key = array_shift($keys);
        $seller_auth[$key]['selected'] = true;
        $seller_menus = &$seller_auth[$key];
        $controller = $seller_auth[$key];
        $controller_navs[] = ['name' => $controller['name'], 'url' => url($controller['url'])];
        if (count($keys) > 0) {
            $key1 = array_shift($keys);
            $controller = $controller['nodes'][$key1];
            $controller_navs[] = ['name' => $controller['name'], 'url' => url($controller['url'])];
            $seller_auth[$key]['nodes'][$key1]['selected'] = true;
            $seller_auth[$key]['nodes'][$key1]['controllers'][CONTROLLER_NAME]['selected'] = true;
        } else {
            $seller_auth[$key]['controllers'][CONTROLLER_NAME]['selected'] = true;
        }
        if (isset($seller_controller_navs[CONTROLLER_NAME]['controller'])) {
            $controller = $seller_controller_navs[CONTROLLER_NAME]['controller'];
            $controller_navs[] = ['name' => $controller['name'], 'url' => url($controller['url'])];
        }

		View::share('controller_navs', $controller_navs);
		//获取当前操作
		$controller_action = $controller['actions'][ACTION_NAME];
		View::share('controller_action', $controller_action);

		View::share('seller_auth', $seller_auth);
		View::share('role_auths', $role_auths);

		View::share('seller_menus', $seller_menus);
	}
	
	protected function getRoleControllerActionNavs($roldId, $accessList) {
	    $admin_auth = Lang::get('_seller_auth_property');
	     
	    $cache_key = '_seller_controller_action_navs';
	    //
	    if (Cache::get($cache_key)) {
	        $seller_controller_action_navs = Cache::get($cache_key);
	    } else {
	        $seller_controller_action_navs = [];
	        foreach ($admin_auth as $key => $nav) {
	            if (isset($nav['nodes'])) {
	                foreach ($nav['nodes'] as $nkey => $node) {
	                    foreach ($node['controllers'] as $ckey => $controller) {
	                        foreach ($controller['actions'] as $akey => $action) {
	                            $action['navs'] = [$key, $nkey];
	                            $seller_controller_action_navs[strtolower($ckey . '/' . $akey)] = $action;
	                        }
	                    }
	                }
	            }
	
	            if (isset($nav['controllers'])) {
	                foreach ($nav['controllers'] as $ckey => $controller) {
	                    foreach ($controller['actions'] as $akey => $action) {
	                        $action['navs'] = [$key];
	                        $seller_controller_action_navs[strtolower($ckey . '/' . $akey)] = $action;
	                    }
	                }
	            }
	        }
	        Cache::forever($cache_key, $seller_controller_action_navs);
	    }
	
	    $cache_key = '_seller_controller_action_navs_'.$roldId;
	    //
	    if (Cache::get($cache_key)) {
	        return Cache::get($cache_key);
	    } else {
	        $role_seller_controller_action_navs = ['navs' => [], 'controllers' => [], 'actions' => []];
	        foreach ($accessList as $access) {
	            $action_key = strtolower($access["controller"] . '/' . $access["action"]);
	            if (isset($seller_controller_action_navs[$action_key])) {
	                $action = $seller_controller_action_navs[$action_key];
	                $navs = $action['navs'];
	                $nav = array_shift($navs);
	                if (!isset($role_seller_controller_action_navs['navs'][$nav])) {
	                    $role_seller_controller_action_navs['navs'][$nav] = [
	                        'name'  => $admin_auth[$nav]['name'],
	                        'url'   => '',
	                        'nodes' => []
	                    ];
	                }
	
	                $node = '';
	                if (count($navs) > 0) {
	                    $node = array_shift($navs);
	                    if (!isset($role_seller_controller_action_navs['navs'][$nav]['nodes'][$node])) {
	                        $role_seller_controller_action_navs['navs'][$nav]['nodes'][$node] = [
	                            'name'  => $admin_auth[$nav]['nodes'][$node]['name'],
	                            'url'   => ''
	                        ];
	                    }
	                }
	
	                if (!isset($role_seller_controller_action_navs['controllers'][$access['controller']])) {
	                    if (empty($node)) {
	                        $name = $admin_auth[$nav]['controllers'][$access['controller']]['name'];
	                    } else {
	                        $name = $admin_auth[$nav]['nodes'][$node]['controllers'][$access['controller']]['name'];
	                    }
	
	                    $role_seller_controller_action_navs['controllers'][$access['controller']] = [
	                        'name'  => $name,
	                        'url'   => ''
	                    ];
	                }
	
	                if (isset($action['show_menu'])) {
	                    $url = $access["controller"] . '/' . $access["action"];
	                    if (empty($role_seller_controller_action_navs['navs'][$nav]['url'])) {
	                        $role_seller_controller_action_navs['navs'][$nav]['url'] = $url;
	                    }
	
	                    if (!empty($node) &&
	                        empty($role_seller_controller_action_navs['navs'][$nav]['nodes'][$node]['url'])) {
	                            $role_seller_controller_action_navs['navs'][$nav]['nodes'][$node]['url'] = $url;
	                        }
	
	                        if (empty($role_seller_controller_action_navs['controllers'][$access['controller']]['url'])) {
	                            $role_seller_controller_action_navs['controllers'][$access['controller']]['url'] = $url;
	                        }
	                }
	                $expands = $this->getActionExpands($access["action"], $action);
	                unset($action['expand']);
	
	                $role_seller_controller_action_navs['actions'][$action_key] = $action;
	
	                foreach ($expands as $expand) {
	                    $role_seller_controller_action_navs['actions'][strtolower($access["controller"] . '/' . $expand)] = $action;
	                }
	            }
	        }
	        Cache::forever($cache_key, $role_seller_controller_action_navs);
	        return $role_seller_controller_action_navs;
	    }
	}
	
	private function getActionExpands($akey, $action) {
	    $expands = [];
	    if ($akey == 'edit') {
	        $expands = ['detail', 'save', 'update', 'updateStatus'];
	    } elseif ($akey == 'create') {
	        $expands = ['detail', 'save', 'updateStatus'];
	    }
	    $expands = isset($action['expand']) ? array_merge($action['expand'], $expands) : $expands;
	    return array_unique($expands);
	}
	
	private function formatUrl(&$data, $old, $role_auths) {
	    $key = strtolower($old['url']);
	    if (isset($role_auths['actions'][$key])) {
	        $data['url'] = $old['url'];
	    }
	}
	
}
