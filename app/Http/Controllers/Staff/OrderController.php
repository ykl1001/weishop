<?php 
namespace YiZan\Http\Controllers\Staff;
use Input, View, Session, Redirect, Request,Time,Response,Lang;
/**
 * 用户订单控制器
 */
class OrderController extends AuthController {
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
        $args = Input::all();
        $args['page'] = $args['page']?$args['page']:1;
		View::share('status', $args['status']);
        $result = $this->requestApi('order.lists',$args);
        View::share('list', $result['data']);
        if($args['tpl']){
            if($this->storeType != 1){
                return $this->display($args['tpl']);
            }else{
                return $this->display("store_".$args['tpl']);
            }
        }
        unset ($args['page']);
        unset ($args['tpl']);
        View::share('args', $args);
        View::share('title','订单管理');
        View::share('order', $args);
        if( count($result['data']['orders']) == 20){
            View::share('show_preloader',true);
        }
        if($args['status']){
            View::share('ajaxurl_page',"_".$args['status']);
        }
        if($this->storeType != 1){
            return $this->display();
        }else{
            return $this->display("store_index");
        }
	}

    /**
     * 订单列表页
     */
    public function indexitme() {
        $args = Input::all();
        $args['status'] = $args['status']?$args['status']-1:1;
        $args['page'] = $args['page']?$args['page']:1;
        $result = $this->requestApi('order.lists',$args);
        View::share('title','订单管理');
        return Response::json($result['data']['orders']);
    }



    /**
     * 接单
     */
    public function orderReceiving() {
        $args = Input::All();
        $result = $this->requestApi('order.status',$args);
        return Response::json($result);
    }


    /**
     * 完成订单
     */
    public function complete() {
        $args = Input::All();
        $result = $this->requestApi('order.complete',$args);
        echo json_encode($result);die;
    }

	/**
	 * 订单详情
	 */
	public function detail() {
		$result = $this->requestApi('order.detail',Input::all());
        if($result['data']){
		    View::share('data',$result['data']);
        }else{
            return $this->error("订单获取失败");
        }
        View::share('title','订单详情');
        View::share('tpl','Order');
        $args = Input::all();
        View::share('args', $args);
        if($args['url_css']){
            View::share('nav_back_url', u('Seller/evaluation'));
            View::share('url_css', $args['url_css']);
        }else{
            View::share('nav_back_url', u('Order/index'));
            View::share('url_css',"#order_index_view_1");
        }
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

    /**
     * 订单详情
     */
    public function staff() {

		$result = $this->requestApi('order.stafflist',['type'=>Input::get('type')]);
        View::share('list',$result['data']);
        View::share('order', Input::all());
        View::share('title','更换人员');
        $tpl = Input::get("tpl");
        View::share('return_url',u($tpl.'/detail',['id'=>Input::get('id')]));
        return $this->display();
    }
    /**
     * 订单详情
     */
    public function savestaff() {

        $result = $this->requestApi('order.designate',Input::all());
        return Response::json($result);
    }
    /**
     * 退款详情
     */
    public function refundview() {
        $orderId = (int)Input::get('id');
        $result = $this->requestApi('logistics.refundById',['id' => $orderId]);
        View::share('data', $result['data']);
        View::share('title','退款详情');

        View::share('nav_back_url',u('Order/detail',['id'=>Input::get('id')]));
        View::share('url_css','order_detail_view');
        if( $result['data']['status'] == 5){
            $result = $this->requestApi('order.refundDetail',['userId' => $result['data']['userId'],'orderId' => $orderId]);
            View::share('userRefund', $result['data']);
        }
        return $this->display();
    }
    /**
     * 退款详情
     */
    public function refunddispose() {
        View::share('title','拒绝退款申请');
        View::share('nav_back_url',u('Order/refundview',['id'=>Input::get('id')]));
        View::share('url_css','order_detail_view');

        $result = $this->requestApi('logistics.refundById',['id'=>Input::get('id')]);
        View::share('data', $result['data']);

        return $this->display();
    }
    /**
     * 拒绝退款
     */
    public function refundSave() {
        $args = Input::all();
        $result = $this->requestApi('logistics.refunddispose',$args);
        return Response::json($result);
    }



    /**
     * 发货
     */
    public function deliver() {
        $result = $this->requestApi('order.detail',Input::all());
        if($result['data']){
            View::share('data',$result['data']);
        }else{
            return $this->error("订单获取失败");
        }
        View::share('title','确认发货');
        View::share('tpl','Order');
        $args = Input::all();
        View::share('args', $args);
        View::share('nav_back_url', u('Order/index'));
        return $this->display();
    }

    /**
     * 查看物流信息
     */
    public function logistics() {
        $args = Input::all();
        $result = $this->requestApi('ordertrack.get',$args);
//        print_r($result);exit;
        View::share('orderinfo', $result['data']);

        View::share('nav_back_url', u('Order/index'));
        View::share('title','物流详情');
        return $this->display();
    }

    /**
     * 订阅物流
     */
    public function postlogistics(){
        $args = Input::all();

        $result = $this->requestApi('order.detail',['id'=>$args['orderId']]);

        if($result['data']){
            View::share('data',$result['data']);
        }else{
            return $this->error("订单获取失败");
        }
        $keycode = Lang::get('couriercompany')['courier_company'][$args['keycode']];
        $key = $this->getConfig('order_track_key');

        $post['keycode'] = $keycode;
        $post['form'] = '';
        $post['to'] = $result['data']['province'].$result['data']['city'].$result['data']['area'];
        $post['number'] = $args['number'];
        $post['orderId'] = $args['orderId'];
        $post['userId'] = $result['data']['userId'];
        $post['sellerId'] = $result['data']['sellerId'];
        if($args['type'] == 1 || $args['type'] == 2){
            $post['company'] = $args['company'];
        }else{
            $post['company'] = $args['keycode'];
        }
        $post['type'] = $args['type'];
        $post['remark'] = $args['remark'];

        $result = $this->requestApi('ordertrack.postlogistics',$post);
        $return = json_decode($result['data'],true);

        if($return['message'] == 'success' || $args['type'] == 1 || $args['type'] == 2){ //订单改成102状态
            $option['id'] = $args['orderId'];
            $option['status'] = 2;
            $result = $this->requestApi('order.status',$option);

            return Response::json($result);
        }else{
            return Response::json($return);
        }

    }

    /**
     * 订阅物流
     */
    public function refund(){
        $result = $this->requestApi('logistics.refundsave',Input::all());
        return Response::json($result);

    }

}