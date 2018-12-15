<?php namespace YiZan\Services\Buyer;
use YiZan\Models\GoodsStock;
use YiZan\Models\Proxy;
use YiZan\Models\SellerCateRelated;
use YiZan\Models\Promotion;
use YiZan\Services\SystemConfigService as baseSystemConfigService;
use YiZan\Services\SellerService as baseSellerService;
use YiZan\Services\InvitationService;
use YiZan\Models\Order;
use YiZan\Models\ShoppingCart;
use YiZan\Models\UserAddress;
use YiZan\Models\Seller;
use YiZan\Models\SellerExtend;
use YiZan\Models\GoodsExtend;
use YiZan\Models\Goods;
use YiZan\Models\OrderGoods;
use YiZan\Models\SellerStaff;
use YiZan\Models\GoodsStaff;
use YiZan\Models\SellerMoneyLog;
use YiZan\Models\PromotionSn;
use YiZan\Models\PromotionSellerCate;
use YiZan\Models\PromotionUnableDate;
use YiZan\Models\Refund;
use YiZan\Models\User;
use YiZan\Models\InvitationBackLog;
use YiZan\Models\SystemConfig;
use YiZan\Models\Region;
use YiZan\Models\Activity;
use YiZan\Models\ActivityGoods;
use YiZan\Models\FreightTmp;
use YiZan\Models\FreightTmpCity;


use YiZan\Models\Invitation;

use Illuminate\Support\Facades\Lang;
use YiZan\Utils\String;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Services\SellerMoneyLogService;
use YiZan\Services\ActivityService as baseActivityService;

use YiZan\Services\PushMessageService;
use DB, Config;
class OrderService extends \YiZan\Services\OrderService {

    /**
     * 催单
     * @param int $userId   会员编号
     * @param int $id       订单编号
     */
    public static function urgeOrder($userId, $id){
        $result = [
            'code' => 0,
            'data' => null,
            'msg' => ''
        ];
        $order = Order::where('user_id', $userId)
            ->where('id', $id)
            ->with('seller','staff','user')
            ->first();
        $order = $order ? $order->toArray() : [];
        try{
            if($order['seller']){
                PushMessageService::notice($order['seller']['userId'], $order['seller']['mobile'], 'order.urge', $order, ['sms', 'app'], 'seller', '3', $order['id']);
            }
            if($order['staff'] && $order['seller']['mobile'] != $order['staff']['mobile']){
                PushMessageService::notice($order['staff']['userId'], $order['staff']['mobile'], 'order.urge', $order, ['sms', 'app'], 'staff', '3', $order['id']);
            }
        } catch(Exception $e) {
            $result['code'] = 99999;
        }
        return $result;
    }

    /**
     * 下单
     * @param int $userId 用户编号
     * @param array $cartIds 购物车编号
     * @param int $addressId 地址编号
     * @param string $giftContent 贺卡内容
     * @param string $invoiceTitle 发票抬头
     * @param string $buyRemark 购物备注
     * @param string $appTime 配送时间(商品)
     * @param int $payment 支付方式 1:在线支付 0:货到付款
     * @param string $promotionSn 优惠券编号
     * @param freType string 配送方式（文字）
     * @param orderType
     * @param sendWay 配送方式（编号）
     * @param int $isUseIntegral 是否使用积分 1:是 0:否
     * @param string $detailAddress 详细地址
     * @param string $mapPoint      定位信息
     * @param int $provinceId    省份
     * @param int $cityId        城市
     * @param int $areaId        区域
     * @param string $name          名字
     * @param string $mobile        电话
     * @param string $doorplate     门牌号
     * @param string $isSaveAddress  是否保存地址
     */
    public static function create($userId, $cartIds, $addressId, $giftContent, $invoiceTitle, $buyRemark, $appTime, $payment, $promotionSnId, $freType, $orderType, $sendWay, $isUseIntegral, $detailAddress, $mapPoint, $provinceId, $cityId, $areaId, $name, $mobile, $doorplate, $isSaveAddress, $storeType ) {

        $result = [
            'code' => 0,
            'data' => null,
            'msg' => ''
        ];
        $payment = $payment != '' ? (int)$payment : $payment;

        //没有选择购物车的商品
        if (count($cartIds) < 1) {
            $result['code'] = 60501;
            return $result;
        }

        if($orderType == 2){
            $dbPrefix = DB::getTablePrefix();
            $mapPoint2 = str_replace(',', ' ', $mapPoint);
            $cz_sellerId = ShoppingCart::where('user_id', $userId)
                ->whereIn('id', $cartIds)
                ->pluck('seller_id');
            if(empty($cz_sellerId)){
                $result['code'] = 60509;
                return $result;
            }
			if(ONESELF_SELLER_ID != $cz_sellerId){
				$cresult = Seller::where('id',$cz_sellerId)->whereRaw("ST_Contains(".$dbPrefix."seller.map_pos,GeomFromText('Point({$mapPoint2})'))")->first();
				if(empty($cresult)){
					$result['code'] = 60012;
					return $result;
				}				
			}
        }

        if($addressId > 0){
            $address = UserAddress::where('user_id', $userId)->where('id', $addressId)->first();
            //地址信息不存在
            if (!$address) {
                $result['code'] = 60506;
                return $result;
            }
        } else {
            /*优化配送方式*/
            if($storeType == 1 && empty($mobile)){
                $result['code'] = 60605;
                return $result;
            }else{
                if($sendWay == 1){
                    if(empty($mobile)) {
                        $result['code'] = 60603;
                        return $result;
                    }
                    if(empty($detailAddress)){
                        $result['code'] = 60601;
                        return $result;
                    }
                    if(empty($name)){
                        $result['code'] = 60602;
                        return $result;
                    }

                    if(empty($doorplate)){
                        $result['code'] = 60604;
                        return $result;
                    }

                }
            }


            $currentAddress = UserAddress::where('user_id', $userId)
                ->where('address', $detailAddress . $doorplate)
                ->where('name', $name)
                ->where('mobile', $mobile)
                ->first();
            if(empty($currentAddress)){
                $address = new UserAddress;
                $address->is_default = 0;
                $address->user_id = $userId;
                $address->address = $detailAddress . $doorplate;
                $address->detail_address = $detailAddress;
                $address->map_point_str = $mapPoint;
                $address->map_point = DB::raw("GeomFromText('POINT(".str_replace(',', ' ', $mapPoint).")')");
                $address->province_id = $provinceId;
                $address->city_id = $cityId;
                $address->area_id = $areaId;
                $address->name = $name;
                $address->mobile = $mobile;
                $address->doorplate = $doorplate;
                if($isSaveAddress){
                    $address->save();
                    if($address->id < 0){
                        $result['code'] = 10205;
                        return $result;
                    }
                }
            } else {
                $address = $currentAddress;
            }
        }

        //一次只能下一个单
        $checkOnlySeller = ShoppingCart::where('user_id', $userId)
            ->whereIn('id', $cartIds)
            ->groupBy('seller_id')
            ->select('seller_id')
            ->get()->toArray();
        //存在多商家商品
        if (count($checkOnlySeller) > 1) {
            $result['code'] = 60507;
            return $result;
        }

        $checkOnlyGoods = ShoppingCart::where('user_id', $userId)
            ->whereIn('id', $cartIds)
            ->groupBy('type')
            ->select('type')
            ->get()->toArray();
        //服务类和商品类同时存在
        if (count($checkOnlyGoods) > 1) {
            $result['code'] = 60507;
            return $result;
        }

        $checkOnlyService = ShoppingCart::where('user_id', $userId)
            ->whereIn('id', $cartIds)
            ->where('type', '2')
            ->select('id')
            ->get()->toArray();
        //一个单只能下一个服务
        if (count($checkOnlyService) > 1) {
            $result['code'] = 60507;
            return $result;
        }


        $carts = ShoppingCart::where('user_id', $userId)
            ->whereIn('id', $cartIds)
            ->with('goods', 'stockGoods')
            ->get()->toArray();
        //有不存在的购物车信息
        if (count($cartIds) != count($carts)) {
            $result['code'] = 60502;
            return $result;
        }


        $checkSeller = Seller::where('id', $carts[0]['sellerId'])
            ->where('status',1)
            ->with('extend')
            ->first();
        //商家信息错误
        if (!$checkSeller) {
            $result['code'] = 60509;
            return $result;
        }

        //除全国店  其他店验证时间参数
        if($checkSeller->store_type != 1)
        {
            //商家是否在营业时间内
            $isCanBusiness = baseSellerService::isCanBusiness($checkSeller->id);
            if (!$isCanBusiness) {
                $result['code'] = 60514;
                return $result;
            }
        }
        // 验证是否是立即送出 是：当前时间+一个时间间隔   否：配送时间不能小于当前时间
        if($appTime == 0){
            $appTime  =UTC_TIME + $checkSeller->send_loop * 60;
            $isNow = 1;
        }else{
            $appTime = Time::toTime($appTime);
            if ($appTime < UTC_TIME) {
                $result['code'] = 60508;
                return $result;
            }
            $isNow = 0;
        }


        $goodsData  = [];
        $goodsFee = 0;
        $num = 0;
        $totalDuration = 0;
        $orderType = 1;
        $carTotalNum = 0;
        // 分享商品会员Id
        $shareUserId = 0;

        //检测结算的商品信息是否处于正常状态,库存,购买限制
        foreach ($carts as $key => $val) {

            //商品是否处于上架状态
            if ($val['goods']['status'] != 1) {
                $result['code'] = 60505;
                $result['msg'] = $val['goods']['name'].' 已下架 ';
                return $result;
                break;
            }

            //购买限制
            if ($val['num'] > $val['goods']['buyLimit'] && $val['goods']['buyLimit'] != 0) {
                $result['code'] = 60503;
                return $result;
                break;
            }

            if ($val['goods']['type'] == 1) {
                if ($val['stockGoods']) {
                    //当有规格编号的时候,检测规格的库存
                    if ($val['num'] > $val['stockGoods']['stockCount']) {
                        $result['code'] = 60504;
                        return $result;
                        break;
                    }
                } else {
                    //无规格的时候 检测商品的库存
                    if ($val['num'] > $val['goods']['stock']) {
                        $result['code'] = 60504;
                        return $result;
                        break;
                    }
                }
            } else {//下单为服务类型时,提成比例计算
                $deduct = [
                    'type' => $val['goods']['deduct_type'],
                    'val' => $val['goods']['deduct_val']
                ];
            }

            $duration = $val['goods']['unit'] == 1 ? $val['goods']['duration'] * 60 : $val['goods']['duration'];
            if($val['stockGoods']){
                $normsPrice = $val['stockGoods']['price'];
            }else{
                $normsPrice = $val['goods']['price'];
            }
            $goodsData[$key] = [
                'seller_id' => $val['sellerId'],
                'goods_name' => $val['goods']['name'],
                'goods_duration' => $duration,
                'goods_images' => $val['goods']['image'],
                'goods_norms_id' => 0,
                'goods_norms' => $val['stockGoods']['skuName'],
                'goods_id' => $val['goodsId'],
                'sku_sn' => $val['stockGoods']['skuSn'],
                'price' => $normsPrice,
                'num' => $val['num'],
                'sale_price' => 0.00,
            ];

            $goodsFee += $goodsData[$key]['price'] * $val['num'];
            $num += $val['num'];
            $totalDuration += $duration * $val['num'];
            $orderType = $val['goods']['type'];
            if($val['shareUserId'] > 0 && IS_OPEN_FX){
                $shareUserId = $val['shareUserId'];
                break;
            }
            //统计商品
            $carTotalNum += $val['num'];
        }
        //未达到商家的起送费
        if ($goodsFee < $checkSeller->service_fee && $orderType == 1) {
            $result['code'] = 60510;
            return $result;
        }

        $systemFullSubsidy = 0;     //平台满减补贴金额
        $sellerFullSubsidy = 0;     //商家满减补贴金额
        $firstOrderCutMoney = 0;    //首单立减金额
        $specialOrderCutMoney = 0;   //特价商品优惠金额
        $activityGoodsId = null;    //当前订单所选特价商品编号
        $fullOrderCutMoney = 0;     //满X减Y金额
        $activityFullId = null;     //满减活动编号
        $activityDiscountFee = 0;   //活动优惠金额统计

        //在线支付 服务优惠信息
        if($payment == 1)
        {
            //读取活动
            $activity = baseActivityService::getSellerActivity($checkSeller->id);
            //排除的状态
            $notStatus = [
                ORDER_STATUS_CANCEL_USER,
                ORDER_STATUS_CANCEL_AUTO,
                ORDER_STATUS_CANCEL_SELLER,
                ORDER_STATUS_CANCEL_ADMIN,
            ];

            $firstOrder = Order::where('user_id', $userId)->whereNotIn('status', $notStatus)->first();
            if(!$firstOrder && !empty($activity['new']))
            {
                if($activity['new']['fullMoney'] > 0){//cz
                    if($goodsFee >= $activity['new']['fullMoney']){
                        $firstOrderCutMoney = $activity['new']['cutMoney'];
                    }
                }else{
                    $firstOrderCutMoney = $activity['new']['cutMoney'];
                }
            }


            if( ! function_exists('array_column'))
            {
                $specialGoodsIds = \YiZan\Http\Controllers\YiZanViewController::array_column($activity['special'], 'goodsId');
            }
            else{
                $specialGoodsIds = array_column($activity['special'], 'goodsId');
            }
            foreach ($carts as $key => $value) {
                if( in_array($value['goodsId'], $specialGoodsIds) )
                {
                    //获取当前商品折扣信息
                    $activityGoodsInfo = $activity['special'][$value['goodsId']];
                    //获取每天满减次数，特价次数
                    $activityCount = Order::where('user_id', $userId)->whereNotIn('status', $notStatus)->where('create_day', Time::getNowDay())->where('activity_goods_id', 'like', '%,'.$activityGoodsInfo['id'].',%')->count();

                    if($activityCount < $activityGoodsInfo['joinNumber'])
                    {
                        //统计优惠的特价金额
                        if(!empty($value['stockGoods']))
                        {
                            $totalSalePrice = $value['stockGoods']['price'] * $value['num'] * (1-($activityGoodsInfo['sale']/10)); //1 -
                        }
                        else
                        {
                            $totalSalePrice = $value['price'] * $value['num'] * (1-($activityGoodsInfo['sale']/10)); //1 -
                        }

                        foreach ($goodsData as $k => $v) {
                            //(empty($v['goods_norms']) && $v['goods_id'] == $value['goodsId']) || (!empty($v['goods_norms']) && $v['goods_norms_id'] == $value['norms']['id'])  这个判断是个坑那个挖的？
                            if( !empty($v['goods_norms']) && $v['skuSn'] == $value['stockGoods']['skuSn']  && $v['goods_id'] == $value['goodsId'] )
                            {
                                $goodsData[$k]['price'] = $value['stockGoods']['price'];//$activityGoodsInfo['price']; //重写原价
                                $goodsData[$k]['sale_price'] = $value['stockGoods']['price'] * $value['num'] * ($activityGoodsInfo['sale']/10);

                            }

                        }

                        $specialOrderCutMoney += $totalSalePrice;

                        $activityGoodsId .= ','.$activityGoodsInfo['id'].',';
                    }
                }

                if($value['shareUserId'] > 0 && IS_OPEN_FX){
                    $shareUserId = $value['shareUserId'];
                    break;
                }
            }

            //验证是否满足满减(满X减Y x=总价-首单-特价优惠金额)
            $goodsFee2 = $goodsFee - $firstOrderCutMoney - $specialOrderCutMoney;

            $full_tig = ''; //活动标识，属于平台还是商家
            foreach ($activity['full'] as $key => $value) {
                if($goodsFee2 >= $value['fullMoney'])
                {
                    //统计当前满减活动当天参与次数
                    $fullCount = Order::where('user_id', $userId)->whereNotIn('status', $notStatus)->where('create_day', Time::getNowDay())->where('activity_full_id', $value['id'])->count();
                    //如果限制了次数，超出次数不享受
                    if($fullCount < $value['joinNumber'])
                    {
                        if($fullOrderCutMoney == 0)
                        {
                            $fullOrderCutMoney = $value['cutMoney'];
                        }
                        elseif($fullOrderCutMoney > 0 && $fullOrderCutMoney<= $value['cutMoney'])
                        {
                            $fullOrderCutMoney = $value['cutMoney'];
                        }

                        //平台满减补贴金额
                        if($value['isSystem'] == 1)
                        {
                            $systemFullSubsidy = $fullOrderCutMoney;
                            $full_tig = 'system';  //标识最后一次为平台
                        }
                        else
                        {
                            $sellerFullSubsidy = $fullOrderCutMoney;
                            $full_tig = 'seller';  //标识最后一次为商家
                        }

                        $activityFullId = $value['id'];
                    }
                }
            }


            if($full_tig == 'system')
            {
                $sellerFullSubsidy = 0; //如果活动为平台，清空商家补贴
            }
            else if($full_tig == 'seller')
            {
                $systemFullSubsidy = 0; //如果活动是商家，清空平台补贴
            }
            else
            {
                $sellerFullSubsidy = 0;
                $systemFullSubsidy = 0;
            }

            //活动优惠金额 = 满减+首单立减+特价商品
            $activityDiscountFee =  $firstOrderCutMoney + $fullOrderCutMoney + $specialOrderCutMoney;

        }
        //全国店根据运费模版设置运费
        if($checkSeller->store_type == 1 && $addressId > 0)
        {
            // $address = UserAddress::where('id', $addressId)->where('user_id', $userId)->first();
            $freightTmpId = null;

            //优先查询是否精确到市
            if($address->city_id > 0)
            {
                $freightTmpId = FreightTmpCity::where('seller_id', $checkSeller->id)->where('region_id', $address->city_id)->pluck('freight_tmp_id');
            }

            //其次查询是否精确到省
            if(!$freightTmpId && $address->province_id > 0)
            {
                $freightTmpId = FreightTmpCity::where('seller_id', $checkSeller->id)->where('region_id', $address->province_id)->pluck('freight_tmp_id');
            }

            //最后查询如果有精确数据获取模版运费，如果省市均无精确数据获取默模版，
            if($freightTmpId > 0)
            {
                $freightTmp = FreightTmp::where('seller_id', $checkSeller->id)->where('id', $freightTmpId)->first();
            }
            else
            {
                $freightTmp = FreightTmp::where('seller_id', $checkSeller->id)->where('is_default', 1)->first();
            }

            // 如果有模版计算运费，如果没有默认0
            if($freightTmp)
            {
                //默认在X范围内
                $checkSeller->delivery_fee = $freightTmp->money;
                //每增加Y件商品 
                if($carTotalNum > $freightTmp->num)
                {
                    $checkSeller->delivery_fee += ceil( ($carTotalNum - $freightTmp->num) / $freightTmp->add_num ) * $freightTmp->add_money;
                }
            }
            else
            {
                $checkSeller->delivery_fee = 0;
            }
        }

        //验证是否满足减满条件(免运费)
        $freightMsg = null;
        if($checkSeller->is_avoid_fee == 1 && $goodsFee >= $checkSeller->avoid_fee){
            //配送费为0
            $freight = 0;
            $freightMsg = "满".$checkSeller->avoid_fee."免运费";
        }


        //是否满足满X免配送费
        $freightInfo = NULL;
        if($checkSeller->is_avoid_fee == 1 && $goodsFee >= $checkSeller->avoid_fee){
            //重置配送费为0元
            $checkSeller->delivery_fee = 0;
            $freightInfo = "满".$checkSeller->avoid_fee."元免配送费";
        }

        //是否是到店服务（到店消费，到店自提 免配送费）
        if(in_array($sendWay, [2, 3]))
        {
            //重置配送费为0元
            $checkSeller->delivery_fee = 0;
            $freightInfo = "到店服务免配送费";
        }

        //随机选择服务人员
        $sellerId = $checkSeller->id;

        //服务全部分配，商品配送分配（到店不分配）
        if( $orderType == 2 || ($orderType == 1 && in_array($sendWay, [1])) )
        {
            $staff = SellerStaff::where('seller_id', $checkSeller->id)
                ->where('order_status', 1)
				->where('status', 1)
                ->whereNotIn('id', function($query) use ($sellerId){
                    $query->select('staff_id')
                        ->from('staff_leave')
                        ->where('begin_time', '<=', UTC_TIME)
                        ->where('end_time', '>=', UTC_TIME)
                        ->where('is_agree', 1)
                        ->where('status', 1);
                });

            if($orderType == 2) {  //服务类
                $goodsId = $goodsData[0]['goods_id'];
                $staff->whereIn('type', [0,2,3])
                    ->whereIn('id', function($query) use ($goodsId){
                        $query->select('staff_id')
                            ->from('goods_staff')
                            ->where('goods_id', $goodsId);
                    });
            }elseif($orderType == 1 && in_array($sendWay, [1])){    //商品类，配送（非到店）
                $staff->whereIn('type', [0,1,3]);
            }
            $staff = $staff->orderBy(DB::raw('RAND()'))->first();
            $allotStaffId = $staff->id;
        }
        else
        {
            $allotStaffId = 0;
        }
        $checkSeller->delivery_fee = $orderType == 1 ? $checkSeller->delivery_fee : 0;

        $totalFee = $goodsFee + $checkSeller->delivery_fee;//订单总金额


        if(!IS_OPEN_FX){
            $shareUserId = 0;
        }
		//如果总额小于首单减的金额 修改首单减金额
        if($totalFee < $firstOrderCutMoney)
        {
            $firstOrderCutMoney = $totalFee;
        }
	
        //if($shareUserId > 0){
            //优惠券是否可用
            if ($promotionSnId > 0 &&  $totalFee > 0.001 && $payment === 1) {
                $checkPro = PromotionSn::where('id', $promotionSnId)
                    ->where('user_id',$userId)
                    ->where('use_time', 0)
                    ->where('begin_time','<=',UTC_TIME)
                    ->where('expire_time','>=',UTC_TIME)
                    ->with('promotion')->first();

                //是否为不可用日期
                $isAbleDate = PromotionUnableDate::where('date_time',UTC_DAY)
                    ->where('promotion_id',$checkPro->promotion->id)
                    ->first();



                if ($checkPro->promotion->is_store == 1) {
                    $types_dsy = $checkSeller->store_type == 1 ? 5: 4;
                    if($checkPro->promotion->use_type == $types_dsy){
                        $isAble = true;
                    }
                }else{
                    //是否为指定商家
                    if ($checkPro->promotion->use_type == 3) {
                        $isAble = $checkPro->promotion->seller_id == $sellerId;
                    }
                    //是否为指定分类
                    if ($checkPro->promotion->use_type == 2) {
                        $isAble = PromotionSellerCate::where('promotion_id',$checkPro->promotion->id)
                            ->whereIn('seller_cate_id',function($query) use ($sellerId){
                                $query->select('cate_id')
                                    ->from('seller_cate_related')
                                    ->where('seller_id',$sellerId);
                            })->first();
                    }

                    if ($checkPro->promotion->use_type == 1) {
                        $isAble = true;
                    }
                }
                if(!$checkPro || $isAbleDate || !$isAble || ($checkPro->promotion->limit_money > 0 && $checkPro->promotion->limit_money > $totalFee)){
                    $result['code'] = 60516;
                    return $result;
                }
                $discountFee = $checkPro->money;
            } else {
                $discountFee = 0;
            }
       // }else{
        //    $discountFee = 0;
        //}



        $payFee = $totalFee - $discountFee - $activityDiscountFee > 0 ?  $totalFee - $discountFee - $activityDiscountFee : 0;//支付金额

        $drawnFee =(double)round(($totalFee * ($checkSeller->deduct / 100)),2) ; // 平台抽成金额

        //员工提成,配送人员暂无提成
        if ($orderType == 2) {
            $staffFee = $deduct['type'] == 1 ? $deduct['val'] : (double)round((($deduct['val'] / 100) * $totalFee),2);
        } else {
            $staffFee = 0;
        }

        //全国店，周边店自动取消订单时间
        if($checkSeller->store_type == 1)
        {
            $systemOrderPass = baseSystemConfigService::getConfigByCode('system_order_pass_all');
        }
        else
        {
            $systemOrderPass = baseSystemConfigService::getConfigByCode('system_order_pass');
        }

        $autoCancelTime = $systemOrderPass + UTC_TIME;

        $orderData  = [
            'sn' => Helper::getSn(),
            'seller_id' => $checkSeller->id,
            'user_id' => $userId,
            'name' => $address->name,
            'name_match' => String::strToUnicode($address->name),
            'mobile' => $address->mobile,
            'address_id' => $address->id,
            'address' => $address->address,
            'map_point' => $address->map_point_str,
            'province_id' => $address->province_id,
            'city_id' => $address->city_id,
            'area_id' => $address->area_id,
            'province' => Region::where('id', $address->province_id)->pluck('name'),
            'city' => Region::where('id', $address->city_id)->pluck('name'),
            'area' => Region::where('id', $address->area_id)->pluck('name'),
            'buy_remark' => $buyRemark,
            'invoice_remark' => $invoiceTitle,
            'gift_remark' => $giftContent,
            'app_time' => $appTime,
            'app_day' => Time::toDayTime($appTime),
            'create_time' => UTC_TIME,
            'create_day' => UTC_DAY,
            'fre_time' => $appTime,
            'auto_cancel_time' => $autoCancelTime,
            'duration' => $totalDuration,
            'order_type' => $orderType,
            'total_fee' => $totalFee,
            'goods_fee' => $goodsFee,
            'seller_fee' => $totalFee - $drawnFee - $sellerFullSubsidy - $specialOrderCutMoney,
            'drawn_fee' => $drawnFee,
            'discount_fee' => $discountFee,
            'freight' => $checkSeller->delivery_fee,
            'freight_info' => $freightInfo,
            'count' => $num,
            'seller_staff_id' => (int)$allotStaffId,
            'status' => ORDER_STATUS_BEGIN_USER,
            'pay_fee' => $payFee,
            'staff_fee' => $staffFee,
            'promotion_sn_id' => $promotionSnId,
            'fre_type' => $freType,
            'send_way' => $sendWay,
            'is_now' => $isNow,
            'first_level' => $checkSeller->first_level,
            'second_level' => $checkSeller->second_level,
            'third_level' => $checkSeller->third_level,
            'integral' => '0',
            'integral_fee' => '0.00',
            'activity_full_id' => $activityFullId,
            'activity_goods_id' => $activityGoodsId,
            'system_full_subsidy' => $systemFullSubsidy,
            'seller_full_subsidy' => $sellerFullSubsidy,
            'activity_full_money' => $fullOrderCutMoney,
            'activity_goods_money' => $specialOrderCutMoney,
            'activity_new_money' => $firstOrderCutMoney,
            'is_all' => $storeType,
        ];

        /**
         * 获取该商家的订单属性
         * 如果是平台托管->订单转至平台（需记录扣除平台配送费）【在线支付，线下支付商家接单，, 非到店】
         * 如果是平台众包->订单转至商家（无需记录平台配送费）
         */
        if($checkSeller->send_type == 1 && in_array($sendWay, [1]) && $payment == 1)
        {
            $send_fee = baseSystemConfigService::getConfigByCode('system_send_staff_fee');  //配送服务费
            $send_system_fee = baseSystemConfigService::getConfigByCode('system_send_fee'); //平台抽佣
            $send_staff_fee = $send_fee - $send_system_fee; //配送服务费-平台抽走的部分，剩余的归服务人员所有

            //验证订单是否满足平台配送的要求(订单金额<平台配送金额)
            if($payFee < $send_fee)
            {
                $result['code'] = 60606;
                return $result;
            }

            //满足的话写入平台抽取的金额
            $orderData['send_fee'] = $send_fee;
            $orderData['send_system_fee'] = $send_system_fee;
            $orderData['send_staff_fee'] = $send_staff_fee > 0 ? $send_staff_fee : 0; 
        }

        DB::beginTransaction();

        $integralOff = baseSystemConfigService::getConfigByCode('integral_off');
        $integralOpenType = baseSystemConfigService::getConfigByCode('integral_open_type');

        if(!$shareUserId &&  ( $integralOff && ($integralOpenType == $storeType || $integralOpenType == 2))){
            //使用积分抵现
            if ($payFee > 0.001 && $isUseIntegral == 1 && $payment === 1){
                $user = User::where('id', $userId)->first();
                $val = SystemConfig::where('code', 'cash_integral')->pluck('val');
                $limitVal = SystemConfig::where('code', 'limit_cash_integral')->pluck('val');
                $payFees = $limitVal == 100 ? ceil($payFee) : $payFee;
                $limitCashMoney = (double)round(($limitVal / 100 * $payFees), 2); //抵现上限金额
                $ableCashMoney = (double)round(($val / 100 * $user->integral), 2); // 当前可抵现金额
                $cashMoney = $ableCashMoney <= $limitCashMoney ? $ableCashMoney : $limitCashMoney; //最终抵现金额
                $integral = (int)($cashMoney / ($val / 100)); // 抵现积分
                $cashMoney = (double)round(($integral * ($val / 100)), 2); //根据积分算抵现金额
                //会员有积分的时候扣除相应的积分
                if ($user->integral >= $integral) {
                    $user->integral = $user->integral - $integral;
                    $user->save();
                    $orderData['integral'] = $integral;
                    $orderData['integral_fee'] = $cashMoney >= $payFee ? $payFee : $cashMoney;
                    $payFee = $orderData['pay_fee'] = ($payFee - $cashMoney) < 0 ? 0 : ($payFee - $cashMoney);
                }
            }
        }

        if ($payFee == 0 || $payment === 0) {
            $orderData['pay_type'] = $payFee == 0 ? 'free' : 'cashOnDelivery';
            $orderData['pay_time'] = UTC_TIME;
            $orderData['pay_status'] = ORDER_PAY_STATUS_YES;
            $orderData['status'] = $payFee == 0 ? ORDER_STATUS_PAY_SUCCESS : ORDER_STATUS_PAY_DELIVERY;
            //$orderData['seller_confirm_time'] = UTC_TIME;
            //平台抽成,扣除余额
            if ($drawnFee > 0.001 && $payment === 0) {
                if ((int)$checkSeller->is_cash_on_delivery != 1) {
                    $result['code'] = '60515';
                    return $result;
                }
                $deduction = SellerExtend::where('seller_id', $checkSeller->id)
                    ->where('money', '>=', $drawnFee)
                    ->decrement('money', $drawnFee);
                if (!$deduction) {//余额不足
                    $result['code'] = '60515';
                    return $result;
                }
            }


        }

        //计算营业额  额业额 ＝ 实付金额+平台满减+首单减+优惠券+积分抵扣
        $turnover = $payFee + $systemFullSubsidy + $firstOrderCutMoney + $discountFee + $orderData['integral_fee'];

        //计算佣金  佣金=（实付金额+平台满减+首单减+优惠券+积分抵扣）* 佣金比例 = 营业额 * 佣金比例
        
        // $orderData['drawn_fee'] = (double)round(($turnover * ($checkSeller->deduct / 100)),2) ; // 平台抽成金额(包含配送费)
        $orderData['drawn_fee'] = (double)round((($turnover - $orderData['freight']) * ($checkSeller->deduct / 100)),2) ; // 平台抽成金额(不包含配送费)

        //计算入账金额  入账金额 = （实付金额+平台满减+首单减+优惠券+积分抵扣）- 佣金 = 营业额 - 佣金
        $orderData['seller_fee'] = $turnover - $orderData['drawn_fee'];
        $orderData['share_user_id'] = 0;
        if($payment && IS_OPEN_FX){
            $orderData['share_user_id'] = $shareUserId;
        }
        $bln = false;
        $orderId = Order::insertGetId($orderData);
        if ((int)$orderId > 0) {
            //写入积分日志
            if ($orderData['integral'] > 0) {
                \YiZan\Services\UserIntegralService::createIntegralLog($userId, 2, 4, $orderId, $payFee, $orderData['integral']);
            }
            if($orderData['pay_type'] == 'cashOnDelivery'){
                //写入扣款日志
                SellerMoneyLogService::createLog(
                    $checkSeller->id,
                    SellerMoneyLog::TYPE_DELIVERY_MONEY,
                    $orderId,
                    -$drawnFee,
                    '现金支付订单' . $orderData['sn'] . '，佣金扣款',
                    1
                );
            }
            if($payment && IS_OPEN_FX && $orderData['status'] == ORDER_STATUS_PAY_SUCCESS){
                self::invitationOrder($orderId,$orderData,$checkSeller->store_type,$shareUserId);
            }
            try {
                foreach ($goodsData as $key=>$val) {
                    $goodsData[$key]['order_id'] = $orderId;
                    //更新商品扩展表销量
                    GoodsExtend::where('goods_id', $val['goods_id'])->increment('sales_volume', $val['num']);
                    if ($orderType == 1) {
                        if ($val['sku_sn']) {
                            //更新商品规格表库存
                            GoodsStock::where('goods_id', $val['goods_id'])->where('sku_sn', $val['sku_sn'])->decrement('stock_count', $val['num']);
                            GoodsStock::where('sku_sn', $val['sku_sn'])->where('sku_sn', $val['sku_sn'])->increment('sale_count', $val['num']);
                        } else {
                            //更新商品表库存
                            Goods::where('id', $val['goods_id'])->decrement('stock', $val['num']);
                        }
                    }


                }
                //插入订单商品明细表
                OrderGoods::insert($goodsData);
                //更新商家扩展表
                baseSellerService::incrementExtend($checkSeller->id,'order_count');//增加商家销量
                if($orderData['seller_fee'] > 0.001 && $orderData['pay_status'] == 1 && $orderData['pay_type'] != 'cashOnDelivery') {
                    baseSellerService::incrementExtend($checkSeller->id, 'wait_confirm_money', $orderData['seller_fee']);
                }

                //删除已下单的购物车
                ShoppingCart::whereIn('id', $cartIds)->delete();

                //已使用的优惠券更新
                if ((int)$promotionSnId > 0) {
                    PromotionSn::where('id', $promotionSnId)->update(['use_time' => UTC_TIME]);
                }

                $result['msg'] = Lang::get('api.success.user_create_order');
                $result['data'] =  self::getOrderById($userId,$orderId);
                $bln = true;
                DB::commit();

            } catch (Exception $e) {
                $result['code'] = '60013';
                DB::rollback();
            }
        }
        if ($bln && $orderData['pay_status'] == ORDER_PAY_STATUS_YES) {
            //$result['data'] = $result['data']->toArray();
            try {
                PushMessageService::notice( $result['data']['seller']['userId'],  $result['data']['seller']['mobile'], 'order.create',  $result['data'],['sms', 'app'],'seller','3',$orderId, "neworder.caf");
                if($result['data']['staff'] && $result['data']['seller']['userId'] != $result['data']['staff']['userId']){
                    PushMessageService::notice( $result['data']['staff']['userId'],  $result['data']['staff']['mobile'], 'order.create',  $result['data'],['sms', 'app'],'staff','3',$orderId, "neworder.caf");
                }
            } catch (Exception $e) {

            }
        }

        if(FANWEFX_SYSTEM)
        {
            $user_fanwe_id = User::where('id', $userId)->pluck('fanwe_id');
            $seller_fanwe_id = User::where('id', $checkSeller->user_id)->pluck('fanwe_id');
            //$OrderGoodsId = OrderGoods::where('order_id', $orderId)->lists('id');

            $path = 'share_profit';
            $args = [
                'appsys_id' => Config::get('app.fanwefx.appsys_id'),
                'order_no'  => $orderData['sn'],
                'order_desc' => '',
                'order_time' => Time::todate($orderData['create_time']),
                'order_user_id' => $user_fanwe_id,
                'shop_user_id' => $seller_fanwe_id,
                'order_money' => $orderData['pay_fee'],
                'settlement_status' => 0,
                'base_money'     => $orderData['pay_fee'],
                'limit_money'    => $checkSeller->limit_money,
                'scheme_id'      => $checkSeller->scheme_id,
                'passage_id'     => null, //$checkSeller->passage_id,
            ];

            //$user = User::where('id', $userId)->first();

            //如果会员是分销的会员
            if($user_fanwe_id > 0)
            {
                $fxres = \YiZan\Services\FxBaseService::requestApi($path, $args);
                //保存分销数据
                Order::where('id', $orderId)->update(['fanwefx_data'=>serialize($fxres), 'fanwefx_status'=>$fxres[0]['status']]);
            }
        }

        return $result;
    }


    /**
     * 根据订单编号获取订单
     * @param  [int] $userId     [会员编号]
     * @param  [int] $orderId    [订单编号]
     * @param  [int] $autoAssign [周边店自动分配]
     * @return [object]          [订单对象]
     */
    public static function getOrderById($userId, $orderId) {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );
        //检测是否存在过期商品的订单
        $activityGoodsIsChange = self::activityGoodsIsChange($userId, $orderId);  //1=正常 -1存在过期商品

        //查询订单
        $res = Order::where('user_id', $userId)->where('id', $orderId)->with('goods','cartSellers','seller','staff','refundCount','user')->first();
        if(!$res){
            return  false;
        }
        $res = $res->toArray();
        $res['invoiceTitle']   = $res['invoiceRemark'];
        $res['price']          = $res['totalFee'];
        $res['giftContent']          = $res['giftRemark'];
        $res['sellerName']          = $res['seller']['name'];
        $res['shopName']          = $res['seller']['name'];
        $res['sellerTel']          = $res['seller']['serviceTel'] ? $res['seller']['serviceTel'] : $res['seller']['mobile'];
        $res['staffName']          = $res['staff']['name'];
        $res['staffMobile']          = $res['staff']['mobile'];
        $res['countGoods']          = Goods::where('type', 1)->where('seller_id', $res['seller']['id'])->where('status', 1)->count('id');
        $res['countService']          = Goods::where('type', 2)->where('seller_id', $res['seller']['id'])->where('status', 1)->count('id');
        // unset($res['seller'],$res['staff']);
        if(!$res['seller']){
            $res['sellerId']  = 0;
        }
        $res['activityGoodsIsChange'] = $activityGoodsIsChange;

        return $res;
    }

    /**
     * [activityGoodsIsChange 验证是否存在活动过期的商品]
     * @return [type] [description]
     */
    public static function activityGoodsIsChange($userId, $orderId) {
        $ids = Order::where('id', $orderId)->pluck('activity_goods_id');
        $ids = str_replace(',,', ',', $ids);
        $ids = (array_filter(array_unique(explode(',', $ids))));

        $all = ActivityGoods::whereIn('id', $ids)->get()->toArray();

        if(count($all) != count($ids))
        {
            //取消订单 
            self::cancelOrder($userId, $orderId, '订单信息已经更改，请重新下单');
            return -1;
        }

        foreach ($all as $key => $value) {

            $activity = Activity::find($value['activityId']);
            if(!$activity || $activity->end_time <= UTC_TIME)
            {
                //取消订单 
                self::cancelOrder($userId, $orderId, '订单信息已经更改，请重新下单');
                return -1;
                break;
            }
        }

        return 1;  //允许去支付
    }

    /**
     * 订单计算
     * @param int       $userId       会员编号
     * @param int       $cartIds      购物车编号
     * @param string    $promotionSn  优惠券编号
     * @param int       $addressId    会员地址编号（全国店计算运费）
     */
    public static function orderCompute($userId, $cartIds, $promotionSnId, $addressId,$cancel,$price){

        $check = ShoppingCart::where('user_id', $userId)
            ->whereIn('id', $cartIds);

        $checkSeller = $check->groupBy('seller_id')
            ->lists('seller_id');

        if (count($checkSeller) > 1) {
            $result['code'] = 60511;
            return $result;
        }

        $seller = Seller::where('id', $checkSeller[0])
            ->with('extend','sellerCate')
            ->first()
            ->toArray();

        $checkSeller = $check->groupBy('type')
            ->select('type')
            ->get()->toArray();

        if (count($checkSeller) > 1) {
            $result['code'] = 60512;
            return $result;
        }

        $checkService = $check->where('type', 2)
            ->count();

        if ($checkService > 1) {
            $result['code'] = 60513;
            return $result;
        }

        $data = ShoppingCart::where('user_id', $userId)
            ->whereIn('id', $cartIds)
            ->with('goods', 'stockGoods')
            ->get()
            ->toArray();

        $totalFee = 0;
        $goodsFee = 0;
        $discountFee = 0;
        $freight = $data[0]['type'] == 1 ? $seller['deliveryFee'] : 0;;
        $payFee = 0;
        $isCashOnDelivery = (int)$seller['isCashOnDelivery'];
        $isShowPromotion = 0;



        if(!IS_OPEN_FX){
            //优惠券金额
            $pro = PromotionSn::where('user_id',$userId)
                ->where('id', $promotionSnId)
                ->where('use_time', 0)
                ->where('begin_time', '<=', UTC_TIME)
                ->where('expire_time', '>=', UTC_TIME)
                ->where('is_del',0)
                ->first();
            foreach ($data as $key => $value) {
                if($value['stockGoods']){
                    $goodsFee += $value['stockGoods']['price'] * $value['num'];
                } else {
                    $goodsFee += $value['price'] * $value['num'];
                }
            }
        }else{
            if($data['shareUserId'] <= 0){
                //优惠券金额
                $pro = PromotionSn::where('user_id',$userId)
                    ->where('id', $promotionSnId)
                    ->where('use_time', 0)
                    ->where('begin_time', '<=', UTC_TIME)
                    ->where('expire_time', '>=', UTC_TIME)
                    ->where('is_del',0)
                    ->first();
                foreach ($data as $key => $value) {
                    if($value['stockGoods']){
                        $goodsFee += $value['stockGoods']['price'] * $value['num'];
                    } else {
                        $goodsFee += $value['price'] * $value['num'];
                    }
                }
            }
        }
        //读取活动
        $activity = baseActivityService::getSellerActivity($seller['id']);
        //排除的状态
        $notStatus = [
            ORDER_STATUS_CANCEL_USER,
            ORDER_STATUS_CANCEL_AUTO,
            ORDER_STATUS_CANCEL_SELLER,
            ORDER_STATUS_CANCEL_ADMIN,
        ];

        //平台满减补贴金额
        $systemFullSubsidy = 0;
        //商家满减补贴金额
        $sellerFullSubsidy = 0;

        //验证是否满足首单立减
        $firstOrderCutMoney = 0;

        $firstOrder = Order::where('user_id', $userId)->whereNotIn('status', $notStatus)->first();
        if(!$firstOrder && !empty($activity['new']))
        {
            if($activity['new']['fullMoney'] > 0){//cz
                if($price >= $activity['new']['fullMoney']){
                    $firstOrderCutMoney = $activity['new']['cutMoney'];
                }
            }else{
                $firstOrderCutMoney = $activity['new']['cutMoney'];
            }
        }

        //验证是否满足特价商品
        $specialOrderCutMoney = 0;
        if( ! function_exists('array_column'))
        {
            $specialGoodsIds = \YiZan\Http\Controllers\YiZanViewController::array_column($activity['special'], 'goodsId');
        }
        else{
            $specialGoodsIds = array_column($activity['special'], 'goodsId');
        }

        //商品总数
        $carTotalNum = 0;

        foreach ($data as $key => $value) {
            if( in_array($value['goodsId'], $specialGoodsIds) )
            {
                //获取当前商品折扣信息
                $activityGoodsInfo = $activity['special'][$value['goodsId']];

                //获取每天满减次数，特价次数
                $activityCount = Order::where('user_id', $userId)->whereNotIn('status', $notStatus)->where('create_day', Time::getNowDay())->where('activity_goods_id', 'like', '%,'.$activity['special'][$value['goodsId']]['id'].',%')->count();

                if($activityCount < $activityGoodsInfo['joinNumber'])
                {
                    //统计优惠的特价金额
                    if(!empty($value['stockGoods']))
                    {
                        $specialOrderCutMoney += $value['stockGoods']['price'] * $value['num'] * (1-($activityGoodsInfo['sale']/10));//1 -
                    }
                    else
                    {
                        $specialOrderCutMoney += $value['price'] * $value['num'] * (1-($activityGoodsInfo['sale']/10));//1 -
                    }

                }
            }

            //统计商品
            $carTotalNum += $value['num'];
        }
        //验证是否满足满减(满X减Y x=总价-首单-特价优惠金额)
        $fullOrderCutMoney = 0;

        $goodsFee2 = $goodsFee - $firstOrderCutMoney - $specialOrderCutMoney;

        $full_tig = '';
        foreach ($activity['full'] as $key => $value) {
            $systemFullSubsidy = 0; //防止商家和平台满减共存
            $sellerFullSubsidy = 0; //防止商家和平台满减共存

            if($goodsFee2 >= $value['fullMoney'])
            {
                //统计当前满减活动当天参与次数
                $fullCount = Order::where('user_id', $userId)->whereNotIn('status', $notStatus)->where('create_day', Time::getNowDay())->where('activity_full_id', $value['id'])->count();
                //如果限制了次数，超出次数不享受
                if( $fullCount < $value['joinNumber'])
                {
                    if($fullOrderCutMoney == 0)
                    {
                        $fullOrderCutMoney = $value['cutMoney'];
                    }
                    elseif($fullOrderCutMoney > 0 && $fullOrderCutMoney<= $value['cutMoney'])
                    {
                        $fullOrderCutMoney = $value['cutMoney'];
                    }

                    //平台满减补贴金额
                    if($value['isSystem'] == 1)
                    {
                        $systemFullSubsidy = $fullOrderCutMoney;
                        $full_tig = 'system';  //标识最后一次为平台
                    }
                    else
                    {
                        $sellerFullSubsidy = $fullOrderCutMoney;
                        $full_tig = 'seller';  //标识最后一次为商家
                    }
                }
            }
        }

        if($full_tig == 'system')
        {
            $sellerFullSubsidy = 0; //如果活动为平台，清空商家补贴
        }
        else if($full_tig == 'seller')
        {
            $systemFullSubsidy = 0; //如果活动是商家，清空平台补贴
        }
        else
        {
            $sellerFullSubsidy = 0;
            $systemFullSubsidy = 0;
        }

        //全国店根据运费模版设置运费
        if($seller['storeType'] == 1 && $addressId > 0)
        {
            $address = UserAddress::where('id', $addressId)->where('user_id', $userId)->first();
            $freightTmpId = null;

            //优先查询是否精确到市
            if($address->city_id > 0)
            {
                $freightTmpId = FreightTmpCity::where('seller_id', $seller['id'])->where('region_id', $address->city_id)->pluck('freight_tmp_id');
            }

            //其次查询是否精确到省
            if(!$freightTmpId && $address->province_id > 0)
            {
                $freightTmpId = FreightTmpCity::where('seller_id', $seller['id'])->where('region_id', $address->province_id)->pluck('freight_tmp_id');
            }

            //最后查询如果有精确数据获取模版运费，如果省市均无精确数据获取默模版，
            if($freightTmpId > 0)
            {
                $freightTmp = FreightTmp::where('seller_id', $seller['id'])->where('id', $freightTmpId)->first();
            }
            else
            {
                $freightTmp = FreightTmp::where('seller_id', $seller['id'])->where('is_default', 1)->first();
            }

            // 如果有模版计算运费，如果没有默认0
            if($freightTmp)
            {
                //默认在X范围内
                $freight = $freightTmp->money;
                //每增加Y件商品 
                if($carTotalNum > $freightTmp->num)
                {
                    $freight += ceil( ($carTotalNum - $freightTmp->num) / $freightTmp->add_num ) * $freightTmp->add_money;
                }
            }
            else
            {
                $freight = 0;
            }
        }

        //验证是否满足减满条件(免运费)
        $freightMsg = null;
        if($seller['isAvoidFee'] == 1 && $goodsFee >= $seller['avoidFee']){
            //配送费为0
            $freight = 0;
            $freightMsg = "满".$seller['avoidFee']."免运费";
        }

        //可用优惠券数量
        $sellerId = $seller['id'];
        $totalMoney = $goodsFee + $freight;
        //总金额等于商品金额加上配送费减去优惠金额
        // $totalFee = $goodsFee + $freight - $discountFee;

        //总金额 = 商品金额 + 配送费 - 优惠金额 - 首单立减 - 满减优惠 - 商品特价
        $totalFee = $goodsFee + $freight - $discountFee - $firstOrderCutMoney - $fullOrderCutMoney - $specialOrderCutMoney;

        $totalFee = $totalFee > 0 ? $totalFee : 0;

        //本次下单需要扣除的平台抽成
        $fee = $totalFee * $seller['deduct'] / 100;

        $payFee = $totalFee;

        //如果商家余额大于等于平台抽成
        if($fee > $seller['extend']['money']){
            $isCashOnDelivery = 0;
        }
        //可用积分
        $integral = 0;
        $cashMoney = 0;
        if ($payFee > 0.001) {
            $ioff = baseSystemConfigService::getConfigByCode('integral_off');
            $integralOpenType = baseSystemConfigService::getConfigByCode('integral_open_type');
            if($ioff && ($seller['storeType'] == $integralOpenType || $integralOpenType == 2)){
                $limitVal = SystemConfig::where('code', 'limit_cash_integral')->pluck('val');
                $userIntegral = User::where('id', $userId)->pluck('integral');
                $val = SystemConfig::where('code', 'cash_integral')->pluck('val');
                $payFees = $limitVal == 100 ? ceil($payFee) : $payFee; //抵现上限比例为100%时, 支付金额转为整数
                $limitCashMoney = (double)round(($limitVal / 100 * $payFees), 2); //抵现上限金额
                if(IS_OPEN_FX){
                    if($data[0]['shareUserId'] <= 0){
                        $ableCashMoney = (double)round(($val / 100 * $userIntegral), 2); // 当前可抵现金额
                        $cashMoney = $ableCashMoney <= $limitCashMoney ? $ableCashMoney : $limitCashMoney; //最终抵现金额
                        $integral = (int)($cashMoney / ($val / 100)); // 抵现积分
                        $cashMoney = (double)round(($integral * ($val / 100)), 2); //根据积分算抵现金额
                    }
                }else{
                    $ableCashMoney = (double)round(($val / 100 * $userIntegral), 2); // 当前可抵现金额
                    $cashMoney = $ableCashMoney <= $limitCashMoney ? $ableCashMoney : $limitCashMoney; //最终抵现金额
                    $integral = (int)($cashMoney / ($val / 100)); // 抵现积分
                    $cashMoney = (double)round(($integral * ($val / 100)), 2); //根据积分算抵现金额
                }
            }
            $cashMoney = $cashMoney >= $payFee ? $payFee : $cashMoney;
            //cz
            if(empty($promotionSnId)){
                $totalFee = $payFee = $cashMoney >= $payFee ? 0 : ($payFee - $cashMoney);
            }else{
                $totalFee = $payFee = $cashMoney >= $payFee ? 0 : $payFee;
            }
        }
        if(!IS_OPEN_FX){
            $promotionMaxMoney = $goodsFee - $specialOrderCutMoney + $freight;

            $promotionWids =  PromotionSellerCate::whereIn('seller_cate_id',function($query) use ($sellerId){
                $query->select('cate_id')
                    ->from('seller_cate_related')
                    ->where('seller_id', $sellerId);
            })->groupBy('promotion_id')->lists('promotion_id');

            $storeType = $seller['storeType'] != 1 ? 4 : 5;
            $promotionIds = Promotion::where('limit_money','<=',$promotionMaxMoney)
                ->where(function($query) use ($promotionWids, $sellerId,$storeType){
                    $query->where(function($queryOne) use ($promotionWids){
                        $queryOne->where('use_type',2)
                            ->whereIn('id',$promotionWids);
                    })->orWhere(function($queryTwo) use ($sellerId){
                        $queryTwo->where('use_type',3)
                            ->where('seller_id',$sellerId);
                    })->orWhere(function($queryThree) use ($storeType){
                        $queryThree->where('is_store',1)
                            ->where('use_type',$storeType);
                    })->orWhere('use_type',1);
                })->whereNotIn('id', function($query){
                    $query->select('promotion_id')
                        ->from('promotion_unable_date')
                        ->where('date_time',UTC_DAY);
                })->lists('id');
            $proSnCount = PromotionSn::where('user_id',$userId)
                ->where('use_time', 0)
                ->where('begin_time', '<=', UTC_TIME)
                ->where('expire_time', '>=', UTC_TIME)
                ->where('is_del',0)
                ->whereIn('promotion_id',$promotionIds)
                ->count();

            $cancel = !empty($cancel) ? $cancel : 0;
            if (($pro || $proSnCount > 0) && $cancel == 0) {
                $discountFee = $pro->promotion->type == 'offset' ? ($goodsFee + $freight) : (double)$pro->money;
                $isShowPromotion = $promotionMaxMoney > 0.001 ? 1 : 0;
            }

            //如果有优惠金额,总金额应减掉优惠金额
            $totalFee = $totalFee - $discountFee;
        }else{
            if($data[0]['shareUserId'] <= 0){
                $promotionMaxMoney = $goodsFee - $specialOrderCutMoney + $freight;

                $promotionWids =  PromotionSellerCate::whereIn('seller_cate_id',function($query) use ($sellerId){
                    $query->select('cate_id')
                        ->from('seller_cate_related')
                        ->where('seller_id', $sellerId);
                })->groupBy('promotion_id')->lists('promotion_id');

                $storeType = $seller['storeType'] != 1 ? 4 : 5;
                $promotionIds = Promotion::where('limit_money','<=',$promotionMaxMoney)
                    ->where(function($query) use ($promotionWids, $sellerId,$storeType){
                        $query->where(function($queryOne) use ($promotionWids){
                            $queryOne->where('use_type',2)
                                ->whereIn('id',$promotionWids);
                        })->orWhere(function($queryTwo) use ($sellerId){
                            $queryTwo->where('use_type',3)
                                ->where('seller_id',$sellerId);
                        })->orWhere(function($queryThree) use ($storeType){
                            $queryThree->where('is_store',1)
                                ->where('use_type',$storeType);
                        })->orWhere('use_type',1);
                    })->whereNotIn('id', function($query){
                        $query->select('promotion_id')
                            ->from('promotion_unable_date')
                            ->where('date_time',UTC_DAY);
                    })->lists('id');
                $proSnCount = PromotionSn::where('user_id',$userId)
                    ->where('use_time', 0)
                    ->where('begin_time', '<=', UTC_TIME)
                    ->where('expire_time', '>=', UTC_TIME)
                    ->where('is_del',0)
                    ->whereIn('promotion_id',$promotionIds)
                    ->count();


                $cancel = !empty($cancel) ? $cancel : 0;
                if (($pro || $proSnCount > 0) && $cancel == 0) {
                    $discountFee = $pro->promotion->type == 'offset' ? ($goodsFee + $freight) : (double)$pro->money;
                    $isShowPromotion = $promotionMaxMoney > 0.001 ? 1 : 0;
                }

                //如果有优惠金额,总金额应减掉优惠金额
                $totalFee = $totalFee - $discountFee;
            }
        }
        $totalFee = $totalFee > 0 ? $totalFee : 0;

        $data = [
            'totalFee'              => (double)round($totalFee,2),
            'goodsFee'              => (double)round($goodsFee,2),
            'discountFee'           => (double)round($discountFee, 2),
            'freight'               => (double)round($freight, 2),
            'payFee'                => (double)round($payFee,2),
            'isCashOnDelivery'      => $isCashOnDelivery,
            'isShowPromotion'       => 1,
            'promotionCount'        => $proSnCount,
            'sellerId'              => $sellerId,
            'totalMoney'            => (double)round($totalMoney,2),
            'freightMsg'            => $freightMsg,
            'integral'              => $integral,
            'cashMoney'             => (double)round($cashMoney, 2),
            'firstOrderCutMoney'    => (double)round($firstOrderCutMoney, 2),
            'fullOrderCutMoney'     => (double)round($fullOrderCutMoney, 2),
            'systemFullSubsidy'     => (double)round($systemFullSubsidy, 2),
            'sellerFullSubsidy'     => (double)round($sellerFullSubsidy, 2),
            'specialOrderCutMoney'  => (double)round($specialOrderCutMoney, 2),
            'promotionMaxMoney'  => (double)round($promotionMaxMoney, 2),//优惠券满减
        ];

        return $data;
    }




    /**
     * 不显示优惠信息
     */
    public function notshow($userId, $orderId) {
        $order = Order::where('user_id',$userId)->where('id',$orderId)->with('userRefund','refundCount')->first();
        if($order){
            Order::where('user_id',$userId)->where('id',$orderId)->update(['promotion_is_show'=>1]);
        }
    }


    /**
     * 积分兑换商品下单
     * @param int $userId 会员编号
     * @param int $goodsId 商品编号
     * @param int $addressId 地址编号
     * @param string $buyRemark 备注
     * @param string $appTime 预约时间
     * @param int $payment 支付方式 1:在线支付
     * @param string $freType 配送方式(文字)
     * @param int $sendWay 配送方式(编号)
     * @return array
     */
    public function integralOrder($userId, $goodsId, $addressId, $buyRemark, $appTime, $payment, $freType, $sendWay){
        $result = [
            'code' => 0,
            'data' => null,
            'msg' => ''
        ];
        $goods = Goods::where('id', $goodsId)->where('status', 1)->with('seller')->first();
        //商品不存在或已下架
        if (!$goods) {
            $result['code'] = 60517;
            return $result;
        }

        //商品库存不足
        if ($goods->stock < 1) {
            $result['code'] = 60504;
            return $result;
        }

        $user = User::where('id', $userId)->first();
        //积分不足
        if ($user->integral < $goods->exchange_integral) {
            $result['code'] = 60518;
            return $result;
        }

        $notDeliveryData = []; //无需配送的商品订单数据
        $address = UserAddress::where('user_id', $userId)->where('id', $addressId)->first();
        //地址信息不存在
        if (!$address) {
            $result['code'] = '60506';
            return $result;
        }
        //商品需要配送
        if ($goods->is_virtual == 1) {

            // 验证是否是立即送出 是：当前时间+一个时间间隔   否：配送时间不能小于当前时间
            if($appTime == 0){
                $appTime  =UTC_TIME + $goods->seller->send_loop * 60;
                $isNow = 1;
            }else{
                $appTime = Time::toTime($appTime);
                if ($appTime < UTC_TIME) {
                    $result['code'] = '60508';
                    return $result;
                }
                $isNow = 0;
            }
            $deliveryFee = $goods->seller->delivery_fee;//运费
            $status = $goods->seller->delivery_fee > 0.001 ? ORDER_STATUS_BEGIN_USER : ORDER_STATUS_PAY_SUCCESS;
            if ($goods->seller->delivery_fee == 0) {
                $notDeliveryData = [
                    'pay_type' => 'integral',
                    'pay_time' => UTC_TIME
                ];
            }

        } else {
            $appTime = UTC_TIME;
            $deliveryFee = 0;
            $status = ORDER_STATUS_START_SERVICE;
            $notDeliveryData = [
                'pay_type' => 'integral',
                'pay_time' => UTC_TIME,
                'seller_confirm_time' => UTC_TIME,
                'fre_time' => UTC_TIME
            ];
        }
        $sellerId = $goods->seller->id;
        //选择配送人员
        $staff = SellerStaff::where('seller_id', $goods->seller->id)
            ->where('order_status', 1)
            ->whereNotIn('id', function($query) use ($sellerId){
                $query->select('staff_id')
                    ->from('staff_leave')
                    ->where('begin_time', '<=', UTC_TIME)
                    ->where('end_time', '>=', UTC_TIME)
                    ->where('is_agree', 1)
                    ->where('status', 1);
            })->whereIn('type', [0,1,3])
            ->orderBy(DB::raw('RAND()'))
            ->first();

        $systemOrderPass = baseSystemConfigService::getConfigByCode('system_order_pass');
        $autoCancelTime = $systemOrderPass + UTC_TIME;

        $orderData = [
            'sn' => Helper::getSn(),
            'seller_id' => $goods->seller->id,
            'user_id' => $userId,
            'name' => $address->name,
            'name_match' => String::strToUnicode($address->name),
            'mobile' => $address->mobile,
            'address_id' => $address->id,
            'address' => $address->address,
            'map_point' => $address->map_point_str,
            'province_id' => $address->province_id,
            'city_id' => $address->city_id,
            'area_id' => $address->area_id,
            'province' => Region::where('id', $address->province_id)->pluck('name'),
            'city' => Region::where('id', $address->city_id)->pluck('name'),
            'area' => Region::where('id', $address->area_id)->pluck('name'),
            'buy_remark' => $buyRemark,
            'app_time' => $appTime,
            'app_day' => Time::toDayTime($appTime),
            'create_time' => UTC_TIME,
            'create_day' => UTC_DAY,
            'fre_time' => $appTime,
            'auto_cancel_time' => $autoCancelTime,
            'order_type' => 1,
            'total_fee' => $deliveryFee,
            'goods_fee' => 0,
            'seller_fee' => 0,
            'drawn_fee' => 0,
            'discount_fee' => 0,
            'freight' => $deliveryFee,
            'freight_info' => '',
            'count' => 1,
            'seller_staff_id' => $staff->id,
            'status' => $status,
            'pay_fee' => $deliveryFee,
            'staff_fee' => 0,
            'promotion_sn_id' => 0,
            'fre_type' => $freType,
            'send_way' => $sendWay,
            'is_now' => $isNow,
            'first_level' => $goods->seller->first_level,
            'second_level' => $goods->seller->second_level,
            'third_level' => $goods->seller->third_level,
            'integral' => $goods->exchange_integral,
            'integral_fee' => '0.00',
            'is_integral_goods' => 1,
            'pay_status' => $deliveryFee > 0.001 ? ORDER_PAY_STATUS_NO : ORDER_PAY_STATUS_YES
        ];
        if (!empty($notDeliveryData)) {
            $orderData = array_merge($orderData, $notDeliveryData);
        }
        $bln = false;
        DB::beginTransaction();
        try {
            $user->integral = $user->integral - $goods->exchange_integral;
            $user->save();
            $orderId = Order::insertGetId($orderData);
            //插入订单商品明细表
            $goodsData = [
                'order_id' => $orderId,
                'seller_id' => $sellerId,
                'goods_name' => $goods->name,
                'goods_duration' => 0,
                'goods_images' => $goods->image,
                'goods_norms' => '',
                'goods_id' => $goodsId,
                'goods_norms_id' => 0,
                'price' => $goods->price,
                'num' => 1
            ];
            OrderGoods::insert($goodsData);

            //写入积分日志
            if ($orderData['integral'] > 0) {
                \YiZan\Services\UserIntegralService::createIntegralLog($userId, 2, 8, $orderId, $deliveryFee, $orderData['integral']);
            }
            //更新商品扩展表销量
            GoodsExtend::where('goods_id', $goodsId)->increment('sales_volume', 1);
            //更新商品表库存
            Goods::where('id', $goodsId)->decrement('stock', 1);

            $result['msg'] = Lang::get('api.success.user_create_order');
            $result['data'] =  self::getOrderById($userId,$orderId);
            $bln = true;
            DB::commit();

        } catch (Exception $e) {
            $result['code'] = '60013';
            DB::rollback();
        }

        if ($bln && $orderData['pay_status'] == ORDER_PAY_STATUS_YES) {
            try {
                PushMessageService::notice( $result['data']['seller']['userId'],  $result['data']['seller']['mobile'], 'order.create',  $result['data'],['sms', 'app'],'seller','3',$orderId, "neworder.caf");
                if($result['data']['staff'] && $result['data']['seller']['userId'] != $result['data']['staff']['userId']){
                    PushMessageService::notice( $result['data']['staff']['userId'],  $result['data']['staff']['mobile'], 'order.create',  $result['data'],['sms', 'app'],'staff','3',$orderId, "neworder.caf");
                }
            } catch (Exception $e) {

            }
        }
        return $result;


        return $result;
    }


    /**
     * 邀请下单 检测
     * @param int $userId 会员编号
     * @param int $goodsId 商品编号
     * @param int $addressId 地址编号
     * @param string $buyRemark 备注
     * @param string $appTime 预约时间
     * @param int $payment 支付方式 1:在线支付
     * @param string $freType 配送方式(文字)
     * @param int $sendWay 配送方式(编号)
     * @return array
     */
    public function invitationOrder($orderId,$data,$storeTtype = 0,$shareUserId=0){

        if(!IS_OPEN_FX){
            return;
        }
        if($data['user_id'] > 0){
            $invitation = InvitationService::getById(1);
            if($invitation->id  > 0 ){

                $order = Order::find($orderId);

                if(!$order){
                    return false;
                }

                //优惠后的价格   = 商品总优惠价  -    特价商品优惠金额(商家)    -     商家满减补贴金额(商家)
                $money =  ($order->goods_fee - $order->activity_goods_money) - $order->seller_full_subsidy;

                if($money >= $invitation->full_money){

                    $user = User::find($data['user_id']);  //初级 自己

                    if(strtolower($user->invitation_type) == "seller"  &&  $invitation->seller_status == 0){
                        return false;
                    }
                    if($invitation->user_status == 0){
                        return false;
                    }else if($invitation->user_status == 2 && $user->protocol_fee > 0 && $user->is_pay == 0){
                        return false;
                    }
                    DB::beginTransaction();
                    $invitationId = 0;
                    if(strtolower($user->invitation_type) == "seller"){
                        //查询1级邀请人
                        $firstInvitation = Seller::find($user->invitation_id);
                        $invitationId = $firstInvitation->user_id;
                    }else{
                        if(!$user->invitation_id){
                            if($shareUserId || $storeTtype == 1){
                                $firstInvitation = $user;
                            }
                        }else{
                            //查询1级邀请人
                            $firstInvitation = User::find($user->invitation_id);
                            $invitationId = $firstInvitation->id;
                        }
                    }
                    if(strtolower($user->invitation_type) == "seller"){
                        $firstReturnMoney = round($money *  $invitation->seller_percent / 100,2);//商家返现比例
                    }

                    try{
                        $datas = [];
                        $full = 0;

                        if($storeTtype == 1 && IS_OPEN_FX){
                            if($firstInvitation){

                                if(
                                    (   $invitation->user_status == 1) ||
                                    (   $invitation->user_status == 2 &&  strtolower(   $firstInvitation->invitation_type   ) == "user" ) && (
                                        (
                                            $invitation->protocol_fee > 0 && $firstInvitation->is_pay == 1
                                        ) ||
                                        $invitation->protocol_fee <= 0
                                    )
                                ){

                                    $firstReturnPrimaryMoney = round( $money *  $invitation->is_all_user_primary / 100,2);  //初级自己的钱钱！

                                    $datas[] = [
                                        'status'            => 0,
                                        'order_id'          => $order->id,
                                        'user_id'           => $order->user_id,
                                        'invitation_type'   => "user",
                                        'invitation_id'     => $user->id,
                                        'percent'           => $invitation->is_all_user_primary,
                                        'return_fee'        =>  $firstReturnPrimaryMoney,
                                        'level'             => 0,
                                        'create_time'       => UTC_TIME,
                                        'create_day'        => UTC_DAY,
                                        'share_user_id'        => $shareUserId,
                                        'update_time'        => UTC_TIME
                                    ];

                                }


                                //分享人得到初级返现
                                $firstInvitationshareUser = User::find($shareUserId);

                                if($firstInvitationshareUser->id && ( $firstInvitationshareUser->id != $user->id ) ){

                                    if(
                                        (   $invitation->user_status == 1) ||
                                        (   $invitation->user_status == 2 &&  strtolower(   $firstInvitationshareUser->invitation_type   ) == "user" ) && (
                                            (
                                                $invitation->protocol_fee > 0 && $firstInvitationshareUser->is_pay == 1
                                            ) ||
                                            $invitation->protocol_fee <= 0
                                        )
                                    ){
                                        $firstReturnPrimaryShareUserMoney = $firstReturnPrimaryMoney;
                                        $datas[] = [

                                            'status'            => 0,
                                            'order_id'          => $order->id,
                                            'user_id'           => $order->user_id,
                                            'invitation_type'   => "user",
                                            'invitation_id'     => $firstInvitationshareUser->id,
                                            'percent'           => $invitation->is_all_user_primary,
                                            'return_fee'        => $firstReturnPrimaryShareUserMoney,
                                            'level'             => 0,
                                            'create_time'       => UTC_TIME,
                                            'create_day'        => UTC_DAY,
                                            'share_user_id'     => $shareUserId,
                                            'update_time'        => UTC_TIME
                                        ];
                                    }

                                }
                                //一级
                                if(strtolower($user->invitation_type) == "user" || $user->invitation_id > 0){

                                    if(
                                        (   $invitation->user_status == 1) ||
                                        (   $invitation->user_status == 2 &&  strtolower(   $user->invitation_type   ) == "user" ) && (
                                            (
                                                $invitation->protocol_fee > 0 && $user->is_pay == 1
                                            ) ||
                                            $invitation->protocol_fee <= 0
                                        )
                                    ){
                                        $firstReturnMoney = round( $money *  $invitation->is_all_user_percent / 100 ,2);
                                        $datas[] = [
                                            'status'            => 0,
                                            'order_id'          => $order->id,
                                            'user_id'           => $order->user_id,
                                            'invitation_type'   => strtolower($user->invitation_type),
                                            'invitation_id'     => $user->invitation_id,
                                            'percent'           => $invitation->is_all_user_percent,
                                            'return_fee'        =>$firstReturnMoney,
                                            'level'             => 1,
                                            'create_time'       => UTC_TIME,
                                            'create_day'        => UTC_DAY,
                                            'share_user_id'        => $shareUserId,
                                            'update_time'        => UTC_TIME
                                        ];
                                    }

                                    $firstReturnPrimary = User::find($user->invitation_id);

                                }

                                //二级
                                if(strtolower($firstReturnPrimary->invitation_type) == "user" || $firstReturnPrimary->invitation_id > 0){

                                    if(
                                        (   $invitation->user_status == 1) ||
                                        (   $invitation->user_status == 2 &&  strtolower(   $firstReturnPrimary->invitation_type   ) == "user" ) && (
                                            (
                                                $invitation->protocol_fee > 0 && $firstReturnPrimary->is_pay == 1
                                            ) ||
                                            $invitation->protocol_fee <= 0
                                        )
                                    ){

                                        $secondReturnMoney = round( $money *  $invitation->is_all_user_percent_second / 100,2);
                                        $datas[] = [
                                            'status'            => 0,
                                            'order_id'          => $order->id,
                                            'user_id'           => $order->user_id,
                                            'invitation_type'   => strtolower($firstReturnPrimary->invitation_type),
                                            'invitation_id'     => $firstReturnPrimary->invitation_id,
                                            'percent'           => $invitation->is_all_user_percent_second,
                                            'return_fee'        => $secondReturnMoney,
                                            'level'             => 2,
                                            'create_time'       => UTC_TIME,
                                            'create_day'        => UTC_DAY,
                                            'share_user_id'        => $shareUserId,
                                            'update_time'        => UTC_TIME
                                        ];
                                    }
                                    $secondInvitation = User::find($firstReturnPrimary->invitation_id);
                                }
                                //三级
                                if(strtolower($secondInvitation->invitation_type) == "user" || $secondInvitation->invitation_id > 0){

                                    if(
                                        (   $invitation->user_status == 1) ||
                                        (   $invitation->user_status == 2 &&  strtolower(   $secondInvitation->invitation_type   ) == "user" ) && (
                                            (
                                                $invitation->protocol_fee > 0 && $secondInvitation->is_pay == 1
                                            ) ||
                                            $invitation->protocol_fee <= 0
                                        )
                                    ){

                                        $thirdReturnMoney = round( $money *  $invitation->is_all_user_percent_third / 100 ,2);

                                        $datas[] = [
                                            'status'            => 0,
                                            'order_id'          => $order->id,
                                            'user_id'           => $order->user_id,
                                            'invitation_type'   => strtolower($secondInvitation->invitation_type),
                                            'invitation_id'     => $secondInvitation->invitation_id,
                                            'percent'           => $invitation->is_all_user_percent_third,
                                            'return_fee'        => $thirdReturnMoney,
                                            'level'             => 3,
                                            'create_time'       => UTC_TIME,
                                            'create_day'        => UTC_DAY,
                                            'share_user_id'        => $shareUserId,
                                            'update_time'        => UTC_TIME
                                        ];

                                    }
                                }
                                //写入扣款日志
                                InvitationService::createLog($datas);
                            }
                            $full = $firstReturnMoney + $firstReturnPrimaryShareUserMoney + $firstReturnPrimaryMoney + $secondReturnMoney + $thirdReturnMoney;

                        }else{
                            if($firstInvitation){

                                    if(
                                        (   $invitation->user_status == 1) ||
                                        (   $invitation->user_status == 2 &&  strtolower(   $firstInvitation->invitation_type   ) == "user" ) && (
                                            (
                                                $invitation->protocol_fee > 0 && $firstInvitation->is_pay == 1
                                            ) ||
                                            $invitation->protocol_fee <= 0
                                        )
                                    ){

                                    $firstReturnMoney = round($money *  $invitation->user_percent / 100,2);//一级
                                    $mgo_data[] = [
                                        'status'            => 0,
                                        'order_id'          => $order->id,
                                        'user_id'           => $order->user_id,
                                        'invitation_type'   => strtolower($user->invitation_type),
                                        'invitation_id'     => $invitationId,
                                        'percent'           => $invitation->user_percent,
                                        'return_fee'        => $firstReturnMoney,
                                        'level'             => 1,
                                        'create_time'       => UTC_TIME,
                                        'create_day'        => UTC_DAY,
                                        'share_user_id'        => $shareUserId,
                                    ];
                                }
                                //2级邀请人只有是会员才发放返现
                                if(strtolower($firstInvitation->invitation_type) == "user" || $firstInvitation->invitation_id > 0){

                                    if(
                                        (   $invitation->user_status == 1) ||
                                        (   $invitation->user_status == 2 &&  strtolower(   $firstInvitation->invitation_type   ) == "user" ) && (
                                            (
                                                $invitation->protocol_fee > 0 && $firstInvitation->is_pay == 1
                                            ) ||
                                            $invitation->protocol_fee <= 0
                                        )
                                    ){

                                        $secondReturnMoney = round($money * $invitation->user_percent_second / 100, 2);//二级
                                        $mgo_data[] = [
                                            'status' => 0,
                                            'order_id' => $order->id,
                                            'user_id' => $order->user_id,
                                            'invitation_type' => strtolower($firstInvitation->invitation_type),
                                            'invitation_id' => $firstInvitation->invitation_id,
                                            'percent' => $invitation->user_percent_second,
                                            'return_fee' => $secondReturnMoney,
                                            'level' => 2,
                                            'create_time' => UTC_TIME,
                                            'create_day' => UTC_DAY,
                                            'share_user_id' => $shareUserId,
                                        ];
                                    }
                                    $secondInvitation = User::find($firstInvitation->invitation_id);
                                    //3级邀请人只有是会员才发放返现
                                    if(strtolower($secondInvitation->invitation_type) == "user" || $secondInvitation->invitation_id > 0){

                                        if(
                                            (   $invitation->user_status == 1) ||
                                            (   $invitation->user_status == 2 &&  strtolower(   $secondInvitation->invitation_type   ) == "user" ) && (
                                                (
                                                    $invitation->protocol_fee > 0 && $firstInvitation->is_pay == 1
                                                ) ||
                                                $invitation->protocol_fee <= 0
                                            )
                                        ){
                                            $thirdReturnMoney = round($money * $invitation->user_percent_third / 100, 2);//三级
                                            $mgo_data[] = [
                                                'status' => 0,
                                                'order_id' => $order->id,
                                                'user_id' => $order->user_id,
                                                'invitation_type' => strtolower($secondInvitation->invitation_type),
                                                'invitation_id' => $secondInvitation->invitation_id,
                                                'percent' => $invitation->user_percent_third,
                                                'return_fee' => $thirdReturnMoney,
                                                'level' => 3,
                                                'create_time' => UTC_TIME,
                                                'create_day' => UTC_DAY,
                                                'share_user_id' => $shareUserId,
                                            ];
                                        }
                                    }
                                }
                                !InvitationService::createLog($mgo_data);
                            }
                            $full = $firstReturnMoney + $secondReturnMoney + $thirdReturnMoney;
                        }
                        //修改订单返现字段信息
                        $order->is_invitation = 1;
                        $order->return_fee = $full;
                        $order->save();
                        DB::commit();
                        return true;
                    } catch(Exception $e){
                        DB::rollback();
                    }
                }
            }
        }
        return false;
    }

    /**
     * [recountCashMoney 重新计算可抵扣的积分]
     * @param  [type] $userId [description]
     * @param  [type] $payFee [description]
     * @return [type]         [description]
     */
    public static function recountCashMoney($userId, $payFee){
        //可用积分
        $integral = 0;
        $cashMoney = 0;
        if ($payFee > 0.001) {
            $userIntegral = User::where('id', $userId)->pluck('integral');
            $val = SystemConfig::where('code', 'cash_integral')->pluck('val');
            $limitVal = SystemConfig::where('code', 'limit_cash_integral')->pluck('val');
            $ioff = baseSystemConfigService::getConfigByCode('integral_off');
            if($ioff){
                $payFees = $limitVal == 100 ? ceil($payFee) : $payFee; //抵现上限比例为100%时, 支付金额转为整数
                $limitCashMoney = (double)round(($limitVal / 100 * $payFees), 2); //抵现上限金额
                $ableCashMoney = (double)round(($val / 100 * $userIntegral), 2); // 当前可抵现金额
                $cashMoney = $ableCashMoney <= $limitCashMoney ? $ableCashMoney : $limitCashMoney; //最终抵现金额
                $integral = (int)($cashMoney / ($val / 100)); // 抵现积分
                $cashMoney = (double)round(($integral * ($val / 100)), 2); //根据积分算抵现金额
            }
            $cashMoney = $cashMoney >= $payFee ? $payFee : $cashMoney;
        }

        return ['integral'=>$integral, 'cashMoney'=>$cashMoney];
    }

    /**
     * [ totalnum  统计]
     * @param  [type] $userId [description]
     * @return [type]         [description]
     */
    public static function totalnum($userId){

        $paymentwhere = [ORDER_STATUS_BEGIN_USER]; //待支付
        $shippedwhere = [ORDER_STATUS_PAY_SUCCESS,ORDER_STATUS_PAY_DELIVERY]; //待发货
        $receiptwhere = [ORDER_STATUS_AFFIRM_SELLER,ORDER_REFUND_ADMIN_REFUSE,ORDER_REFUND_SELLER_REFUSE]; //待收货
        $ratewhere = [ORDER_STATUS_FINISH_SYSTEM,ORDER_STATUS_FINISH_USER]; //待评价
        $refundwhere = [ORDER_REFUND_SELLER_REFUSE_LOGISTICS,ORDER_REFUND_USER_REFUSE_LOGISTICS,ORDER_STATUS_REFUND_AUDITING,ORDER_STATUS_CANCEL_REFUNDING,ORDER_STATUS_REFUND_HANDLE,ORDER_STATUS_REFUND_FAIL,ORDER_STATUS_REFUND_SUCCESS,ORDER_REFUND_SELLER_AGREE,ORDER_REFUND_SELLER_REFUSE,ORDER_REFUND_ADMIN_AGREE,ORDER_REFUND_ADMIN_REFUSE];//退款

        $data = [];
        $data['paymentstatus']  = Order::where('user_id',$userId)->whereIn('status',$paymentwhere)->where("pay_status",0)->count();//待支付
        $data['shippedstatus']  = Order::where('user_id',$userId)->whereIn('status',$shippedwhere)->count();//待发货
        // $data['receiptstatus']  = Order::where(function($where) use($receiptwhere,$userId){
            // $where->where('is_all',1)
                // ->whereIn('status',$receiptwhere)
                // ->where('user_id',$userId);
        // })->orWhere(function($where) use($receiptwhere,$userId){
            // $where->where('is_all',0)
                // ->whereIn('status',[ORDER_STATUS_AFFIRM_SELLER,ORDER_STATUS_START_SERVICE])
                // ->where('user_id',$userId);
        // })->count();
        //待收货
		$data['receiptstatus'] = Order::where(function($where) use($receiptwhere,$userId){
			$where->where('is_all',1)
				->whereIn('status',$receiptwhere)
				->where('user_id', $userId);
		})->orWhere(function($where) use($userId){
			$where->where('is_all',0)
				->whereIn('status',[ORDER_STATUS_AFFIRM_SELLER,ORDER_STATUS_START_SERVICE,ORDER_STATUS_FINISH_STAFF])
				->where('user_id', $userId);
		})->count();
			
        $data['ratestatus']     = Order::where('user_id',$userId)->whereIn('status',$ratewhere)->where("is_rate",0)->count();//待评价
        $data['refundstatus']   = Order::where('user_id',$userId)->whereIn('status',$refundwhere)->where("pay_fee", '>=', 0.0001)->count();//退款

        return $data;
    }

}