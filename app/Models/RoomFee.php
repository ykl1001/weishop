<?php namespace YiZan\Models;

class RoomFee extends Base { 
	
    public function puser()
    {
        return $this->belongsTo('YiZan\Models\PropertyUser', 'puser_id');
    }
    
    public function district()
    {
        return $this->belongsTo('YiZan\Models\District', 'district_id');
    } 

    public function seller()
    {
        return $this->belongsTo('YiZan\Models\Seller', 'seller_id');
    } 

    public function build()
    {
        return $this->belongsTo('YiZan\Models\PropertyBuilding', 'build_id');
    } 

    public function room()
    {
        return $this->belongsTo('YiZan\Models\PropertyRoom', 'room_id');
    } 

    public function payitem()
    {
        return $this->belongsTo('YiZan\Models\PayItem', 'payitem_id');
    } 

    public function PropertyFeeCount()
    {
        return $this->hasManyCount('YiZan\Models\PropertyFee', 'roomfee_id', 'id')->where('status', 1);
    }

    public function NotPropertyFeeCount()
    {
        return $this->hasManyCount('YiZan\Models\PropertyFee', 'roomfee_id', 'id')->where('status', 0);
    }
}
