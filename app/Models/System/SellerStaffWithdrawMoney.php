<?php namespace YiZan\Models\System;

class SellerStaffWithdrawMoney extends \YiZan\Models\SellerWithdrawMoney {
	protected $visible = ['id', 'sn', 'money', 'bank', 'bank_no', 'content', 
		'dispose_time', 'dispose_remark', 'dispose_admin', 'create_time', 'status', 'admin', 'seller', 'authenticate','name', 'extend','staff'];
	
	public function admin(){
        return $this->belongsTo('YiZan\Models\System\AdminUser', 'dispose_admin', 'id');
    }

    public function staff(){
        return $this->belongsTo('YiZan\Models\System\SellerStaff', 'staff_id', 'id');
    }

    public function authenticate(){
        return $this->belongsTo('YiZan\Models\System\SellerAuthenticate', 'seller_id', 'seller_id');
    }

    public function extend(){
        return $this->belongsTo('YiZan\Models\System\SellerStaffExtend', 'staff_id', 'staff_id');
    }

}
