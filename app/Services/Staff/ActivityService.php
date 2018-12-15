<?php
namespace YiZan\Services\Staff;

use YiZan\Models\Sellerweb\Activity;
use YiZan\Models\ActivityGoods;
use YiZan\Models\ActivitySeller;
use YiZan\Models\ActivityPromotion;
use YiZan\Models\ActivityLogs;
use YiZan\Models\Order;
use YiZan\Models\User;
use YiZan\Models\Goods;

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
     */
    public static function getList($sellerId)
    {
        //需要刷新的活动（未开始，进行中，已结束）
        self::refreshActicity();

        $list = Activity::whereIn('type', [4,5,6])->orderBy('id', 'desc');

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
                $list = $list->where('is_system', 1)->where('use_seller', 0)->where('seller_id', 0)
                             ->orWhere('is_system', 1)->where('use_seller', 1)->whereIn('id', $activity_ids);
            }

        }
        else
        {
            //全部
            $list = $list->where('is_system', 0)->where('seller_id', $sellerId)
                         ->orWhere('is_system', 1)->where('use_seller', 0)->where('seller_id', 0)
                         ->orWhere('is_system', 1)->where('use_seller', 1)->whereIn('id', $activity_ids);
        }


        $list = $list->with('del')->get()->toArray();

        return $list;
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

    /**
     * [activityFull 添加满减活动]
     * @param  [type] $sellerId   [description]
     * @param  [type] $startTime  [description]
     * @param  [type] $endTime    [description]
     * @param  [type] $joinNumber [description]
     * @param  [type] $fullMoney  [description]
     * @param  [type] $cutMoney   [description]
     * @return [type]             [description]
     */
    public static function activityFull($sellerId, $startTime, $endTime, $joinNumber, $fullMoney, $cutMoney) {
        $result = array(
            'code'  => 0,
            'data'  => '',
            'msg'   => Lang::get('api_staff.success.add')
        );

        $rules = [
            'sellerId'          => ['required'],
            'startTime'         => ['required'],
            'endTime'           => ['required'],
            'joinNumber'        => ['required','gt:0'],
            'fullMoney'         => ['required','gt:0'],
            'cutMoney'          => ['required','gt:0'],
        ];

        $messages = [
            'sellerId.required'     => 61010,  //未获取到商家信息，请登录或刷新重试
            'startTime.required'    => 61002,  //请选择开始时间
            'endTime.required'      => 61003,  //请选择结束时间
            'joinNumber.required'   => 61021,  //请填写参与次数
            'joinNumber.gt'         => 61021,  //请填写参与次数
            'fullMoney.required'    => 61004,  //满减金额必须大于0
            'fullMoney.gt'          => 61004,  //满减金额必须大于0
            'cutMoney.required'     => 61004,  //满减金额必须大于0
            'cutMoney.gt'           => 61004,  //满减金额必须大于0
            
        ];

        $validator = Validator::make(
            [
                'sellerId'       => $sellerId,
                'startTime'      => $startTime,
                'endTime'        => $endTime,
                'joinNumber'     => $joinNumber,
                'fullMoney'      => $fullMoney,
                'cutMoney'       => $cutMoney,
            ], $rules, $messages);

        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();

            $result['code'] = $messages->first();

            return $result;
        }

        //开始结束时间验证
        $startTime = Time::toTime($startTime);
        $endTime = Time::toTime($endTime) + 86400 - 1;
        if($startTime > $endTime){
            $result['code'] = 61005; //结束时间需大于开始时间
            return $result;
        }

        if($cutMoney >= $fullMoney)
        {
            $result['code'] = 61006; //优惠金额不能大于等于满足条件的金额

            return $result;
        }
        
        //保存
        // DB::beginTransaction();
        try {
           $name = "满{$fullMoney}减{$cutMoney}";
            $data = [
                'name'          => $name,
                'name_match'    => String::strToUnicode($name),
                'start_time'    => $startTime,
                'end_time'      => $endTime,
                'type'          => 5,
                'full_money'    => $fullMoney,
                'cut_money'     => $cutMoney,
                'create_time'   => UTC_TIME,
                'title'         => $name,
                'join_number'   => !empty($joinNumber) ? $joinNumber : null,
                'is_system'     => 0,
                'seller_id'     => $sellerId,
            ];

            //保存活动
            $id = Activity::insertGetId($data);
            

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }

        return $result;
    }

    //特价商品
    public static function activitySpecial($sellerId, $startTime, $endTime, $joinNumber, $sale, $ids) {
        $result = array(
            'code'  => 0,
            'data'  => '',
            'msg'   => Lang::get('api_staff.success.add')
        );
        
        $rules = [
            'sellerId'          => ['required'],
            'startTime'         => ['required'],
            'endTime'           => ['required'],
            'joinNumber'        => ['required','gt:0'],
            'sale'              => ['required','gt:0', 'lt:10'],
            'ids'               => ['required'],
        ];

        $messages = [
            'sellerId.required'     => 61010,  //未获取到商家信息，请登录或刷新重试
            'startTime.required'    => 61002,  //请选择开始时间
            'endTime.required'      => 61003,  //请选择结束时间
            'joinNumber.required'   => 61021,  //请填写参与次数
            'joinNumber.gt'         => 61021,  //请填写参与次数
            'sale.required'         => 61011,  //请填写折扣
            'sale.gt'               => 61007,  //折扣范围0-10
            'sale.lt'               => 61007,  //折扣范围0-10
            'ids.required'          => 61008,  //请至少选择一件商品
        ];

        $validator = Validator::make(
            [
                'sellerId'       => $sellerId,
                'startTime'      => $startTime,
                'endTime'        => $endTime,
                'joinNumber'     => $joinNumber,
                'sale'           => $sale,
                'ids'            => $ids,
            ], $rules, $messages);

        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();

            $result['code'] = $messages->first();

            return $result;
        }

        //开始结束时间验证
        $startTime = Time::toTime($startTime);
        $endTime = Time::toTime($endTime) + 86400 - 1;
        if($startTime > $endTime){
            $result['code'] = 61005; //结束时间需大于开始时间
            return $result;
        }

        //验证是否添加商品
        if(count($ids) <= 0)
        {
            $result['code'] = 61008; //请至少添加一件商品
            return $result;
        }

        //保存
        // DB::beginTransaction();
        try {
           $name = "特价商品";
            $data = [
                'name'          => $name,
                'name_match'    => String::strToUnicode($name),
                'start_time'    => $startTime,
                'end_time'      => $endTime,
                'type'          => 6,
                'create_time'   => UTC_TIME,
                'title'         => $name,
                'join_number'   => !empty($joinNumber) ? $joinNumber : null,
                'is_system'     => 0,
                'seller_id'     => $sellerId,
                'sale'          => $sale,
            ];

            //保存活动
            $id = Activity::insertGetId($data);

            //保存商品
            foreach ($ids as $key => $value)
            {
                $goodsdata = Goods::find($value);

                $activityGoods[] = [
                    'activity_id'=>$id, 
                    'seller_id'=>$sellerId,
                    'goods_id'=>$goodsdata->id,
                    'price'=>$goodsdata->price,
                    'sale_price'=>$goodsdata->price * ( $sale / 10 ),
                    'sale'=>$sale,
                    'join_number'=>!empty($joinNumber) ? $joinNumber : null,
                ];
            }
            ActivityGoods::insert($activityGoods);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }

        return $result;
    }

}
