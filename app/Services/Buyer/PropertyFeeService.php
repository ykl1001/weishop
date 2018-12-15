<?php namespace YiZan\Services\Buyer;


use YiZan\Models\Sellerweb\PropertyFee; 
use YiZan\Models\PropertyUser; 
use YiZan\Models\RoomFee; 
use DB, Time;
class PropertyFeeService extends \YiZan\Services\PropertyFeeService {

	/**
	 * 会员物业费列表
	 * @param $userId int 会员编号
	 * @param $sellerId int 物业公司编号
	 * @param $payitemId int 支付编号 
	 *
	 */
	public static function getLists($userId, $sellerId, $payitemId){
		$propertyUser = PropertyUser::where('user_id', $userId)
									->where('seller_id', $sellerId)
									->first();  
		$list =  PropertyFee::where('seller_id', $sellerId)
							->where('status', 0)//未支付物业费项目
							->where('build_id', '=', $propertyUser->build_id)
				            ->where('room_id', '=', $propertyUser->room_id);
		if($payitemId > 0){
			$roomFeeIds = RoomFee::where('payitem_id', $payitemId)
								 ->lists('id');

			$list->whereIn('roomfee_id', $roomFeeIds); 
		} 
		$list = $list->select('*')
					 ->with('roomfee.payitem')
					 ->get()
					 ->toArray();    
		$data['prepay'] = [];
		$data['payable'] = [];
		$curentMon = Time::toTime(Time::toDate(UTC_TIME, 'Y-m'));
		foreach ($list as $value) {
			if($curentMon > $value['createMonth']){
				$data['payable'][] = $value; 
			} else {
				$data['prepay'][] = $value; 
			}
		}
		return $data;
	}

	/**
	 * 缴费记录
	 * @param $userId int 会员编号
	 * @param $sellerId int 物业公司编号
	 */
	public static function getPayLists($userId, $sellerId){ 
		// DB::connection()->enableQueryLog();
		$paytimelists =  PropertyFee::where('property_fee.seller_id', $sellerId)
									->where('property_fee.status', 1)
									->join('property_user', function($join) use($userId){
						                $join->on('property_user.id', '=', 'property_fee.puser_id')
						                     ->where('property_user.user_id', '=', $userId);
						            }) 
						            ->select('property_fee.pay_time')
						            ->groupBy('pay_time')
						            ->orderBy('pay_time', 'DESC')
						            ->get()
						            ->toArray();
		$list = [];
		foreach ($paytimelists as $time) {
			$timeStr = date('Y-m-d', $time['payTime']);//Time::toDate($time['payTime'], 'Y-m-d');
			$list[$timeStr] = PropertyFee::where('property_fee.seller_id', $sellerId)
												 ->where('property_fee.status', 1)
												 ->whereRaw("FROM_UNIXTIME(pay_time, '%Y-%m-%d') = '".$timeStr."'")
												 ->join('property_user', function($join) use($userId){
									                	$join->on('property_user.id', '=', 'property_fee.puser_id')
									                    	 ->where('property_user.user_id', '=', $userId);
									             }) 
									             ->select('property_fee.*')
									             ->with('roomfee.payitem')
									             ->get()
									             ->toArray(); 
		}   
		// print_r(DB::getQueryLog());exit;
		return $list;
	}

    /**
     * 缴费记录
     * @param $userId int 会员编号
     * @param $sellerId int 物业公司编号
     */
    public static function getByIdsLists($userId, $ids){
        $ids = explode(",",$ids);
        $paylists =  PropertyFee::whereIn('property_fee.id', $ids)
            ->leftJoin('property_user', function($join) use($userId){
                $join->on('property_user.id', '=', 'property_fee.puser_id')
                    ->where('property_user.user_id', '=', $userId);
            })
            ->select('property_fee.*')
            ->with('roomfee.payitem')
            ->get()
            ->toArray();

        return $paylists;
    }

}
