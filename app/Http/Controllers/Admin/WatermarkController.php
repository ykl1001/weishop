<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\Config;
use View, Input, Lang;

/**
 * 系统配置
 */
class WatermarkController extends AuthController {
	public function index(){
		$args = Input::all();
		$args['groupCode'] = "admin_image";
		$result = $this->requestApi('system.config.get',$args);


        View::share('list',$result['data']);

		return $this->display();
	}


	public function save(){
		$data = Input::all();
		$i = 0; 
		foreach ($data as $key => $value) {
			$args[$i]['code'] = trim($key);
			$args[$i]['val'] = trim($value);
			$i++;
		}
		$args['configs'] = $args;
		$result = $this->requestApi('system.config.update',$args);
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('Watermark/index'), $result['data']);
	}
	

}
