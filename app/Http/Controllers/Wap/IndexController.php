<?php namespace YiZan\Http\Controllers\Wap;

use YiZan\Utils\Time;
use View, Input, Lang, Route, Page ,Session,Log,Redirect, Cache,Response;
/**
 * 首页
 */
class IndexController extends BaseController {

    //
    public function __construct() {
        parent::__construct();
        View::share('nav','index');
    }

    public function setShareNum() {

        $result =  $this->requestApi('goods.setsharenum',Input::all());
        return Response::json($result);
    }
    /**
     * 首页信息
     */
    public function index() {
		//APP每次打开重新定位
		if((int)Input::get('show_prog') == 1) {
			Session::set("defaultAddress", null);
			Session::save();
		}
        //新加看看check
        $sellercheck = $this->requestApi('seller.check', ['id'=>$this->userId]);
        View::share('seller',$sellercheck['data']);
        $setSellerReg = Session::get('setSellerReg');
        if(!empty($setSellerReg) && (empty($sellercheck['data']) || $sellercheck['data']["isCheck"] != 1)){
            Session::set('setSellerReg',null);
            Redirect::to(u('Seller/reg'))->send();
        }

        //其他东西要当首页cz
        $index_data = $this->requestApi('indexnav.index');
        $nourl = Input::get('nourl');
        if(!empty($index_data['data']) && $index_data['data']['type'] != 1 && empty($nourl)){
            Redirect::to(u($index_data['data']['url']))->send();
        }

        $defaultAddress = Session::get("defaultAddress");
        if (!$defaultAddress) {
            $address = $this->requestApi('user.address.getdefault');
            $defaultAddress = $address['data'];
            $defaultAddress['address'] = $address['data']['detailAddress'];
            if(!empty($defaultAddress)){
                $defaultAddress['cityId'] = $address['data']['city']['id'];
            }
            unset($defaultAddress['city']);
            Session::put("defaultAddress", $defaultAddress);
        }

        if (isset($defaultAddress['here']) && $defaultAddress['here'] == 1) {
            $defaultAddress = [];
        }

        $args = Input::get();

        if(!empty($args['address']) && !empty($args['mapPointStr'])){
            $defaultAddress['address'] = Input::get('address');
            $defaultAddress['mapPointStr'] = Input::get('mapPointStr');
            $defaultAddress['isSetCity'] = 0;
            $args = [
                'name'=>Input::get('city'),
                'area' => Input::get('area')
            ];
            $city =  $this->requestApi('user.address.getbyname',$args);

            if($city['code'] == 0){
                $defaultAddress['cityId'] = $city['data']['id'];
                $defaultAddress['cityName'] = $city['data']['name'];
                $defaultAddress['areaId'] = $city['data']['area']['id'];
                $defaultAddress['areaName'] = $city['data']['area']['name'];
                $index_data = $this->requestApi('indexnav.lists', $defaultAddress);
                View::share('indexnav', $index_data['data']);
                Session::put('indexnav', $index_data['data']);
            }
            Session::put("defaultAddress", $defaultAddress);
        }

        //未进行定位时,进入加载定位页面
        if(empty($defaultAddress['mapPointStr'])){
            return $this->display('loading');
        }

        View::share("args", $args);

        $is_service = [];
        $is_service['data'] = 1;

        if(!empty($defaultAddress)){
            //判断该城市是否开通
            $is_service =  $this->requestApi('user.address.getisservice',['cityId'=>$defaultAddress['cityId']]);
        }
        View::share("cityIsService", $is_service['data']);

        //首页轮播图信息 + 菜单 + 活动
        if (!Cache::has('index_config_' . $defaultAddress['cityId'])) {
            $data = $this->requestApi('config.index', ['cityId'=>$defaultAddress['cityId']]);
            Cache::put('index_config_' . $defaultAddress['cityId'], $data['data'], 1);
        } else {
            $data['data'] = Cache::get('index_config_'.$defaultAddress['cityId']);
        }

        //轮播图下菜单
        if(!Cache::has('index_menu_'.$defaultAddress['cityId'])) {
            $menu = $this->requestApi('Config.getmenu',['cityId'=>$defaultAddress['cityId']]);
            Cache::put('index_menu_'.$defaultAddress['cityId'], $menu, 1);
        } else {
            $menu = Cache::get('index_menu_'.$defaultAddress['cityId']);
        }

        $type = $this->getConfig('buyer_index_show');
        View::share('type', $type);

        View::share('menu', $menu['data']);

        View::share('data', $data['data']);
		$defaultAddress['address'] = ereg_replace("undefined","",$defaultAddress['address']);
        View::share("orderData", $defaultAddress);

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $protocol.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
        $weixin_arrs = $this->requestApi('Useractive.getweixin',array('url' => $url));
        if($weixin_arrs['code'] == 0){
            View::share('weixin',$weixin_arrs['data']);
        }
        $getWeixinUser = $this->requestApi('Useractive.getWeixinUser',['openid'=>$this->user['openid']]);
        View::share("nickname",  $getWeixinUser['data']['nickname']);
		View::share("weiXinData",  $getWeixinUser['data']);

        $weiXinUserData = Session::get("user");
        View::share('weiXinUserData',$weiXinUserData);
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            View::share('weixinliu', 1);
        }

        View::share('user', $this->userId);
        //邀请注册
        if( strtolower(Input::get('type')) == 'user' && Input::get('id') > 0)
        {
            Session::put('invitationType', Input::get('type'));
            Session::put('invitationId', Input::get('id'));
        }
        Session::save();

        $couponmsg = $this->requestApi('user.promotion.couponmsg');

        if($couponmsg['code'] == 0 && $couponmsg['data']['num']){
            View::share('content', $couponmsg['data']['content']);
        }
        return $this->display();
    }

    public function indexList(){
        $args = Input::all();

        $defaultAddress = Session::get("defaultAddress");
        if(empty($defaultAddress['cityId'])){
            return false;
        }
        $args['mapPoint'] = $defaultAddress['mapPointStr'];
        $args['cityId'] = $defaultAddress['cityId'];
        $type = $this->getConfig('buyer_index_show');
        if($type == 1 || $args['noIndex'] == 1){
            $goods = $this->requestApi('config.getrecommendgoods', $args);
            $data['goods'] = $goods['data'];
            if($goods['code'] == 0){
                View::share('data', $data);
            }
            return $this->display('lists_item2');
        }else{
            $list = $this->requestApi('config.getrecommendsellers', $args);
            $data['sellers'] = $list['data'];
            if($list['code'] == 0){
                View::share('data', $data);
            }
            return $this->display('lists_item');
        }

    }

    public function nav() {
        $defaultAddress = Session::get("defaultAddress");
        $menu = '';
        if (!empty($defaultAddress)) {
            $menu = $this->requestApi('Config.getmenu',['cityId'=>$defaultAddress['cityId']]);
        }
        View::share('menu', $menu['data']);

        return $this->display();
    }

    //清空定位信息
    public function relocation() {
        Session::set('defaultAddress', NULL);
    }

    //不是会员新增地址
    public function addressmap(){
        $cityAddress = Input::all();
        $oneself = $cityAddress['oneself'];
        if($oneself == 1){
            $nav_back_url = u('Oneself/index');
        }else{
            $nav_back_url = u('Index/index');
        }
        View::share('nav_back_url', $nav_back_url);
        View::share('oneself', $oneself);

        unset($cityAddress['oneself']);
        if(empty($cityAddress)){
            $cityAddress = Session::get("defaultAddress");
        }
        View::share('cityAddress', $cityAddress);

        $cityinfo =  $this->requestApi('user.address.getbyid',['cityId'=>$cityAddress['cityId']]);
        View::share('cityinfo', $cityinfo['data']);


        $args = Input::all();
        $list = $this->requestApi('user.address.lists',$args);
        View::share('list',$list['data']);

        View::share('userId',$this->userId); 

        return $this->display();
    }

    public function addrsearch(){
        $defaultAddress = Input::all();
        if(empty($defaultAddress)){
            $defaultAddress = Session::get("defaultAddress");
        }
        $data['data']['mapPointStr'] = $defaultAddress['mapPointStr'];

        $cityinfo =  $this->requestApi('user.address.getbyid',['cityId'=>$defaultAddress['cityId']]);
        View::share('data', $data['data']);
        View::share('cityinfo', $cityinfo['data']);

        return $this->display();
    }

    public function cityservice(){
        //获取开通的城市
        $city = $this->requestApi('user.address.getservicecity');

        $args = Session::get("defaultAddress");
        //print_r($args);
        if(!empty($args['city'])){
            $cityinfo =  $this->requestApi('user.address.getbyname',['name'=>$args['city']]);
        }else{
            $cityinfo =  $this->requestApi('user.address.getbyid',['cityId'=>$args['cityId']]);
        }
        //print_r($city);
        View::share('cityinfo', $cityinfo['data']);
        View::share('city', $city['data']);

        $args = Input::get();
        View::share('args', $args);

        return $this->display();
    }

    public function relocation2(){
        $args = Input::get();

        if((int)Input::get('isSetCity') == 1){
            $defaultAddress['address'] = Input::get('address');
            $defaultAddress['realAddress'] = Input::get('realAddress');
            $defaultAddress['mapPointStr'] = Input::get('mapPointStr');
            $defaultAddress['isSetCity'] = (int)Input::get('isSetCity');
            $defaultAddress['mobile'] = Input::get('mobile');
            $defaultAddress['isIndexSetAddress'] = true;

            $args2 = [
                'name'=>Input::get('city'),
                'area' => Input::get('area')
            ];
            $city =  $this->requestApi('user.address.getbyname',$args2);
            if($city['code'] == 0){
                $defaultAddress['cityId'] = $city['data']['id'];
                $defaultAddress['cityName'] = $city['data']['name'];
                $defaultAddress['provinceId'] = $city['data']['province']['id'];
                $defaultAddress['provinceName'] = $city['data']['province']['name'];
                $defaultAddress['areaId'] = $city['data']['area']['id'];
                $defaultAddress['areaName'] = $city['data']['area']['name'];
                if($city['data']['isService'] == 0){
                    $data['code'] = 1;
                    die(json_encode($data));
                }

            }

            Session::put("defaultAddress", $defaultAddress);
            $index_data = $this->requestApi('indexnav.lists', $defaultAddress); 
            Session::put('indexnav', $index_data['data']);
            Session::put('address_info', null);
            Session::save(); 
        }else{
            $args2 = [
                'name'=>Input::get('city'),
                'area' => Input::get('area')
            ];
            $city =  $this->requestApi('user.address.getbyname',$args2);
            if($city['code'] == 0){
                $defaultAddress['cityId'] = $city['data']['id'];
                $defaultAddress['cityName'] = $city['data']['name'];
                $defaultAddress['provinceId'] = $city['data']['province']['id'];
                $defaultAddress['provinceName'] = $city['data']['province']['name'];
                $defaultAddress['areaId'] = $city['data']['area']['id'];
                $defaultAddress['areaName'] = $city['data']['area']['name'];
                $data['cityId'] = $city['data']['id'];
                if($city['data']['isService'] == 0){
                    $data['code'] = 1;
                    die(json_encode($data));
                }
            }
        }

        $data['code'] = 0;
        die(json_encode($data));
    }

    /**
     *地区搜索
     */
    public function  citysearch(){
        $args = Input::get();
        $city =  $this->requestApi('user.address.search',['keywords'=>$args['keywords']]);
        if($city['code'] == 0){
            $data['data'] = $city['data'];
        }
        $data['code'] = 0;
        die(json_encode($data));



    }

    /**
     * 城市定位
     * @return [type] [description]
     */
    public function location(){
        View::share('service_citys', $this->getServiceCitys());
        return $this->display();
    }

    public function here() {
        Session::put('orderData.here', 1);
    }

    private function formatAdv($advs) {
        foreach ($advs as $key => $value) { 
            switch ($value['type']) {
                case '1':
                    $advs[$key]['url'] = u('Goods/index',array('categoryId'=>$value['arg'])); 
                    break;
                case '2':
                    $advs[$key]['url'] = u('Goods/detail',array('goodsId'=>$value['arg'])); 
                    break;
                case '3':
                    $advs[$key]['url'] = u('Seller/detail',array('sellerId'=>$value['arg'])); 
                    break;
                case '4':
                    $advs[$key]['url'] = u('Article/detail',array('articleId'=>$value['arg']));
                    break; 
                case '5':
                    $advs[$key]['url'] = $value['arg']; 
                    break;
                case '6':
                    $advs[$key]['url'] = u('Reservation/index'); 
                    break;
                default:
                    $advs[$key]['url'] = u('Goods/index'); 
                    break;
            }
        }
        return $advs;
    }

    public function activity() {
        return $this->display();
    }

    /**
     * 显示推送第三方URL
     */
    public function iframe(){
        $args = Input::all();
        if(empty($args['url'])){
            Redirect::to(u('Index/index'))->send();
        }
        View::share('url',urldecode($args['url']));
        return $this->display();
    }

    /**
     * 选择地址
     */
    public function district(){
        //新加看看check
        $sellercheck = $this->requestApi('seller.check', ['id'=>$this->userId]);
        View::share('seller',$sellercheck['data']);
        $setSellerReg = Session::get('setSellerReg');
        if(!empty($setSellerReg) && (empty($sellercheck['data']) || $sellercheck['data']["isCheck"] != 1)){
            Session::set('setSellerReg',null);
            Redirect::to(u('Seller/reg'))->send();
        }

        $args = Input::all();

        if(!empty($args['address']) && !empty($args['location']) && empty($args['SetNoCity'])){
            $defaultAddress['address'] = Input::get('address');
            $defaultAddress['mapPointStr'] = Input::get('location');
            $defaultAddress['isSetCity'] = 0;
            $args['city'] = Input::get('city');
            $city =  $this->requestApi('user.address.getbyname',['name'=>$args['city']]);

            if($city['code'] == 0){
                $defaultAddress['cityId'] = $city['data']['id'];
                $index_data = $this->requestApi('indexnav.lists', $defaultAddress);
                View::share('indexnav', $index_data['data']);
                Session::put('indexnav', $index_data['data']);
            }
            Session::put("defaultAddress", $defaultAddress);
        }

        $defaultAddress = Session::get("defaultAddress");

        if(!empty($args['city'])){
            $cityinfo =  $this->requestApi('user.address.getbyname',['name'=>$args['city']]);
        }else{
            //如果是点其他城市进来的
            if($args['cityId'] > 0){
                $cityinfo =  $this->requestApi('user.address.getbyid',['cityId'=>$args['cityId']]);
            }else{
                //如果是有session进来的
                $args['location'] = $defaultAddress['mapPointStr'];
                $cityinfo =  $this->requestApi('user.address.getbyid',['cityId'=>$defaultAddress['cityId']]);
            }
        }

        $args['cityIds'] = $cityinfo['data']['id'];
        View::share('cityinfo', $cityinfo['data']);


        if (!isset($args['keywords']) && empty($args['keywords'])) {
            $list = $this->requestApi('district.getnearestlist', $args);
        } else {
            $list = $this->requestApi('district.searchvillages', ['keywords'=>$args['keywords'],'cityId'=>$args['cityId']]);
        }

        if ($list['code'] == 0) {
            View::share('list', $list['data']);
        }
        View::share("orderData", $defaultAddress);
//        print_r($args);exit;
        View::share("args", $args);

        return $this->display();
    }

    /**
     * special专题页
     */
    public function special(){
        $args = Input::all();
        $result = $this->requestApi('special.get', $args);

        $defaultAddress = Session::get("defaultAddress");
        if(empty($defaultAddress['cityId'])){
            if(!empty($args['address']) && !empty($args['mapPointStr'])){
                $defaultAddress['address'] = Input::get('address');
                $defaultAddress['mapPointStr'] = Input::get('mapPointStr');
                $defaultAddress['isSetCity'] = 0;
                $args2 = [
                    'name'=>Input::get('city'),
                    'area' => Input::get('area')
                ];
                $city =  $this->requestApi('user.address.getbyname',$args2);
                if($city['code'] == 0){
                    $defaultAddress['cityId'] = $city['data']['id'];
                    $defaultAddress['cityName'] = $city['data']['name'];
                    $defaultAddress['areaId'] = $city['data']['area']['id'];
                    $defaultAddress['areaName'] = $city['data']['area']['name'];
                }
                Session::put("defaultAddress", $defaultAddress);

            }
        }
        $args['mapPoint'] = $defaultAddress['mapPointStr'];
        $args['cityId'] = $defaultAddress['cityId'];

        View::share('args', $args);
        View::share('defaultAddress', $defaultAddress);

        if($result['code'] == 0 && $result['data']['status'] == 1){
            View::share('data', $result['data']);
        }

        return $this->display();
    }

    public function specialList(){
        $args = Input::all();
        $result = $this->requestApi('special.get', $args);

        $defaultAddress = Session::get("defaultAddress");
        if(empty($defaultAddress['cityId'])){
            return false;
        }
        $args['mapPoint'] = $defaultAddress['mapPointStr'];
        $args['cityId'] = $defaultAddress['cityId'];
        $args['type'] = $result['data']['type'];

        if($result['code'] == 0 && $result['data']['status'] == 1){
            if($result['data']['type'] == 4){
                $list = $this->requestApi('goods.typelists', $args);

                if($list['code'] == 0){
                    View::share('data', $list['data']);
                }
                return $this->display('special_lists_item');
            }else{
                $list = $this->requestApi('seller.typelists', $args);

                if($list['code'] == 0){
                    View::share('data', $list['data']);
                }

                return $this->display('seller_item','seller');
            }

        }

    }
}
