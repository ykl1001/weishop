<?php namespace YiZan\Http\Controllers\Wap;

use YiZan\Utils\ImgVerify;
use Input, View, Session, Redirect, Request, Time, Response, Cache,Lang;
/**
 * 用户订单控制器
 */
class LogisticsController extends UserAuthController {
	protected $_config = ''; //基础配置信息

	public function __construct() {
		parent::__construct();
		View::share('nav','mine');
		$this->_config = Session::get('site_config');
		$sellerServiceTel = \YiZan\Services\SystemConfigService::getConfigByCode('seller_service_tel');
		View::share('sellerServiceTel',$sellerServiceTel);
		View::share('config',$this->_config);
	}
	/**
	 * 订单列表页
	 */
	public function index() {
    	return $this->indexList('index');
	}

	public function indexList($tpl = 'item') {
		$args = Input::all();
		$args['status'] = 5;
		$list = $this->requestApi('order.lists',$args);
		View::share('args', $args);
		View::share('nav_back_url', u('UserCenter/index'));

		if($list['code'] == 0)
			View::share('list', $list['data']);

    	return $this->display($tpl);
	}

	/**
	 * 订单详情
	 */
	public function detail() {
		$id = (int)Input::get('id');
		$result = $this->requestApi('order.detail',array('id' => $id));

			View::share('data', $result['data']);
		$payments = $this->getPayments();
		View::share('payments', $payments);

        //活动名称
        $activity_result = $this->requestApi('Activity.getshare',['orderId'=>$id]);
        View::share('activity', $activity_result['data']);

//        print_r($activity_result['data']);
        if(!empty($activity_result['data'])){
            $brief_count = count($result['data']['brief']);
            $desc = $result['data']['brief'][rand(0,$brief_count-1)];
            View::share('desc',$desc);

            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $url = $protocol.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
            $weixin_arrs = $this->requestApi('Useractive.getweixin',array('url' => $url));

            if($weixin_arrs['code'] == 0){
                View::share('weixin',$weixin_arrs['data']);
            }
            $link_url = u('UserCenter/obtaincoupon',array('orderId'=>$id,'activityId'=>$activity_result['data']['id']));
            View::share('link_url',$link_url);
        }

        $tid = (int)Input::get("tid");
        $pid = (int)Input::get("pid");

        if($tid != 0 || $pid != 0 ){
            if($pid !=  0){
                $sid = $pid;
            }
            if($tid !=  0){
                $sid = $tid;
            }
            View::share('nav_back_url', u('UserCenter/msgshow',['sellerId'=>$sid]));
        }else{
            View::share('nav_back_url', u('Order/index'));
        }

		return $this->display();
	}


    /**
 * 退款详情
 */
    public function refundview() {
        $orderId = (int)Input::get('orderId');
        $result = $this->requestApi('logistics.refundById',['orderId' => $orderId]);
        View::share('data', $result['data']);

        $result = $this->requestApi('order.refundview',['orderId' => $orderId]);
        View::share('refund', $result['data']);
        return $this->display();
    }

    /**
     * 退款详情
     */
    public function flow() {
        $couriercompany = Lang::get('couriercompany')['courier_company'];
        View::share('couriercompany', $couriercompany);

        $args = Input::all();
        View::share('args', $args);
        return $this->display();
    }

    /**
     * 选择的物流
     */
    public function logistics() {
        $orderId = (int)Input::get('orderId');
        $result = $this->requestApi('logistics.refundById',['orderId' => $orderId]);
        View::share('data', $result['data']);

        $args = Input::all();
        View::share('args', $args);
        return $this->display();
    }


    /**
     * 申请退款
     */
    public function refund() {
        $args = Input::all();

        $result = $this->requestApi('order.detail',['id' => $args['id']]);
        View::share('data', $result['data']);
        View::share('args', $args);
        return $this->display();
    }
    /**
     * 服务选择
     */
    public function ckservice() {

        $args = Input::all();
        View::share('args', $args);
        return $this->display('service');
    }

    /**
     * 服务选择
     */
    public function refundSave() {

        $args = Input::all();
        $result = $this->requestApi('logistics.refund',$args);
        return Response::json($result);
    }

    /**
     * 服务选择
     */
    public function refundDel() {

        $args = Input::all();
        $result = $this->requestApi('logistics.refundDel',$args);
        return Response::json($result);
    }
    /**
     * 选择的物流
     */
    public function logisticssave() {
        $args = Input::all();

        $result = $this->requestApi('Logistics.refundById',$args);
        $post['keycode'] = Lang::get('couriercompany')['courier_company'][$args['name']];
        $post['form'] = $result['data']['sellerAddress'];
        $post['to'] = $result['data']['order']['province'].$result['data']['order']['city'].$result['data']['order']['area'].$result['data']['order']['address'];
        $post['number'] = $args['no'];
        $post['orderId'] = $args['orderId'];
        $post['userId'] = $result['data']['userId'];
        $post['sellerId'] = $result['data']['sellerId'];
        $post['company'] = $args['name'];


        $option['orderId'] = $result['data']['order']['id'];
        $option['id']   =$result['data']['id'];

        $result = $this->requestApi('ordertrack.postlogistics',$post);
        $return = json_decode($result['data'],true);
        if($return['message'] == 'success'){ //订单改成102状态
            $option['status'] = 3;
            $option['keycode'] =  $post['keycode'] ;
            $option['number'] =  $post['number'] ;
            $option['company'] =  $post['company'] ;
            $option['images'] =  $args['images'];
            $result = $this->requestApi('Logistics.userrefunddispose',$option);

            return Response::json($result);
        }else{
            return Response::json($return);
        }
    }

}