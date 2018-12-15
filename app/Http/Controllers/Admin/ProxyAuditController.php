<?php 
namespace YiZan\Http\Controllers\Admin;
 
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
		$result = $this->requestApi('proxy.detail', $args); 
		$result['data']['pwd'] = '';
		View::share('data', $result['data']);
		return $this->display();
	} 

	/**
	 * 审核代理
	 */
	public function audit() {
		$args = Input::all();
		$result = $this->requestApi('proxy.audit', $args); 
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success($result['msg'], u('ProxyAudit/index'), $result['data']);
	} 

	/**
	 * 删除代理
	 */
	public function destroy() {
		$args = Input::all(); 
		$args['id'] = explode(',', $args['id']);
		$result = $this->requestApi('proxy.delete',$args);
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success($result['msg'], u('ProxyAudit/index'), $result['data']);
	} 
}
