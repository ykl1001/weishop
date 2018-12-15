<?php 
namespace YiZan\Models;

class GoodsCate extends Base {
	
    public function goods() {
        return $this->hasMany('YiZan\Models\Goods', 'cate_id', 'id');
    }

   	public function cates()  {
        return $this->belongsTo('YiZan\Models\SellerCate', 'trade_id', 'id');
    }

    public function seller() {
        return $this->hasMany('YiZan\Models\Seller', 'id', 'seller_id');
    }
    public function goodsNmu() {
        return $this->belongsTo('YiZan\Models\Goods', 'id', 'cate_id');
    }
}
