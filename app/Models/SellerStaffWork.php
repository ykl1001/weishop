<?php namespace YiZan\Models;

class SellerStaffWork extends Base {
    public function staff(){
        return $this->belongsTo('YiZan\Models\Staff','staff_id','id');
    }
}
