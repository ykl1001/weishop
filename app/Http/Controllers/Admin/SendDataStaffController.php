<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Utils\Time;
use Input, View, Response, Lang;
/**
 * 配送设置
 */
class SendDataStaffController extends AuthController {
	public function index() {
		$args = Input::all();

		if($args['beginTime'] || $args['endTime'])
		{
			$args['beginTime'] 	= Time::toTime($args['beginTime']);
			$args['endTime'] 	= Time::toTime($args['endTime']);
			if(empty($args['beginTime']) || empty($args['endTime']))
			{
				return $this->error(Lang::get('api_system.code.80002'));	//开始时间或结束时间不能为空
			}
			if($args['beginTime'] > $args['endTime'])
			{
				return $this->error(Lang::get('api_system.code.40305'));	//结束时间不能小于开始时间
			}
		}

		$result =  $this->requestApi('sendcenter.stafflist', $args);

		if($result['code'] == 0)
		{
			View::share("list", $result['data']['list']);
		}

		View::share('args', $args);
		return $this->display();
	}

	/**
	 * 导出数据
	 * @throws \PHPExcel_Exception
	 * @throws \PHPExcel_Reader_Exception
	 */
	public function export(){
		require_once base_path().'/vendor/phpexcel/PHPExcel.php';
		$execl = new \PHPExcel();

		$execl->setActiveSheetIndex(0);
		$sheet = $execl->getActiveSheet();
		$sheet->setTitle('平台配送人员数据');

		$sheet->setCellValue('A1', "人员ID");
		$sheet->setCellValue('B1', "人员姓名");
		$sheet->setCellValue('C1', "所属公司");
		$sheet->setCellValue('D1', "所在城市");
		$sheet->setCellValue('E1', "订单总数");
		$sheet->setCellValue('F1', "完成总数");
		$sheet->setCellValue('G1', "赚取金额");
		$sheet->setCellValue('H1', "异常订单");

		$sheet->getColumnDimension('A')->setWidth(20);
		$sheet->getColumnDimension('B')->setWidth(50);
		$sheet->getColumnDimension('C')->setWidth(60);
		$sheet->getColumnDimension('D')->setWidth(60);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(20);
		$sheet->getColumnDimension('H')->setWidth(20);

		$args = Input::all();
		$args['pageSize'] = 20;

		if($args['beginTime'] || $args['endTime'])
		{
			$args['beginTime'] 	= Time::toTime($args['beginTime']);
			$args['endTime'] 	= Time::toTime($args['endTime']);
			if(empty($args['beginTime']) || empty($args['endTime']))
			{
				return $this->error(Lang::get('api_system.code.80002'));	//开始时间或结束时间不能为空
			}
			if($args['beginTime'] > $args['endTime'])
			{
				return $this->error(Lang::get('api_system.code.40305'));	//结束时间不能小于开始时间
			}
		}

		$i = 2;
		do {
			$args['page']++;
			$result =  $this->requestApi('sendcenter.stafflist', $args);
			foreach ($result['data']['list'] as $key => $value) {
				$sheet->setCellValue('A' . $i, $value['id']);
				$sheet->setCellValue('B' . $i, $value['name']);
				$sheet->setCellValue('C' . $i, $value['company']);
				$sheet->setCellValue('D' . $i, $value['address']);
				$sheet->setCellValue('E' . $i, $value['total']['totalOrder']);
				$sheet->setCellValue('F' . $i, $value['total']['totalEndOrder']);
				$sheet->setCellValue('G' . $i, $value['total']['mackMoney']);
				$sheet->setCellValue('H' . $i, $value['total']['totalErrOrder']);
				$i++;
			}
		}while(count($result['data']['list']) >= $args['pageSize']);

		$name = iconv("utf-8", "gb2312", "平台配送人员数据");
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
