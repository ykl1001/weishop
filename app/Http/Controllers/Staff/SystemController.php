<?php namespace YiZan\Http\Controllers\Staff;
use YiZan\Services\Buyer\SystemConfigService;
use Input;
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
        $configs = SystemConfigService::getConfigByGroup('staff');
        $versionCode = $sdkType == 'ios' ? $configs['staff_app_version_code'] : $configs['staff_android_app_version_code'];
        header("Content-type:text/json; charset=utf-8");

        $root = array();

        $root["sina_app_api"] = 0;
        $root["qq_app_api"] = 0;
        $root["wx_app_api"] = 1;
        $root["statusbar_hide"] = 0;
        $root["statusbar_color"] = "#ffff2d4b";
        $root["statusba_title_color"] = 0; //顶部字体颜色,1为黑色,0为白色
        $root["topnav_color"] = "#55ACEF";
        $root["ad_img"] = "";
        $root["ad_http"] = "";
        $root["ad_open"] = 0;
        $root["site_url"] = u("", ["show_prog"=>1]);
        $root["reload_time"] = 0;
        $root["top_url"][0] = u("");
        if ($sdkType != 'ios') {
            $root["version"]["serverVersion"] = $versionCode;
            $root["version"]["has_upgrade"] = 0;
            $root["version"]["hasfile"] = 1;
            $root["version"]["filename"] = $configs['staff_android_app_down_url'];
            $root["version"]["android_upgrade"] = "版本更新";
        } else {
            $nowVersionCode = Input::get('sdk_version_name');
            $root["version"]["has_upgrade"] = $nowVersionCode < $versionCode ? 1 : 0;
            $root["version"]["ios_down_url"] = $configs['staff_app_down_url'];
        }

        die(json_encode($root));
    }
}
