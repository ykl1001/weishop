<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\OrderConfig;
use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Form;
/**
 * 订单统计
 */
class OneselfBusinessStatisticsController  extends AuthController {
	public function index(){
        $args = Input::all();
        $orderyear = $this->requestApi('seller.statistics.year',['sellerId'=>ONESELF_SELLER_ID]);
        View::share('orderyear',$orderyear['data']);
        $args['year'] = ($args['year'] > 0) ? $args['year'] : (int)Time::toDate(UTC_TIME, 'Y');
        $args['month'] = ($args['month'] > 0) ? $args['month'] : (int)Time::toDate(UTC_TIME, 'm');
		View::share('args', $args);
		$data = $this->requestApi('order.ordercount.revenue',$args);
		if($data['code'] == 0){
			View::share('list', $data['data']['list']);
			View::share('total', $data['data']['total']);
		}
		return $this->display();
	}

    /**
     * 导出到excel
     */
    public function export() {
        require_once base_path().'/vendor/phpexcel/PHPExcel.php';
        $execl = new \PHPExcel();

        $execl->setActiveSheetIndex(0);
        $sheet = $execl->getActiveSheet();
        $sheet->setTitle(' 商城营业统计');

        $sheet->setCellValue('A1', "日期");
        $sheet->setCellValue('B1', "营业额");
        $sheet->setCellValue('C1', "有效订单");
        $sheet->setCellValue('D1', "退款\\取消订单");
        $sheet->setCellValue('E1', "优惠券");
        $sheet->setCellValue('F1', "积分抵扣");

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(13);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(30);

        $sheet->getStyle('B')->getAlignment()->setWrapText(true);

        $args = Input::all();
        $args['year'] = ($args['year'] > 0) ? $args['year'] : (int)Time::toDate(UTC_TIME, 'Y');
        $args['month'] = ($args['month'] > 0) ? $args['month'] : (int)Time::toDate(UTC_TIME, 'm');
        View::share('args', $args);
        $result = $this->requestApi('order.ordercount.revenue',$args);
        $sheet->setCellValue('A2', "汇总");
        $sheet->setCellValue('B2', $result['data']['total']['totalMoney']);
        $sheet->setCellValue('C2', $result['data']['total']['totalNum']);
        $sheet->setCellValue('D2', $result['data']['total']['totalCancelNum']);
        $sheet->setCellValue('E2', $result['data']['total']['totalPromotion']);
        $sheet->setCellValue('F2', $result['data']['total']['totalIntegral']);
        $i = 3;
        foreach ($result['data']['list'] as $key => $value) {
            $sheet->setCellValue('A'.$i, $value['date']);
            $sheet->setCellValue('B'.$i, $value['total']);
            $sheet->setCellValue('C'.$i, $value['num']);
            $sheet->setCellValue('D'.$i, $value['cancelNum']);
            $sheet->setCellValue('E'.$i, $value['orderPromotion']);
            $sheet->setCellValue('F'.$i, $value['integral']);
            $i++;
        }

        $name = iconv("utf-8", "gb2312", "商城营业统计");
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
