<?php 
namespace YiZan\Models;

class SellerRole extends Base 
{
    public function access()
    {
        return $this->hasMany('YiZan\Models\SellerRoleAccess', 'rid', 'id');
    }
}
