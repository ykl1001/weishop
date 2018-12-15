<?php 
namespace YiZan\Models;

use YiZan\Utils\Time;

class Special extends Base
{

    protected $appends = array(
        'typeName'
    );

    /**
     * 类型
     * @return bool
     */
    public function getTypeNameAttribute(){
        $type = $this->attributes['type'];
        switch($type){
            case 1:
                return "满减";
            case 2:
                return "满免";
            case 3:
                return "免运费";
            case 4:
                return "折扣商品";
        }
    }
}
