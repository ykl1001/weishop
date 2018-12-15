<?php namespace YiZan\Models;

use YiZan\Utils\Time;
use Lang;
class PropertyOrder extends Base {

    public function puser()
    {
        return $this->belongsTo('YiZan\Models\PropertyUser', 'puser_id');
    }
    
    public function seller()
    {
        return $this->belongsTo('YiZan\Models\Seller', 'seller_id');
    } 

    public function orderItem(){
    	return $this->hasMany('YiZan\Models\PropertyOrderItem', 'order_id', 'id');
    }

    public function userPayLog(){
    	return $this->belongsTo('YiZan\Models\UserPayLog', 'id', 'order_id');
    }

}