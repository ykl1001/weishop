<?php namespace YiZan\Services\System;

use YiZan\Models\System\UserPayLog;
use YiZan\Models\Order;
use YiZan\Models\User;
use YiZan\Utils\Time;
use DB,Exception;
class UserPayLogService extends \YiZan\Services\UserPayLogService {
	/**
	 * 获取提现列表
     * @param  string  $name       会员名称
     * @param  string  $mobile     会员手机号
     * @param  string  $orderSn    订单编号
     * @param  string  $paySn      流水号
	 * @param  string  $beginTime  创建开始时间
	 * @param  string  $endTime    创建结束时间
	 * @param  string  $payment    支付方式
	 * @param  string  $payType    支付类型
	 * @param  integer $page       页码
     * @param  integer $pageSize   每页数量
     * @param  integer $isTotal    是否取全部数据
	 * @return array                
	 */
	public static function getLists($name, $mobile, $orderSn, $paySn, $beginTime, $endTime, $payment, $payStatus, $payType, $page, $pageSize, $isTotal) {
		$list = UserPayLog::with('user', 'seller', 'order.goods');

		if (!empty($name) || !empty($mobile)) {//搜索名称或手机号
			$list->join('user', function($join) use($name, $mobile) {
	            $join->on('user.id', '=', 'user_pay_log.user_id');
	            if (!empty($name)) {
	            	$join->where('user.name', '=', $name);
	            }
	            if (!empty($mobile)) {
	            	$join->where('user.mobile', '=', $mobile);
	            }
	        });
		}

        if($orderSn == true){
            $orderId = Order::where('sn', $orderSn)->pluck('id');
            $list->where('user_pay_log.order_id', $orderId);
        }

        if($paySn == true){
            $list->where('user_pay_log.sn', $paySn);
        }

		if (!empty($payment)) {//支付方式
			$list->where('user_pay_log.payment_type', $payment);
		}

        if($payStatus > 0){
            $list->where('user_pay_log.status', $payStatus - 1);
        }

        if($payType > 0){
            $list->where('user_pay_log.pay_type', $payType);
        }

		if (!empty($beginTime)) {//创建开始时间
			$list->where('user_pay_log.create_day', '>=', Time::toTime($beginTime));
		}

		if (!empty($endTime)) {//创建结束时间
			$list->where('user_pay_log.create_day', '<=', Time::toTime($endTime));
		}

		$list->orderBy('user_pay_log.id', 'desc');

		if($isTotal){
            $list = $list->get()->toArray();
            return $list;
        } else {
            $total_count = $list->count();
            $list = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();
            return ["list" => $list, "totalCount" => $total_count];
        }
	}

    public static function dispose($adminId, $id, $content, $status) {
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> ''
        );
        $withdraw = \YiZan\Models\SellerWithdrawMoney::find($id);
        if (!$withdraw) {//提现不存在
            $result['code'] = 40101;
            return $result;
        }

        // 已处理
        if ($withdraw->status == STATUS_WITHDRAW_PASS || $withdraw->status == STATUS_WITHDRAW_REFUSE)
        {
            $result['code'] = 40103;
            return $result;
        }
        DB::beginTransaction();
        try {
            $extend = User::where("id", $withdraw->user_id)->first();
            // 成功
            if($status == STATUS_WITHDRAW_PASS)
            {
                $withdraw->status  = STATUS_WITHDRAW_PASS;
            }
            // 1：未通过
            if($status == STATUS_WITHDRAW_REFUSE)
            {
                $withdraw->status          = STATUS_WITHDRAW_REFUSE;
                $extend->balance += $withdraw->money;
                $extend->save();
                \YiZan\Services\SellerMoneyLogService::createUserLog($withdraw->user_id,10, $withdraw->money, "提现失败退回账户",1);
            }
            $withdraw->dispose_admin   = $adminId;
            $withdraw->dispose_time    = UTC_TIME;
            $withdraw->dispose_remark  = $content;
            $withdraw->save();
            DB::commit();
        }catch (Exception $e){
            DB::rollback();
            $result['code'] = 40104;
        }
        return $result;
    }
}
