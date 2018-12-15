<?php 
namespace YiZan\Models;

class SellerIconRelated extends Base 
{

   	public function icon(){
        return $this->belongsTo('YiZan\Models\SellerAuthIcon', 'icon_id');
    }

	public function seller(){
        return $this->hasMany('YiZan\Models\SellerIconRelated', 'icon_id', 'id');
    }
}
