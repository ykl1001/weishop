<?php namespace YiZan\Models;

use YiZan\Utils\Time;
class LogisticsRefund extends Base
{
    public function order(){
        return $this->belongsTo('YiZan\Models\Order','order_id','id');
    }
    public function getcreateTimeAttribute() {
        return Time::toDate($this->attributes['create_time'],'Y-m-d H:i:s');
    }

    public function getsellerDisposeTimeAttribute() {
        return Time::toDate($this->attributes['seller_dispose_time'],'Y-m-d H:i:s');
    }

    public function getImagesAttribute() {
        return explode(',', $this->attributes['images']);
    }

    public function getsellerDisposeImagesAttribute() {
        return explode(',', $this->attributes['seller_dispose_images']);
    }

    public function getuserDisposeImagesAttribute() {
        return explode(',', $this->attributes['user_dispose_images']);
    }

    public function getuserDisposeTimeAttribute() {
        return Time::toDate($this->attributes['user_dispose_time'],'Y-m-d H:i:s');
    }

    public function getstaffDisposeTimeAttribute() {
        return Time::toDate($this->attributes['staff_dispose_time'],'Y-m-d H:i:s');
    }
    public function getadminDisposeTimeAttribute() {
        return Time::toDate($this->attributes['admin_dispose_time'],'Y-m-d H:i:s');
    }


}
