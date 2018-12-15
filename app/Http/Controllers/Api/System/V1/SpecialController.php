<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\SpecialService;
use YiZan\Utils\Time;

/**
 * 专题管理
 */
class SpecialController extends BaseController {
    /**
     * 专题列表
     */
    public function lists(){
        $data = SpecialService::getLists(0);
        return $this->outputData($data);
    }

    /**
     * 获取专题
     */
    public function get() {
        $data = SpecialService::getSpecial((int)$this->request('id'));
        return $this->outputData($data);
    }

    /**
     * 添加专题
     */
    public function save() {
        $result = SpecialService::saveSpecial(
                (int)$this->request('id'),
                trim($this->request('name')),
               trim($this->request('image')),
               trim($this->request('content')),
               (int)$this->request('status')
            );
        return $this->output($result);
    }

    


    /**
     * 删除优惠券
     */
    public function delete() {
        $status = PromotionService::deletePromotion((int)$this->request('id'));
        return $this->output($status);
    }



}