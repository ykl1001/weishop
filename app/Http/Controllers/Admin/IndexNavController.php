<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\AdminUser;
use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Form, Config,Cache;

/**
 * 前端底部设置
 */
class IndexNavController extends AuthController {
	
	/**
	 * 列表
	 */
	public function index() {
		$args = Input::all();
		//开通的城市列表
        $citys = $this->requestApi('city.getcitylists');
        array_unshift($citys['data'], ['id' => 0,'name' => '所有城市']);
        View::share('citys', $citys['data']);
 		$result = $this->requestApi('indexnav.lists',$args);
 		View::share('list', $result['data']['list']);
		return $this->display();
	}

	/**
	 * 添加
	 */
	public function create(){  
		View::share('icon_lists', self::getIconLists());
		//开通的城市列表
        $citys = $this->requestApi('city.getcitylists');
        array_unshift($citys['data'], ['id' => 0,'name' => '所有城市']);
        View::share('citys', $citys['data']);
		return $this->display('edit');
	}

	/**
	 * 编辑
	 */
	public function edit(){
		View::share('icon_lists', self::getIconLists());
		$args = Input::all();
		$result = $this->requestApi('indexnav.detail', $args);  
		View::share('data', $result['data']);
		//开通的城市列表
        $citys = $this->requestApi('city.getcitylists');
        array_unshift($citys['data'], ['id' => 0,'name' => '所有城市']);
        View::share('citys', $citys['data']);
		return $this->display();
	}

	/**
	 * 保存 
	 */
	public function save() {
		$args = Input::all();
		$result = $this->requestApi('indexnav.save', $args); 
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success($result['msg'], u('IndexNav/index'), $result['data']);
	} 

	/**
	 * 删除 
	 */
	public function destroy() {
		$args = Input::all();
		$args['id'] = explode(',', $args['id']);
		$result = $this->requestApi('indexnav.delete',$args);
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success($result['msg'], u('IndexNav/index'), $result['data']);
	} 

	public function getIconLists(){
		$file_str = file_get_contents(base_path()."/public/wap/community/newclient/index_iconfont/iconfont.css");
		preg_match_all('/\\\\\S{4}/', $file_str, $matches);  
		$icon_lists = str_replace('\\', '&#x', $matches[0]);  
		return $icon_lists;
	}

}
