<?php
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\Goods;
use View, Input, Lang, Route, Page, Validator, Session, Response;
/**
 * 系统通用服务库
 */
class SystemGoodsController extends AuthController {
    /**
     * 服务管理-服务列表
     */
    public function index() {
        $args = Input::all();
        $result = $this->requestApi('system.goods.lists', $args);
        if ($result['code'] == 0)
            View::share('list', $result['data']['list']);
        return $this->display();
    }

    /**
     * 服务管理-更新服务详细
     */
    public function edit() {
        $args = Input::all();
        $result = $this->requestApi('system.goods.get', $args);
        View::share('data', $result['data']);

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

        //获取标签列表（二级）
        $tagList3 = $this->requestApi('systemTagList.secondLevel', ['pid'=>$result['data']['systemTagListPid']]);
        $tagList3 = $tagList3['data'];
        array_unshift($tagList3,$tagList2);
        View::share('systemTagListId', $tagList3);

        $stock = $this->requestApi('stock.getLists',['status' => 1]);
        View::share('stock', $stock['data']['list']);

        $stockItem = $this->requestApi('stock.getStock',['goodsId' => $result['data']['id'],'stockId' => $result['data']['stockTypeId'],'isSystem' =>1]);

        View::share('stockItem', $stockItem);
        return $this->display();
    }

    /**
     * 服务管理-添加服务详细
     */
    public function create() {


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

        //获取标签列表（二级）
        $tagList3 = $this->requestApi('systemTagList.secondLevel');
        $tagList3 = $tagList3['data'];
        array_unshift($tagList3,$tagList2);
        View::share('systemTagListId', $tagList3);

        $stock = $this->requestApi('stock.getLists',['status' => 1]);
        View::share('stock', $stock['data']['list']);

        return $this->display('edit');
    }

    /**
     * 服务管理-添加，更新服务处理
     */
    public function save() {
        $args = Input::all();
        $args['isSystem'] = 1;
        $args['norms']['stock'] = $args['stock_id'];
        $args['norms']['skuItem'] = $args['sku_item'];
        $args['norms']['skuPrice'] = $args['sku_price'];
        $args['norms']['skuStock'] = $args['sku_stock'];
        unset($args['sku_stock'],$args['sku_price'],$args['sku_item'],$args['stock_id'],$args['addmoney'],$args['addstock']);
        if(count($args['norms']['skuPrice']) != count($args['norms']['skuStock'])){
            return $this->error("有为空的选项,请检查");
        }
        $result = $this->requestApi('system.goods.update',$args);
        if ( $result['code'] > 0 )
            return $this->error($result['msg']);

        return $this->success(Lang::get('admin.code.98008'), u('SystemGoods/index'), $result['data']);
    }

    /**
     * 服务管理-删除服务
     */
    public function destroy() {
        $args = Input::all();
        $args['id'] = explode(',', $args['id']);
        if ( !empty( $args['id'] ) )
            $result = $this->requestApi('system.goods.delete',$args);

        if( $result['code'] > 0 )
            return $this->error($result['msg']);

        return $this->success(Lang::get('admin.code.98005'));

    }

    /**
     * 查找二级分类
     */
    public function selectSecond() {
        $args = Input::all();
        $result = $this->requestApi('goods.cate.selectSecond',$args);
        return Response::json($result['data']);
    }

    //获取商品标签分类
    public function getcate() {
        $list = [];
        $result = $this->requestApi('systemTagList.lists');

        foreach($result['data'] as $k=>$v) {
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

    public function updateStatus() {
        $args = Input::all();
        $result = $this->requestApi('system.goods.updateStatus', $args);
        echo json_encode($result);
    }

}