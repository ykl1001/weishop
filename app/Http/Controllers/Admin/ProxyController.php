<?php 
namespace YiZan\Http\Controllers\Admin;
 
use View, Input, Lang, Response;

/**
 * 代理管理
 */
class ProxyController extends AuthController {

	/**
	 * 代理列表
	 */
	public function index() {
		$args = Input::all();

        $zx = array("1", "18", "795", "2250");
        View::share('zx', $zx);

		if(Input::ajax()){
			$args['isAll'] = 1;
			$result = $this->requestApi('proxy.lists',$args);
			return Response::json($result['data']);
		} else { 
			$result = $this->requestApi('proxy.lists',$args);
			if( $result['code'] == 0 ) {
				View::share('list', $result['data']['list']);
			}  
	        return $this->display();
		}
	}

	/**
	 * 添加代理
	 */
	public function create(){
		return $this->display('edit');
	}

	/**
	 * 编辑代理
	 */
	public function edit(){
		$args = Input::all();
		$result = $this->requestApi('proxy.detail', $args); 
		$result['data']['pwd'] = '';
		View::share('data', $result['data']);
		return $this->display();
	}

	/**
	 * 保存代理
	 */
	public function save() {
		$args = Input::all();
		$result = $this->requestApi('proxy.save', $args); 
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success($result['msg'], u('Proxy/index'), $result['data']);
	} 

	/**
	 * 删除代理
	 */
	public function destroy() {
		$args = Input::all(); 
		$args['id'] = explode(',', $args['id']);
		$result = $this->requestApi('proxy.delete',$args);
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success($result['msg'], u('Proxy/index'), $result['data']);
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
		$sheet->setTitle('代理列表');

		$sheet->setCellValue('A1', "编号");
		$sheet->setCellValue('B1', "代理账户");
		$sheet->setCellValue('C1', "代理等级");
		$sheet->setCellValue('D1', "电话");
		$sheet->setCellValue('E1', "城市");
		$sheet->setCellValue('F1', "行政区");
		$sheet->setCellValue('G1', "自定义");
		$sheet->setCellValue('H1', "状态");

		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getColumnDimension('C')->setWidth(20);
		$sheet->getColumnDimension('D')->setWidth(20);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(50);
		$sheet->getColumnDimension('H')->setWidth(10);


		$args = Input::all();
		$args['page'] = 0;
		$args['pageSize'] = 50;
		$i = 2;
		$zx = array("1", "18", "795", "2250");
		$proxyLvl = ['1'=>'一级代理','2'=>'二级代理','3'=>'三级代理'];
		do {
			$args['page']++;
			$result = $this->requestApi('proxy.lists', $args);

			foreach ($result['data']['list'] as $key => $value) {
				if(!in_array($value['province']['id'],$zx))
				 	$cityName = $value['city']['name'];
				else
					 $cityName = $value['province']['name'];
				if(!in_array($value['province']['id'],$zx))
					$districtName = $value['area']['name'];
				else
					$districtName = $value['city']['name'];

				$sheet->setCellValue('A' . $i, $value['id']);
				$sheet->setCellValue('B' . $i, ' '.$value['name'].' ');
				$sheet->setCellValue('C' . $i, $proxyLvl[$value['level']]);
				$sheet->setCellValue('D' . $i, $value['mobile']);
				$sheet->setCellValue('E' . $i, $cityName);
				$sheet->setCellValue('F' . $i, $districtName);
				$sheet->setCellValue('G' . $i, $value['thirdArea']);
				$sheet->setCellValue('H' . $i, $value['status']?'启用':'禁用');
				$i++;
			}
		}while(count($result['data']['list']) >= $args['pageSize']);

		$name = iconv("utf-8", "gb2312", "代理列表");
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
