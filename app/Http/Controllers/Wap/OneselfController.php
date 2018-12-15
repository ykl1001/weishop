<?php namespace YiZan\Http\Controllers\Wap;

use YiZan\Utils\Time;
use View, Input, Lang, Route, Page ,Session,Log,Redirect;
/**
 * 自营首页
 */
class OneselfController extends BaseController {

    //
    public function __construct() {
        parent::__construct();
        View::share('nav','index');
    }
    /**
     * 首页信息 
     */
    public function index() {
        $defaultAddress = Session::get("defaultAddress");
        if (!$defaultAddress) {
            $address = $this->requestApi('user.address.getdefault');
            $defaultAddress = $address['data'];
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
            $args['city'] = Input::get('city');
            $city =  $this->requestApi('user.address.getbyname',['name'=>$args['city']]);

            if($city['code'] == 0){
                $defaultAddress['cityId'] = $city['data']['id'];
                $index_data = self::requestApi('indexnav.lists', $defaultAddress); 
                View::share('indexnav', $index_data['data']);
                Cache::put('indexnav', $index_data['data']);
            }
            Session::put("defaultAddress", $defaultAddress);
        }

        $is_service = [];
        $is_service['data'] = 1;

        if(!empty($defaultAddress)){
            //判断该城市是否开通
            $is_service =  $this->requestApi('user.address.getisservice',['cityId'=>$defaultAddress['cityId']]);
        }
        View::share("cityIsService", $is_service['data']);

        //首页轮播图信息 + 菜单 + 活动
        $data = $this->requestApi('config.index', ['mapPoint'=>$defaultAddress['mapPointStr'],'cityId'=>$defaultAddress['cityId'],'codeType'=>'BUYER_SYSTEM_ONESELF']);
//        print_r($data);die;
        if($data['code'] == 0)
            View::share('data', $data['data']);
        View::share("orderData", $defaultAddress);

        $menu = $this->requestApi('Config.getmenu',['cityId'=>$defaultAddress['cityId'],'platformType'=>'oneself']);
        View::share('menu', $menu['data']);

        return $this->display();
    }


    /**
     * 搜索
     */
    public function search(){
        $keyword = Input::get('keyword');
        $search_type = 'goods';
        $type = Input::get('type')=='a'?'a':'';
        $searchs = array();
        if (Session::get('searchs')) {
            $searchs = Session::get('searchs');
        }
        if (!empty($keyword) && !in_array($keyword, $searchs)) {
            array_unshift($searchs, $keyword);
            $searchs = array_slice($searchs, 0,5);//显示5条历史
            Session::set('searchs', $searchs);
            Session::save();
        }

        if (empty($keyword)){
            Session::set('search_times',0);
            Session::save();
            $history_search = Session::get('searchs');
            View::share('history_search',$history_search);
            $hot_data = $this->requestApi('Hotwords.lists',['cityId'=>intval(Session::get('defaultAddress.cityId')),'pageSize'=>12]);
            if($hot_data['code'] == 0)
                View::share('hot_data',$hot_data['data']);
        }

        $option = Input::all();
        if(empty($option['mapPoint'])){
            $option['mapPoint'] = preg_replace('/[,\s+]/', ' ', Session::get('defaultAddress.mapPointStr'));
            $option['cityId'] = intval(Session::get('defaultAddress.cityId'));
        }
        $option['pageSize'] = 6;
        View::share('option',$option);
        if (Input::get('keyword')) {
            View::share('keyword',$keyword);
            /*搜索商品*/
            $option['vsType'] = "oneself";
            $goods_data = $this->requestApi('seller.goodslists',$option);
            View::share('data',$goods_data['data']['goodslist']);
            View::share('seller_count',$goods_data['data']['seller_total']);
            View::share('goods_count',$goods_data['data']['goods_total']);
            return $this->display("searchresult_goods");
        } else {
            return $this->display();
        }
    }
}
