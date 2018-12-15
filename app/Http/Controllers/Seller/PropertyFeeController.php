<?php namespace YiZan\Http\Controllers\Seller;

use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Validator, Session, DB, Response, Redirect, Request;
/**
 * 物业费
 */
class PropertyFeeController extends AuthController {

	/**
	 * 物业费列表
	 */
	public function index() {
		$args = Input::all(); 
        $propertybuildinglist = $this->requestApi('propertybuilding.lists');
        View::share('builds', $propertybuildinglist['data']['list']);
        $payitemlist = $this->requestApi('payitem.lists',['isAll'=>1]);  
        View::share('payitemlist', $payitemlist['data']);  
        $result = $this->requestApi('propertyfee.lists', $args);
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
        View::share('args', $args);
		return $this->display();
	}

    public function create(){
        $args = Input::all();
        $list = $this->requestApi('propertybuilding.lists', ['pageSize' => 99999]);
        View::share('buildIds', $list['data']['list']);
        // $payitemlist = $this->requestApi('payitem.lists',['isAll'=>1]);
        // foreach ($payitemlist['data'] as $key => $value) {
        //     $payitemlist['data'][$key]['chargingItem'] = Lang::get('api_seller.property.charging_item.'.$value['chargingItem']);
        //     $payitemlist['data'][$key]['chargingUnit'] = Lang::get('api_seller.property.charging_unit.'.$value['chargingUnit']);
        // }
        // View::share('payitemlist', $payitemlist['data']);
        return $this->display('edit');
    }

    public function checkRoomFee(){
        $args = Input::all();
        $result = $this->requestApi('propertyfee.check', $args);
        if($result['code'] > 0){
            return $this->error($result['msg']);
        } else {
            return $this->success($result['msg']);
        }
    }

    /**
     * 详情
     */
    public function detail(){
        $args = Input::all();
        $data = $this->requestApi('propertyfee.get', $args);  
        View::share('data', $data['data']); 
        return $this->display();
    }

    public function save() {
        $args = Input::all();
        $data = $this->requestApi('propertyfee.save', $args);

        if ($args['id'] > 0) {
           $url = u('PropertyFee/index');
        } else {
            $url = u('PropertyFee/create');
        }
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98009'),$url );
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98008'), $url , $data['data']);

    } 

	/**
	 * [destroy 删除物业费]
	 */
	public function destroy(){
		$args = Input::all();
		if( $args['id'] > 0 ) {
			$result = $this->requestApi('propertyfee.delete',['id'=>$args['id']]); 
		}
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('PropertyFee/index'), $result['data']);
	} 

    public function searchroom() {
        $args = Input::all();
        $args['pageSize'] = 9999;
        $result = $this->requestApi('propertyroom.lists',$args);
        return Response::json($result);
    }

    /**
     * 物业打印
     */
    public function printer() {
        $args = Input::all();
        $data = $this->requestApi('propertyfee.get', $args);
        View::share('data', $data['data']);
        View::share('args', $args);
        View::share('time', Time::toDate(UTC_TIME,'Y-m-d H:i'));
        if($data['data']['fee'] > 0){
            View::share('money',$this->toChineseNumber($data['data']['fee']) );
        }else{
            View::share('money',"零圆");
        }
        return $this->display();
    }
    function toChineseNumber($money){
        $money = round($money,2);
        $cnynums = array("零","壹","贰","叁","肆","伍","陆","柒","捌","玖");
        $cnyunits = array("圆","角","分");
        $cnygrees = array("拾","佰","仟","万","拾","佰","仟","亿");
        list($int,$dec) = explode(".",$money,2);
        $dec = array_filter(array($dec[1],$dec[0]));
        $ret = array_merge($dec,array(implode("",$this->cnyMapUnit(str_split($int),$cnygrees)),""));
        $ret = implode("",array_reverse($this->cnyMapUnit($ret,$cnyunits)));
        return str_replace(array_keys($cnynums),$cnynums,$ret);
    }
    function cnyMapUnit($list,$units) {
        $ul=count($units);
        $xs=array();
        foreach (array_reverse($list) as $x) {
            $l=count($xs);
            if ($x!="0" || !($l%4))
                $n=($x=='0'?'':$x).($units[($l-1)%$ul]);
            else $n=is_numeric($xs[0][0])?$x:'';
            array_unshift($xs,$n);
        }
        return $xs;
    }

    public function lists(){
        $args = Input::all();  
        $result = $this->requestApi('propertyfee.lists', $args);
        View::share('list', $result['data']['list']);
        View::share('users', $result['data']['users']);
        View::share('totalFee', $result['data']['totalFee']);
        View::share('args', $args);
        return $this->display();
    }

    /**
     * 创建物业订单
     */
    public function createOrder(){
        $args = Input::all();
        $data = $this->requestApi('propertyorder.create', $args);  
        if($data['code']>0){
            return $this->error($data['msg'], u('PropertyFee/index'));
        }
        return $this->success($data['msg'], u('PropertyFee/index'));
    }

    /**
     * 导出到excel
     */
    public function export() {
        require_once base_path().'/vendor/phpexcel/PHPExcel.php';
        $execl = new \PHPExcel();

        $execl->setActiveSheetIndex(0);
        $sheet = $execl->getActiveSheet();
        $sheet->setTitle('物业收费管理列表');

        $sheet->setCellValue('A1', "编号");
        $sheet->setCellValue('B1', "楼栋号");
        $sheet->setCellValue('C1', "房间号");
        $sheet->setCellValue('D1', "业主");
        $sheet->setCellValue('E1', "收费项目");
        $sheet->setCellValue('F1', "费用");
        $sheet->setCellValue('G1', "计算开始时间");
        $sheet->setCellValue('H1', "结算结束时间");
        $sheet->setCellValue('I1', "状态");

        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(30);
        $sheet->getStyle('B')->getAlignment()->setWrapText(true);

        $args = Input::all();
        $i = 2;
        $result = $this->requestApi('propertyfee.lists', $args);

        if($args['json'] == 1){
            if(empty($result['data']['list'])){
                $results['code'] = 0;
            }else{
                $results['code'] = 1;
            }
            die(json_encode($results));
        }

        foreach ($result['data']['list'] as $key => $value) {

            if($value['status'] == 1){
                $payStatus = '已支付';
            }
            else{
                $payStatus = '未支付';
            }

            $sheet->setCellValue('A'.$i, $value['id']);
            $sheet->setCellValue('B'.$i, $value['build']['name']);
            $sheet->setCellValue('C'.$i, $value['room']['roomNum']);
            $sheet->setCellValue('D'.$i, $value['room']['owner']);
            $sheet->setCellValue('E'.$i, $value['roomfee']['payitem']['name']);
            $sheet->setCellValue('F'.$i, $value['fee']);
            $sheet->setCellValue('G'.$i, yztime($value['beginTime']));
            $sheet->setCellValue('H'.$i,  yztime($value['endTime']));
            $sheet->setCellValue('I'.$i, $payStatus);

            $i++;
        }

        $name = iconv("utf-8", "gb2312", "物业收费管理列表");
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
