<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\IntegralService;
use Lang, Validator;
use DB;
/**
 *  积分商品
 */
class IntegralController extends BaseController
{
    /**
     * 积分商品列表
     */
    public function lists() {
        $data =IntegralService::getList(
            $this->request('sellerId',0),
            $this->request('name'),
            max((int)$this->request('page'), 1),
            (int)$this->request('pageSize',20)
        );
        
		return $this->outputData($data);
    }
    /**
     * 保存积分商品列表
     */
    public function save() {
        $result = IntegralService::save(
            (int)$this->request('id'),
            trim($this->request('name')),
            $this->request('images'),
            $this->request('brief'),
            (int)$this->request('sort'),
            (int)$this->request('isVirtual'),
            (int)$this->request('stock'),
            (int)$this->request('exchangeIntegral'),
            (int)$this->request('status'),
            (int)$this->request('sellerId',0)
        );
        return $this->output($result);
    }

    /**
     * 保存积分商品列表
     */
    public function saveIntegral() {
        $result = IntegralService::saveIntegral(
            (int)$this->request('id'),
            $this->request('integral')
        );
        return $this->output($result);
    }

    /**
     * 获取积分商品列表
     */
    public function getIntegral() {
        $result = IntegralService::getIntegral(
            (int)$this->request('id')
        );
        return $this->outputData($result);
    }

    /**
     * 删除
     */
    public function delete()
    {
        $result = IntegralService::delete(
            $this->request('id')
        );

        return $this->output($result);
    }
}