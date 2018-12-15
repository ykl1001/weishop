<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Models\Order;
use YiZan\Http\Requests\OrderCreatePostRequest;
use View, Input, Lang, Route, Page, Form, Format, Response,Time,Redirect;
/**
 * 订单管理
 */
class OrderController extends AuthController {
	public function index() {
		$post = Input::all();
		!empty($post['page']) ? $args['page'] = intval($post['page']) : $args['page'] = 1;
		!empty($post['mobile']) ? $args['mobile'] = strval($post['mobile']) : null;
		!empty($post['beginTime']) ? $args['beginTime'] = Time::toTime(strval($post['beginTime'])) : 0;
		!empty($post['endTime']) ? $args['endTime'] = (Time::toTime(strval($post['endTime'])) + 24 * 60 * 60 - 1 ) : 0;
		$args['status'] = isset($post['status']) ? intval($post['status']) : 0 ;
        $args['sn'] = trim($post['sn']);
        $args['name'] = trim($post['name']);
        $args['staffName'] = trim($post['staffName']);
		$args['orderType'] = 1;
		$args['payTypeStatus'] = $post['payTypeStatus'];

		$result = $this->requestApi('order.lists',$args);
		if($result['code']==0)
		{
            View::share('list',$result['data']['list']);
		}
        View::share('orderStatus',$result['data']['orderStatus']);
		View::share('args',$args);
		View::share('excel',http_build_query($args));
        View::share('searchUrl', u('Order/index',['status'=>$post['status'], 'nav'=>$post['nav']]));

		return $this->display();
	}

	/**
	 * 订单详细
	 */
	public function detail() {
		$args['orderId'] = Input::get('orderId');
		if($args['orderId'] > 0) {
			$result = $this->requestApi('order.detail',$args);
		      if($result['code'] == 0){			  
			    View::share('data', $result['data']);
                View::share('staff', $result['data']['staffList']);
			}
		}
//        print_r($result['data']);exit;
        //获取系统名 获取配送费
        $config = $this->getConfig();
        $system_send_staff_fee = $config['system_send_staff_fee'];
        $site_name = $config['site_name'];
        View::share('system_send_staff_fee', $system_send_staff_fee);
        View::share('site_name',$site_name);

        if(STORE_TYPE == 1){
            //from地址
            $addressFrom = [
                'provinceId' => $this->seller['province']['id'],
                'cityId' => $this->seller['city']['id'],
                'areaId' => $this->seller['area']['id']
            ];
            $from = $this->requestApi('order.addressStr', $addressFrom);
            //to地址
            $addressTo = [
                'provinceId' => $result['data']['provinceId'],
                'cityId' => $result['data']['cityId'],
                'areaId' => $result['data']['areaId']
            ];
            $to = $this->requestApi('order.addressStr', $addressTo);
            View::share('from', $from['data']);
            View::share('to', $to['data']);
            $express = Lang::get('couriercompany');
            View::share("couriercompany", $express['courier_company']);
            View::share("refund",$result['data']['refund']);

            if( $result['data']['refund']['status'] == 5){
                $result = $this->requestApi('order.refundDetail',['userId' => $result['data']['userId'],'orderId' => $args['orderId']]);
                View::share('userRefund', $result['data']);
            }
            return $this->display('alldetail');
        }
        return $this->display();
	}
    /*物流发货*/
    public function postlogistics() {
        $args = Input::all();
        $result = $this->requestApi('order.postlogistics',$args);
        return Response::json($result);
    }
	/**
	 * 订单状态更新
	 */
	public function status() {
		$args = Input::all();
		$result = $this->requestApi('order.updatestatus',$args);
		return Response::json($result);
	}
	
	/**
	 * 订单修改
	 */
	public function refundRemark() {
		$post = Input::all();
		if( !empty($post['id']) ) {
			$args['orderId'] = intval($post['id']);
		}else{
			return $this->error(Lang::get('admin.code.23000'));
		}

		if( !empty($post['status']) ) {
			$args['status'] = $post['status'];
		}else{
			return $this->error(Lang::get('admin.code.23004'));
		}

		//退款需要添加备注
		if( $post['status'] == ORDER_STATUS_REFUND_AUDITING ){
			if( !empty($post['remark']) ) {
				$args['content'] = strval(trim($post['remark']));
			}else{
				return $this->error(Lang::get('admin.code.23001'));
			}
		}
	    //拒绝需要添加备注
		if( $post['status'] == ORDER_STATUS_CANCEL_SELLER ){
		    if( !empty($post['refuseContent']) ) {
		        $args['content'] = strval(trim($post['refuseContent']));
		    }else{
		        return $this->error(Lang::get('admin.code.50201'));
		    }
		}

		$result = $this->requestApi('order.updatestatus',$args);

		if($result['code']>0) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('Order/detail',['id'=>$args['orderId']]));
	}

	/**
	 * 删除订单
	 */
	public function destroy() {
		$args = Input::all();
		if( !empty( $args['id'] ) ) {
			$result = $this->requestApi('order.delete',$args); 
		}
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98005'), u('Order/index'), $result['data']);
	}
	//指派人员
	public function designate() {
	    $args = Input::all();
	    $result = $this->requestApi('order.designate', $args);
	    return Response::json($result);
	}
	/*
	 * 随机指派
	 */
	public function reassign() {
	    $args = Input::all();
	    $result = $this->requestApi('order.ranupdate', $args);
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
		$sheet->setTitle('订单列表');


		$sheet->setCellValue('A1', "订单号");
		$sheet->setCellValue('B1', "客户姓名");
		$sheet->setCellValue('C1', "客户电话");
        if(STORE_TYPE == 1){
            $sheet->setCellValue('D1', "收货地址");
        }else{
            $sheet->setCellValue('D1', "客户地址");
        }
		$sheet->setCellValue('E1', "订单金额");
		$sheet->setCellValue('F1', "订单状态");
		$sheet->setCellValue('G1', "支付方式");
		$sheet->setCellValue('H1', "商品信息");

		$sheet->getColumnDimension('A')->setWidth(26);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getColumnDimension('C')->setWidth(15);
		$sheet->getColumnDimension('D')->setWidth(25);
		$sheet->getColumnDimension('E')->setWidth(13);
		$sheet->getColumnDimension('F')->setWidth(13);
		$sheet->getColumnDimension('G')->setWidth(13);
		$sheet->getColumnDimension('H')->setWidth(26);
		$args = Input::all();
		// $args = [];
		$result = $this->requestApi('order.lists', $args);
		$i = 2;
		foreach ($result['data']['list'] as $key => $value) {
			$orderGoodsInfo = [];
			foreach ($value['orderGoods'] as $k => $v) {
				if(!empty($v['goodsNorms']))
				{
					$orderGoodsInfo[$k] = $v['goodsName'] .'【'. $v['goodsNorms'] . '】';
				}
				else
				{
					$orderGoodsInfo[$k] = $v['goodsName'];
				}
			}
			$orderGoodsInfo = implode(",", $orderGoodsInfo);

			       
            if($value['isCashOnDelivery']){
                $payStatusType = '货到付款';
            }
            else{
            	if($value['payStatus'] == 1){
					$payStatusType = '在线支付';
				}  
	            else{
	                $payStatusType = '未支付';
	            }
            }
            

			$sheet->setCellValue('A'.$i, "SN:".$value['sn']);
			$sheet->setCellValue('B'.$i, $value['name']);
			$sheet->setCellValue('C'.$i, $value['mobile'].' ');
			$sheet->setCellValue('D'.$i, $value['address']);
			$sheet->setCellValue('E'.$i, $value['totalFee']);
			$sheet->setCellValue('F'.$i, $value['orderStatusStr']);
			$sheet->setCellValue('G'.$i, $payStatusType);
			$sheet->setCellValue('H'.$i, $orderGoodsInfo);
			$i++;
		}		
		
		$name = iconv("utf-8", "gb2312", "订单列表详细");
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
     * 订单详细
     */
    public function printer() {
        $args['orderId'] = Input::get('orderId');
        if($args['orderId'] > 0) {
            $result = $this->requestApi('order.printer',$args);
            if($result['code'] == 0){
                View::share('data', $result['data']);
                View::share('staff', $result['data']['staffList']);
            }
        }
        return $this->display();
    }
    /**
     * 查看物流
     */
    public function checkLogistics() {
        $args = Input::all();
        $result = $this->requestApi('order.get', $args);
        return Response::json($result['data']);
    }
    /**
     * 订阅物流
     */
    public function refund(){
        $result = $this->requestApi('logistics.refundsave',Input::all());
        return Response::json($result);

    }
    public function refunddispose(){
        $result = $this->requestApi('logistics.refundById',['id'=>Input::get('id')]);
        if( $result['data']['status'] == 2 ){
            return Redirect::to(u('Order/detail', ['orderId' => Input::get('id')]));
        }
        View::share('data', $result['data']);
        return $this->display();
    }
    /**
     * 拒绝退款
     */
        public function refundSave() {
            $args = Input::all();
            $cause = [
                '1' => '商品已影响二次销售',
                '2' =>"商品已发货",
                '3' =>"买家不想退款了",
                '4' => $args['cause']
            ];
            $data['content'] = $cause[$args['causeId']];
            $data['refundExplain'] = $args['brief'];
            $data['id'] = $args['id'];
            $data['images'] = $args['images'];
            $data['orderId'] = $args['orderId'];
            $data['status'] =2;
            $result = $this->requestApi('logistics.refunddispose',$data);
            if( $result['code'] != 81006 ) {
                return $this->error($result['msg']);
            }
            return $this->success($result['msg'], u('Order/detail',['orderId'=>$args['orderId']]), $result['data']);
       // return Response::json($result);
    }

}
