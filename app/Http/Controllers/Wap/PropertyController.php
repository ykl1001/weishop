<?php namespace YiZan\Http\Controllers\Wap;
use Illuminate\Support\Facades\Response;
use Input, View, Cache, Session,Request,Redirect;
/**
 * 物业
 */
class PropertyController extends UserAuthController {

    public function __construct() {
        parent::__construct();
        View::share('nav','forum');

        $districtId = Session::get('mydistrictId');
        if(!empty($districtId)){
            $backurl = u('Property/index',['districtId'=>$districtId]);
        }else{
            $backurl = u('Property/index',['id'=>1]);
        }
        View::share('backurl',$backurl);
    }

    public function index() {
        $args = Input::all();

        $defaultAddress = Session::get("defaultAddress");
        $args['mapPointStr'] = $defaultAddress['mapPointStr'];
        $args['cityId'] = $defaultAddress['cityId'];
        if(empty($args['mapPointStr'])){
            Redirect::to(u('Index/district'))->send();
        }

        $args['districtId'] = !empty($args['districtId']) ? $args['districtId'] : Session::get('mydistrictId');
        if($args['id'] > 0){//如果第一次点进来
            $args['districtId'] = 0;
        }
		
		if($args['districtId'] == 0){
			$districts = $this->requestApi('district.lists');
			foreach($districts['data'] as $k=>$v){
				if($v['status'] == 1){
					$args['districtId'] = $v['id'];
					break;
				}
			}
			
		}

        if($args['districtId'] > 0){
            Session::put('mydistrictId', $args['districtId']);

            $data = $this->requestApi('district.getdistrict', $args);
            $system = $data['data']['system'];
            $propertyId = $data['data']['id'];
            $isCheck = $data['data']['isCheck'];
            $isJoin = $data['data']['isJoin'];
            $isVerify = $data['data']['isVerify'];
            $isProperty = $data['data']['isProperty'];
			$isAccessStatus = $data['data']['accessStatus'];
            $data['data'] = $data['data']['district'];
            $data['data']['system'] = $system;
            $data['data']['accessStatus'] = $isAccessStatus;
            $data['data']['isCheck'] = $isCheck;
            $data['data']['isJoin'] = $isJoin;
            $data['data']['isVerify'] = $isVerify;
            $data['data']['isProperty'] = $isProperty;
            $data['data']['proprty']['id'] = $propertyId;
        }else{
            Session::put('mydistrictId', null);
            $data = $this->requestApi('district.nearby', $args);
        }

        if(!empty($data['data']['sellerId'])){
            $articlelist = $this->requestApi('article.lists', ['sellerId'=>$data['data']['sellerId']]);
        }else{
            $articlelist['data'] = '';
        }


        View::share('articlelist', $articlelist['data']);

        $app_opendoor_config = $this->requestApi('config.configByCode',['code' => 'app_opendoor']);
//        print_r($data['data']);exit;
        View::share('data', $data['data']);
        View::share('args', $args);
        View::share('user', $this->user);
        View::share('app_opendoor_config', $app_opendoor_config['data']);
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) {
            $url = u("Property/doors",['districtId'=>$data['data']['id']]);
        } else {
            $url = u("Unlock/index",['districtId'=>$data['data']['id']]);
        }

        //轮播图下菜单
        if(!Cache::has('index_menu_'.$defaultAddress['cityId'])) {
            $menu = $this->requestApi('Config.getmenu',['cityId'=>$defaultAddress['cityId']]);
            Cache::put('index_menu_'.$defaultAddress['cityId'], $menu, 1);
        } else {
            $menu = Cache::get('index_menu_'.$defaultAddress['cityId']);
        }
        View::share('menu', $menu['data']);

        View::share("orderData", $defaultAddress);

        /*首页推荐列表*/
        $type = $this->getConfig('buyer_index_show');
        View::share('type', $type);
        if($type == 1){
            //好货推荐
            $goods = $this->requestApi('config.getrecommendgoods');
            $data['data']['goods'] = $goods['data'];
            if($data['code'] == 0)
                View::share('data', $data['data']);
        }else{
            //推荐商家
            $sellers = $this->requestApi('config.getrecommendsellers', ['mapPoint'=>$data['data']['mapPointStr'],'cityId'=>$data['data']['cityId']]);
            $data['data']['sellers'] = $sellers['data'];
            if($data['code'] == 0)
                View::share('data', $data['data']);
        }
 
        View::share('is_url',$url);
        return $this->display();
    }

    /**
     * 业主详细
     */
    public function detail() {
        $args = Input::all();
        $data = $this->requestApi('district.getdistrict', $args);
        //print_r($data);

        View::share('data', $data['data']);
        View::share('args', $args);
        return $this->display();
    }

    /**
     * 社区公告
     */
    public function article() {
        $args = Input::all();
        $data = $this->requestApi('district.getdistrict', $args);
//        print_r($data);exit;
        $list = $this->requestApi('article.lists', ['sellerId'=>$data['data']['sellerId']]);
        View::share('list', $list['data']);
        View::share('args', $args);
        return $this->display();
    }

    public function articledetail() {
        $data = $this->requestApi('article.get', ['id'=>Input::get('id')]);
        //print_r($data);
        View::share('data', $data['data']);

        $result = $this->requestApi('article.read', ['id'=>Input::get('id')]);
        return $this->display();
    }


    public function brief() {
        $args = Input::all();
        $data = $this->requestApi('property.detail', $args);
        View::share('data', $data['data']);

        return $this->display();
    }

    public function applyaccess() {
        $args = Input::all();

        $result = $this->requestApi('user.applyaccess',$args);
        return Response::json($result);
    }

    public function livipayment(){
        return $this->display();
    }

    public function livelog(){
        $args = Input::all();
        $list = $this->requestApi('Live.lists',$args);
        View::share('args', $args);
        View::share('list', $list['data']);

        return $this->display();
    }

    public function typepay(){
        $args = Input::all();

        $defaultAddress = $args;
        unset($defaultAddress['type']);
        if(empty($defaultAddress)){
            $defaultAddress = Session::get("defaultAddress");
        }

        $isservice =  $this->requestApi('user.address.getisservice',['cityId'=>$defaultAddress['cityId']]);

        if($isservice['data'] == 1){
            $city = $this->requestApi('user.address.getbyid',['cityId'=>$defaultAddress['cityId']]);
            $city['data']['typepay'] = $args['type'];

            //获取缴费单位
            $company = $this->requestApi('live.getcompany',$city['data']);
            View::share('city', $city['data']);
            View::share('company', $company['data']);
        }else{
            View::share('company', '');
        }

        View::share('isservice', $isservice['data']);
        View::share('args', $args);
        return $this->display();
    }

    public function arrearage(){
        $args = Input::all();
        //获取欠费
        $arrearage = $this->requestApi('live.arrearage',$args);
        //var_dump($arrearage);exit;
        $args['balance'] = $arrearage['data']['Data']['Balances']['Balance'][0];
        $args2 = json_encode($args,true);
        $args2 = base64_encode($args2);
        View::share('arrearage', $arrearage['data']);
        View::share('args', $args);
        View::share('args2', $args2);

        return $this->display();
    }

    public function query(){
        $args = Input::all();
        $result = $this->requestApi('live.query',$args);
        return Response::json($result);
    }

    /**
     * 门禁钥匙
     */
    public function getdoorkeys() {
        $args = Input::all();
        $result = $this->requestApi('user.getdoorkeys', $args);
        return Response::json($result);

    }
    /**
     * 用户小区门禁列表
     */
    public function doors() {
        $args = Input::all();
        //小区
        $data = $this->requestApi('district.getdistrict', $args);
        View::share('data', $data['data']);
        View::share('args', $args);
        View::share('user', $this->user);
        //门禁
        $args['villagesid'] = $args['districtId'];
        $result = $this->requestApi('user.getdoorkeys', $args);
        View::share('list',$result['data']);

        return $this->display();

    }
    /*
     * 摇一摇开关
     * **/
    public function shakeswitch(){//shequwy
        $args = Input::all();
        //$shakeswitch = $args['status']=='on'?1:0;
        $result = $this->requestApi('property.shakeswitch',$args);
        return Response::json($result);
    }
}