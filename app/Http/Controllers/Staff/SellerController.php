<?php
namespace YiZan\Http\Controllers\Staff;

use View, Input, Lang, Route, Page ,Session,Response,Redirect,Cache,Request,Image,Config;
/**
 * 我的
 */
class SellerController extends AuthController
{

    public function __construct()
    {
        parent::__construct();
        View::share('active', "seller");
        if (in_array($this->role, [2, 4])) {
            return Redirect::to(u('Index/index'))->send();
        }
        View::share('show_top_preloader', true);
    }

    /**
     * 首页信息
     */
    public function index()
    {
        View::share('title', '店铺');
        $result = $this->requestApi('shop.info');
        View::share('seller', $result['data']);
        return $this->display();
    }

    /**
     * 首页信息
     */
    public function goodslists()
    {

        $args = Input::all();
        View::share('title', '商品管理');
        $args['type'] = $args['type'] ? $args['type'] : 1;
        $result = $this->requestApi('goodscate.lists', $args);

        View::share('goods', $result['data']);
        View::share('status', $args['status']);
		if ($args['tpl']) {
            return $this->display('goods_' . $args['tpl']);
        }
        return $this->display();
    }

    /**
     * 首页信息
     */
    public function goods()
    {
        $args = Input::all();
        if (!$args['status']) {
            $args['status'] = 1;
        }
        
        if($_GET['keywords'] != ''){
            $args['keywords'] = $_GET['keywords'];
        }
        
        if($_GET['id'] != ''){
            $args['id'] = $_GET['id'];
        }
        
        if($_GET['status'] != ''){
            $args['status'] = $_GET['status'];
        }

        View::share('nav_back_url', u('Seller/goodslists'));
        $args['page'] = $args['page'] ? $args['page'] : 1;
        View::share('title', '商品管理');
        $result = $this->requestApi('goods.lists', $args);
        View::share('goods', $result['data']);
        View::share('status', $args['status']);
        View::share('id', $args['id']);
        View::share('page', $args['page']);
        unset($args['page']);
        View::share('args', $args);
        if ($args['tpl']) {
            return $this->display('service_' . $args['tpl']);
        }
        View::share('ajaxurl_page', "_" . $args['status']);

        Cache::forget($this->staffId);
        return $this->display();
    }

    /**
     * 首页信息
     */
    public function seller()
    {
        $args = Input::all();
        $args['type'] = $args['type'] ? $args['type'] : 2;
        View::share('title', '商品管理');
        $result = $this->requestApi('goodscate.lists', $args);
        View::share('goods', $result['data']);
        if (count($result['data']) == 20) {
            View::share('show_preloader', true);
        }
        unset($args['page']);
        View::share('args', $args);
        if ($args['tpl']) {
            return $this->display('seller_' . $args['tpl']);
        }
        return $this->display();
    }

    /**
     * 首页信息
     */
    public function selleritme()
    {
        $args = Input::all();
        $args['type'] = $args['type'] ? $args['type'] : 2;
        $result = $this->requestApi('goodscate.lists', $args);
        return Response::json($result['data']);
    }

    /**
     * 首页信息
     */
    public function goodsitme()
    {
        $args = Input::all();
        $args['type'] = $args['type'] ? $args['type'] : 1;
        $result = $this->requestApi('goodscate.lists', $args);
        return Response::json($result['data']);
    }

    /**
     * 员工详细
     */
    public function info()
    {
        $args = Input::all();
        View::share('title', '店铺信息');
        $result = $this->requestApi('shop.info');
        View::share('nav_back_url', u('Seller/index'));
        View::share('seller', $result['data']);

        $ua = $_SERVER['HTTP_USER_AGENT'];//这里只进行IOS和Android两个操作系统的判断，其他操作系统原理一样
        $appVersion = 0;
        if (strpos($ua, 'Android') !== false) {//strpos()定位出第一次出现字符串的位置，这里定位为0
            preg_match("/(?<=Android )[\d\.]{1,}/", $ua, $version);
            $appVersion = $version[0];
        } elseif (strpos($ua, 'iPhone') !== false) {
            preg_match("/(?<=CPU iPhone OS )[\d\_]{1,}/", $ua, $version);
            $appVersion = str_replace('_', '.', $version[0]);
        } elseif (strpos($ua, 'iPad') !== false) {
            preg_match("/(?<=CPU OS )[\d\_]{1,}/", $ua, $version);
            $appVersion = str_replace('_', '.', $version[0]);
        }
        if ($appVersion > 4.3) {
            View::share('isV', false);
        } else {
            View::share('isV', true);
        }
        if ($args['tpl']) {
            return $this->display('info_' . $args['tpl']);
        }
        return $this->display();
    }

    /**
     * 货到付款
     */
    public function isDelivery()
    {
        $result = $this->requestApi('shop.isDelivery', Input::all());
        return Response::json($result);
    }

    /**
     * 营业状态
     */
    public function isStatus()
    {
        $result = $this->requestApi('shop.isStatus', Input::all());
        return Response::json($result);
    }

    /**
     * 营业状态
     */
    public function isDel()
    {
        $result = $this->requestApi('goodscate.del', Input::all());
        return Response::json($result);
    }

    /**
     * 添加分类
     */
    public function add()
    {

        View::share('title', '添加分类');
        View::share('type', 2);
        View::share('msg', '添加服务');
        View::share('nav_back_url', u('Seller/seller'));
        View::share('csss', "#seller_seller_view");
        $result = $this->requestApi('seller.trade');
        View::share('trade', $result['data']);
        return $this->display('edit');
    }

    /**
     * 添加分类
     */
    public function goodsadd()
    {
        View::share('title', '添加分类');
        View::share('type', 1);
        View::share('msg', '添加商品');
        View::share('nav_back_url', u('Seller/goodslists'));
        $result = $this->requestApi('seller.trade');
        View::share('trade', $result['data']);
        return $this->display('edit');
    }

    /**
     * 编辑分类
     */
    public function goodsedit()
    {
        $result = $this->requestApi('goodscate.getById', Input::all());
        View::share('data', $result['data']);
        $result = $this->requestApi('seller.trade');
        View::share('trade', $result['data']);
        View::share('title', '编辑分类 ');
        View::share('type', Input::get('type'));
        View::share('id', Input::get('id'));
        if (Input::get('type') == 1) {
            View::share('nav_back_url', u('Seller/goodslists'));
        } else {
            View::share('nav_back_url', u('Seller/seller'));
        }
        View::share('msg', '更新');
        return $this->display('edit');
    }

    /**
     * 编辑分类
     */
    public function saveedit()
    {
        $data = Input::all();
        $result = $this->requestApi('goodscate.edit', $data);
        return Response::json($result);
    }

    /**
     * 编辑分类
     */
    public function service()
    {
        View::share('title', '服务列表');
        $args = Input::all();
        $args['page'] = $args['page'] ? $args['page'] : 1;
        if (!$args['status']) {
            $args['status'] = 1;
        }
        $result = $this->requestApi('goods.lists', $args);
        View::share('goods', $result['data']);
        View::share('status', $args['status']);
        View::share('id', $args['id']);
        View::share('page', $args['page']);
        unset($args['page']);
        View::share('args', $args);
        View::share('nav_back_url', u('Seller/seller'));
        if ($args['tpl']) {
            return $this->display('service_' . $args['tpl']);
        }
        View::share('ajaxurl_page', "_" . $args['status']);
        return $this->display();
    }

    /**
     * 编辑分类
     */
    public function ajaxservice()
    {
        $args = Input::all();
        $args['page'] = $args['page'] ? $args['page'] : 1;
        if (!$args['status']) {
            $args['status'] = 1;
        }
        $result = $this->requestApi('goods.lists', $args);
        return Response::json($result['data']);
    }

    /**
     * 上下架商品
     */
    public function opgoods()
    {
        $args = Input::get();
        $args['ids'] = explode(',', $args['goodsId']);
        $result = $this->requestApi('goods.op', $args);
        return Response::json($result);
    }

    /**
     * 编辑分类
     */
    public function preview()
    {
        $args = Input::all();
        $result = $this->requestApi('goods.detail', ['id' => $args['id']]);
        if ($result['code'] == 0) {
            if ($result['unit'] == 1 && $result['type'] == 2) {
                $result['duration'] = $result['duration'] * 60;
            }
            View::share('data', $result);
            View::share('title', $result['seller']['name']);
        }
        return $this->display();
    }

    /**
     * 编辑分类
     */
    public function account()
    {
        $args = Input::all();
        $args['status'] = $args['status'] ? $args['status'] : 0;
        $args['type'] = $args['type'] ? $args['type'] : 1;
        $args['page'] = $args['page'] ? $args['page'] : 1;
        $result = $this->requestApi('shop.account', $args);
        View::share('account', $result['data']);

        $result = $this->requestApi('shop.info');
        View::share('info', $result['data']);
        $result = $this->requestApi('config.configByCode',['code'=>'seller_withdraw_day']);
        View::share('seller_withdraw_day',$result['data']);

        View::share('ajaxurl_page', "_" . $args['status']);

        View::share('acut', $args);
        unset($args['page']);
        View::share('args', $args);
        if ($args['tpl']) {
            return $this->display('account_' . $args['tpl']);
        }
        View::share('title', '我的账单');
        return $this->display();
    }

    /**
     * 编辑分类
     */
    public function ajaxaccount()
    {
        $args = Input::all();
        $args['type'] = 1;
        $result = $this->requestApi('shop.account', $args);
        return Response::json($result['data']);
    }

    /**
     * 编辑分类
     */
    public function ajaxwithdrawlog()
    {
        $args = Input::all();
        $args['type'] = 2;
        $args['status'] = 2;
        $args['page'] = $args['page'] ? $args['page'] : 2;
        $result = $this->requestApi('shop.account', $args);
        return Response::json($result['data']);
    }

    /**
     * 我要提现
     */
    public function carry()
    {
        $args = Input::all();
        View::share('title', '提现');
        $result = $this->requestApi('seller.getAccount');
        View::share('bank', $result['data']);
        if ($args['tpl']) {
            return $this->display('carry_' . $args['tpl']);
        }
        return $this->display();
    }

    /**
     * 提现
     */
    public function withdraw()
    {
        $args = Input::all();
        $result = $this->requestApi('user.withdraw', $args);
        return Response::json($result);
    }

    /**
     * 提现记录
     */
    public function withdrawlog()
    {
        $args = Input::all();
        $args['type'] = $args['type'] ? $args['type'] : 2;
        $args['status'] = $args['status'] ? $args['status'] : 2;
        $result = $this->requestApi('shop.account', $args);
        View::share('acut', $args);
        unset($args['page']);
        View::share('args', $args);
        View::share('account', $result['data']);
        View::share('title', '提现记录');

        if ($args['tpl']) {
            return $this->display('withdrawlog_' . $args['tpl']);
        }
        if (count($result['data']) == 20) {
            View::share('show_preloader', true);
        }
        return $this->display();
    }

    /**
     * 提现记录
     */
    public function analysis()
    {
        $args = Input::all();
        $args['days'] = $args['days'] ? $args['days'] : 1;
        $result = $this->requestApi('order.statistics', $args);
        View::share('args', $args);
        View::share('data', $result['data']);
        View::share('title', '经营分析');
        View::share('ajaxurl_page', "_" . $args['days']);
        return $this->display();
    }

    /**
     * 提现记录
     */
    public function recharge()
    {
        View::share('title', '我要充值');
        $payments = $this->getPayments();
//        print_r($payments);exit;
        unset($payments['cashOnDelivery']);
        unset($payments['balancePay']);
        unset($payments['unionpay']);
        unset($payments['unionapp']);
        unset($payments['weixinJs']);
        unset($payments['weixin']);
        unset($payments['weixinSeller']);
        View::share('payments', $payments);
        $nav_back_url = u('Seller/account');
        View::share('nav_back_url', $nav_back_url);
        return $this->display();
    }

    /**
     * 充值
     */
    public function pay()
    {
        $args = Input::all();
        if (isset($args['payment']) && $args['payment'] == 'weixinJs') {
            Session::put('wxpay_open_id', $args['openId']);
            Session::put('pay_payment', 'weixinJs');
            Session::save();
            return Redirect::to(u('Seller/pay', ['money' => $args['money']]));
        }

        if (!isset($args['payment'])) {
            $args['payment'] = Session::get('pay_payment');
            $args['openId'] = Session::get('wxpay_open_id');
        }
        $args['extend']['url'] = Request::fullUrl();
        $args['extend']['url'] = "http://www.niusns.com/payment/o2o.php";

        if (!empty($args['openId'])) {
            $args['extend']['openId'] = $args['openId'];
        }
        $pay = $this->requestApi('seller.recharge', $args);
        if ($pay['code'] == 0) {
            if (isset($pay['data']['payRequest']['html'])) {
                echo $pay['data']['payRequest']['html'];
                exit;
            }
        }
    }

    public function createpaylog()
    {
        $args = Input::all();
        $pay = $this->requestApi('seller.recharge', $args);
        die(json_encode($pay["data"]));
    }


    /**
     * 获取支持的支付方式
     * @return [type] [description]
     */
    protected function getPayments()
    {
        $type = 'web';
        if (preg_match("/\sMicroMessenger\/\\d/is", Request::header('USER_AGENT'))) {
            $type = 'wxweb';
        }
        if ($_COOKIE["app"] == "true") {
            $type = 'app';
        }

        $payments = Cache::get('payments');
        return $payments[$type];
    }

    /**
     * 添加商品
     */
    public function addnew()
    {
        $args = Input::all();

        //获取平台商品分类
        if ($args['tagPid'] > 0 && $args['tagId'] > 0) {
            $tag = $this->requestApi('systemTag.checktag', $args);
            if ($tag['data']) {
                View::share('tag', $tag['data']);
            }
        }

        if ($args['type'] == 2) {
            $title = "添加服务";
            $url = u('Seller/service', ['id' => $args['tradeId'], 'type' => $args['type'], 'tagPid' => $args['tagPid'], 'tagId' => $args['tagId']]);
            $css = "#seller_service_view_1";
        } else {
            $title = "添加商品";
            $args['type'] = 1;
            $url = u('Seller/goods', ['id' => $args['tradeId'], 'type' => $args['type'], 'tagPid' => $args['tagPid'], 'tagId' => $args['tagId']]);
            $css = "#seller_goods_view_1";
        }

        //获取商家是否是全国店
        $seller = $this->requestApi('shop.info');
        View::share('storeType', $seller['data']['storeType']);

        View::share('css', $css);
        View::share('nav_back_url', $url);
        View::share('title', $title);
        View::share('args', $args);
        View::share('showData', Cache::get($this->staffId));
        return $this->display();
    }

    /**
     * 编辑商品
     */
    public function editnew()
    {
        $args = Input::all();

        if ($args['type'] == 2) {
            $title = "编辑服务";
            $url = u('Seller/service', ['id' => $args['tradeId'], 'type' => $args['type'], 'tagPid' => $args['tagPid'], 'tagId' => $args['tagId']]);
            $css = "#seller_service_view_1";
        } else {
            $title = "编辑商品";
            $args['type'] = 1;
            $url = u('Seller/goods', ['id' => $args['tradeId'], 'type' => $args['type'], 'tagPid' => $args['tagPid'], 'tagId' => $args['tagId']]);
            $css = "#seller_goods_view_1";
        }
        View::share('nav_back_url', $url);
        View::share('css', $css);
        $result = $this->requestApi('goods.detail', ['id' => $args['id']]);
        if ($result['code'] == 0) {
            if ($result['unit'] == 1 && $result['type'] == 2) {
                $result['duration'] = $result['duration'] * 60;
            }

            View::share('data', $result);
        }

        //获取平台商品分类
        if ($args['tagPid'] > 0 && $args['tagId'] > 0) {
            $tag = $this->requestApi('systemTag.checktag', $args);
            if ($tag['data']) {
                View::share('tag', $tag['data']);
            }
        } else {
            $tag = $result['systemTagListId'];
            $tag['pid'] = $result['systemTagListPid'];
            View::share('tag', $tag);
        }

        //获取商家是否是全国店
        $seller = $this->requestApi('shop.info');
        View::share('storeType', $seller['data']['storeType']);

        View::share('title', $title);
        View::share('type', $args['type']);
        View::share('args', $args);
        View::share('showData', Cache::get($this->staffId));
        return $this->display('addnew');
    }


    /**
     * 编辑商品
     */
    public function editnewsystem()
    {
        $args = Input::all();
        $result = $this->requestApi('goods.systemGoodsEdit', ['id' => $args['id']]);
        if ($result['code'] == 0) {
            View::share('data', $result['data']);
            $showargs['tagPid'] = $result['data']['systemTagListPid'];
            $showargs['tagId'] = $result['data']['systemTagListId'];
            $tag = $this->requestApi('systemTag.checktag', $showargs);
            if ($tag['data']) {
                View::share('tag', $tag['data']);
            }
        }
        View::share('title', "添加平台商品");
        View::share('type', $args['type']);
        View::share('args', $args);
        View::share('nopre', "nopre");
        View::share('css', "seller_getTagLists_view");
        return $this->display('editnewsystem');
    }

    /**
     * 添加商品
     */
    public function goodsSave()
    {
        $args = Input::all();
        if ($args['type'] == 1) {
            $arr = [];
            foreach ($args['norms'] as $v) {
                $arr[] = $v['name'];
            }
            if (count($arr) != count(array_unique($arr))) {
                return Response::json(['code' => -1, 'msg' => '商品型号不能重复']);
            }
        } else {
            $args['staffs'] = explode(',', $args['staffId']);
        }
        $result = $this->requestApi('goods.edit', $args);
        Cache::put($this->staffId, "");
        return Response::json($result);
    }

    /**
     * 阅读单条消息
     */
    public function msgshow()
    {
        $args = Input::all();
        if (!is_array($args['id'])) {
            $args['id'] = (int)$args['id'];
        }
        $this->requestApi('msg.read', $args);
        $list = $this->requestApi('msg.getdata', $args);
        View::share('nav', 'msg');
        View::share('data', $list);
        return $this->display();
    }

    /**
     * 批量阅读消息
     */
    public function readMsg()
    {
        $args = Input::all();
        if (!is_array($args['id'])) {
            $args['id'] = (int)$args['id'];
        }
        $result = $this->requestApi('msg.read', $args);
        die(json_encode($result));
    }

    /**
     * 批量删除消息
     */
    public function deleteMsg()
    {
        $args = Input::all();
        if (!is_array($args['id'])) {
            $args['id'] = (int)$args['id'];
        }
        $result = $this->requestApi('msg.delete', $args);
        die(json_encode($result));
    }

    /**
     * 意见反馈
     */
    public function feedback()
    {
        View::share('title', '意见反馈');
        View::share('nav_back_url', u('Mine/index'));
        return $this->display();
    }

    /**
     * 增加意见反馈
     */
    public function addfeedback()
    {
        $content = strip_tags(Input::get('content'));
        $result = $this->requestApi('feedback.create', ['content' => $content, 'deviceType' => 'wap']);
        die(json_encode($result));
    }


    /**
     * [reg 修改密码]
     */
    public function repwd()
    {
        View::share('staff', $this->staff);
        View::share('title', '修改密码');
        View::share('nav_back_url', u('Mine/index'));
        return $this->display();
    }

    /**
     * [doreg 执行修改密码]
     */
    public function dorepwd()
    {
        $data = Input::all();
        $data['type'] = 'repwd';
        $result = $this->requestApi('user.repwd', $data);
        if ($result['code'] == 0) {
            Session::set('staff', '');
            $this->setSecurityToken(null);
        }
        die(json_encode($result));
    }

    /**
     * [ 营业时间]
     */
    public function time()
    {
        $data = Input::all();
        View::share('title', '营业时间');
        $result = $this->requestApi('shop.time', $data);
        View::share('data', $result['data']);
        return $this->display();
    }

    /**
     * [ 修改营业时间]
     */
    public function savetime()
    {
        $data = Input::all();
        View::share('title', '营业时间');
        $result = $this->requestApi('shop.savetime', $data);
        return Response::json($result);
    }

    /**
     * [评价列表]
     */
    public function evaluation()
    {
        $data = Input::all();
        $data['type'] = $data['type'] ? $data['type'] : 1;
        $data['page'] = $data['page'] ? $data['page'] : 1;
        View::share('title', '评价管理');
        $result = $this->requestApi('seller.evalist', $data);

        $eva = [];
        foreach ($result['data']['eva'] as $key => $value) {
            $eva[$value['orderId']][] = $value;  
        }
        $result['data']['eva'] = $eva;

        View::share('evaluation', $result['data']);
        unset($data['page']);
        View::share('args', $data);
        if (count($result['data']['eva']) == 20) {
            View::share('show_preloader', true);
        }
        View::share('ajaxurl_page', "_" . $data['type']);
        View::share('seller', $this->staff);

        if($this->storeType == 1)
        {
            if ($data['tpl']) {
                return $this->display('rate_all_' . $data['tpl']);
            }
        }
        else
        {
            if ($data['tpl']) {
                return $this->display('rate_' . $data['tpl']);
            }
        }

        
        return $this->display();
    }

    /**
     * [评价列表]
     */
    public function ajaxevaluation()
    {
        $data = Input::all();
        $data['type'] = $data['type'] ? $data['type'] : 1;
        $data['page'] = $data['page'] ? $data['page'] : 1;
        $result = $this->requestApi('seller.evalist', $data);
        return Response::json($result['data']);
    }

    /**
     * [评价回复]
     */
    public function saveevaluation()
    {
        $data = Input::all();
        $result = $this->requestApi('seller.evareply', $data);
        return Response::json($result);
    }

    /**
     * [店铺简介]
     */
    public function brief()
    {
        View::share('title', '店铺简介');
        return $this->display();
    }

    /**
     * [店铺公告]
     */
    public function announcement()
    {
        $result = $this->requestApi('shop.info');
        View::share('data', $result['data']);
        View::share('title', '店铺公告');
        return $this->display();
    }

    /**
     * [保存店铺简介]
     */
    public function savebrief()
    {
        $brief = Input::get('brief');
        $args['shopdatas']['brief'] = $brief;
        $result = $this->requestApi('shop.edit', $args);
        return Response::json($result['data']);
    }

    /**
     * [保存店铺公告]
     */
    public function savearticle()
    {
        $article = Input::get('article');
        $args['shopdatas']['article'] = $article;
        $result = $this->requestApi('shop.edit', $args);
        return Response::json($result['data']);
    }

    /**
     * [配送时间]
     */
    public function delivery()
    {
        View::share('title', '配送时间');
        return $this->display();
    }

    /**
     * [配送时间]
     */
    public function savedelivery()
    {
        $data = Input::all();
        $args['shopdatas']['deliveryTime'] = $data;
        $result = $this->requestApi('shop.edit', $args);
        return Response::json($result['data']);
    }

    /**
     * [店铺名称]
     */
    public function name()
    {
        View::share('title', '店铺名称');
        return $this->display();
    }

    /**
     * [联系电话]
     */
    public function tel()
    {
        View::share('title', '联系电话');
        return $this->display();
    }

    /**
     * [起送价]
     */
    public function serviceFee()
    {
        View::share('title', '起送价');
        return $this->display();
    }


    /**
     * [保存店铺公告]
     */
    public function savename()
    {
        $name = Input::get('name');
        $args['shopdatas']['name'] = $name;
        $result = $this->requestApi('shop.edit', $args);
        return Response::json($result['data']);
    }

    /**
     * [保存电话]
     */
    public function savetel()
    {
        $name = Input::get('tel');
        $args['shopdatas']['tel'] = $name;
        $result = $this->requestApi('shop.edit', $args);
        return Response::json($result);
    }

    /**
     * [保存电话]
     */
    public function savefee()
    {
        $name = Input::get('serviceFee');
        $args['shopdatas']['serviceFee'] = $name;
        $result = $this->requestApi('shop.edit', $args);
        return Response::json($result['data']);
    }

    /**
     * [保存配送费]
     */
    public function savedeliveryfee()
    {
        $name = Input::get('deliveryFee');
        $isAvoidFee = Input::get('isAvoidFee');
        $avoidFee = $isAvoidFee?Input::get('avoidFee'):0;
        $args['shopdatas']['deliveryFee'] = $name;
        $args['shopdatas']['isAvoidFee'] = $isAvoidFee;
        $args['shopdatas']['avoidFee'] = $avoidFee;
        $result = $this->requestApi('shop.edit', $args);
        return Response::json($result['data']);
    }

    /**
     * [保存电话]
     */
    public function deliveryFee()
    {
        $result = $this->requestApi('shop.info');
        View::share('seller',$result['data']);
        View::share('title', '配送费');
        return $this->display();
    }

    /**
     * [保存电话]
     */
    public function map()
    {

        $result = $this->requestApi('shop.info');
        View::share('data', $result['data']);
        View::share('title', '服务范围');
        return $this->display();
    }

    /**
     * 选择配送人员
     */
    public function staff()
    {
        $type = (int)Input::get('type');
        $staffId = explode(',', Input::get('staffId'));
        $title = $type == 1 ? '选择配送人员' : '选择服务人员';
        $result = $this->requestApi('order.stafflist', ['type' => $type]);
        View::share('list', $result['data']);
        View::share('title', $title);
        View::share('staffId', $staffId);
        return $this->display();
    }

    /**
     * 服务范围
     */
    public function sellermap()
    {
        $result = $this->requestApi('shop.sellermap', Input::all());
        return Response::json($result['data']);
    }

    /**
     * 城市
     */
    public function region()
    {
        View::share('title', '所在城市');
        $result = $this->requestApi('config.getOpenCitys');
        View::share('region', $result['data']);
        return $this->display();
    }


    /**
     * 城市
     */
    public function sellerbrief()
    {
        View::share('title', '店铺简介');
        View::share('brief', 'brief');
        return $this->display();
    }

    /**
     * [保存配送费]
     */
    public function savecity()
    {
        $data = Input::get('data');
        $args['shopdatas'] = $data;
        $result = $this->requestApi('shop.edit', $args);
        return Response::json($result['data']);
    }

    /**
     * [保存logo]
     */
    public function savelogo()
    {
        $data = Input::all();
        $args['shopdatas'] = $data;
        $result = $this->requestApi('shop.edit', $args);
        return Response::json($result['data']);
    }


    /**
     * [authCode 订单消费码验证界面]
     * @return [type] [description]
     */
    public function authCode()
    {
        return $this->display();
    }

    /**
     * [checkCode 验证消费码，获取订单编号]
     * @return [type] [description]
     */
    public function checkCode()
    {
        $args = Input::all();
        $result = $this->requestApi('order.checkcode', $args);
        return Response::json($result);
    }

    public function orderAuthCode()
    {
        $args = Input::all();

        $result = $this->requestApi('order.detail', Input::all());
        if ($result['data']) {
            View::share('data', $result['data']);
        } else {
            return $this->error("订单获取失败");
        }
        View::share('title', '订单详情');
        View::share('tpl', 'Order');
        $args = Input::all();
        View::share('args', $args);
        if ($args['url_css']) {
            View::share('nav_back_url', u('Seller/evaluation'));
            View::share('url_css', $args['url_css']);
        } else {
            View::share('nav_back_url', u('Order/index'));
            View::share('url_css', "#order_index_view_1");
        }
        return $this->display();
    }

    //平台商品分类
    public function getTagLists()
    {
        $args = [];
        $args['status'] = 1;
        $result = $this->requestApi('systemTag.lists', $args);
        //排序
        $data = [];
        foreach ($result['data'] as $key => $value) {

            if ($value['pid'] == 0) {
                //  一级分类
                if(!isset($data[$value['id']]))
                {
                    $data[$value['id']] = $value;
                }else{
                    $data[$value['id']] = array_merge($data[$value['id']], $value);
                }
            } else {
                //二级分类
                if ($value['tag']['id'] > 0) {
                    //存在二级分类标签
                    if (!$data[$value['pid']]['twoLevel'][$value['tag']['id']]) {
                        $data[$value['pid']]['twoLevel'][$value['tag']['id']] = $value['tag'];
                    }

                    //三级分类
                    $data[$value['pid']]['twoLevel'][$value['tag']['id']]['threeLevel'][$value['id']] = $value;
                    unset($data[$value['pid']]['twoLevel'][$value['tag']['id']]['threeLevel'][$value['id']]['tag']);

                } else {
                    //不存在二级分类标签
                    if (!$data[$value['pid']]['twoLevel'][0]) {
                        $data[$value['pid']]['twoLevel'][0] = $value['tag'];
                    }
                    //三级分类
                    $data[$value['pid']]['twoLevel'][0]['threeLevel'][$value['id']] = $value;
                    unset($data[$value['pid']]['twoLevel'][0]['threeLevel'][$value['id']]['tag']);
                }

            }
        }
        $args = Input::all();
        View::share('data', $data);
        View::share('args', $args);

        if ($args['tpl'] == "system") {
            return $this->display("system");
        } else {
            View::share('nav_back_url', $_SERVER["HTTP_REFERER"]);
            return $this->display();
        }
    }


    //商品添加类型
    public function systemgoods()
    {
        $data = Input::all();
        View::share('data', $data);
        View::share('title', "选择类型");
        Cache::put($this->staffId, "");
        return $this->display();
    }

    //商品列表
    public function commoditytag()
    {
        $args = Input::all();
        $result = $this->requestApi('goods.systemGoodslists', $args);
        View::share('data', $result['data']['list']);
        View::share('tradeId', $args['tradeId']);
        if ($args['tpl']) {
            return $this->display('commodity_' . $args['tpl']);
        }
        View::share('title', "商品列表");
        return $this->display();
    }

    //商品列表
    public function commodity()
    {
        $args = Input::all();
        $result = $this->requestApi('goods.systemGoodslists', $args);
        View::share('data', $result['data']['list']);
        View::share('tradeId', $args['tradeId']);
        View::share('args', $args);
        if ($args['tpl']) {
            return $this->display('commodity_' . $args['tpl']);
        }
        View::share('title', "商品列表");
        return $this->display();
    }

    /**
     * [保存logo]
     */
    public function showData()
    {
        $data = Input::all();
        Cache::put($this->staffId, $data, 5);
        return;
    }

    /**
     * 商家活动
     */
    public function activity()
    {
        $result = $this->requestApi('Activity.lists');
        if ($result['code'] == 0) {
            View::share('list', $result['data']);
        }

        View::share('title', "营销中心");
        return $this->display();
    }

    /**
     * 查看活动
     */
    public function activityInfo()
    {
        $args = Input::all();
        $data = $this->requestApi('Activity.get', $args);
        if ($data['code'] == 0) {
            View::share('data', $data['data']);
        }

        if (!function_exists('array_column')) {
            $ids = \YiZan\Http\Controllers\YiZanViewController::array_column($data['data']['activityGoods'], 'goodsId');
        } else {
            $ids = array_column($data['data']['activityGoods'], 'goodsId');
        }
        $goodsList = $this->requestApi('goods.activityLists', ['ids' => $ids]); //获取选择的商品信息

        if ($goodsList['code'] == 0) {
            View::share('goodsList', $goodsList['data']);
        }

        View::share('title', "查看活动");
        return $this->display();
    }

    /**
     * 添加满减活动
     */
    public function activityAddFull()
    {
        //获取上月客单价
        $result = $this->requestApi('statistics.revenue');
        if ($result['code'] == 0) {
            View::share('data', $result['data']);
        }
        View::share('title', "满减活动");
        return $this->display();
    }

    /**
     * 添加特价商品活动(活动页)
     */
    public function activityAddSpecial()
    {
        $specialGoods = Session::get('SpecialGoods');
        $ids = !empty($specialGoods) ? $specialGoods : [];
        $result = $this->requestApi('goods.activityLists', ['ids' => $ids]);  //活动商品
        if ($result['code'] == 0) {
            View::share('goodsList', $result['data']);
        }

        View::share('data', Session::get('SpecialData'));  //活动详细
        View::share('title', "商品特价");
        return $this->display();
    }

    /**
     * 添加特价商品(商品页)
     */
    public function activitySpecialGoods()
    {
        $args = Input::all();

        $specialGoods = Session::get('SpecialGoods');
        $args['notIds'] = !empty($specialGoods) ? $specialGoods : [];

        $result = $this->requestApi('goods.activityAllLists', $args);

        $hasGoods = $this->requestApi('goods.hasActivityGoodsIds');
        $hasGoods = $hasGoods['data'];
        //排除已经存在的商品
        foreach ($result['data'] as $key => $value) {
            if (in_array($value['id'], $hasGoods)) {
                $result['data'][$key]['checked_disabled'] = 1;
            }
        }

        if ($result['code'] == 0) {
            View::share('list', $result['data']);
        }

        View::share('title', "添加商品");
        View::share('show_preloader', false);
        View::share('args', $args);
        if ($args['tpl']) {
            return $this->display("activityspecialgoods_item");
        }
        return $this->display();
    }

    /**
     * 闪存以选择的商品
     */
    public function activitySaveSpecialGoods()
    {
        $args = Input::all();

        $ids1 = !empty($args['ids']) ? $args['ids'] : [];
        $specialGoods = Session::get('SpecialGoods');
        $ids2 = !empty($specialGoods) ? $specialGoods : [];
        $ids = array_merge($ids1, $ids2);

        Session::put('SpecialGoods', $ids);
        Session::save();

        return 1;
    }

    /**
     * 删除已选择的商品
     */
    public function activityDeleteSpecialGoods()
    {
        $args = Input::all();
        $SpecialGoods = Session::get('SpecialGoods');

        foreach ($SpecialGoods as $key => $value) {
            if ($value == $args['id']) {
                unset($SpecialGoods[$key]);
            }
        }
        Session::put('SpecialGoods', $SpecialGoods);
        Session::save();
        return 1;
    }


    /**
     * 保存满减活动
     */
    public function activitySaveFull()
    {
        $args = Input::all();
        $result = $this->requestApi('Activity.activityFull', $args);
        if ($result['code'] == 0) {
            //清除数据
            Session::put('SpecialGoods', null);
            Session::put('SpecialData', null);
            Session::save();
        }
        return Response::json($result);
    }

    /**
     * 闪存特价商品活动信息
     */
    public function activitySaveSpecialData()
    {
        $args = Input::all();
        Session::put('SpecialData', $args);
        Session::save();

        return 1;
    }

    /**
     * 保存特价商品活动
     */
    public function activitySaveSpecial()
    {
        $args = Input::all();
        $args['ids'] = Session::get('SpecialGoods');
        $result = $this->requestApi('Activity.activitySpecial', $args);

        if ($result['code'] == 0) {
            //清除数据
            Session::put('SpecialGoods', null);
            Session::put('SpecialData', null);
            Session::save();
        }
        return Response::json($result);
    }

    /**
     * 作废
     */
    public function cancellation()
    {
        $args = Input::all();
        $result = $this->requestApi('Activity.cancellation', $args);
        return Response::json($result);
    }

    /**
     * 银行卡
     */
    public function bank()
    {
        $args = Input::all();
        if ($args['id'] == "") {
            View::share('title', "绑定银行卡");
            View::share('url', 'account');
        } else {
            $result = $this->requestApi('seller.getbankinfo', $args);
            if ($result['code'] == 0) {
                if ($args['verifyCode']) {
                    View::share('verifyCode', $args['verifyCode']);
                    View::share('data', $result['data']['old']);
                    View::share('old', false);
                } else {
                    unset($result['data']['old']);
                    View::share('data', $result['data']);
                    View::share('old', true);
                }
                View::share('title', "编辑银行卡");
                View::share('url', 'carry');
            } else {
                View::share('title', "绑定银行卡");
                View::share('url', 'account');
            }
        }
        return $this->display();
    }

    /**
     * 银行卡
     */
    public function bankSve()
    {
        $args = Input::all();
        $result = $this->requestApi('seller.savebankinfo', $args);
        return Response::json($result);
    }

    /**
     *
     */
    public function verifyCode()
    {
        $args = Input::all();
        $result = $this->requestApi('seller.getbankinfo', $args);
        View::share('data', $result['data']);
        View::share('title', "短信验证");
        return $this->display();
    }

    /**
     * 检查银行卡短信
     */
    public function verifyCodeCk()
    {
        $args = Input::all();
        $result = $this->requestApi('seller.verifyCodeCk', $args);
        return Response::json($result);
    }

    /**
     * [freight 运费列表]
     * @return [type] [description]
     */
    public function freightList()
    {
        //清空模版
        Session::set('saveCheckLocation', NULL);

        $res1 = $this->requestApi('seller.freightList', ['isDefault' => 1]);
        $res2 = $this->requestApi('seller.freightList', ['isDefault' => 0]);

        if ($res1['code'] == 0) {
            $list['default'] = $res1['data'][0];
        }
        if ($res2['code'] == 0) {
            foreach ($res2['data'] as $key => $value) {
                $list['other'][$key]['city'] = $value['city'];
                $list['other'][$key]['data'] = $value;
            }
        }

        //获取城市
        $city = $this->requestApi('city.getCityList', ['pid' => 0, 'level' => 1]);
        $cityList = [];
        foreach ($city['data'] as $key => $value) {
            $cityList[$value['id']] = $value['name'];
        }
        foreach ($list['other'] as $key => $value) {
            $first = true;
            foreach ($value['city'] as $k => $v) {
                if ($first) {
                    $list['other'][$key]['cityName'] = $cityList[$k];
                    $first = false;
                } else {
                    $list['other'][$key]['cityName'] .= ',' . $cityList[$k];
                }

            }
        }
        View::share('list', $list);
        return $this->display();
    }

    /**
     * [freightUpdate 运费修改]
     * @return [type] [description]
     */
    public function freightUpdate()
    {
        //读取数据库模版
        $res1 = $this->requestApi('seller.freightList', ['isDefault' => 1]);
        $res2 = $this->requestApi('seller.freightList', ['isDefault' => 0]);

        if ($res1['code'] == 0) {
            $list['default'] = $res1['data'][0];
        }
        if ($res2['code'] == 0) {
            foreach ($res2['data'] as $key => $value) {
                $list['other'][$value['id']]['city'] = $value['city'];
                $list['other'][$value['id']]['data'] = $value;
            }

        }

        //将数据库模版转换成缓存模版格式
        // $data = [];
        // foreach ($list['other'] as $key => $value) {
        //     $data[$value['data']['id']]['data']['num'] = $value['data']['num'];
        //     $data[$value['data']['id']]['data']['money'] = $value['data']['money'];
        //     $data[$value['data']['id']]['data']['addNum'] = $value['data']['addNum'];
        //     $data[$value['data']['id']]['data']['addMoney'] = $value['data']['addMoney'];
        //     $data[$value['data']['id']]['city'] = $value['city'];
        // }

        $keys = array_keys($list['other']);

        //读取缓存模版
        $model = Session::get('saveCheckLocation');

        foreach ($model as $key => $value) {
            //如果缓存存在数据库的相同模版 保留缓存数据 删除数据库存在数据
            if (in_array($key, $keys)) {
                $id = $list['other'][$key]['data']['id'];
                unset($list['other'][$key]);
                $list['other'][$key] = $value;
                $list['other'][$key]['data']['id'] = $id;
            } else {
                $list['other'][$key] = $value;
            }

        }
        //合并数据库+缓存模版
        // $list['other'] += $data;

        //存储模版
        Session::set('saveCheckLocation', $list['other']);

        //获取城市
        $city = $this->requestApi('city.getCityList', ['pid' => 0, 'level' => 1]);
        $cityList = [];
        foreach ($city['data'] as $key => $value) {
            $cityList[$value['id']] = $value['name'];
        }

        foreach ($list['other'] as $key => $value) {
            $first = true;
            foreach ($value['city'] as $k => $v) {
                if ($first) {
                    $list['other'][$key]['cityName'] = $cityList[$k];
                    $first = false;
                } else {
                    $list['other'][$key]['cityName'] .= ',' . $cityList[$k];
                }

            }
        }
        View::share('list', $list);
        View::share('cityList', $cityList);

        return $this->display();
    }

    /**
     * [saveFreight 保存运费模版]
     * @return [type] [description]
     */
    public function saveFreight()
    {
        $args = Input::all();

        foreach ($args['data'] as $key => $value) {
            if ($key > 0) {
                $args['data'][$key][0] = array_flatten(unserialize(trim($value[0]))); //扁平化二维数组
            }
        }
        $result = $this->requestApi('seller.saveFreight', $args);

        if ($result['code'] == 0) {
            //删除闪存数据
            Session::set('saveCheckLocation', null);
            Session::save();
        }

        return Response::json($result);
    }

    /**
     * 删除运费模版
     */
    public function deleteFreight()
    {
        $args = Input::all();
        $result = $this->requestApi('seller.deleteFreight', $args);
        return Response::json($result);
    }

    /**
     * 保存运费模版
     */
    public function saveModel()
    {
        $args = Input::all();

        $selected = Session::get('saveCheckLocation');

        $data = [];
        foreach ($args['model'] as $key => $value) {
            $data[$value[0]]['data']['num'] = $value[1];
            $data[$value[0]]['data']['money'] = $value[2];
            $data[$value[0]]['data']['addNum'] = $value[3];
            $data[$value[0]]['data']['addMoney'] = $value[4];
            $data[$value[0]]['city'] = $selected[$value[0]]['city'];
        }
        //保存模版
        Session::set('saveCheckLocation', $data);
        Session::save();

        // dd(Session::get('saveCheckLocation'));
    }

    /**
     * [checkLocation 一级城市选择]
     * @return [type] [description]
     */
    public function checkLocation()
    {
        $args = Input::all();
        //获取开通城市
        $result = $this->requestApi('city.getCityList', ['pid' => 0, 'level' => 1]);
        if ($result['code'] == 0) {
            $lists = $this->letterAsc($result['data'], $args['modelId']);
            View::share("lists", $lists);
        }

        View::share("args", $args);
        return $this->display();
    }

    /**
     * [checkLocationSecondLevel 二级城市选择]
     * @return [type] [description]
     */
    public function checkLocationSecondLevel()
    {
        $args = Input::all();
        $result = $this->requestApi('city.getCityList', ['pid' => $args['pid'], 'level' => 2]);
        if ($result['code'] == 0) {
            $lists = $this->letterAsc($result['data'], $args['modelId']);
            View::share("lists", $lists);
        }

        View::share('args', $args);
        return $this->display();
    }

    /**
     * [saveCheckLocation 保存已选地址]
     * @return [type] [description]
     */
    public function saveCheckLocation()
    {
        $args = Input::all();
        $data = Session::get('saveCheckLocation');

        if ($args['pid'] > 0) {
            //保存二级城市编号
            if ($args['ids']) {
                $data[$args['modelId']]['city'][$args['pid']] = $args['ids'];
            } else {
                unset($data[$args['modelId']]['city'][$args['pid']]);
            }

        } else {
            //清除一级城市编号
            foreach ($data[$args['modelId']]['city'] as $key => $value) {
                if (gettype($value) == "string") {
                    unset($data[$args['modelId']]['city'][$key]);
                }
            }
            //保存一级城市编号
            foreach ($args['ids'] as $key => $value) {
                if ($value)
                    $data[$args['modelId']]['city'][$value] = $value;
            }
        }

        Session::set('saveCheckLocation', $data);
        Session::save();
    }

    /**
     * 删除已选地址
     */

    /**
     * [letterAsc 按字母排序]
     * @param  [type] $lists [description]
     * @return [type]        [description]
     */
    public function letterAsc($data, $modelId)
    {
        $lists = [];
        $selected = Session::get('saveCheckLocation');
        $selected = $selected[$modelId]['city'];

        foreach ($data as $key => $value) {
            if ($value['pid'] > 0) {
                $value['selected'] = in_array($value['id'], $selected[$value['pid']]);  //是否选择（二级）
            } else {
                if (gettype($selected[$value['id']]) == "string") {
                    $value['allselected'] = true;   //全选（一级）
                } else {
                    $value['selected'] = array_key_exists($value['id'], $selected); //非全选（一级）
                }
            }
            $lists[$value['firstChar']][] = $value;
        }

        ksort($lists);
        return $lists;
    }

    /**
     * 画布弄图片
     */
    public function shopdetail()
    {
        View::share('staff', $this->staff);
        //获取seller信息
        $args = Input::all();
        $seller = $this->requestApi('seller.detail', $args);
        View::share('seller', $seller['data']);

        $this->cancode();

        $img = Image::canvas(256, 450, '#fff');

        $images = 'StaffD2' . $this->staff['sellerId'] . '.png';
        $outfile = base_path() . '/public/code/' . $images; //输出图片位置

        $imageurl = u().'/code/' . $images;
        View::share('imageurl', $imageurl);

        /**
         * 加字
         */
        $img->text('扫描二维码快速直达店铺', 128, 10, function ($font) {
            $font->file(base_path() . '/resources/fonts/msyh.ttf');
            $font->size(20);
            $font->color('#000');
            $font->valign('top');
            $font->align('center');
        });

        /**
         * 加图
         */
        $logo = formatImage($seller['data']['logo'], 100, 100);
        $img->insert($logo, 'top', 15, 65);

        /**
         * 加BG图
         */
        $bg = base_path() . '/public/images/quanbg.png';
        $img->insert($bg, 'top', 11, 61);

        $seller['data']['name'] = str_limit($seller['data']['name'], 15, '...');
        /**
         * 加BG图
         */
        $img->text($seller['data']['name'], 128, 191, function ($font) {
            $font->file(base_path() . '/resources/fonts/msyh.ttf');
            $font->size(14);
            $font->color('#000');
            $font->valign('center');
            $font->align('center');
        });

        /**
         * 加logo
         */
        $img->insert($outfile, 'top', 170, 215);

        $site_name = $this->getConfig('site_name');
        /**
         * 加字
         */
        $img->text('打开' . $site_name . '扫一扫', 128, 435, function ($font) {
            $font->file(base_path() . '/resources/fonts/msyh.ttf');
            $font->size(12);
            $font->color('#666');
            $font->align('left');
            $font->valign('center');
            $font->align('center');
        });
        $img->save($outfile);

        View::share('images', $images);

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $protocol.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
        $weixin_arrs = $this->requestApi('invitation.getweixin',array('url' => $url));
        if($weixin_arrs['code'] == 0){
            View::share('weixin',$weixin_arrs['data']);
        }
        $share = [
            'title'		=>	$seller['data']['name'],
            'content'	=>	$seller['data']['brief'],
            'url'		=>	u('wap#Seller/detail', ['id' => $this->staff['sellerId'], 'type' => 'seller']),
            'logo'		=> 	$seller['data']['logo'],
        ];
        View::share("share", $share);

        return $this->display();
    }



    public function cancode()
    {
        $value = u('wap#Seller/detail', ['id' => $this->staff['sellerId'], 'type' => 'seller']);

        $errorCorrectionLevel = 'L';//容错级别
        $matrixPointSize = 6;//生成图片大小
        $backColor = 0xFFFFFF; //背景色
        $foreColor = 0x000000; //前景色
        $margin = 1; //边距
        $QR = '';

        $images = 'StaffD2' . $this->staff['sellerId'];
        $outfile = base_path() . '/public/code/' . $images . '.png'; //输出图片位置
        $QR = '';//asset('wap/community/newclient/images/ewm.png'); //输出图片位置
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

    /**
     * 编辑退款地址
     */
    public function refundaddress()
    {
        $refundaddress = Input::get('refundaddress');

        View::share('refundaddress', $refundaddress);
        View::share('title', '编辑退货地址');
        return $this->display();
    }

    /**
     * 保存退款地址
     */
    public function saveRefundaddress()
    {
        $refundaddress = Input::get('refundaddress');
        $args['shopdatas']['refundAddress'] = $refundaddress;
        $result = $this->requestApi('shop.edit', $args);
        return Response::json($result);
    }

    /**
     * [sendset 配送设置]
     * @return [type] [description]
     */
    public function sendset() {
        $result = $this->requestApi('seller.sendsetget');

        if($result['code'] == 0)
        {
            $result = $result['data'];
            $sendWayStr =[
                1 => '商家配送',
                2 => '到店消费',
                3 => '到店自提',
            ];
            $sendTypeStr = [
                1 => '配送托管',
                2 => '平台众包',
            ];

            $sendWayStr = array_where($sendWayStr, function($key, $value) use($result)
            {
                return in_array($key, $result['sendWay']);
            });

            $result['sendWayStr'] = implode(',', $sendWayStr);
            $result['sendTypeStr'] = $sendTypeStr[$result['sendType']];

            View::share('data', $result);
        }

        //获取平台配送设置
        $system_send_staff_fee = $this->requestApi('config.configByCode', ['code'=>'system_send_staff_fee']);
        View::share('system_send_staff_fee', $system_send_staff_fee['data']);
        return $this->display();
    }

    /**
     * 保存配送设置
     */
    public function sendsetSave() {
        $args = Input::all();
        $args['sendWay'] = explode(',', $args['sendWay']);
        if(!in_array(1, $args['sendWay']))
        {
            $args['sendType'] = null;
        }

        $result = $this->requestApi('seller.sendsetSave', $args);
        return Response::json($result);
    }

}