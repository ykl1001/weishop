<?php namespace YiZan\Models;

class SellerBank extends Base 
{
	protected $visible = ['id', 'seller_id', 'bank', 'bank_no', 'name', 'mobile'];

    public function seller(){
        return $this->belongsTo('YiZan\Models\Seller', 'seller_id', 'id');
    }
}