<?php namespace YiZan\Services;

use YiZan\Models\SellerStaffMoneyLog;
use YiZan\Models\SellerStaffExtend;
use YiZan\Utils\Helper;
use Exception;

class SellerStaffMoneyLogService extends BaseService {
	/**
	 * 创建奖金日志
	 * @param  [type] $sellerId  [description]
	 * @param  [type] $type      [description]
	 * @param  [type] $relatedId [description]
	 * @param  [type] $money     [description]
	 * @param  [type] $content   [description]
	 * @return [type]            [description]
	 */
	public static function createLog($staffId, $type, $relatedId, $money, $content,$status = 0) {
		$sellerMoneyLog = new SellerStaffMoneyLog();
		$sellerMoneyLog->staff_id 	 = $staffId;
		$sellerMoneyLog->type 		 = $type;
		$sellerMoneyLog->related_id  = $relatedId;
		$sellerMoneyLog->money 		 = $money;
		$sellerMoneyLog->balance 	 = (float)SellerStaffExtend::where('staff_id', $staffId)->pluck('withdraw_money');
		$sellerMoneyLog->content 	 = $content;
		$sellerMoneyLog->create_time = UTC_TIME;
		$sellerMoneyLog->create_day  = UTC_DAY;
        $sellerMoneyLog->status = $status;
		do {
	    	try {
	    		$sellerMoneyLog->sn = Helper::getSn();
	    		$sellerMoneyLog->save();
	    		$bln = true;
	    	} catch (Exception $e) {
	    		$bln = false;
	    	}
	    } while(!$bln);
	}
}