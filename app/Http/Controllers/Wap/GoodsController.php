<?php namespace YiZan\Http\Controllers\Wap;
use YiZan\Models\Goods;

use View, Input, Lang, Route, Page ,Session, Redirect, Response;
/**
 * 服务
 */
class GoodsController extends BaseController {

	//
	public function __construct() {
		parent::__construct();
		View::share('nav','index');
	}

    /**
     * 服务列表
     */
    public function index(){
        $option = Input::all();
        if($option['type'] == 2){
            $model = 'serviceindex';
        } else {
            $model = 'goodsindex';
        }
        return $this->indexList($model);
    }


    public function indexList($tpl='service_item') {
        $option = Input::all();

        $adv_result = $this->requestApi('config.seller');

        View::share('adv', $adv_result['data']);
        //获取商家详情
        $seller_result = $this->requestApi('seller.detail', $option);
        if($seller_result['data']['storeType']){
            return Redirect::to(u('Seller/detail', ['id'=>$option['id']]));
        }
        View::share('seller', $seller_result['data']);

        if($option['type'] == 2){
            $cate_result = $this->requestApi('service.lists', $option);
        } else {
            $cate_result = $this->requestApi('goods.lists2', $option);
        }
        View::share('cate', $cate_result['data']);


        //清空购物车的服务
        $this->requestApi('shopping.delete', ['type'=>2]);

        //获取购物车
        $result_cart = $this->requestApi('shopping.lists');
        $cart['totalPrice'] = 0;
        $cart['totalAmount'] = 0;
        $cart['sale'] = 0;
        foreach ($result_cart['data'] as $key1 => $value1) {
            if($value1['id'] == $option['id'] && $option['type'] == $value1['type']){
                foreach ($value1['goods'] as $key2 => $value2) {
                    $cart['totalAmount'] += $value2['num'];
                    if($value2['sale'] >= 0){
                        $cart['totalPrice'] += $value2['num'] * ($value2['price'] * $value2['sale']) / 10;
                        $cart['sale'] +=  $value2['num'] *  $value2['price'] * ( 1 - $value2['sale'] / 10);
                    }else{
                        $cart['totalPrice'] += $value2['price'] * $value2['num'];
                    }
                }
                $cart['data'] = $value1;
            }
        }

        //获取商家详情 cz
        $first_result = $this->requestApi('user.firstOrder',['sellerId'=>$option['id']]);
        if(!empty($first_result['data'])){
            if(!empty($seller_result['data']['activity']['new']) && $cart['totalPrice'] >= $seller_result['data']['activity']['new']['fullMoney']){
                $cart['firstOrder'] = 1;
            }
            View::share('activity_arr', $seller_result['data']['activity']);
        }

        View::share('cart', $cart);

        $article_result = $this->requestApi('article.lists', ['sellerId'=>$option['id']]);
//        print_r($article_result);die;
        View::share('articles', $article_result['data']);
/*        $nav_back_url = u('Seller/detail',['id'=>$option['id']]);
        View::share('nav_back_url', $nav_back_url);*/
        View::share('option', $option);
        View::share('shareType','seller' );
		$data = [
			'sellerId' => $seller_result['data']['id']
		];
        View::share('data',$data );

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $protocol.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
        $weixin_arrs = $this->requestApi('invitation.getweixin',array('url' => $url));
        if($weixin_arrs['code'] == 0){
            View::share('weixin',$weixin_arrs['data']);
        }

        $share = [
        	'title'		=>	$seller_result['data']['name'],
			'content'	=>	$seller_result['data']['detail'],
			'url'		=>	u('Goods/index', $option),
			'logo'		=> 	$seller_result['data']['logo'],
        ];

		$getWeixinUser = $this->requestApi('Useractive.getWeixinUser',['openid'=>$this->user['openid']]);
		$newtitle = $getWeixinUser['data']['nickname']."为您推荐了一件商品";

		$share['title'] = $newtitle;
        View::share("weiXinData",  $getWeixinUser['data']);

        $weiXinUserData = Session::get("user");
        View::share('weiXinUserData',$weiXinUserData);
        View::share("user",  $weiXinUserData);

        View::share("share", $share);
        if( $seller_result['data']['id'] == ONESELF_SELLER_ID){
            $return_url = u('Oneself/index');
            View::share('nav_back_url',$return_url);
        }

        if($option['source'] == 'order'){
            $return_url = u('Order/index');
            View::share('nav_back_url',$return_url);
        }

        if($option['showgo'] == 1){
            $return_url = u('Index/special',['id'=>4]);
            View::share('nav_back_url',$return_url);
        }

        View::share('url',u('Goods/index',$option));
        return $this->display($tpl);
    }

    public function sellergoods_list(){
        $option = Input::all();

        //获取商家详情
        $seller_result = $this->requestApi('seller.detail', $option);
        if($seller_result['data']['storeType']){
            return Redirect::to(u('Seller/detail', ['id'=>$option['id']]));
        }
        View::share('seller', $seller_result['data']);

        //商品
        $cate_result = $this->requestApi('goods.lists2', $option);
        View::share('cate', $cate_result['data']);

        //获取购物车
        $result_cart = $this->requestApi('shopping.lists');
        $cart['totalPrice'] = 0;
        $cart['totalAmount'] = 0;
        $cart['sale'] = 0;
        foreach ($result_cart['data'] as $key1 => $value1) {
            if($value1['id'] == $option['id'] && $option['type'] == $value1['type']){
                foreach ($value1['goods'] as $key2 => $value2) {
                    $cart['totalAmount'] += $value2['num'];
                    if($value2['sale'] >= 0){
                        $cart['totalPrice'] += $value2['num'] * ($value2['price'] * $value2['sale']) / 10;
                        $cart['sale'] +=  $value2['num'] *  $value2['price'] * ( 1 - $value2['sale'] / 10);
                    }else{
                        $cart['totalPrice'] += $value2['price'] * $value2['num'];
                    }
                }
                $cart['data'] = $value1;
            }
        }
        View::share('cart', $cart);

        $cartgoods = [];
        foreach($cart["data"]["goods"] as $good)
        {
            $cartgoods[$good["goodsId"]][$good["normsId"]]  = ["num"=>$good["num"], "price"=>$good["price"]];
        }
        View::share('cartgoods', $cartgoods);

        View::share('page', $option['page']);
        View::share('ajax', $option['ajax']);
        return $this->display('goodsindex_item');
    }


	public function sellerdetail(){ 
		$option = Input::all(); 
		//获取商家详情
		$seller_result = $this->requestApi('seller.detail', $option); 
		View::share('seller', $seller_result['data']);
		//获取公告
		$article_result = $this->requestApi('article.lists', ['sellerId'=>$option['id']]); 
		View::share('articles', $article_result['data']); 
		return $this->display();
	} 

	/**
	 * 搜索服务 
	 */
	public function search(){
		//热门标签
		$hot_tags = $this->requestApi('goods.tag.gethottags');
		if ($hot_tags['code'] == 0) {
			View::share('hot_tags',$hot_tags['data']);
		}
		$keywords = Input::get('keywords');
		$searchs = array();
		if (Session::get('goods_searchs')) {
			$searchs = Session::get('goods_searchs');
		}
		if (!empty($keywords) && !in_array($keywords, $searchs)) {
			array_push($searchs, $keywords);
			Session::set('goods_searchs', $searchs);
			Session::save();
		}
		$history_search = Session::get('goods_searchs');
		View::share('data',$history_search); 
		if (Input::get('type')) {
			//return true;
		} else {
			return $this->display();
		}
	}

	/**
	 * 清除搜索历史记录
	 */
	public function clearsearch(){
		Session::set('goods_searchs', NULL);
		Session::save();
	}

	/**
	 * 服务列表 汽车、其他
	 */
	public function goodsList() {
		$args = Input::all();
		$data['id'] = $args['arg'];
		$data['page'] = $args['page'] > 0 ? $args['page'] : 1;

		//获取服务详细
		$result = $this->requestApi('service.lists',$data);
		if($result['code'] == 0){
			View::share('lists', $result['data']);
		}

		View::share('args', $args);
		if(Input::ajax()){
			return $this->display('goodslist_item');
		}else{
			return $this->display();
		}
		
	}

	/**
	 * 服务明细
	 */
	public function detail(){
		$option = Input::all();
		//获取商品/服务详情数据
		$goods_result = $this->requestApi('goods.detail',$option);

		//获取购物车数据
		$result_cart = $this->requestApi('shopping.lists');
		$cart['totalPrice'] = 0;
		$cart['totalAmount'] = 0;
        $cartIds = 0;
        $num = 0;
		foreach ($result_cart['data'] as $key1 => $value1) {
            if($value1['id'] == $goods_result['data']['sellerId']){
				foreach ($value1['goods'] as $key3 => $value3) {
                    if($option['goodsId'] == $value3['goodsId']){
                        $num = $value3['num'];
                    }
                    if($value3['type'] == $goods_result['data']['type']){
                        if($goods_result['data']['seller']['storeType'] == 1){
                            if($value3['goodsId'] == $option['goodsId']) {
                                $goods_result['data']['num'] = $value3['num'];
                                $cart['goods'][] = $value3;
                            }
                        }else{
                            if($value3['type'] == $goods_result['data']['type']){
                                $cart['goods'] = $value1['goods'];
                            }
                        }
                        $cart['totalAmount'] += $value3['num'];
                        if($value3['sale'] <= 0){
                            $cart['totalPrice'] += $value3['price'] * $value3['num'];
                        }else{
                            $cart['totalPrice'] += $value3['num'] * ($value3['price'] * $value3['sale']) / 10;
                        }
                        $cartIds .= $value3['id'].',';
                    }
                }
			}
		}


        $cartIds = rtrim($cartIds,',');
        View::share('cartIds', $cartIds);


		//按规格存放 caiq
		$main_goods = [];
        $yes_fu = 0;
        foreach($cart['goods'] as $key=>$goods){
            foreach($main_goods as $key2=>$goods2){
                if($key2 == $goods['goodsId']){
                    $yes_fu = 1;
                }
            }
            if($yes_fu == 1){
                array_push($main_goods,$goods);
            }else{
                $main_goods[$goods['goodsId']] = $goods;
            }
        }
		$cart['goods'] = $main_goods;


        //规格商品在购物车中的数量 caiq
		foreach ($goods_result['data']['norms'] as $keyN => $norms) {
			$goods_result['data']['norms'][$keyN]['inCart'] = 0;
			foreach ($cart['goods'] as $keyG => $goods) {
				if($norms['id'] == $goods['normsId']){
					$goods_result['data']['norms'][$keyN]['inCart'] = $goods['num'];break;
				}
			}
		}


        $seller_result = $this->requestApi('seller.detail', ['id' => $goods_result['data']['seller']['id']]);

        foreach ($seller_result['data']['activity']['special'] as $key => $value) {
        	if($key == $option['goodsId'])
        	{
        		$seller_result['data']['activity']['special'] = $value;
        		break;
        	}
        	else{
        		$seller_result['data']['activity']['special'] = null;
        	}
        }

//        print_r($goods_result);exit;

        View::share('shareType','goods');
        View::share('data', $goods_result['data']);
        $option['type'] = $goods_result['data']['type'];
        View::share('option', $option);
        View::share('seller', $seller_result['data']);
        if($goods_result['data']['sellerId'] == ONESELF_SELLER_ID){
            $return_url = u('Oneself/index');
            View::share('nav_back_url',$return_url);
        }

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $protocol.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
        $weixin_arrs = $this->requestApi('invitation.getweixin',array('url' => $url));
        if($weixin_arrs['code'] == 0){
            View::share('weixin',$weixin_arrs['data']);
        }

        $share = [
        	'title'		=>	$goods_result['data']['name'],
			'content'	=>	$goods_result['data']['brief'],
			'url'		=>	u('Goods/detail', $option),
			'logo'		=> 	$goods_result['data']['logo'],
        ];
		
		$getWeixinUser = $this->requestApi('Useractive.getWeixinUser',['openid'=>$this->user['openid']]);
		$newtitle = $getWeixinUser['data']['nickname']."为您推荐了一件商品";
		$share['title'] = $newtitle;
		View::share("weiXinData",  $getWeixinUser['data']);
		View::share("nickname",  $getWeixinUser['data']['nickname']);

        $weiXinUserData = Session::get("user");
        View::share('weiXinUserData',$weiXinUserData);
        View::share("share", $share);

        View::share('url',u('Goods/detail',$option));


        //获取商家详情 cz
        $first_result = $this->requestApi('user.firstOrder',['sellerId'=>$goods_result['data']['seller']['id']]);
        if(!empty($first_result['data'])){
            if(!empty($seller_result['data']['activity']['new']) && $cart['totalPrice'] >= $seller_result['data']['activity']['new']['fullMoney']){
                $cart['firstOrder'] = 1;
            }
            View::share('activity_arr', $seller_result['data']['activity']);
        }
        View::share('cart', $cart);

        if($option['showgo'] == 1){
            $return_url = u('Index/special',['id'=>4]);
            View::share('nav_back_url',$return_url);
        }

        if($goods_result['data']['type'] == Goods::SELLER_SERVICE) {
			return $this->display('servicedetail');
		} else {
			//邀请注册
			if(Input::get('shareUserId') > 0)
			{
				Session::put('invitationType', 'user');
				Session::put('invitationId', Input::get('shareUserId'));
				Session::save();
			}
            if($goods_result['data']['seller']['storeType'] == 1){
                //平台客服电话
                $wap_service_tel = $this->getConfig('wap_service_tel');
                View::share('wap_service_tel', $wap_service_tel);

                return $this->display('allgoodsdetail');
            }else{
                return $this->display('goodsdetail');
            }
		}
        $config = $this->getConfig();

        $staff_settled_image = $config['staff_settled_image'];
        $site_name = $config['site_name'];
        View::share('site_name',$site_name);
	}

	/**
	 * 服务简介
	 */
	public function brief(){
		$id = (int)Input::get('id'); 
		$goods_data = $this->requestApi('goods.detail',array('goodsId'=>$id));
		if(empty($goods_data['data'])){
			return Redirect::to('Goods/index');
		} else {
			View::share('top_title',$goods_data['data']['name']);
			View::share('goods_data',$goods_data['data']); 
			return $this->display();
		}
	} 
	
	/**
	 * 商品服务简介
	 */
	public function appbrief()
    { 
		$goods_data = $this->requestApi('goods.detail', Input::all()); 
		if(empty($goods_data['data'])){
			return Redirect::to('Goods/index');
		} else {
			View::share('top_title',$goods_data['data']['name']);
			View::share('goods_data',$goods_data['data']); 
			return $this->display();
		} 
		// $result = $this->requestApi('goods.detail', Input::all());
		
		// if($result['code'] == 0)
  //       {
  //           echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><style>body{padding:0; margin:0;} img{width:100%;}</style>" . $result['data']["brief"];
  //       }
	    
  //       exit;
	}

	/**
	 * 服务时间
	 */
	public function appointday(){
		$args = Input::all();
		$args['duration'] = isset($args['timelen']) ? $args['timelen'] * SERVICE_TIME_LEN : 0;
		$result = $this->requestApi('goods.appointday', $args);
		if ($result['code'] > 0) {
			return $this->error($result['msg']);
		}
		$html = Response::view($this->getDisplayPath('goods', 'date_frame'), ['reservation_date' => $result['data']])->getContent();

		
		return $this->success('', '', $html);
	}

	/**
	 * 服务评价
	 */
	public function discuss() {
		$args = Input::all();
		$result = $this->requestApi('rate.service.lists',$args);

		if($result['code'] == 0)
			View::share('list',$result['data']);

		View::share('args', $args);
		if(Input::ajax()){
			return $this->display('discuss_item');
		}else{
			return $this->display();
		}
		
	}

	/**
	 * 获取购物车
	 */
	public function getCart(){
		if(!Session::get('user')){
			$cart = ['code'=>-1,'data'=>''];
		} else {
			$cart = $this->requestApi('shopping.lists');
		}
		return Response::json($cart);
	}

	/**
	 * 删除购物车
	 */
	public function cartDelete(){
		$result_cart = $this->requestApi('shopping.delete', Input::all());
		if($result_cart['code'] > 0){
			return $this->error('删除失败');
		} elseif((int)Input::get('id') == 0) {
			return $this->success('购物车已清空');
		} else {
            return $this->success('已删除');
        }
	}

	/**
	 * 保存商品至购物车
	 */
	public function saveCart(){
		$args = Input::all();
		if(!Session::get('user')){
			$result_cart = ['code'=>-1,'data'=>''];
		} else {
			$result_cart = $this->requestApi('shopping.save', $args);

            $sellerId = $args['sellerId'];

            $list = $result_cart['data'];
			unset($result_cart['data']);
			$result_cart['data']['totalPrice'] = 0;
			$result_cart['data']['totalAmount'] = 0;
            $result_cart['data']['sale'] = 0;
			$result_cart['data']['list'] = [];
			foreach ($list as $key1 => $value1) {
                if($value1['id'] == $sellerId && $value1['type'] == $args['type']){
                    $result_cart['data']['list'][]  = $value1;
                    foreach ($value1['goods'] as $key2 => $value2) {
                        $result_cart['data']['totalAmount'] += $value2['num'];
                        if($value2['sale'] >= 0){
                            $result_cart['data']['totalPrice'] += $value2['num'] * ($value2['price'] * $value2['sale']) / 10;
                            $result_cart['data']['sale'] +=  $value2['num'] * $value2['price'] * ( 1 - $value2['sale'] / 10) ;
                        }else{
                            $result_cart['data']['totalPrice'] += $value2['price'] * $value2['num'];
                        }
                    }
			    }
            }
            //获取商家详情 cz
            $first_result = $this->requestApi('user.firstOrder',['sellerId'=>$sellerId]);
            if(!empty($first_result['data'])){
                $seller_result = $this->requestApi('seller.detail', ['id'=>$sellerId]);
                if(!empty($seller_result['data']['activity']['new'])){
                    if($result_cart['data']['totalPrice'] >= $seller_result['data']['activity']['new']['fullMoney']){
                        $result_cart['data']['firstOrder'] = 1;
                        $result_cart['data']['sale'] = $seller_result['data']['activity']['new']['cutMoney'];
                    }else{
                        $result_cart['data']['firstOrder'] = 1;
                        $result_cart['data']['activity_name'] = $seller_result['data']['activity']['new']['name'];
                        $result_cart['data']['que_fee'] = $seller_result['data']['activity']['new']['fullMoney']-$result_cart['data']['totalPrice'];
                    }
                }else{
                    $result_cart['data']['firstOrder'] = 0;
                }
            }
		}
        //获取当前服务的购物车信息
        $cartInfo = $this->requestApi('shopping.getInfo', ['goodsId'=>$args['goodsId'],'skuSn'=>$args['skuSn']]);
        $result_cart['data']['cartIds'] = $cartInfo['data']['id'];
		return Response::json($result_cart);
	}

	/**
	 * 收藏
	 */
	public function collect(){
		$args = Input::all();
		$args['type'] = 1;
		$result_collect = $this->requestApi('collect.create',$args); 
	}

    /**
     * 周边店评论
     */
    public function comment() {
        $option = Input::get();
        //获取商家详情
        $seller_result = $this->requestApi('seller.detail', $option);

        if( $seller_result['data']['id'] == ONESELF_SELLER_ID){
            $return_url = u('Oneself/index');
            View::share('nav_back_url',$return_url);
        }
        
        View::share('seller', $seller_result['data']);

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $protocol.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
        $weixin_arrs = $this->requestApi('invitation.getweixin',array('url' => $url));
        if($weixin_arrs['code'] == 0){
            View::share('weixin',$weixin_arrs['data']);
        }

        $share = [
        	'title'		=>	$seller_result['data']['name'],
			'content'	=>	$seller_result['data']['detail'],
			'url'		=>	u('Goods/index', ['id'=>Input::get('id'), 'type'=>1]),
			'logo'		=> 	$seller_result['data']['logo'],
        ];
        View::share("share", $share);

    	return $this->commentList('comment');
    }

    public function commentList($tpl='comment_item') {
    	$args = [
            'sellerId' => (int)Input::get('id'),
            'type' => (int)Input::get('type'),
            'page' => max((int)Input::get('page'),1)
        ];
        $count = $this->requestApi('rate.order.statistics',['sellerId' => $args['sellerId']]);
        $list = $this->requestApi('rate.order.lists',$args);
        $article_result = $this->requestApi('article.lists', ['sellerId'=>$args['sellerId']]);
        View::share('articles', $article_result['data']);
        View::share('count', $count['data']);
        View::share('list', $list['data']);
        View::share('args', $args);

    	return $this->display($tpl);
    }

    // 全国店商品评论
    public function commentall() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $protocol.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
        $weixin_arrs = $this->requestApi('invitation.getweixin',array('url' => $url));
        if($weixin_arrs['code'] == 0){
            View::share('weixin',$weixin_arrs['data']);
        }

        $share = [
        	'title'		=>	$seller_result['data']['name'],
			'content'	=>	$seller_result['data']['detail'],
			'url'		=>	u('Goods/index', ['id'=>Input::get('id'), 'type'=>1]),
			'logo'		=> 	$seller_result['data']['logo'],
        ];
        View::share("share", $share);

    	return $this->commentAllList('commentall');
    }

     public function commentAllList($tpl='commentall_item') {
    	$args = [
            'goodsId' => (int)Input::get('id'),
            'type' => (int)Input::get('type'),
            'page' => max((int)Input::get('page'),1)
        ];

        $count = $this->requestApi('rate.goods.statistics',['goodsId' => $args['goodsId']]);

        $list = $this->requestApi('rate.goods.lists',$args);


        View::share('count', $count['data']);
        View::share('list', $list['data']);
        View::share('args', $args);

    	return $this->display($tpl);
    }

    public function skus() {
        $data = $this->requestApi('goods.skus', ['goodsId' => Input::get('id')]);
        return Response::json($data);
    }
}
