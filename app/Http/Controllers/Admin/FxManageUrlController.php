<?php 
namespace YiZan\Http\Controllers\Admin;

use View, Input, Lang, Route, Page, Response, Config, Redirect;

/**
 * 方维分销平台
 */
class FxManageUrlController extends AuthController {
    /**
     * [makeManageUrl 同步到分销平台]
     * @return [type] [description]
     */
    public function index() {
        $manageurl = $this->requestApi('fx.makeManageUrl');

        $args = Input::all();
        if($args['direct'] == 1){
            return Redirect::to($manageurl['data']);
        }

		View::share('manageurl', $manageurl['data']);
        return $this->display();
    }
	
}
