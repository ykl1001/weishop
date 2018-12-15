<?php 
namespace YiZan\Services\System;

use YiZan\Models\System\Order;
use YiZan\Models\Seller;
use YiZan\Models\Goods;
use YiZan\Models\Refund;
use YiZan\Models\SellerWithdrawMoney;


class TotalViewService extends \YiZan\Services\BaseService 
{

    /**
     * [total 概况浏览]
     */
    public static function total() {
        $data = array();
        $data['order'] = Order::where('status', '<>', ORDER_STATUS_ADMIN_DELETE)->count(); //待审核服务
        $data['seller'] = Seller::where('is_check','0')->count(); //待审核服务人员
        $list = Refund::orderBy('refund.id', 'desc')
            ->select("refund.*", "user.name AS userName","user.mobile","order.order_type")
            ->where('refund.status', 0);
        $list->join('user', 'user.id', '=', 'refund.user_id');
        $list->leftJoin('order', 'order.id', '=', 'refund.order_id');
        $data['refund'] = $list->count();
        $data['withdraw'] = SellerWithdrawMoney::where('status', '0')->count(); //待审提现
        $data['propertyApply'] = Seller::where('is_check', '0')->where('type','3')->count(); //待审核物业
        return $data;
    }
}
