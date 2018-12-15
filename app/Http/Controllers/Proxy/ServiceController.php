<?php 
namespace YiZan\Http\Controllers\Proxy;

use YiZan\Models\Goods; 
use View, Input, Lang, Route, Page, Validator, Session, Response, Cache, Redirect;
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
		return $this->display();
	}


    /**
     * 添加商家
     */
    public function create() {
        $cateIds = $this->requestApi('seller.cate.catesall');
        if($cateIds['code'] == 0)
            View::share('cateIds',$cateIds['data']);
       // print_r($cateIds);
        View::share('type', $type);
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

        if($data['data']['isCheck'] == 0){
            return $this->display();
        } else {
            return $this->display('noedit');
        }

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

        // 全国店清空服务范围
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
            // $args['sendWay'] = [0=>''];
            // $args['serviceWay'] = [0=>''];
            // $args['reserveDays'] = null;
            // $args['sendLoop'] = null;
            $args['refundAddress'] = trim($args['refundAddress']);
        }
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
     * [catelists 分类列表]
     */
    public function cateLists(){
        $args = Input::all();  
        $result_cate = $this->requestApi('goods.cate.lists', $args);    
        View::share('list', $result_cate['data']); 
        return $this->display(); 
    }

    public function cateedit(){
        $args = Input::all();  
        $result_cate = $this->requestApi('goods.cate.get', $args);    
        View::share('data', $result_cate['data']); 
        return $this->display(); 
    }

    public function catesave(){
        $args = Input::all();   
        $result = $this->requestApi('goods.cate.update', $args);    
        if ($result['code'] > 0) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98008'), $url, $result['data']);
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
        // var_dump($result_cate);
        View::share('cate', $result_cate['data']); 
        View::share('args', $args);
        View::share('excel', http_build_query($args));
        return $this->display();
    }

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

        //$servicestime = $this->requestApi('servicestime.lists',$args); 
        // var_dump($servicestime['data']);
        //View::share('stime', $servicestime['data']);
        return $this->display();
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

        $stock = $this->requestApi('stock.getLists',['status' => 1]);
        View::share('stock', $stock['data']['list']);

        $stockItem = $this->requestApi('stock.getStock',['goodsId' => $result['data']['id'],'stockId' => $result['data']['stockTypeId'],'isSystem'=>0]);
        View::share('stockItem', $stockItem);
        View::share('locking', true);
        return $this->display();
    }

    /**
     * 员工管理-员工列表
     */
    public function staffLists() {
        $args = Input::all();
        //获取服务站信息
        $seller = $this->requestApi('seller.get', ['id'=>$args['sellerId']]);
        View::share('seller', $seller['data']);
        View::share('sellerId', $args['sellerId']);
        //获取员工列表
        $result = $this->requestApi('sellerstaff.lists', $args); 
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
        return $this->display();
    }
    
    /**
     * 员工详情
     */
    public function staffEdit(){
        $args = Input::all();

        if ($args['id'] < 1) 
            Redirect::to(u('Staff/index'))->send();
        $seller = $this->requestApi('seller.get', ['id'=>$args['sellerId']]);
       // var_dump($seller);
        if ($seller['code'] == 0) {
            View::share('seller', $seller['data']);
        }
        $result = $this->requestApi('sellerstaff.get',$args);
        if ($result['code'] == 0) {
            View::share('data', $result['data']);
        }
        return $this->display();
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
        
        $args = [];
        $result = $this->requestApi('seller.lists', $args);

        $i = 2;
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
}
