<?php namespace YiZan\Services\Buyer;

use YiZan\Utils\Time;
use YiZan\Models\PuserDoor; 
use DB, Exception;

class PuserDoorService extends \YiZan\Services\PuserDoorService {  

	/**
	 * 获取开门钥匙信息
	 */
	public static function getUserDoors($puserId){
		$list = PuserDoor::where('puser_id', $puserId)
						 ->with('door', 'puser')
						 ->get()
						 ->toArray(); 
		return self::parseData($list);
	}
    /**
     * 获取全部可用门禁钥匙
     */
    public static function getUserDoorsAll($userId){
        $list = PuserDoor::where('puser_id','>', 0)
            ->join('property_user', function($join) use($userId){
                $join->on('property_user.id','=', 'puser_door.puser_id');
            })
            ->where('property_user.user_id','=', $userId)
            ->where('property_user.access_status','=', 1)//已申请
            ->where('property_user.shakeswitch','=', 1)//已打开
            ->with('door', 'puser')
            ->get()
            ->toArray();
        return self::parseData($list);
    }
	/**
	 * 获取全部门禁钥匙
	 */
	public static function getUserDoorsKey($userId){
		$list = PuserDoor::where('puser_id','>', 0)
				->join('property_user', function($join) use($userId){
					$join->on('property_user.id','=', 'puser_door.puser_id');
				})
				->where('property_user.user_id','=', $userId)
				->with('door', 'puser')
				->get()
				->toArray();
		return self::parseData($list);
	}
	private static function parseData($data){
		$result = [];
		foreach ($data as $key => $val) { 
			$arr = [];
			$arr['doorid'] = $val['doorId']; 
			$arr['doorname'] = $val['door']['name'];
			$arr['remark'] = $val['remark'];
			$arr['expiretime'] = Time::toDate($val['endTime'], 'Y-m-d');
			$arr['userid'] = $val['puser']['mobile'];
			$arr['keyid'] = $val['lockId'];
			$arr['community'] = $val['community'];
			$arr['appkey'] = $val['appKey'];
			$arr['keyname'] = $val['door']['pid'];
            $arr['buildId'] = $val['puser']['buildId'];
            $arr['roomId'] = $val['puser']['roomId'];
            $arr['districtId'] = $val['puser']['districtId'];
			$result[] = $arr;
		}
		return $result;
	}

	/**
	 * 自定义小区门名称
	 */
	public static function updateUserDoor($puserId, $doorId, $doorname){
		$door = PuserDoor::where('puser_id', $puserId)
						 ->where('door_id', $doorId)
						 ->with('door')
						 ->first();
		if($door){
			$door->remark = $doorname;
			$door->save();
		}
		return $door;
	}

}
