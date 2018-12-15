<?php namespace YiZan\Models;

class SystemTag extends Base {

    public function systemTagList() {
    	return $this->hasMany('YiZan\Models\SystemTagList', 'system_tag_id', 'id');
    }
    
}
