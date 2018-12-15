<?php
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\AdvService;
use YiZan\Services\Buyer\ArticleService;
use YiZan\Services\SellerService;
use YiZan\Services\RegionService;
use YiZan\Services\PaymentService;
use YiZan\Services\Buyer\GoodsService;
use YiZan\Services\SystemConfigService;
use YiZan\Services\SellerCateService;
use YiZan\Services\MenuService;
use YiZan\Services\InvitationService;
use YiZan\Services\Buyer\IndexNavService;
use YiZan\Services\SpecialService;
use YiZan\Models\SellerCate;
use YiZan\Services\GoodsCateService;
use YiZan\Utils\Http;
use Input;
/**
 * 配置
 */
class ConfigController extends BaseController {
    /**
     * 首页轮播广告
     */
    public function banners() {
        $data = AdvService::getAdvByCode('BUYER_INDEX_BANNER', (int)$this->request('cityId'));
        return $this->outputData($data);
    }


    /**
     * 首页分类
     */
    public function categorys() {
        $data = AdvService::getAdvByCode('BUYER_INDEX_MENU', (int)$this->request('cityId'));

        foreach($data as $key => $value)
        {
            if($value['type'] == 4)
            {
                $data[$key]["arg"] = u('wap#Article/detailapp',array('id'=>$value['arg']));
            }
        }

        return $this->outputData($data);
    }

    public function index()
    {
        $is_show_top = true;//判断是否手机
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $iphone = (strpos($agent, 'iphone')) ? true : false;
        $ipad = (strpos($agent, 'ipad')) ? true : false;
        $android = (strpos($agent, 'android')) ? true : false;
        if($iphone || $ipad || $android) {
            $is_show_top = false;
        }
        $codeType = $this->request('codeType') != "" ? $this->request('codeType') : "BUYER_INDEX_BANNER";
        $banner = AdvService::getAdvByCode($codeType, (int)$this->request('cityId'));
        if($codeType == "BUYER_SYSTEM_ONESELF"){
            $codeType = "BUYER_SYSTEM_ONESELF_MENU";
        }else{
            $codeType = "BUYER_INDEX_MENU";
        }
        $notice = AdvService::getAdvByCode($codeType, (int)$this->request('cityId'));
        $adv = AdvService::getAdvByCode('BUYER_INDEX_ADV', (int)$this->request('cityId'));

        $data = [
            "banner"=>$banner,
            "notice"=>$notice,
            "adv"=>$adv
        ];

        if( $this->request('codeType') != "BUYER_SYSTEM_ONESELF"){
            $menu = SellerCateService::getCatesAll((int)$this->request('id'), (int)$this->request('type'));
            $special = [
                [
                    'id'=>0,
                    'pid'=>0,
                    'name'=>'物业',
                    'status'=>1,
                    'type'=>0,
                    'logo'=>asset('wap/community/client/images/special_logo.png'),
                    'image'=>asset('wap/community/client/images/special_image.png')
                ]
            ];

            foreach($menu as $key => $value)
            {
                $menu[$key]['type']     = 1;
                $menu[$key]['arg']      = strval($value['id']);
                $menu[$key]['image']    = $value['logo'];
            }

            $menu = array_merge($special, $menu);
            $key = 0;
            if(count($menu) > 8){
                for ($i=0; $i < 7; $i++) {
                    $key++;
                    $menus[$i]['id']       = $menu[$i]['id'];
                    $menus[$i]['pid']      = 0;
                    $menus[$i]['name']     = $menu[$i]['name'];
                    if($menu[$i]['id'] == 0){
                        $menus[$i]['type']     = 0;
                    } else {
                        $menus[$i]['type']     = 1;
                    }
                    $menus[$i]['arg']      = strval($menu[$i]['id']);
                    $menus[$i]['image']    = $menu[$i]['logo'];
                }
                $menus[$key]['type']     = -1;
                $menus[$key]['arg']      = '0';
                $menus[$key]['image']    = asset('wap/community/client/images/s9.png');
                $menus[$key]['name']    = "全部";
            } else {
                $menus = $menu;
            }
            $data['menu'] = $menus;
        }else{
            $article = ArticleService::getList(ONESELF_SELLER_ID);
            $data['article'] = $article;
            $cate = GoodsCateService::getIsWapStatusList(ONESELF_SELLER_ID,(int)$this->request('cityId'));
            $data['cate'] = $cate;
        }
        return $this->outputData($data);
    }



    public function seller(){
        $is_show_top = true;//判断是否手机
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $iphone = (strpos($agent, 'iphone')) ? true : false;
        $ipad = (strpos($agent, 'ipad')) ? true : false;
        $android = (strpos($agent, 'android')) ? true : false;
        if($iphone || $ipad || $android) {
            $is_show_top = false;
        }

        $notice = AdvService::getAdvByCode('BUYER_JNIWCZKA', (int)$this->request('cityId'));

        foreach($notice as $key => $value)
        {
            if($value['type'] == 7) //文章
            {
                $notice[$key]['type'] = '5';
                if ($is_show_top) {
                    $notice[$key]["arg"] = u('wap#Article/detail',array('id'=>$value['arg']));
                } else {
                    $notice[$key]["arg"] = u('wap#Article/detailapp',array('id'=>$value['arg']));
                }
            }
        }
        return $this->outputData($notice);
    }

    public function token() {
        $this->createToken();
        $result = [
            'code'  => 0,
            'token' => $this->token,
            'data'  => [
                'city'  => RegionService::getOpenCityByIp(CLIENT_IP)
            ]
        ];
        return $this->output($result);
    }

    /**
     * Wap初始化
     */
    public function init() {
        $this->createToken();

        $citys  = RegionService::getServiceCitys();
        $userAgent = $this->request('userAgent');

        $wapType = 'web';
        if (preg_match("/\sMicroMessenger\/\\d/is", $userAgent)) {
            $wapType = 'wxweb';
        }

        $result = [
            'code'  => 0,
            'token' => $this->token,
            'data'  => [
                'citys'     => $citys,
                'city'      => RegionService::getOpenCityByIp(CLIENT_IP),
                'payments'  => PaymentService::getPaymentTypes(),
                'configs'   => SystemConfigService::getConfigs(),
                'invitation'=> InvitationService::getById(),
                'indexnav'  => IndexNavService::getLists(true)
            ]
        ];

        return $this->output($result);
    }

    /**
     * 得到配置
     */
    public function configByCode()
    {
        $result = SystemConfigService::getConfigByCode($this->request('code'));

        return $this->outputData($result);
    }

    /**
     * 获取支付配置
     */
    public function getpayment() {
        $payment = PaymentService::getPayment($this->request('code'));
        return $this->outputData($payment);
    }

    /**
     * 首页菜单
     */
    public function getmenu() {
        $list = MenuService::getWapList($this->request('cityId'),$this->request('platformType',''));
        return $this->outputData($list);
    }

    /**
     * 首页轮播广告
     */
    public function integral() {
        $data = AdvService::getAdvByCode('BUYER_INTEGRAL_BANNER', (int)$this->request('cityId'));
        foreach($data as $key => $value)
        {
            if($value['type'] == 4)
            {
                $data[$key]["arg"] = u('wap#Article/detailapp',array('id'=>$value['arg']));
            }
        }

        return $this->outputData($data);
    }


    public function sellercatebanner() {
        $data = AdvService::getAdvByCode('BUYER_SELLER_BANNER', (int)$this->request('cityId'), (int)$this->request('sellerCateId'));
        return $this->outputData($data);
    }

    /**
     * 推荐
     */
    public function getrecommendsellers(){
        $data = SellerService::getRecommendSellers($this->request('mapPoint'),(int)$this->request('cityId'),max((int)$this->request('page'), 1));
        return $this->outputData($data);
    }

    /**
     * 推荐
     */
    public function getrecommendgoods(){
        $sellerId = $this->request('sellerId') ? $this->request('sellerId') : 0;
        $orderBy = $this->request('orderBy') ? $this->request('orderBy') : 0;
        $noIndex = $this->request('noIndex') ? 1 : 0;
        $cateId = $this->request('cateId') ? $this->request('cateId') : 0;
        $data = GoodsService::getRecommendGoods(
            max((int)$this->request('page'),1),
            $sellerId,
            $orderBy,
            $noIndex,
            $cateId,
            (int)$this->request('cityId'),
            $this->request('mapPoint')
        );
        return $this->outputData($data);
    }

}