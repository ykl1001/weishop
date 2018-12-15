<?php namespace YiZan\Models;

class UserBank extends Base 
{
	protected $visible = ['id', 'user_id', 'bank', 'bank_no', 'name', 'mobile'];

    public function user(){
        return $this->belongsTo('YiZan\Models\User', 'user_id', 'id');
    }
}