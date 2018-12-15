<?php 
namespace YiZan\Models;

class SharechapmanLog extends Base {

    public function user()
    {
        return $this->belongsTo('YiZan\Models\System\User', 'user_id', 'id');
    }


}
