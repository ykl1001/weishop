<?php 
namespace YiZan\Http\Controllers\Admin; 
 
use Input, View,Time;

/**
 * 平台统计
 */
class PlatformStatisticsController extends AuthController {
	
	/**
	 * 平台营业统计
	 */
	public function index() {
        $args = Input::all(); 
        $args['nav'] = intval($args['nav']);
        $args['year'] = ($args['year'] > 0) ? $args['year'] : Time::toDate(UTC_TIME, 'Y');
        $args['month'] = ($args['month'] > 0) ? $args['month'] : Time::toDate(UTC_TIME, 'm');
        //获取订单列表中的年份
        $orderyear = $this->requestApi('seller.statistics.year');  
        View::share('orderyear',$orderyear['data']);
		$list = $this->requestApi('seller.statistics.platform', $args); 

		// print_r($list);exit;
		View::share('lists', $list['data']['list']);
		View::share('sum', $list['data']['sum']);
		View::share('args', $args);
		return $this->display();
	}

	/**
	 * 导出数据
	 * @throws \PHPExcel_Exception
	 * @throws \PHPExcel_Reader_Exception
	 */
	public function export(){
		$args = Input::all();
		$args['nav'] = intval($args['nav']);
		$args['year'] = ($args['year'] > 0) ? $args['year'] : Time::toDate(UTC_TIME, 'Y');
		$args['month'] = ($args['month'] > 0) ? $args['month'] : Time::toDate(UTC_TIME, 'm');

		if($args['nav'] == 0){
			$subTitle = '平台统计';
		}elseif($args['nav'] == 1){
			$subTitle = '销售统计';
		}


		require_once base_path().'/vendor/phpexcel/PHPExcel.php';
		$execl = new \PHPExcel();

		$execl->setActiveSheetIndex(0);
		$sheet = $execl->getActiveSheet();
		$sheet->setTitle($args['year'].'-'.$args['month'].'-'.'平台数据统计信息 - '.$subTitle);
		//平台统计
		if($args['nav'] == 0){
			$sheet->setCellValue('A1', "日期");
			$sheet->setCellValue('B1', "新会员数");
			$sheet->setCellValue('C1', "佣金");
			$sheet->setCellValue('D1', "商家提现");
			$sheet->setCellValue('E1', "会员充值");
			$sheet->setCellValue('F1', "商家充值");

			$sheet->getColumnDimension('A')->setWidth(30);
			$sheet->getColumnDimension('B')->setWidth(30);
			$sheet->getColumnDimension('C')->setWidth(20);
			$sheet->getColumnDimension('D')->setWidth(20);
			$sheet->getColumnDimension('E')->setWidth(20);
			$sheet->getColumnDimension('F')->setWidth(20);
			$execl->getActiveSheet()->getStyle( 'A2:F2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
			$execl->getActiveSheet()->getStyle( 'A2:F2')->getFill()->getStartColor()->setARGB('FF00CC33');
		}elseif($args['nav'] == 1){//销售统计
			$sheet->setCellValue('A1', "日期");
			$sheet->setCellValue('B1', "营业额");
			$sheet->setCellValue('C1', "有效订单数");
			$sheet->setCellValue('D1', "退款/取消订单");
			$sheet->setCellValue('E1', "在线支付");
			$sheet->setCellValue('F1', "现金支付");
			$sheet->setCellValue('G1', "积分奖金");
			$sheet->setCellValue('H1', "优惠金额");
			$sheet->setCellValue('I1', "平台满减");
			$sheet->setCellValue('J1', "首单减");
			$sheet->setCellValue('K1', "商家补贴");

			$sheet->getColumnDimension('A')->setWidth(30);
			$sheet->getColumnDimension('B')->setWidth(20);
			$sheet->getColumnDimension('C')->setWidth(30);
			$sheet->getColumnDimension('D')->setWidth(30);
			$sheet->getColumnDimension('E')->setWidth(20);
			$sheet->getColumnDimension('F')->setWidth(20);
			$sheet->getColumnDimension('G')->setWidth(20);
			$sheet->getColumnDimension('H')->setWidth(20);
			$sheet->getColumnDimension('I')->setWidth(20);
			$sheet->getColumnDimension('J')->setWidth(20);
			$sheet->getColumnDimension('K')->setWidth(20);
			$execl->getActiveSheet()->getStyle( 'A2:K2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
			$execl->getActiveSheet()->getStyle( 'A2:K2')->getFill()->getStartColor()->setARGB('FF00CC33');
		}




		$result = $this->requestApi('seller.statistics.platform', $args);
		$i = 2;
		$sum = $result['data']['sum'];
		if($args['nav'] == 0) {
			$sheet->setCellValue('A' . $i, '汇总');
			$sheet->setCellValue('B' . $i, $sum['totalRegNum']);
			$sheet->setCellValue('C' . $i, number_format($sum['totalDrawnFee'], 2));
			$sheet->setCellValue('D' . $i, number_format($sum['totalSellerFee'], 2));
			$sheet->setCellValue('E' . $i, number_format($sum['totalBuyerCharge'], 2));
			$sheet->setCellValue('F' . $i, number_format($sum['totalSellerCharge'], 2));
		}elseif($args['nav'] == 1){
			$sheet->setCellValue('A' . $i, '汇总');
			$sheet->setCellValue('B' . $i, number_format($sum['totalPayfee'], 2));
			$sheet->setCellValue('C' . $i, $sum['totalNum']);
			$sheet->setCellValue('D' . $i, $sum['totalCancleNum']);
			$sheet->setCellValue('E' . $i, number_format($sum['totalOnline'], 2));
			$sheet->setCellValue('F' . $i, number_format($sum['totalCash'], 2));
			$sheet->setCellValue('G' . $i, number_format($sum['totalIntegralFee'], 2));
			$sheet->setCellValue('H' . $i, number_format($sum['totalDiscountFee'], 2));
			$sheet->setCellValue('I' . $i, number_format($sum['systemFullSubsidy'], 2));
			$sheet->setCellValue('J' . $i, number_format($sum['activityNewMoney'], 2));
			$sheet->setCellValue('K' . $i, number_format($sum['sellerFullSubsidy']+$sum['activityGoodsMoney'], 2));
		}
		$i = 3;
		foreach ($result['data']['list'] as $key => $value) {
			if($args['nav'] == 0) {
				$sheet->setCellValue('A' . $i, $value['daytime']);
				$sheet->setCellValue('B' . $i, $value['totalRegNum']);
				$sheet->setCellValue('C' . $i, $value['totalDrawnFee']);
				$sheet->setCellValue('D' . $i, $value['totalSellerFee']);
				$sheet->setCellValue('E' . $i, $value['totalBuyerCharge']);
				$sheet->setCellValue('F' . $i, $value['totalSellerCharge']);
			}elseif($args['nav'] == 1){
				$sheet->setCellValue('A' . $i, $value['daytime']);
				$sheet->setCellValue('B' . $i, $value['totalPayfee']);
				$sheet->setCellValue('C' . $i, $value['totalNum']);
				$sheet->setCellValue('D' . $i, $value['totalCancleNum']);
				$sheet->setCellValue('E' . $i, $value['totalOnline']);
				$sheet->setCellValue('F' . $i, $value['totalCash']);
				$sheet->setCellValue('G' . $i, $value['totalIntegralFee']);
				$sheet->setCellValue('H' . $i, $value['totalDiscountFee']);
				$sheet->setCellValue('I' . $i, $value['systemFullSubsidy']);
				$sheet->setCellValue('J' . $i, $value['activityNewMoney']);
				$sheet->setCellValue('K' . $i, $value['sellerFullSubsidy']+$value['activityGoodsMoney']);
			}
			$i++;
		}


		$name = iconv("utf-8", "gb2312", $args['year'].'-'.$args['month'].'-'.'平台数据统计信息 - '.$subTitle);
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
