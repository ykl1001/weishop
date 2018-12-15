<?php namespace YiZan\Models;

class UserIntegral extends Base {

    protected $appends = ['desc'];

	public function user(){
        return $this->belongsTo('YiZan\Models\User','user_id','id');
    }

    /**
     * 类型描述
     */
    public function getDescAttribute() {
        $relatedType = $this->attributes['related_type'];
        $desc = '';
        switch($relatedType){
            case '1' : $desc = '签到送积分'; break;
            case '2' : $desc = '注册送积分'; break;
            case '3' : $desc = '消费送积分'; break;
            case '4' : $desc = '消费抵现'; break;
            case '5' : $desc = '回复送积分'; break;
            case '6' : $desc = '发帖送积分'; break;
            case '7' : $desc = '抵现退回'; break;
            case '8' : $desc = '积分兑换商品'; break;
        }
        return $desc;
    }



}
