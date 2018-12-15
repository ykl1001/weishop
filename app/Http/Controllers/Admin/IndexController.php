<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\AdminUser;
use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Form, Config,Cache;

/**
 * 后台首页
 */
class IndexController extends AuthController {
	/**
	 * 服务器信息
	 */
	public function index() {


		$type = (int)Input::get('type') > 0 ? Input::get('type') : 1;
		$total = $this->requestApi('totalview.total');
		$data = $this->requestApi('order.ordercount.total',array('type'=>$type));
		if($total['code'] == 0)
			View::share('total', $total['data']);
		if($data['code'] == 0)
			View::share('data', $data['data']);

		/*-----------网站统计----------*/
		/*商城*/
		$storeInfo = Cache::remember('admin_index_index_storeinfo',1,function(){
			$storeInfo = ['goodsTotal' => 0,'ordersDay' => 0,'salesAmountDay' => 0,'ordersMonth' => 0 ,'salesAmountMonth' => 0];
			$store_info = $this->requestApi('system.dashboard.storeinfo');
			if($store_info['code'] == 0){
				$storeInfo['goodsTotal'] = $store_info['data']['goodsTotal'];
				$storeInfo['ordersDay'] = $store_info['data']['ordersDay'];
				$storeInfo['salesAmountDay'] = $store_info['data']['salesAmountDay'];
				$storeInfo['ordersMonth'] = $store_info['data']['ordersMonth'];
				$storeInfo['salesAmountMonth'] = $store_info['data']['salesAmountMonth'];
			}
			return $storeInfo;
		});


		View::share('storeInfo', $storeInfo);
		/*加盟*/
		$sellerInfo = Cache::remember('admin_index_index_sellerinfo',1,function(){
			$sellerInfo = ['sellerTotal' => 0,'propertyTotal' => 0,'ordersDay' => 0,'salesAmountDay' => 0,'ordersMonth' => 0 ,'salesAmountMonth' => 0];
			$seller_info = $this->requestApi('system.dashboard.sellerinfo');
			if($seller_info['code'] == 0){
				$sellerInfo['sellerTotal'] = $seller_info['data']['sellerTotal'];
				$sellerInfo['propertyTotal'] = $seller_info['data']['propertyTotal'];
				$sellerInfo['ordersDay'] = $seller_info['data']['ordersDay'];
				$sellerInfo['salesAmountDay'] = $seller_info['data']['salesAmountDay'];
				$sellerInfo['ordersMonth'] = $seller_info['data']['ordersMonth'];
				$sellerInfo['salesAmountMonth'] = $seller_info['data']['salesAmountMonth'];
			}
			return $sellerInfo;
		});


		View::share('sellerInfo', $sellerInfo);
		/*网站*/
		$userInfo = Cache::remember('admin_index_index_userinfo',1,function(){
			$userInfo = ['userTotal' => 0,'districTotal' => 0];
			$user_info = $this->requestApi('system.dashboard.sysinfo');
			if($user_info['code'] == 0){
				$userInfo['userTotal'] = $user_info['data']['userTotal'];
				$userInfo['districTotal'] = $user_info['data']['districTotal'];
			}
			return $userInfo;
		});

		View::share('userInfo', $userInfo);
		/*账户信息*/
		$accountInfo = [
				'account' => $this->adminUser['name'],
				'roleName' => $this->adminUser['role']['name'],
				'loginTime' => $this->adminUser['loginTime'] + date('Z'),
				'loginIp' => $this->adminUser['loginIp'],
				'loginCount' => $this->adminUser['loginCount'],
		];


		/*系统信息*/
		$sysVersion = Config::get('app.sys_version','v1.9');
		View::share('accountInfo', $accountInfo);
		View::share('sysVersion', $sysVersion);

		return $this->display();
	}

	public function test(){
		$goodsNames = [
			'elevit 爱乐维',
			'NORDIC NATURALS 挪威小鱼',
			'ŴaKODO 和光堂',
			'EQUAZEN',
		];

		$rand = rand(0,count($goodsNames) - 1);

		$currentName = $goodsNames[$rand];
		
		echo $currentName;
	}

	/**
	 * 上传
	 */
	public function upload() {
		return $this->display();
	}
	/**
	 * 重新设置密码
	 */
	public function repwd(){
		return $this->display('index','demo');
	}

	public function demo() {
		echo Time::toDate(1447660800);exit;
		return $this->display('index','demo');
	}


}
