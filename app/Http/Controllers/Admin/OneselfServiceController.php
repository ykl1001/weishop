<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\OrderConfig;
use YiZan\Models\Goods;
use View, Input, Lang, Route, Page, Form;
/**
 * 服务
 */
class OneselfServiceController extends OneselfGoodsController {
	
    protected $goodstype;	
    public function __construct() {
        parent::__construct();
        $this->goodstype = Goods::SELLER_SERVICE;
    }

}
