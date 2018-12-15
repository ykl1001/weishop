<?php 
namespace YiZan\Models\Sellerweb;

use YiZan\Utils\Time;

class Activity extends \YiZan\Models\Activity 
{
    public function activityGoods() {
        return $this->hasMany('YiZan\Models\ActivityGoods','activity_id','id');
    }
}
