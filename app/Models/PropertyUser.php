<?php namespace YiZan\Models;

class PropertyUser extends Base { 
	
    public function user()
    {
        return $this->belongsTo('YiZan\Models\User', 'user_id');
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

    public function system()
    {
        return $this->hasMany('YiZan\Models\PropertySystem', 'seller_id', 'seller_id');
    }

    public function first(){
        return $this->belongsTo('YiZan\Models\Proxy', 'first_level', 'id');
    }

    public function second(){
        return $this->belongsTo('YiZan\Models\Proxy', 'second_level', 'id');
    }

    public function third(){
        return $this->belongsTo('YiZan\Models\Proxy', 'third_level', 'id');
    }
}
