<?php namespace YiZan\Http\Controllers\Wap;

use YiZan\Utils\ImgVerify;
use Input, View, Session, Redirect, Request, Time, Response, Cache;
/**
 * 用户订单控制器
 */
class OrderController extends UserAuthController {
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
		$args['status'] = (int)Input::get('status')?(int)Input::get('status'):0;
		$list = $this->requestApi('order.lists',$args);
		View::share('args', $args);
		View::share('nav_back_url', u('Index/index'));

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

		//全国店，周边店提示替换
		if($result['data']['isAll'] == 1)
		{
			$system_order_pass_all = $this->getConfig('system_order_pass_all');
        	$system_order_pass_all += Time::toTime($result['data']['createTime']);
			$time = Time::toDate($system_order_pass_all, 'm月d日 H:i');
		}
		else
		{
			$system_order_pass = $this->getConfig('system_order_pass');
			$time = $system_order_pass / 60;
		}
			
		$result['data']['orderNewStatusStr']['tag'] = str_replace("<TIME>", $time, $result['data']['orderNewStatusStr']['tag']);

        View::share('data', $result['data']);
		
		$payments = $this->getPayments();
		View::share('payments', $payments);

        //活动名称
        $activity_result = $this->requestApi('Activity.getshare',['orderId'=>$id]);
        if($activity_result['data']){
            $getWeixinUser = $this->requestApi('Useractive.getWeixinUser',['openid'=>$this->user['openid']]);
            $newtitle = $getWeixinUser['data']['nickname'].":".$activity_result['data']['title'];
            $activity_result['data']['title'] = $newtitle;
			View::share("weiXinData",  $getWeixinUser['data']);

			$weiXinUserData = Session::get("user");
			View::share('weiXinUserData',$weiXinUserData);
        }
        View::share('activity', $activity_result['data']);
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
            $link_url = u('UserCenter/obtaincoupon',array('orderId'=>$id,'activityId'=>$activity_result['data']['id'],'type'=>'user','id'=>$this->userId));
            View::share('link_url',$link_url);
        }

        $tid = (int)Input::get("tid");
        $pid = (int)Input::get("pid");
        $udbType = (int)Input::get("udbType");
        if($udbType) {
            View::share('nav_back_url', u('UserCenter/orderchange'));
        }else{
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
        }

		return $this->display();
	}


    /**
     * 二维码
     */
    public function cancode(){
        $args = Input::all();
        $val = $args['val'];

        $errorCorrectionLevel = 'L';//容错级别
        $matrixPointSize = 14;//生成图片大小
        $backColor = 0xFFFFFF; //背景色
        $foreColor = 0x000000; //前景色
        $logo = asset('images/fenx.jpg');
        $margin = 1; //边距
        $QR = '';
        include base_path().'/vendor/code/Code.class.php';
        $QRcode = new \QRcode();
        //生成二维码图片
        $QRcode->png($val, false, $errorCorrectionLevel, $matrixPointSize, $margin,$saveandprint=false,$backColor,$foreColor);
        $QR = imagecreatefromstring(file_get_contents($QR));
        if ($logo !== FALSE) {
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);//二维码图片宽度
            $QR_height = imagesy($QR);//二维码图片高度
            $logo_width = imagesx($logo);//logo图片宽度
            $logo_height = imagesy($logo);//logo图片高度
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width/$logo_qr_width;
            $logo_qr_height = $logo_height/$scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            //重新组合图片并调整大小
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,$logo_qr_height, $logo_width, $logo_height);
        }
        echo  $QR;
    }


	/**
	 * [wxpay 微信支付]
	 */
	public function wxpay(){
       	$args = Input::all();
        //余额支付,检测支付密码
        if ($args['balancePay'] == 1) {
            $checkPayPwd = $this->requestApi('user.checkpaypwd', ['password' => $args['key']]);
            if ($checkPayPwd['code'] != 0) {
                return Redirect::to(u('Order/cashierdesk', ['id'=>$args['id']]));
            }
        }
        $url = u('Order/pay',$args);
         $openid = Session::get('wxpay_open_id');
         if(empty($openid)){
            $url = u('Weixin/authorize', ['url' => urlencode($url)]);
         }else{
            $url .= '&openId='.$openid;
         }
		//$url = 'http://www.niusns.com/callback.php?m=Weixin&a=publicauth2&url='.urlencode($url).'&cookie='.urlencode($_COOKIE['laravel_session']);
        return Redirect::to($url);
    }

	/**
	 * [pwxpay 物业微信支付]
	 */
	public function pwxpay(){
       	$args = Input::all(); 
       	$args['isWeixinPay'] = 1;
        $url = u('Order/createPropertyPay',$args);
         $openid = Session::get('wxpay_open_id');
         if(empty($openid)){
             $url = u('Weixin/authorize', ['url' => urlencode($url)]);
         }else{
             $url .= '&openId='.$openid;
         }

		//$url = 'http://www.niusns.com/callback.php?m=Weixin&a=publicauth2&url='.urlencode($url).'&cookie='.urlencode($_COOKIE['laravel_session']);

        return Redirect::to($url);
    }

     public function createpaylog()
     {
		$args = Input::all();
        if ($args['balancePay'] == 1) {
            $checkPayPwd = $this->requestApi('user.checkpaypwd', ['password' => $args['key']]);
            if ($checkPayPwd['code'] != 0) {
                return Response::json($checkPayPwd);
            }
        }
		
		if (!empty($args['balancePay'])){
			$args['extend']['balancePay'] = (int)$args['balancePay'];
		}
		$pay = $this->requestApi('order.pay', $args);
		$pay['data']['orderId'] = $pay['data']['order']['id'];
		unset($pay['data']['order']);
        die(json_encode($pay["data"]));
     }

    public function unionPay(){
        $args = Input::all();
        if ($args['balancePay'] == 1) {
            $checkPayPwd = $this->requestApi('user.checkpaypwd', ['password' => $args['key']]);
            return Response::json($checkPayPwd);
        }
    }

	/**
	 * 订单支付
	 */
	public function pay() {
		$args = Input::all();
        //余额支付,检测支付密码
        if ($args['balancePay'] == 1) {
            $checkPayPwd = $this->requestApi('user.checkpaypwd', ['password' => $args['key']]);
            if ($checkPayPwd['code'] != 0) {
                return Redirect::to(u('Order/cashierdesk', ['id'=>$args['id']]));
            }
        }
		if (isset($args['payment']) && $args['payment'] == 'weixinJs') {
			Session::put('wxpay_open_id', $args['openId']);
			Session::put('pay_payment', 'weixinJs');
			Session::save();
			return Redirect::to(u('Order/pay',['id' => $args['id']]));
		}
		/*货到付款*/
		if (isset($args['payment']) && $args['payment'] == 'cashOnDelivery') {
		    return Redirect::to(u('Order/delivery',['id' => $args['id']]));
		}
		if (!isset($args['payment'])) {
			$args['payment'] = Session::get('pay_payment');
			$args['openId'] = Session::get('wxpay_open_id');
		}
		$args['extend']['url'] = Request::fullUrl();
		//$args['extend']['url'] = "http://www.niusns.com/payment/o2o.php";

		if (!empty($args['openId'])) {
			$args['extend']['openId'] = $args['openId'];
		}
		if (!empty($args['balancePay'])){
			$args['extend']['balancePay'] = (int)$args['balancePay'];
		}
		$pay = $this->requestApi('order.pay', $args);

		if($pay['code'] == 0){
			if (isset($pay['data']['payRequest']['html'])) {
				echo $pay['data']['payRequest']['html'];
				exit;
			}
			View::share('pay',$pay['data']['payRequest']);
		}

		$result = $this->requestApi('order.detail',array('id' => $args['id']));
		if($result['code'] == 0){
			View::share('data',$result['data']);
			if($result['data']['payStatus']){
				return Redirect::to(u('Order/detail', ['id'=>$result['data']['id']]));
			}
		}
		View::share('payment',$args['payment']);
		return $this->display('wxpay');
	}


	/**
	 * 外卖下单
	 */
	public function detection() {
	    $orderdata = Session::get("disps");
	    $shop = $this->requestApi("Shopping.getCart");
	    if($shop['data']){
	        $args = [
	            'mobileId' => $orderdata['mobileId'],
	            'addressId' => $orderdata['addressId'],
	            'type' => $orderdata['type'],
	            'remark' => $orderdata['remark'],
	            'goods' => $shop['data']
	        ];
	        $result = $this->requestApi('restaurant.order.create',$args);
	        View::share('data',$result['data']);
	        if($result['code'] != 0){
	            return $this->error($result['msg']);
	        }else{

	            $this->requestApi("Shopping.delete");
	            Session::put("id",$result['data']['id']);
	            Session::put("disps",null);
	            Session::save();
	        }
	    }else{
	        $orderId_ok = Session::get("id");
	        if($orderId_ok){
	            Session::put("id",null);
	            Session::save();
	           return $this->error("不要重复刷新订单",u('Order/detail',array('id'=>$orderId_ok)));
	        }else{
	           return $this->error("你还没有挑选菜品");
	        }
	    }
	    $payments = $this->getPayments();
	    View::share('payments', $payments);
	    View::share('orderdata', $orderdata);
	    return $this->display();
	}
	/**
	 * 其他ALL下单
	 */
	public function detections() {
	    $orderdata = Session::get("orderData");
	    $id = (int)input::get('id');
	    $args = [
	        'mobileId' => $orderdata['mobileId'],
	        'addressId' => $orderdata['addressId'],
	        'id' => $id,
	    ];
	    $result = $this->requestApi('service.order.create',$args);
	    return Response::json($result);
	}

	/**
	 * 去支付
	 */
	public function toPay() {
	    $id = input::get("orderId");
	    $result = $this->requestApi('order.detail',array('id' =>$id));
	    if($result['code'] == 0){
	       $orderdata['total_price'] = $result['data']['payFee'];
	        View::share('orderdata', $orderdata);
	        View::share('data',$result['data']);
	    }
	    $payments = $this->getPayments();
	    View::share('payments', $payments);
	    return $this->display("detection");
	}
	/**
     * delivery 餐到付款
     */
	public function delivery() {
	    $id = (int)Input::get('id');
	    $result = $this->requestApi('order.delivery',array('orderId' => $id));
		return Redirect::to(u('Order/detail',['id' => $id]));
	}
	/**
	 * [cancelorder 取消订单]
	 */
	public function cancelorder() {
		$args = Input::all();
		$result = $this->requestApi('order.cancel',$args  );
		return Response::json($result);
	}
	/**
	 * [cancelPropertyOrder 取消物业订单]
	 */
	public function cancelPropertyOrder() {
		$args = Input::all();
		$result = $this->requestApi('propertyorder.cancel',$args  );
		if($result['code'] > 0){
			return $this->error($result['msg']);
		} else { 
			return Redirect::to(u('Property/index')); 
		}
	}

	/**
	 * [delorder 删除订单]
	 */
	public function delorder() {
		$id = (int)Input::get('id');
		$result = $this->requestApi('order.delete',array('id' => $id));
		return Response::json($result);
	}

	/**
	 * [confirmorder 订单完成]
	 */
	public function confirmorder() {
		$id = (int)Input::get('id');
		$result = $this->requestApi('order.confirm',array('id' => $id));
		return Response::json($result);
	}

	/**
	 * [commentlist 订单评论页面]
	 */
	public function commentlist(){
		$id = (int)Input::get('oid');
		$result = $this->requestApi('order.detail',array('id' => $id));
		$system_order_pass = $this->getConfig('system_order_pass');
		View::share('system_order_pass', $system_order_pass);
		if ($result['code'] > 0) {
			return $this->error($result['msg']);
		}
		$order = $result['data'];
		if ($order['payEndTime'] > UTC_TIME) {
			View::share('pay_end_str', Time::getEndTimelag($order['payEndTime']));
		}
		View::share('data', $order);
		return $this->display();
	}

	/**
	 * [docomment 评论订单]
	 */
	public function docommentlist(){
		$data = Input::all();
		$result = $this->requestApi("rate.order.create",$data);
		return Response::json($result);
	}

	/**
	 * [comment 订单评论页面]
	 */
	public function comment(){
        $orderId = (int)Input::get('orderId');
        $tid = (int)Input::get('tid');
        if ($orderId < 1) {
            return Redirect::to(u('Order/index'));
        }
        $order = $this->requestApi('order.detail',array('id' => $orderId));
        if (!$order['data']['isCanRate']) {
            return Redirect::to(u('Order/detail',['id' => $orderId,'tid'=>$tid]));
        }
        View::share('order', $order['data']);

        if($tid != 0 ){
            View::share('nav_back_url', u('Order/detail',['id' => $orderId,'tid'=>$tid]));
        }else{
            View::share('nav_back_url', u('Order/detail',['id' => $orderId]));
        }
        View::share('tid', $tid?$tid:0);

		return $this->display();
	}

	/**
	 * [docomment 评论订单]
	 */
	public function docomment(){
		$data = Input::all();
		if($data['isAll'] == 1)
		{
			$result = $this->requestApi("rate.order.createall",$data);	//全国店评价
		}
		else
		{
			$result = $this->requestApi("rate.order.create",$data);	//周边店评价
		}

		
		return Response::json($result);
	}

	/**
	 * [refund 申请退款]
	 */
	public function refund(){
		$id = (int)Input::get('id');
        if ($id < 1) {
            return $this->error('非法请求');
        }
        $result = $this->requestApi('order.detail',array('orderId' => $id));
        //var_dump($result['data']);
        if ($result['code'] == 0) {
        	View::share('data',$result['data']);
        }
		return $this->display();
	}

	/**
	 * [dorefund 申请退款]
	 */
	public function dorefund(){
		$data = Input::all();
		//var_dump($data);
		$result = $this->requestApi("order.refund",$data);
		return Response::json($result);
	}

	/*
	* 日程列表
	*/
	public function schedule(){
		$args = Input::all();
		$args['status'] = (int)Input::get('status');
		$list = $this->requestApi("order.schedule",$args);
		View::share('args', $args);
		if($list['code'] == 0)
			View::share('list', $list['data']);
		if (Input::ajax()) {
			return $this->display('item');
		} else {
			return $this->display('index');
		}
	}

	/**
	 * 保存来自下单选择服务人员的时间参数
	 */
	public function saveOrderData() {
		$args = Input::get("orderData");
		if(!$args['orderTime'])
			die(0);
		Session::put("orderData.staffId", $args['staffId']);
		Session::put("orderData.goodsId", $args['goodsId']);
		Session::put("orderData.staffName", $args['staffName']);
		Session::put("orderData.orderTime", \YiZan\Utils\Time::toDate($args['orderTime'], "Y-m-d H:i"));
		Session::save();
		echo 1;
	}

	/**
	 * 保存地址
	 */
	public function saveOrderDataAdd() {
		$address = Input::get("address");
		if(empty($address))
			die(0);
		Session::put("orderData.address",$address);
		Session::save();
		echo 1;
	}

    /**
     * 保存配送方式
     */
    public function saveOrderSendWay() {
        $sendway = Input::all();
        if(empty($sendway))
            die(0);
        Session::put("sendWay",$sendway);
        Session::save();
        echo 1;
    }

	/**
	 * 备注
	 */
	public function remark() {
        return $this->display();
	}

	/**
	 * 保存备注
	 */
	public function saveOrderDataRemark() {
		$disps['remark'] = Input::get("remark");
		Session::put("disps", $disps);
		Session::save();
		echo 1;
	}

	public function reimburse() {
       $id = (int)Input::get('id');
		$result = $this->requestApi('order.details',array('orderId' => $id));

		if ($result['code'] > 0) {
			return $this->error($result['msg']);
		}
		$order = $result['data'];

		if ($order['payEndTime'] > UTC_TIME) {
			View::share('pay_end_str', Time::getEndTimelag($order['payEndTime']));
		}

		View::share('data', $order);
        return $this->display();
	}
	/**
	 * 创建订单 - 开始
	 */
	public function order() {
        //去掉跳过去的session
        Session::set('cartIds');

        //微信登录的没有手机号码 要去绑定
        $userinfo = Session::get('user');

//        $userinfo['mobile'] = "";
        if(empty($userinfo['mobile'])){
//            View::share('return_url', u('order/order',['cartIds'=>Input::get('cartIds')]));
            View::share('return_url', u('GoodsCart/index'));
            return $this->display('bindmobile');
        }

        $userAddInfo = Session::get('userAddInfo');
        View::share('userAddInfo', $userAddInfo);



	    $ids = explode(',', Input::get('cartIds'));

		$result = $this->requestApi('shopping.getCartList',['ids'=>$ids]);
		if(empty($result['data']) || $result['code'] != 0){
		    return $this->error($result['msg'],u('GoodsCart/index'));
		}

		//验证是否存在库存为0的商品，重置订单页,不显示库存为0的商品
		$cartIds_stock = [];
		$zfee = 0;

        foreach ($result['data'] as $key => $value) {
            if($result['data']['type'] == 1){
                if( (!empty($value['stockGoods']) && $value['stockGoods']['stockCount'] > 0) || (empty($value['stockGoods']) && $value['goods']['stock'] > 0) )
                {
                    $cartIds_stock[] = $value['id'];
                    $zfee += $value['stockGoods']['price']*$value['num'];
                }
            }else{
                $cartIds_stock[] = $value['id'];
                $zfee += $value['price']*$value['num'];
            }
            if($value['shareUserId'] != 0){
                break;
            }

        }

		if(count($ids) != count($cartIds_stock))
		{
			$cartIdsStr = implode(',', $cartIds_stock);
			return Redirect::to(u('Order/order', ['cartIds' => $cartIdsStr]));
		}

        View::share('cartIds', implode(',', $ids));
		View::share('data', $result['data']);
		$curentAddress = Session::get('defaultAddress');  
        if ((int)Input::get('addressId') > 0) {
            $address = $this->requestApi('user.address.get',['id' => (int)Input::get('addressId')]);
        } elseif ((int)$curentAddress['addressId'] <= 0) {
            $address = $this->requestApi('user.address.get',['id' => (int)$curentAddress['addressId']]);
            if($address){
                $address['data'] = $curentAddress;
            }
        } else {
            $address = $this->requestApi('user.address.getdefault');
        }
        // if (empty($address['data'])) {
            // return $this->error('请先添加地址',u('GoodsCart/index'));
        // }
        // Session::put("defaultAddress",$address['data']);
		// Session::save();

        //全国店 且没有地址的时候
        if($result['data'][0]['seller']['storeType'] == 1 && $address['data']['id'] <= 0)
        {
        	//获取默认地址
        	$address = $this->requestApi('user.address.getdefault');
        }

        //cz优惠券 和 积分 获取最佳优惠券
        $proId = (int)Input::get('proId');
        $price = Input::get('price');
        if((int)Input::get('cancel') == 1){
            $promotion['data'] = "";
        }else{
            if($zfee > 0){
                $promotion = $this->requestApi('user.promotion.getbest',['price'=>$zfee,'storeType' => $result['data'][0]['seller']['storeType'],'sellerId' => $result['data'][0]['seller']['id']]);
            }
            if ($proId > 0) {
                $promotion = $this->requestApi('user.promotion.get',['id'=>$proId]);
            }
        }


        View::share('promotion', $promotion['data']);

        $integralConfig = $this->requestApi('config.configByCode',['code'=>'integral_off']);
        if($integralConfig['data']){
            View::share('fee_off', true);
        }

        $fee= $this->requestApi('order.compute',['cartIds'=>$ids,'promotionSnId'=>$promotion['data']['id'],'addressId'=>$address['data']['id'],'cancel'=>Input::get('cancel'),'price'=>$zfee]);
        View::share('fee', $fee['data']);

        //全国店，周边店获取不同的订单参数
        if($result['data'][0]['seller']['storeType'] == 1)
        {
        	$system_order_pass_all = $this->getConfig('system_order_pass_all');
        	$system_order_pass_all += UTC_TIME;
        	View::share("time", Time::toDate($system_order_pass_all, 'm月d日 H:i'));
        }
        else
        {
        	$system_order_pass = $this->getConfig('system_order_pass');
        	View::share("time",$system_order_pass/60);
        }
       
		View::share('address', $address['data']);
		$seller = $result['data'][0]['seller'];

        //配送方式
    //    $sendWay = Session::get("sendWay");
//        print_r($sendWay);
//        exit;
     //   View::share('sendWay',$sendWay);
		
		//获取商家可选择的时间
		$sellerAllowTime = $this->sellerAllowTime($seller);
        View::share('sellerAllowTime', $sellerAllowTime);

        //cz
        $config = $this->getConfig();
        $wap_promotion = $config['wap_promotion'];
        $wap_integral = $config['wap_integral'];
        View::share('wap_promotion', $wap_promotion ? $wap_promotion : "优惠券");
        View::share('wap_integral', $wap_integral ? $wap_integral : "生活币");

        $integralOpenType = $this->requestApi('config.configByCode',['code'=>'integral_open_type']);
        View::share('integralOpenType', $integralOpenType['data']);
	    return $this->display();
	}

    public function integralorder(){
        $args = Input::get();
        $goods = $this->requestApi('Integral.detail',['id' => $args['goodsId']]);
        $userinfo = $this->requestApi('user.userinfo');
        if ($userinfo['data']['integral'] < $goods['data']['exchangeIntegral']) {
            return $this->error('您的积分不足',u('Integral/detail',['id' => $args['goodsId']]));
        }
        View::share('goods', $goods['data']);
        View::share('userinfo', $userinfo['data']);
        if ((int)Input::get('addressId') > 0) {
            $address = $this->requestApi('user.address.get',['id' => (int)Input::get('addressId')]);
        } else {
            $address = $this->requestApi('user.address.getdefault');
        }
        $system_order_pass = $this->getConfig('system_order_pass');
        view::share("time",$system_order_pass/60);
        View::share('address', $address['data']);

        //获取商家可选择的时间
        $sellerAllowTime = $this->sellerAllowTime($goods['data']['seller']);
        View::share('sellerAllowTime', $sellerAllowTime);

        return $this->display();
    }

    public function tointegralorder(){
        $args = Input::all();
        $result = $this->requestApi('order.integralorder',$args);
        return Response::json($result);
    }

	/**
	 * 商家时间选择
	 */
	// public function sellerAllowTime($seller) {
	// 	$weekarray = array("日","一","二","三","四","五","六");

	// 	//获取营业时间
	// 	$staffstime = $this->requestApi('staffstime.lists',['id'=>$seller['id']]);
	// 	if(empty($staffstime['data']))
	// 	{
	// 		return null;
	// 	}
		
	// 	//获取预约天数
	// 	$reserve_days = $seller['reserveDays'];
	// 	//获取配送周期 send_loop
	// 	$send_loop = $seller['sendLoop'];



	// 	//重排时间
	// 	$timeList = [];
	// 	foreach ($staffstime['data'] as $key => $value) {
	// 		$weeks = explode(' ', $value['weeks']);
	// 		foreach ($value['week'] as $k => $v) {

	// 			$hoursInt 	= explode(' ', $value['times']);
	// 			foreach ($hoursInt as $k2 => $v2) {

	// 				$timeList[$v]['hours'][$k2][] = explode('-', $v2);
	// 				foreach ($timeList[$v]['hours'][$k2] as $k3 => $v3) {
	// 					// 组装时间
	// 					$beginTime = Time::toTime(Time::toDate(UTC_TIME, 'Y-m-d').' '.$v3[0]); 
	// 					$endTime   = Time::toTime(Time::toDate(UTC_TIME, 'Y-m-d').' '.$v3[1]);

	// 					while ( $beginTime <= $endTime) {
	// 						$timeList[$v]['time'][] = Time::toDate($beginTime, 'H:i');
	// 						$beginTime += $send_loop * 60; //分钟转换成秒
	// 					}
	// 				}
	// 				unset($timeList[$v]['hours']);
	// 			}

	// 		}
	// 	}
		
	/**
	 * 商家时间选择
	 */
	public function sellerAllowTime($seller) {
		$weekarray = array("日","一","二","三","四","五","六");

		//获取营业时间
		$staffstime = $this->requestApi('staffstime.lists',['id'=>$seller['id']]);

		if(empty($staffstime['data']))
		{
			return null;
		}
		
		//获取预约天数
		$reserve_days = $seller['reserveDays'];
		//获取配送周期 send_loop
		$send_loop = $seller['sendLoop'];



		//重排时间
		$timeList = [];
		foreach ($staffstime['data'] as $key => $value) {
			$weeks = explode(' ', $value['weeks']);
			foreach ($value['week'] as $k => $v) {

				$hoursInt 	= explode(' ', $value['times']);
				foreach ($hoursInt as $k2 => $v2) {

					$timeList[$v]['hours'][$k2][] = explode('-', $v2);
					foreach ($timeList[$v]['hours'][$k2] as $k3 => $v3) {
						// 组装时间
						$beginTime = Time::toTime(Time::toDate(UTC_TIME, 'Y-m-d').' '.$v3[0]); 
						$endTime   = Time::toTime(Time::toDate(UTC_TIME, 'Y-m-d').' '.$v3[1]);

						while ( $beginTime <= $endTime) {
							$timeList[$v]['time'][] = Time::toDate($beginTime, 'H:i');
							$beginTime += 15 * 60; //分钟转换成秒
						}
					}
					unset($timeList[$v]['hours']);
				}

			}
		}

		//获取商家允许的时间段
		$time = [];
		$i = 0;


		while ( $i <= $reserve_days ){
			//获取未来每一天的时间戳 和 星期
			$nowTime = UTC_TIME + 86400 * $i;
			$week = date("w",  UTC_TIME + 86400 * $i);

			if($i == 0) {
				$dayName = '今天';
			}
			else if($i == 1) {
				$dayName = '明天';
			}
			else {
				$dayName = explode('-', Time::toDate($nowTime, 'm-d')); //x月x号
				$dayName = $dayName[0].'月'.$dayName[1].'日';
			}

			$dayName .= '(周' . $weekarray[$week] . ')'; //周几

			if(!empty($timeList[$week])){
				$time[] = [
					'time'		=> Time::toDate($nowTime, 'Y-m-d'),
					'dayName' 	=> $dayName,
					'list'	  	=> $timeList[$week]['time'],
				];
			}else{
				$reserve_days += 1;
			}

			foreach ($time as $key => $value) {
				foreach ($value['list'] as $k => $v) {
					$time[$key]['timestamp'][$k] = Time::toTime($value['time'].' '.$v);
				}
			}

			//处理当天时间
			//临近结业时间的时候可向后延长一个时间段
			if(Time::toDate($nowTime, 'Y-m-d') == Time::toDate(UTC_TIME, 'Y-m-d'))
			{

				$go = true;	//是否是立即送出
				foreach ($timeList[$week]['time'] as $key => $value)
				{
					//删除不可预约时间
					if( str_replace(":", "", $value) < Time::toDate(UTC_TIME, 'Hi') )
					{
						unset($time[0]['list'][$key]);
						unset($time[0]['timestamp'][$key]);
					}
					else
					{
						//立即送出
						if($go)
						{
							$time[0]['list'][0] = Time::toDate(UTC_TIME + $send_loop * 60, 'H:i');
							$time[0]['timestamp'][0] = UTC_TIME + $send_loop * 60;
							$go = false;
						}
						//删除可预约时间与立即送出的中间值
						// if($time[0]['timestamp'][$key+1] <= $time[0]['timestamp'][0])
						// {
						// 	unset($time[0]['list'][$key]);
						// 	unset($time[0]['timestamp'][$key]);
						// }
					}
				}
			}
			ksort($time[0]['list']);
			ksort($time[0]['timestamp']);
			$i++;
		}
		return $time;
	}
	
	/**
	 * 创建订单 - 开始
	 */
	public function toOrder() {
	    $args = Input::all();
	    $args['cartIds'] = explode(',', $args['cartIds']);
	    $result = $this->requestApi('order.create',$args);
	    return Response::json($result);
	}

	/**
	 * 催单
	 */
	public function urge() {
	    $id = Input::get('id');
	    $result = $this->requestApi('order.urge',['id'=>$id]);
	    return Response::json($result);
	}

    /**
     * 支付选择页面
     */
    public function orderpay() {
        $orderId = (int)Input::get('orderId');
        $order = $this->requestApi('order.detail',['id'=>$orderId]);
        if ($order['code'] > 0 || $order['payStatus'] == 1) {
            return Redirect::to('Order/index');
        }
        $payments = $this->getPayments();
        View::share('payments', $payments);
        View::share('orderId', $orderId);
        return $this->display();
    }

    /**
     * 收银台
     */
    public function cashierdesk() {
        $orderId = (int)Input::get('orderId');
        $order = $this->requestApi('order.detail',['id'=>$orderId]);
		
        $data = $order['data'];
        if ($order['code'] > 0 || $data['payStatus'] == 1) {
            return Redirect::to('Order/index');
        }
        $payments = $this->getPayments();
        unset($payments['balancePay']);
        unset($payments['cashOnDelivery']);

        View::share('payments', $payments);
        View::share('data', $data);

        $payPwd = $this->requestApi('user.userinfo');
        View::share('isPayPwd', $payPwd['data']['isPayPwd']);
        return $this->display();
    }

    public function propertypay(){
        // 算总金额
        $args = Input::all();
        $lists = $this->requestApi('propertyfee.getbyidslists',['ids'=>$args['ids']]);
        $totlepay = 0;
        foreach($lists['data'] as $k=>$v){
            $totlepay += $v['fee'];
        }
        $data['payFee'] = $totlepay;
        $data['user']= $this->user; 
        View::share('data', $data);
        $payPwd = $this->requestApi('user.userinfo');
        View::share('isPayPwd', $payPwd['data']['isPayPwd']);
        $balance_result = $this->requestApi('user.balance'); 
        View::share('balance', $balance_result['data']['balance']);

        $payments = $this->getPayments();
        unset($payments['balancePay']);
        unset($payments['cashOnDelivery']);

        View::share('payments', $payments);
        View::share('args', $args);
        return $this->display();

    }

    /**
     * 创建
     */
    public function createPropertyPay(){
		$args = Input::all(); 
        //余额支付,检测支付密码
        if ($args['payment'] == 'balancePay') {
            $checkPayPwd = $this->requestApi('user.checkpaypwd', ['password' => $args['key']]);
            if ($checkPayPwd['code'] != 0) {
                return Redirect::to(u('Property/index'));
            }
        } 
        if(!$args['isWeixinPay']){ 
			//创建订单
			$orderInfo = $this->requestApi('propertyorder.create', $args);     
			$args['orderId'] = $orderInfo['data']['id'];
			if($args['payment'] == 'weixinJs'){
				return Redirect::to(u('Order/pwxpay', ['orderId' => $orderInfo['data']['id'],'payment' => $args['payment']]));
			} 
			
        } else { 
			$args['extend']['openId'] = $args['openId'];
			$args['extend']['url'] = Request::fullUrl();
			//$args['extend']['url'] = "http://www.niusns.com/payment/o2o.php";
        }
 
		//创建支付信息
		$pay = $this->requestApi('propertyorder.payment', $args);    
		if($pay['code'] == 0){
			if (isset($pay['data']['payRequest']['html'])) {
				echo $pay['data']['payRequest']['html'];
				exit;
			} elseif($pay['data']['payRequest']['status']) {
				return Redirect::to(u('UserCenter/balance'));
			} elseif(isset($pay['data']['payRequest']['packages'])) {
				return Response::json($pay['data']);
			}
			View::share('pay', $pay['data']['payRequest']);
		} else {
			if($pay['code'] == 60318){
				return $this->error($pay['msg'], u('UserCenter/recharge'));
			}
			return $this->error($pay['msg']);
		}
		View::share('data',$pay['data']); 
		return $this->display('propertywxpay');
    }

    /**
     * 余额支付
     */
    public function balancepay() {
		$args = Input::all();
		$result = $this->requestApi('order.pay', $args);

		if ($result['code'] == 0) {
			return Redirect::to(u('Order/detail',['id' => $args['id']]));
		}else{
		    return $this->error($result['msg']);
		}
	}


    /**
     * 退款详情
     */
    public function refundview() {
        $orderId = (int)Input::get('orderId');
        $result = $this->requestApi('order.refundview',['orderId' => $orderId]);
        //print_r($result);
        View::share('data', $result['data']);
        return $this->display();
    }

    /**
     * 不显示优惠信息
     */
    public function notshow(){
        $orderId = (int)Input::get('orderId');
        $result = $this->requestApi('order.notshow',['orderId' => $orderId]);
    }


    /**
     * 收银台
     */
    public function livepay() {
        $args = Input::all();


        $payments = $this->getPayments();
        unset($payments['balancePay']);
        unset($payments['cashOnDelivery']);
//        unset($payments['unionpay']);
//        unset($payments['unionapp']);
        View::share('payments', $payments);
        View::share('args', $args);
        $user['balance'] = $this->user['balance'];
        View::share('user', $user);
        $payPwd = $this->requestApi('user.userinfo');
        View::share('isPayPwd', $payPwd['data']['isPayPwd']);

        return $this->display();
    }

    public function createlivelog()
    {
        $args = Input::all();

        //余额支付,检测支付密码
        if ($args['balancePay'] == 1) {
            $checkPayPwd = $this->requestApi('user.checkpaypwd', ['password' => $args['key']]);
            if ($checkPayPwd['code'] != 0) {
                return Redirect::to(u('Order/livepay').'?'.http_build_query($args));
            }
        }
        if (!empty($args['balancePay'])){
            $args['extend']['balancePay'] = (int)$args['balancePay'];
        }

        $pay = $this->requestApi('Order.handpay', $args);
        die(json_encode($pay["data"]));
    }

    /**
     * 生活缴费
     */
    public function handpay() {
        $args = Input::all();

        //余额支付,检测支付密码
        if ($args['balancePay'] == 1) {
            $checkPayPwd = $this->requestApi('user.checkpaypwd', ['password' => $args['key']]);
            if ($checkPayPwd['code'] != 0) {
                return Redirect::to(u('Order/livepay').'?'.http_build_query($args));
            }
        }

        $pay_payment = Session::get('pay_payment');
        if (isset($args['payment']) && $args['payment'] == 'weixinJs' && empty($pay_payment)) {
            Session::put('wxpay_open_id', $args['openId']);
            Session::put('pay_payment', 'weixinJs');
            Session::save();
            return Redirect::to(u('Order/handpay',$args));
        }

        if (!isset($args['payment'])) {
            $args['payment'] = Session::get('pay_payment');
            $args['openId'] = Session::get('wxpay_open_id');
        }
        $args['extend']['url'] = Request::fullUrl();
        //$args['extend']['url'] = "http://www.niusns.com/payment/o2o.php";

        if (!empty($args['openId'])) {
            $args['extend']['openId'] = $args['openId'];
        }
        if (!empty($args['balancePay'])){
            $args['extend']['balancePay'] = (int)$args['balancePay'];
        }

        $pay = $this->requestApi('Order.handpay', $args);
        if($pay['code'] == 0){
            if (isset($pay['data']['payRequest']['html'])) {
                echo $pay['data']['payRequest']['html'];
                exit;
            }else{
                return Redirect::to(u('Property/livelog'));
            }
        }
    }

    /**
     * 生成图形验证码
     */
    public function imgverify() {
        $this->createVerify();
        $imgVerify = new ImgVerify();
        $imgVerify->doimg();
        $code = $imgVerify->getCode();
        Session::set('imgVerify', $code);
        Session::save();
        exit;
    }
    public function createVerify()
    {
        Session::set("user_reg", md5(Request::getClientIp().$_SERVER['HTTP_USER_AGENT']));
        Session::set("user_reg_time", UTC_TIME);
        Session::save();
    }

    /**
     * [bindmobile 绑定手机号]
     */
    public function bindmobile() {
        $data = Input::all();
        $userinfo = Session::get('user');
        $data['unionid'] = $userinfo['unionid'];
        $data['openid'] = $userinfo['openid'];

        $result = $this->requestApi('user.bindmobile',$data);
        if($result['code'] == 0){
            $this->setUser($result['data']);
            $this->setSecurityToken($result['token']);
        }
        return Response::json($result);
    }

    /**
     * 生成验证码
     */
    public function verify() {
        if($this->checkVerify())
        {
            $mobile = Input::get('mobile');
            $type = Input::get('type') ? Input::get('type') : 'reg';
            $result = $this->requestApi('user.mobileverify',array('mobile'=>$mobile, 'type'=>$type));
            return Response::json($result);
        }else{
            return Response::json(['code'=>1,'msg'=>Session::get("check_error")]);
        }
    }

    private function checkVerify()
    {
        if (!Request::ajax())
        {
            Session::put("check_error", '非法请求！1');
            Session::save();
            return false;
        }

//        $referer = Request::header('REFERER');
//        if ($referer != u('User/reg') && $referer != u('User/login',['quicklogin'=>1]) && $referer != u('User/repwd') && $referer != u('Order/order'))
//        {
//            Session::put("check_error", '非法请求！2');
//            Session::save();
//            return false;
//        }

        $imgVerify = Session::get('imgVerify');

        if (strtolower($imgVerify) !== strtolower(Input::get('imgverify')) )
        {
            Session::put("check_error", '验证码不正确！');
            Session::save();
            return false;
        }

        Session::set('imgverify', "");

        Session::save();

        $userRegTime 	= Session::get('user_reg_time');

        $userRegTimeRe 	= (int)Session::get('user_reg_time_re');

        /*if(Session::get('user_reg') != md5(Request::getClientIp().$_SERVER['HTTP_USER_AGENT']) ||
            ($userRegTimeRe > Time::getTime() - 60) || $userRegTime >= Time::getTime()) {
            //die('33');
            return false;
        }*/

        Session::put("user_reg_time_re", Time::getTime());

        Session::save();

        return true;
    }

    public function moveunionid(){
        $data = Input::all();
        $userinfo = Session::get('user');
        $data['unionid'] = $userinfo['unionid'];
        $data['openid'] = $userinfo['openid'];

        $result = $this->requestApi('user.moveunionid',$data);
        if($result['code'] == 0){
            $this->setUser($result['data']);
            $this->setSecurityToken($result['token']);
        }
        return Response::json($result);
    }

    public function bindmobile2(){
        $userinfo = Session::get('user');
        if(empty($userinfo['mobile'])){
            View::share('return', u('UserCenter/index'));
            return $this->display('bindmobile');
        }
    }

    public function saveUserAddressInfo()
    {
    	$args = Input::all();
    	if($args['doorplate'] != ''){
    		Session::put('userAddInfo.doorplate', $args['doorplate']);
    	}
    	if($args['name'] != ''){
    		Session::put('userAddInfo.name', $args['name']);
    	}
    	if($args['mobile'] != ''){
    		Session::put('userAddInfo.mobile', $args['mobile']);
    	}
    	Session::save();
    	die;
    }

    public function recountCashMoney() {
    	$args = Input::all();
    	$result = $this->requestApi('order.recountCashMoney',$args);
    	return Response::json($result['data']);
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
}