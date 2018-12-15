<?php 
namespace YiZan\Http\Controllers\Api\Proxy;
use Lang, Validator;

use YiZan\Services\StockService;
class StockController extends BaseController
{
    public function getLists() {
        $data =  StockService::getLists(
            $this->request('status'),
            max((int)$this->request('page'), 1),
            (int)$this->request('pageSize', 20)
        );

        return $this->outputData($data);
    }

    public function detail() {
        $data =  StockService::detail(
            (int)$this->request('id')
        );

        return $this->outputData($data);
    }


    public function getStock() {
        $data = \YiZan\Services\GoodsService::getStock(
            (int)$this->request('goodsId'),
            (int)$this->request('stockId'),
            (int)$this->request('isSystem')
        );
        return $this->output($data);
    }

}