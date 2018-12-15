<?php 
namespace YiZan\Models;

class District extends Base {
	protected $visible = ['id', 'name', 'seller_id', 'address', 'province_id', 'city_id', 'house_num', 'area_num', 'house_type',
        'area_id', 'map_point_str', 'province', 'city', 'area', 'departid', 'depart_tel',
        'depart_mail', 'depart_street', 'depart_common', 'seller', 'province',
        'city', 'area', 'isUser', 'sellerName', 'first', 'second', 'third','isProperty',
        'isJoin','isVerify','isCheck','type','status','build_id','room_id','property_user_id','system','property_name'];

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

    public function seller()
    {
        return $this->belongsTo('YiZan\Models\Seller', 'seller_id', 'id');
    }

    public function first(){
        return $this->belongsTo('YiZan\Models\Proxy', 'first_level', 'id');
    }

    public function second(){
        return $this->belongsTo('YiZan\Models\Proxy', 'second_level', 'id');
    }

    public function system()
    {
        return $this->hasMany('YiZan\Models\PropertySystem', 'seller_id', 'seller_id');
    }

    public function third(){
        return $this->belongsTo('YiZan\Models\Proxy', 'third_level', 'id');
    }
}
