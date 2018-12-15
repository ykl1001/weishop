<?php 
namespace YiZan\Models;

class SellerAuthIcon extends Base 
{

   	public function seller(){
        return $this->hasMany('YiZan\Models\SellerIconRelated', 'icon_id', 'id');
    }
}
