<?php namespace YiZan\Services;

use YiZan\Models\SellerStaff;
use YiZan\Models\Order;
use YiZan\Models\SellerStaffMoneyLog;
use YiZan\Models\Region;

use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Utils\String;

use Exception, DB, Lang, Validator, App;

class SendcenterService extends BaseService
{
    /**
     * [stafflist 配送中心获取人员配送数据]
     * @param  [type] $time      [指定时间查询 null=全部 1=今天 7=7天 30=30天]
     * @param  [type] $beginTime [开始时间]
     * @param  [type] $endTime   [结束时间]
     * @param  [type] $cityName  [城市名称]
     * @param  [type] $page      [分页]
     * @param  [type] $pageSize  [分页大小]
     * @return [type]            [description]
     */
	public static function stafflist($time, $beginTime, $endTime, $cityName, $page, $pageSize){
        $list = SellerStaff::where('is_system', 1);

        if($cityName)
        {
        	$list->where("address", "like", "%".$cityName."%");
        }

        $totalCount = $list->count();
		$list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();
        
        $ids = array_pluck($list, 'id');

        //完成订单
        $statusArr = [
        	ORDER_STATUS_FINISH_STAFF,
			ORDER_STATUS_FINISH_SYSTEM,
			ORDER_STATUS_FINISH_USER,
        ];
        //异常订单（商家取消 平台取消 会员取消）
        $statusErr = [
        	ORDER_STATUS_CANCEL_USER,
			ORDER_STATUS_CANCEL_SELLER,
			ORDER_STATUS_CANCEL_ADMIN,
        ];

        if($time)
        {
        	switch ($time) {
				case '1':
					$beginTime = UTC_DAY;
					break;

				case '7':
					$beginTime = UTC_DAY - 86400 * 6;
					break;

				case '30':
					$beginTime = UTC_DAY - 86400 * 29;
					break;
			}
			$endTime = UTC_DAY + 86400 - 1;
        }

        //订单统计
		if($beginTime || $endTime)
		{
			//查询指定日期
			foreach ($ids as $key => $value) {
				$orderCount[$value]['totalOrder'] = Order::where('seller_staff_id', $value)
													     ->where('create_day', '>=', $beginTime)
				                                         ->where('create_day', '<=', $endTime)
				                                         ->select(DB::raw('count(*) as totalOrder'))
				                                         ->pluck('totalOrder');

				$orderCount[$value]['totalEndOrder'] = Order::where('seller_staff_id', $value)
															->where('create_day', '>=', $beginTime)
				                                            ->where('create_day', '<=', $endTime)
				                                            ->whereIn('status', $statusArr)
										                    ->addSelect(DB::raw('count(*) as totalEndOrder'))
				                                            ->pluck('totalEndOrder');

				$orderCount[$value]['totalErrOrder'] = Order::where('seller_staff_id', $value)
															->where('create_day', '>=', $beginTime)
				                                            ->where('create_day', '<=', $endTime)
				                                            ->whereIn('status', $statusErr)
										                    ->addSelect(DB::raw('count(*) as totalErrOrder'))
				                                            ->pluck('totalErrOrder');
				                                              
				$orderCount[$value]['mackMoney']  = SellerStaffMoneyLog::where('staff_id', $value)
													     ->where('create_day', '>=', $beginTime)
				                                         ->where('create_day', '<=', $endTime)
				                                         ->where('money', '>', 0)
				                                         ->select(DB::raw('sum(money) as mackMoney'))
				                                         ->pluck('mackMoney');
			}
		}
		else
		{
			//查询全部
			foreach ($ids as $key => $value) {
				$orderCount[$value]['totalOrder'] = Order::where('seller_staff_id', $value)
				                                         ->select(DB::raw('count(*) as totalOrder'))
				                                         ->pluck('totalOrder');

				$orderCount[$value]['totalEndOrder'] = Order::where('seller_staff_id', $value)
				                                            ->whereIn('status', $statusArr)
										                    ->addSelect(DB::raw('count(*) as totalEndOrder'))
				                                            ->pluck('totalEndOrder');

				$orderCount[$value]['totalErrOrder'] = Order::where('seller_staff_id', $value)
				                                            ->whereIn('status', $statusErr)
										                    ->addSelect(DB::raw('count(*) as totalErrOrder'))
				                                            ->pluck('totalErrOrder'); 

				$orderCount[$value]['mackMoney']  = SellerStaffMoneyLog::where('staff_id', $value)
				                                         ->where('money', '>', 0)
				                                         ->select(DB::raw('sum(money) as mackMoney'))
				                                         ->pluck('mackMoney');
			}

		}
		foreach ($list as $key => $value) {
			$list[$key]['total'] = $orderCount[$value['id']];
			$list[$key]['total']['mackMoney'] = (double)$orderCount[$value['id']]['mackMoney'];
		}

        return ["list"=>$list, "totalCount"=>$totalCount];
    }

    /**
     * [stafflist 城市配送数据]
     * @param  [type] $time      [指定时间查询 null=全部 1=今天 7=7天 30=30天]
     * @param  [type] $beginTime [开始时间]
     * @param  [type] $endTime   [结束时间]
     * @param  [type] $cityName  [城市名称]
     * @param  [type] $page      [分页]
     * @param  [type] $pageSize  [分页大小]
     * @return [type]            [description]
     */
    public static function citylist($time, $beginTime, $endTime, $cityName, $page, $pageSize) {
    	//完成订单
        $statusArr = [
        	ORDER_STATUS_FINISH_STAFF,
			ORDER_STATUS_FINISH_SYSTEM,
			ORDER_STATUS_FINISH_USER,
        ];
        //异常订单（商家取消 平台取消 会员取消）
        $statusErr = [
        	ORDER_STATUS_CANCEL_USER,
			ORDER_STATUS_CANCEL_SELLER,
			ORDER_STATUS_CANCEL_ADMIN,
        ];

        $statusArr = implode(',', $statusArr);
        $statusErr = implode(',', $statusErr);

        $orderwheresql = "";
        $cityidwheresql = "";

        if($beginTime || $endTime){
            $beginTime = Time::totime($beginTime) - 86400;
            $endTime = Time::totime($endTime);
        }

        //根据time重写 $beginTime,$endTime
        if($time)
        {
        	switch ($time) {
				case '1':
					$beginTime = UTC_DAY - 86400;
					break;

				case '7':
					$beginTime = UTC_DAY - 86400 * 7;
					break;

				case '30':
					$beginTime = UTC_DAY - 86400 * 30;
					break;
			}
			$endTime = UTC_DAY;
        }
        
        if($beginTime || $endTime)
        {
        	$orderwheresql = "AND o.create_day >= ".$beginTime." AND o.create_day <= ".$endTime;
        }

        if($cityName)
        {
        	$cityIds = Region::where('name', 'like', '%'.$cityName.'%')->lists('id');
        	$cityidwheresql = "o.city_id in(".implode(',', $cityIds).") AND";

        }

        // $sql = "SELECT
        //             o.city_id,
        //             r.name,
        //             count(distinct o1.id) as total_num,
        //             count(distinct o2.id) as finish_num,
        //             count(distinct o3.id) as abnormal_num,
        //             IFNULL(SUM(distinct o2.send_fee), 0) as total_send_fee
        //         FROM yz_order as o
        //         LEFT JOIN yz_order as o1 ON o1.city_id = o.city_id AND o1.send_fee > 0
        //         LEFT JOIN yz_order as o2 ON o2.city_id = o.city_id AND o2.status in ({$statusArr}) AND o2.send_fee > 0
        //         LEFT JOIN yz_order as o3 ON o3.city_id = o.city_id AND o3.status in ({$statusErr}) AND o3.send_fee > 0
        //         LEFT JOIN yz_region as r ON o.city_id = r.id
        //         WHERE {$cityidwheresql} o.city_id > 0 AND o.send_fee > 0 {$orderwheresql}
        //         GROUP BY
        //             city_id";
        $DB_PREFIX = env('DB_PREFIX');
        $sql = "SELECT
                o.city_id,
                r.name,
                count(o.id) AS total_num,
                SUM(

                    IF (
                        o. STATUS IN ({$statusArr}),
                        1,
                        0
                    )
                ) AS finish_num,
                SUM(

                    IF (
                        o. STATUS IN ({$statusErr}),
                        1,
                        0
                    )
                ) AS abnormal_num,
                SUM(

                    IF (
                        o. STATUS IN ({$statusArr}),
                        o.send_fee,
                        0
                    )
                ) AS total_send_fee
            FROM
                {$DB_PREFIX}order AS o
            INNER JOIN {$DB_PREFIX}region AS r ON o.city_id = r.id
            WHERE
                {$cityidwheresql}
                o.city_id > 0
            AND 
                o.send_fee > 0
                {$orderwheresql}
            GROUP BY
                city_id";

        $res = DB::select($sql);
        return $res;
    }
}