<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;
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

    public function update()
    {
        $data = StockService::save(
            $this->request('id'),
            $this->request('name'),
            $this->request('stock'),
            $this->request('status')
        );
        return $this->output($data);
    }

    public function detail() {
        $data =  StockService::detail(
            (int)$this->request('id')
        );

        return $this->outputData($data);
    }
    public function delete() {
        $status = StockService::remove($this->request('id'));
        if (!$status) {
            return $this->outputCode(20108);
        }
        return $this->outputCode(0);
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