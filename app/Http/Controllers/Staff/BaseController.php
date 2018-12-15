<?php namespace YiZan\Http\Controllers\Staff;

use YiZan\Http\Controllers\YiZanViewController;
use View, Session, Input;

abstract class BaseController extends YiZanViewController {
	/**
	 * API调用类型
	 * @var string
	 */
	protected $apiType = 'staff';

	/**
	 * 调用模板
	 * @var string
	 */
	protected $tpl = 'staff.default';

	/**
	 * 员工信息
	 * @var array
	 */
	protected $staff;

	/**
	 * 员工编号
	 * @var int
	 */
	protected $staffId = 0;

    /**
     * 员工权限
     * @var int
     */
    protected $role = 0;
    /**
     * 店铺类型
     * @var int
     */
    protected $storeType = -1;

    protected $getToken = '';


	/**
	 * 初始化信息
	 */
	public function __construct() {
		parent::__construct();
		//设置员工
		$this->setStaff(Session::get('staff'));
        if (Input::get('token') != '') {
            $this->getToken = Input::get('token');
        }
        View::share('role', $this->role);
        if(Input::ajax()){
            View::share('ajax',true);
            View::share('js',"page_js");
        }else{
            View::share('ajax',false);
            View::share('js',"bnt_js");
        }
        View::share('id_action',strtolower(CONTROLLER_NAME).'_'.ACTION_NAME.'_view');
	}

	/**
	 * 设置员工
	 * @param array $staff 员工信息
	 */
	protected function setStaff($staff) {
		if (!empty($staff)) {
			$this->storeType  = $staff['storeType'];
			$this->staff 	  = $staff;
			$this->staffId 	  = $staff['id'];
            $this->role       = $staff['role'];
		}
        Session::set('staff', $staff);
		Session::save();
		
	} 

	/**
	 * 调用API
	 * @param  string 	$method 接口名称
	 * @param  array  	$args   参数
	 * @param  array  	$data   提交数据
	 * @return array          	API返回数据
	 */
	protected function requestApi($method, $args = [], $data = []){
		$data['userId'] = $this->staffId;
		return parent::requestApi($method, $args, $data);
	}
}
