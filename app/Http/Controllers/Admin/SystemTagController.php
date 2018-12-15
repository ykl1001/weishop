<?php 
namespace YiZan\Http\Controllers\Admin;

use View, Input, Lang, Route, Page, Response;

/**
 * 商品标签分类
 */
class SystemTagController extends AuthController {
	/**
	 * 商品标签分类列表
	 */
	public function index() {
		$args = Input::all();
        $result = $this->requestApi('systemTag.lists',$args);

        View::share('list', $result['data']);
        return $this->display();
    }

    /**
     * 添加商品标签分类
     */
    public function create(){
        return $this->display('edit');
    }

    /**
     * 编辑商品标签分类
     */
    public function edit(){
        $args = Input::all();
        $data = $this->requestApi('systemTag.get', ['id'=>$args['id']]);

        View::share('data', $data);
        return $this->display();
    }

    /**
     * 保存商品标签分类
     */
    public function save() {
        $args = Input::get();

        $result = $this->requestApi('systemTag.save', $args);
        $url = u('SystemTag/index');
        
        if($result['code'] == 0){
            return $this->success($result['msg'] ? $result['msg'] : Lang::get('admin.code.98008'), $url);
        }else{
            return $this->error($result['msg'] ? $result['msg'] : Lang::get('admin.code.98009'));
        }

    }



    /**
     * 删除商品标签分类
     */
    public function destroy(){
        $args = Input::get();
        $data = $this->requestApi('systemTag.delete', ['id' => explode(',', $args['id'])]);
        $url = u('systemTag/index');
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'),$url );
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), $url , $data['data']);
    }


}

