<?php namespace YiZan\Http\Controllers\Seller;

use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Validator, Session, DB, Response, Redirect;
/**
 * 报修
 */
class RepairTotalController extends AuthController {

	/**
	 * 报修列表
	 */
	public function index() {
		$args = Input::all();
        !empty($args['name'])      ? $args['name']   = strval($args['name'])    : null;
        !empty($args['build'])      ? $args['build']   = strval($args['build'])    : null;
        !empty($args['roomNum'])      ? $args['roomNum']   = strval($args['roomNum'])    : null;
        !empty($args['page'])     ? $args['page']  = intval($args['page'])       : $args['page'] = 1;
        $args['status'] = $args['status'] - 1;
        $result = $this->requestApi('repair.lists', $args);

        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }

        $arr = array();
        $arr['type'] = $args['type'];
        View::share('searchUrl', u('RepairTotal/index'));
        View::share('args',$args);
		return $this->display();
	}


	/*
	* 查看
	*/
	public function edit() {
		$args = Input::all(); 
        $data = $this->requestApi('repair.get', $args);
        if ($data['code'] == 0) {
            View::share('data', $data['data']);
        }
        View::share('args', $args);
		return $this->display();
	}

    /**
     * [destroy 删除维修]
     */
    public function destroy(){

        $args = Input::all();
        if( !empty( $args['id'] ) ) {
            $result = $this->requestApi('repair.delete',$args);
        }
        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98008'), u('RepairTotal/index'), $result['data']);
    }


    /**
     * 导出到excel
     */
    public function export() {
        require_once base_path().'/vendor/phpexcel/PHPExcel.php';
        $execl = new \PHPExcel();

        $execl->setActiveSheetIndex(0);
        $sheet = $execl->getActiveSheet();
        $sheet->setTitle('报修统计列表');

        $sheet->setCellValue('A1', "编号");
        $sheet->setCellValue('B1', "报修人");
        $sheet->setCellValue('C1', "认证身份");
        $sheet->setCellValue('D1', "楼栋号");
        $sheet->setCellValue('E1', "房间号");
        $sheet->setCellValue('F1', "故障类型");
        $sheet->setCellValue('G1', "维修人");
        $sheet->setCellValue('H1', "报修时间");
        $sheet->setCellValue('I1', "维修完成时间");
        $sheet->setCellValue('J1', "维修评论");
        $sheet->setCellValue('K1', "维修结果");


        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(30);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('k')->setWidth(15);



        $sheet->getStyle('B')->getAlignment()->setWrapText(true);

        $args = Input::all();
        $i = 2;
        $result = $this->requestApi('repair.lists', $args);

        if($args['json'] == 1){
            if(empty($result['data']['list'])){
                $results['code'] = 0;
            }else{
                $results['code'] = 1;
            }
            die(json_encode($results));
        }

        $type = [
            0 => '业主',
            1 => '租客',
            2 => '业主家属',
        ];

        foreach ($result['data']['list'] as $key => $value) {

            if($value['status'] == 1){
                $status = '处理中';
            }
            else if($value['status'] == 2){
                $status = '处理完成';
            }
            else{
                $status = '待处理';
            }

            $sheet->setCellValue('A'.$i, $value['id']);
            $sheet->setCellValue('B'.$i, $value['puser']['name']);
            $sheet->setCellValue('C'.$i, $type[$value['puser']['type']]);
            $sheet->setCellValue('D'.$i, $value['build']['name']);
            $sheet->setCellValue('E'.$i, $value['room']['roomNum']);
            $sheet->setCellValue('F'.$i, $value['types']['name']);
            $sheet->setCellValue('G'.$i, $value['staff']['name']);
            $sheet->setCellValue('H'.$i,  yztime($value['apiTime']));
            $sheet->setCellValue('I'.$i, yztime($value['finishTime']));
            $sheet->setCellValue('J'.$i, $value['star']);
            $sheet->setCellValue('K'.$i, $status);


            $i++;
        }

        $name = iconv("utf-8", "gb2312", "物业报修管理列表");
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
