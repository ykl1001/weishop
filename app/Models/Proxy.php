<?php namespace YiZan\Models;

class Proxy extends Base { 

    public function province()
    {
        return $this->belongsTo('YiZan\Models\Region', 'province_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo('YiZan\Models\Region', 'city_id', 'id');
    }

    public function area()
    {
        return $this->belongsTo('YiZan\Models\Region', 'area_id', 'id');
    }

    public function childs()
    {
        return $this->hasMany('YiZan\Models\Proxy', 'pid', 'id'); 
    }

    public function parentProxy(){
        return $this->belongsTo('YiZan\Models\Proxy', 'pid', 'id');
    }
    
}
