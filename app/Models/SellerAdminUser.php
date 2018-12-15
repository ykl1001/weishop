<?php namespace YiZan\Models;

class SellerAdminUser extends Base 
{
    public function role() {
        return $this->belongsTo('YiZan\Models\SellerRole', 'rid', 'id');
    }

}
