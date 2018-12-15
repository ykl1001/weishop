<?php 
namespace YiZan\Http\Controllers\Api\Buyer;
use YiZan\Services\Buyer\RegionService;
use YiZan\Services\Buyer\PaymentService;
use YiZan\Services\Buyer\SystemConfigService;
use YiZan\Services\Buyer\AdvService;

use YiZan\Models\StaffServiceTime;
use YiZan\Utils\Time;
use Config;

class AppController extends BaseController {
	/**
	 * APP初始化
	 */
	public function init() 
    {
		$this->createToken();
		$configs = SystemConfigService::getConfigByGroup('buyer');

        $payments = PaymentService::getPayments('app');

        $ad = AdvService::getAdv('BUYER_START_BANNER'); //APP启动页广告位

        foreach($payments as $key => $payment)
        {
            unset( $payments[$key]["alipayConfig"]);
            
            unset( $payments[$key]["weixinConfig"]);
            
            $payments[$key]["isDefault"] = 0;

            switch ($payment['code']) 
            {
                case 'alipay':
                case 'alipayWap':
                    $payments[$key]["icon"] = asset('wap/images/ico/zf3.png');
                    break;
                case 'weixin':
                case 'weixinJs':
                    $payments[$key]["icon"] = asset('wap/images/ico/zf2.png');
					break;
                case 'balancePay':
                    $payments[$key]["icon"] = asset('wap/community/client/images/ico/zf5.png');
                    break;
                case 'unionpay':
                    $payments[$key]["icon"] = asset('wap/images/ico/yl.png');
                    break;
                case 'unionapp':
                    $payments[$key]["icon"] = asset('wap/images/ico/yl.png');
                    break;
            }
            
        }

        $payment1 = $payments;

        foreach($payment1 as $key => $payment){
            if($payment['code'] == 'unionapp'){
                unset($payment1[$key]);
            }
        }

        if(count($payments))
        {
            $payments[0]["isDefault"] = 1;
        } 

        $orderConfig = SystemConfigService::getConfigByGroup('order_config');
        $shareContent = sprintf($configs['buyer_share_content'],$configs['site_name'],u('wap#user/app'));
		$result = [
			'code' 	=> 0,
			'token' => $this->token,
			'key'	=> base_convert(Config::get('app.iv_rule'), 10, 24),
			'data'	=> [
				'appVersion' => $this->request("deviceType") == "ios" ? $configs['buyer_app_version'] : $configs['buyer_android_app_version'],
				'forceUpgrade' => (boolean)$configs['buyer_force_upgrade'],
				'upgradeInfo' => $this->request("deviceType") == "ios" ? $configs['buyer_upgrade_info'] : $configs['buyer_android_upgrade_info'],
				'appDownUrl' => $this->request("deviceType") == "ios" ? $configs['buyer_app_down_url'] : $configs['buyer_android_app_down_url'],
				'serviceTel' => $configs['wap_service_tel'],
                'serviceTime'=> $configs['wap_service_time'],
                'payments' => $payment1,
                'payment' => $payments,
				'systemOrderPass' => $orderConfig['system_order_pass']/60,
                'siteName' => $configs['site_name'],
                'shareContent' => $shareContent,
                'aboutUrl'=>u('wap#more/aboutus'),
                'protocolUrl'=>u('wap#more/disclaimer'),
                'helpUrl'=>u('wap#more/help'),
                'introUrl'=>u('wap#more/instructions'),
                'fileUploadType'=>'oss',
                'fileUploadConfig'=>
                [
			        'host' 			=> Config::get('app.image_config.oss.host'),
			        'accessId'		=> Config::get('app.image_config.oss.access_id'),
			        'accessKey'	    => Config::get('app.image_config.oss.access_key'),
			        'bucket' 		=> Config::get('app.image_config.oss.bucket')
		        ],
				'province' => RegionService::getOpenCitys(),
                'isOpenProperty'=>Config::get('app.is_open_property'),
                'statusbar_hide' => 1, //默认隐藏状态栏
                'statusbar_color' => $ad->bg_color,
                'topnav_color' => $ad->bg_color,
                'ad_img' => $ad->image,
                'ad_http' => $ad->arg,
                'ad_open' => 0,
			]
		];
		return $this->output($result);
	}



/*    public function toUpdateTime() {
        set_time_limit(0);
        $pageSize = 20;
        $count = StaffServiceTime::count();
        for($i = 0;$i <= $count; $i += $pageSize){
            $data = StaffServiceTime::skip($i)->take($pageSize)->get()->toArray();
            foreach ($data as $k=>$v){
                $time = Time::toTime(Time::toDate(UTC_DAY,'Y-m-d').' '.$v['beginTime']);
                $endTime = Time::toDate(($time + 3600), 'H:i');
                $endStime = Time::toDate(($time + 3599), 'H:i:s');
                StaffServiceTime::where('seller_id',$v['sellerId'])
                                ->where('week',$v['week'])
                                ->where('begin_time',$v['beginTime'])
                                ->update(['end_time' => $endTime, 'end_stime' => $endStime]);
            }
        }
    }*/
}