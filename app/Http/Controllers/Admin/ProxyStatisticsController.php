<?php 
namespace YiZan\Http\Controllers\Admin; 
 
use Input, View,Time;

/**
 * 代理统计
 */
class ProxyStatisticsController extends AuthController {
	
	/**
	 * 代理统计
	 */
	public function index() {
        $args = Input::all();  
        $args['year'] = ($args['year'] > 0) ? $args['year'] : Time::toDate(UTC_TIME, 'Y');
        $args['month'] = ($args['month'] > 0) ? $args['month'] : Time::toDate(UTC_TIME, 'm');
        //获取订单列表中的年份
        $orderyear = $this->requestApi('seller.statistics.year');  
        View::share('orderyear',$orderyear['data']);

		$list = $this->requestApi('proxy.statistics.lists', $args);
		View::share('lists', $list['data']['list']);
		View::share('sum', $list['data']['sum']);
		View::share('args', $args);
		return $this->display();
	}  

	/**
	 * 代理商家列表
	 */
	public function sellerLists(){
        $args = Input::all();    
        //获取订单列表中的年份
        $orderyear = $this->requestApi('seller.statistics.year');  
        View::share('orderyear',$orderyear['data']);
		$list = $this->requestApi('proxy.statistics.sellerlists', $args);  

		View::share('lists', $list['data']['list']);
		View::share('sum', $list['data']['sum']);
		View::share('proxy', $list['data']['proxy']);
		View::share('args', $args);
		return $this->display();
	}

	/**
	 * 代理下商家经营列表
	 */
	public function monthAccount(){
        $args = Input::all(); 
        //获取订单列表中的年份
        $orderyear = $this->requestApi('seller.statistics.year');  
        View::share('orderyear',$orderyear['data']);
		$list = $this->requestApi('proxy.statistics.monthlists', $args);  

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
		$list = $this->requestApi('proxy.statistics.daylists', $args); 
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
		$args['year'] = ($args['year'] > 0) ? $args['year'] : Time::toDate(UTC_TIME, 'Y');
		$args['month'] = ($args['month'] > 0) ? $args['month'] : Time::toDate(UTC_TIME, 'm');

		require_once base_path().'/vendor/phpexcel/PHPExcel.php';
		$execl = new \PHPExcel();

		$execl->setActiveSheetIndex(0);
		$sheet = $execl->getActiveSheet();
		$sheet->setTitle($args['year'].'-'.$args['month'].'-'.'代理数据统计信息');

		$sheet->setCellValue('A1', "代理名");
		$sheet->setCellValue('B1', "代理等级");
		$sheet->setCellValue('C1', "本月营业额");
		$sheet->setCellValue('D1', "有效订单数");
		$sheet->setCellValue('E1', "在线支付");
		$sheet->setCellValue('F1', "现金支付");
		$sheet->setCellValue('G1', "积分奖金");
		$sheet->setCellValue('H1', "优惠券");
		$sheet->setCellValue('I1', "佣金");
		$sheet->setCellValue('J1', "客单价");

		$sheet->getColumnDimension('A')->setWidth(30);
		$sheet->getColumnDimension('B')->setWidth(20);
		$sheet->getColumnDimension('C')->setWidth(20);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(20);
		$sheet->getColumnDimension('H')->setWidth(20);
		$sheet->getColumnDimension('I')->setWidth(20);
		$sheet->getColumnDimension('J')->setWidth(20);


		$args['page'] = 0;
		$args['pageSize'] = 100;
		$i = 2;
		$proxyLvl = ['1'=>'一级代理','2'=>'二级代理','3'=>'三级代理'];
		do {
			$args['page']++;
			$result = $this->requestApi('proxy.statistics.lists', $args);

			foreach ($result['data']['list'] as $key => $value) {
				$sheet->setCellValue('A' . $i, ' '.$value['name'].' ');
				$sheet->setCellValue('B' . $i, $proxyLvl[$value['level']]);
				$sheet->setCellValue('C' . $i, $value['totalPayfee']);
				$sheet->setCellValue('D' . $i, $value['totalNum']);
				$sheet->setCellValue('E' . $i, $value['totalOnline']);
				$sheet->setCellValue('F' . $i, $value['totalCash']);
				$sheet->setCellValue('G' . $i, $value['totalIntegralFee']);
				$sheet->setCellValue('H' . $i, $value['totalDiscountFee']);
				$sheet->setCellValue('I' . $i, $value['totalDrawnfee']);
				$sheet->setCellValue('J' . $i, number_format($value['totalPayfee']/$value['totalNum'], 2));
				$i++;
			}
		}while(count($result['data']['list']) >= $args['pageSize']);

		$name = iconv("utf-8", "gb2312", $args['year'].'-'.$args['month'].'-'.'代理数据统计信息');
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
	public function sellerexport(){
		$args = Input::all();
		$args['year'] = ($args['year'] > 0) ? $args['year'] : Time::toDate(UTC_TIME, 'Y');
		$args['month'] = ($args['month'] > 0) ? $args['month'] : Time::toDate(UTC_TIME, 'm');

		require_once base_path().'/vendor/phpexcel/PHPExcel.php';
		$execl = new \PHPExcel();

		$execl->setActiveSheetIndex(0);
		$sheet = $execl->getActiveSheet();


		$sheet->setCellValue('A1', "商家名");
		$sheet->setCellValue('B1', "本月营业额");
		$sheet->setCellValue('C1', "有效订单数");
		$sheet->setCellValue('D1', "在线支付");
		$sheet->setCellValue('E1', "现金支付");
		$sheet->setCellValue('F1', "积分奖金");
		$sheet->setCellValue('G1', "优惠券");
		$sheet->setCellValue('H1', "佣金");
		$sheet->setCellValue('I1', "客单价");

		$sheet->getColumnDimension('A')->setWidth(30);
		$sheet->getColumnDimension('B')->setWidth(20);
		$sheet->getColumnDimension('C')->setWidth(20);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(20);
		$sheet->getColumnDimension('H')->setWidth(20);
		$sheet->getColumnDimension('I')->setWidth(20);

		$execl->getActiveSheet()->getStyle( 'A2:I2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
		$execl->getActiveSheet()->getStyle( 'A2:I2')->getFill()->getStartColor()->setARGB('FF00CC33');

		$args['page'] = 0;
		$args['pageSize'] = 100;
		$i = 2;
		$proxy = [];
		do {
			$args['page']++;
			$result = $this->requestApi('proxy.statistics.sellerlists', $args);
			$proxy = $result['data']['proxy'];
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
				$sheet->setCellValue('A' . $i, ' '.$value['name'].' ');
				$sheet->setCellValue('B' . $i, $value['totalPayfee']);
				$sheet->setCellValue('C' . $i, $value['totalNum']);
				$sheet->setCellValue('D' . $i, $value['totalOnline']);
				$sheet->setCellValue('E' . $i, $value['totalCash']);
				$sheet->setCellValue('F' . $i, $value['totalIntegralFee']);
				$sheet->setCellValue('G' . $i, $value['totalDiscountFee']);
				$sheet->setCellValue('H' . $i, $value['totalDrawnfee']);
				$sheet->setCellValue('I' . $i, number_format($value['totalPayfee']/$value['totalNum'], 2));
				$i++;
			}
		}while(count($result['data']['list']) >= $args['pageSize']);
		$sheet->setTitle($args['year'].'-'.$args['month'].'-'.'代理('.$proxy['name'].')商家统计');

		$name = iconv("utf-8", "gb2312", $args['year'].'-'.$args['month'].'-'.'代理('.$proxy['name'].')商家统计');
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
		$args['year'] = ($args['year'] > 0) ? $args['year'] : Time::toDate(UTC_TIME, 'Y');
		$args['month'] = ($args['month'] > 0) ? $args['month'] : Time::toDate(UTC_TIME, 'm');

		require_once base_path().'/vendor/phpexcel/PHPExcel.php';
		$execl = new \PHPExcel();

		$execl->setActiveSheetIndex(0);
		$sheet = $execl->getActiveSheet();


		$sheet->setCellValue('A1', "日期");
		$sheet->setCellValue('B1', "营业额");
		$sheet->setCellValue('C1', "有效订单数");
		$sheet->setCellValue('D1', "在线支付");
		$sheet->setCellValue('E1', "现金支付");
		$sheet->setCellValue('F1', "积分奖金");
		$sheet->setCellValue('G1', "优惠券");
		$sheet->setCellValue('H1', "佣金");
		$sheet->setCellValue('I1', "入账金额");
		$sheet->setCellValue('J1', "客单价");

		$sheet->getColumnDimension('A')->setWidth(20);
		$sheet->getColumnDimension('B')->setWidth(15);
		$sheet->getColumnDimension('C')->setWidth(15);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(20);
		$sheet->getColumnDimension('H')->setWidth(20);
		$sheet->getColumnDimension('I')->setWidth(20);
		$sheet->getColumnDimension('J')->setWidth(20);

		$execl->getActiveSheet()->getStyle( 'A2:J2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
		$execl->getActiveSheet()->getStyle( 'A2:J2')->getFill()->getStartColor()->setARGB('FF00CC33');


		$i = 2;

		$result = $this->requestApi('proxy.statistics.monthlists', $args);
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
			$i++;
		}


		$sheet->setTitle($args['year'].'-'.$args['month'].'-'.'商家('.$seller['name'].')对账单');

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

		$result = $this->requestApi('proxy.statistics.daylists', $args);
		$seller = $result['data']['sum']['seller'];
		$sum = $result['data']['sum'];

		require_once base_path().'/vendor/phpexcel/PHPExcel.php';
		$execl = new \PHPExcel();

		$execl->setActiveSheetIndex(0);
		$sheet = $execl->getActiveSheet();
		$execl->getActiveSheet()->mergeCells('A1:H1');      //合并

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

		$sheet->getColumnDimension('A')->setWidth(30);
		$sheet->getColumnDimension('B')->setWidth(15);
		$sheet->getColumnDimension('C')->setWidth(15);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(20);
		$sheet->getColumnDimension('H')->setWidth(20);

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
			$i++;
		}

		$sheet->setTitle($args['day'].'代理商家('.$seller['name'].')订单明细');

		$name = iconv("utf-8", "gb2312", $args['day'].'代理商家('.$seller['name'].')订单明细');
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
