<?php 
namespace YiZan\Http\Controllers\Admin;
use View, Input, Lang, Time, Response;

/**
 * 商家提现
 */
class SellerStaffWithdrawController extends AuthController {
	public function index() {
	    $args = Input::all();
        $nav = (int)$args['nav'] > 0 ? (int)$args['nav'] : 1;
        $args['beginTime'] = !empty($args['beginTime']) ? Time::toTime($args['beginTime']) : 0;
        $args['endTime'] = !empty($args['endTime']) ? Time::toTime($args['endTime']) : 0;
		$args['pageSize'] = 10;
		$result = $this->requestApi('withdraw.stafflists',$args);
		if( $result['code'] == 0 ) {
			View::share('list', $result['data']['list']);
		}
        View::share('nav', $nav);
        View::share('url', u('SellerStaffWithdraw/index',['nav'=>$nav, 'status' => (int)$args['status']]));
		return $this->display();
	}

	public function create(){
		return $this->display('edit');
	}
	
	public function edit(){
	    return $this->display();
	}

	public function save() {
		$city_id = 0;
		if (Input::get('areaId') > 0) {
			$city_id = Input::get('areaId');
		} elseif(Input::get('cityId') > 0) {
			$city_id = Input::get('cityId');
		} elseif(Input::get('provinceId') > 0) {
			$city_id = Input::get('provinceId');
		}

		if($city_id < 1) return $this->error(Lang::get('admin.code.27006'));
		$result = $this->requestApi('city.create',['cityId' => $city_id, 'sort' => Input::get('sort')]);

		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('City/index'), $result['data']);
	}

	
	public function dispose() {
	    $result = $this->requestApi('withdraw.staffdispose', Input::all());
		/*返回处理*/ 
		if($result['code']==0){
			return $this->success($result['msg']);
		}else{ 
			return $this->error($result['msg']);
		}  
	}

	public function destroy() {
		$args = Input::all();
		if( empty($args['id']) ) return $this->error(Lang::get('admin.code.27007'));
		$args['cityId'] = $args['id'];
		$result = $this->requestApi('city.delete',$args);
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98005'), u('City/index'), $result['data']);
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
		$sheet->setTitle('商家待提现信息');

		$sheet->setCellValue('A1', "商家ID");
		$sheet->setCellValue('B1', "商家名称");
		$sheet->setCellValue('C1', "开户行");
		$sheet->setCellValue('D1', "银行卡卡号");
		$sheet->setCellValue('E1', "持有人");
		$sheet->setCellValue('F1', "提现金额");
		$sheet->setCellValue('G1', "申请时间");

		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getColumnDimension('C')->setWidth(25);
		$sheet->getColumnDimension('D')->setWidth(35);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(30);


		$args = Input::all();
		$args['page'] = 0;
		$args['pageSize'] = 50;
        $args['beginTime'] = !empty($args['beginTime']) ? Time::toTime($args['beginTime']) : 0;
        $args['endTime'] = !empty($args['endTime']) ? Time::toTime($args['endTime']) : 0;
		$i = 2;
		do {
			$args['page']++;
			$result = $this->requestApi('withdraw.stafflists', $args);

			foreach ($result['data']['list'] as $key => $value) {
				$sheet->setCellValue('A' . $i, $value['seller']['id']);
				$sheet->setCellValue('B' . $i, $value['seller']['name']);
				$sheet->setCellValue('C' . $i, $value['bank']);
				$sheet->setCellValue('D' . $i, ' '.$value['bankNo'].' ');
				$sheet->setCellValue('E' . $i, $value['name']);
				$sheet->setCellValue('F' . $i, $value['money']);
				$sheet->setCellValue('G' . $i, Time::toDate($value['createTime']));
				$i++;
			}
		}while(count($result['data']['list']) >= $args['pageSize']);

		$name = iconv("utf-8", "gb2312", "配送人员提现信息");
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
	 * 获取卖家提现消息
	 */
	public function getWithdrawMessage() {
        $result = $this->requestApi('withdraw.lists');
		return Response::json($result);
	}
}
