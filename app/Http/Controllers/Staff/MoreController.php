<?php
namespace YiZan\Http\Controllers\Staff;
use Input, View, Cache;
/**
 * 更多控制器
 */
class MoreController extends BaseController {

	public function __construct() {
		parent::__construct();

        View::share('IsLogin', $this->userId > 1);
        View::share('active',"mine");
	}
    /**
     * 更多详细
     */
    public function detailAll() {
        $args = Input::all();
        if (!isset($args['code'])) {
            return $this->error('非法请求');
        }
        switch ($args['code']) {
            case '1':
                $configcode = 'wap_disclaimer';//免责声明
                View::share('title','免责声明');
                break;
            case '2':
                $configcode = 'wap_service'; //服务范围
                View::share('title','服务范围');
                break;
            case '3':
                $configcode = 'wap_about_us';//关于我们
                View::share('title','关于我们');
                break;
            case '4':
                $configcode = 'wap_order_notice';//订单须知
                View::share('title','订单须知');
                break;
            case '5':
                $configcode = 'coupon_exchange_explain'; //优惠券使用说明
                View::share('title','优惠券使用说明');
                break;
            case '6':
                $configcode = 'wap_refund_agreement'; //退款说明
                View::share('title','退款说明');
                break;
            case '7':
                $configcode = 'wap_help'; //使用帮助
                View::share('title','使用帮助');
                break;
            case '8':
                $configcode = 'wap_terrace_referral'; //平台洗车服务介绍
                View::share('title','平台洗车服务介绍');
                break;
            case '9':
                $configcode = 'wap_lookopen_district'; //查看开通小区
                View::share('title','查看开通小区');
                break;
            default:
                break;
        }
        View::share('data', $this->getConfig($configcode));

        if($args['tpl']){
            return $this->display($args['tpl']);
        }
        View::share('args', $args);
        return $this->display('detail');
    }

	/**
	 * 更多
	 */
	public function index() {
		View::share('seo_title','更多');
		return $this->display();
	}
	/**
	 * 更多详细
	 */
	public function detail() {
		$code = Input::get('code');
		if (!isset($code)) {
			 return $this->error('非法请求');
		}
		switch ($code) {
			case '1':
				$configcode = 'wap_disclaimer';//免责声明
				break;
			default:
				break;
		}
		View::share('about', $this->getConfig($configcode));
		return $this->display();
	}
	/**
     * 免责声明
     */
	public function disclaimer() {
		echo $this->getConfig('wap_disclaimer');
	}

    /**
     * 关于我们
     */
	public function aboutus()
    {
    	echo $this->getConfig('wap_about_us');
	}
	/*优惠券使用说明*/
	public function instructions()
    {
    	echo $this->getConfig('coupon_exchange_explain');
	}

	/**
     * 订单须知
     */
	public function notice() {
		echo $this->getConfig('wap_order_notice');
	}
    /**
     * 使用帮助
     */
    public function help()
    {
    }
    /**
     * 服务介绍
     */
    public function introduce()
    {
    }
    /**
     * 退款协议
     */
    public function refund()
    {
    }
	/**
     * 关于我们(员工)
     */
	public function staffaboutus() {
		echo $this->getConfig('staff_about_us');
	}

	/**
     * 免责声明
     */
	public function staffdisclaimer() {
		echo $this->getConfig('staff_disclaimer');
	}
	/**
     * 订单须知
     */
	public function staffordernotice() {
		echo $this->getConfig('staff_order_notice');
	}
	/**
     * 服务范围
     */
	public function staffservice() {
		echo $this->getConfig('staff_service');
	}

	/**
	 * 不再提醒
	 * 来源 Order/createMoreInfo
	 */
	public function notShowNotes() {
		Cache::forever('notShowNotes'.$this->userId, true);
	}
}