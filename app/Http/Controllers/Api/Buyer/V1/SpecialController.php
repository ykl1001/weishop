<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\SpecialService;
use Lang, Validator;

/**
 * 专题
 */
class SpecialController extends UserAuthController
{
    /**
     * 获取专题
     */
    public function get(){
        $Special = SpecialService::getSpecial((int)$this->request('id'));
        return $this->outputData($Special == false ? [] : $Special->toArray());
    }

}