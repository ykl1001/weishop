<?php 
namespace YiZan\Http\Controllers\Admin;

use View, Input, Lang, Route, Page, Response;

/**
 * 商品标签分类
 */
class SystemTagListController extends AuthController {


    /**
     * 商品标签分类列表
     */
    public function index() {
        $args = Input::all();
        $args['pid'] = 0;
        $result = $this->requestApi('systemTagList.getListItem',$args);
        View::share('list', $result['data']['list']);
        return $this->display();
    }
    /**
	 * 商品标签分类列表
	 */
	public function item() {
        $args = Input::all();
        $result = $this->requestApi('systemTagList.getListItem',$args);
        View::share('list', $result['data']['list']);
        return $this->display();
    }

    /**
     * 添加商品标签分类
     */
    public function create(){
        //获取标签列表
        $tagList = $this->requestApi('systemTagList.lists2');

        $tagList = $tagList['data'];
        $tagList2 = [ 
            "id" => 0,
            "pid" => 0,
            "name" => "顶级分类",
            "sort" => 100,
            "status" => 1,
            "level" => 0,
            "levelname" => "顶级分类"
        ];
        array_unshift($tagList,$tagList2);

        View::share('tagList', $tagList);

        //标签分类
        $tag = $this->requestApi('systemTag.lists');
        $tag = $tag['data'];
        $tag2 = [ 
            "id" => 0,
            "name" => "请选择"
        ];
        array_unshift($tag,$tag2);
        View::share('tag', $tag);

        return $this->display('edit');
    }

    /**
     * 编辑商品标签分类
     */
    public function edit() {
        $args = Input::all();
        if( !empty($args['id']) ) {
            $data = $this->requestApi('systemTagList.get', ['id'=>$args['id']]);
            View::share('data', $data['data']);

            //获取标签列表
            $tagList = $this->requestApi('systemTagList.lists');

            $tagList = $tagList['data'];
            $tagList2 = [ 
                "id" => 0,
                "pid" => 0,
                "name" => "顶级分类",
                "sort" => 100,
                "status" => 1,
                "level" => 0,
                "levelname" => "顶级分类"
            ];
            array_unshift($tagList,$tagList2);
            View::share('tagList', $tagList);

            //标签分类
            $tag = $this->requestApi('systemTag.lists');
            $tag = $tag['data'];
            $tag2 = [ 
                "id" => 0,
                "name" => "请选择"
            ];
            array_unshift($tag,$tag2);
			
            View::share('tag', $tag);
        }
        return $this->display();
    }

    /**
     * 保存商品标签分类
     */
    public function save() {
        $args = Input::get();
        $result = $this->requestApi('systemTagList.save', $args);

        $url = u('SystemTagList/index');
        
        if($result['code'] == 0){
            return $this->success($result['msg'] ? $result['msg'] : Lang::get('admin.code.98008'), $url);
        }else{
            return $this->error($result['msg'] ? $result['msg'] : Lang::get('admin.code.98009'));
        }

    }

    //获取商品标签分类
    public function getcate() {
        $args = Input::all();
        $list = [];
        $result = $this->requestApi('systemTagList.getListItem',$args);

        foreach($result['data']['list'] as $k=>$v) {
            $list[] = [
                'id' => $v['id'],
                'levelname' => $v['name'],
                'pid' => $v['pid'],
                'levelrel' => $v['name'],
                'sort' => $v['sort'],
                'status' => $v['status'],
                'img' => $v['img'],
                'systemTagId' => $v['systemTagId'],
                'isDel' => count($v['childs']) > 0 ? 1 : 0,  //如果存在子分类 不可删 0=可以删除 1=不可以删除
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
                       'img' => $v['img'],
                       'systemTagId' => $v['systemTagId'],
                       'tag' => $val['tag'],
                       'isDel' => $val['useTag']!=null ? 1 : 0, //如果存在商品使用该分类 则不可删 0=可以删除 1=不可以删除
                   ];
               }
           }
        }
        return $list;
    }

    /**
     * 通过一级标签获取二级标签
     */
    public function secondLevel() {
        $args = Input::all();
        $result = $this->requestApi('systemTagList.secondLevel', $args);
        return Response::json($result['data']);
    }

    /**
     * 删除商品标签分类
     */
    public function destroy(){
        $args = Input::get();
        $data = $this->requestApi('systemTagList.delete', ['id' => explode(',', $args['id'])]);
        $url = u('systemTagList/index');
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'),$url );
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), $url , $data['data']);
    }


}

