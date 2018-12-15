<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\MakeService;
use YiZan\Http\Controllers\Api\Buyer\BaseController;
use Lang, Validator;

/**
 * 首页底部导航
 */
class MakeController extends BaseController
{
    /**
     * 列表
     */
    public function money(){
        $data = MakeService::money(
            $this->userId
            );
        return $this->outputData($data);
    }

    /**
     * 列表
     */
    public function order(){
        $data = MakeService::order(
            $this->userId,
            (int)$this->request('userId'),
            (int)$this->request('status'),
           (int)$this->request('page')
        );
        return $this->outputData($data);
    }

    /**
     * 列表
     */
    public function detail(){
        $data = MakeService::detail(
            $this->userId,
            (int)$this->request('userId'),
            (int)$this->request('id')
        );
        return $this->outputData($data);
    }

}