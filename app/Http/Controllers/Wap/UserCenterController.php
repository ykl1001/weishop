<?php 
namespace YiZan\Http\Controllers\Wap;
use Input, View, Session, Redirect, Request, Time, Response, Cache, YiZan\Utils\String, Config;

/**
 * 用户中心控制器
 */
class UserCenterController extends UserAuthController {
    public function __construct() 
    {
        parent::__construct();
    }
    /**
     * 用户中心首页
     */
    public function index() {
        View::share('user',$this->user);
        $balance_result = $this->requestApi('user.balance');
        View::share('nav','mine');

        View::share('balance', $balance_result['data']['balance']);
        $result = $this->requestApi('seller.check', ['id'=>$this->userId]);

        //是否是商家注册
        $setSellerReg = Session::get('setSellerReg');
        if(!empty($setSellerReg) && (empty($result['data']) || $result['data']["isCheck"] != 1)){
            Session::set('setSellerReg',null);
            Redirect::to(u('Seller/reg'))->send();
        }
        $setForum = Session::get('setForum');
        if(!empty($setForum)){
            Session::set('setForum',null);
            Redirect::to(u('Forum/detail',['id'=>$setForum]))->send();
        }

        View::share('seller',$result['data']);
        View::share('title',"- 用户中心");

        if($result['code'] == 0){
            View::share('share_active',$result['data']);

            if(!empty($result['data'])){
                $desc = $result['data']['brief'];
                View::share('desc',$desc);

                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                $url = $protocol.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];

                $weixin_arrs = $this->requestApi('Useractive.getweixin',array('url' => $url));  //此代码导致index页面加载过慢

                if($weixin_arrs['code'] == 0){
                    View::share('weixin',$weixin_arrs['data']);
                }

                $link_url = u('UserCenter/obtaincoupon');
                View::share('link_url',$link_url);
            }
        }

        $district = $this->requestApi('district.lists');
        View::share('district', $district['data']);

        $integral = $this->requestApi('user.userinfo');
        View::share('integral', $integral['data']['integral']);

        //分享返现
        $invitation = $this->requestApi('invitation.get',['id'=>1]);
        if($invitation['code'] == 0)
        {
            View::share('invitation', $invitation['data']);
        }
        $totalnum = $this->requestApi('order.totalnum');
        View::share('totalnum', $totalnum['data']);

        Session::put('reg_info',"");
        Session::save();

        //佣金
        $user = $this->user;
        if(FANWEFX_SYSTEM && $this->userId){
            $userbyfanweid = $this->requestApi('user.fanwe_id',['userId'=>$this->userId]);
            $user['fanweId'] = $userbyfanweid['data']['fanweId'];
        }
        View::share('user',$user);

        if(!empty($user['fanweId']) && FANWEFX_SYSTEM){
            $args_data['user_id'] = $user['fanweId'];
            $fx_userinfo = $this->requestApi('fx.api', ['path'=>'get_user_info', 'args'=>$args_data]);
            View::share('fx_userinfo', $fx_userinfo['data']);
        } else if($user['fanweId'] == 0 && FANWEFX_SYSTEM) {
            $fxInfo = $this->requestApi('user.getsharechapman');
            View::share('fxInfo', $fxInfo['data']);
        }

        //cz
        $config = $this->getConfig();
        $wap_promotion = $config['wap_promotion'];
        $wap_integral = $config['wap_integral'];
        View::share('wap_promotion', $wap_promotion);
        View::share('wap_integral', $wap_integral);

        return $this->display();
    }
    /**
     * 用户信息
     */
    public function info() {
        $payPwd = $this->requestApi('user.userinfo');
        View::share('isPayPwd', $payPwd['data']['isPayPwd']);
        View::share('user',$this->user);
        View::share('title',"- 用户信息");
        return $this->display();
    }
    /**
     * 更改用户信息
     */
    public function updateinfo() {
        $name = trim(Input::get('name'));
        $avatar = trim(Input::get('avatar'));
        $result = $this->requestApi('user.info.update',array('name'=>$name,'avatar'=>$avatar));
        if ($result['code'] == 0){
            //cz修改
            if(FANWEFX_SYSTEM && !empty($this->user['fanweId'])){
                $args_data['user_id'] = $this->user['fanweId'];
                $args_data['user_nickname'] = $name;
                $args_data['user_photo'] = $avatar;
                $fx_userinfo = $this->requestApi('fx.api', ['path'=>'modify_user', 'args'=>$args_data]);
            }

            $this->setUser($result['data']);
        }
        return Response::json($result);
    }
    /**
     * 修改昵称
     */
    public function nick(){
        View::share('user',$this->user);
        return $this->display();
    }
    
    /**
     * 分享奖励
     */
    public function share() {
        View::share('title',"- 分享");
        return $this->display();
    }
    
    /**
     * 优惠券领取
     */
    public function recoupon() {
        $id = Input::get('id');
        $result = $this->requestApi('user.promotion.receive',array('id'=>$id));
        die(json_encode($result));
            
    }


    /**
     * 我的收藏
     */
    public function collect() {
        return $this->collectList('collect');
    }

    public function collectList($tpl='collect_item') {
        $args = Input::all();
        if (!isset($args['type'])) {
            $args['type'] = 1;
        }
        $list = $this->requestApi('collect.lists',$args);

        View::share('args',$args);
        View::share('title',"- 收藏");
        if($list['code'] == 0)
            View::share('list',$list['data']);

        return $this->display($tpl);

    }

    /**
     * 取消店铺服务收藏
     */
    public function delcollect() {
        $args = Input::all();
        if (!isset($args['type'])) {
            $args['type'] == 1;
        }
        $result = $this->requestApi('collect.delete',$args);
        return Response::json($result);
    }   

    //添加收藏
    public function addcollect() {
        $args = Input::all();
        if (!isset($args['type'])) {
            $args['type'] == 1;
        }
        $result = $this->requestApi('collect.create',$args);
        return Response::json($result);
    }

    /**
     * 我的常用地址
     */
    public function address() {
        $userinfo = Session::get('user');
        if(empty($userinfo['mobile'])){
            View::share('return_url', u('GoodsCart/index'));
            return $this->display('bindmobile','order');
        }

        return $this->addressList('address');
    }

    public function addressList($tpl='address_item') {
        $args = Input::all();
        $list = $this->requestApi('user.address.lists',$args);
        View::share('list',$list['data']);
        View::share('args',$args);
        View::share('title',"- 常用地址");
        if($args['cartIds']){
            if($args['cartIds'] == 10)
            {
                View::share('nav_back_url', u('Order/order', ['addressId'=>$args['addressId'],'cartIds'=>$args['arg']]));
            }
            elseif($args['cartIds'] == 2 || $args['cartIds'] == 1)
            {
                View::share('nav_back_url', u('GoodsCart/index', ['cartIds'=>$args['cartIds']]));
            }
        }elseif((int)$args['goodsId'] > 0) {
            View::share('nav_back_url', u('Order/integralorder', ['goodsId'=>$args['goodsId']]));
        }else if((int)Input::get('plateId') > 0){
            $plateId = Input::get('plateId');
            $postId = Input::get('postId');
            $plateId = $plateId == "" ? Session::get('plateId') : $plateId;
            $postId = $postId == "" ? Session::get('postId') : $postId;
            $arg['plateId'] = $plateId;
            $arg['postId'] = $postId;
            $arg['postId'] = $postId;
            $nav_back_url = u('Forum/addbbs',$arg);

            Session::set('plateId',$plateId);
            Session::set('postId',$postId);
            View::share('nav_back_url', $nav_back_url);
        }else {
            if($args['change'] == 2){
                View::share('nav_back_url', u('Index/addressmap'));
            }else{
                View::share('nav_back_url', u('UserCenter/index'));
            }
        }
        Session::set('address_info', null);
        Session::save();

        return $this->display($tpl);
    }

    /**
     * 常用地址详情
     */
    public function addressdetail() {
        $defaultAddress = Input::all();
        if(empty($defaultAddress['cityId'])){
            $defaultAddress = Session::get("defaultAddress");
        }
        View::share('defaultAddress', $defaultAddress);

        if ((int)Input::get('id') > 0) {
            $data = $this->requestApi('user.address.get', ['id' => (int)Input::get('id')]);

            $address_info = Session::get('address_info');
            if(!empty($address_info) && $address_info['id'] == Input::get('id')){
                //cz 如果有id选择了其他的用session的
                if(!empty($address_info['provinceName'])){
                    $address_info['pca_name'] = $address_info['provinceName']." ".$address_info['cityName']." ".$address_info['areaName'];
                }else if(!empty($address_info['cityName'])){
                    $address_info['pca_name'] = $address_info['cityName']." ".$address_info['areaName'];
                }
                $address_info['address'] = $address_info['detailAddress'];
                $data['data'] = $address_info;
            }else{
                $data['data']['mapPoint'] = $data['mapPointStr'];
                if(!empty($data['data']['province'])){
                    $data['data']['pca_name'] = $data['data']['province']['name']." ".$data['data']['city']['name']." ".$data['data']['area']['name'];
                }else if(!empty($data['data']['city'])){
                    $data['data']['pca_name'] = $data['data']['city']['name']." ".$data['data']['area']['name'];
                }
                $data['data']['address'] = $data['data']['detailAddress'];
            }

            View::share('data', $data['data']);
            View::share('title',"- 编辑地址");
        }else{
            $address_info = Session::get('address_info');
            if(empty($address_info)){
                //如果没有
                //unset($defaultAddress['mobile'],$defaultAddress['name'],$defaultAddress['addres']);
				
				if($defaultAddress['mobile']){
					unset($defaultAddress['mobile']);
				}
				
				if($defaultAddress['name']){
					unset($defaultAddress['name']);
				}
				
				if($defaultAddress['addres']){
					unset($defaultAddress['addres']);
				}
                $address_info = $defaultAddress;
                $address_info['mapPoint'] = $defaultAddress['mapPointStr'];
            }
            if(!empty($address_info['provinceName'])){
                $address_info['pca_name'] = $address_info['provinceName']." ".$address_info['cityName']." ".$address_info['areaName'];
            }else if(!empty($address_info['cityName'])){
                $address_info['pca_name'] = $address_info['cityName']." ".$address_info['areaName'];
            }
            $address_info['address'] = $address_info['detailAddress'];
            View::share('data', $address_info);
            View::share('title',"- 添加地址");
        }

        if((int)Input::get('change') == 2){
            $nav_back_url = u('UserCenter/address',['change'=>2,'SetNoCity'=>Input::get('SetNoCity')]);
            View::share('nav_back_url', $nav_back_url);
        }else{
            $cartIds = Input::get('arg');
            $cartIds = $cartIds == "" ? Session::get('cartIds') : $cartIds;
            if(!empty($cartIds)){
                Session::set('cartIds',$cartIds);
                $arg['cartIds'] = $cartIds;
                $nav_back_url = u('Order/order',$arg);
            }else{
                $nav_back_url = u('UserCenter/address',['SetNoCity'=>Input::get('SetNoCity')]);
            }
            View::share('arg', $cartIds);
            View::share('nav_back_url', $nav_back_url);
        }

        return $this->display();    
    }
    /**
     * 确定订单时新增地址
     */
    public function addresssdetail() {
        $address_info = Session::get('address_info');
        if ((int)Input::get('id') > 0) {
            $data = $this->requestApi('user.address.get', ['id' => (int)Input::get('id')]);
            if ($address_info) {
                $data['data']['detailAddress'] = $address_info['detailAddress'];
                $data['data']['mapPoint'] = $address_info['mapPoint'];
            }
            View::share('data', $data['data']);
            View::share('title',"- 编辑地址");
        }else{
            View::share('data', $address_info);
            View::share('title',"- 添加地址");
        }
        $nav_back_url = u('UserCenter/address',['change'=>1]);
        $noshow = 1;
        View::share('noshow',$noshow);

        View::share('nav_back_url',$nav_back_url);
        return $this->display();
    }

    /**
     * 选择地址
     */
    public function addressmap() {
        $defaultAddress = Session::get("defaultAddress");
        //当有城市传入,查询传入的城市
        $cityId = (int)Input::get('cityId');
        $defaultAddress['cityId'] = $cityId > 0 ? $cityId : $defaultAddress['cityId'];
        $cityinfo =  $this->requestApi('user.address.getbyid',['cityId'=>$defaultAddress['cityId']]);

        View::share('cityinfo', $cityinfo['data']);
		
        if ((int)Input::get('id') > 0) {
            $data = $this->requestApi('user.address.get', ['id' => (int)Input::get('id')]);
            View::share('data', $data['data']);
        }else{
            $defaultAddress = Input::all();
            if(empty($defaultAddress['cityId'])){
                $defaultAddress = Session::get("defaultAddress");
                $defaultAddress['change'] = Input::get('change');
            }
            $data['data']['mapPointStr'] = $defaultAddress['mapPointStr'];
            View::share('defaultAddress', $defaultAddress);
            View::share('data', $data['data']);
        }

        return $this->display();
    }

    public function saveMap() {
        $address_info = Input::get();

        $args = [
            'name'=>Input::get('city'),
            'area' => Input::get('area')
        ];
        $city =  $this->requestApi('user.address.getbyname',$args);
        if($city['code'] == 0){
            $address_info['cityId'] = $city['data']['id'];
            $address_info['cityName'] = $city['data']['name'];
            $address_info['provinceId'] = $city['data']['province']['id'];
            $address_info['provinceName'] = $city['data']['province']['name'];
            $address_info['areaId'] = $city['data']['area']['id'];
            $address_info['areaName'] = $city['data']['area']['name'];
            if($city['data']['isService'] == 0){
                $data['code'] = 1;
                $data['data'] = $city['data'];
                die(json_encode($data));
            }
        }

        if (Session::has('address_info')){
            $address_info2 = Session::get('address_info');
            $address_info['id'] = $address_info2['id'];
            $address_info['name'] = $address_info2['name'];
            $address_info['mobile'] = $address_info2['mobile'];
            $address_info['doorplate'] = $address_info2['doorplate'];
            $address_info['SetNoCity'] = $address_info2['SetNoCity'];
        }

        Session::put('address_info', $address_info);
        Session::save();

        return $this->success('成功','',$city['data']);
    }

    public function saveAddrData() {
        $address_info = Input::all();
        $args = [
            'name'=>Input::get('city'),
            'area' => Input::get('area')
        ];
        $city =  $this->requestApi('user.address.getbyname',$args);
        if($city['code'] == 0){
            $address_info['cityId'] = $city['data']['id'];
            $address_info['cityName'] = $city['data']['name'];
            $address_info['provinceId'] = $city['data']['province']['id'];
            $address_info['provinceName'] = $city['data']['province']['name'];
            $address_info['areaId'] = $city['data']['area']['id'];
            $address_info['areaName'] = $city['data']['area']['name'];
        }

        Session::put('address_info', $address_info);
        Session::save();
        return $this->success('成功');
    }

    /**
     * 搜索地址
     */
    public function addrsearch() {
        $defaultAddress = Session::get("defaultAddress");
		//当有城市传入,查询传入的城市
		$cityId = (int)Input::get('cityId');
		$defaultAddress['cityId'] = $cityId > 0 ? $cityId : $defaultAddress['cityId'];
        $cityinfo =  $this->requestApi('user.address.getbyid',['cityId'=>$defaultAddress['cityId']]);
        View::share('cityinfo', $cityinfo['data']);

        if ((int)Input::get('id') > 0) {
            $data = $this->requestApi('user.address.get', ['id' => (int)Input::get('id')]);
            View::share('data', $data['data']);
        }else{
            $defaultAddress = Input::all();
            if(empty($defaultAddress)){
                $defaultAddress = Session::get("defaultAddress");
            }
            $data['data']['mapPointStr'] = $defaultAddress['mapPointStr'];

            View::share('defaultAddress', $defaultAddress);
            View::share('data', $data['data']);
        }

        return $this->display();    
    }

    /**
     * 获取城市数据
     */
    public function getcity() {
        $data = $this->requestApi('app.init');
        die(json_encode($data['data']['province']));
    }

    /**
     * 常用地址操作
     */
    public function saveaddress() {
        $data = Input::all();

        $result = $this->requestApi('user.address.create',$data);
        Session::set('address_info', null);
        Session::save();

        if($data['SetNoCity'] != 1){
            $defaultAddress['address'] = $result['data']['address'];
            $defaultAddress['mapPointStr'] = $result['data']['mapPointStr'];
            $defaultAddress['cityId'] = $result['data']['cityId'];
            Session::put('defaultAddress', $defaultAddress);
            $index_data = $this->requestApi('indexnav.lists', $defaultAddress);  
            Session::put('indexnav', $index_data['data']);
            Session::save();
        }

        die(json_encode($result));
    }
    /**
     * 删除地址
     */
    public function deladdress() {
        $id = (int)Input::get('id');
        $result = $this->requestApi('user.address.delete',array('id'=>$id));
        die(json_encode($result));
            
    }

    /**
     * 设置默认地址
     */
    public function setdefault() {
        $id = (int)Input::get('id');
        $result = $this->requestApi('user.address.setdefault',array('id'=>$id));

        if ((int)Input::get('change') == 1 || (int)Input::get('change') == 2) {
            $defaultAddress['address'] = $result['data']['address'];
            $defaultAddress['name'] = $result['data']['name']; 
            $defaultAddress['realAddress'] = $result['data']['city']['name'].$result['data']['address'];
            $defaultAddress['mapPointStr'] = $result['data']['mapPointStr'];
            $defaultAddress['cityId'] = $result['data']['cityId'];
            $defaultAddress['id'] = $result['data']['id'];
            $defaultAddress['isIndexSetAddress'] = true;
            $index_data = $this->requestApi('indexnav.lists', $defaultAddress);  
            Session::put('indexnav', $index_data['data']);
            Session::put('defaultAddress',$defaultAddress);
            Session::save();
        }
        die(json_encode($result));  
    }

    /**
     * 设置默认地址
     */
    public function setdefault2() {
        $id = (int)Input::get('id');
        $result = $this->requestApi('user.address.get',['id'=>$id]);

        $defaultAddress['address'] = $result['data']['detailAddress'];
        $defaultAddress['name'] = $result['data']['name']; 
        $defaultAddress['realAddress'] = $result['data']['city']['name'].$result['data']['address'];
        $defaultAddress['mapPointStr'] = $result['data']['mapPointStr'];
        $defaultAddress['cityId'] = $result['data']['city']['id'];

        //cz
        $defaultAddress['cityName'] = $result['data']['city']['name'];
        $defaultAddress['provinceId'] = $result['data']['province']['id'];
        $defaultAddress['provinceName'] = $result['data']['province']['name'];
        $defaultAddress['areaId'] = $result['data']['area']['id'];
        $defaultAddress['areaName'] = $result['data']['area']['name'];

        $defaultAddress['id'] = $result['data']['id'];
        $defaultAddress['isIndexSetAddress'] = true;
        $defaultAddress['mobile'] = $result['data']['mobile'];
        Session::put('defaultAddress',$defaultAddress);
        $index_data = $this->requestApi('indexnav.lists', $defaultAddress);  
        Session::put('indexnav', $index_data['data']);
        $cityAddress['cityId'] = $result['data']['city']['id']; 
        Session::put("cityAddress", $cityAddress); 
        Session::save();
        die(json_encode($result));
    }

    /**
     * 我的消息
     */
    public function message() {
        return $this->messageList('message');
    }

    public function messageList($tpl='message_item') {
        $args = Input::all();
        $list = $this->requestApi('msg.lists',['page' => $args['page']]);
        if($list['code'] == 0)
            View::share('list',$list['data']);
        View::share('args',$args);

        if(!Input::ajax()){
            View::share('nav','message');
            View::share('title',"- 消息");
        }
            
        return $this->display($tpl);    
    }
    /**
     * 我的消息
     */
    public function msgshow() {
        return $this->msgshowList('msgshow');
    }

    public function msgshowList($tpl='msgshow_item') {
        $args = Input::get();
        $list = $this->requestApi('msg.getdata',['sellerId'=>$args['sellerId'],'page' => $args['page']]);
//        print_r($list['data']);exit;
        if($list['code'] == 0){
            View::share('data',$list['data']);
        }
        View::share('args',$args);
        if(!Input::ajax()){
            View::share('msgshow','yes');
            View::share('nav','message');
            View::share('title',"- 消息内容");
        }
        return $this->display($tpl);
    }

    /**
     * 我的消息
     */
    public function team() {;
        return $this->teamList('team');
    }

    public function teamList($tpl='team_item') {
        $args = Input::get();
        $list = $this->requestApi('msg.getdata',['team'=>1,'sellerId'=>$args['sellerId'],'page' => $args['page']]);

        if($list['code'] == 0){
            View::share('data',$list['data']);
        }
        View::share('args',$args);
        if(!Input::ajax()){
            View::share('msgshow','yes');
            View::share('nav','message');
            View::share('title',"- 团队消息内容");
        }
        return $this->display($tpl);
    }

    /**
     * 我的消息
     */
    public function wealth() {
        return $this->wealthList('wealth');
    }

    public function wealthList($tpl='wealth_item') {
        $args = Input::get();
        $list = $this->requestApi('msg.wealth',['team'=>2,'sellerId'=>$args['sellerId'],'page' => $args['page']]);
        if($list['code'] == 0){
            View::share('data',$list['data']);
        }
        View::share('args',$args);
        if(!Input::ajax()){
            View::share('msgshow','yes');
            View::share('nav','message');
            View::share('title',"- 财富消息内容");
        }
        return $this->display($tpl);
    }

    /**
     * 删除我的消息
     */
    public function delmessage() {  
        $id = Input::get('id');
        $result = $this->requestApi('msg.delete',array('id'=>$id));
        die(json_encode($result));
    }
    
    /**
     * [readmsg 读消息]
     * @return [type] [description]
     */
    public function readmsg() {
        $messageid = Input::get('mid');
        $result = $this->requestApi('msg.read',array('id' => $messageid));
        die(json_encode($result));
    }
    /**
     * 意见反馈
     */
    public function feedback() {
        View::share('title','- 意见反馈');
        return $this->display();
    }

    /**
     * 增加意见反馈
     */
    public function addfeedback() {
        $content = strip_tags(Input::get('content'));
        $result = $this->requestApi('feedback.create',array('content'=>$content,'deviceType'=>'wap'));
        die(json_encode($result));
    }

    /**
     * 用户登出
     */
    public function logout() {
        Session::set('user','');
        Session::set('reservation_data','');
        Session::set('orderData', '');
        Session::put('userAddInfo.doorplate','');
        Session::put('userAddInfo.name','');
        Session::put('userAddInfo.mobile','');
        Session::put('wxlogin_userinfo','');
        Session::put('reg_info','');
        Session::put('cate_info','');
        Session::put('defaultAddress', '');
        Session::save();
        $this->setSecurityToken(null);
        return Redirect::to(u('UserCenter/index'));
    } 

    /**
     * 用户下单
     */
    public function order(){
        $option = Input::all();  
        if (!isset($option['duration'])) {
            $option['duration'] = 0;
        } else {
            $option['duration'] = $option['duration'] * 3600;
        }
        $result = $this->requestApi('order.create',$option);    
        echo json_encode($result);
        exit;
        if(Input::ajax()){
            $result = $this->requestApi('order.create',$option);    
            echo json_encode($result);
        } else {
            $result = $this->requestApi('order.detail',$option);
            if($result['code'] == 0){
                View::share('order',$result['data']);
                return $this->display();
            } else {
                $this->error($result['msg']);
            }
        }
        
    }

    /*
    * 举报历史
    */
    public function report() {
        $list = $this->requestApi('Ordercomplain.lists');  
        //var_dump($list);
        if($list['code'] == 0)
            View::share('list',$list['data']);
        return $this->display();
    } 

    /*
    * 举报详情
    */
    public function reportdetail() {
        $id = (int)Input::get('id');
        $result = $this->requestApi('Ordercomplain.get',array('complainId'=>$id));
        if($result['code'] == 0)
            $data = $result['data'];
            $data['image'] = explode(',', $data['images']);
            View::share('data',$data);
        return $this->display();
    } 

    /*
    * app举报详情
    */
    public function appreportdetail() {
        $id = (int)Input::get('id');
        $result = $this->requestApi('Ordercomplain.get',array('complainId'=>$id));
        if($result['code'] == 0){
            $data = $result['data'];
            $data['image'] = explode(',', $data['images']);
            View::share('data',$data);
            View::share('is_show_top',false);
        }
        return $this->display();
    }

    /**
     * 我的电话
     */
    public function mobile() {
        $list = $this->requestApi('user.mobile.lists');
        View::share('list', $list['data']);
        View::share('title', '- 我的电话');
        View::share('nav_back_url',u('UserCenter/index'));
        return $this->display();
    }
    /**
     * 确认订单时选择电话
     */
    public function mobiles() {
        $list = $this->requestApi('user.mobile.lists');
        View::share('list', $list['data']);
        View::share('title', '- 我的电话');
        Session::put("url",u('Order/createmoreinfo') );
        Session::save();
        View::share('nav_back_url',Session::get('url'));
        return $this->display();
    }

    /**
     * 删除地址
     */
    public function delmobile() {
        $id = (int)Input::get('id');
        $result = $this->requestApi('user.mobile.delete',array('mobileId'=>$id));
        return Response::json($result);
    }

    /**
     * 设置默认地址
     */
    public function setdefaultmobile() {
        $id = (int)Input::get('id');
        $result = $this->requestApi('user.mobile.setdefault',array('mobileId'=>$id));
        return Response::json($result);
    }

    /**
     * 电话详情
     */
    public function mobiledetail() {
        View::share('title', '- 新增电话');
        return $this->display();
    }
    /**
     * 确定订单时新增电话
     */
    public function mobilesdetail() {
        View::share('title', '- 新增电话');
        return $this->display();
    }
    /**
     * 保存电话
     */
    public function savemobile() {
        $data = Input::get();
        $result = $this->requestApi('user.mobile.create', $data);
        return Response::json($result);
    }

    /**
     * 修改密码
     */
    public function updatepwd() {
        View::share('user', $this->user);
        View::share('title', '- 修改密码');
        return $this->display();
    }

    /**
     * 更新密码
     */
    public function savepwd() {
        $args = Input::get();
        unset($args['pwds']);
        $result = $this->requestApi('user.repwd', $args);
        if ($result['code'] == 0) {
            Session::set('user','');
            Session::set('reservation_data','');
            $this->setSecurityToken(null);
        }
        return Response::json($result);
    }


    /**
     * 生成验证码
     */
    public function verify() {
        $args = Input::get();
        $result = $this->requestApi('user.mobileverify', $args);
        die(json_encode($result));
    }


    /**
     * 关于我们
     */
    public function aboutus() {
        $aboutus = $this->getConfig('aboutus');
        View::share('aboutus', $aboutus);
        return $this->display();
    }

    public function userhelp() {
        $isFx = Input::get('isFx');
        if(!$isFx){
            $userhelp = $this->getConfig('wap_order_notice');
            View::share('userhelp', $userhelp);
            View::share('title',"新手帮助");
        }else{
            //生成二维码
            $userc = $this->requestApi('invitation.get');
            if($this->user['isPay'] == 1 && IS_OPEN_FX && in_array($userc['data']['userStatus'] ,[1,2])){
                return Redirect::to(u('Invitation/index'))->send();
            }
            $data  =[
                'purchaseAgreement'=>  $userc['data']['purchaseAgreement'],
                'privilegeDetails'=>  $userc['data']['privilegeDetails'],
                'protocolFee'=>  $userc['data']['protocolFee'],
            ];
            if($userc['code'] == 0){
                View::share('userc',$data);
            }
            if($isFx == 1)
            View::share('title',"分销资质购买");
            else
            View::share('title',"购买协议");
        }
        View::share('isFx',$isFx);
        return $this->display();
    }

    /**
     * 邀请好友
     */
    public function invite() {
        View::share('title', '- 邀请好友');
        return $this->display();
    }

    public function config() {
        View::share('title', '- 设置');
        return $this->display();
    }

    /**
     * 换绑手机号(验证当前手机)
     */
    public function verifymobile() {
        View::share('user',$this->user);
        return $this->display();
    }

    /**
     * 换绑手机号(验证当前手机)
     */
    public function doverifymobile() {
        $args = [
            'mobile' => Input::get('mobile'),
            'verifyCode' => Input::get('code')
        ];
        $result = $this->requestApi('user.info.verifymobile',$args);
        return Response::json($result);
    }


    /**
     * 换绑手机号(验证新手机)
     */
    public function changemobile() {
        if ($_SERVER['HTTP_REFERER'] != u('UserCenter/verifymobile')) {
            $this->error('请先验证手机号码');
        }
        return $this->display();
    }

    /**
     * 换绑手机号(验证新手机)
     */
    public function dochangemobile() {
        $args = [
            'oldMobile' => $this->user['mobile'],
            'mobile' => Input::get('mobile'),
            'verifyCode' => Input::get('code')
        ];
        $result = $this->requestApi('user.updatemobile',$args);
        if ($result['code'] == 0) {
            //cz修改
            if(FANWEFX_SYSTEM && !empty($this->user['fanweId'])){
                $args_data['user_id'] = $this->user['fanweId'];
                $args_data['user_username'] = Input::get('mobile');
                $args_data['user_mobile'] = Input::get('mobile');
                $fx_userinfo = $this->requestApi('fx.api', ['path'=>'modify_user', 'args'=>$args_data]);
            }

            $this->setUser($result['data']);
            $this->setSecurityToken($result['token']);
        }
        return Response::json($result);
    }

    /**
     * 修改密码
     */
    public function repwd() {
        return $this->display();
    }

    /**
     * 执行修改密码
     */
    public function dorepwd() {
        $args = [
            'oldPwd' => Input::get('oldpwd'),
            'pwd' => Input::get('pwd')
        ];
        $result = $this->requestApi('user.renewpwd',$args);
        if ($result['code'] == 0) {
            //cz修改
            if(FANWEFX_SYSTEM && !empty($this->user['fanweId'])){
                $args_data['user_id'] = $this->user['fanweId'];
                $args_data['user_password'] = Input::get('pwd');
                $fx_userinfo = $this->requestApi('fx.api', ['path'=>'modify_user', 'args'=>$args_data]);
            }

            $this->setUser($result['data']);
            $this->setSecurityToken($result['token']);
        }
        return Response::json($result);
    }

    /**
     * 分享出去的页面
     */
    public function obtaincoupon(){
        $args = Input::all();

        $user_info = Session::get('wxpay_userinfo');
        if(!empty($user_info)){
            $args['openId'] = $user_info['openid'];
        }else{
            if(empty($args['openId'])){
                $url = $_SERVER['REQUEST_URI'];
                if (empty($url)) {
                    return $this->error('参数错误');
                }

                $result = $this->requestApi('config.getpayment',['code' => 'weixinJs']);
                if (!$result || $result['code'] != 0) {
                    return $this->error('获取微信配置信息失败', $url);
                }

                $payment = $result['data'];
                $config = $payment['config'];

                Session::put('authorize_return_url', $url);

                $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$config['appId'].
                    '&redirect_uri='.urlencode(u('UserCenter/accesstoken'))
                    .'&response_type=code&scope=snsapi_userinfo&state=YZ#wechat_redirect';

                return Redirect::to($url);
            }
        }
        $limitGet = 0;
        if(!empty($args['activityId'])){
            $data['code'] = 0; //有活动
            $activity = $this->requestApi('activity.getshare',$args);
            if(empty($activity['data'])){
                $data['code'] = 3; //没有活动
                //没有活动 强行找出这个活动 (需要背景图片)
                $activity = $this->requestApi('activity.get',array('activityId'=>$args['activityId']));
            }else{ //有活动

                //判断活动是否完了
                $activity_count = count($activity['data']['logs']);
                if($activity_count == $activity['data']['sharePromotionNum']){
                    $data['code'] = 2; //活动已经完了
                    //没有活动 强行找出这个活动 (需要背景图片)
                    //$activity = $this->requestApi('Activity.get',array('activityId'=>$args['activityId']));

                }else{
                    if(!empty($this->userId)){
                            //判断用户是否领取
                            $logs_bln = $this->requestApi('Activity.logs',array('userId'=>$this->userId,'activityId'=>$args['activityId']));
                            if(($logs_bln['data']['status'] == 1 && $activity['data']['limitGet'] == 0) || ($logs_bln['data']['status'] == 1 && $logs_bln['data']['count'] < $activity['data']['limitGet'])){
                                //有用户Id 把优惠券给这用户 且生成一条记录
                                $this->requestApi('user.promotion.send',array('userId'=>$this->userId,'orderId'=>$args['orderId'],'activityId'=>$args['activityId'],'promotionId'=>$activity['data']['promotion'][0]['promotionId']));
                                $limitGet = $logs_bln['data']['status'];
                            }
                            $data['code'] = 1; //恭喜你领取了活动
                            //领取了再查一次
                            $activity = $this->requestApi('activity.getshare',array('orderId'=>$args['orderId'],'activityId'=>$args['activityId']));
                            //获取名称
                            $is_have_user = 0;
                            $name = '';
                            foreach($activity['data']['logs'] as $v){
                                if($v['userId'] == $this->userId){
                                    $is_have_user = 1;
                                    $name = $v['user']['name'];
                                    break;
                                }
                            }
                    }else{
                        //没有用户Id
                    }
                }

            }
        }else{
            $data['code'] = 3; //没有活动
        }
        View::share('args',$args);
        View::share('data',$data);
        View::share('name',$name);
        View::share('activity',$activity['data']);
        View::share('is_show_top',false);
        View::share('limitGet',$limitGet);


        return $this->display('obtain_coupon');
    }

    public function accesstoken() {
        $code = $_REQUEST['code'];
        $url = Session::get('authorize_return_url');
        if (empty($code)) {
            return $this->error('授权失败', $url);
        }

        $result = $this->requestApi('config.getpayment',['code' => 'weixinJs']);
        if (!$result || $result['code'] != 0) {
            return $this->error('获取微信配置信息失败', $url);
        }

        $payment = $result['data'];
        $config = $payment['config'];

        $wxurl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$config['appId'].
            '&secret='.$config['appSecret'].'&code='.$code.'&grant_type=authorization_code';
        $result = @file_get_contents($wxurl);
        $result = empty($result) ? false : @json_decode($result, true);
        if (!$result) {
            return $this->error('授权失败', $url);
        } elseif (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->error('授权失败:'.$result['errmsg'], $url);
        }

        $openid = $result['openid'];
        $wxurl = "https://api.weixin.qq.com/sns/userinfo?access_token={$result['access_token']}&openid={$result['openid']}&lang=zh_CN";
        $result = @file_get_contents($wxurl);
        $result = empty($result) ? false : @json_decode($result, true);

        Session::put('wxpay_userinfo',$result);
        Session::save();
        $url = $url.'&openId='.$openid;

        return Redirect::to($url);
    }

    public function docheckmobile(){
        $args = Input::all();
        $wxpay_userinfo = Session::get('wxpay_userinfo');
        $args['nickname'] = $wxpay_userinfo['nickname'];
        $args['avatar'] = $wxpay_userinfo['headimgurl'];

        $result = $this->requestApi('user.checkuser',$args);

        if ($result['code'] == 0) {
            $this->setUser($result['data']);
            $this->setSecurityToken($result['token']);
            Session::set('return_url','');
            Session::save();
        }
        if(!$result['data']){
            $url = u('User/reg',['promotion'=>'coupon','orderId'=>$args['orderId']]);
        }else{
            $args['userId'] = $result['data']['id'];
            $url = u('UserCenter/obtaincoupon',$args);
        }
        die(json_encode($url));
    }

    /**
     * 我的余额
     */
    public function balance() {
        return $this->balanceList('balance');
    }

    public function balanceList($tpl='balance_item') {
        $args = Input::all();
        $balance_result = $this->requestApi('user.balance'); 
        View::share('balance', $balance_result['data']['balance']);
		
        View::share('lockAmount', $balance_result['data']['lockAmount']);
        $result = $this->requestApi('user.getbalance', $args);
        View::share('data', $result['data']);
        View::share('args', $args);
        View::share('bank',self::getbank());
        return $this->display($tpl);
    }

    public function getbank() {
        //return "";
        $balance_result = $this->requestApi('user.bank.getbank');
        if(Input::ajax()){
            return Response::json($balance_result['data']);
        }
        return $balance_result['data'];
    }

    public function recharge() {
        $payments = $this->getPayments();
        unset($payments['cashOnDelivery']);
        unset($payments['balancePay']);
        $args = Input::all();
        if($args['isFx'] == 1){
            unset($payments['unionpay']);
            unset($payments['unionapp']);
            $userc = $this->requestApi('invitation.get');
            if($this->user['isPay'] == 1 && IS_OPEN_FX && in_array($userc['data']['userStatus'] ,[1,2])){
                return Redirect::to(u('Invitation/index'))->send();
            }
            View::share('money',  $userc['data']['protocolFee']);
            $payPwd = $this->requestApi('user.userinfo');
            View::share('isPayPwd', $payPwd['data']['isPayPwd']);
        }
        View::share('payments', $payments);
        View::share('isFx', $args['isFx']);
        View::share('user', $this->user);

        return $this->display();
    }

    /**
     * [wxpay 微信支付]
     */
    public function wxpay(){
        $args = Input::all();
        $url = u('UserCenter/pay',$args);
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
     * 充值
     */
    public function pay() {
        $args = Input::all();
        if (isset($args['payment']) && $args['payment'] == 'weixinJs') {
            Session::put('wxpay_open_id', $args['openId']);
            Session::put('pay_payment', 'weixinJs');
            Session::save();
            return Redirect::to(u('UserCenter/pay',['money' => $args['money']]));
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
        $pay = $this->requestApi('user.charge', $args);
        if($pay['code'] == 0){
            if($pay['data']['isFx'] == 1){
                $this->user['isPay'] = $pay['data']['status'] == 1 ? 1 : 0 ;
                $result['data'] = $this->user;
                $this->setUser($result['data']);
                return Redirect::to(u('Invitation/index'));
            }else{
                if (isset($pay['data']['payRequest']['html'])) {
                    echo $pay['data']['payRequest']['html'];
                    exit;
                }
            }
            View::share('pay',$pay['data']['payRequest']);
        }

        $result = $this->requestApi('user.getbalance');
        View::share('data', $result['data']);

        View::share('money',$args['money']);
        View::share('payment',$args['payment']);
        return $this->display('wxpay');
    }
    public function createpaylog()
    {
        $args = Input::all();
        $pay = $this->requestApi('user.charge', $args);
        die(json_encode($pay["data"]));
    }

    /**
     * 分享返现
     */
    public function shareReturnMoney() {
        return $this->display();
    }

    public function regpush(){
        $data = Input::all();
        $result = $this->requestApi('user.regpush',$data);
    }

    /**
     * 设置支付密码
     */
    public function paypwd() {
        $args = Input::all();
        $payPwd = $this->requestApi('user.userinfo');
        View::share('isPayPwd', $payPwd['data']['isPayPwd']);
        if($args['type'] == 2){
            View::share('nav_back_url', u('Order/livepay').'?'.http_build_query($args['args']));
        }else if($args['type'] == 3){
            View::share('nav_back_url', u('UserCenter/info'));
        }
        return $this->display();
    }


    /**
     * 忘记支付密码
     */
    public function repaypwd() {
        View::share('user', $this->user);
        return $this->display();
    }

    /**
     *设置支付密码
     */
    public function dorepaypwd() {
        $args = Input::get();
        unset($args['reNewPwd']);
        $result = $this->requestApi('user.repaypwd', $args);
        if ($result['code'] == 0) {
            return $this->success($result['msg']);
        } else {
            return $this->error($result['msg']);
        }
    }


    /**
     * 积分详情
     */
    public function integral() {
        $args = Input::get();
        $list = $this->requestApi('user.integral', $args);
        View::share('list', $list['data']);
        if (Input::ajax()) {
            return $this->display('integral_item');
        } else {
            return $this->display();
        }

    }

    /**
     * 检测支付密码
     */
    public function checkpaypwd() {
        $input = Input::get();
        $args['password'] = md5(md5(md5($input['password']) . $input['password']) . $this->userId);
        $result = $this->requestApi('user.checkpaypwd', $args);
        if ($result['code'] == 0) {
            return $this->success($result['msg'], '', $result['data']);
        } else {
            return $this->error($result['msg']);
        }
    }

    /**
     * 每日签到
     */
    public function signin() {
        $page = max((int)Input::get('page'), 1);
        $sign = $this->requestApi('user.signin');
        $result = $this->requestApi('user.integral',['type'=>1, 'page'=>$page]);
        View::share('integral', $result['data']['list']);
        $signIntegral = $this->requestApi('config.configByCode',['code' => 'sign_integral']);
        View::share('signIntegral', $signIntegral['data']);
        if(Input::ajax()) {
            return $this->display('signin_item');
        } else {
            return $this->display();
        }

    }

    /**
     * 微信绑定
     */
    public function bind(){
        $args = Input::all();
        $user_info = Session::get('wxbind_userinfo');

        if(!empty($user_info)){
            $args['openid'] = $user_info['openid'];
            $args['unionid'] = $user_info['unionid'];
        }else{
            if(empty($args['unionid'])){
                $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                if (empty($url)) {
                    return $this->error('参数错误');
                }
                $result = $this->requestApi('config.getpayment',['code' => 'weixinJs']);
                if (!$result || $result['code'] != 0) {
                    return $this->error('获取微信配置信息失败', $url);
                }

                $payment = $result['data'];
                $config = $payment['config'];

                Session::put('authorize_bind_url', $url);

//                $config['appId'] = 'wxdec1e10223f8e4be';
                $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$config['appId'].
                    '&redirect_uri='.urlencode(u('UserCenter/accesstoken2'))
                    .'&response_type=code&scope=snsapi_userinfo&state=YZ#wechat_redirect';

                return Redirect::to($url);
            }
        }

        //判断是否有这个unionid 如果没有就加载模板 有了登录
        $result = $this->requestApi('user.weixinbind',$args);
        View::share('result', $result);


        return $this->display('bind');

    }

    public function accesstoken2() {
        $code = $_REQUEST['code'];
        $url = Session::get('authorize_bind_url');
        $url = !empty($url) ? $url : u("UserCenter/bind");

        if (empty($code)) {
            return $this->error('授权失败', $url);
        }

        $state = Input::get('state');
        if($state == "fwapp"){
            $result = $this->requestApi('config.getpayment',['code' => 'weixin']);
            $payment = $result['data'];
            $config = $payment['config'];
        }else{
            $result = $this->requestApi('config.getpayment',['code' => 'weixinJs']);
            $payment = $result['data'];
            $config = $payment['config'];

//            $config['appId'] = 'wxdec1e10223f8e4be';
//            $config['appSecret'] = '4af43812ccce2ff4df1071b931a504c2';
        }

        if (!$result || $result['code'] != 0) {
            return $this->error('获取微信配置信息失败', $url);
        }

        $wxurl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$config['appId'].
            '&secret='.$config['appSecret'].'&code='.$code.'&grant_type=authorization_code';
        $result = @file_get_contents($wxurl);
        $result = empty($result) ? false : @json_decode($result, true);

        if (!$result) {
            return $this->error('授权失败', $url);
        } elseif (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->error('授权失败:'.$result['errmsg'], $url);
        }

        $openid = $result['openid'];
        $wxurl = "https://api.weixin.qq.com/sns/userinfo?access_token={$result['access_token']}&openid={$result['openid']}&lang=zh_CN";
        $result = @file_get_contents($wxurl);
        $result = empty($result) ? false : @json_decode($result, true);

        Session::put('wxbind_userinfo',$result);
        Session::save();

        $unionid = $result['unionid'];
        $url = $url.'?unionid='.$unionid;

        return Redirect::to($url);
    }
    /**
     * 我的评价
     */
    public function rate() {
        $args = Input::all();
        $result = $this->requestApi('rate.order.userlists',$args);
        $result = $result['data'];

        $list = [];
        foreach ($result as $key => $value) {
            if($value['isAll'] == 1)
            {
                $list[$value['orderId']][] = $value;
            }
            else
            {
                $list[$value['orderId']] = $value;
            }
            unset($result[$key]);
        }

        View::share('list', $list);
        if($args['tpl'] == "item") {
            return $this->display('rate_item');
        } else {
            return $this->display();
        }
    }

    /**
     *消息中心
     */
    public function  systemmessage()
    {
        $list = $this->requestApi('msg.systemmessage');
        View::share('nav','msg');

        if ($list['code'] == 0){
            View::share('list', $list['data']);
       }
        return $this->display();
    }

    /**
     *订单变更状态列表
     */

    public function systemList($tpl='system_item') {
        $args = Input::all();
        $list = $this->requestApi('msg.orderchange',['page' => $args['page']]);

        if($list['code'] == 0)
            View::share('list',$list['data']);
            View::share('args',$args);

        if(!Input::ajax()){
            View::share('nav','message');
            View::share('title',"- 消息");
        }

        return $this->display($tpl);
    }


    public function  orderchange(){
        return $this->systemList('orderchange');
    }

    /**
     * 分销商wap端管理地址跳转
     * @return mixed|string 兑换结果
     */
    public function wapcenter(){
        $user = $this->user;
        $userbyfanweid = $this->requestApi('user.fanwe_id',['userId'=>$this->userId]);
        $user['fanweId'] = $userbyfanweid['data']['fanweId'];

        $result = $this->requestApi('fx.makewapurl', ['fanweId'=>$user['fanweId']]);
        return Redirect::to($result['data']);
    }

    /**
     * 分佣积分兑换
     */
    public function fxexchange() {
        //当前佣金（积分）
        $user = $this->user;
        if(!empty($user['fanweId']) && FANWEFX_SYSTEM){
            $args_data['user_id'] = $user['fanweId'];
            $fx_userinfo = $this->requestApi('fx.api', ['path'=>'get_user_info', 'args'=>$args_data]);
            View::share('fx_userinfo', $fx_userinfo['data']);

            //兑换比例
            $fx_exchange_percent = $this->getConfig('fx_exchange_percent');
            View::share('fx_exchange_percent', $fx_exchange_percent);
        }
        else
        {
            Redirect::to(u('UserCenter/index'))->send();
        }

        return $this->display();
    }

    /**
     * 确认兑换
     */
    public function fxexchangemake() {
        $user = $this->user;
        //计算兑换金额
        $fx_exchange_percent = $this->getConfig('fx_exchange_percent');
        $money = round(Input::get('money') / $fx_exchange_percent, 2);

        $args = [
            'userId' => $user['id'],
            'money' => $money,
            'type' => 1,    //充值
            'remark' => '佣金兑换金额',
        ];
        $result = $this->requestApi('user.updatebalance', $args);


        //本地充值成功，扣除平台佣金
        if($result['code'] == 0)
        {
            $data = [
                'appsys_id' => Config::get('app.fanwefx.appsys_id'),
                'user_id' => $user['fanweId'],
                'money' => Input::get('money'),
                'description' => '佣金兑换余额',
            ];
            $result = $this->requestApi('fx.api', ['path'=>'consume_commission', 'args'=>$data]);

            //如过平台佣金扣除失败，扣除本地充值的金额
            if($result['code'] > 0)
            {
                $args = [
                    'userId' => $user['id'],
                    'money' => $money,
                    'type' => 2,    //扣除
                    'remark' => '抹掉佣金兑换金额',
                ];
                $this->requestApi('user.updatebalance', $args);
            }
           
        }
        return Response::json($result);
    }

    /**
     * 申请成为分销商
     */
    public function sharechapman(){
        $result = $this->requestApi('user.getsharechapman');

        View::share('data',$result['data']);
        return $this->display();
    }

    public function regsharechapman(){
        $data = Input::all();

        $result = $this->requestApi('user.regsharechapman',$data);
        return Response::json($result);
    }
}
