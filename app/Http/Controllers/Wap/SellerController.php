<?php namespace YiZan\Http\Controllers\Wap;



use View, Input, Lang, Route, Page ,Session,Redirect,Response,Image,Config;
/**
 * 服务人员
 */
class SellerController extends BaseController {

    //
    public function __construct() {
        parent::__construct();
        View::share('nav','Seller/index');
    }

    /**
     * 商家列表
     */
    public function index(){
        return $this->indexList('index');
    }

    public function indexList($tpl = 'seller_item') {
        $option = Input::all();
        $args = $option;
        if ((int)$option['id'] < 1) {
            $args['id'] = 0;
            $args['type'] = (int)$option['type'];
        }
        if(empty($option['sort']) || (int)$option['sort'] < 1){
            $args['sort'] = 0;//排序方式，默认综合排序
        }

        if ($args['id'] > 0) {
            $seller_cate = $this->requestApi('seller.getcate',array('id'=>$args['id']));
            View::share('cate',$seller_cate['data']);
            $args['type'] = $seller_cate['data']['type'];
        }

        $defaultAddress = Session::get("defaultAddress");
        if ($defaultAddress) {
            $args['mapPoint'] = $defaultAddress['mapPointStr'];
        }

        //获取商家所有分类
        $seller_cates = $this->requestApi('seller.catelists', array('type'=>0));//$args['type']

        if($seller_cates['code'] == 0) {
            View::share('seller_cates',$seller_cates['data']);
        }
        $address = Session::get("defaultAddress");
        $banner = $this->requestApi('config.sellercatebanner',['cityId' => (int)$address['cityId'], 'sellerCateId' => $args['id']]);

        View::share('banner', $banner['data'][0]);
        $args['types'] = $args['types'] == 'goods' ? 'goods' : 'seller';
        if($tpl != 'index'){
            if($args['types'] == "goods"){
                $goods_data = $this->requestApi('goods.getGoodsListsDsy',$args); //getGoodsListsDsy

                if($goods_data['code'] == 0) {
                    View::share('data',$goods_data['data']);
                }
                if($tpl == "seller_item"){
                    $tpl = "goods_item";
                }
                $args['types'] = "goods";
            }else{
                $seller_data = $this->requestApi('seller.lists',$args);
                if($seller_data['code'] == 0) {
                    View::share('data',$seller_data['data']);
                }
                $args['types'] = "seller";
            }
        }

        View::share('args',$args);
        View::share('tpl',$tpl);
        View::share('shareType',"goods");
        $getWeixinUser = $this->requestApi('Useractive.getWeixinUser',['openid'=>$this->user['openid']]);
        View::share("nickname",  $getWeixinUser['data']['nickname']);
        View::share("weiXinData",  $getWeixinUser['data']);


        $weiXinUserData = Session::get("user");
        View::share('weiXinUserData',$weiXinUserData);
        return $this->display($tpl);
    }

    public function appindex(){
        $option = Input::all();
        $args = $option;
        if ((int)$option['id'] < 1) {
            $args['id'] = 0;
            $args['type'] = (int)$option['type'];
        }
        if(empty($option['sort']) || (int)$option['sort'] < 1){
            $args['sort'] = 0;//排序方式，默认综合排序
        }

        if ($args['id'] > 0) {
            $seller_cate = $this->requestApi('seller.getcate',array('id'=>$args['id']));
            View::share('cate',$seller_cate['data']);
            $args['type'] = $seller_cate['data']['type'];
        }

        //获取商家所有分类
        $seller_cates = $this->requestApi('seller.catelists', array('type'=>$args['type']));

        //print_r($seller_cates);
        if($seller_cates['code'] == 0) {
            View::share('seller_cates',$seller_cates['data']);
        }
        //need to do get mapPoint 经纬度
        //print_r($seller_cate);
        //var_dump($args);
        $seller_data = $this->requestApi('seller.lists',$args);
        //print_r($seller_data);
        if($seller_data['code'] == 0) {
            View::share('data',$seller_data['data']);
        }

        if (Input::ajax()) {
            return Response::json($seller_data);
        } else {
            View::share('args',$args);
            return $this->display();
        }
    }
    /**
     * 搜索
     */
    public function search(){
        $keyword = Input::get('keyword');
        $sellerId = Input::get('sellerId');
        if(!$sellerId){
            $search_type = Input::get('search_type')=='seller'?'seller':'goods';
        }else{
            $search_type = 'goods';
        }
        $type = Input::get('type')=='a'?'a':'';

        $sellerId = Input::get('sellerId');

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


        if($option['sellerId']){
            $option['sort'] = ($option['sort'] == '') ? 3 : $option['sort'];
        }

        $option['pageSize'] = 6;
        View::share('option',$option);
        $defaultAddress = Session::get("defaultAddress");
        View::share('defaultAddress',$defaultAddress);
        if (Input::get('keyword')) {


            View::share('keyword',$keyword);
            /*按商品搜索店铺*/
            if($search_type=='seller'){
                $option['pageSize'] = 5;

                $seller_data = $this->requestApi('seller.sellergoodslists',$option);
                foreach($seller_data['data']['sellerlist'] as $k=>$v){
                    $seller_data['data']['sellerlist'][$k]['distance'] = round($v['distance'],2);
                }
                //分页
                if($type=='a'){
                    $search_tpl = 'searchresult_sellers_item';
                }else{
                    $search_tpl = 'searchresult_sellers';
                }

                if($seller_data['code'] == 0){
                    View::share('data',$seller_data['data']['sellerlist']);
                    View::share('seller_count',$seller_data['data']['seller_total']);
                    View::share('goods_count',$seller_data['data']['goods_total']);
                }

            }else{
                /*搜索商品*/

                $goods_data = $this->requestApi('seller.goodslists',$option);

                //分页
                if($type=='a'){
                    $search_tpl = 'searchresult_goods_item';
                }else{
                    $search_tpl = 'searchresult_goods';
                    if($goods_data['code'] == 0){
                        if(empty($goods_data['data']['goodslist'])){
                            //无商品跳转搜索店铺
                            if(!$sellerId){
                                header("Location:search?search_type=seller&keyword=$keyword&sellerId=$sellerId");exit;
                            }
                            return $this->display($search_tpl);
                        }
                        //dd($goods_data);
                    }
                }

                View::share('data',$goods_data['data']['goodslist']);
                View::share('seller_count',$goods_data['data']['seller_total']);
                View::share('goods_count',$goods_data['data']['goods_total']);

            }

            return $this->display($search_tpl);
        } else {
            return $this->display();
        }
    }

    /**
     * 清除搜索历史记录
     */
    public function clearsearch(){
        Session::set('searchs', NULL);
        Session::save();
    }

    /**
     * 分类
     */
    public function goodscate(){
        $sellerId = Input::get('sellerId');
        if($sellerId == ""){
            $this->error('错误');
        }

        $option = Input::all();
        $option['id'] = $sellerId;
        //获取商家所有分类
        $cate_result = $this->requestApi('goods.lists', $option);
        View::share('cate', $cate_result['data']);

        $count = 0;
        if(!empty($cate_result['data'])){
            foreach($cate_result['data'] as $k=>$v){
                $count += count($v['goods']);
            }
        }
        View::share('count', $count);
        View::share('args', $option);

        return $this->display();
    }

    /**
     * categoods 全国点分类下的商品
     */
    public function categoods(){
        $option = Input::all();
        View::share('args', $option);

        //好货推荐
        $goods = $this->requestApi('config.getrecommendgoods',['sellerId'=>$option['sellerId'],'noIndex'=>1,'cateId'=>$option['id']]);
        $data['data']['goods'] = $goods['data'];
        if($data['code'] == 0)
            View::share('data', $data['data']);


        return $this->display();
    }

    /**
     * 机构详情
     */
    public function detail(){
        $option = Input::all();
        $seller_data = $this->requestApi('seller.detail',$option);
        $seller_data = $seller_data['data'];
        if ($seller_data['code'] == 0) {
            View::share('seller_data',$seller_data);
            View::share('seller',$seller_data);
        }

        //获取公告
        $article_result = $this->requestApi('article.lists', ['sellerId'=>$option['id']]);
        View::share('articles', $article_result['data']);
        View::share('option',$option);
        View::share('collect','yes');

        //邀请注册
        if( strtolower(Input::get('type')) == 'seller' && Input::get('id') > 0)
        {
            Session::put('invitationType', Input::get('type'));
            if(Input::get('shareUserId')){
				$invitationUserId = Input::get('shareUserId');				
			}else{
				$invitationUserId = Input::get('id');
			}
            Session::put('invitationId', $invitationUserId);
            Session::save();
        }else if( Input::get('shareUserId') > 0)
        {
			Session::put('invitationType', 'user');
            Session::put('invitationId', Input::get('shareUserId'));
            Session::save();
		}

        if($option['id'] == ONESELF_SELLER_ID){
            $return_url = u('Oneself/index');
        }else{
            $return_url = u('Index/index');
        }
        View::share('return_url',$return_url);

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $protocol.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
        $weixin_arrs = $this->requestApi('invitation.getweixin',array('url' => $url));
        if($weixin_arrs['code'] == 0){
            View::share('weixin',$weixin_arrs['data']);
        }

        $share = [
            'title'		=>	$seller_data['name'],
            'content'	=>	$seller_data['detail'],
            'url'		=>	u('Seller/detail', ['id'=>$option['id'],'type'=>'seller']),
            'logo'		=> 	$seller_data['logo'],
        ];

        $getWeixinUser = $this->requestApi('Useractive.getWeixinUser',['openid'=>$this->user['openid']]);

        $newtitle = $getWeixinUser['data']['nickname']."为您推荐:".$share['title'];
        $share['title'] = $newtitle;

        View::share("share", $share);
        View::share("nickname",  $getWeixinUser['data']['nickname']);
        View::share("weiXinData",  $getWeixinUser['data']);
        $userInfo = $this->user;
        View::share("user", $userInfo);




        $weiXinUserData = Session::get("user");
        View::share('weiXinUserData',$weiXinUserData);
        if($seller_data['storeType'] == 1){
            //好货推荐
            $goods = $this->requestApi('config.getrecommendgoods',['sellerId'=>$seller_data['id'],'noIndex'=>1]);
            $data['data']['goods'] = $goods['data'];
            if($data['code'] == 0)
                View::share('data', $data['data']);
            //平台客服电话
            $wap_service_tel = $this->getConfig('wap_service_tel');
            View::share('wap_service_tel', $wap_service_tel);
            //平台名称
            $site_name = $this->getConfig('site_name');
            View::share('site_name', $site_name);

            return $this->display('alldetail');
        }else{
			View::share('shareType','seller' );
			$data = [
				'sellerId' => $seller_data['id'],
				'id' => $seller_data['id']
			];
			View::share('data',$data);
            return $this->display();
        }

    }

    /**
     * 二维码
     */
    public function cancode(){
        $shareType = Input::get('shareType');
        if($shareType == "goods"){
            $value = u('Goods/detail',[
                'goodsId'=>Input::get('id'),
                'id'=>Input::get('id'),
                'type'=>'goods',
                'shareUserId' => $this->userId
            ]);
        }else{
            $value = u('Seller/detail',[
                    'id'=>Input::get('id'),
                    'type'=>'seller',
                    'shareUserId' => $this->userId
                ]
            );
        }
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
        $QRcode->png($value, false, $errorCorrectionLevel, $matrixPointSize, $margin,$saveandprint=false,$backColor,$foreColor);
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
        //输出图片
        echo imagepng($QR);exit;
    }


    public function reg() {


        //微信登录的没有手机号码 要去绑定
        $userinfo = $this->user;
        if(empty($userinfo['mobile'])){
            View::share('return_url', u('Seller/settled'));
            return $this->display('bindmobile','order');
        }

        View::share('title', '- 商家开户');
        $cate_result = $this->requestApi('seller.catelists');
        View::share('cate', $cate_result['data']);
        if(Input::get("isdata") == ""){
            $seller = $this->requestApi('seller.sellerdatum');
            if($seller['data'] != ""){
                if($seller['data']['isCheck'] == -1){
                    View::share('isCheck',true);
                    View::share('isData',false);
                }else{
                    View::share('isCheck',false);
                    View::share('isData',true);
                }
                $seller = $seller['data'];
                $seller['introduction'] =  $seller['brief'];
                $seller['mapPoint'] =  $seller['mapPointStr'];
                $seller['idcardSn'] =  $seller['authenticate']['idcardSn'];
                $seller['idcardPositiveImg'] =   $seller['authenticate']['idcardPositiveImg'];
                $seller['idcardNegativeImg'] =   $seller['authenticate']['idcardNegativeImg'];
                $seller['businessLicenceImg'] =   $seller['authenticate']['businessLicenceImg'];
                unset($seller['authenticate'],$seller['mapPos'],$seller['nameMatch'],$seller['brief']);
                Session::put('reg_info',$seller);
            }else{
                Session::put('reg_info',Session::get('reg_info'));
                View::share('isData',false);
            }
            Session::save();
        }else{
            View::share('isData',false);
        }
        View::share('option', Session::get('reg_info'));
        $cate_option = Session::get('cate_info');
        View::share('cate_option', $cate_option);
        $choose_cate = '';
        $i = 0;
        if(SELLER_TYPE_IS_ALL){
            foreach ($cate_result['data'] as $key => $value) {
                foreach ($cate_option as $id) {
                    if($value['id'] == $id){
                        $choose_cate .= ' '.$value['name'];
                        $i++;
                    }
                }
                if ($value['childs']) {
                    foreach ($value['childs'] as $k => $v) {
                        if (in_array($v['id'], $cate_option)) {
                            $choose_cate .= ' '.$v['name'];
                            $i++;
                        }
                    }
                }
                if($i >= 2){
                    $choose_cate .= "等";
                    break;
                }
            }
        }else{
            foreach ($cate_result['data'] as $key => $value) {
                if($value['id'] == $cate_option){
                    $choose_cate = $value['name'];
                }
                if ($value['childs']) {
                    foreach ($value['childs'] as $k => $v) {
                        if ($v['id'] == $cate_option) {
                            $choose_cate =  $v['name'];
                        }
                    }
                }
            }
        }
        View::share('cate_str', $choose_cate);
        View::share('login_user', Session::get('user'));
        return $this->display();
    }

    /**
     * 全部分类
     */
    public function cates(){
        $args = Input::all();
        $cates = $this->requestApi('seller.catelists',$args);
        if ($cates['code'] == 0) {
            View::share('cates',$cates['data']);
        }

        return $this->display();

    }

    /**
     * 商家入住
     */
    public function settled(){
        //微信登录的没有手机号码 要去绑定
        $userinfo = $this->user;
        if(empty($userinfo['mobile'])){
            View::share('return_url', u('Seller/settled'));
            return $this->display('bindmobile','order');
        }

        $result = $this->requestApi('seller.check', ['id'=>$this->userId]);

        $config = $this->getConfig();

        $staff_settled_image = $config['staff_settled_image'];
        $site_name = $config['site_name'];
        $wap_service_tel = $config['wap_service_tel'];
        View::share('staff_settled_image',$staff_settled_image);
        View::share('site_name',$site_name);
        View::share('wap_service_tel',$wap_service_tel);
        View::share('seller',$result['data']);

        return $this->display();
    }

    /**
     * @return string
     */
    public function openappurl(){
        $args = Input::all();
        View::share('args',$args);

        $result = $this->requestApi('config.getpayment',['code' => 'weixin']);
        $payment = $result['data'];
        $config = $payment['config'];
        $appId = $config['appId'];
        if($args['type'] == 1){
            $url = asset('app/staff.ipa');

        }else{
            $url = asset('app/androidstaffapp.apk');

        }
        $size = get_headers($url,true);
        $sizes = isset($size['Content-Length'][1]) ? $size['Content-Length'][1] : $size['Content-Length'];
        $filesize = round(round($sizes / 1048576 * 100) / 100,1) . ' MB';
        View::share('appId',$appId);
        View::share('appsize',$filesize);
        View::share('config',$this->getConfig());
        return $this->display();
    }

    public function cate(){
        $cate_result = $this->requestApi('seller.catelists');
        View::share('title', '- 经营范围');
        View::share('cate', $cate_result['data']);
        View::share('current', Session::get('cate_info'));
        View::share('nav_back_url', u('Seller/reg'));
        View::share('isData',Input::get('isdata'));
        View::share('storeType', Session::get('reg_info')['storeType']);
        return $this->display();
    }

    public function saveCate(){
        $args = Input::all();
        Session::put('cate_info', $args['cateIds']);
        Session::save();
        return $this->success('成功');
    }

    public function saveRegData(){
        $args = Input::all();
        Session::put('reg_info', $args);
        Session::save();
        return $this->success('成功');
    }

    public function doreg(){
        $args = Input::all();
        $cate = Session::get('cate_info');
        $args['cateIds'] = is_array($cate) ? $cate : [$cate];
        $args['cateIds'] = array_filter($args['cateIds']);
        $reg_result = $this->requestApi('seller.reg', $args);
        if($reg_result['code'] > 0){
            return $this->error($reg_result['msg']);
        } else {
            Session::set('reg_info', NULL);
            Session::save();
            return $this->success($reg_result['msg']);
        }
    }

    public function app(){
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $iphone = (strpos($agent, 'iphone')) ? true : false;
        $ipad = (strpos($agent, 'ipad')) ? true : false;
        $android = (strpos($agent, 'android')) ? true : false;
        $config = $this->getConfig();
        if($iphone || $ipad) {
            if(strpos($agent, 'micromessenger')){
                return $this->display();
            } else {
                Redirect::to($config['staff_app_down_url'])->send();
            }
        }
        if($android) {
            Redirect::to($config['staff_android_app_down_url'])->send();
        }
    }

    /**
     * 地图页面
     */
    public function map(){
        if(Input::get('isdata')){
            View::share('isData',true);
        }else{
            View::share('isData',false);
        }
        View::share('data', Session::get('reg_info'));
        return $this->display();
    }

    /**
     * 保存地图数据
     */
    public function mapSave(){
        $data = Session::get('reg_info');
        $data['mapPosStr'] = Input::get('mapPos');
        $data['mapPointStr'] = Input::get('mapPoint');
        Session::put('reg_info', $data);
        Session::save();
        return $this->success('成功');
    }

    /**
     * 地图页面
     */
    public function mappoint(){
        $data = Session::get('reg_info');
        View::share('data', $data);
        View::share('isData',Input::get('isdata'));
        return $this->display();
    }

    /**
     * 保存地图数据
     *
     */
    public function mapPointSave(){
        $data = Session::get('reg_info');
        $data['mapPointStr'] = Input::get('mapPoint');
        $data['address'] = Input::get('address');

        $city =  $this->requestApi('user.address.getbyname',['name'=>Input::get('city')]);
        if($city['code'] == 0){
            $data['cityId'] = $city['data']['id'];
            $data['city'] = Input::get('city');
        }

        Session::put('reg_info', $data);
        Session::save();
        return $this->success('成功');
    }
    /**
     * 画布弄图片
     */
    public function shopdetail()
    {
        $args = Input::all();
        $shareType = $args['shareType'];

        if($shareType == "goods"){
            //获取商品详情
            $data = $this->requestApi('goods.detail', ['goodsId'=>$args['goodsId']]);
            $goodsOrSellerimages	= $data['data']['images'][0];
            $goodsOrSellerName	= $data['data']['name'];
            $images = "goods_".$args['goodsId'].'.png';
        }else{
            //获取商家详情
            $data = $this->requestApi('seller.detail', ['id'=>$args['id']]);
            $goodsOrSellerimages	= $data['data']['logo'];
            $goodsOrSellerName	= $data['data']['name'];
            $images = "seller_".$args['id'].'.png';
        }
        $getWeixinUser = $this->requestApi('Useractive.getWeixinUser',['openid'=>$this->user['openid']]);
        $seller = $this->requestApi('seller.detail', $args);
        $user = $this->user;
        if($user['avatar']){
            $headimage = $user['avatar'];
        }else{
            $headimage =  asset('wap/community/client/images/wdtt.png');//y20
        }
//        if($getWeixinUser['data']['headimgurl']){
//            $headimage = $getWeixinUser['data']['headimgurl'];
//        }else{
//            $headimage =  asset('wap/community/newclient/images/y20.png');//y20
//        }

        $imgs = $this->cancodeDsy();

        $img = Image::canvas(300, 452, '#FFF');

        //$images = "sellerIds_".$args['id']. '.png';
        $outfile = base_path() . '/public/code/' . $images; //输出图片位置
        $imageurl =  asset("/code/".$images);
        /**
         * 会员logo
         */
        $logo = $this->make($headimage,60);
        $img->insert($logo,'top-left', 45, 25);
        /**
         * 会员logo背景
         */
        $bg = $this->make(asset("/images/quanbg.png"),60);
        $img->insert($bg, 'top-left', 45, 25);
        /**
         * 会员名称
         */
      //  $userName = str_limit($getWeixinUser['data']['nickname'], 15, '...')."向你推荐";
        $userName = str_limit($user['name'], 15, '...')."向你推荐";


        $img->text($userName, 180, 50, function ($font) {
            $font->file(base_path() . '/resources/fonts/msyh.ttf');
            $font->size(14);
            $font->color('#000');
            $font->valign('top');
            $font->align('center');
        });

        /**
         * 商品或商家名称
         */
        if(strlen($goodsOrSellerName) > 20){
            $img->text(mb_substr($goodsOrSellerName,0,18,'utf-8'), 150, 100, function ($font) {
                $font->file(base_path() . '/resources/fonts/msyh.ttf');
                $font->size(14);
                $font->color('#000');
                $font->valign('top');
                $font->align('center');
            });
            $name = mb_substr($goodsOrSellerName,18);
            $img->text($name, 150, 120, function ($font) {
                $font->file(base_path() . '/resources/fonts/msyh.ttf');
                $font->size(14);
                $font->color('#000');
                $font->valign('top');
                $font->align('center');
            });

        }else{
            $img->text($goodsOrSellerName, 150, 100, function ($font) {
                $font->file(base_path() . '/resources/fonts/msyh.ttf');
                $font->size(14);
                $font->color('#000');
                $font->valign('top');
                $font->align('center');
            });
        }
        /**
         * 商品图片logos
         */

        $goodsOrSellerImg = $this->make($goodsOrSellerimages,180);
        $img->insert($goodsOrSellerImg, 'top', 170, 150);
        $site_name = $this->getConfig('site_name');

        /**
         * 商品二维码
         */
        $img2 = $this->make($imageurl,78);
        $img->insert($img2, 'bottom-left', 30, 20);

        $site_name = $this->getConfig('site_name');

        /**
         * 加字
         */
        $img->text('长按或扫描', 115,390, function ($font) {
            $font->file(base_path() . '/resources/fonts/msyh.ttf');
            $font->size(12);
            $font->color('#000');
            $font->align('bottom');
        });
        $site_config = $this->getConfig();
        /**
         * 加字
         */
        $img->text('二维码购买', 115, 410, function ($font) {
            $font->file(base_path() . '/resources/fonts/msyh.ttf');
            $font->size(12);
            $font->color('#000');
            $font->align('bottom');
        });
//        $img3 = $this->make(asset('wap/community/newclient/images/y20.png'),45, 45);
        if($site_config['app_logo']){
            $img3 = $this->make($site_config['app_logo'],45, 45);
            $img->insert($img3, 'bottom-right', 50, 50);
        }


        /**
         * 加字
         */
        $img->text($site_config['site_name'],200,420,function ($font) {
            $font->file(base_path() . '/resources/fonts/msyh.ttf');
            $font->size(12);
            $font->color('#000');
            $font->align('bottom');
        });

        $img->save($outfile);
        if($args['html']){
            echo "<img src=".$imageurl." >";
        }else{
            echo file_get_contents($imageurl);
        }
        exit;
    }
    public function cancodeDsy()
    {
        $shareType = Input::get('shareType');
        if($shareType == "goods"){
            $value = u('Goods/detail',[
                'goodsId'=>Input::get('goodsId'),
                'id'=>Input::get('id'),
                'type'=>'goods',
                'shareUserId' => $this->userId
            ]);
            $images = "goods_".Input::get('goodsId');
        }else{
            $value = u('Seller/detail',[
                    'id'=>Input::get('id'),
                    'type'=>'seller',
                    'shareUserId' => $this->userId
                ]
            );
            $images = "seller_".Input::get('id');
        }

        $errorCorrectionLevel = 'L';//容错级别
        $matrixPointSize = 6;//生成图片大小
        $backColor = 0xFFFFFF; //背景色
        $foreColor = 0x000000; //前景色
        $margin = 1; //边距
        $QR = '';

        $outfile = base_path() . '/public/code/' . $images . '.png'; //输出图片位置
        if (file_exists($outfile)) {
            @unlink($outfile);
        }
        include base_path() . '/vendor/code/Code.class.php';
        $QRcode = new \QRcode();
        $logo = asset('images/fenx.jpg');
        //生成二维码图片
        $QRcode->png($value, $outfile, $errorCorrectionLevel, $matrixPointSize, $margin, $saveandprint = false, $backColor, $foreColor);
        $QR = imagecreatefromstring(file_get_contents($QR));
        if ($logo !== FALSE) {
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);//二维码图片宽度
            $QR_height = imagesy($QR);//二维码图片高度
            $logo_width = imagesx($logo);//logo图片宽度
            $logo_height = imagesy($logo);//logo图片高度
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width / $logo_qr_width;
            $logo_qr_height = $logo_height / $scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            //重新组合图片并调整大小
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
        }
        //输出图片
        imagepng($QR, $outfile);

        $image = $images . '.png';//二维码
        return $image;
    }

    public function make($url,$w = 100,$h){
        $h = $h ? $h : $w;
        return Image::make($url)->resize($w, $h);
    }
}
