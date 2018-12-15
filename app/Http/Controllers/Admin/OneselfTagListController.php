<?php
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\OrderConfig;
use YiZan\Models\Goods;
use View, Input, Lang, Route, Page, Form,Response;
/**
 * 商品
 */
class OneselfTagListController  extends AuthController {

    protected function requestApi($method, $args = [],$data = []){
//		if($this->goodsType == "" || $this->goodsType == 1){
//            $this->goodsType = Goods::SELLER_GOODS;
//        }
//        $args['type'] = $this->goodsType;
        $args['sellerId'] = ONESELF_SELLER_ID;
        return parent::requestApi($method, $args,$data = []);
    }

    public function index(){
        $args = Input::all();
        $result = $this->requestApi('goods.cate.lists', $args);
        if( $result['code'] == 0 ){
            View::share('list', $result['data']);
        }
        return $this->display();
    }
    /**
     * 添加商家分类
     */
    public function create(){

        $seller_cate_result = $this->requestApi('seller.cate.getSellerCateOneselfLists');
        foreach ($seller_cate_result['data'] as $value) {
            $cate[] = $value;
        }
        $tagList2 = [
            "id" => 0,
            "pid" => 0,
            "name" => "请选择"
        ];
        array_unshift($cate,$tagList2);
        View::share('cate', $cate);
        return $this->display('edit');
    }
    /**
     * 保存商家分类
     */
    public function save() {
        $args = Input::get();
        if($this->goodsType = 1 || $this->goodsType == ""){
            $url = u('OneselfTagList/index',['id'=>$args['id']]);
        }else{
            $url = u('OneselfTagServicesList/index',['id'=>$args['id']]);
        }

        $result = $this->requestApi('goods.cate.OneselfTagCreate', $args);
        if($result['code'] == 0){
            return $this->success( Lang::get('admin.code.98008'), $url);
        }else{
            return $this->error($result['msg'] ? $result['msg'] : Lang::get('admin.code.98009'));
        }

    }
    /**
     * 编辑商家分类
     */
    public function edit(){
        $args = Input::all();
        $seller_cate_result = $this->requestApi('seller.cate.getSellerCateOneselfLists');
        foreach ($seller_cate_result['data'] as $value) {
            $cate[] = $value;
        }
        $tagList2 = [
            "id" => 0,
            "pid" => 0,
            "name" => "请选择"
        ];
        array_unshift($cate,$tagList2);
        View::share('cate', $cate);
        $result_data = $this->requestApi('goods.cate.getOneself', $args);
        View::share('data', $result_data);
        return $this->display();
    }

    /**
     * 删除商家分类
     */
    public function destroy(){
        $args = Input::get();
        $args['id'] = explode(',', $args['id']);
        $data = $this->requestApi('goods.cate.oneselfDelete', ['id' => $args['id']]);
        if($this->goodsType = 1 || $this->goodsType == ""){
            $url = u('OneselfTagList/index');
        }else{
            $url = u('OneselfTagServicesList/index');
        }
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'),$url );
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), $url , $data['data']);
    }

    /**
     * 更改状态
     */
    public function updateStatus() {
        $args = Input::all();
        $result = $this->requestApi('goods.cate.updatestatus',$args);
        return Response::json($result);
    }
    /**
     * 更改状态
     */
    public function isWapStatus() {
        $args = Input::all();
        $result = $this->requestApi('goods.cate.isWapStatus',$args);
        return Response::json($result);
    }

}
