<?php 
namespace YiZan\Services;

use YiZan\Models\SharechapmanLog;
use YiZan\Models\System\User;
use YiZan\Utils\Time;
use YiZan\Services\FxBaseService;
use YiZan\Utils\Encrypter;

use Exception, DB, Lang, Validator, App,Config;
use YiZan\Models\SellerMoneyLog;

class SharechapmanLogService extends BaseService
{
    
    /**
     *
     *总后台提现列表显示
     *sellerName 商户名称
     */
    public static function lists($name,$mobile, $status,$beginTime, $endTime,$page, $pageSize) {
        if($status == 0){
            $list = SharechapmanLog::with('user');
        }else{
            $status = $status-1;
            $list = SharechapmanLog::where('status', $status)->with('user');
        }

        if (!empty($name)) {//搜索商户
            $list->where('user_id',function($query) use ($name) {
                $query->select('id')
                    ->from('user')
                    ->where('name','like',"%".$name."%");
            });
        }

        if (!empty($mobile)) {//搜索会员
            $list->where('user_id',function($query) use ($mobile) {
                $query->select('id')
                    ->from('user')
                    ->where('mobile', $mobile);
            });
        }

        if ($beginTime > 0) {
            $list->where('create_time', '>=', $beginTime);
        }
        if ($endTime > 0) {
            $list->where('create_time', '<', $endTime);
        }
        $total_count = $list->count();
        $list->orderBy('sharechapman_log.id', 'desc');
    
        $list = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();        

		return ["list" => $list, "totalCount" => $total_count];
    }
   
	public static function dispose($adminId, $id, $content, $status) {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ''
		);
		$withdraw = SharechapmanLog::find($id);
		if (!$withdraw) {//提现不存在
			$result['code'] = 80801;
			return $result;
		}

        // 已处理
		if ($withdraw->status == STATUS_WITHDRAW_PASS)
        {
			$result['code'] = 80802;
			return $result;
		}

        // 成功
        if($status == STATUS_WITHDRAW_PASS)
        {

            //cz修改
            if(FANWEFX_SYSTEM){
                $user = User::find($withdraw->user_id);
                if(!$user){
                    $result['code'] = 80801;
                    return $result;
                }
                $args['username'] = $user->mobile;
                $args['password'] = $user->mingPwd;
                $args['nickname'] = $user->name;
                $args['photo'] = $user->avatar;
                $args['mobile'] = $user->mobile;
                //cz fanwe
                $encrypter = new Encrypter(md5(Config::get('app.fanwefx.appsys_id')));
                $pwd2 = $encrypter->decrypt($user->ming_pwd);
                $args['pwd']        = $pwd2;

                if($user->fanwe_recommend_user > 0){
                    $args['recommender_id'] = $user->fanwe_recommend_user;
                }
                $fan_result = FxBaseService::requestApi('register',$args);

                if($fan_result['errcode'] > 0){
                    $result['code'] = 80801;
                    return $result;
                }

                $args2['user_status'] = 1;
                $args2['user_id'] = $fan_result['user_id'];
                FxBaseService::requestApi('set_user_status', $args2);

                User::where('id',$withdraw->user_id)->update(['fanwe_id' => $fan_result['user_id']]);
            }else{
                $result['code'] = 80801;
                return $result;
            }

            $withdraw->status          = STATUS_WITHDRAW_PASS;
            $result['data'] = User::find($withdraw->user_id);
        }else{
            $withdraw->status          = STATUS_WITHDRAW_REFUSE;
        }

        $withdraw->dispose_admin   = $adminId;
        $withdraw->dispose_time    = UTC_TIME;
        $withdraw->dispose_remark  = $content;
        $withdraw->save();

		return $result;
	}
}
