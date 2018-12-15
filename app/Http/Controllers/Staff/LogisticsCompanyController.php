<?php 
namespace YiZan\Http\Controllers\Staff;
use Input, View, Session, Redirect, Request,Time,Response,Lang;
/**
 * 用户订单控制器
 */
class LogisticsCompanyController extends AuthController {
    protected $_config = ''; //基础配置信息

    public function __construct() {
        parent::__construct();
        View::share('active',"order");
        View::share('show_top_preloader',true);
    }
	/**
	 * 订单列表页
	 */
	public function index() {
        View::share('title','物流公司');
        $args = Input::all();
        $couriercompany = Lang::get('couriercompany')['courier_company'];

        View::share('couriercompany', $couriercompany);
        View::share('args', $args);
        return $this->display();
	}

}