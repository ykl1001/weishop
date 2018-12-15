<?php namespace YiZan\Http\Controllers\Wap;

use YiZan\Utils\Time;
use View, Input, Lang, Route, Page ,Session,Log;
/**
 * 积分商城
 */
class IntegralController extends BaseController {

	//
	public function __construct() {
		parent::__construct();
        View::share('nav','mine');
	}

	public function index() {
        $args = Input::all();
        $args['status'] = 1;
        $args['pageSize'] = 20;
        $args['page'] =  $args['page'] ?  $args['page']:  1 ;
		$list = $this->requestApi('Integral.lists',$args);
        View::share('list', $list['data']['list']);
        if($args['tpl']){
            return $this->display($args['tpl']);
        }
        $integral = $this->requestApi('user.integral');
        View::share('integral', $integral['data']['integral']);

        $data = $this->requestApi('config.integral');
        View::share('data', $data['data']);
        return $this->display();
	}

    /**
     * 兑换记录
     */
    public function userlog() {
        $args = Input::all();
        $args['page'] =  $args['page'] ?  $args['page']:  1 ;
        $integral = $this->requestApi('integral.userlog',$args);
        View::share('list', $integral['data']['list']);
        if($args['tpl']){
            return $this->display($args['tpl']);
        }
        return $this->display();
    }

    /**
     * 兑换记录
     */
    public function rules() {
        $integral = $this->requestApi('user.integral');
        View::share('list', $integral['data']['list']);
        return $this->display();
    }
    /**
     * 兑换详情
     */
    public function get() {
        $args = Input::all();
        $integral = $this->requestApi('integral.get',$args);
        View::share('data', $integral['data']);
        return $this->display();
    }
    /**
     * 商品详情
     */
    public function detail() {
        $args = Input::all();
        $integral = $this->requestApi('integral.detail',$args);
        View::share('data', $integral['data']);

        $integral = $this->requestApi('user.integral');
        View::share('integral', $integral['data']['integral']);

        $userId = $this->userId;
        View::share('userId', $userId);

        return $this->display();
    }

}
