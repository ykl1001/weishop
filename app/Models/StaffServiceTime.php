<?php namespace YiZan\Models;

class StaffServiceTime extends Base
{
    /**
     * 结束时间
     * @return bool
     */
    public function getEndTimeAttribute()
    {
        $endTime 	= $this->attributes['end_time'];

        return $endTime == '00:00' ? '24:00' : $endTime;
    }
}
