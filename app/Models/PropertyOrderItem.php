<?php namespace YiZan\Models;

use YiZan\Utils\Time;
use Lang;
class PropertyOrderItem extends Base {
    
    public function propertyFee()
    {
        return $this->belongsTo('YiZan\Models\PropertyFee', 'propertyfee_id');
    }

}