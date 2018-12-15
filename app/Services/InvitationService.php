<?php namespace YiZan\Services;

use YiZan\Models\Invitation;
use YiZan\Models\Seller;
use YiZan\Models\User;
use YiZan\Models\InvitationBackLog;
use YiZan\Models\System\Order;

use YiZan\Utils\String;
use YiZan\Utils\Time;
use Illuminate\Database\Query\Expression;

use DB, Lang, Exception, Validator;

class InvitationService extends BaseService {
    /*
    * 邀请返现
    */
    public static function getById(){
        $data = Invitation::first();

        if(!$data)
        {
            $data = Invitation::insert([
                'user_status' => 0,
                'user_percent' => 0.0,
                'seller_status' => 0,
                'seller_percent' => 0.0,
                'full_money' => 0,
            ]);
            $data = Invitation::first();
        }

        return $data;
    }

    /**
     * [save 保存分享返现设置]
     * @param  [type] $id            [description]
     * @param  [type] $userStatus    [description]
     * @param  [type] $userPercent   [description]
     * @param  [type] $userPercentSecond   [description]
     * @param  [type] $userPercentThird   [description]
     * @param  [type] $sellerStatus  [description]
     * @param  [type] $sellerPercent [description]
     * @param  [type] $sellerPercentSecond [description]
     * @param  [type] $sellerPercentThird [description]
     * @param  [type] $fullMoney     [description]
     * @param  [type] $shareTitle    [description]
     * @param  [type] $shareContent  [description]
     * @param  [type] $shareLogo     [description]
     * @param  [type] $shareExplain  [description]
     * @return [type]                [description]
     */
    public static function save(
        $id, $userStatus, $userPercent, $userPercentSecond, $userPercentThird, $sellerStatus, $sellerPercent,
        $sellerPercentSecond, $sellerPercentThird, $fullMoney, $shareTitle, $shareContent, $shareLogo, $inviteLogo,
        $shareExplain,$shareDescribe,$pointsNoExplain,$isAllUserPrimary,$isAllUserPercent,$isAllUserPercentSecond,$isAllUserPercentThird,$purchaseAgreement,$privilegeDetails,$protocolFee
    ){
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => Lang::get('api_system.success.update_info'),
        );

        $rules = array(
            'userStatus'            => ['required'],
            'userPercent'           => ['required'],
            'userPercentSecond'     => ['required'],
            'userPercentThird'      => ['required'],

            'isAllUserPrimary'           => ['required'],
            'isAllUserPercent'           => ['required'],
            'isAllUserPercentSecond'     => ['required'],
            'isAllUserPercentThird'      => ['required'],

            'sellerStatus'          => ['required'],
            'sellerPercent'         => ['required'],
            'sellerPercentSecond'   => ['required'],
            'sellerPercentThird'    => ['required'],
            'fullMoney'             => ['required'],
            'shareTitle'            => ['required'],
            'shareContent'          => ['required'],
            'shareLogo'             => ['required'],
            'inviteLogo'            => ['required'],
            'shareExplain'          => ['required'],

            'purchaseAgreement'    => ['required'],
            'privilegeDetails'     => ['required']
        );
        $messages = array
        (
            'userStatus.required'           => 35000,   // 会员邀请返现状态错误
            'sellerStatus.required'         => 35002,   // 商家邀请返现状态错误
            'userPercent.required'          => 35001,   // 会员邀请返现比率不能为空
            'sellerPercent.required'        => 35003,   // 商家邀请返现比率不能为空
            'fullMoney.required'            => 35004,   // 消费金额满X元不能为空
            'shareTitle.required'           => 35005,   // 分享链接标题不能为空
            'shareContent.required'         => 35006,   // 分享链接内容不能为空
            'shareLogo.required'            => 35007,   // 分享图标不能为空
            'inviteLogo.required'           => 35016,   // 邀请分享图片不能为空
            'shareExplain.required'         => 35008,   // 活动说明不能为空
            'userPercentSecond.required'    => 35011,   // 会员邀请返现比率不能为空
            'userPercentThird.required'     => 35012,   // 会员邀请返现比率不能为空
            'sellerPercentSecond.required'  => 35013,   // 商家邀请返现比率不能为空
            'sellerPercentThird.required'   => 35014,   // 商家邀请返现比率不能为空

            'isAllUserPrimary.required'              => 35012,
            'isAllUserPercent.required'              => 35012,
            'isAllUserPercentSecond.required'        => 35012,
            'isAllUserPercentThird.required'         => 35012,


            'purchaseAgreement.required'             => 35020,
            'privilegeDetails.required'              => 35021,

        );

        $validator = Validator::make(
            [
                'userStatus'              => $userStatus,
                'userPercent'             => $userPercent,
                'userPercentSecond'       => $userPercentSecond,
                'userPercentThird'        => $userPercentThird,
                'sellerStatus'            => $sellerStatus,
                'sellerPercent'           => $sellerPercent,
                'sellerPercentSecond'     => $sellerPercentSecond,
                'sellerPercentThird'      => $sellerPercentThird,
                'fullMoney'               => $fullMoney,
                'shareTitle'              => $shareTitle,
                'shareContent'            => $shareContent,
                'shareLogo'               => $shareLogo,
                'inviteLogo'              => $inviteLogo,
                'shareExplain'            => $shareExplain,
                'isAllUserPrimary'        => $isAllUserPrimary,
                'isAllUserPercent'        => $isAllUserPercent,
                'isAllUserPercentSecond'  => $isAllUserPercentSecond,
                'isAllUserPercentThird'   => $isAllUserPercentThird,
                'purchaseAgreement'  => $purchaseAgreement,
                'privilegeDetails'   => $privilegeDetails
            ], $rules, $messages
        );

        //验证信息
        if ($validator->fails())
        {
            $messages = $validator->messages();

            $result['code'] = $messages->first();

            return $result;
        }

        if(!in_array($userStatus,[0,1,2])){
            $result['code'] = 35009;    // 状态不合法
            return $result;
        }
        if($userStatus == 2 && $protocolFee < 0){
            $result['code'] = 35022;    // 状态不合法
            return $result;
        }

        if(!in_array($sellerStatus,[0,1])){
            $result['code'] = 35009;    // 状态不合法
            return $result;
        }

        if($userPercent < 0 || $userPercent > 100){
            $result['code'] = 35010;    // 返现比率超出范围（0-100）
            return $result;
        }

        if($userPercentSecond < 0 || $userPercentSecond > 100){
            $result['code'] = 35010;    // 返现比率超出范围（0-100）
            return $result;
        }

        if($userPercentThird < 0 || $userPercentThird > 100){
            $result['code'] = 35010;    // 返现比率超出范围（0-100）
            return $result;
        }

        if($sellerPercent < 0 || $sellerPercent > 100){
            $result['code'] = 35010;    // 返现比率超出范围（0-100）
            return $result;
        }

        if($sellerPercentSecond < 0 || $sellerPercentSecond > 100){
            $result['code'] = 35010;    // 返现比率超出范围（0-100）
            return $result;
        }

        if($sellerPercentThird < 0 || $sellerPercentThird > 100){
            $result['code'] = 35010;    // 返现比率超出范围（0-100）
            return $result;
        }

        if($id > 0) {
            $Invitation = Invitation::find($id);
        }
        else {
            $Invitation = new Invitation;
        }

        $Invitation->user_status           = $userStatus;
        $Invitation->user_percent_second   = $userPercentSecond;
        $Invitation->user_percent_third    = $userPercentThird;
        $Invitation->user_percent          = $userPercent;
        $Invitation->seller_status         = $sellerStatus;
        $Invitation->seller_percent        = $sellerPercent;
        $Invitation->seller_percent_second = $sellerPercentSecond;
        $Invitation->seller_percent_third  = $sellerPercentThird;
        $Invitation->full_money            = $fullMoney;
        $Invitation->share_title           = $shareTitle;
        $Invitation->share_content         = $shareContent;
        $Invitation->share_logo            = $shareLogo;
        $Invitation->invite_logo           = $inviteLogo;
        $Invitation->share_explain         = $shareExplain;
        $Invitation->share_describe        = $shareDescribe;
        $Invitation->points_no_explain     =  $pointsNoExplain;

        $Invitation->is_all_user_primary        = $isAllUserPrimary;
        $Invitation->is_all_user_percent        = $isAllUserPercent;
        $Invitation->is_all_user_percent_second  = $isAllUserPercentSecond;
        $Invitation->is_all_user_percent_third   = $isAllUserPercentThird;
        $Invitation->purchase_agreement  = $purchaseAgreement;
        $Invitation->privilege_details   = $privilegeDetails;
        $Invitation->protocol_fee   = $protocolFee;
        DB::beginTransaction();
        try{
            $Invitation->save();
            DB::commit();
        } catch(Exception $e){
            $result['code'] = 35015;
            //print_r($e->getMessage());die;
            DB::rollback();
        }

        return $result;

    }

    /**
     * [orderlist 分佣订单列表]
     * @param  [type] $sn             [订单编号]
     * @param  [type] $userName       [购买用户昵称]
     * @param  [type] $invitationName [推荐用户昵称]
     * @param  [type] $status    [订单状态-1全部,0未接单,1已接单]
     * @param  [type] $page           [分页]
     * @param  [type] $pageSize       [分页大小]
     * @return [type]                 [description]
     */
    public static function orderlist($sn, $userName, $invitationName, $status,$orderType, $page, $pageSize) {
        $prefix = DB::getTablePrefix();
        $list = Order::leftJoin('invitation_back_log', function($join){
            $join->on('order.id', '=', 'invitation_back_log.order_id');
        })
            ->where('order.is_invitation', 1);
        if($orderType == 1){
            $list->where('invitation_back_log.share_user_id', '!=',0 );
            $list->where('invitation_back_log.share_user_id', '!=',"invitation_back_log.invitation_id" );
        }
        if($userName == true){
            $buyerId = User::where('name', $userName)->pluck('id');
            $list->where('invitation_back_log.user_id', '=', (int)$buyerId);
        }
        if($invitationName == true){
            $invitorId = User::where('name', $invitationName)->pluck('id');
            $list->where('invitation_back_log.invitation_id', '=', (int)$invitorId);
        }
        if($status > 0){
            if($status == 1){
                //查询未完结的订单
                $list->whereRaw("(".$prefix."order.status NOT IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.",".ORDER_STATUS_USER_DELETE.",".ORDER_STATUS_SELLER_DELETE.",".ORDER_STATUS_ADMIN_DELETE.")
                    AND buyer_finish_time IS NULL AND cancel_time IS NULL)");
            } else {
                //查询已完结的订单
                $list->whereRaw("(".$prefix."order.status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.")
                    OR (".$prefix."order.status = ".ORDER_STATUS_USER_DELETE." AND buyer_finish_time > 0 AND cancel_time IS NULL) 
                    OR (".$prefix."order.status = ".ORDER_STATUS_SELLER_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL) 
                    OR (".$prefix."order.status = ".ORDER_STATUS_ADMIN_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL) 
                    OR (".$prefix."order.status = ".ORDER_STATUS_REFUND_SUCCESS." AND cancel_time IS NOT NULL))");

            }
        } else {
            $list->whereRaw("cancel_time IS NULL");
        }
        if($sn == true){
            $list->where('order.sn', $sn);
        }

        $totalCount = count($list->groupBy('order.id')->lists('sn'));

        $list = $list->select('order.*')
            ->selectRaw('IFNULL(SUM(IF('.$prefix.'invitation_back_log.level = 0, '.$prefix.'invitation_back_log.return_fee, 0)), 0) AS level0,IFNULL(SUM(IF('.$prefix.'invitation_back_log.level = 1, '.$prefix.'invitation_back_log.return_fee, 0)), 0) AS level1,IFNULL(SUM(IF('.$prefix.'invitation_back_log.level = 2, '.$prefix.'invitation_back_log.return_fee, 0)), 0) AS level2,IFNULL(SUM(IF('.$prefix.'invitation_back_log.level = 3, '.$prefix.'invitation_back_log.return_fee, 0)), 0) AS level3')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->with('user')
            ->orderBy('order.id')
            ->get();
        return ["list" => $list, "totalCount" => $totalCount];
    }

    /**
     * [userlist 获取分享邀请用户的返现资金]
     * @param  [type] $invitationName [description]
     * @param  [type] $page           [description]
     * @param  [type] $pageSize       [description]
     * @return [type]                 [description]
     */
    public static function userlist($userName, $invitationName, $type, $page, $pageSize){
        $prefix = DB::getTablePrefix();
        $userId = User::where('mobile', $userName)->pluck('id');
        //搜索邀请人为会员
        if($type == 1){
            $invitorId = User::where('mobile', $invitationName)->pluck('id');
        } else {
            $invitorId = Seller::where('mobile', $invitationName)->pluck('id');
        }

        if($userName && $userId <= 0){
            return ["list"=>0, "totalCount"=>0];
        }
        if($invitationName && $invitorId <= 0){
            return ["list"=>0, "totalCount"=>0];
        }
        $data = [];
		
		$userName = $userName ? ' and id = '.$userId : '';
		
		$invitationName = $invitationName ? ' and (invitationId1 = '.$invitorId.' OR invitationId2 = '.$invitorId.' OR invitationId3 = '.$invitorId.')' : '';
		
        $countSql = "SELECT count(*) as totalNum FROM (SELECT U.id,
                            U.name,
                            sum(O.pay_fee) AS totalSellFee,
                            sum(IBL.return_fee) AS totalReturnFee,
                            IF(U.invitation_type='seller', S.name, U1.name) as invitationName1,
                            IF(U1.invitation_type='seller', S1.name, U2.name) as invitationName2,
                            IF(U2.invitation_type='seller', S2.name, U3.name) as invitationName3,
                            IF(U.invitation_type='seller', S.id, U1.id) as invitationId1,
                            IF(U1.invitation_type='seller', S1.id, U2.id) as invitationId2,
                            IF(U2.invitation_type='seller', S2.id, U3.id) as invitationId3
                    FROM ".$prefix."user AS U
                    LEFT JOIN ".$prefix."invitation_back_log AS IBL
                            ON U.id = IBL.invitation_id
                            AND IBL. STATUS = 1
                    LEFT JOIN ".$prefix."order AS O
                            ON O.id = IBL.order_id 
                            AND (O.status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.")
                                            OR (O.status = 500 AND O.buyer_finish_time > 0 AND O.cancel_time IS NULL) 
                                            OR (O.status = 501 AND O.auto_finish_time > 0 AND O.cancel_time IS NULL) 
                                            OR (O.status = 502 AND O.auto_finish_time > 0 AND O.cancel_time IS NULL))  
                    LEFT JOIN ".$prefix."user AS U0 ON U0.id = U.invitation_id and U.invitation_type = 'user'
                    LEFT JOIN ".$prefix."user AS U1 ON U1.id = U.invitation_id and U.invitation_type = 'user'
                    LEFT JOIN ".$prefix."seller AS S ON U.invitation_id = S.id and U.invitation_type = 'seller'
                    LEFT JOIN ".$prefix."user AS U2 ON U2.id = U1.invitation_id and U1.invitation_type = 'user'
                    LEFT JOIN ".$prefix."seller AS S1 ON U1.invitation_id = S1.id and U1.invitation_type = 'seller'
                    LEFT JOIN ".$prefix."user AS U3 ON U3.id = U2.invitation_id and U2.invitation_type = 'user'
                    LEFT JOIN ".$prefix."seller AS S2 ON U2.invitation_id = S2.id and U2.invitation_type = 'seller'
                    GROUP BY U.id ) as newuser where 1  ".$userName.$invitationName;
        $totalCountData = DB::select($countSql);
        $totalCount = $totalCountData[0]->totalNum;
        $sql = "SELECT 
                    id,
                    name,
                    totalSellFee,
                    totalReturnFee, 
                    invitationName1,
                    invitationName2,
                    invitationName3
                FROM (SELECT U.id,
                            U.name,
                            sum(O.pay_fee) AS totalSellFee,
                            sum(IBL.return_fee) AS totalReturnFee, 
                            IF(U.invitation_type='seller', S.name, U1.name) as invitationName1,
                            IF(U1.invitation_type='seller', S1.name, U2.name) as invitationName2,
                            IF(U2.invitation_type='seller', S2.name, U3.name) as invitationName3,
                            IF(U.invitation_type='seller', S.id, U1.id) as invitationId1,
                            IF(U1.invitation_type='seller', S1.id, U2.id) as invitationId2,
                            IF(U2.invitation_type='seller', S2.id, U3.id) as invitationId3
                    FROM ".$prefix."user AS U
                    LEFT JOIN ".$prefix."invitation_back_log AS IBL 
                            ON U.id = IBL.invitation_id
                            AND IBL. STATUS = 1 
                    LEFT JOIN ".$prefix."order AS O 
                            ON O.id = IBL.order_id 
                            AND (O.status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.")
                                            OR (O.status = 500 AND O.buyer_finish_time > 0 AND O.cancel_time IS NULL) 
                                            OR (O.status = 501 AND O.auto_finish_time > 0 AND O.cancel_time IS NULL) 
                                            OR (O.status = 502 AND O.auto_finish_time > 0 AND O.cancel_time IS NULL))  
                    LEFT JOIN ".$prefix."user AS U1 ON U1.id = U.invitation_id and U.invitation_type = 'user'
                    LEFT JOIN ".$prefix."seller AS S ON U.invitation_id = S.id and U.invitation_type = 'seller'
                    LEFT JOIN ".$prefix."user AS U2 ON U2.id = U1.invitation_id and U1.invitation_type = 'user'
                    LEFT JOIN ".$prefix."seller AS S1 ON U1.invitation_id = S1.id and U1.invitation_type = 'seller'
                    LEFT JOIN ".$prefix."user AS U3 ON U3.id = U2.invitation_id and U2.invitation_type = 'user'
                    LEFT JOIN ".$prefix."seller AS S2 ON U2.invitation_id = S2.id and U2.invitation_type = 'seller'
                    GROUP BY U.id
                    ORDER BY totalSellFee DESC, totalReturnFee DESC, U.id DESC
                ) as newuser where 1  ".$userName.$invitationName." LIMIT ".($page - 1) * $pageSize.", ".$pageSize."";
        $lists = DB::select($sql);

        return ["list"=>$lists, "totalCount"=>$totalCount];
    }

    /**
     * [invitationlist 成功邀请的人员列表]
     * @param  [type] $invitationId [description]
     * @param  [type] $page         [description]
     * @param  [type] $pageSize     [description]
     * @return [type]               [description]
     */
    public static function invitationlist($invitationId, $sn, $userName, $status, $page, $pageSize) {
        //DB::connection()->enableQueryLog();
        $prefix = DB::getTablePrefix();
        $lists = InvitationBackLog::join('order', function($join) use($invitationId){
            $join->on('order.id', '=', 'invitation_back_log.order_id')
                ->where('invitation_id', '=', $invitationId);
        })
            ->leftJoin('user', function($join) use($invitationId){
                $join->on('user.id', '=', 'invitation_back_log.user_id');
            });
        if($sn == true){
            $lists->where('order.sn', $sn);
        }
        if($status > 0){
            $lists->where('invitation_back_log.status', $status - 1);
        }
        if($userName == true){
            $lists->where('user.name', '=', $userName);
        }
        $totalCount = $lists->count();
        $lists = $lists->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->with('user')
            ->select('order.*')
            ->addSelect(DB::raw('level, percent, '.$prefix.'invitation_back_log.return_fee as return_money, '.$prefix.'invitation_back_log.status as order_status, '.$prefix.'user.name as user_name'))
            ->get();
        // print_r(DB::getQueryLog());
        return ["list"=>$lists, "totalCount"=>$totalCount];
    }

    /**
     * 二维码
     */
    public function cancode($type,$id){
        $type = ucfirst($type);
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => null
        );

        if(!$id){
            $result['code'] = 9999;
            return $result;
        }
        $table = DB::table(strtolower($type))->where('id', $id)->first();
        if (!$table) {
            $result['code'] = 60014;
            return $result;
        }
        $args = ['id'=>$id,'type'=>$type];
        if($type == "User"){
            $value = u("wap#/User/guide",['id'=>$id,'type'=>$type]);
            $images = $type.'D'.$table->id.'S'.Time::toDate($table->reg_time,'ymdHis').'Y';
        }else{
            $args['urltype'] = 1;
            $value = u('wap#Seller/detail',$args);
            $images = $type.'D'.$table->id.'S'.Time::toDate($table->create_time,'ymdHis').'Y';
        }
        $result['data'] = [
            "val" => $value,
            "images" => $images,
        ];
        return $result;
    }
    //
    public function userc($type,$id){
        $type = strtolower($type);
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => null
        );
        if(!$id){
            $result['code'] = 9999;
            return $result;
        }
        $prefix = DB::getTablePrefix();
        if($type == 'user') {
            $sql = "SELECT count(*) as num
                    FROM (
                        SELECT 
                            1 as level, 
                            U.id,
                            U.name 
                            FROM {$prefix}user as U 
                            WHERE U.invitation_id = {$id}
                        UNION
                        SELECT 
                            2 as level, 
                            U1.id, 
                            U1.name FROM {$prefix}user as U 
                            INNER JOIN {$prefix}user as U1 
                            ON U.invitation_id = {$id} 
                            AND U.id = U1.invitation_id
                        UNION
                        SELECT 
                            3 as level, 
                            U2.id, 
                            U2.name FROM {$prefix}user as U 
                            INNER JOIN {$prefix}user as U1 
                            ON U.invitation_id = {$id} 
                            AND U.id = U1.invitation_id 
                            INNER JOIN {$prefix}user as U2 
                            ON U1.id = U2.invitation_id
                        ) AS tmp 
                        LIMIT 1";
            $data = DB::select($sql);
            $count = $data[0]->num;
        } else {
            $count = User::where('invitation_type', $type)->where('invitation_id', $id)->count();
        }
        $result['data']['count'] = $count;
        $result['data']['money'] = InvitationBackLog::where('invitation_type', strtolower($type))->where('invitation_id', $id)->selectRaw('SUM(return_fee) as money')->where('status',1)->pluck('money');

        return $result;
    }


    public function createLog($data){

        if($data != ""){
            if(is_array($data)){
                $res =  InvitationBackLog::insert($data);
            }else{

                $res = InvitationBackLog::insertGetId($data);
            }
            return  $res;
        }
        return false;
    }

    /**
     * 返现统计
     */
    public static function statistics($year, $month){
        $prefix = DB::getTablePrefix();
        $current = $year.'-'.sprintf("%02d", $month);
        $t = Time::toDate(Time::toTime($current), 't');
        if($current == Time::toDate(UTC_TIME, 'Y-m')){
            $t = Time::toDate(UTC_TIME, 'd');
        } else if(Time::toTime($current) > Time::toTime(Time::toDate(UTC_DAY, 'Y-m'))){
            return ["list" => [], "sum" => []];
        }
        $startTime = Time::toTime($current.'-01 00:00:00');
        $endTime = Time::toTime($current.'-'.$t.' 23:59:59');
        $sumsql = "SELECT  
                        sum(totalFee) as totalFee,
                        sum(totalReturnFee) as totalReturnFee,
                        sum(level1Fee) as level1Fee,
                        sum(level2Fee) as level2Fee,
                        sum(level3Fee) as level3Fee,
                        sum(newUserNum) as newUserNum
                    FROM
                    (SELECT  
                        IFNULL(SUM(totalFee), 0) AS totalFee, 
                        IFNULL(SUM(totalReturnFee), 0) AS totalReturnFee, 
                        IFNULL(SUM(level1Fee), 0) AS level1Fee, 
                        IFNULL(SUM(level2Fee), 0) AS level2Fee, 
                        IFNULL(SUM(level3Fee), 0) AS level3Fee,
                        0 AS newUserNum
                    from (
                        SELECT
                        O.id,  
                        O.total_fee as totalFee,
                        O.return_fee as totalReturnFee,
                        sum(IF(IBL.level = 1, IBL.return_fee , 0)) as level1Fee,
                        sum(IF(IBL.level = 2, IBL.return_fee , 0)) as level2Fee,
                        sum(IF(IBL.level = 3, IBL.return_fee , 0)) as level3Fee
                    FROM
                        {$prefix}order AS O
                    INNER JOIN {$prefix}invitation_back_log AS IBL ON IBL.order_id = O.id
                    AND IBL. STATUS = 1
                    WHERE O.create_time BETWEEN {$startTime} AND {$endTime}
                    GROUP BY
                        O.id
                    ) as newOrder 
                    UNION  
                    SELECT 
                        0 AS totalFee, 
                        0 AS totalReturnFee, 
                        0 AS level1Fee, 
                        0 AS level2Fee, 
                        0 AS level3Fee,
                        count(*) AS newUserNum
                    FROM {$prefix}user where invitation_id > 0  
                    AND invitation_type is not null
                    AND reg_time BETWEEN {$startTime} AND {$endTime} 
                    ) as tmp 
                    ";

        $sum = DB::select($sumsql);

        $sql = "SELECT 
                    daytime,
                    sum(totalFee) as totalFee,
                    sum(totalReturnFee) as totalReturnFee,
                    sum(level1Fee) as level1Fee,
                    sum(level2Fee) as level2Fee,
                    sum(level3Fee) as level3Fee,
                    sum(newUserNum) as newUserNum
                FROM
                (SELECT 
                    FROM_UNIXTIME(createTime,'%Y-%m-%d') as daytime,
                    IFNULL(SUM(totalFee), 0) AS totalFee, 
                    IFNULL(SUM(totalReturnFee), 0) AS totalReturnFee, 
                    IFNULL(SUM(level1Fee), 0) AS level1Fee, 
                    IFNULL(SUM(level2Fee), 0) AS level2Fee, 
                    IFNULL(SUM(level3Fee), 0) AS level3Fee,
                    0 AS newUserNum
                from (
                    SELECT
                    O.id,
                    O.create_time as createTime,
                    O.create_day as createDay,
                    O.total_fee as totalFee,
                    O.return_fee as totalReturnFee,
                    sum(IF(IBL.level = 1, IBL.return_fee , 0)) as level1Fee,
                    sum(IF(IBL.level = 2, IBL.return_fee , 0)) as level2Fee,
                    sum(IF(IBL.level = 3, IBL.return_fee , 0)) as level3Fee
                FROM
                    {$prefix}order AS O
                INNER JOIN {$prefix}invitation_back_log AS IBL ON IBL.order_id = O.id
                AND IBL. STATUS = 1
                WHERE O.create_time BETWEEN {$startTime} AND {$endTime} 
                GROUP BY
                    O.id
                ) as newOrder
                GROUP BY createDay 
                UNION  
                SELECT
                    FROM_UNIXTIME(reg_time,'%Y-%m-%d') as daytime,
                    0 AS totalFee, 
                    0 AS totalReturnFee, 
                    0 AS level1Fee, 
                    0 AS level2Fee, 
                    0 AS level3Fee,
                    count(*) AS newUserNum
                FROM {$prefix}user where invitation_id > 0  
                AND invitation_type is not null
                AND reg_time BETWEEN {$startTime} AND {$endTime} 
                GROUP BY FROM_UNIXTIME(reg_time,'%Y-%m-%d')
                ) as tmp
                GROUP BY daytime
                ";

        $queryData = DB::select($sql);
        $list = [];
        for($i = 1; $i <= $t; $i++) {
            $daytime = $current . '-' . sprintf("%02d", $i);
            $dayData = [
                'totalFee' => 0,
                'totalReturnFee' => 0,
                'level1Fee' => 0,
                'level2Fee' => 0,
                'level3Fee' => 0,
                'newUserNum' => 0,
                'daytime' => $daytime,
            ];
            $bool = false;
            foreach ($queryData as $item) {
                $item = (array)$item;
                if($item['daytime'] == $daytime){
                    $bool = true;
                    break;
                }
            }
            if($bool){
                $list[] = $item;
            } else {
                $list[] = $dayData;
            }
        }
        // print_r(DB::getQueryLog());
        return ["list" => $list, "sum" => $sum[0]];
    }

    /**
     * 获取邀请列表
     */
    public static function getUserLists($id, $type, $userId,$level = 1,$page, $pageSize){

        if($userId){
            $id = $userId;
        }
        $type = strtolower($type);
        $levels = $level ? $level : 0;
        $prefix = DB::getTablePrefix();
        if($type == 'user') {
            if($levels == 1){
                $sql = "SELECT tmp.id,tmp.level
                        FROM (
                            SELECT
									1 as level,
                                    U.id
                                    FROM {$prefix}user as U
                                    WHERE U.invitation_id = {$id}
                                ) AS tmp";
            }else if($levels == 2){
                $sql = "SELECT tmp.id,tmp.level
                        FROM (
                                SELECT
									2 as level,
                                    U1.id
									
									FROM {$prefix}user as U
                                    INNER JOIN {$prefix}user as U1
                                    ON U.invitation_id = {$id}
                                    AND U.id = U1.invitation_id
                ) AS tmp";
            }else if($levels == 3){
                $sql = "SELECT tmp.id,tmp.level
                        FROM (
                            SELECT
								3 as level,
                                U2.id
								FROM {$prefix}user as U
                                INNER JOIN {$prefix}user as U1
                                ON U.invitation_id = {$id}
                                AND U.id = U1.invitation_id
                                INNER JOIN {$prefix}user as U2
                                ON U1.id = U2.invitation_id
                            ) AS tmp";
            }else{
                $sql = "SELECT tmp.id,tmp.level
                    FROM (
                        SELECT
							1 as level,
                            U.id
                            FROM {$prefix}user as U
                            WHERE U.invitation_id = {$id}
                        UNION
                        SELECT
						
							2 as level,
                            U1.id
							FROM {$prefix}user as U
                            INNER JOIN {$prefix}user as U1
                            ON U.invitation_id = {$id}
                            AND U.id = U1.invitation_id
                        UNION
                        SELECT
						
							3 as level,
                            U2.id
							FROM {$prefix}user as U
                            INNER JOIN {$prefix}user as U1
                            ON U.invitation_id = {$id}
                            AND U.id = U1.invitation_id
                            INNER JOIN {$prefix}user as U2
                            ON U1.id = U2.invitation_id
                        ) AS tmp";
            }
            $sql .= " LIMIT ".($page - 1) * $pageSize.", ".$pageSize."";

            $level = DB::select($sql);
            $ids = [];
            $leveldata = [];
            foreach($level as $v){
                $leveldata[$v->id] = $v->level;
                $ids[] = $v->id;
            }
            $levelType = [
                1 => 'I',
                2 => 'II',
                3 => 'III'
            ];
            $user = User::whereIn("user.id",$ids)->where("user.invitation_type",$type);
            $user = $user->select(
                "user.id as userId",
                "user.avatar",
                "user.name as userName",
                "user.mobile as UserMobile",
                "user.invitation_id as invitationId",
                "user.invitation_type as invitationType"
            )->orderBy("user.id","desc")
                ->get()
                ->toArray();
            $list['list'] = [];
            foreach($user  as $k => $item){

                $userId = $item['userId'];
                $invitationId = $item['invitationId'];

                $item['partnerName'] = User::where("id", $item['invitationId'])->pluck('name');

                $item['returnFee'] = InvitationBackLog::where( function($query) use($userId){
                    $query->where('invitation_id', $userId)
                        ->orWhere("share_user_id",$userId);
                })->where("invitation_type",'user')->where("status",1)->sum("return_fee");

                $item['orderCount'] = InvitationBackLog::where( function($query) use($userId){
                    $query->where('invitation_id', $userId)
                        ->orWhere("share_user_id",$userId);
                })->where("invitation_type",'user')->where("status",1)->count();

                $item['level'] = $levelType[$leveldata[$userId]];

                if($item['level'] != "III"){

                    $sql = "SELECT count(tmp.id) as count
						FROM (
							SELECT
								U.id
								FROM {$prefix}user as U
								WHERE U.invitation_id = {$userId}
							UNION
							SELECT
								U1.id
								FROM {$prefix}user as U
								INNER JOIN {$prefix}user as U1
								ON U.invitation_id = {$userId}
								AND U.id = U1.invitation_id
							UNION
							SELECT
								U2.id
								FROM {$prefix}user as U
								INNER JOIN {$prefix}user as U1
								ON U.invitation_id = {$userId}
								AND U.id = U1.invitation_id
								INNER JOIN {$prefix}user as U2
								ON U1.id = U2.invitation_id
							) AS tmp";
                    $level = DB::select($sql);

                    $item['partner'] = $level[0]->count;
                }
                $list['list'][] =  $item;
            }
        } else {
            $invitation = self::getById();
            $sql = "SELECT 
                        1 as level, 
                        U.id,
                        U.name,
                        IFNULL(sum(IBL.return_fee),0) as commision  
                    FROM {$prefix}user as U LEFT JOIN {$prefix}invitation_back_log AS IBL 
                    ON U.id = IBL.invitation_id 
                    AND IBL.status = 1 
                    WHERE U.invitation_type = '{$type}'
                    AND U.invitation_id = {$id}
                    GROUP BY U.id 
                    ORDER BY U.id DESC, commision DESC
                    LIMIT ".($page - 1) * $pageSize.", ".$pageSize."";
            $data = DB::select($sql);
            $list['list'] = [];
            foreach ($data as $value) {
                $value = (array)$value;
                $value['percent'] = $invitation->seller_percent.'%';
                $list[] = $value;
            }
        }
        $sql = "SELECT count(tmp.id) as count
                    FROM (
                        SELECT
                            U.id
                            FROM {$prefix}user as U
                            WHERE U.invitation_id = {$id}
                        UNION
                        SELECT
                            U1.id
							FROM {$prefix}user as U
                            INNER JOIN {$prefix}user as U1
                            ON U.invitation_id = {$id}
                            AND U.id = U1.invitation_id
                        UNION
                        SELECT
                            U2.id
							FROM {$prefix}user as U
                            INNER JOIN {$prefix}user as U1
                            ON U.invitation_id = {$id}
                            AND U.id = U1.invitation_id
                            INNER JOIN {$prefix}user as U2
                            ON U1.id = U2.invitation_id
                        ) AS tmp";
        $level = DB::select($sql);
        $list['count'] = $level[0]->count;

        // print_r($list);die;
        return $list;
    }

    /**
     * 奖励记录列表
     */
    public static function getRecords($id, $type, $page, $pageSize){
        $prefix = DB::getTablePrefix();
        $data = InvitationBackLog::join('order', function($join) use($id){
            $join->on('invitation_back_log.order_id', '=', 'order.id')
                ->where('invitation_back_log.status', '=', 1)
                ->where('invitation_back_log.invitation_id', '=', $id);
        })
            ->leftJoin('user', function($join){
                $join->on('invitation_back_log.user_id', '=', 'user.id');
            })
            ->selectRaw(DB::raw("{$prefix}order.id,{$prefix}order.create_time,{$prefix}invitation_back_log.percent,{$prefix}invitation_back_log.return_fee,({$prefix}order.pay_fee - {$prefix}order.freight) as total_fee,{$prefix}user.name"))
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get();
        return $data;
    }
    public static function moneyLog($paySn, $beginTime, $endTime,$page, $pageSize) {


        $list = \YiZan\Models\System\UserPayLog::with('user')->where('is_fx',1);

        if($paySn){
            $list->where('sn',  'like',trim($paySn));
        }
        if ($beginTime) {//创建开始时间
            $list->where('create_day', '>=', Time::toTime($beginTime));
        }

        if ($endTime) {//创建结束时间
            $list->where('create_day', '<=', Time::toTime($endTime));
        }
        $list->orderBy('id', 'desc');
        $total_count = $list->count();
        $list = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();
        return ["list" => $list, "totalCount" => $total_count];
    }
}
