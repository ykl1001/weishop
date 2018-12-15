<?php 
namespace YiZan\Models;

/**
 * 推送信息
 */
class PushMessage extends Base 
{
    public function seller(){
        return $this->belongsTo('YiZan\Models\Seller','seller_id','id');
    }
    public function order(){
        return $this->belongsTo('YiZan\Models\Order','order_id','id');
    }

    public function Ordertrack(){
        return $this->belongsTo('YiZan\Models\Ordertrack','order_id','order_id');
    }

    public function orders(){
        return $this->belongsTo('YiZan\Models\Orders','order_id','id');
    }

    public function refund(){
        return $this->belongsTo('YiZan\Models\LogisticsRefund','order_id','order_id');
    }
    public function user(){
        return $this->belongsTo('YiZan\Models\User','order_id','id')->select("id","name");

    }
}
