<?php namespace YiZan\Services\Buyer;
use YiZan\Models\Promotion;
use YiZan\Models\PromotionSn;
use YiZan\Models\PushMessage;
use YiZan\Models\ReadMessage;
use YiZan\Models\ShoppingCart;
use YiZan\Models\UserAddress;
use YiZan\Models\UserCollect;
use YiZan\Models\Order;
use YiZan\Utils\Time;
use YiZan\Services\Buyer\ForumMessageService;
use YiZan\Models\InvitationBackLog;

use DB;
/**
 * 阅读信息
 */
class ReadMessageService extends \YiZan\Services\ReadMessageService 
{ 
    /**
     * Summary of getSellerList
     * @param mixed $userId 
     * @param mixed $page 
     * @param mixed $pageSize 
     * @return mixed
     */
    public static function getBuyerList($userId, $page, $pageSize = 20) 
    {
        $begin = $pageSize * ($page - 1);

        $sql = "SELECT  T.`seller_id`,
				T.`sum`,
				P.`title`,
				IFNULL(S.`logo`, '') AS `logo`,
				IFNULL(S.`name`, '平台消息') AS `name`
		FROM
		(
				SELECT 	P.`seller_id`,
								SUM(CASE WHEN read_time > 1 THEN 0 ELSE 1 END) AS `sum`,
								MAX(P.`id`) AS `id`
						FROM `".env("DB_PREFIX")."read_message` AS R
								INNER JOIN `".env("DB_PREFIX")."push_message` AS P ON R.`message_id` = P.`id`
						WHERE R.`user_id` = ".$userId."
						GROUP BY P.`seller_id`
		) AS T
		INNER JOIN `".env("DB_PREFIX")."push_message` AS P ON T.`id` = P.`id`
		LEFT OUTER JOIN `".env("DB_PREFIX")."seller` AS S ON T.`seller_id` = S.`id` limit ".$begin.",".$pageSize;

        $list = DB::select($sql);
        return $list;
    }
    /**
     * 阅读消息
     * @param int $userId 买家编号
     * @param int $id 消息编号
     * @return array 
     */
    public static function readMessage($userId, $ids)
    {
		$result = 
        [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];
        if( !is_array( $ids ) ){
            $ids = [  '0' => $ids   ];
        }
        if( !empty($ids))
        {
            ReadMessage::where("user_id", $userId)->where("read_time", 0)->whereIn("id", $ids)->update(["read_time" => Time::getTime()]);
        }
        else
        {
            ReadMessage::where("user_id", $userId)->where("read_time", 0)->update(["read_time" => Time::getTime()]);
        }

        return $result;
    }
    /**
     * 删除消息
     * @param int $userId 买家编号
     * @param int $id 消息编号
     * @return array 
     */
    public static function deleteMessage($userId, $id) 
    {
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ""
		];
        
        if(is_int($id))
        {
            if($id > 0)
            {
                ReadMessage::where("user_id", $userId)->where("id", $id)->delete();
            }
            else
            {
                ReadMessage::where("user_id", $userId)->delete();
            }
        }
        else if(is_array($id) && count($id) > 0)
        {
            ReadMessage::where("user_id", $userId)->whereIn("id", $id)->delete();
        }
        
        return $result;
    }
    /**
     * 是否有新消息
     * @param int $userId 买家编号
     * @return bool
     */
    public static function hasNewMessage($userId)
    {
        $reslut = ReadMessage::where('user_id', $userId)
            ->where('is_read', 0)
            ->pluck('user_id');
        
        return $reslut > 0;
    }

    public static function getDatas($userId, $sellerId, $page,$team = 0, $pageSize = 20)
    {
       ReadMessage::where('read_time',0)
                    ->where('user_id', $userId)
                    ->whereIn('message_id',function($query) use ($userId, $sellerId){
                            $query->select('id')
                                ->from('push_message')
                                ->where('seller_id', $sellerId);
                    })->update(['read_time' => UTC_TIME]);

        $list = PushMessage::whereRaw(" FIND_IN_SET(  '{$userId}', users) ");//->join('read_message', 'read_message.message_id', '=', 'push_message.id')
        if($team){
            $list->where('push_message.send_type','=',6)
                ->with("user");
        }else{
            $list->where('push_message.send_type','!=',3) ->where('push_message.send_type','!=',6);
        }
        $list = $list->where('push_message.type','=','buyer')
            // ->select("push_message.*","read_message.read_time as readCount")
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->orderBy('push_message.id','desc')
            ->get()
            ->toArray();
        return $list;
    }

    public static function getCounts($userId)
    {
        $list = [];
        $carts = ShoppingCart::join('seller', 'shopping_cart.seller_id', '=', 'seller.id')
                ->join('goods', 'shopping_cart.goods_id', '=', 'goods.id') 
                ->where('shopping_cart.user_id', $userId)
                ->where('shopping_cart.type', '!=', 2)
                ->where('seller.status', 1)
                ->sum(num);

        if ($userId > 0) {
            $list['cartGoodsCount'] = $carts;
            $list['collectCount'] = UserCollect::where('user_id', $userId)->count();
            $list['addressCount'] = UserAddress::where('user_id', $userId)->count();
            $list['systemCount'] =ForumMessageService::getMessageNum($userId);
            $list['newMsgCount'] = ReadMessage::where('user_id', $userId)
                                                ->where('read_time', 0)
                                                ->join('push_message', function($join) {
                                                    $join->on('read_message.message_id', '=', 'push_message.id');
                                                 })
                                                ->count();
            $list['orderCount'] = Order::where('user_id', $userId)
                                            ->whereNotIn('status', [
                                                ORDER_STATUS_USER_DELETE,
                                                ORDER_STATUS_SELLER_DELETE,
                                                ORDER_STATUS_ADMIN_DELETE
                                            ])->count();
            $list['proCount'] = PromotionSn::where('user_id', $userId)
                                                ->where('is_del', 0)
                                                ->where('use_time',0)
                                                ->where('expire_time','>',UTC_TIME)
                                                ->count();

        } else {
            $list['cartGoodsCount'] = 0;
            $list['collectCount'] = 0;
            $list['addressCount'] = 0;
            $list['systemCount'] = 0;
            $list['newMsgCount'] = 0;
            $list['orderCount'] = 0;
            $list['proCount'] = 0;
        }
        
        return $list;
    }

	public static function  systemmessage($userId){
        $lists = [];
        /*系统消息*/
        $lists['systemCount'] = ReadMessage::where('user_id', $userId)
            ->where('read_time', 0)
            ->join('push_message', function($join) {
                $join->on('read_message.message_id', '=', 'push_message.id');
                $join->where('push_message.send_type', '!=',3);
                $join->where('push_message.send_type', '!=',6);
                $join->where('push_message.type', '=','buyer');
            })
            ->count();
         $systemInfo = PushMessage::where('users',$userId)
             ->where('send_type','!=',3)
             ->where('send_type','!=',6)
             ->where('type','=','buyer')
             ->orderBy('id','desc')
             ->first();
        if($systemInfo){
            $lists['systemInfo'] = $systemInfo->toArray();

        }else{
            $lists['systemInfo'] ='';
        }
        /*生活圈*/
        $lists['messageNum'] = ForumMessageService::getMessageNum($userId);

        /*订单数量*/
        $lists['orderCount'] = ReadMessage::where('user_id', $userId)
            ->where('read_time', 0)
            ->join('push_message', function($join) {
                $join->on('read_message.message_id', '=', 'push_message.id');
                $join->where('push_message.send_type', '=',3);
                $join->where('push_message.type', '=','buyer');
            })
            ->count();
        $orderInfo = PushMessage::where('users', $userId)
            ->where('send_type',3)
            ->where('type','buyer')
            ->orderBy('id','DESC')
            ->first();

        /*团队数量*/
        $lists['teamCount'] = ReadMessage::where('user_id', $userId)
            ->where('read_time', 0)
            ->join('push_message', function($join) {
                $join->on('read_message.message_id', '=', 'push_message.id');
                $join->where('push_message.send_type', '=',6);
                $join->where('push_message.type', '=','buyer');
            })
            ->count();

         $teamInfo = PushMessage::where('users', $userId)
           ->where('send_type',6)
           ->where('type','buyer')
           ->orderBy('id','DESC')
           ->first();

        $lists['financeInfo'] = InvitationBackLog::where("invitation_id",$userId)->where("invitation_type",'user')->with("user")->orderBy('update_time','desc')->orderBy("is_show","asc")->first();

        $financeCount = InvitationBackLog::where('user_id', $userId)->where("invitation_type",'user')->where('is_show',0)->count();
        $lists['financeCount'] = $financeCount;
        $lists['orderInfo'] = $orderInfo;
        return $lists;

    }
    public static function wealth($userId, $sellerId, $page,$team = 0){

        InvitationBackLog::where('user_id', $userId)->where("invitation_type",'user')->where('is_show',0)->update(["is_show" => 1]);
        $pageSize = 20;
        $list = InvitationBackLog::where('invitation_id',$userId)->where("invitation_type",'user')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->with("user",'jtuser')
            ->orderBy('update_time','desc')
            ->get()
            ->toArray();
        return $list;

    }

    public static function orderchange($userId, $page)
    {
    	ReadMessage::where('read_time',0)
                    ->where('user_id', $userId)
                    ->whereIn('message_id',function($query){
                            $query->select('id')
                                ->from('push_message')
                                ->where('send_type',3);
                    })->update(['read_time' => UTC_TIME]);
        $list = PushMessage::where('users',$userId)
            ->where('push_message.send_type',3)
            ->where('push_message.type','=','buyer')
            ->with('orders.goods','refund')
            ->select(
                'push_message.*' ,
                'order_track.express_number'
            )
            ->skip(($page - 1) * 20)
            ->take(20)
            ->leftJoin('order_track', 'order_track.order_id', '=', 'push_message.order_id')
            ->orderBy('id','desc')
            ->get()
            ->toArray();
			
        return $list;
    }
}
