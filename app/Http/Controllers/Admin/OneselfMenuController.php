<?php
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\Menu;
use View, Input, Lang, Route, Page, Validator, Session, Response, Time;
/**
 * 自营菜单
 */
class OneselfMenuController extends MenuController{
    /**
     * 首页
     */
    public function index(){
        $post = Input::all();

        !empty($post['page']) ? $args['page'] = intval($post['page']) : $args['page'] = 1;
        $args['type'] = "oneself";
        $result = $this->requestApi('Menu.lists', $args);
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
        View::share('url', u('OneselfMenu/create'));
        return $this->display();
    }
    /**
     * 添加活动
     */
    public function save(){
        $args = Input::all();
        $args['platformType'] = "oneself";
        $result = $this->requestApi('Menu.update',$args);
        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }
        return $this->success( Lang::get('admin.code.98008'), u('OneselfMenu/index'), $result['data'] );
    }
	
	/**
     * [destroy]
     */
    public function destroy(){
        $args['id'] = explode(',', Input::get('id'));
        if( $args['id'] > 0 ) {
            $result = $this->requestApi('Menu.delete',$args);
        }
        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98005'), u('OneselfMenu/index'), $result['data']);
    }
}  