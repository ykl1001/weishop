<?php namespace YiZan\Models;

class SellerStaffBank extends Base
{
	protected $visible = ['id', 'staff_id', 'bank', 'bank_no', 'name', 'mobile'];

    public function staff(){
        return $this->belongsTo('YiZan\Models\Staff', 'staff_id', 'id');
    }
}