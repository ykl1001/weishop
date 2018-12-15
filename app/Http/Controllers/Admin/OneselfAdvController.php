<?php
namespace YiZan\Http\Controllers\Admin;
use Input,View,Lang;
/**
 *广告管理
 */
class OneselfAdvController extends UserAppAdvController {
    protected $clietnType;
    public function __construct() {
        parent::__construct();
        $this->clietnType = 'oneself';
    }
    /**
     * 广告 列表
     */
    public function index() {
        $args = Input::all();
        $args["code"] = "oneself";
        $result = $this->requestApi('adv.OneselfAdvlists',$args);
        if( $result['code'] == 0)
            View::share('list', $result['data']['list']);
        return $this->display();
    }
    /**
     * 添加广告
     */
    public function create() {
        $positions = $this->requestApi('adv.position.lists');
        if( $positions['code'] == 0){
            foreach ($positions['data'] as $key => $value) {
                if($value['code'] == 'BUYER_SYSTEM_ONESELF' || $value['code'] == 'BUYER_SYSTEM_ONESELF_MENU'){
                    $positionsId  = $value['id'];
                    $position[] = $value;
                   continue;
                }
            }
            View::share('positionsId',$positionsId);
            View::share('positions', $position);
        }

        //商家分类
        $list = $this->requestApi('seller.cate.catesall');
        // print_r($list);
        if($list['code'] == 0) {
            $sellerCate[] = [
                'id' => 0,
                'name' => '请选择',
                'childs' => [],
            ];
            foreach ($list['data'] as $key => $value) {
                $sellerCate[] = [
                    'id' => $value['id'],
                    'name' => $value['name'],
                    'childs' => $value['childs'],
                ];
            }
            View::share('sellerCate', $sellerCate);
        }

        //商品
        $list = $this->requestApi('system.goods.lists');
        // print_r($list);
        if($list['code'] == 0) {
            $goods[] = [
                'id' => 0,
                'name' => '请选择'
            ];
            foreach ($list['data']['list'] as $key => $value) {
                $goods[] = [
                    'id' => $value['id'],
                    'name' => $value['name'],
                ];
            }
            View::share('service', $goods);
        }

        //文章
        $list = $this->requestApi('article.lists', Input::all());
        if($list['code'] == 0) {
            View::share('article', $list['data']['list']);
        }

        //开通的城市列表
        $citys = $this->requestApi('city.getcitylists');
        array_unshift($citys['data'], ['id' => 0,'name' => '所有城市']);
        View::share('citys', $citys['data']);
        return $this->display('edit');
    }
	
    /**
     * 更新广告
     */
    public function update() {
        $args = Input::all();
        
        !empty($args['id']) ?   $args['id']  = intval($args['id'])  :  $args['id'] = 0;
        if($args['id'] > 0 ){
            $data = $this->requestApi('adv.update',$args);
            if( $data['code'] == 0 ) {
                return $this->success($data['msg'] ? $data['msg'] : $data['msg'] = Lang::get('admin.code.98003'),u('OneselfAdv/edit',[ 'id'=>$args['id'] ]));
            }
            else {
                return $this->error($data['msg'] ? $data['msg'] : $data['msg']=Lang::get('admin.code.98004'),'',$args);
            }
        }else{
            $args['createTime'] = UTC_TIME;
            $data = $this->requestApi('adv.create',$args);
            if( $data['code'] == 0 ) {
                return $this->success($data['msg'] ? $data['msg'] : $data['msg'] = Lang::get('admin.code.98001'),u('OneselfAdv/edit',[ 'id'=>$args['id'] ]));
            }
            else {
                return $this->error($data['msg'] ? $data['msg'] : $data['msg']=Lang::get('admin.code.98002'),'',$args);
            }
        }
    }
    /**
     * 更新广告
     */
    public function edit() {
        $positions = $this->requestApi('adv.position.lists');
        if( $positions['code'] == 0){
            foreach ($positions['data'] as $key => $value) {
                if($value['code'] == 'BUYER_SYSTEM_ONESELF' || $value['code'] == 'BUYER_SYSTEM_ONESELF_MENU'){
                    $positionsId  = $value['id'];
                    $position[] = $value;
                    continue;
                }
            }
            View::share('positionsId',$positionsId);
            View::share('positions', $position);
        }
        $result = $this->requestApi('adv.get',Input::all());

        if($result['code'] == 0 )
            View::share('data', $result['data']);
        //商家分类
        $list = $this->requestApi('seller.cate.catesall');
        if($list['code'] == 0) {
            $sellerCate[] = [
                'id' => 0,
                'name' => '请选择',
                'childs' => [],
            ];
            foreach ($list['data'] as $key => $value) {
                $sellerCate[] = [
                    'id' => $value['id'],
                    'name' => $value['name'],
                    'childs' => $value['childs'],
                ];
            }
            View::share('sellerCate', $sellerCate);
        }
        //商品
        $list = $this->requestApi('system.goods.lists');
        // print_r($list);
        if($list['code'] == 0) {
            $goods[] = [
                'id' => 0,
                'name' => '请选择'
            ];
            foreach ($list['data']['list'] as $key => $value) {
                $goods[] = [
                    'id' => $value['id'],
                    'name' => $value['name'],
                ];
            }
            View::share('service', $goods);
        }

        //文章
        $list = $this->requestApi('article.lists', Input::all());
        if($list['code'] == 0) {
            View::share('article', $list['data']['list']);
        }

        //开通的城市列表
        $citys = $this->requestApi('city.getcitylists');
        array_unshift($citys['data'], ['id' => 0,'name' => '所有城市']);
        View::share('citys', $citys['data']);

        return $this->display();
    }
    /**
     * 广告状态设置
     */
    public function setstatus() {
        !empty($this->clietnType) ? $url = ucfirst($this->clietnType) : $url = 'User';
        $data = $this->requestApi('adv.setstatus', Input::all());
        if( $data['code'] == 0 ) {
            return $this->success($data['msg'], u('OneselfAdv.index'), $data['data']);
        }
        else {
            return $this->error($data['msg'], '', $data['data']);
        }
    }
    /**
     * 删除广告
     */
    public function destroy() {
        !empty($this->clietnType) ? $url = ucfirst($this->clietnType) : $url = 'User';
        $id = explode(',', Input::get('id'));
        $args = array();
        if (empty($id)) {
            return $this->error(Lang::get('admin.noId'),u('OneselfAdv/index'));
        }
        $args['id']  = $id;
        $data = $this->requestApi('adv.delete',$args);
        if( $data['code'] > 0 ) {
            return $this->error($data['msg'], '');
        }
        return $this->success($data['msg'], u('OneselfAdv/index'), $data['data']);
    }

}