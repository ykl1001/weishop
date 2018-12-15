<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Models\AdminUser;
use View, Input, Lang, Route, Page, Form, Config, Session, Redirect;

/**
 * 后台首页
 */
class AdminUserController extends AuthController {
	/**
	 * 服务器信息
	 */
	public function index() {
	    $args = Input::all();
		!empty($post['page']) ? $args['page'] = intval($post['page']) : $args['page'] = 1;
		!empty($post['pageSize']) ? $args['pageSize'] = intval($post['pageSize']) : $args['pageSize'] = 20;
		$result = $this->requestApi('seller.user.lists',$args);
		if($result['code']==0) {
			$list = $result['data']['list'];
			$totalCount = $result['data']['totalCount'];
			View::share('list', $list);
		} 
		return $this->display();
	}
	/**
	 * 创建-修改管理员
	 */
	public function edit() {
	    $args = Input::all();

	    if( !empty($args['id']) ) {
	        $args['id'] = $args['id'];
	        $result = $this->requestApi('seller.user.get',$args);
	        if(empty($result['data'])){
                return $this->error($result['msg'], u('AdminUser/index'), $result['data']);
            }
	        $role = $this->requestApi('seller.role.lists');
	        unset($result['data']['pwd']);//清除密码
	        $result['data']['ts'] = Lang::get("admin.code.11007");

	        View::share('data', $result['data']);
	        View::share('role', $role['data']['list']);
	    }
	    return $this->display();
	}
	
	public function create() {
	    //分组
	    $role = $this->requestApi('seller.role.lists');
	    View::share('seller', Session::get('seller'));
	    View::share('role', $role['data']['list']);
	    return $this->display('edit');
	}
	
	public function save() {
	    $args = Input::all();
	    if( empty($args['rid']) ) return $this->error(Lang::get('admin.code.11002'));
	
	    if( !empty($args['id']) ) {
	        $result = $this->requestApi('seller.user.update',$args);
	        $url = u('AdminUser/edit',['id'=>$args['id']]);
	    }
	    else {
	        if( empty($args['name']) ) return $this->error(Lang::get('admin.code.11000'));
	        if( empty($args['pwd']) ) return $this->error(Lang::get('admin.code.11001'));
	        $result = $this->requestApi('seller.user.create',$args);
	        $url = u('AdminUser/create');
	    }
	
	    if( $result['code'] > 0 ) {
	        return $this->error($result['msg'], $url, $result['data']);
	    }
	    return $this->success(Lang::get('admin.code.98008'), $url, $result['data']);
	}
	
	/**
	 * 删除管理员
	 */
	public function destroy() {
	    $args = Input::all();
	    $args['id'] = explode(',', $args['id']);
	    if( !empty( $args['id'] ) ) {
	        $result = $this->requestApi('seller.user.delete',$args);
	    }
	    if( $result['code'] > 0 ) {
	        return $this->error($result['msg']);
	    }
	    return $this->success(Lang::get('admin.code.98004'), u('AdminUser/index'), $result['data']);
	}
	
	/**
	 * 重新设置密码 编辑功能修改
	 */
	public function repwd(){
	    /*$data = Input::all();
	     if( $data['id'] != $this->adminId ) {
	     return Redirect::to('AdminUser/index');
	     }*/
	    $data['id'] = $this->adminId;
	    View::share('data',$data);
	    return $this->display();
	}
	
	public function checkRepwd() {
	    $args = Input::all();
	    if( $args['id'] != $this->adminId || !isset($args['id'])) {
	        return $this->success(Lang::get('admin.code.98012'), u('Public/logout'));
	    }
	    if( empty($args['id']) )  return $this->error( Lang::get('admin.noId') );
	    if( empty($args['oldPwd']) ) return $this->error( Lang::get('admin.code.11003') );
	    if( empty($args['newPwd']) ) return $this->error( Lang::get('admin.code.11004') );
	    if( empty($args['reNewPwd']) ) return $this->error( Lang::get('admin.code.11005') );
	    if( $args['newPwd'] != $args['reNewPwd']) return $this->error( Lang::get('admin.code.11006') );
	    if( strlen($args['newPwd']) < 6 ) return $this->error( Lang::get('admin.code.11008') );
	
	    $result = $this->requestApi('admin.user.repwd',$args);
	
	    if( $result['code'] == 0 ) {
	        return $this->success($result['msg'], u('Public/logout'));
	    }
	    else {
	        return $this->error($result['msg'], u('AdminUser/repwd'), $result['data']);
	    }
	}
	
	public function updateStatus() {
	    $args = Input::all();
	    $result = $this->requestApi('admin.user.updateStatus',$args);
	    return Response::json($result);
	}

}
