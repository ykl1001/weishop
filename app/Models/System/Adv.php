<?php 
namespace YiZan\Models\System;

class Adv extends \YiZan\Models\Adv 
{
	protected $visible = ['id', 'name', 'image', 'bg_color', 'type', 'arg', 'app', 'sort', 'status', 'city', 'position','create_time','city_id', 'seller_cate_id','mould_id','data_json'];
    
    public function city()
    {
        return $this->belongsTo('YiZan\Models\Region', 'city_id', 'id');
    }
    
    public function position()
    {
        return $this->belongsTo('YiZan\Models\AdvPosition', 'position_id', 'id');
    }
}
