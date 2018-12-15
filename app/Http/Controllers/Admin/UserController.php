<?php 
namespace YiZan\Http\Controllers\Admin; 

use View, Input, Response,Time;

/**
 * 会员管理
 */
class UserController extends AuthController {  

	/**
	 * 会员列表
	 */
	public function index() {
		//会员列表接口 
		$result = $this->requestApi('user.lists', Input::all());
		if( $result['code'] == 0 ) {
			View::share('list', $result['data']['list']); 
		}
		return $this->display(); 
	}

	/**
	 * 获取会员
	 * 根据会员Id 获取会员信息
	 */
	public function edit() {	
		$result = $this->requestApi('user.get', Input::all());
        /*返回处理*/
		if($result['code'] > 0 ) {
			return $this->error($result['msg'], u('User/index'));
		} 
		View::share('data', $result['data']);
		return $this->display(); 
	}
	/**
	* 
	*   修改会员信息
	*	id				会员编号
	*	mobile			注册的手机号码
	*	name			昵称
	*	pwd				5-20位字符串，为空不修改
	*	avatar			头像，为空不修改 
	*/
	public function update() {
		/*修改会员接口*/
		$result = $this->requestApi('user.update', Input::all());
		/*返回处理*/
		if( $result['code'] == 0 ) {
            $args = Input::all();
			return $this->success($result['msg'], u('User/index'));
		} else {
			return $this->error($result['msg']);
		}
	}

	/**
	 * 删除会员信息
	 */
	public function destroy() {
		$args = Input::all();
		$args['id'] = explode(',', $args['id']);
		$result = $this->requestApi('user.delete', $args);
		if( $result['code'] > 0 ) {
			return $this->error($result['msg'], url('User/index'));
		}
		return $this->success($result['msg'], url('User/index'));
	}
	
	/**
	 * 搜索会员
	 * 根据会员Id 获取会员信息
	 */
	public function search() {
		/*获取会员接口*/
		$list = $this->requestApi('user.search', Input::all()); 
		return Response::json($list['data']);
	}
	/**
	 * 会员洗车券
	 * 根据会员Id 获取洗车券
	 */
	public function detail() {
		/*获取会员接口*/
		$args =  Input::all();
		$list = $this->requestApi('user.promotion.lists', $args);
		View::share('list', $list['data']['list']);
		View::share('id', $args['id']);
		return $this->display(); 
	}


    /**
     * 修改余额
     */
    public function updatebalance(){
        $args = Input::all();
        $result = $this->requestApi('user.updatebalance', $args);
        return Response::json($result);
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
		$sheet->setTitle('会员信息');

		$sheet->setCellValue('A1', "会员ID");
		$sheet->setCellValue('B1', "会员昵称");
		$sheet->setCellValue('C1', "手机");
		$sheet->setCellValue('D1', "余额");
		$sheet->setCellValue('E1', "现有积分");
		$sheet->setCellValue('F1', "累计积分");
		$sheet->setCellValue('G1', "注册时间");

		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getColumnDimension('C')->setWidth(25);
		$sheet->getColumnDimension('D')->setWidth(13);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(30);


		$args = Input::all();
		$args['page'] = 0;
		$args['pageSize'] = 500;
		$i = 2;
		do {
			$args['page']++;
			$result = $this->requestApi('user.lists', $args);

			foreach ($result['data']['list'] as $key => $value) {
				$sheet->setCellValue('A' . $i, $value['id']);
				$sheet->setCellValue('B' . $i, $value['name']);
				$sheet->setCellValue('C' . $i, $value['mobile']);
				$sheet->setCellValue('D' . $i, $value['balance']);
				$sheet->setCellValue('E' . $i, $value['integral']);
				$sheet->setCellValue('F' . $i, $value['totalIntegral']);
				$sheet->setCellValue('G' . $i, date('Y-m-d h:i:s', $value['regTime']));
				$i++;
			}
		}while(count($result['data']['list']) >= $args['pageSize']);

		$name = iconv("utf-8", "gb2312", "会员信息");
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

    public function paylog(){
        $args = Input::all();
        $args['nav'] = $nav = (int)$args['nav'] > 0 ? (int)$args['nav'] : 1;
        View::share('nav', $nav);
        View::share('url', u('User/paylog',['nav'=>$nav, 'userId' => (int)$args['userId']]));

        $args['beginTime'] = !empty($args['beginTime']) ? Time::toTime($args['beginTime']) : 0;
        $args['endTime'] = !empty($args['endTime']) ? Time::toTime($args['endTime']) : 0;
        $args['pageSize'] = 10;

        View::share('args', $args);
        $result = $this->requestApi('user.paylog',$args);

        if( $result['code'] == 0 ) {
            View::share('list', $result['data']['list']);
        }
        return $this->display();
    }

    /**
     * 导出数据
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function paylogExport(){
        require_once base_path().'/vendor/phpexcel/PHPExcel.php';
        $execl = new \PHPExcel();

        $execl->setActiveSheetIndex(0);
        $sheet = $execl->getActiveSheet();
        $sheet->setTitle('明细');

        $args = Input::all();
        if($args['nav'] == 1){
            $sheet->setCellValue('A1', "编号");
            $sheet->setCellValue('B1', "金额");
            $sheet->setCellValue('C1', "描述");
            $sheet->setCellValue('D1', "创建时间");
            $sheet->setCellValue('E1', "支付时间");

            $sheet->getColumnDimension('A')->setWidth(10);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(15);
            $sheet->getColumnDimension('E')->setWidth(15);

            $args = Input::all();
            $args['page'] = 0;
            $args['pageSize'] = 500;
            $i = 2;
            do {
                $args['page']++;
                $result = $this->requestApi('user.paylog',$args);
                foreach ($result['data']['list'] as $key => $value) {
                    $sheet->setCellValue('A' . $i, $value['id']);
                    $sheet->setCellValue('B' . $i, $value['money']);
                    $sheet->setCellValue('C' . $i, $value['content']);
                    $sheet->setCellValue('D' . $i, Time::toDate($value['createTime']));
                    $sheet->setCellValue('E' . $i, Time::toDate($value['payTime']));
                    $i++;
                }
            }while(count($result['data']['list']) >= $args['pageSize']);
        }else{
            $sheet->setCellValue('A1', "编号");
            $sheet->setCellValue('B1', "积分");
            $sheet->setCellValue('C1', "描述");
            $sheet->setCellValue('D1', "创建时间");
            $sheet->setCellValue('E1', "消费金额");

            $sheet->getColumnDimension('A')->setWidth(10);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(15);
            $sheet->getColumnDimension('E')->setWidth(15);

            $args = Input::all();
            $args['page'] = 0;
            $args['pageSize'] = 500;
            $i = 2;
            do {
                $args['page']++;
                $result = $this->requestApi('user.paylog',$args);

                foreach ($result['data']['list'] as $key => $value) {
                    $sheet->setCellValue('A' . $i, $value['id']);
                    $sheet->setCellValue('B' . $i, $value['integral']);
                    $sheet->setCellValue('C' . $i, $value['remark']);
                    $sheet->setCellValue('D' . $i, Time::toDate($value['createTime']));
                    $sheet->setCellValue('E' . $i, $value['money']);
                    $i++;
                }
            }while(count($result['data']['list']) >= $args['pageSize']);
        }

        $name = iconv("utf-8", "gb2312", "明细");
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
