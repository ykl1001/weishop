<?php
namespace YiZan\Http\Controllers\Api\Buyer;

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
        $data =IntegralService::getWapList(
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize',10)
        );
        
		return $this->outputData($data);
    }
    /**
     * 积分商品兑换
     */
    public function userlog() {
        $data =IntegralService::userlog(
            $this->userId,
            $this->request('id'),
            max((int)$this->request('page'), 1),
            (int)$this->request('pageSize',20));
        return $this->outputData($data);
    }

    /**
     * 积分商品详情
     */
    public function get() {
        $data =IntegralService::get(
            $this->userId,
            $this->request('id'));
        return $this->outputData($data);
    }


    /**
     * 积分商品详情
     */
    public function detail() {
        $data =IntegralService::detail($this->request('id'));
        return $this->outputData($data);
    }


}