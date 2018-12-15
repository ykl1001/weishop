<?php
namespace YiZan\Http\Controllers\Staff;

use Redirect,View,Input,Response;
/**
 * 首页
 */
class IndexController extends AuthController {
    public function __construct() {
        parent::__construct();
        View::share('active',"index");
        View::share('show_top_preloader',true);
    }
    /**
     * 新订单
     */
    public function index() {
        $args = Input::all();
        $args['page'] = $args['page']?$args['page']:1;
        $args['status'] = 2;
        $list = $this->requestApi('order.lists',$args);

        //cz
        if($args['staffUserId'] == $this->staffId){
            if(!empty($args['id'])){
                $result = $this->requestApi('order.detailnewstaffid',$args);
                if($result['code'] == 0 && !empty($result['data'])){
                    $result['data']['isChange'] = $args['isChange'];
                    $fu_count = -count($list['data']['orders'])-1;
                    $list['data']['orders'] = array_pad($list['data']['orders'],$fu_count,$result['data']);
                }
            }
        }
        if($list['code'] == 0)
            View::share('list', $list['data']);
        if($args['tpl']){
            if($this->storeType != 1){
                return $this->display($args['tpl']);
            }else{
                return $this->display("store_".$args['tpl']);
            }
        }

        View::share('order', $args);
        unset ($args['page']);
        View::share('args', $args);
        View::share('title','新订单');
        if( count($list['data']['orders']) == 20){
            View::share('show_preloader',true);
        }
        View::share('nav_back_url', u('Index/index'));
        if($this->storeType != 1){
            return $this->display();
        }else{
            return $this->display("store_index");
        }
    }

    /**
     * 订单详情
     */
    public function detail() {
        $args = Input::all();
        $result = $this->requestApi('order.detail',$args);
        View::share('data',$result['data']);
        View::share('title','订单详情');
        View::share('tpl','Index');
        View::share('args', $args);
        View::share('nav_back_url', u('Index/index'));
        View::share('url_css',"#index_index_view");
        //获取系统名 获取配送费
        $config = $this->getConfig();
        $system_send_staff_fee = $config['system_send_staff_fee'];
        $site_name = $config['site_name'];
        View::share('system_send_staff_fee', $system_send_staff_fee);
        View::share('site_name',$site_name);

        if($this->storeType != 1){
            if($args['tpl']){
                return $this->display('detail_'.$args['tpl']);
            }
            return $this->display();
        }else{
            if($args['tpl']){
                return $this->display('store_detail_'.$args['tpl']);
            }
            return $this->display("store_detail");
        }
    }

    public function repair(){
        $args = Input::all();
        $args['page'] = $args['page']?$args['page']:1;
        $args['status'] = $args['status'] ? $args['status'] : 0;
        $args['new'] = 1;
        $list = $this->requestApi('repair.lists',$args);

        if($list['code'] == 0) {
            View::share('list', $list['data']);
        }

        View::share('args', $args);

        if($args['tpl']){
            return $this->display($args['tpl']."repair");
        }
        return $this->display();
    }

    public function repairList(){
        $args = Input::all();
        $args['page'] = $args['page'] ? $args['page'] : 1;
        $args['status'] = $args['status'] ? $args['status'] : 0;
        $args['new'] = 1;
        $list = $this->requestApi('repair.lists',$args);

        if($list['code'] == 0) {
            View::share('list', $list['data']);
        }

        return $this->display('itemrepair');
    }
}