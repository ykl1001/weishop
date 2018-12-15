<?php namespace YiZan\Models;

class Orders extends  Base {
    protected $table = 'order';
    protected $visible = ['id',  'name', 'logo', 'is_all','status','goods'];
    public function goods(){
        return $this->belongsTo('YiZan\Models\OrderGoods', 'id', 'order_id');
    }
}
