<?php 
namespace YiZan\Http\Controllers\Admin; 

use View, Input, Form,Lang; 

/**
*广告管理
*/
class UserAppAdvController extends AuthController { 

	protected function requestApi($method, $args = [],$data = []){
		!empty($this->clietnType) ? $this->clietnType : $this->clietnType = 'buyer';
        $args['code'] = $this->clietnType;
        return parent::requestApi($method, $args,$data = []);
	} 
	/**
	 * 广告 列表
	*/
	public function index() { 
        $args = Input::all();
		//$args["code"] = "BUYER_INDEX_BANNER";
		$result = $this->requestApi('adv.lists',$args);
		if( $result['code'] == 0)
			View::share('list', $result['data']['list']);
        $is_show_adv = 1;
        View::share('is_show_adv', $is_show_adv);
        
		return $this->display();
	}	
	/**
	 * 添加广告
	*/
	public function create() {
		$positions = $this->requestApi('adv.position.lists',['clientType' => 'buyer']);
        if( $positions['code'] == 0){
            foreach ($positions['data'] as $key => $value) {
                if($value['code'] == 'BUYER_INDEX_MENU'){
                    $positionsId  = $value['id'];
                }
                if($value['code'] == 'BUYER_SELLER_BANNER'){
                    $bsAdvPositionId  = $value['id'];
                }
                if($value['code'] == 'BUYER_INDEX_ADV'){
                    unset($positions['data'][$key]);
                }
            }
            View::share('positionsId',$positionsId);
            View::share('bsAdvPositionId',$bsAdvPositionId);
            View::share('positions', $positions['data']);
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
        $list = $this->requestApi('article.lists');
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
	public function edit() {
		$positions = $this->requestApi('adv.position.lists',['clientType' => 'buyer']);
		if( $positions['code'] == 0){
			foreach ($positions['data'] as $key => $value) {
				if($value['code'] == 'BUYER_INDEX_MENU'){
					$positionsId  = $value['id'];
				}
                if($value['code'] == 'BUYER_SELLER_BANNER'){
                    $bsAdvPositionId  = $value['id'];
                }
			}
			View::share('positionsId',$positionsId);
            View::share('bsAdvPositionId',$bsAdvPositionId);
            View::share('positions', $positions['data']);
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
      //  print_r($sellerCate);
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
        $list = $this->requestApi('article.lists');
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
	 * 更新广告
	*/
	public function update() {
		!empty($this->clietnType) ? $url = ucfirst($this->clietnType) : $url = 'User'; 
		$args = Input::all();
		!empty($args['id']) ?   $args['id']  = intval($args['id'])  :  $args['id'] = 0;

		if($args['id'] > 0 ){
			$data = $this->requestApi('adv.update',$args);  
			if( $data['code'] == 0 ) {
				if(!empty($this->WapModuletype)){ 
					return $this->success($data['msg'] ? $data['msg'] : $data['msg'] = Lang::get('admin.code.98003'),u('WapModule/edit',[ 'id'=>$args['id'] ]));
				}else{
					return $this->success($data['msg'] ? $data['msg'] : $data['msg'] = Lang::get('admin.code.98003'),u('UserAppAdv/edit',[ 'id'=>$args['id'] ]));
				}
			}
			else {
				return $this->error($data['msg'] ? $data['msg'] : $data['msg']=Lang::get('admin.code.98004'),'',$args);
			}	
		}else{  
			$args['createTime'] = UTC_TIME;
			$data = $this->requestApi('adv.create',$args);
			if( $data['code'] == 0 ) {
				if(!empty($this->WapModuletype)){ 
					return $this->success($data['msg'] ? $data['msg'] : $data['msg'] = Lang::get('admin.code.98001'),u('WapModule/create'));
				}else{
					return $this->success($data['msg'] ? $data['msg'] : $data['msg'] = Lang::get('admin.code.98001'),u('UserAppAdv/create'));
				}
			}
			else {
				return $this->error($data['msg'] ? $data['msg'] : $data['msg']=Lang::get('admin.code.98002'),'',$args);
			}	
		}  
	}
	/**
	 * 广告状态设置
	*/
	public function setstatus() {
		!empty($this->clietnType) ? $url = ucfirst($this->clietnType) : $url = 'User'; 
		$data = $this->requestApi('adv.setstatus', Input::all());
		if( $data['code'] == 0 ) {
			if(!empty($this->WapModuletype)){ 
				return $this->success($data['msg'] ,u('WapModule/create'), $data['data']);
			}else{
				return $this->success($data['msg'], u('UserAppAdv.index'), $data['data']);
			}
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
        $args = Input::all();
        $id = (int)Input::get('id');
        if (empty($id)) {
			return $this->error(Lang::get('admin.noId'),u('UserAppAdv/index'));
		}
		$args['id']  = $id;
		$data = $this->requestApi('adv.delete',$args);
		if( $data['code'] > 0 ) {
			return $this->error($data['msg'], '');
		}
		if(!empty($this->WapModuletype)){
			return $this->success($data['msg'] ,u('WapModule/index'), $data['data']);
		}else{
			return $this->success($data['msg'], u('UserAppAdv/index'), $data['data']);
		}
	} 
	
	//获取分类
	public function getcate() {
		$result = $this->requestApi('goods.cate.lists');
		if($result['code']==0)
			$this->generateTree(0,$result['data']);

		//生成树形
		$cate = $this->_cates;
		return $cate;
	}
    //获取分类
	public function getarticle() {
		$result = $this->requestApi('article.cate.lists');
		if($result['code']==0) {
			$this->generateTree(0,$result['data']);
		}
		//生成树形
		$cate = $this->_cates;
		return $cate;
	}

    /**
     * 创建广告
     */
    public function create_adv(){
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
        $list = $this->requestApi('article.lists');
        if($list['code'] == 0) {
            View::share('article', $list['data']['list']);
        }

        //开通的城市列表
        $citys = $this->requestApi('city.getcitylists');
        array_unshift($citys['data'], ['id' => 0,'name' => '所有城市']);
        View::share('citys', $citys['data']);

        return $this->display('edit_adv');
    }

    /**
     * 更新广告
     */
    public function edit_adv() {
        $positions = $this->requestApi('adv.position.lists',['clientType' => 'buyer']);
        if( $positions['code'] == 0){
            View::share('positions', $positions['data']);
        }
        $result = $this->requestApi('adv.get',Input::all());
        if(!empty($result['data']['dataJson'])){
            $result['data']['dataJson'] = json_decode($result['data']['dataJson'],true);
        }

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
        //  print_r($sellerCate);
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
        $list = $this->requestApi('article.lists');
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
     * 更新广告
     */
    public function update_adv() {
        !empty($this->clietnType) ? $url = ucfirst($this->clietnType) : $url = 'User';
        $args = Input::all();
        !empty($args['id']) ?   $args['id']  = intval($args['id'])  :  $args['id'] = 0;
        $args['isAdv'] = 1;

        if(empty($args['upData'])){
            return $this->error('请填写内容',u('UserAppAdv/edit_adv',[ 'id'=>$args['id'] ]));
        }

        if($args['id'] > 0 ){
            $data = $this->requestApi('adv.update',$args);
            if( $data['code'] == 0 ) {
                if(!empty($this->WapModuletype)){
                    return $this->success($data['msg'] ? $data['msg'] : $data['msg'] = Lang::get('admin.code.98003'),u('WapModule/edit',[ 'id'=>$args['id'] ]));
                }else{
                    return $this->success($data['msg'] ? $data['msg'] : $data['msg'] = Lang::get('admin.code.98003'),u('UserAppAdv/edit_adv',[ 'id'=>$args['id'] ]));
                }
            }
            else {
                return $this->error($data['msg'] ? $data['msg'] : $data['msg']=Lang::get('admin.code.98004'),'',$args);
            }
        }else{
            $args['createTime'] = UTC_TIME;
            $data = $this->requestApi('adv.create',$args);
            if( $data['code'] == 0 ) {
                if(!empty($this->WapModuletype)){
                    return $this->success($data['msg'] ? $data['msg'] : $data['msg'] = Lang::get('admin.code.98001'),u('WapModule/create'));
                }else{
                    return $this->success($data['msg'] ? $data['msg'] : $data['msg'] = Lang::get('admin.code.98001'),u('UserAppAdv/create_adv'));
                }
            }
            else {
                return $this->error($data['msg'] ? $data['msg'] : $data['msg']=Lang::get('admin.code.98002'),'',$args);
            }
        }
    }

}
