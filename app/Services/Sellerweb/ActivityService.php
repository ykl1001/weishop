<?php
namespace YiZan\Services\Sellerweb;

use YiZan\Models\Sellerweb\Activity;
use YiZan\Models\ActivityGoods;
use YiZan\Models\ActivitySeller;
use YiZan\Models\ActivityPromotion;
use YiZan\Models\ActivityLogs;
use YiZan\Models\Order;
use YiZan\Models\User;

use YiZan\Utils\Time;
use YiZan\Utils\String;
use DB, Exception, Validator, Lang;

/**
 * 活动管理
 */
class ActivityService extends \YiZan\Services\ActivityService {

    /**
     * [refreshActicity 刷新活动]
     * @return [array] $type [需要刷新的状态]
     */
    public static function refreshActicity() {
       //进行中 1
       Activity::where('start_time', '<=', UTC_TIME)
               ->where('end_time', '>', UTC_TIME)
               ->update(['time_status' => 1]);

       //未开始 0
       Activity::where('start_time', '>', UTC_TIME)
               ->update(['time_status' => 0]);

       //已经束 -1
       Activity::where('end_time', '<=', UTC_TIME)
               ->update(['time_status' => -1]);
    }

    /**
     * [getList 列表]
     * @param  [type] $name      [活动名称]
     * @param  [type] $startTime [开始时间]
     * @param  [type] $endTime   [结束时间]
     * @param  [type] $type      [举办方：0全部, 1商家, 2平台]
     * @param  [type] $page      [description]
     * @param  [type] $pageSize  [description]
     * @return [type]            [description]
     */
    public static function getList($sellerId,$name,$startTime,$endTime,$type,$page,$pageSize)
    {
        //需要刷新的活动（未开始，进行中，已结束）
        self::refreshActicity();

        $list = Activity::whereIn('type', [4,5,6])->orderBy('id', 'desc');

        $name = empty($name) ? '' : String::strToUnicode($name,'+');
        if ($name == true) {
            $list = $list->whereRaw('MATCH(name_match) AGAINST(\'' . $name . '\' IN BOOLEAN MODE)');
        }

        //是否过期
        // if(!empty($status)){
        //     $list->where('status',$status);
        // }

        if($startTime > 0){
            $list = $list->where('start_time', '>=', Time::toTime($startTime));
        }

        if($endTime > 0){
            $list = $list->where('end_time', '<=', Time::toTime($endTime));
        }
        //获取平台指定商家列表
        $activity_ids = ActivitySeller::where('seller_id', $sellerId)->lists('activity_id');

        if ($type > 0) 
        {
            // 1 商家
            if($type == 1)
            {
                $list = $list->where('is_system', 0)->where('seller_id', $sellerId);
            }
            // 2 平台
            if($type == 2)
            {
                $list->where(function($sql) use($activity_ids){
                    $sql->where(function($query){
                            $query->where('is_system', 1)->where('use_seller', 0)->where('seller_id', 0);
                        })
                        ->orWhere(function($query) use($activity_ids){
                            $query->where('is_system', 1)->where('use_seller', 1)->whereIn('id', $activity_ids);
                        });
                });
            }

        }
        else
        {
            //全部
            $list->where(function($sql) use($sellerId, $activity_ids){
                    $sql->where(function($query) use($sellerId){
                            $query->where('is_system', 0)->where('seller_id', $sellerId);
                        })
                        ->orWhere(function($query){
                            $query->where('is_system', 1)->where('use_seller', 0)->where('seller_id', 0);
                        })
                        ->orWhere(function($query) use($activity_ids){
                            $query->where('is_system', 1)->where('use_seller', 1)->whereIn('id', $activity_ids);
                        });
                });
        }

        $totalCount = $list->count();
        $list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->with('del')
            ->get()
            ->toArray();


        return ["list"=>$list, "totalCount"=>$totalCount];
    }

    /**
     * 根据编号获取活动
     * @param  integer $id 活动编号
     * @return array       活动信息
     */
    public static function getById($sellerId, $id) {
        if($id < 1){
            return false;
        }

        $lists = Activity::with('activityGoods')->find($id);
        return $lists;  
    }


    /**
     * 作废活动
     */
    public static function cancellation($sellerId, $id){
        if($id < 1){
            return false;
        }

        $result = array(
            'code'  => 0,
            'data'  => '',
            'msg'   => Lang::get('api_system.code.28209')
        );
        $time_status = Activity::where('seller_id', $sellerId)->where('id', $id)->pluck('time_status');
        try {
            if($time_status == 1)
            {
                //进行中， 结束
                $data = [
                    'end_time' => UTC_TIME,
                    'time_status' => -1,
                ];
                Activity::where('seller_id', $sellerId)->where('id',$id)->update($data);
            }
            else
            {
                //未开始，已结束， 删除
                Activity::where('seller_id', $sellerId)->where('id',$id)->delete();
                ActivityGoods::where('seller_id', $sellerId)->where('activity_id', $id)->delete();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }

        return $result;
    }

}
