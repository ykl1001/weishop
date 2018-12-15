<?php 
namespace YiZan\Http\Controllers\Admin;
use View, Input, Lang, Time, Response;

/**
 * 商家提现
 */
class SharechapmanController extends AuthController {
	public function index() {
	    $args = Input::all();
        $nav = (int)$args['nav'] > 0 ? (int)$args['nav'] : 1;
        $args['beginTime'] = !empty($args['beginTime']) ? Time::toTime($args['beginTime']) : 0;
        $args['endTime'] = !empty($args['endTime']) ? Time::toTime($args['endTime']) : 0;
		$args['pageSize'] = 10;
		$result = $this->requestApi('SharechapmanLog.lists',$args);

		if( $result['code'] == 0 ) {
			View::share('list', $result['data']['list']);
		}
        View::share('nav', $nav);
		return $this->display();
	}

	public function dispose() {
        $args = Input::all();
	    $result = $this->requestApi('SharechapmanLog.dispose', $args);

        /*返回处理*/
		if($result['code']==0){
			return $this->success($result['msg']);
		}else{
			return $this->error($result['msg']);
		}
	}

}
