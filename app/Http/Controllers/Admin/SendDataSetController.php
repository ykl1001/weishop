<?php 
namespace YiZan\Http\Controllers\Admin;
use Input, View, Response, Lang;
/**
 * 配送设置
 */
class SendDataSetController extends AuthController {
	public function index() {
		$result = $this->requestApi('system.config.get', ['groupCode'=>'sendcenter']);
		if($result['code']==0){
			foreach ($result['data'] as $key => $value) {
				$data[$value['code']] = $value;
			}
			View::share('data', $data);
		}

		return $this->display();
	}

	public function save() {
		$post = Input::all();

		//参数验证
		if($post['systemSendFee'] > $post['systemSendStaffFee'])
		{
			return $this->error(Lang::get('api.code.11000'));
		}
		if(!is_numeric($post['systemStaffChangeHour']))
		{
			return $this->error(Lang::get('api.code.11001'));
		}


		$i = 0;
		foreach ($post as $key => $value) {
			if($key != 'id')
			{
				$args[$i]['code'] = snake_case($key);
				$args[$i]['val'] = $value;
				$i++;
			}
		}
		$result = $this->requestApi('system.config.update', ['configs'=>$args]);
		if($result['code'] == 0)
		{
			return $this->success($result['msg'], u('SendDataSet/index'));
		}
		return $this->error($result['msg']);
	}

}
