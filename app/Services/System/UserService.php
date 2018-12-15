<?php namespace YiZan\Services\System;

use YiZan\Models\System\User;
use YiZan\Models\System\UserAddress;
use YiZan\Models\System\UserCollectGoods;
use YiZan\Models\System\UserCollectSeller;
use YiZan\Models\System\UserPayLog;
use YiZan\Models\UserCollect;
use YiZan\Models\Seller;
use YiZan\Models\UserIntegral;
use YiZan\Models\UserRefundLog;
use YiZan\Models\System\UserVerifyCode;
use YiZan\Models\System\Order;
use YiZan\Models\System\OrderPromotion;
use YiZan\Models\System\OrderRate;
use YiZan\Models\System\PromotionSn;
use YiZan\Services\FxBaseService;

use YiZan\Utils\String;
use YiZan\Utils\Encrypter;
use YiZan\Utils\Helper;
use DB, Validator, Lang, Config;

class UserService extends \YiZan\Services\UserService {
	/**
	 * 获取会员列表
	 * @param  string  $mobile   [description]
	 * @param  string  $name     [description]
	 * @param  integer $status   [description]
	 * @param  integer $page     [description]
	 * @param  integer $pageSize [description]
	 * @return [type]            [description]
	 */
	public static function getLists($mobile, $name, $status, $userType, $page, $pageSize) {
		$list = User::with('regProvince', 'regCity', 'loginProvince', 'loginCity', 'seller', 'staff','bank');
		
		if (!empty($name)) {//搜索名称
			$list->where('name' ,'like', '%'.$name.'%');
		}

		if (!empty($mobile)) {//搜索手机号
			if(strlen($mobile) == '11')
				$list->where('mobile',$mobile);
			else
				$list->where('mobile','like', '%'.$mobile.'%');
		}

		if ($status > 0) {//状态
			$list->where('status', $status - 1);
		}

        if ($userType > 0) {
            if ($userType == 1) {
                $list->whereIn('id', function($query) {
                    $query->select('user_id')->from('seller');
                })
                    ->whereIn('id', function($query) {
                        $query->select('user_id')->from('seller_staff');
                    });
            } else {
                $list->whereNotIn('id', function($query) {
                    $query->select('user_id')->from('seller');
                })
                    ->whereNotIn('id', function($query) {
                        $query->select('user_id')->from('seller_staff');
                    });
            }
        }
		$total_count = $list->count();
		$list->orderBy('id', 'desc');
		
		$list = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();
		return ["list" => $list, "totalCount" => $total_count];
	}

	/**
	 * 会员搜索
	 * @param  [type] $mobileName 手机或者名称
	 * @return [type]             [description]
	 */
	public static function searchUser($mobileName) {
		$list = User::select('id', 'name', 'mobile')->with(['address' => function($query) {
						$query->orderBy('is_default', 'desc')
							  ->orderBy('id', 'desc')
							  ->groupBy('user_id');
					}]);

		if (!empty($mobileName)) {
            $list->where(function($query) use ($mobileName){
                $query->where('mobile',$mobileName)
                    ->orWhere('name','like','%'.$mobileName.'%');
            });
		}

		return $list->orderBy('id', 'desc')->skip(0)->take(30)->get()->toArray();
	}

	public static function getById($id) {
		return User::with('regProvince', 'regCity', 'loginProvince', 'loginCity','bank')->find($id);
	}

	/**
	 * 更新会员状态
	 * @param  [type] $id     [description]
	 * @param  [type] $status [description]
	 * @return [type]         [description]
	 */
	public static function updateStatus($id, $status) {
		User::where('id', $id)->update(['status' => $status]);
	}

	public static function updateUser($id, $mobile, $name, $pwd, $avatar, $status,$fanweId) {
		$pwd = strval($pwd);

		$result = array(
			'code'	=> 0,
			'data'	=> $mobile,
			'msg'	=> ''
		);
     
		$rules = array(
		    'mobile' => ['required','regex:/^1[0-9]{10}$/','unique:user,mobile,'.$id],
		    'name' 	 => ['required'],
		    'pwd' 	 => ['sometimes','min:5','max:20']
		);

		$messages = array(
		    'mobile.required'	=> '20102',
		    'mobile.regex'		=> '20103',
		    'mobile.unique'		=> '20104',
		    'name.required' 	=> '20105',
		    'pwd.min' 			=> '20106',
		    'pwd.max' 			=> '20106',
		);

		$user = User::find($id);
		if (!$user) {//会员不存在
			$result['code'] = 20101;
			return $result;
		}

		$validator = Validator::make([
				'mobile' => $mobile,
				'name' 	 => $name,
				'pwd' 	 => $pwd
			], $rules, $messages);
		if ($validator->fails()) {//验证信息
	    	$messages = $validator->messages();
	    	$result['code'] = $messages->first();
	    	return $result;
	    }

        //cz修改
        if(FANWEFX_SYSTEM && !empty($fanweId)){
            $args_data['user_id'] = $fanweId;
            if($mobile != $user->mobile){
                $args_data['user_username'] = $mobile;
                $args_data['user_mobile'] = $mobile;
            }
            $args_data['user_nickname'] = $name;
            $args_data['user_photo'] = $avatar;
            if(!empty($pwd)){
                $args_data['user_password'] = $pwd;
            }
            $fan_result = FxBaseService::requestApi('modify_user',$args_data);
            if($fan_result['errcode'] > 0){
                $result['code'] = 20101;
                return $result;
            }
            //cz fanwe
            $encrypter = new Encrypter(md5(Config::get('app.fanwefx.appsys_id')));
            $pwd2 = $encrypter->encrypt($pwd);
            $user->mine_pwd 	    = $pwd2;
        }

	    //当有会员头像时
	    if (!empty($avatar)) {
	    	$avatar = self::moveUserImage($user->id, $avatar);
	    	if (!$avatar) {
	    		$result['code'] = 20107;
	    		return $result;
	    	} else {
	    		$user->avatar = $avatar;
	    	}
	    }

	    if (!empty($pwd)) {
	    	$user->pwd = md5(md5($pwd) . $user->crypt);
	    }
	    $user->mobile 			= $mobile;
	    $user->name 			= $name;
        $user->status 			= $status;
	    $user->save();
	    return $result;
	}

	public static function removeUser($ids) {
		if (!is_array($ids)) {
			$ids = (int)$ids;
			if ($ids < 1) {
				return false;
			}
			$ids = [$ids];
		}

        if(Seller::whereIn('user_id',$ids)->count() != 0){
            return false;
        }
		DB::beginTransaction();
		try {
			//删除会员
			User::whereIn('id', $ids)->delete();
			//删除会员地址表
			UserAddress::whereIn('user_id', $ids)->delete();
			//删除会员收藏
            UserCollect::whereIn('user_id', $ids)->delete();
			//删除会员支付日志
			//UserPayLog::whereIn('user_id', $ids)->delete();
			//删除会员退款处理
			//UserRefundLog::whereIn('user_id', $ids)->delete();
			//删除会员验证码表
			UserVerifyCode::whereIn('user_id', $ids)->delete();
			//删除订单
			//Order::whereIn('user_id', $ids)->delete();
			//删除订单优惠详细
			//OrderPromotion::whereIn('user_id', $ids)->delete();
			//删除订单评价
			//OrderRate::whereIn('user_id', $ids)->delete();
			//删除优惠发放表
			//PromotionSn::whereIn('user_id', $ids)->delete();
            DB::commit();
		} catch (Exception $e) {
    		DB::rollback();
    		return false;
    	}
	    return true;
	}

    /**
     * @param int $adminId 管理员编号
     * @param int $userId 会员编号
     * @param double $money 金额
     * @param int $type 类型 : 1 充值 2扣款
     * @param string $remark 备注
     * @return [array]
     */
    public static function updatebalance($adminId,$userId, $money, $type, $remark){
        $result = array(
            'code'	=> 0,
            'data'	=> '',
            'msg'	=> Lang::get('api_system.success.handle')
        );
        $checkUser = User::where('id', $userId)->first();
        //会员不存在
        if (!$checkUser) {
            $result['code'] = '20101';
            return $result;
        }
        //金额不对
        if (!preg_match('/^[0-9]+\.?[0-9]{0,2}$/',$money) || (double)$money  < 0.01) {
            $result['code'] = '20201';
            return $result;
        }
        //类型不正确
        if (!in_array($type, [1,2])) {
            $result['code'] = '40417';
            return $result;
        }

        //余额不足
        if ($type == 2 && $checkUser->balance < $money) {
            $result['code'] = '20202';
            return $result;
        }

        DB::beginTransaction();
        try {
            if ($type == 1) {
                $data = [
                    'balance'       => DB::raw("IFNULL(balance, 0) + " . $money),
                    'total_money' => DB::raw("IFNULL(total_money, 0) + " . $money)
                ];
            } else {
                $data = [
                    'balance'       => DB::raw("balance - " . $money),
                    'total_money' => DB::raw("total_money - " . $money)
                ];
            }
            User::where('id', $userId)->update($data);
            UserPayLog::insert([
                'payment_type' => $type == 1 ? 'systemRecharge' : 'systemDebit',
                'pay_type' => $type == 1 ? 4 : 5,
                'user_id' => $userId,
                'order_id' => 0,
                'activity_id' => 0,
                'seller_id' => 0,
                'money' => (double)$money,
                'balance' => $type == 1 ? $checkUser->balance + $money : $checkUser->balance - $money,
                'content' => $remark,
                'pay_time' => UTC_TIME,
                'pay_day' => UTC_DAY,
                'create_time' => UTC_TIME,
                'create_day' => UTC_DAY,
                'status' => 1,
                'admin_id' => $adminId,
                'sn' => Helper::getSn()
            ]);
            DB::commit();
        } catch (Exception $e) {
            $result['code'] = '99999';
            DB::rollback();
        }

        return $result;
    }

	/**
	 * 获取会员总数
	 * @return mixed
     */
	public static function count(){
		return User::count();
	}

    public function changeFanwe($userId,$fanweId){
//        DB::connection()->enableQueryLog();
//        User::where('id',$userId)->update(['fanwe_id'=>$fanweId]);
//        print_r(DB::getQueryLog());exit;

        return User::where('id',$userId)->update(['fanwe_id'=>$fanweId]);
    }

    public function paylog($userId,$beginTime,$endTime,$page,$pageSize,$nav){
        //DB::connection()->enableQueryLog();

        if($nav == 1){
            $list = UserPayLog::where('status',1)->where('user_id',$userId);
            if ($beginTime > 0) {
                $list->where('create_time', '>=', $beginTime);
            }
            if ($endTime > 0) {
                $list->where('create_time', '<', $endTime);
            }
            $total_count = $list->count();
            $list = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();
        }else{
            $list = UserIntegral::where('status',1)->where('user_id',$userId);
            if ($beginTime > 0) {
                $list->where('create_time', '>=', $beginTime);
            }
            if ($endTime > 0) {
                $list->where('create_time', '<', $endTime);
            }
            $total_count = $list->count();
            $list = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();
        }
        
        return ["list" => $list, "totalCount" => $total_count];
    }
}
