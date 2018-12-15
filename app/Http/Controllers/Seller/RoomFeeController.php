<?php namespace YiZan\Http\Controllers\Seller;

use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Validator, Session, DB, Response, Redirect, Request;
/**
 * 物业费
 */
class RoomFeeController extends AuthController {

	/**
	 * 物业费列表
	 */
	public function index() {
		$args = Input::all(); 
        $result = $this->requestApi('roomfee.lists', $args);
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        } 
        View::share('args', $args);
        return $this->display();
	}

    public function search(){
        $args = Input::all(); 
        $result = $this->requestApi('roomfee.search', $args); 
        return Response::json($result['data']); 
    }

    public function create(){
        $args = Input::all();
        $list = $this->requestApi('propertybuilding.lists',['pageSize'=>99999]);
        View::share('buildIds', $list['data']['list']);
       // $roomlist = $this->requestApi('propertyroom.lists'); 
      //  View::share('roomIds', $roomlist['data']['list']);
        $payitemlist = $this->requestApi('payitem.lists',['isAll'=>1]); 
        foreach ($payitemlist['data'] as $key => $value) {
            $payitemlist['data'][$key]['chargingItem'] = Lang::get('api_seller.property.charging_item.'.$value['chargingItem']);
            $payitemlist['data'][$key]['chargingUnit'] = Lang::get('api_seller.property.charging_unit.'.$value['chargingUnit']);
        }
        View::share('payitemlist', $payitemlist['data']); 
        return $this->display('edit');
    }

    /**
     * 编辑
     */
    public function edit(){
        $args = Input::all();
        $data = $this->requestApi('roomfee.get', $args); 
        View::share('data', $data['data']);
        $list = $this->requestApi('propertybuilding.lists',['pageSize'=>99999]);
        View::share('buildIds', $list['data']['list']);
        $roomlist = $this->requestApi('propertyroom.lists', ['buildId'=>$data['data']['buildId']]); 
        View::share('roomIds', $roomlist['data']['list']);
        $payitemlist = $this->requestApi('payitem.lists',['isAll'=>1]); 
        foreach ($payitemlist['data'] as $key => $value) {
            $payitemlist['data'][$key]['chargingItem'] = Lang::get('api_seller.property.charging_item.'.$value['chargingItem']);
            $payitemlist['data'][$key]['chargingUnit'] = Lang::get('api_seller.property.charging_unit.'.$value['chargingUnit']);
        }
        View::share('payitemlist', $payitemlist['data']); 
        return $this->display();
    }

    public function save() {
        $args = Input::all();
        $data = $this->requestApi('roomfee.save', $args);

        if ($args['id'] > 0) {
           $url = u('RoomFee/index');
        } else {
            $url = u('RoomFee/create');
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
            $args['id'] = explode(',', $args['id']);

			$result = $this->requestApi('roomfee.delete',['id'=>$args['id']]); 
		}
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('RoomFee/index'), $result['data']);
	} 

    public function searchroom() {
        $args = Input::all();
        $args['pageSize'] = 9999;
        $result = $this->requestApi('propertyroom.lists',$args);
        return Response::json($result['data']);
    }  

}
