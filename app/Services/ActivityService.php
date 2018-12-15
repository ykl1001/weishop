<?php
namespace YiZan\Services;

use YiZan\Models\Activity;
use YiZan\Models\ActivityPromotion;
use YiZan\Models\ActivityLogs;
use YiZan\Models\Promotion;
use YiZan\Models\Order;
use YiZan\Models\User;
use YiZan\Models\ActivitySeller;
use YiZan\Models\PromotionSn;
use YiZan\Models\GoodsNorms;

use YiZan\Utils\Time;
use YiZan\Utils\String;
use DB, Exception, Validator, Lang;

/**
 * 活动管理
 */
class ActivityService extends BaseService {

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
               ->update(['time_status' => -1,'status' => 0]);
    }

    /**
     * 列表
     * @param string $clientType 类型
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          广告信息
     */
    public static function getList($name,$status,$startTime,$endTime,$type,$page,$pageSize)
    {
        //需要刷新的活动（未开始，进行中，已结束）
        self::refreshActicity();

        $list = Activity::where('seller_id', 0)->orderBy('id', 'desc');

        $name = empty($name) ? '' : String::strToUnicode($name,'+');
        if ($name == true) {
            $list->whereRaw('MATCH(name_match) AGAINST(\'' . $name . '\' IN BOOLEAN MODE)');
        }

        if(!empty($status)){
            $list->where('status',$status);
        }

        if($startTime > 0){
            $list->where('start_time','>=',$startTime);
        }

        if($endTime > 0){
            $list->where('end_time','<',$endTime + 86400);
        }

        if ($type > 0) {
            $list->where('type', $type);
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
    public static function getById($id) {
        if($id < 1){
            return false;
        }
        return Activity::with('promotion.promotion','activitySeller')->find($id);       
    }

    /**
     * 删除活动
     */
    public static function delete($id){
        $result = array(
            'code'	=> 0,
            'data'	=> '',
            'msg'	=> ''
        );

        return Activity::whereIn('id',$id)->delete();
    }

    /**
     * 获取分享活动
     */
    public static function getActivity($id,$type){
        $result = array(
            'code'	=> 0,
            'data'	=> '',
            'msg'	=> ''
        );

        $share_activity = Activity::where('type',$type);
        if ($id > 0) {
            $share_activity->where('id',$id);
        }
        $share_activity = $share_activity->first();
        if(!empty($share_activity) && $type == 2){
            $share_activity['brief'] = explode('fanwei',$share_activity['brief']);
        }
        $result['data'] = $share_activity;
        return $result;
    }

    /**
     * 创建活动
     */
    public static function create($name,$image,$startTime,$endTime,$content,$promotion,$type,$sort,$status){
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );

        $rules = array(
            'name'          => ['required'],
            'image'        => ['required'],
            'startTime'        => ['required'],
            'endTime'        => ['required'],
            'content'        => ['required'],
            'promotion'        => ['required']
        );

        $messages = array(
            'name.required'     => 10301,
            'image.required'   => 10110,
            'startTime.required'    => 40304,
            'endTime.required'        => 28105,
            'content.required'        => 60301,
            'promotion.required'  => 60302
        );

        $validator = Validator::make([
            'name'          => $name,
            'image'        => $image,
            'startTime'    => $startTime,
            'endTime'        => $endTime,
            'content'        => $content,
            'promotion'       => $promotion
        ], $rules, $messages);

        $startTime = Time::toTime($startTime);
        $endTime = Time::toTime($endTime)+86400;
        if($startTime > $endTime){
            $result['code'] = 40305;
            return $result;
        }

        //优惠券概率
        if(is_array($promotion) && count($promotion) >= 1) {
            $arrs = array();
            $lv = 0;
            foreach ($promotion as $v) {
                $arrs = explode(',', $v);
                $lv += $arrs[2];
            }
            if($lv != 100){
                $result['code'] = 60303;
                return $result;
            }
        }

        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        $brief = implode("fanwei",$content);
        $sort = empty($sort) ? 100:$sort;
        DB::beginTransaction();
        try {
            $activity = new Activity();
            $activity->name = $name;
            $activity->name_match = String::strToUnicode($name);
            $activity->create_time = UTC_TIME;
            $activity->start_time = $startTime;
            $activity->end_time = $endTime;
            $activity->image = $image;
            $activity->sort = $sort;
            $activity->brief = $brief;
            $activity->type = $type;
            $activity->status = $status;
            $activity->save();

            //添加优惠券
            if(is_array($promotion) && count($promotion) >= 1) {
                $activityPromotion = new ActivityPromotion();

                $arrs = array();
                foreach ($promotion as $v) {
                    $arrs = explode(',', $v);
                    $activityPromotion::insert([
                        'promotion_id' => $arrs[0],
                        'num' => $arrs[1],
                        'probability' => $arrs[2]
                    ]);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }

        return $result;
    }

    /**
     * 修改活动
     */
    public static function update($id,$name,$image,$startTime,$endTime,$content,$promotion,$type,$sort,$status){
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );

        $rules = array(
            'id'          => ['required'],
            'name'          => ['required'],
            'image'        => ['required'],
            'startTime'        => ['required'],
            'endTime'        => ['required'],
            'content'        => ['required'],
            'promotion'        => ['required']
        );

        $messages = array(
            'id.required'     => 30403,
            'name.required'     => 10301,
            'image.required'   => 30102,
            'startTime.required'    => 40304,
            'endTime.required'        => 28106,
            'content.required'        => 60301,
            'promotion.required'  => 60302
        );

        $validator = Validator::make([
            'id'          => $id,
            'name'          => $name,
            'image'        => $image,
            'startTime'    => $startTime,
            'endTime'        => $endTime,
            'content'        => $content,
            'promotion'       => $promotion
        ], $rules, $messages);

        $startTime = Time::toTime($startTime);
        $endTime = Time::toTime($endTime)+86400;
        if($startTime > $endTime){
            $result['code'] = 40305;
            return $result;
        }

        //优惠券概率
        if(is_array($promotion) && count($promotion) >= 1) {
            $arrs = array();
            $lv = 0;
            foreach ($promotion as $v) {
                $arrs = explode(',', $v);
                $lv += $arrs[2];
            }
            if($lv != 100){
                $result['code'] = 60003;
                return $result;
            }
        }
        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }


        $brief = implode("fanwei",$content);
        $sort = empty($sort) ? 100:$sort;
        DB::beginTransaction();
        try {
            $activity = Activity::where('id',$id)->first();

            $activity->id = $id;
            $activity->name = $name;
            $activity->create_time = UTC_TIME;
            $activity->start_time = $startTime;
            $activity->end_time = $endTime;
            $activity->image = $image;
            $activity->sort = $sort;
            $activity->brief = $brief;
            $activity->type = $type;
            $activity->status = $status;
            $activity->save();

            //添加优惠券
            if(is_array($promotion) && count($promotion) >= 1) {
                ActivityPromotion::truncate();
                $arrs = array();
                foreach ($promotion as $v) {
                    $arrs = explode(',', $v);
                    ActivityPromotion::insert([
                        'promotion_id' => $arrs[0],
                        'num' => $arrs[1],
                        'probability' => $arrs[2]
                    ]);
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }

        return $result;
    }

    /**
     * 获取活动的优惠券
     */
    public function getPromotionLists(){
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> ''
        );

        $share_activity_promotion = ActivityPromotion::with('promotion')->get()->toArray();

        if(!empty($share_activity_promotion)){
            $result['data'] = $share_activity_promotion;
        }else{
            $result['code'] = 99999;
        }

        return $result;
    }

    /**
     * 获取活动的优惠券
     */
    public function getPromotion($userId,$activityId){
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> ''
        );
        //判断是否已经领取过
        $activityLogs = new ActivityLogs();
        $activity_logs_count = $activityLogs->where('user_id',$userId)->where('activity_id',$activityId)->count();

        if($activity_logs_count > 0){
            $result['code'] = 60901;
        }else{
            $share_activity_promotion = ActivityPromotion::with('promotion')->orderBy('probability','desc')->get()->toArray();
            if(!empty($share_activity_promotion)){
                $index = self::GameWinner($share_activity_promotion);
                if($index == -1){
                    $result['data'] = 60902;
                }else{
                    $result['data'] = $share_activity_promotion[$index];
                }

                DB::beginTransaction();
                try {
                    $promotion_id = empty($result['data']['promotion']['id']) ? 0 : $result['data']['promotion']['id'];

                    //记录user和promotion的关系
                    $activityLogs::insert([
                        'user_id' => $userId,
                        'promotion_id' => $promotion_id,
                        'activity_id'=>$activityId
                    ]);

                    //如果获得了优惠券 就给用户赏一张 并且活动的优惠券已使用数量+1
                    if($promotion_id > 0){
                        PromotionService::createPromotion(
                            $userId,
                            $promotion_id,
                            $result['data']['promotion']['endTime'],
                            $result['data']['promotion']['expireDay'],
                            $result['data']['promotion']['perpetual'],
                            $result['data']['promotion']['goodsCate']
                        );

                        ActivityPromotion::where('id',$result['data']['id'])->increment('use_num');
                    }

                    DB::commit();
                } catch (Exception $e) {
                    DB::rollback();
                    $result['code'] = 99999;
                }

            }else{
                $result['code'] = 99999;
            }
        }



        return $result;
    }

    /**
     * 保存注册活动
     * @param int $id 活动编号
     * @param string $name 名称
     * @param string $startTime 开始时间
     * @param string $endTime 结束时间
     * @param int $status 状态 1:启用 0:禁用
     * @param int $promotionId 优惠券编号
     * @return array
     */
    public static function saveActivityReg($id,$name,$startTime,$endTime,$status,$promotionId,$num){
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );

        $rules = array(
            'name'          => ['required'],
            'startTime'        => ['required'],
            'endTime'        => ['required']
        );

        $messages = array(
            'name.required'     => 10301,
            'startTime.required'    => 40304,
            'endTime.required'        => 28106
        );

        $validator = Validator::make([
            'name'          => $name,
            'startTime'    => $startTime,
            'endTime'        => $endTime
        ], $rules, $messages);

        $startTime = Time::toTime($startTime);
        $endTime = Time::toTime($endTime)+86400;
        if($startTime > $endTime){
            $result['code'] = 40305;
            return $result;
        }
        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        $checkPro = Promotion::where('id', $promotionId)->first();
        if (!$checkPro) {
            $result['code'] = 40409;
            return $result;
        }

        if ($num < 1) {
            $result['code'] = 28118;
            return $result;
        }

        if ($id > 0) {
            $activity = Activity::where('id',$id)->first();
        } else {
            $activity = new Activity();
        }
        $activity->name = $name;
        $activity->create_time = UTC_TIME;
        $activity->start_time = $startTime;
        $activity->end_time = $endTime;
        $activity->sort = 100;
        $activity->type = 1;
        $activity->status = $status;
        $activity->promotion_id = $promotionId;
        $activity->num = $num;
        $activity->save();
        return $result;
    }


    /*
	* 游戏中奖算法
	* @access public
	* @param  [array]   $arrAward 例：
	*		array(array("id"=>1, "probability"=>0.00001, "useNum"=>'2', 'num'=>'5'),
	*			 array("id"=>2, "probability"=>0.0001, "useNum"=>'1', 'num'=>'5'))
    *   id : 编号，robability：中奖概率，Surplus：是否还有剩余奖品,useNum:已中奖数量，num:奖品数量
	* @return [int] -1表示没有中奖，
	*/
    function GameWinner($arrAward) {
        $max_num = 100;
        $awards  = [];
        foreach($arrAward as $key=>$value) {
            //如果数量大于已发放数量时
            if($value["num"] == -1 || $value["num"] > $value["useNum"]) {
                $num = $value["probability"] + count($awards);
                $awards = array_pad($awards, $num, $key);
            }
        }
        if (count($awards) < $max_num) {
            $awards = array_pad($awards, $max_num, -1);
        }
        shuffle($awards);
        return $awards[mt_rand(0, 99)];
    }

    /**
     * 检测用户是否存在
     * @param $mobile
     */
    public function checkUser($mobile){
        $data = User::where('mobile',$mobile)->first();
        if($data != NULL){
            $data = $data->toArray();
            return $data;
        }

        //没找到用户注册个账户
        $user = UserService::createUser(
            $mobile,
            '123456',
            '123456',
            'reg',
            '1'
        );
        $user['data'] = $user['data']->toArray();

        $data2 = $user['data'];
        $data2['new'] = 1;
        return $data2;
    }


    /**
     * 获取分享活动
     */
    public static function getshare($orderId){
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> ''
        );

        $order = Order::where('id',$orderId)->where('pay_status',1)->where('pay_type','!=','cashOnDelivery')->first();

        if(!empty($order)){
            $share_activity = Activity::where('type',1);
            $share_activity->where('status',1)->where('start_time','<',UTC_TIME)->where('end_time','>=',UTC_TIME)->where('money','<=',$order->pay_fee);
            $share_activity = $share_activity->first();
            if(!empty($share_activity)){
                $share_activity = $share_activity->toArray();
                $result['data'] = $share_activity;
                $result['data']['url'] = u('wap#UserCenter/obtaincoupon');
            }
        }

        return $result;
    }

    /**
     * 修改活动
     */
    public static function registerUpdate($id,$name,$startTime,$endTime,$promotion,$type,$status){
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );

        $rules = array(
            'name'          => ['required'],
            'startTime'        => ['required'],
            'endTime'        => ['required']
        );

        $messages = array(
            'name.required'     => 10301,
            'startTime.required'    => 40304,
            'endTime.required'        => 41304
        );

        $validator = Validator::make([
            'name'          => $name,
            'startTime'    => $startTime,
            'endTime'        => $endTime
        ], $rules, $messages);

        $startTime = Time::toTime($startTime);
        $endTime = Time::toTime($endTime);
        if($startTime > $endTime){
            $result['code'] = 40305;
            return $result;
        }

        if($startTime < Time::getNowDay() && $endTime < Time::getNowDay()){
            $result['code'] = 41308;
            return $result;
        }
        $is_have_count = Activity::where('type',2);
        if($id >0){
            $is_have_count->where('id','!=',$id);
        }
        $is_have_count = $is_have_count->where(function($query) use($startTime,$endTime)
        {
            $query->orWhere(function($query) use($startTime,$endTime)
            {
                $query->where('start_time', '<=', $startTime)
                    ->where('end_time', '>=',$startTime);
            })->orWhere(function($query) use($startTime,$endTime)
            {
                $query->where('start_time', '<=', $endTime)
                    ->where('end_time', '>=', $endTime);
            })->orWhere(function($query) use($startTime,$endTime)
            {
                $query->where('start_time', '<=', $startTime)
                    ->where('end_time', '>=', $endTime);
            })->orWhere(function($query) use($startTime,$endTime)
            {
                $query->where('start_time', '>=', $startTime)
                    ->where('end_time', '<=', $endTime);
            });
        })
            ->count();

        if($is_have_count > 0){
            $result['code'] = 41305;
            return $result;
        }

        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        foreach($promotion as $v){
            if(empty($v['id'])){
                $result['code'] = 40409;
                return $result;
            }
            if(empty($v['num'])){
                $result['code'] = 41307;
                return $result;
            }
        }

        $sort = empty($sort) ? 100:$sort;
        DB::beginTransaction();
        try {
            if(!empty($id)){
                $activity = Activity::where('id',$id)->first();
                $activity->id = $id;
                $activity->name = $name;
                $activity->name_match = String::strToUnicode($name);
                $activity->start_time = $startTime;
                $activity->end_time = $endTime;
                $activity->sort = $sort;
                $activity->type = $type;
                $activity->status = $status;
                $activity->save();

                ActivityPromotion::where('activity_id',$id)->delete();
                foreach ($promotion as $v) {
                    ActivityPromotion::insert([
                        'activity_id' => $id,
                        'promotion_id' => $v['id'],
                        'num' => $v['num']
                    ]);
                }
            }else{
                $activity = new Activity();
                $activity->name = $name;
                $activity->name_match = String::strToUnicode($name);
                $activity->create_time = UTC_TIME;
                $activity->start_time = $startTime;
                $activity->end_time = $endTime;
                $activity->sort = $sort;
                $activity->type = $type;
                $activity->status = $status;
                $activity->save();

                foreach ($promotion as $v) {
                    ActivityPromotion::insert([
                        'activity_id' => $activity->id,
                        'promotion_id' => $v['id'],
                        'num' => $v['num']
                    ]);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }

        return $result;
    }


    /**
     ** 修改活动
     */
    public static function shareUpdate($id,$name,$bgimage,$startTime,$endTime,$promotionId,$num,$money,$sharePromotionNum,$title,$detail,$image,$buttonName,$buttonUrl,$brief,$count,$type,$status,$limitGet){

        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );

        $rules = array(
            'name'          => ['required'],
            'bgimage'          => ['required'],
            'startTime'        => ['required'],
            'endTime'        => ['required'],
            'promotionId'          => ['required'],
            'num'          => ['required'],
            'sharePromotionNum'          => ['required'],
            'title'          => ['required'],
            'detail'          => ['required'],
            'image'          => ['required'],
            'buttonName'          => ['required'],
            'buttonUrl'          => ['required'],
            'limitGet'          => ['required']

        );
        $messages = array(
            'name.required'     => 10301,
            'startTime.required'    => 40304,
            'endTime.required'        => 41304,
            'bgimage.required'          => 41309,
            'promotionId.required'      => 41419,
            'num.required'          => 41307,
            'sharePromotionNum.required'          => 41307,
            'title.required'          => 41311,
            'detail.required'          => 41312,
            'image.required'          => 41313,
            'buttonName.required'          => 41314,
            'buttonUrl.required'          => 41315,
            'limitGet.required'          => 80888
        );
        $validator = Validator::make([
            'name'          => $name,
            'startTime'    => $startTime,
            'endTime'        => $endTime,
            'bgimage'          => $bgimage,
            'promotionId'          => $promotionId,
            'num'          => $num,
            'sharePromotionNum'          => $sharePromotionNum,
            'title'          => $title,
            'detail'          => $detail,
            'image'          => $image,
            'buttonName'          => $buttonName,
            'buttonUrl'          => $buttonUrl,
            'limitGet'          => $limitGet
        ], $rules, $messages);

        if($promotionId <= 0){
            $result['code'] = 41419;
            return $result;
        }
        if($num <= 0){
            $result['code'] = 41310;
            return $result;
        }
        if($sharePromotionNum <= 0){
            $result['code'] = 41318;
            return $result;
        }
        if(strlen($title) > 60){
            $result['code'] = 41316;
            return $result;
        }
        if(strlen($detail) > 90){
            $result['code'] = 41317;
            return $result;
        }

        $startTime = Time::toTime($startTime);
        $endTime = Time::toTime($endTime);
        if($startTime > $endTime){
            $result['code'] = 40305;
            return $result;
        }

        if($startTime < Time::getNowDay() && $endTime < Time::getNowDay()){
            $result['code'] = 41308;
            return $result;
        }

        $is_have_count = Activity::where('type',1);
        if($id >0){
            $is_have_count->where('id','!=',$id);
        }
        $is_have_count = $is_have_count->where(function($query) use($startTime,$endTime)
        {
            $query->orWhere(function($query) use($startTime,$endTime)
            {
                $query->where('start_time', '<=', $startTime)
                    ->where('end_time', '>=',$startTime);
            })->orWhere(function($query) use($startTime,$endTime)
            {
                $query->where('start_time', '<=', $endTime)
                    ->where('end_time', '>=', $endTime);
            })->orWhere(function($query) use($startTime,$endTime)
            {
                $query->where('start_time', '<=', $startTime)
                    ->where('end_time', '>=', $endTime);
            })->orWhere(function($query) use($startTime,$endTime)
            {
                $query->where('start_time', '>=', $startTime)
                    ->where('end_time', '<=', $endTime);
            });
        })
            ->count();

//        print_r($is_have_count);
//        exit;

        if($is_have_count > 0){
            $result['code'] = 41305;
            return $result;
        }

        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

//        DB::connection()->enableQueryLog();

        $sort = empty($sort) ? 100:$sort;
        DB::beginTransaction();
        try {
            if(!empty($id)){
                $activity = Activity::where('id',$id)->first();
                $activity->id = $id;
                $activity->name = $name;
                $activity->bgimage = $bgimage;
                $activity->name_match = String::strToUnicode($name);
                $activity->start_time = $startTime;
                $activity->end_time = $endTime;
                $activity->money = $money;
                $activity->share_promotion_num = $sharePromotionNum;
                $activity->title = $title;
                $activity->detail = $detail;
                $activity->image = $image;
                $activity->button_name = $buttonName;
                $activity->button_url = $buttonUrl;
                $activity->brief = $brief;
                $activity->count = $count;
                $activity->sort = $sort;
                $activity->type = $type;
                $activity->status = $status;
                $activity->limit_get = $limitGet;
                $activity->save();

                ActivityPromotion::where('activity_id',$id)->delete();
                ActivityPromotion::insert([
                    'activity_id' => $id,
                    'promotion_id' => $promotionId,
                    'num' => $num
                ]);
            }else{
                $activity = new Activity();
                $activity->name = $name;
                $activity->name_match = String::strToUnicode($name);
                $activity->bgimage = $bgimage;
                $activity->create_time = UTC_TIME;
                $activity->start_time = $startTime;
                $activity->end_time = $endTime;
                $activity->money = $money;
                $activity->share_promotion_num = $sharePromotionNum;
                $activity->title = $title;
                $activity->detail = $detail;
                $activity->image = $image;
                $activity->button_name = $buttonName;
                $activity->button_url = $buttonUrl;
                $activity->brief = $brief;
                $activity->count = $count;
                $activity->sort = $sort;
                $activity->type = $type;
                $activity->status = $status;
                $activity->limit_get = $limitGet;
                $activity->save();

                ActivityPromotion::insert([
                    'activity_id' => $activity->id,
                    'promotion_id' => $promotionId,
                    'num' => $num
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
//        print_r(DB::getQueryLog());
//        exit;
        return $result;
    }


    /**
     * 获取分享活动
     */
    public static function logs($userId,$activityId){
        $logs = ActivityLogs::where('activity_id',$activityId)->where('user_id',$userId)
            ->where('create_time','>',UTC_DAY)
            ->get();
        if($logs->id > 0){
            $result['data']['status'] = 0;
        }else{
            $result['data']['status'] = 1;
        }
        $result['data']['count'] = count($logs);
        return $result;
    }


    /**
     * [saveFull 添加满减活动]
     * @param  [type] $startTime  [开始时间]
     * @param  [type] $endTime    [结束时间]
     * @param  [type] $type       [类型：5 满减]
     * @param  [type] $fullMoney  [满足条件 满X]
     * @param  [type] $cutMoney   [满足条件 减Y]
     * @param  [type] $joinNumber [每个会员参加次数]
     * @param  [type] $useSeller  [0:全部商家 1:部分商家]
     * @param  [type] $ids        [已选择的部分商家编号]
     * @param  [type] $isSystem   [标识：是否是平台活动  1：平台 0：非平台]
     * @param  [type] $sellerId   [商家编号：isSystem=0 时才满足 平台时该数值为null]
     * @return [type]             [description]
     */
    public static function saveFull($startTime, $endTime, $type, $fullMoney, $cutMoney, $joinNumber, $useSeller, $ids, $isSystem) {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api_system.code.28208')
        );

        $rules = array(
            'startTime'    => ['required'],
            'endTime'      => ['required'],
            'type'         => ['required','numeric'],
            'fullMoney'    => ['required','numeric','min:0.01'],
            'cutMoney'     => ['required','numeric','min:0.01'],
            'useSeller'    => ['required']
        );

        $messages = array(
            'startTime.required'    => 28200, // 请选择开始时间
            'endTime.required'      => 28201, // 请选择结束时间
            'type.required'         => 28202, // 活动类型错误
            'fullMoney.required'    => 28203, // 请完善活动内容
            'fullMoney.min'         => 28204, // 满减金额必须是大于等于0的数字
            'fullMoney.numeric'     => 28204, // 满减金额必须是大于等于0的数字
            'cutMoney.required'     => 28203, // 请完善活动内容
            'cutMoney.min'          => 28204, // 满减金额必须是大于等于0的数字
            'cutMoney.numeric'      => 28204, // 满减金额必须是大于等于0的数字
            'useSeller.required'    => 28205, // 请选择适用范围
        );

        $validator = Validator::make([
            'startTime'   => $startTime,
            'endTime'     => $endTime,
            'type'        => $type,
            'fullMoney'   => $fullMoney,
            'cutMoney'    => $cutMoney,
            'useSeller'   => $useSeller
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
        if($startTime >= $endTime){
            $result['code'] = 40305;
            return $result;
        }

        //满减金额验证
        if($cutMoney > $fullMoney)
        {
            $result['code'] = 28206; // 优惠金额不能大于满减金额
            return $result;
        }
        
        //指定商家验证
        if($useSeller == 1 && count($ids) < 1)
        {
            $result['code'] = 28207; // 请至少添加一个指定商家
            return $result;
        }

        //验证次数
        if($joinNumber <= 0)
        {
            $result['code'] = 28211; //请填写参与次数;
            return $result;
        }

        //保存
        DB::beginTransaction();
        try {
            $name = "满{$fullMoney}减{$cutMoney}";
            $data = [
                'name'          => $name,
                'name_match'    => String::strToUnicode($name),
                'start_time'    => $startTime,
                'end_time'      => $endTime,
                'type'          => $type,
                'full_money'    => $fullMoney,
                'cut_money'     => $cutMoney,
                'use_seller'    => $useSeller,
                'create_time'   => UTC_TIME,
                'title'         => $name,
                'join_number'   => !empty($joinNumber) ? $joinNumber : null,
                'is_system'     => 1,
            ];

            //保存活动
            $id = Activity::insertGetId($data);
            
            //保存该活动关联的商家
            if($useSeller == 1 && count($ids) > 0)
            {
                foreach ($ids as $key => $value)
                {
                    $activitySeller[] = ['activity_id'=>$id, 'seller_id'=>$value];
                }
                ActivitySeller::insert($activitySeller);
            }
            

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }

        return $result;
    }


    /**
     * [saveFull 添加首单立减]
     * @param  [type] $startTime  [开始时间]
     * @param  [type] $endTime    [结束时间]
     * @param  [type] $type       [类型：4 首单立减]
     * @param  [type] $cutMoney   [满足条件 减X]
     * @param  [type] $useSeller  [0:全部商家 1:部分商家]
     * @param  [type] $ids        [已选择的部分商家编号]
     * @param  [type] $isSystem   [标识：是否是平台活动  1：平台 0：非平台]
     * @return [type]             [description]
     */
    public static function saveNew($startTime, $endTime, $type, $fullMoney, $cutMoney, $useSeller, $ids, $isSystem) {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api_system.code.28208')
        );

        $rules = array(
            'startTime'    => ['required'],
            'endTime'      => ['required'],
            'type'         => ['required','numeric'],
            'cutMoney'     => ['required','numeric','min:0.01'],
            'useSeller'    => ['required']
        );

        $messages = array(
            'startTime.required'    => 28200, // 请选择开始时间
            'endTime.required'      => 28201, // 请选择结束时间
            'type.required'         => 28202, // 活动类型错误
            'cutMoney.required'     => 28203, // 请完善活动内容
            'cutMoney.min'          => 28204, // 满减金额必须是大于等于0的数字
            'cutMoney.numeric'      => 28204, // 满减金额必须是大于等于0的数字
            'useSeller.required'    => 28205, // 请选择适用范围
        );

        $validator = Validator::make([
            'startTime'   => $startTime,
            'endTime'     => $endTime,
            'type'        => $type,
            'cutMoney'    => $cutMoney,
            'useSeller'   => $useSeller
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

        if($startTime >= $endTime){
            $result['code'] = 40305;
            return $result;
        }
        
        //指定商家验证
        if($useSeller == 1 && count($ids) < 1)
        {
            $result['code'] = 28207; // 请至少添加一个指定商家
            return $result;
        }

        //保存
        // DB::beginTransaction();
        try {
            if(!empty($fullMoney)){
                $name = "首单满".$fullMoney."立减{$cutMoney}";
            }else{
                $name = "首单立减{$cutMoney}";
            }

            $data = [
                'name'          => $name,
                'name_match'    => String::strToUnicode($name),
                'start_time'    => $startTime,
                'end_time'      => $endTime,
                'type'          => $type,
                'full_money'    => $fullMoney,
                'cut_money'     => $cutMoney,
                'use_seller'    => $useSeller,
                'create_time'   => UTC_TIME,
                'title'         => $name,
                'is_system'     => 1,
            ];

            //保存活动
            $id = Activity::insertGetId($data);
            
            //保存该活动关联的商家
            if($useSeller == 1 && count($ids) > 0)
            {
                foreach ($ids as $key => $value)
                {
                    $activitySeller[] = ['activity_id'=>$id, 'seller_id'=>$value];
                }
                ActivitySeller::insert($activitySeller);
            }
            

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }

        return $result;
    }


    /**
     * 作废活动
     */
    public static function cancellation($id){
        if($id < 1){
            return false;
        }

        $result = array(
            'code'  => 0,
            'data'  => '',
            'msg'   => Lang::get('api_system.code.28209')
        );
        $time_status = Activity::where('id', $id)->pluck('time_status');
        try {
            if($time_status == 1)
            {
                //进行中， 结束
                $data = [
                    'end_time' => UTC_TIME,
                    'time_status' => -1,
                ];
                Activity::where('id',$id)->update($data);
            }
            else
            {
                //未开始，已结束， 删除
                Activity::where('id',$id)->delete();
                ActivitySeller::where('activity_id', $id)->delete();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }

        return $result;
    }

    /**
     * 根据数组某一值排序
     * $arr 数组
     * $keys 需要排序的键名
     * $type asc:升序  desc 降序
     */
    public static function arraySort($arr, $keys, $type = 'asc') {
        $keysvalue = $new_array = array();
        foreach ($arr as $k => $v){
            $keysvalue[$k] = $v[$keys];
        }
        $type == 'asc' ? asort($keysvalue) : arsort($keysvalue);
        reset($keysvalue);
        foreach ($keysvalue as $k => $v) {
           $new_array[$k] = $arr[$k];
        }
        return $new_array;
    }

    /**
     * 获取商家当前所有活动信息
     */
    public static function getSellerActivity($sellerId) {
        //获取平台指定商家列表
        $activity_ids = ActivitySeller::where('seller_id', $sellerId)->lists('activity_id');
        
        //查询满减活动，如有重叠，获取最大优惠（平台+商家）
        $list = Activity::whereIn('type', [4,5,6])->where('start_time', '<', UTC_TIME)->where('end_time', '>', UTC_TIME)->where('time_status', 1)
                        ->where(function($sql) use($sellerId, $activity_ids){
                            $sql->orWhere(function($query) use($sellerId)
                            {
                                $query->where('is_system', '=', 0)
                                      ->where('seller_id', '=', $sellerId);
                            })->orWhere(function($query)
                            {
                                $query->where('is_system', '=', 1)
                                      ->where('use_seller', '=', 0)
                                      ->where('seller_id', '=', 0);
                            })->orWhere(function($query) use($activity_ids)
                            {
                                $query->where('is_system', '=', 1)
                                      ->where('use_seller', '=', 1)
                                      ->whereIn('id', $activity_ids);
                            });
                        })
                        ->with('activityGoods')
                        ->orderBy('id', 'desc')
                        ->get()
                        ->toArray();

        $data['new'] = [];
        $data['full'] = [];
        $data['special'] = [];

        $full = []; //保存已存在的满减

        //获取优先级
        foreach ($list as $key => $value) {
            //首单立减
            if($value['type'] == 4)
            {
                if(empty($data['new']))
                {
                    $data['new'] = $value;  //保存第一条首单立减
                }
                else
                {
                    if($value['cutMoney'] >= $data['new']['cutMoney'])
                    {
                        $data['new'] = $value;  //如果优惠金额大于已存在的优惠金额 优惠金额大的数据替换优惠金额小的数据
                    }
                }
            }
            //满减优惠 满X减Y
            else if($value['type'] == 5)
            {
                if(empty($data['full']))
                {
                    $data['full'][$value['id']] = $value;  //保存第一条满减优惠
                    $full[$value['fullMoney']]  = ['id'=>$value['id'], 'cutMoney'=>$value['cutMoney']];  //保存满足的金额 
                }
                else
                {
                    //X不存在 保存数据
                    if(empty($full[$value['fullMoney']]))
                    {
                        $data['full'][$value['id']] = $value;  //保存1+条满减优惠
                        $full[$value['fullMoney']]  = ['id'=>$value['id'], 'cutMoney'=>$value['cutMoney']];  //保存满足的金额 
                    }
                    //X存在 对比Y 保存Y大的数据
                    else
                    {
                        if($value['cutMoney'] >= $full[$value['fullMoney']]['cutMoney'])
                        {
                            //删除旧数据
                            unset($data['full'][$full[$value['fullMoney']]['id']]);

                            //保存新数据
                            $data['full'][$value['id']] = $value;  //保存1+条满减优惠

                            //替换X数据
                            $full[$value['fullMoney']]  = ['id'=>$value['id'], 'cutMoney'=>$value['cutMoney']];
                            
                        }
                    }
                }
            }
            //商品特价
            else if($value['type'] == 6)
            {
                foreach ($value['activityGoods'] as $k => $v) {
                    if(empty($data['special']))
                    {
                        $data['special'][$v['goodsId']] = $v;
                    }
                    else
                    {
                        //商品不存在 保存数据
                        if(empty($data['special'][$v['goodsId']]))
                        {
                            $data['special'][$v['goodsId']] = $v;
                        }
                        //商品存在 对比折扣 保存折扣大（数字小）的数据
                        else
                        {
                            if( $v['sale'] <= $data['special'][$v['goodsId']]['sale'])
                            {
                                $data['special'][$v['goodsId']] = $v;
                            }
                            
                        }
                        
                    }
                    
                }
                
            }
            
        }

        //满减从下到大排序
        $data['full'] = self::arraySort($data['full'], 'fullMoney', 'asc');

        return $data;
    }

}
