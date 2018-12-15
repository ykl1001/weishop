<?php
namespace YiZan\Services;

use YiZan\Models\InvitationBackLog;
use YiZan\Models\UserPayLog;
use YiZan\Models\User;
use DB;
/**
 * 赚钱
 */
class MakeService extends BaseService {

    /**
     * @param $name 赚钱
     * @param $page
     * */

    public function money($userId)
    {

        if(!$userId){
            return null;
        }
        $data = [];
        $prefix = DB::getTablePrefix();
        $sql = "SELECT tmp.id,tmp.level
					FROM (
						SELECT
							1 as level,
							U.id
							FROM {$prefix}user as U
							WHERE U.invitation_id = {$userId}
							AND U.invitation_type = 'user'
						UNION
						SELECT
						
							2 as level,
							U1.id
							FROM {$prefix}user as U
							INNER JOIN {$prefix}user as U1
							ON U.invitation_id = {$userId}
							AND U.id = U1.invitation_id
							AND U.invitation_type = 'user'
						UNION
						SELECT
						
							3 as level,
							U2.id
							FROM {$prefix}user as U
							INNER JOIN {$prefix}user as U1
							ON U.invitation_id = {$userId}
							AND U.id = U1.invitation_id
							AND U.invitation_type = 'user'
							INNER JOIN {$prefix}user as U2
							ON U1.id = U2.invitation_id
							AND U1.invitation_type = 'user'
						) AS tmp";
        $level = DB::select($sql);
        $ids = [];
        $leveldata = [];
        foreach($level as $v){
            $leveldata[$v->id] = $v->level;
            $ids[] = $v->id;
        }


        $data['returnFee'] = UserPayLog::where("user_id",$userId)->where("pay_type",7)->where("status",1)->where("create_day",UTC_DAY)->sum("money");

        $orderModel = InvitationBackLog::join('order', function($join){
            $join->on('invitation_back_log.order_id', '=', 'order.id');
        })->where("invitation_back_log.invitation_id",'=',$userId)->where('invitation_back_log.invitation_type', '=', 'user')->where("invitation_back_log.status",1);

        $status1 = clone $orderModel;
        $status1 = $status1->whereIn('invitation_back_log.user_id', $ids)->count();
        $status2 = clone $orderModel;
        $status2 = $status2->whereNotIn("invitation_back_log.user_id",$ids)->where("invitation_back_log.share_user_id",'=',$userId)->count();

        $data['orderCount']  = $status1 + $status2;


        $orderMoney1 = clone $orderModel;
        $orderMoney1 = $orderMoney1->whereIn('invitation_back_log.user_id', $ids)->sum("order.pay_fee");


        $orderMoney2 = clone $orderModel;
        $orderMoney2 = $orderMoney2->whereNotIn("invitation_back_log.user_id",$ids)->where("invitation_back_log.share_user_id",'=',$userId)->count();

        $data['orderMoney']  = $orderMoney1 + $orderMoney2;

        $data['distributionMoney'] = UserPayLog::where("user_id",$userId)->where("pay_type",7)->where("status",1)->sum("money");

        $waitMoney1 = InvitationBackLog::whereIn('user_id', $ids)->where('invitation_id', $userId)->where("invitation_type",'user')->where("status",0)->where("is_refund",0)->sum("return_fee");

        $waitMoney2 = InvitationBackLog::whereNotIn('user_id', $ids)->where('invitation_id', $userId)->where("invitation_type",'user')->where("status",0)->where("is_refund",0)->sum("return_fee");

        $data['waitMoney'] =  $waitMoney1 + $waitMoney2;

        $prefix = DB::getTablePrefix();
        $sql = "SELECT count(tmp.id) as count
                    FROM (
                        SELECT
                            1 as level,
                            U.id,
                            U.name
                            FROM {$prefix}user as U
                            WHERE U.invitation_id = {$userId}
                        UNION
                        SELECT
                            2 as level,
                            U1.id,
                            U1.name FROM {$prefix}user as U
                            INNER JOIN {$prefix}user as U1
                            ON U.invitation_id = {$userId}
                            AND U.id = U1.invitation_id
                        UNION
                        SELECT
                            3 as level,
                            U2.id,
                            U2.name FROM {$prefix}user as U
                            INNER JOIN {$prefix}user as U1
                            ON U.invitation_id = {$userId}
                            AND U.id = U1.invitation_id
                            INNER JOIN {$prefix}user as U2
                            ON U1.id = U2.invitation_id
                        ) AS tmp";
        $level = DB::select($sql);
        $data['userCount'] = $level[0]->count;
        return $data;
    }


    /**
     * @param $name 赚钱
     * @param $page
     * */

    public function order($userId ,$mUserId,$status = 0, $page = 1)
    {

        if(!$userId){
            return null;
        }
	if($mUserId){
            $userId = $mUserId;
	}
        $page = $page ? $page : 1;
        $pageSize = 20;
        $prefix = DB::getTablePrefix();
        $sql = "SELECT tmp.id,tmp.level
				FROM (
					SELECT
						1 as level,
						U.id
						FROM {$prefix}user as U
						WHERE U.invitation_id = {$userId}
						AND U.invitation_type = 'user'
					UNION
					SELECT
					
						2 as level,
						U1.id
						FROM {$prefix}user as U
						INNER JOIN {$prefix}user as U1
						ON U.invitation_id = {$userId}
						AND U.id = U1.invitation_id
						AND U.invitation_type = 'user'
					UNION
					SELECT
					
						3 as level,
						U2.id
						FROM {$prefix}user as U
						INNER JOIN {$prefix}user as U1
						ON U.invitation_id = {$userId}
						AND U.id = U1.invitation_id
						AND U.invitation_type = 'user'
						INNER JOIN {$prefix}user as U2
						ON U1.id = U2.invitation_id
						AND U1.invitation_type = 'user'
					) AS tmp  LIMIT ".($page - 1) * $pageSize.", ".$pageSize."";
        $level = DB::select($sql);
        $ids = [];
        $leveldata = [];
        foreach($level as $v){
            $leveldata[$v->id] = $v->level;
            $ids[] = $v->id;
        }
        if($status){
            if($ids){
                $list = InvitationBackLog::join('order', function($join){
                    $join->on('invitation_back_log.order_id', '=', 'order.id');
                })
                    ->where('invitation_back_log.invitation_type', '=', 'user')
                    ->whereIn('invitation_back_log.user_id', $ids);
            }else{
                return null;
            }

        }else{
            $list = InvitationBackLog::whereNotIn("invitation_back_log.user_id",$ids)->join('order', function($join){
                $join->on('invitation_back_log.order_id', '=', 'order.id');
            })
                ->where("invitation_back_log.share_user_id",'=',$userId);
        }

        $list = $list
            ->where("invitation_back_log.invitation_id",'=',$userId)
            ->where('invitation_back_log.invitation_type', '=', 'user')
            ->with("user")->select("invitation_back_log.*","order.sn as orderSn","order.create_time as orderCreateTime")
            ->orderBy("invitation_back_log.create_time",'DESC')
            ->skip(($page - 1) * 20)
            ->take(20)
            ->get()
            ->toArray();
        return $list;
    }
    public function detail($userId,$mUserId,$id)
    {

        if(!$userId){
            return null;
        }
        if(!$mUserId){
            $userId = $mUserId;
        }
        /*with([
            'goods' => function($query) use($id) {
                $query->where('order_id', $id);
            }])->*/

        $data = InvitationBackLog::join('order', function($join) use($userId,$id){
            $join->on('invitation_back_log.order_id', '=', 'order.id')
                ->where('order.id', '=', $id)
                ->where('invitation_back_log.invitation_type', '=', 'user')
                ->where('invitation_back_log.invitation_id', '=', $userId);
        })
            ->join('order_goods', 'order_goods.order_id', '=', 'invitation_back_log.order_id')
            ->join('user', 'user.id', '=', 'invitation_back_log.user_id')
            ->select(
                "order_goods.*",
                "order.province as userProvince",
                "order.city as userCity",
                "order.area as userArea",
                "order.address as userAddress",
                "order.name as userName",
                "order.mobile as userMobile",
                "order.sn as orderSn",
                "order.create_time as orderCreateTime",
                "order.total_fee as orderTotalFee",
                "invitation_back_log.return_fee as reFee",
                "invitation_back_log.status as orderStatus",
                "invitation_back_log.is_refund as orderIsRefund",
                "user.name as uName",
                "user.mobile as uMobile"
            )->first();
//        print_r($data->toArray());
//        die;
        return $data;
    }


}
