<?php 
namespace YiZan\Http\Controllers\Admin; 
 
use Input, View,Time;

/**
 * 商家管理
 */
class BusinessStatisticsController extends AuthController {
	
	/**
	 * 商家营业统计
	 */
	public function index() {
        $args = Input::all();
        $args['year'] = ($args['year'] > -99) ? $args['year'] : Time::toDate(UTC_TIME, 'Y');
        $args['month'] = ($args['month'] > -99) ? $args['month'] : Time::toDate(UTC_TIME, 'm');
        //获取订单列表中的年份
        $orderyear = $this->requestApi('seller.statistics.year');
        View::share('orderyear',$orderyear['data']);
		$list = $this->requestApi('seller.statistics.lists', $args); 

		// print_r($list);exit;
		View::share('lists', $list['data']['list']);
		View::share('sum', $list['data']['sum']);
		View::share('args', $args);
		return $this->display();
	}

	/**
	 * 月对账单
	 */
	public function monthAccount(){
        $args = Input::all();
        $args['year'] = ($args['year'] > -99) ? $args['year'] : Time::toDate(UTC_TIME, 'Y');
        $args['month'] = ($args['month'] > -99) ? $args['month'] : Time::toDate(UTC_TIME, 'm');
        //获取订单列表中的年份
        $orderyear = $this->requestApi('seller.statistics.year'); 
        View::share('orderyear',$orderyear['data']);
		$list = $this->requestApi('seller.statistics.monthlists', $args);
		View::share('lists', $list['data']['list']);
		View::share('sum', $list['data']['sum']);
		View::share('seller', $list['data']['seller']);
		View::share('args', $args);
		return $this->display();
	}

	/**
	 * 天对账单
	 */
	public function dayAccount(){
        $args = Input::all();  
		$list = $this->requestApi('seller.statistics.daylists', $args); 
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
		$args['year'] = ($args['year'] > -99) ? $args['year'] : Time::toDate(UTC_TIME, 'Y');
		$args['month'] = ($args['month'] > -99) ? $args['month'] : Time::toDate(UTC_TIME, 'm');
		require_once base_path().'/vendor/phpexcel/PHPExcel.php';
		$execl = new \PHPExcel();

		$execl->setActiveSheetIndex(0);
		$sheet = $execl->getActiveSheet();
		$sheet->setTitle($args['year'].'-'.$args['month'].'-'.'商家营业统计');

		$sheet->setCellValue('A1', "商家名称");
		$sheet->setCellValue('B1', "本月营业额");
		$sheet->setCellValue('C1', "有效订单数");
		$sheet->setCellValue('D1', "在线支付");
		$sheet->setCellValue('E1', "现金支付");
		$sheet->setCellValue('F1', "积分奖金");
		$sheet->setCellValue('G1', "优惠券");
		$sheet->setCellValue('H1', "佣金");
		$sheet->setCellValue('I1', "客单价");

		$sheet->getColumnDimension('A')->setWidth(50);
		$sheet->getColumnDimension('B')->setWidth(15);
		$sheet->getColumnDimension('C')->setWidth(15);
		$sheet->getColumnDimension('D')->setWidth(15);
		$sheet->getColumnDimension('E')->setWidth(15);
		$sheet->getColumnDimension('F')->setWidth(15);
		$sheet->getColumnDimension('G')->setWidth(15);
		$sheet->getColumnDimension('H')->setWidth(15);
		$sheet->getColumnDimension('I')->setWidth(15);

		$execl->getActiveSheet()->getStyle( 'A2:I2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
		$execl->getActiveSheet()->getStyle( 'A2:I2')->getFill()->getStartColor()->setARGB('FF00CC33');

		$args['page'] = 0;
		$args['pageSize'] = 50;
		$i = 2;
		do {
			$args['page']++;
			$result = $this->requestApi('seller.statistics.lists', $args);
			if($i == 2) {
				$sum = $result['data']['sum'];
				$sheet->setCellValue('A' . $i, '合计');
				$sheet->setCellValue('B' . $i, number_format($sum['totalPayfee'], 2));
				$sheet->setCellValue('C' . $i, $sum['totalNum']);
				$sheet->setCellValue('D' . $i, number_format($sum['totalOnline'], 2));
				$sheet->setCellValue('E' . $i, number_format($sum['totalCash'], 2));
				$sheet->setCellValue('F' . $i, number_format($sum['totalIntegralFee'], 2));
				$sheet->setCellValue('G' . $i, number_format($sum['totalDiscountFee'], 2));
				$sheet->setCellValue('H' . $i, number_format($sum['totalDrawnfee'], 2));
				$sheet->setCellValue('I' . $i, number_format($sum['totalPayfee']/$sum['totalNum'], 2));
				$i++;
			}

			foreach ($result['data']['list'] as $key => $value) {
				$sheet->setCellValue('A' . $i, $value['name']);
				$sheet->setCellValue('B' . $i, number_format($value['totalPayfee'], 2));
				$sheet->setCellValue('C' . $i, $value['totalNum']);
				$sheet->setCellValue('D' . $i, number_format($value['totalOnline'], 2));
				$sheet->setCellValue('E' . $i, number_format($value['totalCash'], 2));
				$sheet->setCellValue('F' . $i, number_format($value['totalIntegralFee'], 2));
				$sheet->setCellValue('G' . $i, number_format($value['totalDiscountFee'], 2));
				$sheet->setCellValue('H' . $i, number_format($value['totalDrawnfee'], 2));
				$sheet->setCellValue('I' . $i, number_format($value['totalPayfee']/$value['totalNum'], 2));
				$i++;
			}
		}while(count($result['data']['list']) >= $args['pageSize']);

		$name = iconv("utf-8", "gb2312", $args['year'].'-'.$args['month'].'-'.'商家营业统计');
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
	 * 导出数据
	 * @throws \PHPExcel_Exception
	 * @throws \PHPExcel_Reader_Exception
	 */
	public function monthaccountexport(){
		$args = Input::all();
		$args['year'] = ($args['year'] > -99) ? $args['year'] : Time::toDate(UTC_TIME, 'Y');
		$args['month'] = ($args['month'] > -99) ? $args['month'] : Time::toDate(UTC_TIME, 'm');
		require_once base_path().'/vendor/phpexcel/PHPExcel.php';
		$execl = new \PHPExcel();

		$execl->setActiveSheetIndex(0);
		$sheet = $execl->getActiveSheet();
		$sheet->setTitle($args['year'].'-'.$args['month'].'-'.'商家对账单');

		$sheet->setCellValue('A1', "日期");
		$sheet->setCellValue('B1', "营业额");
		$sheet->setCellValue('C1', "有效订单数");
		$sheet->setCellValue('D1', "在线支付");
		$sheet->setCellValue('E1', "现金支付");
		$sheet->setCellValue('F1', "积分奖金");
		$sheet->setCellValue('G1', "优惠券");
		$sheet->setCellValue('H1', "佣金(支出)");
		$sheet->setCellValue('I1', "入账金额");
		$sheet->setCellValue('J1', "客单价");
		$sheet->setCellValue('K1', "平台补贴");
		$sheet->setCellValue('L1', "商家补贴(支出)");

		$sheet->getColumnDimension('A')->setWidth(20);
		$sheet->getColumnDimension('B')->setWidth(15);
		$sheet->getColumnDimension('C')->setWidth(15);
		$sheet->getColumnDimension('D')->setWidth(15);
		$sheet->getColumnDimension('E')->setWidth(15);
		$sheet->getColumnDimension('F')->setWidth(15);
		$sheet->getColumnDimension('G')->setWidth(15);
		$sheet->getColumnDimension('H')->setWidth(15);
		$sheet->getColumnDimension('I')->setWidth(15);
		$sheet->getColumnDimension('K')->setWidth(15);
		$sheet->getColumnDimension('L')->setWidth(15);

		$execl->getActiveSheet()->getStyle( 'A2:J2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
		$execl->getActiveSheet()->getStyle( 'A2:J2')->getFill()->getStartColor()->setARGB('FF00CC33');

		$i = 2;

		$result = $this->requestApi('seller.statistics.monthlists', $args);
		$seller = $result['data']['seller'];

		$sum = $result['data']['sum'];
		$sheet->setCellValue('A' . $i, '合计');
		$sheet->setCellValue('B' . $i, number_format($sum['totalPayfee'], 2));
		$sheet->setCellValue('C' . $i, $sum['totalNum']);
		$sheet->setCellValue('D' . $i, number_format($sum['totalOnline'], 2));
		$sheet->setCellValue('E' . $i, number_format($sum['totalCash'], 2));
		$sheet->setCellValue('F' . $i, number_format($sum['totalIntegralFee'], 2));
		$sheet->setCellValue('G' . $i, number_format($sum['totalDiscountFee'], 2));
		$sheet->setCellValue('H' . $i, number_format($sum['totalDrawnfee'], 2));
		$sheet->setCellValue('I' . $i, number_format($sum['totalSellerFee'], 2));
		$sheet->setCellValue('J' . $i, number_format($sum['totalPayfee']/$sum['totalNum'], 2));
		$sheet->setCellValue('K' . $i, number_format($sum['totalCash'], 2));
		$sheet->setCellValue('L' . $i, number_format($sum['sellerFullSubsidy']+$sum['activityGoodsMoney'], 2));
		$i++;

		foreach ($result['data']['list'] as $key => $value) {
			$sheet->setCellValue('A' . $i, ' '.$value['daytime'].' ');
			$sheet->setCellValue('B' . $i, $value['totalPayfee']);
			$sheet->setCellValue('C' . $i, $value['totalNum']);
			$sheet->setCellValue('D' . $i, $value['totalOnline']);
			$sheet->setCellValue('E' . $i, $value['totalCash']);
			$sheet->setCellValue('F' . $i, $value['totalIntegralFee']);
			$sheet->setCellValue('G' . $i, $value['totalDiscountFee']);
			$sheet->setCellValue('H' . $i, $value['totalDrawnfee']);
			$sheet->setCellValue('I' . $i, number_format($value['totalSellerFee'], 2));
			$sheet->setCellValue('J' . $i, number_format($value['totalPayfee']/$value['totalNum'], 2));
			$sheet->setCellValue('K' . $i, number_format($value['totalCash'], 2));
			$sheet->setCellValue('L' . $i, number_format($value['sellerFullSubsidy']+$value['activityGoodsMoney'], 2));
			$i++; 
		}

		$name = iconv("utf-8", "gb2312", $args['year'].'-'.$args['month'].'-'.'商家('.$seller['name'].')对账单');
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
	 * 导出数据
	 * @throws \PHPExcel_Exception
	 * @throws \PHPExcel_Reader_Exception
	 */
	public function dayaccountexport(){
		$args = Input::all();
		$args['year'] = ($args['year'] > 0) ? $args['year'] : Time::toDate(UTC_TIME, 'Y');
		$args['month'] = ($args['month'] > 0) ? $args['month'] : Time::toDate(UTC_TIME, 'm');

		$result = $this->requestApi('seller.statistics.daylists', $args);
		$seller = $result['data']['sum']['seller'];
		$sum = $result['data']['sum'];

		require_once base_path().'/vendor/phpexcel/PHPExcel.php';
		$execl = new \PHPExcel();

		$execl->setActiveSheetIndex(0);
		$sheet = $execl->getActiveSheet();
		$execl->getActiveSheet()->mergeCells('A1:K1');      //合并

		$sheet->setCellValue('A1', "商家名：{$sum['seller']['name']}  账单日期：{$args['day']}  有效订单数：{$sum['totalNum']}  已入账总额：{$sum['totalSellerFee']}  ");
		$execl->getActiveSheet()->getStyle( 'A1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
		$execl->getActiveSheet()->getStyle( 'A1')->getFill()->getStartColor()->setARGB('FF00CC33');

		$sheet->setCellValue('A2', "订单号");
		$sheet->setCellValue('B2', "在线支付");
		$sheet->setCellValue('C2', "现金支付");
		$sheet->setCellValue('D2', "积分奖金");
		$sheet->setCellValue('E2', "优惠券");
		$sheet->setCellValue('F2', "佣金");
		$sheet->setCellValue('G2', "入账金额");
		$sheet->setCellValue('H2', "状态");
		$sheet->setCellValue('I2', "平台补贴");
		$sheet->setCellValue('J2', "商家补贴(支出)");

		$sheet->getColumnDimension('A')->setWidth(30);
		$sheet->getColumnDimension('B')->setWidth(15);
		$sheet->getColumnDimension('C')->setWidth(15);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(20);
		$sheet->getColumnDimension('H')->setWidth(20);
		$sheet->getColumnDimension('I')->setWidth(20);
		$sheet->getColumnDimension('J')->setWidth(20);

		$i = 3;
		foreach ($result['data']['list'] as $key => $value) {
			$sheet->setCellValue('A' . $i, ' '.$value['sn'].' ');
			$sheet->setCellValue('B' . $i, ($value['isCashOnDelivery'] ? 0 : $value['payFee']));
			$sheet->setCellValue('C' . $i, ($value['isCashOnDelivery'] ? $value['payFee'] : 0));
			$sheet->setCellValue('D' . $i, $value['integralFee']);
			$sheet->setCellValue('E' . $i, ($value['discountFee'] > $value['totalFee']  ? $value['totalFee'] : $value['discountFee']));
			$sheet->setCellValue('F' . $i, $value['drawnFee']);
			$sheet->setCellValue('G' . $i, ($value['isCashOnDelivery']?$value['drawnFee']:$value['sellerFee']));
			$sheet->setCellValue('H' . $i, $value['orderStatus']);
			
			$sheet->setCellValue('I' . $i, number_format($value['activityNewMoney']+$value['systemFullSubsidy']+$value['discountFee']+$value['integralFee'], 2));
			$sheet->setCellValue('J' . $i, $value['sellerFullSubsidy']+$value['activityGoodsMoney']);
			$i++;
		}

		$sheet->setTitle($args['day'].'商家('.$seller['name'].')订单明细');

		$name = iconv("utf-8", "gb2312", $args['day'].'商家('.$seller['name'].')订单明细');
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
