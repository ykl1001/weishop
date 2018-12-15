<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\SellerAuthIconService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang, Validator;

/**
 * 商家认证图标
 */
class SellerauthiconController extends BaseController
{
    /**
     * 图标列表
     */
    public function lists()
    {
        $data = SellerAuthIconService::getList(
            max($this->request('page'),1),
            max($this->request('pageSize'),20)
        );
        
        return $this->outputData($data);
    }

    /**
     * 图标详情
     */
    public function get()
    {
        $data = SellerAuthIconService::get((int)$this->request('id'));
        return $this->outputData($data);
    }

    /**
     * 保存认证图标
     */
    public function save()
    {
        $result = SellerAuthIconService::save
        (
            (int)$this->request('id'),
            trim($this->request('name')),
            $this->request('icon'),
            (int)$this->request('status'),
            (int)$this->request('sort')
        );
        
        return $this->output($result);
    }

    /**
     * 删除图标
     */
    public function delete()
    {
        $result = SellerAuthIconService::delete((array)$this->request('id'));
        
        return $this->output($result);
    }

   
}