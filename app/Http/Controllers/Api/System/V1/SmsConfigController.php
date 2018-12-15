<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\SmsConfigService;
use Input;
/**
 * 短信配置
 */
class SmsConfigController extends BaseController 
{

    /**
     * 保存
     */
    public function save()
    {
        $result = SmsConfigService::save(
        		strval($this->request('SmsUserName')), 
        		strval($this->request('SmsPassword'))
        	);
        
        return $this->output($result);
    }
    /**
     * 获取
     */
    public function get() {
        $result = SmsConfigService::get();
        
        return $this->output($result);
    }
}