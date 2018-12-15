<?php 
namespace YiZan\Services\System;

use YiZan\Models\System\SellerStaffWithdrawMoney;
use YiZan\Models\SellerStaffExtend;
use YiZan\Utils\Time;
use Exception, DB, Lang, Validator, App;
use YiZan\Models\SellerStaffMoneyLog;

class SellerStaffWithdrawMoneyService extends \YiZan\Services\SellerStaffWithdrawMoneyService {
    
    /**
     *
     *总后台提现列表显示
     *sellerName 商户名称
     */
    public static function lists($sellerStaffName, $status,$beginTime, $endTime, $page, $pageSize) {
        $list = SellerStaffWithdrawMoney::with('staff', 'extend', 'admin')->where('status', $status);
        if (!empty($sellerStaffName)) {//搜索商户
           $list->where('staff_id',function($query) use ($sellerStaffName) {
              $query->select('id')
                    ->from('seller_staff')
                    ->where('name',$sellerStaffName);
           });
        }
        if ($beginTime > 0) {
            $list->where('create_time', '>=', $beginTime);
        }
        if ($endTime > 0) {
            $list->where('create_time', '<', $endTime);
        }
        $total_count = $list->count();
        $list->orderBy('seller_staff_withdraw_money.id', 'desc');
    
        $list = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();        

		return ["list" => $list, "totalCount" => $total_count];
    }
    
	/**
	 * 获取提现列表
	 * @param  string  $sellerName   服务人员名称
	 * @param  string  $sellerMobile 服务人员手机号
	 * @param  string  $beginTime    创建开始时间
	 * @param  string  $endTime      创建结束时间
	 * @param  integer $status       处理状态
	 * @param  integer $page         页码
	 * @param  integer $pageSize     每页数量
	 * @return array                
	 */
	public static function getLists($sellerStaffName, $sellerStaffMobile, $beginTime, $endTime, $status, $page, $pageSize) {
		$list = SellerStaffWithdrawMoney::with('admin', 'seller', 'authenticate');
		if (!empty($sellerStaffName) || !empty($sellerStaffMobile)) {//搜索名称或手机号
			$list->join('seller_staff', function($join) use($sellerStaffName, $sellerStaffMobile) {
	            $join->on('seller_staff.id', '=', 'seller_staff_withdraw_money.staff_id');
	            if (!empty($sellerStaffName)) {
	            	$join->where('seller_staff.name', '=', $sellerStaffName);
	            }
	            if (!empty($sellerStaffMobile)) {
	            	$join->where('seller_staff.mobile', '=', $sellerStaffMobile);
	            }
	        });
		}

		if (!empty($beginTime)) {//创建开始时间
			$list->where('create_day', '>=', Time::toTime($beginTime));
		}

		if (!empty($endTime)) {//创建结束时间
			$list->where('create_day', '<=', Time::toTime($endTime));
		}

		if ($status > 0) {//状态
			$list->where('status', $status - 1);
		}
		$total_count = $list->count();
		$list->orderBy('seller_staff_withdraw_money.id', 'desc');
		
		$list = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();
		return ["list" => $list, "totalCount" => $total_count];
	}
   
	public static function dispose($adminId, $id, $content, $status) {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ''
		);
		// if (empty($content)) {//处理结果不能为空
		//     $result['code'] = 40102;
		//     return $result;
		// }
		$withdraw = SellerStaffWithdrawMoney::find($id);
		if (!$withdraw) {//提现不存在
			$result['code'] = 40101;
			return $result;
		}

        // 已处理
		if ($withdraw->status == STATUS_WITHDRAW_PASS ||
            $withdraw->status == STATUS_WITHDRAW_REFUSE) 
        {
			$result['code'] = 40103;
			return $result;
		}
		DB::beginTransaction();
        try {
           
            // 成功
            if($status == STATUS_WITHDRAW_PASS)
            {
                $withdraw->status  = STATUS_WITHDRAW_PASS;
                $extend = SellerStaffExtend::where("staff_id", $withdraw->staff_id)->first();
                $extend->frozen_money -= $withdraw->money;
                $extend->save();
            }
            // 1：未通过
            if($status == STATUS_WITHDRAW_REFUSE)
            {
                $withdraw->status          = STATUS_WITHDRAW_REFUSE;

                $extend = SellerStaffExtend::where("staff_id", $withdraw->staff_id)->first();
                $extend->withdraw_money += $withdraw->money;
                $extend->frozen_money -= $withdraw->money;
                $extend->save();
                
            }            
            $withdraw->dispose_admin   = $adminId;
            $withdraw->dispose_time    = UTC_TIME;
            $withdraw->dispose_remark  = $content;
            $withdraw->save();
            
            \YiZan\Models\SellerStaffMoneyLog::where('related_id',$withdraw->id)->update(['status' => $status]);

            DB::commit();
            $bln = true;
        }catch (Exception $e){
           // print_r($e);
            DB::rollback();
            $bln = false;
            $result['code'] = 40104;
        }
		return $result;
	}

	/**
     * 获取卖家提现消息
     * @return mixed
     */
    public static function getwithdrawmessage() {
        return SellerStaffWithdrawMoney::where('status', 0)->count();
    }
}
