<?php 
namespace YiZan\Services\Sellerweb;
use YiZan\Models\SellerExtend;
use YiZan\Models\UserVerifyCode;
use YiZan\Models\SellerWithdrawMoney;
use YiZan\Models\SellerMoneyLog;
use YiZan\Models\SystemConfig;
use YiZan\Models\Order;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Services\SystemConfigService;
use Exception, DB, Lang, Validator, App;
class UserAccountService extends \YiZan\Services\BaseService { 
	
	/**
	 * 服务人员的可提现金额
	 * @return [type] [description]
	 */
	public static function getAccount($sellerId){
        $data = [
            'money' => 0,
            'lockMoney' => 0,
            'waitConfirmMoney' => 0,
        ];
        $result = SellerExtend::where('seller_id',$sellerId)
            ->select(DB::Raw('(total_money - money - use_money) as lock_money'),'money','wait_confirm_money','money_cycle_day')
            ->first();
        $lockMoney = Order::where('seller_id', $sellerId)
            ->whereIn('status', [ORDER_STATUS_FINISH_SYSTEM, ORDER_STATUS_FINISH_USER])
            ->where('seller_withdraw_time', '>', 0)
            ->where('seller_withdraw_time', '>', UTC_TIME)
			->where('pay_type', '<>', 'cashOnDelivery')
            ->sum('seller_fee');
        $waitWithdrawMoney = SellerWithdrawMoney::where('seller_id', $sellerId)->where('status', 0)->sum('money');//提现冻结金额
        if ($result) {
            $data['money'] = $result->money;
            $data['waitConfirmMoney'] = $result->wait_confirm_money;
        }
        if($data['money'] >= 100){
            $data['moneyCycle'] = $result->money;
        }else{
            $data['moneyCycle'] = 0;
        }
        $lockCycl = false;

        if($data['moneyCycle'] >= 100){
            //验证服务人员银行卡信息
            $bankinfo = BankInfoService::getBankInfo($sellerId,false);
            if($bankinfo){
                if($result->money_cycle_day != "" || $result->money_cycle_day > 1){
                    if($result->money_cycle_day <= UTC_DAY && $data['moneyCycle'] >= 100){
                        $lockCycl = true;
                    }
                }else{
                    $lockCycl = true;
                }
            }
        }

        $data['lockCycl'] = $lockCycl;
        $data['moneyCycleDay'] = Time::toDate( $result->money_cycle_day ? $result->money_cycle_day : UTC_DAY,"Y-m-d");
        $data['lockMoney'] = $lockMoney + $waitWithdrawMoney;
        return $data;
	} 
	
	/**
	 * 服务人员提款申请
	 * @return [type] [description]
	 */
	public static function  createWithdraw($sellerId,$id,$money,$mobile,$verifyCode){

		//验证服务人员银行卡信息
		$bankinfo = BankInfoService::getBankInfo($sellerId,$id);

		if(empty($bankinfo) || $bankinfo['bank'] == ''|| $bankinfo['bankNo'] == ''){
 			$result['code'] = 10154;
 			return $result;
		}
		//验证服务人员余额是否足够本次提现
		$current_money = self::getAccount($sellerId);

        if ($money < 100) {
            $result['code'] = 11153;
            return $result;
        }

		if ($current_money['money'] < $money) {
 			$result['code'] = 10153;
 			return $result;
		}

		$data = array( 
			'money' 	=> $money, 
			'mobile'	=> $mobile,
			'code'		=> $verifyCode
		);

		$result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
		); 

		$messages = array( 
		    'mobile.required'		=> 10101,
		    'mobile.regex'			=> 10102,
		    'code.required' 		=> 10103,
		    'code.size' 			=> 10104,
        );

		$rules = array(  
			'money'			=> ['required'],
		    'mobile' 	 	=> ['required','regex:/^1[0-9]{10}$/'],
		    'code' 	 		=> ['required','size:6'],
		); 

		$validator = Validator::make($data, $rules, $messages);
		if ($validator->fails()) {//验证信息
	    	$messages = $validator->messages();
	    	$result['code'] = $messages->first();
	    	return $result;
	    } 

	    //检测验证码
	    $verifyCodeId = UserService::checkVerifyCode($verifyCode, $mobile, UserVerifyCode::TYPE_WITHDRAW);
	    if (!$verifyCodeId) {
	    	$result['code'] = 10104;
	    	return $result;
	    }

        $withdraw = new SellerWithdrawMoney();

        $withdraw->sn 			=	Helper::getSn();
        $withdraw->seller_id	=	$sellerId;
        $withdraw->money 		=	$money;
        $withdraw->name 		=	$bankinfo['name'];
        $withdraw->bank 		=	$bankinfo['bank'];
        $withdraw->bank_no 		=	$bankinfo['bankNo'];
        $withdraw->content 		=	"提现申请";
        $withdraw->create_time 	=	Time::getTime();
        $withdraw->create_day 	=	Time::getNowDay();
        $keyinfo  = SystemConfig::where('code', 'money_cycle_day')->first();

	    DB::beginTransaction();	
	    //插入取款表
		$withdraw_status = $withdraw->save();  
		//修改商家可提现金额
    	$extend_status = SellerExtend::where('seller_id', $sellerId)->update([
            'money' => $current_money['money'] - $money,
            'money_cycle_day' => UTC_DAY + 24 * 3600 * ($keyinfo->val + 1)
        ]);
    	//插入资金流水表
    	\YiZan\Services\SellerMoneyLogService::createLog($sellerId,SellerMoneyLog::TYPE_APPLY_WITHDRAW,$withdraw->id,$money,'提款银行：'.$withdraw->bank.',提款帐号：'.$withdraw->bank_no);
 		
	    if($withdraw_status && $extend_status ){
    		UserVerifyCode::destroy($verifyCodeId);
	    	DB::commit();
	    } else {
    		DB::rollback();
 			$result['code'] = 10155;
 			return $result;
	    }

    	return $result; 
	}

	/**
	 * 服务人员提款列表
	 * @return [type] [description]
	 */
	public static function logLists($seller,$beginDate,$endDate,$status,$page,$pageSize){
	
		if(empty($beginDate)){
			$beginTime = 0;
		} else {
			$beginTime = Time::toTime($beginDate);
		}

		if(empty($endDate)){
			$endTime = UTC_DAY;
		} else {
			$endTime = Time::toTime($endDate);
		}		   
		
		$queries = SellerMoneyLog::where('seller_id',$seller->id)->orderBy('id', 'desc');

        if($status > 0){
            if($status == 1){//查询类型为收入的数据
                //如果为物业公司
                if($seller->type == 3){
                    $queries->whereRaw("type in ('property_fee')") ;
                } else {
                    $queries->whereRaw("(type in ('order_confirm', 'seller_recharge', 'system_recharge', 'invitation_back', 'withdraw_error') OR (type = 'delivery_money' and money > 0))") ;
                }
            } else {//查询类型为支出的数据
                $queries->whereRaw("(type in ('apply_withdraw', 'system_debit', 'send_fee') OR (type = 'delivery_money' and money < 0))") ;
            }
        } else {
            //如果为物业公司
            if($seller->type == 3){
                $queries->whereRaw("(type in ('property_fee') OR (type = 'delivery_money' and money < 0) OR (type in ('apply_withdraw', 'system_debit')) )");
            } else {
                $queries->whereRaw("(type in ('order_confirm', 'seller_recharge', 'system_recharge', 'invitation_back', 'withdraw_error', 'send_fee') OR (type = 'delivery_money' and money > 0) OR (type = 'delivery_money' and money < 0) OR (type in ('apply_withdraw', 'system_debit')) )");
            }
        }
		$queries->whereBetween('create_day',[$beginTime,$endTime]);
		$result['totalCount'] = $queries->count();
		$result['list'] = $queries->skip(($page - 1) * $pageSize)
            					  ->take($pageSize)
            					  ->get()
            					  ->toArray();
		return $result;
	}

}
