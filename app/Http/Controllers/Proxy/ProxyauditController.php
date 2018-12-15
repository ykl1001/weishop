<?php 
namespace YiZan\Http\Controllers\Proxy;
 
use View, Input, Lang, Response;

/**
 * 代理审核管理
 */
class ProxyAuditController extends AuthController {

	/**
	 * 代理列表
	 */
	public function index() {
		$args = Input::all(); 
		$result = $this->requestApi('proxy.authlists',$args);
		if( $result['code'] == 0 ) {
			View::share('list', $result['data']['list']);
		}  
        return $this->display();
	} 

	/**
	 * 编辑代理
	 */
	public function edit(){
		$args = Input::all();
		$result = $this->requestApi('proxy.edit', $args); 
		$result['data']['pwd'] = '';
		View::share('data', $result['data']);
		return $this->display();
	} 
 
}
