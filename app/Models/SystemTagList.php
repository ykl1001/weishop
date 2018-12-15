<?php namespace YiZan\Models;

class SystemTagList extends Base {
    
    // protected $appends = ['hasGoods'];

    public function childs(){
    	return $this->hasMany('YiZan\Models\SystemTagList', 'pid', 'id');
    }

    public function tag(){
    	return $this->belongsTo('YiZan\Models\SystemTag', 'system_tag_id', 'id');
    }

    public function useTag() {
    	//只获取一条数据
    	return $this->hasOne('YiZan\Models\Goods', 'system_tag_list_id', 'id');
    }

    public function pid() {
        return $this->belongsTo('YiZan\Models\SystemTagList', 'pid', 'id');
    }

    public function hasOneItem() {
        return $this->hasOne('YiZan\Models\SystemTagList', 'pid', 'id');
    }

}
