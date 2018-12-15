<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\OrderConfig;
use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Form;
/**
 * 订单统计
 */
class OneselfStatisticsController extends AuthController {
	public function index(){

        $args = Input::all();
        $orderyear = $this->requestApi('seller.statistics.year',['sellerId'=>ONESELF_SELLER_ID]);
        View::share('orderyear',$orderyear['data']);
        $args['year'] = ($args['year'] > 0) ? $args['year'] : (int)Time::toDate(UTC_TIME, 'Y');
        $args['month'] = ($args['month'] > 0) ? $args['month'] : (int)Time::toDate(UTC_TIME, 'm');
		View::share('args', $args);
		$data = $this->requestApi('order.ordercount.oneselfordernum',$args);
		if($data['code'] == 0){
			View::share('list', $data['data']['list']);
		}else if($data['code'] == 19999){
		    //return $this->error("时间段必须为1-15天");
		}
        View::share('args', $args);
        $argsp['sellerId'] = ONESELF_SELLER_ID;
        $result = $this->requestApi('goods.cate.lists',$argsp );
        $tagList2 = [
            "id" => 0,
            "name" => "全部",
        ];
        array_unshift($result['data'],$tagList2);
        View::share('cate', $result['data']);
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
        $sheet->setTitle(' 商城商品统计');

        $sheet->setCellValue('A1', "商品\\服务名称");
        $sheet->setCellValue('B1', "分类");
        $sheet->setCellValue('C1', "销售");
        $sheet->setCellValue('D1', "销量额");

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(30);

        $sheet->getStyle('B')->getAlignment()->setWrapText(true);

        $args = Input::all();
        $orderyear = $this->requestApi('seller.statistics.year',['sellerId'=>ONESELF_SELLER_ID]);
        View::share('orderyear',$orderyear['data']);
        $args['year'] = ($args['year'] > 0) ? $args['year'] : (int)Time::toDate(UTC_TIME, 'Y');
        $args['month'] = ($args['month'] > 0) ? $args['month'] : (int)Time::toDate(UTC_TIME, 'm');
        View::share('args', $args);
        $result = $this->requestApi('order.ordercount.oneselfordernum',$args);
        $i = 2;
        foreach ($result['data']['list'] as $key => $value) {
            $sheet->setCellValue('A'.$i, $value['goodsName']);
            $sheet->setCellValue('B'.$i, $value['categoods']['cate']['name']);
            $sheet->setCellValue('C'.$i, $value['num']);
            $sheet->setCellValue('D'.$i, $value['totleprice']);
            $i++;
        }

        $name = iconv("utf-8", "gb2312", "商城商品统计");
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
