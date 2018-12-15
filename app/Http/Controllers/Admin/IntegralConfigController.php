<?php 
namespace YiZan\Http\Controllers\Admin;

use View, Input, Lang, Route, Page, Form;
/**
 * 积分配置
 */
class IntegralConfigController extends AuthController {

	public function edit(){
		$args = Input::all();
		$args['groupCode'] = "integral_config";
		$result = $this->requestApi('system.config.get',$args);
		if( $result['code']==0 ) {
				View::share('list', $result['data']);
		}
		return $this->display();
	}


	public function save(){
		$data = Input::all();
		foreach ($data as $key => $value) {
            if($key != 'integral_remark' && $key != 'id') {
                if(!preg_match('/^[0-9]+$/',$value)){
                    return $this->error('只允许为正整数');
                }else{
                    $value = (int)$value;
                }
            }
            if(in_array($key, ['cost_integral', 'cash_integral', 'limit_cash_integral']) && ($value < 0 || $value > 100)) {
                return $this->error('比例为0-100之间的正整数');
            }
			$args[] = [
                'code' => $key,
                'val' => $value
            ];
		}
		$_args['configs'] = $args;
		$result = $this->requestApi('system.config.update',$_args);
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('IntegralConfig/edit'), $result['data']);
	}

}
