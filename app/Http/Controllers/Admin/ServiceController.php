<?php
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\Goods;
use YiZan\Http\Requests\Admin\GoodsCreatePostRequest;

use YiZan\Utils\Http;

use View, Input, Lang, Route, Page, Validator, Session, Response, Cache, Config;
/**
 * 商家
 */
class ServiceController extends AuthController {
    /**
     * 商家管理-商家列表
     */
    public function index() {
        $args = Input::all();
        $result = $this->requestApi('seller.lists', $args);
        if ($result['code'] == 0)
            View::share('list', $result['data']['list']);
        $cateIds = $this->requestApi('seller.cate.catesall');
        View::share('cateIds',$cateIds['data']);
        View::share('args', $args);

        //获取分销模式，分销通道
        $passageId = \YiZan\Http\Controllers\Admin\FxController::get_enabled_passages();
        //获取分销方案
        $schemeId = \YiZan\Http\Controllers\Admin\FxController::query_commission_schemes();

        View::share('passageId', $passageId);
        View::share('schemeId', $schemeId);

        return $this->display();
    }


    /**
     * 添加商家
     */
    public function create() {
        $cateIds = $this->requestApi('seller.cate.catesall');
        if($cateIds['code'] == 0)
            View::share('cateIds',$cateIds['data']);

        //获取商家是否是全国店
        // $seller = $this->requestApi('seller.get', ['id'=>$args['sellerId']]);
        // View::share('storeType', $seller['data']['storeType']);

        //【分销平台】全国店获取分销模式
        // if(FANWEFX_SYSTEM && $seller['data']['storeType'] == 1)
        // {
        //获取分销模式，分销通道
        $passageId = \YiZan\Http\Controllers\Admin\FxController::get_enabled_passages();
        //获取分销方案
        $schemeId = \YiZan\Http\Controllers\Admin\FxController::query_commission_schemes();

        View::share('passageId', $passageId);
        View::share('schemeId', $schemeId);
        // View::share('fx', true);
        // }

        $this->getAuthIcons();
        return $this->display('edit');
    }


    /**
     * 商家管理-更新商家详细
     */
    public function edit() {
        $args = Input::all();
        $data = $this->requestApi('seller.get', $args);
        $seller = $data['data'];
        $seller['idcardSn'] = $seller['authenticate']['idcardSn'];
        $seller['idcardPositiveImg'] = $seller['authenticate']['idcardPositiveImg'];
        $seller['idcardNegativeImg'] = $seller['authenticate']['idcardNegativeImg'];
        $seller['businessLicenceImg'] = $seller['authenticate']['businessLicenceImg'];
        $seller['certificateImg'] = $seller['authenticate']['certificateImg'];
        $seller['authIcons'] = [];
        foreach($seller['sellerAuthIcon'] as $v){
            $seller['authIcons'][] = $v['iconId'];
        }
        // $seller['deliveryTime'] = explode('-', $seller['deliveryTime']);
        View::share('data', $seller);
        $staffstime = $this->requestApi('staffstime.lists',$args);
        if($staffstime['code'] == 0)
            View::share('stime', $staffstime['data']);

        $cateIds = $this->requestApi('seller.cate.catesall');
        if($cateIds['code'] == 0)
            View::share('cateIds',$cateIds['data']);
        if($data['code'] == 0){
            $_cateIds = [];
            foreach ($data['data']['sellerCate'] as $cate) {
                $_cateIds[] = $cate['cateId'];
            }
            View::share('_cateIds',$_cateIds);
        }

        //获取商家是否是全国店
        // $seller = $this->requestApi('seller.get', ['id'=>$args['sellerId']]);
        // View::share('storeType', $seller['data']['storeType']);

        //【分销平台】全国店获取分销模式
        // if(FANWEFX_SYSTEM && $seller['data']['storeType'] == 1)
        // {
        //获取分销模式，分销通道
        $passageId = \YiZan\Http\Controllers\Admin\FxController::get_enabled_passages();
        //获取分销方案
        $schemeId = \YiZan\Http\Controllers\Admin\FxController::query_commission_schemes();

        View::share('passageId', $passageId);
        View::share('schemeId', $schemeId);
        // View::share('fx', true);
        // }

        $this->getAuthIcons();
        return $this->display();
    }

    /**
     * 商家认证图标
     */
    public function getAuthIcons() {
        $authIcon = $this->requestApi('Sellerauthicon.lists',['pageSize' => 9999]);
        $authIcons = [];
        if ($authIcon['data']['totalCount'] > 0) {
            foreach ($authIcon['data']['list'] as $k=>$v) {
                $authIcons['iconIds'][$k] = $v['id'];
                $authIcons['iconNames'][$k] = $v['name'];
            }
        }
        View::share('authIcons',$authIcons);
    }
    /**
     * 保存商家
     */
    public function save() {
        $args = Input::get();

        $detime['stimes'] =  $args['_stime'];
        $detime['etimes'] = $args['_etime'];
        if (count($detime['stimes']) != count($detime['etimes'])) {
            return $this->error('配送时间没填写完整');
        }
        if (count($detime['stimes']) > 3 || count($detime['etimes']) > 3) {
            return $this->error('配送时间段最多可设置三个');

        }
        $args['deliveryTime'] = json_encode($detime);
        if (empty($args['cateIds'])) {
            return $this->error('请至少选择一个经营类型');
        }

        // 全国店清空服务范围 清空起送费 配送费 满免 货到付款 配送方式 服务方式 预约天数 配送时间周期 及全国店部分数据验证
        if($args['storeType'] == 1)
        {
            $args['mapPos'] = null;
            $args['mapPoint'] = $args['_mapPoint'];
            $args['address'] = $args['_address'];

            $args['serviceFee'] = 0;
            $args['deliveryFee'] = 0;
            $args['isAvoidFee'] = 0;
            $args['avoidFee'] = 0;
            $args['isCashOnDelivery'] = null;
            $args['sendWay'] = [0=>''];
            $args['serviceWay'] = [0=>''];
            $args['reserveDays'] = null;
            $args['sendLoop'] = null;
            $args['refundAddress'] = trim($args['refundAddress']);
        }
        // 周边店部分数据验证
        else
        {
            if($args['isAvoidFee'] == 1 && $args['avoidFee'] <= 0 ){
                return $this->error('请设置满免金额');
            }
            if($args['deliveryFee'] <= 0 && $args['isAvoidFee'] == 1){
                return $this->error('配送费已经为0，无需再设置满免');
            }
        }

        unset($args['_mapPoint']);
        unset($args['_address']);

        $args['cateIds'] = is_array($args['cateIds']) ? $args['cateIds'] : explode(',', $args['cateIds']);

        //如果没有商家配送 则清空配送服务
        if(!in_array(1, $args['sendWay']))
        {
            $args['sendType'] = null;
        }

        if ((int)$args['id'] < 1) {
            $result = $this->requestApi('seller.create', $args);
        } else {
            $result = $this->requestApi('seller.update', $args);
        }
        if ($result['code'] > 0) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98008'), u('Service/index'), $result['data']);
    }


    /**
     * 商家管理-删除商家
     */
    public function destroy() {
        $args = Input::all();
        if(!is_array($args['id']))
        {
            $args['id'] = explode(',', $args['id']);
        }
        else
        {
            $args['id'] = array_filter($args['id']);
        }

        if ( !empty( $args['id'] ) )
            $result = $this->requestApi('seller.delete',['id' => $args['id']]);

        if( $result['code'] > 0 )
            return $this->error($result['msg']);

        return $this->success(Lang::get('admin.code.98005'), u('Service/index'), $result['data']);
    }



    /**
     * 修改状态
     */
    public function updateStatus() {
        if(Input::get('ref_module') == 'SystemGoods'){
            $api = 'system.goods';
        } else if(Input::get('ref_module') == 'goods') {
            $api = 'goods';
        } elseif(Input::get('ref_module') == 'GoodsCate'){
            $api = 'goods.cate';
        } else {
            $api = 'seller';
        }
        $result = $this->requestApi($api.'.updatestatus',[
            'id' => Input::input('id'),
            'status' => Input::input('val'),
            'field' => 'status'
        ]);
        $result = array (
            'status'    => true,
            'data'      => Input::input('val'),
            'msg'       => null
        );
        return Response::json($result);
    }


    /**
     * 获取商家栏目
     */
    public static function getType() {
        $type = [
            ['name' => '请选择', 'value' => '0'],
            ['name' => '跑腿', 'value' => '2'],
            ['name' => '家政', 'value' => '3'],
            ['name' => '汽车', 'value' => '4'],
            ['name' => '其他', 'value' => '5'],
        ];
        View::share('type', $type);
    }

    /*
    * 保存银行卡
    */
    public function banksave() {
        $args = Input::all();
        $result = $this->requestApi('seller.bankupdate', $args);
        return Response::json($result);
    }

    public function delbank() {
        $args = Input::all();
        //var_dump($args);
        $result = $this->requestApi('seller.bankdelete', $args);
        if( $result['code'] > 0 )
            return $this->error($result['msg']);

        return $this->success(Lang::get('admin.code.98005'), u('Service/edit',['id'=>$args['sellerId']]), $result['data']);
    }

    public function gettimes()
    {
        $args['id'] = (int)Input::get('id');
        $staffstime = $this->requestApi('staffstime.lists',$args);

        return Response::json($staffstime);
    }
    public function showtime()
    {
        $staffstime = $this->requestApi('staffstime.edit',Input::all());
        return Response::json($staffstime);
    }

    /*
    *更新预约时间
    */
    public function updatetime(){
        $args = Input::all();
        $data  = $this->requestApi('staffstime.update',$args);
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'), url('Service/index'));
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), url('Service/index'), $data['data']);
    }
    /*
    *添加预约时间
    */
    public function addtime(){
        $args = Input::all();
        // var_dump($args);
        // exit;
        $data  = $this->requestApi('staffstime.add',$args);
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'), url('Service/index'));
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), url('Service/index'), $data['data']);
    }
    /**
     * [destroy 删除时间]
     */
    public function deldatatime(){
        $args = Input::all();
        //var_dump($args);
        $result = $this->requestApi('staffstime.delete',$args);
        return Response::json($result);
    }

    /**
     * [servicelists 商家列表]
     */
    public function serviceLists(){
        $args = Input::all();
        $args['type'] = Goods::SELLER_SERVICE;
        $result = $this->requestApi('goods.lists', $args);
        //var_dump($result['data']);
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
        $result_cate = $this->requestApi('goods.cate.lists', $args);
        //var_dump($result_cate['data']);
        View::share('cate', $result_cate['data']);
        View::share('args', $args);
        View::share('excel',http_build_query($args));
        return $this->display();
    }

    /**
     * [oodslists 商品列表]
     */
    public function goodsLists(){
        $args = Input::all();
        $args['type'] = Goods::SELLER_GOODS;
        $result = $this->requestApi('goods.lists', $args);
        // print_r($result);
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
        View::share('args', $args);
        View::share('excel', http_build_query($args));
        return $this->display();
    }

    /**
     * [catelists 分类列表]
     */
    public function cateLists(){
        $args = Input::all();
        $result_cate = $this->requestApi('goods.cate.lists', $args);
        View::share('list', $result_cate['data']);
        View::share('args', $args);
        return $this->display();
    }

    public function cateedit(){
        $args = Input::all();
        $result_cate = $this->requestApi('goods.cate.get', $args);
        View::share('data', $result_cate);

        $seller_cate_result = $this->requestApi('seller.cate.catelists',$args);
        // print_r($seller_cate_result);
        foreach ($seller_cate_result['data'] as $value) {
            $cate[] = $value['cates'];
        }
        //print_r($cate);
        View::share('cate', $cate);
        View::share('args', $args);

        return $this->display('editgoodscate');
    }

//    public function catesave(){
//        $args = Input::all();
//        $result = $this->requestApi('goods.cate.update', $args);
//        if ($result['code'] > 0) {
//            return $this->error($result['msg']);
//        }
//        return $this->success(Lang::get('admin.code.98008'), $url, $result['data']);
//    }

    /**
     * [serviceedit 编辑商家]
     */
    public function serviceEdit(){
        $args = Input::all();
        $args['type'] = Goods::SELLER_SERVICE;
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
        //$servicestime = $this->requestApi('servicestime.lists',$args); 
        // var_dump($servicestime['data']);
        //View::share('stime', $servicestime['data']);
        return $this->display();
    }

    /**
     * 添加服务
     */
    public function createService(){
        $args = Input::all();
        $args['type'] = Goods::SELLER_SERVICE;
        $result_cate = $this->requestApi('goods.cate.lists',['sellerId'=>$args['sellerId'],'type'=>Goods::SELLER_SERVICE]);
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
        $tagList3 = $this->requestApi('systemTagList.secondLevel');
        $tagList3 = $tagList3['data'];
        array_unshift($tagList3,$tagList2);
        View::share('systemTagListId', $tagList3);
        View::share('args', $args);
        View::share('data', $args);
        return $this->display('serviceEdit');
    }

    public function serviceSave() {
        $args = Input::all();
        if($args['type'] == Goods::SELLER_SERVICE){
            if(isset($args['staffIds']) && !empty($args['staffIds'])){
                $args['staffIds'] = explode(',', $args['staffIds']);
            }
            $url = u('Service/serviceLists', ['sellerId'=>$args['sellerId']]);
        } else {
            $args['norms']['stock'] = $args['stock_id'];
            $args['isSystem'] = 0;
            $args['norms']['skuItem'] = $args['sku_item'];
            $args['norms']['skuPrice'] = $args['sku_price'];
            $args['norms']['skuStock'] = $args['sku_stock'];

            //unset($args['sku_stock'],$args['sku_price'],$args['sku_item'],$args['stock_id'],$args['addmoney'],$args['addstock']);

            if(count($args['norms']['skuPrice']) != count($args['norms']['skuStock'])){
                return $this->error("有为空的选项,请检查");
            }
            $url = u('Service/goodsLists', ['sellerId'=>$args['sellerId']]);
        }

        if( $args['id'] > 0 ){
            $result = $this->requestApi('goods.update',$args);
            $msg = Lang::get('seller.code.98003');
        } else {
            $result = $this->requestApi('goods.create',$args);
            $msg = Lang::get('seller.code.98001');
        }
        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }

        return $this->success($msg, $url);
    }

    /**
     * [oodsedit 编辑商品]
     */
    public function goodsEdit(){
        $args = Input::all();
        $args['type'] = Goods::SELLER_GOODS;
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
        View::share('args', $args);

        $stock = $this->requestApi('stock.getLists',['status' => 1]);
        View::share('stock', $stock['data']['list']);

        $stockItem = $this->requestApi('stock.getStock',['goodsId' => $result['data']['id'],'stockId' => $result['data']['stockTypeId']]);
        View::share('stockItem', $stockItem);
        return $this->display();
    }

    /**
     * 添加商品
     */
    public function createGoods(){
        $args = Input::all();
        $args['type'] = Goods::SELLER_GOODS;
        $result_cate = $this->requestApi('goods.cate.lists',['sellerId'=>$args['sellerId'],'type'=>Goods::SELLER_GOODS]);
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
        $tagList3 = $this->requestApi('systemTagList.secondLevel', ['pid'=>$tagList2['data']['systemTagListPid']]);
        $tagList3 = $tagList3['data'];
        array_unshift($tagList3,$tagList2);
        View::share('systemTagListId', $tagList3);
        View::share('args', $args);

        //获取商家是否是全国店
        $seller = $this->requestApi('seller.get', ['id'=>$args['sellerId']]);
        View::share('storeType', $seller['data']['storeType']);

        //【分销平台】全国店
        if(FANWEFX_SYSTEM && $seller['data']['storeType'] == 1)
        {
            //获取分销模式，分销通道
            $passageId = \YiZan\Http\Controllers\Admin\FxController::get_enabled_passages();
            //获取分销方案
            $schemeId = \YiZan\Http\Controllers\Admin\FxController::query_commission_schemes();

            View::share('passageId', $passageId);
            View::share('schemeId', $schemeId);
            View::share('fx', true);
        }

        $stock = $this->requestApi('stock.getLists',['status' => 1]);
        View::share('stock', $stock['data']['list']);

        return $this->display('goodsEdit');
    }

    /*验证码*/
    public function userverify() {
        $args = Input::all();
        $result = $this->requestApi('user.verify',$args);
        return Response::json($result);
    }


    /**
     * 导出到excel
     */
    public function export() {
        require_once base_path().'/vendor/phpexcel/PHPExcel.php';
        $execl = new \PHPExcel();

        $execl->setActiveSheetIndex(0);
        $sheet = $execl->getActiveSheet();
        $sheet->setTitle('商家信息列表');

        $sheet->setCellValue('A1', "商家名称");
        $sheet->setCellValue('B1', "商家类型");
        $sheet->setCellValue('C1', "商家电话");
        $sheet->setCellValue('D1', "商家地址");
        $sheet->setCellValue('E1', "商家所在省");
        $sheet->setCellValue('F1', "商家所在市");
        $sheet->setCellValue('G1', "商家简介");
        $sheet->setCellValue('H1', "审核状态");
        $sheet->setCellValue('I1', "认证状态");
        $sheet->setCellValue('J1', "起送费(元)");
        $sheet->setCellValue('K1', "配送费(元)");
        $sheet->setCellValue('L1', "提成值(%)");
        $sheet->setCellValue('M1', "启用状态");

        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(35);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(35);
        $sheet->getColumnDimension('H')->setWidth(10);
        $sheet->getColumnDimension('I')->setWidth(10);
        $sheet->getColumnDimension('J')->setWidth(10);
        $sheet->getColumnDimension('K')->setWidth(10);
        $sheet->getColumnDimension('L')->setWidth(10);
        $sheet->getColumnDimension('M')->setWidth(10);

        $sheet->getStyle('B')->getAlignment()->setWrapText(true);

        $args = Input::all();
        $args['page'] = 0;
        $args['pageSize'] = 50;
        $i = 2;
        do {
            $args['page']++;
            $result = $this->requestApi('seller.lists', $args);

            $typeStatus = [1 => '个人加盟', 2 => '商家加盟'];
            $checkStatus = [0 => '待审核', 1 => '通过', -1 => '拒绝'];
            $authStatus = [0 => '未认证', 1 => '已认证'];
            $Status = [1 => '正常', 0 => '停用'];
            foreach ($result['data']['list'] as $key => $value) {
                $sheet->setCellValue('A'.$i, $value['name']);
                $sheet->setCellValue('B'.$i, $typeStatus[$value['type']]);
                $sheet->setCellValue('C'.$i, $value['mobile']);
                $sheet->setCellValue('D'.$i, $value['address']);
                $sheet->setCellValue('E'.$i, $value['province']['name']);
                $sheet->setCellValue('F'.$i, $value['city']['name']);
                $sheet->setCellValue('G'.$i, $value['brief']);
                $sheet->setCellValue('H'.$i, $checkStatus[$value['isCheck']]);
                $sheet->setCellValue('I'.$i, $authStatus[$value['isAuthenticate']]);
                $sheet->setCellValue('J'.$i, $value['serviceFee']);
                $sheet->setCellValue('K'.$i, $value['deliveryFee']);
                $sheet->setCellValue('L'.$i, $value['deduct']);
                $sheet->setCellValue('M'.$i, $Status[$value['status']]);
                $i++;
            }
        }while(count($result['data']['list']) >= $args['pageSize']);


        $name = iconv("utf-8", "gb2312", "商家信息");
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        header("Expires: 0");
        $execl = \PHPExcel_IOFactory::createWriter($execl, 'Excel2007');
        $execl->save('php://output');
    }


    /**
     * 修改余额
     */
    public function updatebalance(){
        $args = Input::all();
        $result = $this->requestApi('seller.updatebalance', $args);
        return Response::json($result);
    }
    /**
     *  通用服务
     * */
    public function systemgoods() {
        $args = Input::all();
        $result = $this->requestApi('system.goods.getlists', $args);
        if ($result['code'] == 0){
            View::share('list', $result['data']['list']);
            $result_cate = $this->requestApi('goods.cate.lists',['sellerId'=>$args['sellerId'],'type'=>Goods::SELLER_GOODS]);
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
    public function systemgoodsedit()
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

        View::share('args', $args);

        $stock = $this->requestApi('stock.getLists',['status' => 1]);
        View::share('stock', $stock['data']['list']);

        $stockItem = $this->requestApi('stock.getStock',['goodsId' => $result['data']['id'],'stockId' => $result['data']['stockTypeId'],'isSystem' => 1]);
        View::share('stockItem', $stockItem);
        return $this->display('goodsedit');
    }

    /*
     * 保存通用
     */
    public function systemgoodssave()
    {
        $args = Input::all();
        $args['type'] = Goods::SELLER_GOODS;
        $args['isSystem'] = 1;
        $args['norms']['stock'] = $args['stock_id'];
        $args['norms']['skuItem'] = $args['sku_item'];
        $args['norms']['skuPrice'] = $args['sku_price'];
        $args['norms']['skuStock'] = $args['sku_stock'];
        unset($args['sku_stock'],$args['sku_price'],$args['sku_item'],$args['stock_id'],$args['addmoney'],$args['addstock']);
        if(count($args['norms']['skuPrice']) != count($args['norms']['skuStock'])){
            return $this->error("有为空的选项,请检查");
        }

        $result = $this->requestApi('goods.systemAdd', $args);
        if ($result['code'] > 0) {
            return $this->error($result['msg']);
        }
        return $this->success(
            Lang::get('admin.code.98008'),
            u('Service/goodslists',['sellerId' => $args['sellerId']])
        );
    }
    /**
     * 一键导入商品库 执行
     */
    public function oneChannelCk(){
        $args = Input::all();
        $result = $this->requestApi('goods.oneChannelCk', $args);
        return Response::json($result);
    }

    /**
     * 一键导入商品库 执行
     */
    public function oneChannel(){
        $args = Input::all();
        $result = $this->requestApi('goods.oneChannel', $args);
        return Response::json($result);
    }
    /**
     * 商家管理-删除商品
     */
    public function goodsDestroy() {
        $args = Input::all();
        if(!is_array($args['id']))
        {
            $args['id'] = explode(',', $args['id']);
        }
        $args['id'] = array_filter($args['id']);

        if ( !empty( $args['id'] ) )
            $result = $this->requestApi('goods.deleteservice',['id' => $args['id']]);

        if( $result['code'] > 0 )
            return $this->error($result['msg']);

        if($args['type'] == 1)
        {
            return $this->success(Lang::get('admin.code.98005'), u('Service/goodslists',['sellerId' => $args['sellerId']]));
        }
        elseif($args['type'] == 2)
        {
            return $this->success(Lang::get('admin.code.98005'), u('Service/servicelists',['sellerId' => $args['sellerId']]));
        }

    }

    /**
     * 添加商家分类
     */
    public function creategoodscate(){
        $args = Input::all();

        $seller_cate_result = $this->requestApi('seller.cate.catelists',$args);
        // print_r($seller_cate_result);
        foreach ($seller_cate_result['data'] as $value) {
            $cate[] = $value['cates'];
        }
        //print_r($cate);
        View::share('cate', $cate);
        View::share('args', $args);
        return $this->display('editgoodscate');
    }

    /**
     * 添加商家分类
     */
    public function catesave(){
        $args = Input::get();
        $url = u('Service/catelists',['sellerId'=>$args['sellerId'],'type'=>$args['type']]);

        if ((int)$args['id'] > 0) {
            $result = $this->requestApi('goods.cate.update', $args);
        } else {
            $result = $this->requestApi('goods.cate.create', $args);
        }

        if($result['code'] == 0){
            return $this->success( Lang::get('admin.code.98008'), $url);
        }else{
            return $this->error($result['msg'] ? $result['msg'] : Lang::get('admin.code.98009'));
        }
    }

    /**
     * 批量添加分销
     */
    public function morefx() {
        $args = Input::all();
        $result = $this->requestApi('seller.morefx', $args);
        return Response::json($result);
    }


}
