<?php 
namespace YiZan\Services\Proxy;

use YiZan\Models\System\Order;
use YiZan\Models\Seller;
use YiZan\Models\Proxy;
use YiZan\Models\District;
use YiZan\Models\Goods;
use YiZan\Models\Refund;
use YiZan\Models\SellerWithdrawMoney;
use DB;

class TotalViewService extends \YiZan\Services\BaseService 
{

    /**
     * [total 概况浏览]
     */
    public static function total($proxy) {
        if($proxy->pid){
            $parentProxy = Proxy::find($proxy->pid);
        }

        $data = array();
        $data['order'] = Order::where('status', '<>', ORDER_STATUS_ADMIN_DELETE);
                        switch ($proxy->level) {
                            case '2':
                                $data['order']->where('first_level',$proxy->pid);
                                $data['order']->where('second_level',$proxy->id);
                                break;
                            case '3':
                                $data['order']->where('first_level',$parentProxy->pid);
                                $data['order']->where('second_level',$parentProxy->id);
                                $data['order']->where('third_level',$proxy->id);
                                break;
                            default:
                                $data['order']->where('first_level',$proxy->id);
                                break;
                        }
        $data['order'] = $data['order']->count(); //待审核服务

        $data['seller'] = Seller::where('is_check','0');
                        switch ($proxy->level) {
                            case '2':
                                $data['seller']->where('first_level',$proxy->pid);
                                $data['seller']->where('second_level',$proxy->id);
                                break;
                            case '3':
                                $data['seller']->where('first_level',$parentProxy->pid);
                                $data['seller']->where('second_level',$parentProxy->id);
                                $data['seller']->where('third_level',$proxy->id);
                                break;
                            default:
                                $data['seller']->where('first_level',$proxy->id);
                                break;
                        }
        $data['seller'] = $data['seller']->count(); //待审核服务人员

        $data['property'] = Seller::where('seller.type', 3)->where('is_check', 0);
                            switch ($proxy->level) {
                                case '2':
                                    $data['property']->where('first_level',$proxy->pid);
                                    $data['property']->where('second_level',$proxy->id);
                                    break;
                                case '3':
                                    $data['property']->where('first_level',$parentProxy->pid);
                                    $data['property']->where('second_level',$parentProxy->id);
                                    $data['property']->where('third_level',$proxy->id);
                                    break;
                                default:
                                    $data['property']->where('first_level',$proxy->id);
                                    break;
                            }
        $data['property'] = $data['property']->count(); //待审核物业

        $data['district'] = District::orderBy('id','asc');
                            switch ($proxy->level) {
                                case '2':
                                    $data['district']->where('first_level',$proxy->pid);
                                    $data['district']->where('second_level',$proxy->id);
                                    break;
                                case '3':
                                    $data['district']->where('first_level',$parentProxy->pid);
                                    $data['district']->where('second_level',$parentProxy->id);
                                    $data['district']->where('third_level',$proxy->id);
                                    break;
                                default:
                                    $data['district']->where('first_level',$proxy->id);
                                    break;
                            }
        $data['district'] = $data['district']->count(); //小区
        return $data;
    }
}
