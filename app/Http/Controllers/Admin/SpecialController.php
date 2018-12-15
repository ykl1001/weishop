<?php
namespace YiZan\Http\Controllers\Admin;

use YiZan\Http\Requests\Admin\PromotionPostRequest;
use YiZan\Utils\Time;

use View, Input, Form, Lang, Response;
/**
 * 专题券管理
 */
class SpecialController extends AuthController {
	/**
	 * 专题列表
	 */
	public function index() {
		$result = $this->requestApi('special.lists', Input::all());
		if($result['code']==0) {
			View::share('list', $result['data']);
		}
		return $this->display();
	}

	/**
	 *编辑专题
	 */
	public function edit(){
		$result = $this->requestApi('special.get', Input::all());
		if($result['code'] > 0) {
			return $this->error($result['msg']);
		}
		View::share('data', $result['data']);

		return $this->display();
	}

	/**
	 *创建专题
	 */
	public function create(){
		return $this->display("edit");
	}

	/**
	 *添加/编辑专题
	 *
	 */
	public function save(){
        $args = Input::get();
		$result = $this->requestApi("special.save",$args);
		if($result['code'] > 0) {
			return $this->error($result['msg'], u('Special/index'));
		}
		return $this->success($result['msg']);
	}





}
