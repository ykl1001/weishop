<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\SystemConfigService;
use Lang, Validator;

/**
 * 系统配置
 */
class ConfigController extends BaseController 
{
    /**
     * 初始化信息
     */
    public function init() {
        $this->createToken();
        $result = [
            'code'  => 0,
            'token' => $this->token,
            'data'  => [
                'configs' => SystemConfigService::getConfigs()
            ]
        ];
        return $this->output($result);
    }

    public function token() {
        $this->createToken();
        $result = [
            'code'  => 0,
            'token' => $this->token,
            'data'  => null
        ];
        return $this->output($result);
    }

    public function get(){
        $result = SystemConfigService::getByCode($this->request('code'));
        return $this->outputData($result);
    }

    public function updateconfig(){
        $result = SystemConfigService::updateConfig(
            $this->request('code'),
            $this->request('val')
            );
        return $this->output($result);
    }

}