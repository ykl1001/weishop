<?php namespace YiZan\Services\Buyer;

use YiZan\Models\DoorOpenLog;
use YiZan\Models\District;
use Exception, DB, Lang, Validator, App;

class DoorOpenLogService extends \YiZan\Services\DoorOpenLogService{

    /**
     * 记录开门
     */
    public static function doorOpenRecord($puser, $errorCode, $districtId, $doorId, $buildId, $roomId){
        $puserId = null;
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg' => '成功',
        );
        foreach ($puser->toArray() as $userInfo) {
            if($userInfo['districtId'] == $districtId){
                $puserId = $userInfo['id'];
                break;
            }
        }
        $doors = PuserDoorService::getUserDoors($puserId);

        $sellerId = District::where('id', $districtId)->pluck('seller_id');
        $record = new DoorOpenLog();
        $record->puser_id 		= $puserId;
        $record->error_code 	= $errorCode;
        $record->seller_id 		= $sellerId;
        $record->district_id 	= $districtId;
        $record->door_id 		= $doorId ? $doorId : $doors[0]['doorid'];
        $record->build_id 		= $buildId;
        $record->room_id 		= $roomId;
        $record->create_time 	= UTC_TIME;
        $record->create_day 	= UTC_DAY;
        $record->save();
        return $result;
    }

}