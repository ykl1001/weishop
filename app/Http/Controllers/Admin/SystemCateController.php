<?php
namespace YiZan\Http\Controllers\Admin;

use View, Input, Lang, Route, Page, Response;

/**
 * 系统分类
 */
class SystemCateController extends AuthController {
    /**
     * 商家分类列表
     */
    public function index() {
        $list = $this->getcate();
        if( count($list) > 0 ) {
            $pids[] = array_unique(array_reduce($list, create_function('$v,$w', '$v[$w["id"]]=$w["pid"];return $v;')));
            View::share('list', $list);
            View::share('pids',$pids);
        }
        //var_dump($list[0]['seller']);
        return $this->display();
    }

    /**
     * 添加商家分类
     */
    public function create(){
        $seller_cate_result = $this->requestApi('system.cate.lists');
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
        if( !empty($args['id']) ) {
            $result = $this->requestApi('seller.cate.get', ['id'=>$args['id']]);
            if($result['code'] == 0){
                $cates = $this->requestApi('seller.cate.catesall');
                $cate = $cates['data'];
                foreach($cate as $k=>$v) {
                    unset($cate[$k]['seller']);
                }
                $cate2 = [
                    "id" => 0,
                    "pid" => 0,
                    "name" => "顶级分类",
                    "sort" => 100,
                    "status" => 1,
                    "level" => 0,
                    "levelname" => "顶级分类"
                ];
                array_unshift($cate,$cate2);
                View::share('data', $result['data']);
                View::share('cate', $cate);
            }
        }
        return $this->display();
    }

    /**
     * 保存商家分类
     */
    public function save() {
        $args = Input::all();
        $result = $this->requestApi('system.cate.update', $args);
        if($result['code'] == 0){
            return $this->success($result['msg'] ? $result['msg'] : Lang::get('admin.code.98008'), $url);
        }else{
            return $this->error($result['msg'] ? $result['msg'] : Lang::get('admin.code.98009'));
        }

    }

    //获取分类
    public function getcate() {
        $list = [];
        $result = $this->requestApi('system.cate.lists');
        foreach($result['data'] as $k=>$v) {
            $list[] = [
                'id' => $v['id'],
                'levelname' => $v['name'],
                'pid' => $v['pid'],
                'levelrel' => $v['name'],
                'sort' => $v['sort'],
                'status' => $v['status'],
                'seller' => $v['seller']
            ];
            if (count($v['childs']) > 0) {
                foreach($v['childs'] as $val) {
                    $list[] = [
                        'id' => $val['id'],
                        'levelname' => '&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: #B40001">➤</span>'.$val['name'],
                        'pid' => $val['pid'],
                        'levelrel' => $v['name'].'|'.$val['name'],
                        'sort' => $val['sort'],
                        'status' => $val['status'],
                        'seller' => $val['seller']
                    ];
                }
            }
        }
        return $list;
    }

    /**
     * 删除商家分类
     */
    public function destroy(){
        $args = Input::get();
        $data = $this->requestApi('seller.cate.delete', ['id' => explode(',', $args['id'])]);
        $url = u('SellerCate/index');
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'),$url );
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), $url , $data['data']);
    }


}
