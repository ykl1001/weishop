<?php 
namespace YiZan\Http\Controllers\Seller;

use View, Input, Lang, Route, Page, Response;

/**
 * 商家分类
 */
class GoodsCateController extends AuthController {
	/**
	 * 商家分类列表
	 */
	public function index() { 
        $result = $this->requestApi('goods.cate.lists');   
        View::share('list', $result['data']); 
		return $this->display();
	}

    /**
     * 添加商家分类
     */
    public function create(){ 

        $seller_cate_result = $this->requestApi('seller.cate.lists');   
        foreach ($seller_cate_result['data'] as $value) {
            $cate[] = $value['cates'];  
        } 
        //print_r($cate);
        View::share('cate', $cate);  
        return $this->display('edit');
    }

    /**
     * 添加商家分类
     */
    public function edit(){
        $args = Input::all(); 
        $seller_cate_result = $this->requestApi('seller.cate.lists'); 
        foreach ($seller_cate_result['data'] as $value) {
            $cate[] = $value['cates'];    
        } 
        View::share('cate', $cate); 
        $result_data = $this->requestApi('goods.cate.get', $args); 
        //print_r($result_data);
        View::share('data', $result_data['data']);
        return $this->display();
    }

    /**
     * 保存商家分类
     */
    public function save() {
        $args = Input::get();
        if ((int)$args['id'] > 0) {
            $url = u('GoodsCate/edit',['id'=>$args['id']]);
            $result = $this->requestApi('goods.cate.update', $args);
        } else {
            $url = u('GoodsCate/index');
            $result = $this->requestApi('goods.cate.create', $args);
        }
        if($result['code'] == 0){
            return $this->success( Lang::get('admin.code.98008'), $url);
        }else{
            return $this->error($result['msg'] ? $result['msg'] : Lang::get('admin.code.98009'));
        }

    }

    //获取分类
    public function getcate() {
        $result = $this->requestApi('goods.cate.lists'); 
        if($result['code']==0) {
            $this->generateTree(0,$result['data']);
        }
        //生成树形
        $cate = $this->_cates;
        return $cate;
    }

    /**
     * 删除商家分类
     */
    public function destroy(){
        $args = Input::get();
        $args['id'] = explode(',', $args['id']);
        $data = $this->requestApi('goods.cate.delete', ['id' => $args['id']]); 
        $url = u('GoodsCate/index');
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
        $result = $this->requestApi('goods.cate.updateStatus',$args);
        return Response::json($result);
    }
}
