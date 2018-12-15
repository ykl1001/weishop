<?php 
namespace YiZan\Http\Controllers\Admin;

use View, Input, Lang, Route, Page, Response;

/**
 * 商家认证图标
 */
class SellerAuthIconController extends AuthController {
	/**
	 * 商家认证图标列表
	 */
	public function index() {
        $args = Input::get();
        $list = $this->requestApi('Sellerauthicon.lists', $args);
        View::share('list', $list['data']['list']);
        return $this->display();
    }

    /**
     * 添加商家认证图标
     */
    public function create(){
        return $this->display('edit');
    }

    /**
     * 编辑商家认证图标
     */
    public function edit(){
        $args = Input::all();
        $data = $this->requestApi('Sellerauthicon.get', $args);
        View::share('data', $data['data']);
        return $this->display();
    }

    /**
     * 保存商家图标
     */
    public function save() {
        $args = Input::get();
        $result = $this->requestApi('Sellerauthicon.save', $args);
        if($result['code'] == 0){
            $url = u('SellerAuthIcon/index');
            return $this->success($result['msg'] ? $result['msg'] : Lang::get('admin.code.98008'), $url, $result['data']);
        }else{
            return $this->error($result['msg'] ? $result['msg'] : Lang::get('admin.code.98009'));
        }

    }



    /**
     * 删除商家分类
     */
    public function destroy(){
        $args = Input::get();
        $data = $this->requestApi('Sellerauthicon.delete', ['id' => explode(',', $args['id'])]);
        $url = u('SellerAuthIcon/index');
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'),$url );
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), $url , $data['data']);
    }


}
