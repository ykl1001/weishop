<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Utils\Time;
use Input, View, Response, Lang;
/**
 * 配送设置
 */
class SendDataInfoController extends AuthController {
	public function index() {
		$args = Input::all();

		$result =  $this->requestApi('sendcenter.citylist', $args);

		if($result['code'] == 0)
		{
			View::share("list", $result['data']);
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
		$sheet->setTitle('平台配送数据概况');

		$sheet->setCellValue('A1', "城市");
		$sheet->setCellValue('B1', "订单总量");
		$sheet->setCellValue('C1', "完成总量");
		$sheet->setCellValue('D1', "异常订单");
		$sheet->setCellValue('E1', "服务费合计");

		$sheet->getColumnDimension('A')->setWidth(30);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getColumnDimension('C')->setWidth(30);
		$sheet->getColumnDimension('D')->setWidth(30);
		$sheet->getColumnDimension('E')->setWidth(30);

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

		$i = 3;
		$result =  $this->requestApi('sendcenter.citylist', $args);

		//合计
		$total_num = 0;
        $finish_num = 0;
        $abnormal_num = 0;
        $total_send_fee = 0;
        foreach($result['data'] as $v){
            $total_num += $v['total_num'];
            $finish_num += $v['finish_num'];
            $abnormal_num += $v['abnormal_num'];
            $total_send_fee += $v['total_send_fee'];
        }
        $sheet->setCellValue('A2', '总计');
		$sheet->setCellValue('B2', $total_num);
		$sheet->setCellValue('C2', $finish_num);
		$sheet->setCellValue('D2', $abnormal_num);
		$sheet->setCellValue('E2', $total_send_fee);

        //单项统计
		foreach ($result['data'] as $key => $value) {
			$sheet->setCellValue('A' . $i, $value['name']);
			$sheet->setCellValue('B' . $i, $value['total_num']);
			$sheet->setCellValue('C' . $i, $value['finish_num']);
			$sheet->setCellValue('D' . $i, $value['abnormal_num']);
			$sheet->setCellValue('E' . $i, $value['total_send_fee']);
			$i++;
		}

		$name = iconv("utf-8", "gb2312", "平台配送数据概况");
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
