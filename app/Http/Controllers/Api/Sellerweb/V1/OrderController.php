<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\Sellerweb\OrderService;
use YiZan\Services\OrderTrackService;
use Lang, Validator;

/**
 * 订单
 */
class OrderController extends BaseController 
{
    /**
     * 订单列表
     */
    public function lists()
    {
        $data = OrderService::getSellerList
        (
            $this->sellerId,
            $this->request('sn'),
            $this->request('orderType'),
            $this->request('provinceId'),
            $this->request('cityId'),
            $this->request('areaId'),
            intval($this->request('status')),
            intval($this->request('beginTime')),
            intval($this->request('endTime')),
            intval($this->request('mobile')),
            trim($this->request('staffName')),
            trim($this->request('name')),
            $this->request('payTypeStatus'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
		return $this->outputData($data);
    }
    /**
     * 订单列表
     */
    public function getcarstaff()
    {
        $data = OrderService::getCarList
        (
            $this->staffId,
            intval($this->request('status')),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        return $this->outputData($data);
    }
    /**
     * 获取订单
     */
    public function detail()
    {
        $order = OrderService::getSellerOrderDetail($this->sellerId, intval($this->request('orderId')));
        return $this->outputData($order);
    }

    /**
     * 获取打印订单
     */
    public function printer()
    {
        $order = OrderService::printer($this->sellerId, intval($this->request('orderId')));

        return $this->outputData($order);
    }

    /**
     * 开始服务
     */
    public function start()
    {
        $result = OrderService::updateSellerOrder(intval($this->request('orderId')), $this->sellerId, ORDER_STATUS_START_SERVICE);
        
        return $this->output($result);
    }
    /**
     * 完成服务
     */
    public function finish()
    {
        $result = OrderService::updateSellerOrder(intval($this->request('orderId')), $this->sellerId, ORDER_STATUS_FINISH_SERVICE);
        
        return $this->output($result);
    }
    /**
     * 更新订单
     **/
    public function updatestatus()
    {
        $result = OrderService::updateSellerOrder(intval($this->request('orderId')), $this->sellerId, intval($this->request('status')), $this->request('content'));
        
        return $this->output($result);
    }
    /**
     * 删除订单
     */
    public function delete()
    {
        die;//暂先隐藏此功能
        $result = OrderService::deleteOrder($this->sellerId, intval($this->request('id')));
        
        return $this->output($result);
    }
    /**
     * 商家指派人员
     */
    public function designate() {
        $result = OrderService::designate(
            $this->request('orderId'),
            (int)$this->request('staffId'),
            $this->sellerId
        );
        return $this->output($result);
    }
    /**
     * 随机指派人员
     */
    public function ranupdate() {
        $result = OrderService::ranUpdate(
            (int)$this->request('orderId'),
            $this->request('serviceContent'),
            $this->request('money')
        );
        return $this->output($result);
    }

    /**
     * 发送快递
     */
    public function postlogistics(){
        $result = OrderTrackService::get(
            $this->request('keycode'),
            $this->request('number'),
            $this->request('from'),
            $this->request('to'),
            $this->request('key'),
            $this->request('orderId'),
            $this->request('userId'),
            $this->request('sellerId'),
            $this->request('company'),
            $this->request('type'),
            $this->request('remark')
        );
        return $this->outputData($result);
    }

    /**
     * 查询物流
     */
    public function get(){
        $result = OrderTrackService::getOrder(
            $this->request('sellerId'),
            $this->request('userId'),
            $this->request('id')
        );
        return $this->outputData($result);
    }

    /**
     * 获取地址String
     */
    public function addressStr() {
        $result = OrderTrackService::addressStr(
            $this->request('provinceId'),
            $this->request('cityId'),
            $this->request('areaId')
        );
        return $this->outputData($result);
    }
    /**
     * 退款详情
     */
    public function refundDetail() {
        $result = OrderService::refundDetail(
            (int)$this->request('userId'),
            (int)$this->request('orderId')
        );
        return $this->outputData($result);
    }
}