<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\OrderConfig;
use YiZan\Models\Goods;
use View, Input, Lang, Route, Page, Form;
/**
 * 商品
 */
class OneselfTagServicesListController  extends OneselfTagListController {

	protected $goodsType;
 	public function __construct() {
		parent::__construct();
		$this->goodsType = Goods::SELLER_SERVICE;
	}
    public function index(){
        $args = Input::all();
        $result = $this->requestApi('seller.cate.getSellerCateOneselfLists', $args);
        if( $result['code'] == 0 ){
            View::share('list', $result['data']);
        }
        $result_cate = $this->requestApi('goods.cate.lists', $args);
        View::share('cate', $result_cate['data']);
        View::share('args', $args);

        //获取标签列表（一级）
        $tagList = $this->requestApi('systemTagList.lists');
        $tagList = $tagList['data'];
        $tagList2 = [
            "id" => 0,
            "pid" => 0,
            "name" => "请选择",
            "sort" => 100,
            "status" => 1,
            "level" => 0,
            "levelname" => ""
        ];
        array_unshift($tagList,$tagList2);
        View::share('systemTagListPid', $tagList);

        return $this->display();
    }
}
