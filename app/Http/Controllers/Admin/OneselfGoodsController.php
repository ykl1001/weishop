<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\OrderConfig;
use YiZan\Models\Goods;
use View, Input, Lang, Route, Page, Form,Response, Session;
/**
 * 商品
 */
class OneselfGoodsController extends AuthController {

	protected function requestApi($method, $args = [],$data = []){
		
		!empty($this->goodstype) ? $this->goodstype : $this->goodstype = Goods::SELLER_GOODS; 
        $args['type'] = $this->goodstype;
        $args['sellerId'] = ONESELF_SELLER_ID;
		return parent::requestApi($method, $args,$data = []); 
	} 
	
	public function index(){
        $args = Input::all();
        $result = $this->requestApi('goods.lists', $args);
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
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
    /**
     * 添加商品
     */
    public function create(){
        $args = Input::all();
        $result_cate = $this->requestApi('goods.cate.lists');
        if(!count($result_cate['data']))
        {
            $result_cate['data'][0] = ['id'=>0, 'name'=>'请选择'];
        }
        View::share('cate', $result_cate['data']);
        //获取标签列表（一级）
        $tagList = $this->requestApi('systemTagList.lists',['status'=>1]);
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

        //获取标签列表（二级）
        $tagList3 = $this->requestApi('systemTagList.secondLevel', ['pid'=>$tagList['data']['systemTagListPid']]);
        $tagList3 = $tagList3['data'];
        array_unshift($tagList3,$tagList2);
        View::share('systemTagListId', $tagList3);
        View::share('args', $args);

        $stock = $this->requestApi('stock.getLists',['status' => 1]);
        View::share('stock', $stock['data']['list']);

        return $this->display('edit');
    }
    /**
     * 添加商品
     */
    public function edit(){
        $args = Input::all();
        $args['goodsId'] = Input::get('id');
        $result = $this->requestApi('goods.get',$args);
        $args['sellerId'] = $result['data']['sellerId'];
        $result_cate = $this->requestApi('goods.cate.lists', $args);
        View::share('cate', $result_cate['data']);
        View::share('data', $result['data']);

        //获取标签列表（一级）
        $tagList = $this->requestApi('systemTagList.lists',['status'=>1]);
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

        //获取标签列表（二级）
        $tagList3 = $this->requestApi('systemTagList.secondLevel', ['pid'=>$result['data']['systemTagListPid']]);
        $tagList3 = $tagList3['data'];
        array_unshift($tagList3,$tagList2);
        View::share('systemTagListId', $tagList3);


        $stock = $this->requestApi('stock.getLists',['status' => 1]);
        View::share('stock', $stock['data']['list']);

        $stockItem = $this->requestApi('stock.getStock',['goodsId' => $result['data']['id'],'stockId' => $result['data']['stockTypeId']]);
        View::share('stockItem', $stockItem);

        return $this->display();
    }

    /**
     * 添加商品
     */
    public function serviceSave(){
        $args = Input::all();

        $args['type'] = $this->goodstype;
		
		if($args['type'] == Goods::SELLER_SERVICE){
            if(isset($args['staffIds']) && !empty($args['staffIds'])){
                $args['staffIds'] = explode(',', $args['staffIds']);
            }
			$url = "OneselfService";
        }else{
            $args['norms']['stock'] = $args['stock_id'];
            $args['norms']['skuItem'] = $args['sku_item'];
            $args['norms']['skuPrice'] = $args['sku_price'];
            $args['norms']['skuStock'] = $args['sku_stock'];
            if(count($args['norms']['skuPrice']) != count($args['norms']['skuStock'])){
                return $this->error("有为空的选项,请检查");
            }
			$url = "OneselfGoods";
		}
		
        if(empty($args['id'])){
            $result = $this->requestApi('oneself.goods.systemAdd', $args);
        }else{
            $result = $this->requestApi('oneself.goods.systemUpdate', $args);
        }
        if ($result['code'] > 0) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98008'), u($url.'/index'), $result['data']);
    }
	/**
	 * 修改状态
	 */
	public function updateStatus() {
		$result = $this->requestApi('system.goods.updateStatus',[
				'id' => Input::input('id'),
				'status' => Input::input('val')
			]);
		$result = array (
            'status'    => true,
            'data'      => Input::input('val'),
            'msg'       => null
        );
		return Response::json($result);
	}

    public function destroy()
    {
        $args = Input::all();
        if(!is_array($args['id']))
        {
            $args['id'] = explode(',', $args['id']);
        }
        else
        {
            $args['id'] = array_filter($args['id']);
        }

        $args['sellerId'] = ONESELF_SELLER_ID; 
        if( $args['id'] > 0 ) {
            $result = $this->requestApi('oneself.goods.delete', $args);
        }
        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }
        if($args['type'] == 0){
            $url = u('OneselfService/index');
        } else {
            $url = u('OneselfGoods/index');
        }
        return $this->success(Lang::get('admin.code.98005'), $url);

    }

    /*
     * 通用商品列表
     */
    public function systemGoods()
    {
        $args = Input::all();
        $result = $this->requestApi('system.goods.getlists', $args);
        if ($result['code'] == 0){
            View::share('list', $result['data']['list']);
            $result_cate = $this->requestApi('goods.cate.lists',['sellerId'=>ONESELF_SELLER_ID,'type'=>Goods::SELLER_GOODS]);
            if(!count($result_cate['data']))
            {
                $result_cate['data'][0] = ['id'=>0, 'name'=>'请选择'];
            }
        }
        View::share('cate', $result_cate['data']);
        View::share('args', $args);
        return $this->display();
    }

    /*
     * 编辑通用
     */
    public function goodsedit()
    {
        $args = Input::all();

        $result_cate = $this->requestApi('goods.cate.lists',['type'=>1]);
        View::share('cate', $result_cate['data']);

        $result = $this->requestApi('system.goods.get', $args);
        if ($result['code'] == 0)
            View::share('data', $result['data']);

        //获取标签列表（一级）
        $tagList = $this->requestApi('systemTagList.lists',['status'=>1]);
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
        //获取标签列表（二级）
        $tagList3 = $this->requestApi('systemTagList.secondLevel', ['pid'=>$result['data']['systemTagListPid']]);
        $tagList3 = $tagList3['data'];
        array_unshift($tagList3,$tagList2);
        View::share('systemTagListId', $tagList3);


        View::share('systemgoodssave', "systemgoodssave");
        $stock = $this->requestApi('stock.getLists',['status' => 1]);
        View::share('stock', $stock['data']['list']);

        $stockItem = $this->requestApi('stock.getStock',['goodsId' => $result['data']['id'],'stockId' => $result['data']['stockTypeId']]);
        View::share('stockItem', $stockItem);

        return $this->display('edit');
    }

    /*
     * 保存通用
     */
    public function systemgoodssave()
    {
        $args = Input::all();
        $args['type'] = Goods::SELLER_GOODS;
        $args['norms']['stock'] = $args['stock_id'];
        $args['norms']['skuItem'] = $args['sku_item'];
        $args['norms']['skuPrice'] = $args['sku_price'];
        $args['norms']['skuStock'] = $args['sku_stock'];
        if(count($args['norms']['skuPrice']) != count($args['norms']['skuStock'])){
            return $this->error("有为空的选项,请检查");
        }
        $result = $this->requestApi('oneself.goods.systemAdd', $args);

        if ($result['code'] > 0) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98008'), u('Service/index'));
    }
    /**
     * 一键导入商品库 检查
     */
    public function oneChannelCk(){
        $args = Input::all();
        $result = $this->requestApi('system.goods.oneChannelCk', $args);
        return Response::json($result);
    }

    /**
     * 一键导入商品库 执行
     */
    public function oneChannel(){
        $args = Input::all();
        $result = $this->requestApi('system.goods.oneChannel', $args);
        return Response::json($result);
    }
    /**
     * 获取员工信息
     */
    public function search(){
        /*获取会员接口*/
        $list = $this->requestApi('sellerstaff.search', Input::all());
        // print_r($list);exit;
        return Response::json($list['data']);
    }
}
