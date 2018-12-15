<?php 
namespace YiZan\Models;

class OrderTrack extends Base
{
    public function seller(){
        return $this->belongsTo('YiZan\Models\Seller','seller_id','id');
    }
}
