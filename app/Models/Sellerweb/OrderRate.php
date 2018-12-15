<?php namespace YiZan\Models\Sellerweb;

class OrderRate extends \YiZan\Models\OrderRate {
	
	protected $visible = ['id', 'content', 'images', 'reply', 'specialty_score', 'communicate_score', 'punctuality_score', 
			'score', 'result', 'create_time', 'user', 'order', 'staff', 'star', 'is_all', 'goods_id', 'goods_star', 'goods', 'reply_time'];


    public function staff(){
        return $this->belongsTo('YiZan\Models\SellerStaff','staff_id','id');
    }

    public function goods() {
        return $this->belongsTo('YiZan\Models\Goods', 'goods_id', 'id');
    }
}
