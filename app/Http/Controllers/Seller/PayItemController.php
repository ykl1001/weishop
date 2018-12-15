<?php namespace YiZan\Http\Controllers\Seller;

use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Validator, Session, DB, Response, Redirect;
/**
 * 收费项目
 */
class PayItemController extends AuthController {

	/**
	 * 收费项目列表
	 */
	public function index() {
		$args = Input::all();  
        $result = $this->requestApi('payitem.lists', $args); 
        if(Input::ajax()){
            return Response::json($result['data']['list']);
        } else {
            if( $result['code'] == 0 ){
                View::share('list', $result['data']['list']);
            } 
            View::share('args',$args);
            return $this->display();
        }
	} 

    /*
    * 收费项目添加
    */
    public function create() { 
        View::share('chargingItem', Lang::get('api_seller.property.charging_item'));
        View::share('chargingUnit', Lang::get('api_seller.property.charging_unit'));
        return $this->display('edit');
    }

	/*
	* 收费项目查看
	*/
	public function edit() {
		$args = Input::all(); 
        $data = $this->requestApi('payitem.get', $args);
        if ($data['code'] == 0) {
            View::share('data', $data['data']);
        } 
        View::share('chargingItem', Lang::get('api_seller.property.charging_item'));
        View::share('chargingUnit', Lang::get('api_seller.property.charging_unit'));
        View::share('args', $args);
		return $this->display();
	} 

	/*
	* 收费项目保存
	*/
	public function save() {
		$args = Input::all(); 
        $result = $this->requestApi('payitem.save', $args);
		if($result['code'] == 0){
            return $this->success($result['msg']);
        }
        return $this->error($result['msg']);
    }  

    /*
    * 收费项目删除
    */
    public function destroy(){
        $args = Input::all();
        $data = $this->requestApi('payitem.delete', ['id' => $args['id']]);
        $url = u('PayItem/index');
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'),$url );
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), $url , $data['data']);
    }
}
