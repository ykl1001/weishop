<?php 
namespace YiZan\Http\Controllers\Proxy; 

use View, Input, Response; 

/**
 * 代理管理
 */
class ProxyController extends AuthController {

    /**
     * 重新设置密码
     */
    public function repwd(){
        return $this->display();
    }

    /**
	 * 修改密码
	 */
	public function doRepwd() {
		$args = Input::all();  
		if( empty($args['oldPwd']) ){
			return $this->error( Lang::get('admin.code.11003') );
		}
		if( empty($args['newPwd']) ){
			return $this->error( Lang::get('admin.code.11004') );
		}
		if( empty($args['reNewPwd']) ){
			return $this->error( Lang::get('admin.code.11005') );
		}
		if( $args['newPwd'] != $args['reNewPwd']) {
			return $this->error( Lang::get('admin.code.11006') );
		}
		if( strlen($args['newPwd']) < 6 ) {
			return $this->error( Lang::get('admin.code.11008') );
		}
		$result = $this->requestApi('proxy.repwd',$args);
		
		if( $result['code'] == 0 ) {
			return $this->success($result['msg'], u('Public/logout'));
		}
		else {
			return $this->error($result['msg'], u('AdminUser/repwd'), $result['data']);
		}
	}

	/**
	 * 代理列表
	 */
	public function index(){
		$args = Input::all();

        $zx = array("1", "18", "795", "2250");
        View::share('zx', $zx);

		$result = $this->requestApi('proxy.lists', $args); 
		if ($result['code'] == 0){
			View::share('list', $result['data']['list']);
			View::share('proxy', $result['data']['proxy']);
		} 
        View::share('args', $args); 
		return $this->display();
	}

	/**
	 * 代理列表
	 */
	public function lists(){
		$args = Input::all();
		$result = $this->requestApi('proxy.childs', $args); 
		return Response::json($result['data']);
	}

	/**
	 * 创建代理
	 */
	public function create(){
        return $this->display('edit');
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

	/**
	 * 保存代理
	 */
	public function save(){
		$args = Input::all();
		$result = $this->requestApi('proxy.save', $args); 
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success($result['msg'], u('Proxy/index'), $result['data']);
	}
 	
}
