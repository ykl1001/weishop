<?php 
namespace YiZan\Http\Controllers\Staff;

use View, Input, Lang, Route, Page, Response, Config;

/**
 * 方维分销
 */
class FxController extends AuthController {
	/**
	 * 获取已开通的分销模式接口
	 */
	public function get_enabled_passages() {
		$args['appsys_id'] = Config::get('app.fanwefx.appsys_id'); 
        $result = $this->requestApi('fx.api', ['path'=>'get_enabled_passages', 'args'=>$args]);
        $data = [];
        foreach ($result['data'] as $key => $value) {
            switch ($value) {
                case 'distribution':
                    $name = '三级分销模式';
                    break;

                case 'fission':
                    $name = '裂变分销模式';
                    break;

                case 'channel':
                    $name = '渠道分销模式';
                    break;

                case 'agent':
                    $name = '区域代理分销模式';
                    break;

                case 'manager':
                    $name = '管理员分销模式';
                    break;

                case 'spreader':
                    $name = '渠道推广分销模式';
                    break;

                case 'bonus':
                    $name = '全民分红分销模式';
                    break;

                case 'return':
                    $name = '消费全返分销模式';
                    break;

                case 'spark':
                    $name = '星火草原分销模式';
                    break;
                
            }
            $data[$key]['id'] = $value;
            $data[$key]['name'] = $name;
        }

        return $data;
	}

    /**
     * [get_enabled_passages_name 获取分销模式名称]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function get_enabled_passages_name($name) {
        switch ($name) {
            case 'distribution':
                $name = '三级分销模式';
                break;

            case 'fission':
                $name = '裂变分销模式';
                break;

            case 'channel':
                $name = '渠道分销模式';
                break;

            case 'agent':
                $name = '区域代理分销模式';
                break;

            case 'manager':
                $name = '管理员分销模式';
                break;

            case 'spreader':
                $name = '渠道推广分销模式';
                break;

            case 'bonus':
                $name = '全民分红分销模式';
                break;

            case 'return':
                $name = '消费全返分销模式';
                break;

            case 'spark':
                $name = '星火草原分销模式';
                break;
            
        }

        return $name;
    }

    /**
     * [query_commission_schemes description]
     * @return [type] [description]
     */
    public function query_commission_schemes() {
        $args['appsys_id'] = Config::get('app.fanwefx.appsys_id');
        $result = $this->requestApi('fx.api', ['path'=>'query_commission_schemes', 'args'=>$args]);
        return $result['data'];
    }
	
}
