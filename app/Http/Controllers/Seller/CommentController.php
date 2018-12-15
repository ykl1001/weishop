<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Models\Comment;
use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Validator, Session, Response, Redirect;
/**
 * 评价
 */
class CommentController extends AuthController {
	
	public function index() {
		$post = Input::all(); 
		$seller = $this->seller;

		if($seller['storeType'] == 1)
		{
			//全国店
			return Redirect::to('Comment/indexall');
		}

		$result = $this->requestApi('order.rate.lists', $post);
		if($result['code']==0){
			View::share('list',$result['data']['list']);	
		}
		// View::share('args', $args);
		return $this->display();
	}

	//全国店评价列表
	public function indexall() {
		$args = Input::all();

		if($args['beginTime'] || $args['endTime'])
		{
			$args['beginTime'] = Time::toTime($args['beginTime']);
            $args['endTime'] = Time::toTime($args['endTime']) + 86400;
            if($args['beginTime'] > $args['endTime'])
            {
            	return $this->error('开始时间不能大于结束时间', u('Comment/indexall'));
            }
		}

		$result = $this->requestApi('order.rate.orderlists', $args);
		if($result['code']==0){
			View::share('list',$result['data']['list']);	
		}
		return $this->display();
	}

	//全国店评价详情
	public function alldetail() {
		$args = Input::all();
		$result = $this->requestApi('order.rate.alldetail', $args);

		if($result['code']==0){
			View::share('list',$result['data']);	
		}
		return $this->display();
	}

	//全国店评价回复
	public function allreply() {
		$args = Input::all();
		$result = $this->requestApi('order.rate.allreply', $args);

		return Response::json($result);
	}

	/**
	 * 回复
	 */
	public function reply() {
		$args = Input::all();
		$result = $this->requestApi('order.rate.get', $args); 
		if($result['code']==0){
			View::share('data',$result['data']);			
		}   
		View::share('id',$args);
		return $this->display();
	}
	/**
	 * 提交回复
	 */
	public function ajaxreply() {
		$args = Input::all();		
		$result = $this->requestApi('order.rate.reply', $args);
		return Response::json($result);
	}
	
}
