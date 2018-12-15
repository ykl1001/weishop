<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Models\Article;
use View, Input, Lang, Route, Page,Response;

/**
 * 黄页管理
 */
class PropertySystemController extends AuthController {
	/**
	 * 黄页列表
	 */
	public function index() {
		$post = Input::all();
		!empty($post['title']) ? $args['title'] = strval($post['title']) : null; 
		!empty($post['page']) ? $args['page'] = intval($post['page']) : $args['page'] = 1;
		$result = $this->requestApi('propertysystem.lists', $args);
		View::share('list', $result['data']['list']); 
		return $this->display();
	}

	/**
	 * 黄页添加-编辑详细
	 */
	public function edit() { 
		$result = $this->requestApi('propertysystem.get', Input::all());
		View::share('data', $result['data']); 
		return $this->display();
	}

	/**
	 * 黄页添加-编辑处理
	 */
	public function create() { 
		return $this->display('edit');
	} 

	public function save() {
		$args = Input::all();
		$result = $this->requestApi('propertysystem.save',$args);


		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('PropertySystem/index'), $result['data']);
	}

	/**
	 * 黄页分类-删除分类
	 */
	public function destroy() {
		$args = Input::all();
		$args['id'] = explode(',', $args['id']);
		if( !empty( $args['id'] ) ) {
			$result = $this->requestApi('propertysystem.delete',$args);
		}
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98005') , u('PropertySystem/index'), $result['data']);
	}

	public function updateStatus() {
		$args = Input::all();
		$result = $this->requestApi('propertysystem.updateStatus',$args);
		return Response::json($result);
	}
}
