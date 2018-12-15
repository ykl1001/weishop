<?php
namespace YiZan\Services;

require_once(base_path().'/Saas/SAASAPIClient.php');

use Cache, Config;

/**
 * 分销模块服务基类
 * @package Fanwewd\Api\Open\Fx\Services
 */
abstract class FxBaseService extends \YiZan\Services\BaseService {

    /**
     * 调用分销接口
     * @param string    $path   接口地址
     * @param array     $args   请求附加参数数据
     * @return bool|null
     */
    public static function requestApi($path, $args) {
        $client = new \SAASAPIClient(FANWESAAS_APP_ID, FANWESAAS_APP_SECRET);
        $url = self::getAccessUrl().'/';

        $is_add_appsys = Cache::get('add_fx_appsys');
        
        if (!$is_add_appsys) {
            $ret = $client->invoke($url. 'api/add_appsys',  ['appsys_id' => Config::get('app.fanwefx.appsys_id'), 'replace_on_exists' => true]); 
            if ($ret['errcode'] == 0) {
                Cache::forever('add_fx_appsys', $ret); 
            }
        }
        
        $ret = $client->invoke($url. 'api/' . $path, $args);

        if ($ret['errcode'] == 0) {
            return isset($ret['data']) ? $ret['data'] : true;
        } else {
           // $this->throwException(51000, 'fx_api');
           return $ret;
        }
    }

    /**
     * 获取分销系统当前店铺管理地址
     * @return \附加安全参数后的安全地址
     */
    public static function makeManageUrl() {
        $client = new \SAASAPIClient(FANWESAAS_APP_ID, FANWESAAS_APP_SECRET);
        $url = self::getAccessUrl().'/admin/entry';
        
        $args = [];
        $args['appid']      = FANWESAAS_APP_ID;
        $args['appsecret']  = FANWESAAS_APP_SECRET;
        $args['rooturl']    = u('admin#/');
        return $client->makeSecurityUrl($url, $args, false, 1);
    }

    /**
     * 生成分销商wap端管理地址
     * @param int   $fx_user_id 分销商ID
     * @return \附加安全参数后的安全地址
     */
    public static function makeWapUrl($fx_user_id) {
        $client = new \SAASAPIClient(FANWESAAS_APP_ID, FANWESAAS_APP_SECRET);
        $url = self::getAccessUrl().'/wap/entry';

        $args = [];
        $args['userid']     = $fx_user_id;
        $args['rooturl']    = u('wap#/');
        $ret = $client->makeSecurityUrl($url, $args, false, 1);
        return $ret;
    }
    
    public static function getAccessUrl(){ 
        return Config::get('app.fanwefx.fx_host');
    }
}