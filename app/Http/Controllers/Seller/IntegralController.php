<?php 
namespace YiZan\Http\Controllers\Seller;
 
use Input, View,Time,Response,Lang;

/**
 * 积分商城
 */
class IntegralController extends AuthController {
	
	/**
	 * 积分商城列表
	 */
	public function index() {
        $args = Input::all();
        $args['status'] = 1;
        $args['pageSize'] = 20;
        $result = $this->requestApi('integral.lists', $args);
		View::share('list', $result['data']['list']);
		View::share('args', $args);
		return $this->display();
	}

    /**
     * 积分商城列表
     */
    public function create() {
        $args = Input::all();
        $result = $this->requestApi('integral.lists', $args);
        if ($result['code'] == 0)
            View::share('list', $result['data']['list']);
        View::share('args', $args);
        View::share('totalCount', $result['data']['totalCount']);

        $count = ceil( $result['data']['totalCount'] / 10);

        View::share('count', $count);

        View::share('ajax', false);
        View::share('page', 1);

        return $this->display();
    }

    /**
     * 积分商城列表
     */
    public function edit() {
        $args = Input::all();
        $result = $this->requestApi('integral.getIntegral', $args);
        if ($result['code'] == 0)
            View::share('data', $result['data']);
        return $this->display('create');
    }

    /**
     * 积分商城列表
     */
    public function item() {
        $args = Input::all();
        $result = $this->requestApi('integral.lists', $args);
        if ($result['code'] == 0)
            View::share('list', $result['data']['list']);
        View::share('ajax', true);
        View::share('page', $args['page']);
        return $this->display("item");
    }



    /**
     * save积分商城列表
     */
    public function save() {
        $args = Input::all();
        $result = $this->requestApi('integral.save', $args);

        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }
        return $this->success($result['msg'], u('Integral/index'), $result['data']);
    }


    /**
     * save积分商城列表
     */
    public function saveIntegral() {
        $args = Input::all();
        $result = $this->requestApi('integral.saveIntegral', $args);
        return Response::json($result);
    }

    /**
     * [destroy 删除]
     */
    public function destroy(){
        $args['id'] = (int)Input::get('id');
        if( $args['id'] > 0 ) {
            $result = $this->requestApi('integral.delete',$args);
        }
        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98005'), u('Integral/index'), $result['data']);
    }
}
