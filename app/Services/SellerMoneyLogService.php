<?php namespace YiZan\Services;

use YiZan\Models\SellerMoneyLog;
use YiZan\Models\UserPayLog;
use YiZan\Models\User;
use YiZan\Models\SellerExtend;
use YiZan\Utils\Helper;
use Exception;

class SellerMoneyLogService extends BaseService {

	/**
	 * 创建奖金日志
	 * @param  [type] $sellerId  [description]
	 * @param  [type] $type      [description]
	 * @param  [type] $relatedId [description]
	 * @param  [type] $money     [description]
	 * @param  [type] $content   [description]
	 * @return [type]            [description]
	 */
	public static function createLog($sellerId, $type, $relatedId, $money, $content,$status = 0) {
		$sellerMoneyLog = new SellerMoneyLog();
		$sellerMoneyLog->seller_id 	 = $sellerId;
		$sellerMoneyLog->type 		 = $type;
		$sellerMoneyLog->related_id  = $relatedId;
		$sellerMoneyLog->money 		 = $money;
		$sellerMoneyLog->balance 	 = (float)SellerExtend::where('seller_id', $sellerId)->pluck('money');
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

    public static function createUserLog($userId, $type, $money, $content,$status = 0,$withdrawId) {
        $userPayLog = new UserPayLog();
        $userPayLog->user_id 	 = $userId;
        $userPayLog->pay_type 	= $type; //提现
        $userPayLog->payment_type = "withdrawals"; //提现
        $userPayLog->money 		 = $money;
        $userPayLog->balance 	 = (float)User::where('id', $userId)->pluck('balance');
        $userPayLog->content 	 = $content;
        $userPayLog->create_time = UTC_TIME;
        $userPayLog->create_day  = UTC_DAY;
        $userPayLog->status = $status;
        $userPayLog->withdraw_id = $withdrawId ;
        do {
            try {
                $userPayLog->sn = Helper::getSn();
                $userPayLog->save();
                $bln = true;
            } catch (Exception $e) {
                print_r($e->getMessage());die;
            }
        } while(!$bln);
    }

    public static function createUserShareLog($userId, $type, $orderId,$money, $content,$status = 0) {
        $userPayLog = new UserPayLog();
        $userPayLog->user_id 	 = $userId;
        $userPayLog->pay_type 	= $type; //提现
        $userPayLog->payment_type = "share"; //提现
        $userPayLog->order_id  = $orderId;
        $userPayLog->money 		 = $money;
        $userPayLog->balance 	 = (float)User::where('id', $userId)->pluck('balance');
        $userPayLog->content 	 = $content;
        $userPayLog->create_time = UTC_TIME;
        $userPayLog->create_day  = UTC_DAY;
        $userPayLog->status = $status;
        do {
            try {
                $userPayLog->sn = Helper::getSn();
                $userPayLog->save();
                $bln = true;
            } catch (Exception $e) {
                print_r($e->getMessage());die;
            }
        } while(!$bln);
    }
}