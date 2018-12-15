<?php
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\Activity;
use View, Input, Lang, Route, Page, Validator, Session, Response, Time;

class StockController extends AuthController{

    public function index(){
        $args = Input::All();
        $stock = $this->requestApi('stock.getLists', $args);
        if ($stock['code'] == 0) {
            View::share('list', $stock['data']['list']);
        }
        return $this->display();
    }
    public function create(){
        View::share('count',3);
        return $this->display('edit');
    }

    public function edit(){
        $args = Input::All();
        $stock = $this->requestApi('stock.detail', $args);
        if ($stock['code'] == 0) {
            $count = 3 - count( $stock['data']['stock']);
            View::share('count',$count);
            View::share('data', $stock['data']);
        }
        return $this->display('edit');
    }
    public function save(){
        $args = Input::All();
        $stock = $this->requestApi('stock.update', $args);
        if( $stock['code'] > 0 ) {
            return $this->error($stock['msg']);
        }
        return $this->success(Lang::get('admin.code.98008'), u('Stock/index'));
    }
    public function destroy() {
        $args = Input::all();
        $result = $this->requestApi('stock.delete', $args);
        if( $result['code'] > 0 ) {
            return $this->error($result['msg'], url('Stock/index'));
        }
        return $this->success($result['msg'], url('Stock/index'));
    }

}  