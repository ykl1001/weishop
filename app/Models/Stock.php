<?php 
namespace YiZan\Models;
class Stock extends Base {

    public function getStockAttribute() {
        return unserialize($this->attributes['stock']);
    }
}
