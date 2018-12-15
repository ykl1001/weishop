<?php 
namespace YiZan\Http\Controllers\Admin;

use View, Input, Lang, Route, Page, Response, Config;

/**
 * 方维分销平台
 */
class FxExchangeController extends AuthController {
    /**
     * [index description]
     * @return [type] [description]
     */
    public function index() {
    	$result = $this->requestApi('system.config.get', ['groupCode'=>'fanwefx']);
    	if($result['code'] == 0)
        {
            foreach ($result['data'] as $key => $value) {
                $data[$value['code']] = $value['val'];
            }
            View::share('data', $data);
        }

        
        return $this->display();
    }

    public function save() {
    	$args = Input::all();
    	if(!is_numeric($args['fx_exchange_percent']))
    	{
    		$result = array (
	            'status'    => false,
	            'data'      => Input::input('val'),
	            'msg'       => Lang::get('api_system.code.87006')
	        );
	    	return Response::json($result);
    	}

    	foreach ($args as $key => $value) {
    		$data[$key]['code'] = $key;
    		$data[$key]['val'] = $value;
    	}
    	$result = $this->requestApi('system.config.update', ['configs'=>$data]);
    	$result = array (
            'status'    => true,
            'data'      => Input::input('val'),
            'msg'       => Lang::get('api_system.success.update_info')
        );
    	return Response::json($result);
    }
	
}
