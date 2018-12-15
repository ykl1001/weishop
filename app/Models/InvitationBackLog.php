<?php namespace YiZan\Models;

class InvitationBackLog extends Base 
{ 
	public function order() {
		return $this->belongsTo('YiZan\Models\Order', 'order_id', 'id');
	}

	public function user() {
		return $this->belongsTo('YiZan\Models\User', 'user_id', 'id')->select("id","group_id","name","fanwe_id","mobile");
	}

    public function jtuser() {
        return $this->belongsTo('YiZan\Models\User', 'invitation_id', 'id')->select("id","group_id","name","fanwe_id","mobile");
    }
    public function goods(){
        return $this->belongsTo('YiZan\Models\OrderGoods', 'order_id', 'order_id');
    }
}