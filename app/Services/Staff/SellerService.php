<?php namespace YiZan\Services\Staff;
use YiZan\Models\OrderRate;
use YiZan\Models\SellerMoneyLog;
use YiZan\Models\Staff\SellerStaff;
use YiZan\Models\Seller;
use YiZan\Models\Order;
use YiZan\Models\Article;
use YiZan\Models\System\SellerWithdrawMoney;
use YiZan\Models\StaffServiceTime;
use YiZan\Models\StaffServiceTimeSet;
use YiZan\Models\SellerDeliveryTime;
use YiZan\Models\System\User;
use YiZan\Models\SellerMap;
use YiZan\Models\SellerBank;
use YiZan\Models\SellerExtend;
use YiZan\Models\SystemConfig;
use YiZan\Models\UserVerifyCode;
use YiZan\Models\FreightTmp;
use YiZan\Models\FreightTmpCity;
use YiZan\Models\Staff\Region;

use YiZan\Utils\Helper;
use YiZan\Services\UserService as baseUserService;
use YiZan\Services\System\StaffStimeService as systemStaffStimeService;
use YiZan\Utils\Time;
use YiZan\Utils\String;
use DB, Lang, Exception, Request, Validator;
class SellerService extends \YiZan\Services\SellerService {

    /**
     * 更改店铺信息
     * @param $sellerId 店铺编号
     * @param $data 店铺信息
     */
    public static function update($sellerId, $data) {
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg' => Lang::get('api_staff.success.update')
        ];
        //$data = json_decode($data, true);
        // print_r($data);
        // exit; 
        if (empty($data)) {
            $result['code'] = 60008;
            return $result;
        }
        $seller = Seller::where('id', $sellerId)->first();

        if (!$seller) {
            $result['code'] = 10108;
            return $result;
        }
        $seller_data = [];
        //头像图片上传
        if (!empty($data['img'])) {
          if ($data['img'] != $seller->logo) {
            $logo = self::moveSellerImage($seller->id, $data['img']);
            $seller_data['logo'] = $logo;
            if (!$logo) {//转移图片失败
                $result['code'] = 30606;
                return $result;
            }
          }
        }
        if($data['name'] == true) {
          $seller_data['name'] = $data['name'];
        }
        if($data['status'] == true) {
          $seller_data['status'] = (int)$data['status'];
        }
        if($data['tel'] == true) {

            $isMob="/^1[0-9]{10}$/";
            $isTel="/^([0-9]{3,4})?[0-9]{7,8}$/";

            if(!preg_match($isMob, $data['tel']) && !preg_match($isTel, $data['tel']))
            {
                $result['code'] = 10102;
                return $result;
            }
            else{
                $seller_data['service_tel'] = $data['tel'];
            }
        }
        if($data['serviceRange'] == true) {
          $seller_data['address'] = $data['serviceRange'];
        }
        if($data['address'] == true) {
            $seller_data['address'] = $data['address'];
        }
        if($data['addressDetail'] == true) {
            $seller_data['address_detail'] = $data['addressDetail'];
        }
        if($data['provinceId'] == true) {
            $seller_data['province_id'] = $data['provinceId'];
        }
        if($data['cityId'] == true) {
            $seller_data['city_id'] = $data['cityId'];
        }
        if($data['areaId'] == true) {
            $seller_data['area_id'] = $data['areaId'];
        } else {
            $seller_data['area_id'] = 0;
        }
        if($data['refundAddress'] == true) {
            $seller_data['refund_address'] = $data['refundAddress'];
        }

        if($data['mapPointStr'] == true) {
            $mapPoint = Helper::foramtMapPoint($data['mapPointStr']);
            $seller_data['map_point_str'] = $mapPoint;
            $seller_data['map_point'] = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");;
        }
        if($data['brief'] == true) {
          $seller_data['brief'] = $data['brief'];
        }

        if ($data['serviceFee'] != '' && $data['serviceFee'] >= 0) {
          $seller_data['service_fee'] = (float)$data['serviceFee'];
        }
        if ($data['deliveryFee'] != '' && $data['deliveryFee'] >= 0) {
          $seller_data['delivery_fee'] = (float)$data['deliveryFee'];
        }
        if ($data['isAvoidFee'] != '' && $data['isAvoidFee'] >= 0) {
            $seller_data['is_avoid_fee'] = (int)$data['isAvoidFee'];
        }
        if ($data['avoidFee'] != '' && $data['avoidFee'] >= 0) {
            $seller_data['avoid_fee'] = (float)$data['avoidFee'];
        }

        if(is_numeric($data['isCashOnDelivery'])) {
          $seller_data['is_cash_on_delivery'] = $data['isCashOnDelivery'];
        }

        if (!empty($data['article'])) {
            $article = Article::where('seller_id', $sellerId)->first();
            if ($article) {
              Article::where('seller_id', $sellerId)->update(['content'=>$data['article']]);
            } else {
              $data = array(
                  'seller_id' => $sellerId,
                  'content' => $data['article'],
                  'status' => 1,
                );
              Article::insert($data);
            }

        }
        if (!empty($seller_data) && false === Seller::where('id', $sellerId)->update($seller_data)) {
            $result['code'] = 60004;
            return $result;
        }

        if (!empty($data['businessHour'])) {
            $weeks = $data['businessHour']['weeks'];
            $hours = $data['businessHour']['hours'];
            $stime = StaffServiceTimeSet::where('seller_id', $seller->id)->first();
            if (!$stime) { //没有则新增
                systemStaffStimeService::insert($sellerId, $weeks, $hours);
            } else {  //有则更新
                systemStaffStimeService::update($sellerId, $stime->id, $weeks, $hours);
            }
        }
        if (!empty($data['deliveryTime'])) {
            $stimes = $data['deliveryTime']['stimes'];
            $etimes = $data['deliveryTime']['etimes'];
            $dtime = SellerDeliveryTime::where('seller_id', $seller->id)->get();
            if ($dtime) {
              SellerDeliveryTime::where('seller_id', $seller->id)->delete();
            }
            foreach ($stimes as $key => $value) {
                  $delivery = new SellerDeliveryTime();
                  $delivery->seller_id     = $seller->id;
                  $delivery->stime         = $value;
                  $delivery->etime         = $etimes[$key];
                  $delivery->save();
            }

        }
        $result = self::getSellerInfo($sellerId);

        return $result;
    }


    /**
     * 营业状态/货到付款状态
     * @param $sellerId 店铺编号
     * @param $data 店铺信息
     */
    public static function isStatus($sellerId,$type,$status) {
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg' => Lang::get('api_staff.success.update')
        ];
        $seller = Seller::where('id', $sellerId)->first();
        if (!$seller) {
            $result['code'] = 10108;
            return $result;
        }
        if($type == "delivery"){
            $type = 'is_cash_on_delivery';
        }
        $seller_data["{$type}"] = (int)$status == 1 ? 1 : 0;
        if (false === Seller::where('id', $sellerId)->update($seller_data)) {
            $result['code'] = 60004;
            return $result;
        }
        return $result;
    }

    public static function getSellerInfo($sellerId) {

        $seller = Seller::where('id', $sellerId)->with('extend', 'deliveryTimes','province','city','area')->first();
        if (!$seller) {
            $result['code'] = 20001;
            return $result;
        }

        $week = Time::toDate(UTC_TIME, 'w');
        $stime = [];
        $serviceTimes = StaffServiceTime::where('seller_id', $sellerId)->where('week',$week)->get()->toArray();
        foreach ($serviceTimes as $key => $val) {
            $stime[$key]= $val['beginTime'] . '-'. $val['endTime'];
        }
		
        foreach ($seller->deliveryTimes as $key => $value) {
           $stimes[] = $value['stime'];
           $etimes[] = $value['etime'];
        }
        $seller->deliveryTime = array(
                'stimes' => !empty($stimes) ? $stimes : [],
                'etimes' => !empty($etimes) ? $etimes : [],
            );
        $orderstatus = [
            ORDER_STATUS_FINISH_SYSTEM,
            ORDER_STATUS_FINISH_USER,
            ORDER_STATUS_USER_DELETE
        ];

        $turnover = Order::where('seller_id', $seller->id)
                            ->whereIn('status', $orderstatus)->where('pay_time','>',0)
                            ->where('cancel_time',NULL)
                            ->where('create_day', UTC_DAY)
                            ->selectRaw('sum(pay_fee + system_full_subsidy + activity_new_money + discount_fee + integral_fee) as turnover')->first();
                            
        // $ordernum = Order::where('seller_id', $seller->id)->whereNotIn('status', $orderstatus)->where('create_day', UTC_DAY)->count();
        $ordernum = Order::where('seller_id', $seller->id)->whereIn('status', $orderstatus)->where('create_day', UTC_DAY)->count();
        $article = Article::where('seller_id', $seller->id)->pluck('content');
        $lockMoney = Order::where('seller_id', $seller->id)
            ->whereIn('status', [ORDER_STATUS_FINISH_SYSTEM, ORDER_STATUS_FINISH_USER])
            ->where('seller_withdraw_time', '>', 0)
            ->where('seller_withdraw_time', '>', UTC_TIME)
            ->where('pay_type', '<>', 'cashOnDelivery')
            ->sum('seller_fee'); //商家待到账金额
        $waitWithdrawMoney = SellerWithdrawMoney::where('seller_id', $seller->id)->where('status', 0)->sum('money');//提现冻结金额
        $result['data'] = array(
            'balance' => $seller->extend->money,
            'lockMoney' => $lockMoney + $waitWithdrawMoney,
            'name' => $seller->name,
            'img' => $seller->logo,
            'status'=> $seller->status,
            'deliveryTime' => $seller->deliveryTime,
            'tel' => $seller->service_tel,
            'brief' => $seller->brief,
            'serviceRange' => $seller->address,
            'address' => $seller->address,
            'addressDetail' => $seller->address_detail,
            'provinceId' => $seller->province_id,
            'cityId' => $seller->city_id,
            'areaId' => $seller->area_id,
            'mapPointStr' => $seller->map_point_str,
            'mapPosStr' => $seller->map_pos_str,
            'turnover' => $turnover->turnover,
            'orderNum' => $ordernum,
            'businessHour' => implode(' ', $stime),
            'article' => $article,
            'deliveryFee'=>$seller->delivery_fee,
            'avoidFee'=>$seller->avoid_fee,
            'isAvoidFee'=>$seller->is_avoid_fee,
            'serviceFee'=>$seller->service_fee,
            'region'=> $seller->area_id > 0 ? $seller->province->name.'-'.$seller->city->name.'-'.$seller->area->name :$seller->province->name.'-'.$seller->city->name,
            'contacts' => $seller->contacts,
            'deduct' => $seller->deduct,
            'isCashOnDelivery' => $seller->is_cash_on_delivery,
            'storeType' => $seller->store_type,
            'refundAddress' => $seller->refund_address,
        );
        $result['data']['lockCyclBankId'] = SellerBank::where('seller_id',$sellerId)->first()->id;//0;//0;//
        return $result;
    }

    public static function time($sellerId) {

        $seller = Seller::where('id', $sellerId)->first();
        if (!$seller) {
            $result['code'] = 20001;
            return $result;
        }
        $weekday = ['周日','周一','周二','周三','周四','周五','周六'];
        $beginTime = UTC_DAY + DEFAULT_BEGIN_ORDER_DATE;

        $endTime = $beginTime + 24 * 60 * 60 - 1;

        $stime =  StaffServiceTimeSet::where('seller_id', $sellerId)->first();
        $hours =[];
        $week = [];
         foreach($stime->week as $value)
         {
             $week[$value] =
                 [
                     'week'     => $value,
                     'status'   => 1,
                     'weekday'   => $weekday[$value]
                 ];
         }
        for ($x=0; $x<=6; $x++) {

            if(array_key_exists($x, $week) == false)
            {
                $week[$x] =
                    [
                        'week'      => $x,
                        'status'    => 0,
                        'weekday'   => $weekday[$x]
                    ];
            }
        }
        foreach($stime->hours as $value)
        {
            $hours[$value] =
                [
                    'hour'      => $value,
                    'status'    => 1
                ];
        }
        //当表中无预约时间数据,返回默认数据
        for (; $beginTime <= $endTime; $beginTime += SERVICE_TIME_SPAN)
        {
            $hour = Time::toDate($beginTime, 'H:i');

            if(array_key_exists($hour, $hours) == false)
            {
                $hours[$hour] =
                    [
                        'hour'      => $hour,
                        'status'    => 0
                    ];
            }
        }
        ksort($hours);
        ksort($week);
        $seller->businessHours = array(
            'weeks' => $week,
            'hours' => $hours,
        );

        $result['data'] =  $seller->businessHours;
        return $result;
    }

    public function  savetime($sellerId,$businessHour){
        $seller = Seller::where('id', $sellerId)->first();

        if (!$seller) {
            $result['code'] = 10108;
            return $result;
        }
        $weeks = $businessHour['week'];
        $hours = $businessHour['hours'];
        if (!is_array($weeks) || count($weeks) < 1 || !is_array($hours) || count($hours) < 1 ) {
            $result['code'] = 50701; //选择的天和服务时间不能为空
            return $result;
        }
        $stime = StaffServiceTimeSet::where('seller_id', $seller->id)->first();
        if (!$stime) { //没有则新增
            systemStaffStimeService::insert($sellerId, $weeks, $hours);
        } else {  //有则更新
            systemStaffStimeService::update($sellerId, $stime->id, $weeks, $hours);
        }
        $result['code'] =  0;
        $result['msg'] = Lang::get('api_staff.success.update');
        return $result;
    }
    /**
     * 获取评价列表
     * @param int $sellerId         商家编号
     * @param int $type             类型[1 未回复， 2 已回复]
     * @param int $page             页码
     * @return list
     */
    public function getOrderRates($sellerId, $type, $page){

        $star = OrderRate::where('seller_id', $sellerId)
                          ->selectRaw('sum(star)/count(*) as score')
                          ->first();

        $unReply = OrderRate::where('seller_id', $sellerId)
                            ->where('reply', '')
                            ->where('reply_time', 0)
                            ->count();

        $reply = OrderRate::where('seller_id', $sellerId)
                          ->where('reply', '<>', '')
                          ->where('reply_time', '>', 0)
                          ->count();

        $score = round($star->score,1);

        $list = OrderRate::where('seller_id', $sellerId);

        // if($type == 1){
        //     $list->where('reply', '')
        //          ->where('reply_time', 0);
        // }else{
        //     $list->where('reply', '<>', '')
        //         ->where('reply_time','>',  0);
        // }

        $list = $list->orderBy('id', 'desc')
                     ->with('user', 'order', 'goods')
                     ->skip(($page - 1) * 20)
                     ->take(20)
                     ->get()
                     ->toArray();
        foreach ($list as $k=>$v) {
            $list[$k]['userName'] = $v['user']['name'];
            if ($v['isAno'] == 1) {
                $firstStr = String::msubstr($v['user']['name'], 0, 1, 'utf-8',false);
                $lastStr = String::msubstr($v['user']['name'], -1, 1, 'utf-8',false);
                $list[$k]['userName'] = $firstStr.'***'.$lastStr;
            }

            $list[$k]['avatar'] = $v['user']['avatar'];
            $list[$k]['replyTime'] = Time::toDate($v['replyTime'], 'Y-m-d H:i');
            $list[$k]['createTime'] = Time::toDate($v['createTime'], 'Y-m-d H:i');
            unset($list[$k]['user']);
        }

        return ['score'=>$score,'unReply'=>$unReply,'reply'=>$reply,'eva'=>$list];
    }

    /**
     * 评价回复
     * @param int $sellerId             商家编号
     * @param int $id                   订单评价编号
     * @param int $content              评价内容
     * @return 评价的结果
     */
    public function replyOrderRate($sellerId, $id, $content){

        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.create_order_rate')
        );

        $orderRate = OrderRate::where('seller_id', $sellerId)
                              ->where('id', $id)
                              ->first();

        if(empty($orderRate)){
            $result['code'] = 20003;
            return $result;
        }

        if(empty($content)){
            $result['code'] = 30402;
            return $result;
        }
        $content = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '',$content);
        OrderRate::where('seller_id', $sellerId)->where('id', $id)->update(['reply'=>$content,'reply_time' => UTC_TIME]);
//        $orderRate->reply = $content;
//
//        $orderRate->reply_time = UTC_TIME;
//
//        $orderRate->save();
        return $result;
    }

    /**
     * 员工列表
     * @param int $sellerId             商家编号
     * @param int $type                 类型
     * @return 评价的结果
     */
    public function getStaffLists($sellerId, $type){
        $list = SellerStaff::where('seller_id', $sellerId)
                           ->whereIn('type', ['0', '3', $type])
                           ->where('order_status', 1)
                           ->whereNotIn('id', function($query) use ($sellerId){
                                $query->select('staff_id')
                                    ->from('staff_leave')
                                    ->where('begin_time', '<=', UTC_TIME)
                                    ->where('end_time', '>=', UTC_TIME)
                                    ->where('is_agree', 1)
                                    ->where('status', 1);
                            })
                           ->get()
                           ->toArray();
        return $list;
    }

    /**
     * 商家账单
     * @param int $sellerId             商家编号
     * @param int $status                 类型,1收入、2提现、3充值
     * @return
     */
    public function getSellerAccount($sellerId, $type, $status = 0, $page){
        $list = [];
        $seller = Seller::where('id', $sellerId)->first();
        if (!$seller) {
            $result['code'] = 10108;
            return $result;
        }

        $lists = SellerMoneyLog::where('seller_id', $sellerId)
                               ->where('money', '>', 0);

        if ($type == 1 && $status != 2) {
            if ($status == 1) {
                $lists->whereIn('type', [SellerMoneyLog::TYPE_ORDER_CONFIRM, SellerMoneyLog::TYPE_SYSTEM_RECHARGE]);
            } else if($status == 3){
                $lists->whereIn('type', [SellerMoneyLog::TYPE_SELLER_RECHARGE, SellerMoneyLog::TYPE_SYSTEM_RECHARGE]);
            } else {
                // 取消待到账的余额
                $lists->whereIn('type', [
                    SellerMoneyLog::TYPE_APPLY_WITHDRAW,
                    SellerMoneyLog::TYPE_ORDER_CONFIRM,
                    SellerMoneyLog::TYPE_SELLER_RECHARGE,
                    SellerMoneyLog::TYPE_SYSTEM_RECHARGE,
                    SellerMoneyLog::TYPE_SYSTEM_DEBIT,
                    SellerMoneyLog::TYPE_SEND_FEE
                ]);
            }
        } else {
            $lists->whereIn('type', [SellerMoneyLog::TYPE_APPLY_WITHDRAW, SellerMoneyLog::TYPE_SEND_FEE]);
        }

        $lists = $lists->orderBy('create_time', 'desc')
            ->skip(($page - 1) * 20)
            ->take(20)
            ->get()
            ->toArray();

        $statusStr = [
            ['moneyColor' => 'f_danger', 'statusColor' => 'c_e19c23', 'statusStr' => '待审核'],
            ['moneyColor' => 'f_success', 'statusColor' => 'c_24cd68', 'statusStr' => '已到账'],
            ['moneyColor' => 'f_warning', 'statusColor' => 'c_24cd68', 'statusStr' => '已拒绝'],
            ['moneyColor' => 'f_danger', 'statusColor' => 'c_e19c23', 'statusStr' => '已支出'],
        ];
        foreach ($lists as $k => $v) {
            //cz
            if($v['type'] == 'send_fee'){
                $list[$k] = $statusStr[3];
            }else{
                $list[$k] = $statusStr[$v['status']];
            }
            $list[$k]['createTime'] = yzday($v['createTime']);
            $list[$k]['status'] = $v['status'];

            if ($v['type'] == SellerMoneyLog::TYPE_APPLY_WITHDRAW) {
                $list[$k]['money'] = '-' . $v['money'];
                $list[$k]['remark'] = '提现';
            } else if($v['type'] == SellerMoneyLog::TYPE_SELLER_RECHARGE){
                $list[$k]['money'] = '+' . $v['money'];
                $list[$k]['remark'] = '充值';
            } else if($v['type'] == SellerMoneyLog::TYPE_SYSTEM_RECHARGE) {
                $list[$k]['money'] = '+' . $v['money'];
                $list[$k]['remark'] = '平台充值';
            } else if($v['type'] == SellerMoneyLog::TYPE_SYSTEM_DEBIT) {
                $list[$k]['money'] = '-' . $v['money'];
                $list[$k]['remark'] = '平台扣款';
            }  else if($v['type'] == SellerMoneyLog::TYPE_SEND_FEE) {
                $list[$k]['money'] = '-' . $v['money'];
                $list[$k]['remark'] = '配送服务费';
            } else {
                $list[$k]['money'] = '+' . $v['money'];
                $list[$k]['remark'] = $v['type'] == SellerMoneyLog::TYPE_ORDER_PAY ? '待到账' : '入余额';
            }

            if ($v['type'] == SellerMoneyLog::TYPE_ORDER_PAY) {
                $list[$k]['statusStr'] = '待到账';
            }
            if($v['type'] == 'apply_withdraw' && $v['status'] == 2){
                $list[$k]['refundInfo'] = $v['refundInfo'];
            }
        }
        $result['code'] = 0;
        $result['data'] = $list;
        return $result;
    }

    public function setSellerMap($sellerId, $option = array()) {

      if (!empty($option['address']) && !empty($option['mapPoint']) && !empty($option['mapPos'])) {
        $mapPoint = Helper::foramtMapPoint($option['mapPoint']);
        if (!$mapPoint){
            $result['code'] = 30615;    // 地图定位错误
            return $result;
        }

        $mapPos = Helper::foramtMapPos($option['mapPos']);
        if (!$mapPos) {
            $result['code'] = 30617;    // 服务范围错误
            return $result;
        }

        $data = array(
          'address' => $option['address'],
          'map_pos_str' => $mapPos["str"],
          'map_point_str' => $mapPoint,
          'map_point' => DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')"),
          'map_pos' => DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')")
          );

        Seller::where('id', $sellerId)->update($data);

        SellerMap::where('seller_id',$sellerId)->update([
                'map_pos'=>DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')"),
                'map_point'=>DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')")
                ]);

      }
      $seller = Seller::where('id', $sellerId)->first();
      $result = $seller ? $seller->toArray() : [];
      return $result;
    }

    /**
     * 更新银行卡
     * @param  integer $sellerId   机构或个人编号
     * @param  integer $id         银行信息编号
     * @param  string  $bank       银行名称
     * @param  string  $bankNo     银行卡号
     * @param  string  $mobile     验证手机
     * @param  string  $verifyCode 验证码
     * @return array               处理结果
     */
    public static function saveBankInfo($sellerId,$id, $bank, $bankNo, $mobile,$name, $verifyCode){
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );

        $rules = array(
            'bank'         => ['required'],
            'bank_no'     => ['required','size:19'],
            'mobile'        => ['required','mobile'],
            'code'      => ['required','size:6'],
            'name'      => ['required'],
        );

        $validata = array(
            'seller_id' => $sellerId,
            'bank'      => $bank,
            'bank_no'   => $bankNo,
            'mobile'    => $mobile,
            'name'      => $name,
            'code'      => $verifyCode
        );

        $messages = array(
            'bank.required'     => 10150,   // 请输入银行
            'bank_no.required'  => 10151,   // 请输入银行卡号
            'bank_no.size'      => 20010,   // 银行卡格式不正确
            'mobile.required'       => 10101,
            'mobile.mobile'     => 10102,
            'name.required'         => 10208,
            'code.required'         => 10103,
            'code.size'             => 10104,
        );

        $validator = Validator::make($validata, $rules, $messages);
        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        if( $id > 0) {
            $bankObj = SellerBank::where("seller_id",$sellerId)->where('id', $id)->first();
            if (!$bankObj) {
                $result['code'] = 10154;
                return $result;
            }
        }else{
            $bankObj = new SellerBank();
        }
        //检测验证码
        $verifyCodeId = baseUserService::checkVerifyCode($verifyCode, $mobile, UserVerifyCode::TYPE_BANKINFO);
        if (!$verifyCodeId) {
            $result['code'] = 10104;
            return $result;
        }
        $bankObj->seller_id     = $sellerId;
        $bankObj->bank          = $bank;
        $bankObj->bank_no       = $bankNo;
        $bankObj->mobile        = $mobile;
        $bankObj->name          = $name;
        DB::beginTransaction();
        try
        {
            $bankObj->save();
            $result['data'] = $bankObj;
            UserVerifyCode::destroy($verifyCodeId);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
        return $result;
    }

    /**
     * 获取银行卡信息
     * @param int $sellerId 商家编号
     * @param int $id 银行卡信息编号
     */
    public static function getBankInfo($sellerId,$id=0) {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );
        $bank = SellerBank::where('seller_id', $sellerId);
        if($id > 0){
            $bank->where('id',$id);
        }
       $bank = $bank->first();
        if (!$bank) {
            $result['code'] = 10154;
            return $result;
        }
        $result['data'] = $bank->toArray();
        //银行卡号
        $str = '**** **** **** ';
        $bankNolen = strlen($result['data']['bankNo']);
        $bankNo = String::msubstr($result['data']['bankNo'], 0, ($bankNolen-4), 'utf-8',false);
        $result['data']['bankNo'] = preg_replace('/'.$bankNo.'/', $str, $result['data']['bankNo'], 1);
        //户主名称
        $name = $result['data']['name'];
        $firstName = String::msubstr($name, 0, 1, 'utf-8',false);
        $result['data']['name'] = preg_replace('/'.$firstName.'/', '*', $name, 1);
        $result['data']['old'] =  $bank;
        return $result;
    }

    /**
     * 删除银行卡信息
     * @param int $sellerId 商家编号
     * @param int $id 银行卡信息编号
     */
    public static function delBankInfo($sellerId, $id) {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );
        $bank = SellerBank::where('seller_id', $sellerId)->where('id', $id)->first();
        if (!$bank) {
            $result['code'] = 10154;
            return $result;
        }
        //SellerBank::where('seller_id', $sellerId)->where('id', $id)->delete();
        SellerBank::where('seller_id', $sellerId)->delete();
        return $result;
    }

    /**
     * 银行卡短信验证
     * @param int $sellerId 商家编号
     * @param int $id 银行卡信息编号
     */
    public static function verifyCodeCk($verifyCode,$mobile) {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => $verifyCode,
            'msg'   => ''
        );
        //检测验证码
        $verifyCodeId = baseUserService::checkVerifyCode($verifyCode, $mobile, UserVerifyCode::TYPE_BANKINFO);
        if (!$verifyCodeId) {
            $result['code'] = 10104;
            return $result;
        }
        return $result;
    }
    /**
     * 服务人员的可提现金额
     * @return [type] [description]
     */
    public static function getAccount($sellerId){
        $data = [
            'money' => 0,
            'lockMoney' => 0,
            'waitConfirmMoney' => 0,
        ];
        DB::connection()->enableQueryLog();
        $result = SellerExtend::where('seller_id',$sellerId)->first();
        if ($result) {
            $data['money'] = $result->money;
        }
        if($data['money'] >= 100){
            $data['moneyCycle'] = $result->money;
        }else{
            $data['moneyCycle'] = 0;
        }
        $lockCycl = false;
        //验证服务人员银行卡信息
        $bankinfo = SellerBank::where('seller_id', $sellerId)->first();
        if($data['moneyCycle'] >= 100){
            if($bankinfo){
                $bankinfo = $bankinfo->toArray();
                if($result->money_cycle_day != "" || $result->money_cycle_day > 1){
                    if($result->money_cycle_day <= UTC_DAY && $data['moneyCycle'] >= 100){
                        $lockCycl = true;
                    }
                }else{
                    $lockCycl = true;
                }
            }
        }
        $data['bank'] = $bankinfo;
        $data['lockCycl'] = $lockCycl;
        $data['notice'] = SystemConfig::where('code', 'staff_bank_info')->pluck('val');
        $data['moneyCycleDay'] = Time::toDate( $result->money_cycle_day?$result->money_cycle_day:UTC_DAY,"Y-m-d");
        return $data;
    }

    /**
     * [freightList 获取运费模版列表]
     * @param  [type] $sellerId [商家编号]
     * @param  [type] $region   [null：全部查询  1：默认  2：其他城市]
     * @return [type]           [description]
     */
    public static function freightList($sellerId, $isDefault=null) {
        $list =  FreightTmp::where('seller_id', $sellerId);

        if($isDefault >= 0)
        {
            $list->where('is_default', $isDefault);
        }

        $list->with(['tmpcity' => function($query) use($sellerId){
                    $query->where('seller_id', '=', $sellerId);
                }]);

        $list = $list->get()->toArray();

        foreach ($list as $key => $value) {
            foreach ($value['tmpcity'] as $k => $v) {
                $pid = Region::where('id', $v['regionId'])->pluck('pid');
                if($pid == 0)
                {
                    $list[$key]['city'][$v['regionId']] = $v['regionId'];
                }
                else
                {
                    $list[$key]['city'][$pid][] = $v['regionId'];
                }
            }
            unset($list[$key]['tmpcity']);
        }

        return $list;
    }

    /**
     * [saveFreight 保存运费模版]
     * @param  [type] $sellerId [description]
     * @param  [type] $data     [description]
     * @return [type]           [description]
     */
    public static function saveFreight($sellerId, $data) {
        if($sellerId <= 0)
        {
            return false;
        }

        $result = [
            'code' => 0,
            'data' => '',
            'msg' => Lang::get('api_staff.success.handle')
        ];

        DB::beginTransaction();
        try
        {
            $tmpIds = FreightTmp::where('seller_id', $sellerId)->lists('id');

            if( FreightTmp::where('seller_id', $sellerId)->count() > 0 )
            {
                // 删除历史模版
                $res = FreightTmp::where('seller_id', $sellerId)->delete();
                //删除错误
                if(!$res){
                    $result['code'] = 62000;
                    return $result;
                }
            }

            if( FreightTmpCity::where('seller_id', $sellerId)->count() > 0 )
            {

                //删除历史模版城市
                $res = FreightTmpCity::where('seller_id', $sellerId)->whereIn('freight_tmp_id', $tmpIds)->delete();

                //删除错误
                if(!$res){
                    $result['code'] = 62000;
                    return $result;
                }
            }
            foreach ($data as $key => $value) {
                $update = [];
                if( is_null($value[0]) )
                {
                    $result['code'] = 62001;
                    return $result;
                }
                $update = [
                    'seller_id' => $sellerId,
                    'num' => $value[1],
                    'money' => $value[2],
                    'add_num' => $value[3],
                    'add_money' => $value[4],
                    'is_default' => $value[0] == 0 ? 1 : 0,
                ];
                $id = FreightTmp::insertGetId($update);

                $tmpCity = [];
                foreach ($value[0] as $key => $value) {
                    $tmpCity[] = [
                        'freight_tmp_id' => $id,
                        'seller_id' => $sellerId,
                        'region_id' => $value
                    ];
                }
                FreightTmpCity::insert($tmpCity);
            }

            
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
        return $result;
    }


    /**
     * [deleteFreight 删除运费模版]
     * @param  [type] $sellerId [商家编号]
     * @param  [type] $id       [运费模版编号]
     * @return [type]           [description]
     */
    public static function deleteFreight($sellerId, $id)
    {
        FreightTmp::where('seller_id', $sellerId)->where('id', $id)->delete();
        FreightTmpCity::where('seller_id', $sellerId)->where("freight_tmp_id", $id)->delete();
        return true;
    }

    /**
     * [deleteFreight 删除运费模版]
     * @param  [type] $sellerId [商家编号]
     * @param  [type] $id       [运费模版编号]
     * @return [type]           [description]
     */
    public static function getInfo($sellerId)
    {
        $seller = Seller::where('id', $sellerId)->first();

        if(empty($seller)){
            return '';
        }else{
            $seller = $seller->toArray();
            return $seller;
        }

    }

    /**
     * [sendsetget 获取配送设置信息]
     * @param  [type] $sellerId [description]
     * @return [type]           [description]
     */
    public static function sendsetget($sellerId)
    {
        $result = Seller::where("id",$sellerId)->first();

        $data['serviceFee']    = sprintf("%.2f", $result->service_fee);
        $data['deliveryFee']   = sprintf("%.2f", $result->delivery_fee);
        $data['sendWay']       = explode(',', $result->send_way);
        $data['sendType']      = $result->send_type;

        return $data;
    }

    /**
     * [sendsetSave 保存配送设置]
     * @param  [type] $sellerId    [description]
     * @param  [type] $serviceFee  [description]
     * @param  [type] $deliveryFee [description]
     * @param  [type] $sendWay     [description]
     * @param  [type] $sendType    [description]
     * @return [type]              [description]
     */
    public static function sendsetSave($sellerId, $serviceFee, $deliveryFee, $sendWay, $sendType) {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => Lang::get('api_staff.success.update')
        );

        $rules = array(
            'sellerId'    => ['required'],
            'serviceFee'  => ['required'],
            'deliveryFee' => ['required'],
            'sendWay'     => ['required'],
        );

        $messages = array(
            'sellerId.required'    => 21000,   // 未获取到商家信息，请刷新重试
            'serviceFee.required'  => 21001,   // 请填写起送价
            'deliveryFee.required' => 21002,   // 请填写配送费
            'sendWay.required'     => 21003,   // 请至少选择一个消费方式
        );

        $validata = array(
            'sellerId'      => $sellerId,
            'serviceFee'    => $serviceFee,
            'deliveryFee'   => $deliveryFee,
            'sendWay'       => $sendWay,
        );

        $validator = Validator::make($validata, $rules, $messages);
        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        if(count($sendWay) < 1)
        {
            $result['code'] = 21003;    // 请至少选择一个消费方式
            return $result;
        }

        //选择了送货上门 需要选择配送方式
        if(in_array(1, $sendWay))
        {
            if(empty($sendType))
            {
                $result['code'] = 21004;    // 请选择配送服务
                return $result;
            }
        }
        else
        {
            $sendType = null;
        }
        
        $sellerObj = Seller::where("id",$sellerId)->first();

        $sellerObj->service_fee    = $serviceFee;
        $sellerObj->delivery_fee   = $deliveryFee;
        $sellerObj->send_way       = implode(',', $sendWay);
        $sellerObj->send_type      = $sendType;
        DB::beginTransaction();
        try
        {
            $sellerObj->save();
            $result['data'] = $sellerObj;
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
        return $result;
    } 

}
