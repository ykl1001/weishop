<?php namespace YiZan\Models;

class LiveLog extends Base
{
    public function user(){
        return $this->belongsTo('YiZan\Models\User');
    }
}
