<?php
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\Activity;
use View, Input, Lang, Route, Page, Validator, Session, Response, Time;
/**
 * 繳費記錄
 */
class InvitationMoneyController extends AuthController{

    public function index(){
        $args = Input::all();
        $result = $this->requestApi('invitation.moneylog', $args);
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
        return $this->display();
    }
}  