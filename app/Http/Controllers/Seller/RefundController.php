<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Form, Format, Response;
/**
 * 订单管理
 */
class RefundController extends AuthController {
	/**
	 * 订单列表
	 */
	public function index() {
		$post = Input::all();
		!empty($post['mobile']) ? $args['mobile'] = strval($post['mobile']) : null;
		!empty($post['beginTime']) ? $args['beginTime'] = Time::toTime(strval($post['beginTime'])) : 0;
		!empty($post['endTime']) ? $args['endTime'] = (Time::toTime(strval($post['endTime'])) + 24 * 60 * 60 - 1 ) : 0;
		!empty($post['page']) ? $args['page'] = intval($post['page']) : $args['page'] = 1;
		$args['status'] = 5;
		$result = $this->requestApi('order.lists', $args);
		if( $result['code'] == 0 ) {
			View::share('list', $result['data']['list']);
		}
		View::share('nav',$post['nav']);
		View::share('excel',http_build_query($args));
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
			}
		}
		//获取员工列表
		$result = $this->requestApi('sellerstaff.lists', ['pageSize' => 1000]);
		if( $result['code'] == 0 ){
		    View::share('staff', $result['data']['list']);
		}
		return $this->display();
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
		$sheet->setCellValue('B1', "服务名称");
		$sheet->setCellValue('C1', "服务人员");
		$sheet->setCellValue('D1', "服务人员电话");
		$sheet->setCellValue('E1', "会员名称");
		$sheet->setCellValue('F1', "会员联系电话");
		$sheet->setCellValue('G1', "订单金额");
		$sheet->setCellValue('H1', "优惠金额");
		$sheet->setCellValue('I1', "支付金额");
		$sheet->setCellValue('J1', "支付状态");
		$sheet->setCellValue('K1', "订单状态");
		$sheet->setCellValue('L1', "下单时间");

		$sheet->getColumnDimension('A')->setWidth(25);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getColumnDimension('C')->setWidth(15);
		$sheet->getColumnDimension('D')->setWidth(13);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(13);
		$sheet->getColumnDimension('G')->setWidth(10);
		$sheet->getColumnDimension('H')->setWidth(10);
		$sheet->getColumnDimension('I')->setWidth(10);
		$sheet->getColumnDimension('J')->setWidth(10);
		$sheet->getColumnDimension('K')->setWidth(10);
		$sheet->getColumnDimension('L')->setWidth(18);

		$sheet->getStyle('B')->getAlignment()->setWrapText(true);
		
		$args = [];
		$result = $this->requestApi('order.lists', $args);

		$i = 2;
		$payStatus = [0 => '等待支付', 1 => '已支付'];
		foreach ($result['data']['list'] as $key => $value) {
			$sheet->setCellValue('A'.$i, "SN:".$value['sn']);
			$sheet->setCellValue('B'.$i, $value['goods']['name']);
			$sheet->setCellValue('C'.$i, $value['seller']['name']);
			$sheet->setCellValue('D'.$i, $value['seller']['mobile']);
			$sheet->setCellValue('E'.$i, $value['user']['name']);
			$sheet->setCellValue('F'.$i, $value['user']['mobile']);
			$sheet->setCellValue('G'.$i, $value['totalFee']);
			$sheet->setCellValue('H'.$i, $value['discountFee']);
			$sheet->setCellValue('I'.$i, $value['payFee']);
			$sheet->setCellValue('J'.$i, $payStatus[$value['payStatus']]);
			$sheet->setCellValue('K'.$i, $value['orderStatusStr']);
			$sheet->setCellValue('L'.$i, yztime( $value['createTime'] ));
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



}
