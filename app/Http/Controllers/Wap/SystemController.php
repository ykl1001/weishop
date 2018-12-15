<?php namespace YiZan\Http\Controllers\Wap;
use YiZan\Services\Buyer\SystemConfigService;
use YiZan\Services\Buyer\AdvService;
use Cache,Config,Input,Session;
/**
 * app相关信息
 */
class SystemController extends \YiZan\Http\Controllers\YiZanViewController
{
    public function __construct()
    {

    }
    public function init()
    {
        $sdkType = Input::get('sdk_type');
        $ad = AdvService::getAdv('BUYER_START_BANNER'); //APP启动页广告位
        $urlArgs = parse_url($ad->url);
        $adOpen = $urlArgs['host'] == 'wap.'.Config::get('app.domain') ? 0 : 1;
        $configs = SystemConfigService::getConfigByGroup('buyer');
        $versionCode = ($sdkType == 'ios') ? $configs['buyer_app_version_code'] : $configs['buyer_android_app_version_code'];
        $app_opendoor_config = $configs['app_opendoor'];
        header("Content-type:text/json; charset=utf-8");

        $root = array();

        $root["sina_app_api"] = 1;
        $root["qq_app_api"] = 1;
        $root["wx_app_api"] = 1;
        $root["statusbar_hide"] = 0;
        $root["statusbar_color"] = "#ffff2d4b";
        $root["statusba_title_color"] = 0; //顶部字体颜色,1为黑色,0为白色
        $root["topnav_color"] = "#55ACEF";
        $root["ad_img"] = $ad->image;
        $root["ad_http"] = $ad->url;
        $root["ad_open"] = $adOpen;

        //物业要当首页cz
        if($configs['is_property_index']){
            $root["site_url"] = u("Index/dir", ["show_prog"=>1]);
            Session::set('is_property_index',1);
            Session::save();
        }else{
            $root["site_url"] = u("", ["show_prog"=>1]);
        }

        $root["site_url"] = u("", ["show_prog"=>1]);
        $root["reload_time"] = 86400 * 365;
        $root["top_url"][0] = u("");
        $root["app_opendoor_config"] = (int)$app_opendoor_config;
        if ($sdkType != 'ios') {
            $root["version"]["serverVersion"] = $versionCode;
            $root["version"]["has_upgrade"] = 0;
            $root["version"]["hasfile"] = 1;
            $root["version"]["filename"] = $configs['buyer_android_app_down_url'];
            $root["version"]["android_upgrade"] = "版本更新";
        } else {
            $nowVersionCode = Input::get('sdk_version_name');
            $root["version"]["has_upgrade"] = $nowVersionCode < $versionCode ? 1 : 0;
            $root["version"]["ios_down_url"] = $configs['buyer_app_down_url'];
        }

        die(json_encode($root));
    }
}
