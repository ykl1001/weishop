<?php namespace YiZan\Services\System;

use YiZan\Models\Proxy;
use YiZan\Models\Seller;
use YiZan\Models\Order;
use YiZan\Models\District;

use YiZan\Utils\Time;
use YiZan\Utils\String;
use DB, Exception,Validator, Lang;

/**
 * 代理
 */
class ProxyService extends \YiZan\Services\ProxyService {

    /**
     * 查询代理列表
     * @param string $name      代理账户
     * @param int $provinceId   省份编号
     * @param int $cityId       省份编号
     * @param int $areaId       区域编号
     * @param int $page         分页
     * @param int $pageSize     每页数量
     * @param int $level        等级
     * @param int $isAll        是否取全部
     * @return $result 结果集
     */
    public static function getLists($name, $provinceId, $cityId, $areaId, $page, $pageSize, $level, $isAll){
        $list = Proxy::where('is_check', 1)
            ->orderBy('id');

        if($name){
            $list->where('name', $name);
        }

        if($provinceId > 0){
            $list->where('province_id', $provinceId);
        }

        if($cityId > 0){
            $list->where('city_id', $cityId);
        }

        if($areaId > 0){
            $list->where('area_id', $areaId);
        }

        if($isAll){
            if($level > 1){
                $list->where('level', $level - 1);
                $list = $list->with('province', 'city', 'area', 'childs')
                    ->get()
                    ->toArray();
                return $list;
            } else {
                $list = $list->with('province', 'city', 'area', 'childs')
                    ->get()
                    ->toArray();
                return $list;
            }
        } else {
            $totalCount = $list->count();
            $list = $list->skip(($page - 1) * $pageSize)
                ->take($pageSize)
                ->with('province', 'city', 'area', 'childs')
                ->get()
                ->toArray();

            foreach ($list as $key => $value) {
                $list[$key]['canDelete'] = self::getLists_canDelete($value['id']);
            }
            return ["list" => $list, "totalCount" => $totalCount];
        }
    }

    /**
     * 判断是否可删除
     */
    public static function getLists_canDelete($id) {
        //删除代理时判断是几级代理 根据代理级别 依次判断下级代理下是否有商品/商家/物业
        $proxy = Proxy::find($id);
        switch ($proxy->level) {
            case '2':
                $data['firstLevel'] = (int)$proxy->pid;
                $data['secondLevel'] = (int)$proxy->id;
                $data['thirdLevel'] = 0;
                break;
            case '3':
                $parentProxy = Proxy::find($proxy->pid);
                $data['firstLevel'] = (int)$parentProxy->pid;
                $data['secondLevel'] = (int)$proxy->pid;
                $data['thirdLevel'] = (int)$proxy->id;
                break;

            default:
                $data['firstLevel'] = $proxy->id;
                $data['secondLevel'] = 0;
                $data['thirdLevel'] = 0;
                break;
        }
        $sellerLists = Seller::orderBy('id', 'DESC');
        $orderLists = Order::orderBy('id', 'DESC');
        $districtLists = District::orderBy('id', 'DESC');

        if($data['firstLevel'] > 0){
            $sellerLists->where('first_level', $data['firstLevel']);
            $orderLists->where('first_level', $data['firstLevel']);
            $districtLists->where('first_level', $data['firstLevel']);
        }

        if($data['secondLevel'] > 0){
            $sellerLists->where('second_level', $data['secondLevel']);
            $orderLists->where('second_level', $data['secondLevel']);
            $districtLists->where('second_level', $data['secondLevel']);
        }

        if($data['thirdLevel'] > 0){
            $sellerLists->where('third_level', $data['thirdLevel']);
            $orderLists->where('third_level', $data['thirdLevel']);
            $districtLists->where('third_level', $data['thirdLevel']);
        }
        if($data['firstLevel'] > 0){
            $sellerLists = $sellerLists->first();
            $orderLists = $orderLists->first();
            $districtLists = $districtLists->first();
            $prefix = DB::getTablePrefix();
            $childsProxy = Proxy::whereRaw("(pid = ".$proxy->id." or pid in (select id from ".$prefix."proxy where pid = ".$proxy->id."))")
                                ->get()
                                ->toArray();
            if($sellerLists || $orderLists || $districtLists || !empty($childsProxy)){
                return 0;
            }
        }

        return 1; //可删除
    }

    /**
     * 查询审核代理列表
     * @param string $name      代理账户
     * @param int $provinceId   省份编号
     * @param int $cityId       省份编号
     * @param int $areaId       区域编号
     * @param int $isCheck      审核状态
     * @param int $page         分页
     * @param int $pageSize     每页数量
     * @param int $level        等级
     * @param int $isAll        是否取全部
     * @return $result 结果集
     */
    public static function getAuthLists($name, $provinceId, $cityId, $areaId, $isCheck, $page, $pageSize, $level, $isAll){
        $list = Proxy::orderBy('id');

        if($isCheck){
            $list->where('is_check', $isCheck - 2);
        }

        if($name){
            $list->where('name', $name);
        }

        if($provinceId > 0){
            $list->where('province_id', $provinceId);
        }

        if($cityId > 0){
            $list->where('city_id', $cityId);
        }

        if($areaId > 0){
            $list->where('area_id', $areaId);
        }

        if($isAll){
            if($level > 1){
                $list->where('level', $level - 1);
                $list = $list->with('province', 'city', 'area', 'childs')
                    ->get()
                    ->toArray();
                return $list;
            } else {
                $list = $list->with('province', 'city', 'area', 'childs')
                    ->get()
                    ->toArray();
                return $list;
            }
        } else {
            $totalCount = $list->count();
            $list = $list->skip(($page - 1) * $pageSize)
                ->take($pageSize)
                ->with('province', 'city', 'area', 'childs')
                ->get()
                ->toArray();

            foreach ($list as $key => $value) {
                $list[$key]['canDelete'] = self::getLists_canDelete($value['id']);
            }
            return ["list" => $list, "totalCount" => $totalCount];
        }
    }

    /**
     * 添加/编辑代理
     * @param int $id           代理编号
     * @param string $name      代理账户
     * @param string $pwd       代理账户
     * @param string $realName  真实姓名
     * @param string $mobile    电话号码
     * @param int $pid          父代理
     * @param int $level        代理级别
     * @param int $provinceId   省份编号
     * @param int $cityId       省份编号
     * @param int $areaId       区域编号
     * @param string $thirdArea 三级代理区域
     * @param int $status       状态
     * @return $result 结果集
     */
    public static function save($id, $name, $pwd, $realName, $mobile, $pid, $level, $provinceId, $cityId, $areaId, $thirdArea, $status){
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );
        //去除空格
        $name = trim($name);

        if($id > 0){
            $proxy = Proxy::find($id);
            if(empty($proxy)){
                $result['code'] = 99999;
                return $result;
            }

            //如果编辑代理的时候修改了代理名 则判断新的代理名是否存在
            if($name != $proxy->name){
                $proxyInfo = Proxy::where('name', $name)
                    ->first();
                if($proxyInfo){
                    $result['code'] = 40939;
                    return $result;
                }
                $proxy->name = $name;
            }

            $level = $proxy->level;
            $pid = $proxy->pid;
            $rules = array(
                'realName'      => ['required', 'max:10'],
                'mobile'        => ['required','regex:/^1[0-9]{10}$/'],
                'level'         => ['required']
            );

            $messages = array
            (
                'realName.required' => 40929,   // 请输入真实姓名
                'realName.max'      => 40937,   // 长度限制
                'mobile.required'   => 40930,   // 请输入电话号码
                'mobile.regex'      => 40931,   // 请输入正确的电话号码
                'level.required'    => 40932    // 请输入代理级别
            );

            $checks = [
                'realName'      => $realName,
                'mobile'        => $mobile,
                'level'         => $level
            ];
        } else {
            $proxy = new Proxy();
            $rules = array(
                'name'          => ['required', 'regex:/^[0-9A-Za-z]{6,15}$/'],
                'pwd'           => ['required','min:6'],
                'realName'      => ['required', 'max:10'],
                'mobile'        => ['required','regex:/^1[0-9]{10}$/'],
                'level'         => ['required']
            );

            $messages = array
            (
                'name.required'     => 40926,   // 请输入代理账户
                'name.regex'        => 40936,   // 名字正则
                'pwd.required'      => 40927,
                'pwd.min'           => 40928,
                'realName.required' => 40929,   // 请输入真实姓名
                'realName.max'      => 40937,   // 长度限制
                'mobile.required'   => 40930,   // 请输入电话号码
                'mobile.regex'      => 40931,   // 请输入正确的电话号码
                'level.required'    => 40932    // 请输入代理级别
            );

            $checks = [
                'name'          => $name,
                'pwd'           => $pwd,
                'realName'      => $realName,
                'mobile'        => $mobile,
                'level'         => $level
            ];

            $proxyInfo = Proxy::where('name', $name)
                ->first();
            if($proxyInfo){
                $result['code'] = 40939;
                return $result;
            }

            $proxyInfo = Proxy::where('mobile', $mobile)->first();
            if($proxyInfo){
                $result['code'] = 40940;
                return $result;
            }
            $proxy->name = $name;
            $proxy->create_time = UTC_TIME;
        }

        $validator = Validator::make($checks, $rules, $messages);

        //验证信息
        if ($validator->fails())
        {
            $messages = $validator->messages();

            $result['code'] = $messages->first();

            return $result;
        }

        $proxy->real_name   = $realName;
        $proxy->mobile      = $mobile;
        $proxy->level       = $level;
        //设置密码
        if($id <= 0){
            $crypt  = String::randString(6);
            $proxy->pwd = md5(md5($pwd).$crypt);
            $proxy->crypt = $crypt;
        } else {
            if($pwd && strlen($pwd) < 6){
                $result['code'] = 40928;
                return $result;
            } else if($pwd && strlen($pwd) > 20){
                $result['code'] = 40938;
                return $result;
            } else if($pwd && strlen($pwd) >= 6){
                $proxy->pwd = md5(md5($pwd).$proxy->crypt);
            }
        }

        switch ($level) {
            case '2':
                $parentProxy = Proxy::find($pid);
                if(empty($parentProxy)){
                    $result['code'] = 40935;
                    return $result;
                }
                if(!empty($provinceId) && $parentProxy->province_id != $provinceId){
                    $result['code'] = 40933;
                }


                $zx = array("1", "18", "795", "2250");
                if(in_array($provinceId,$zx)){
                    $parentProxy->city_id = $cityId;
                }elseif((int)$id < 1){
                    if((int)$areaId < 1){
                        $result['code'] = 40941;
                        return $result;
                    }
                }

                $proxy->pid = $pid;
                $proxy->province_id = (int)$parentProxy->province_id;
                $proxy->city_id     = (int)$parentProxy->city_id;
                $proxy->area_id     = (int)$id < 1 ? (int)$areaId : $proxy->area_id;
                $proxy->third_area  = $thirdArea;
                break;

            case '3':
                if(empty($thirdArea)){
                    $result['code'] = 40934;
                    return $result;
                }
                $parentProxy = Proxy::find($pid);
                if(empty($parentProxy)){
                    $result['code'] = 40935;
                    return $result;
                }
                if(!empty($provinceId) && $parentProxy->province_id != $provinceId){
                    $result['code'] = 40933;
                    return $result;
                }
                if(!empty($cityId) && $parentProxy->city_id != $cityId){
                    $result['code'] = 40933;
                    return $result;
                }
                $proxy->pid = $pid;
                $proxy->province_id = (int)$parentProxy->province_id;
                $proxy->city_id     = (int)$parentProxy->city_id;
                $proxy->area_id     = $areaId > 0 ? (int)$areaId : $parentProxy->area_id;
                $proxy->third_area  = $thirdArea;
                break;

            default:
                if(empty($provinceId)){
                    $result['code'] = 40941;
                    return $result;
                }
                $proxy->province_id = (int)$provinceId;
                $zx = array("1", "18", "795", "2250");
                if(in_array($provinceId,$zx)){
                    $proxy->city_id     = 0;
                }else{
                    if(empty($cityId)){
                        $result['code'] = 40941;
                        return $result;
                    }
                    $proxy->city_id     = (int)$cityId;
                }
                $proxy->area_id     = 0;
                $proxy->third_area  = $thirdArea;
                break;
        }
        //新增代理判断当前地理位置是否已经存在此代理  
        if($id <= 0){
            $proxyData = Proxy::where('province_id', $proxy->province_id)
                ->where('city_id', $proxy->city_id)
                ->where('area_id', $proxy->area_id)
                ->where('third_area', $proxy->third_area)
                ->first();

            if($proxyData){
                $result['code'] = 40944;
                return $result;
            }
        }

        $proxy->status = $status;
        $proxy->save();
        return $result;
    }

    /**
     * 删除代理
     * @param int $id 编号
     * @return $result 结果集
     */
    public static function delete($ids){
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );

        foreach ($ids as $key => $id) {
            //删除代理时判断是几级代理 根据代理级别 依次判断下级代理下是否有商品/商家/物业
            $proxy = Proxy::find($id);
            switch ($proxy->level) {
                case '2':
                    $data['firstLevel'] = (int)$proxy->pid;
                    $data['secondLevel'] = (int)$proxy->id;
                    $data['thirdLevel'] = 0;
                    break;
                case '3':
                    $parentProxy = Proxy::find($proxy->pid);
                    $data['firstLevel'] = (int)$parentProxy->pid;
                    $data['secondLevel'] = (int)$proxy->pid;
                    $data['thirdLevel'] = (int)$proxy->id;
                    break;

                default:
                    $data['firstLevel'] = $proxy->id;
                    $data['secondLevel'] = 0;
                    $data['thirdLevel'] = 0;
                    break;
            }
            $sellerLists = Seller::orderBy('id', 'DESC');
            $orderLists = Order::orderBy('id', 'DESC');
            $districtLists = District::orderBy('id', 'DESC');

            if($data['firstLevel'] > 0){
                $sellerLists->where('first_level', $data['firstLevel']);
                $orderLists->where('first_level', $data['firstLevel']);
                $districtLists->where('first_level', $data['firstLevel']);
            }

            if($data['secondLevel'] > 0){
                $sellerLists->where('second_level', $data['secondLevel']);
                $orderLists->where('second_level', $data['secondLevel']);
                $districtLists->where('second_level', $data['secondLevel']);
            }

            if($data['thirdLevel'] > 0){
                $sellerLists->where('third_level', $data['thirdLevel']);
                $orderLists->where('third_level', $data['thirdLevel']);
                $districtLists->where('third_level', $data['thirdLevel']);
            }
            if($data['firstLevel'] > 0){
                $sellerLists = $sellerLists->get()->toArray();
                $orderLists = $orderLists->get()->toArray();
                $districtLists = $districtLists->get()->toArray();
                $prefix = DB::getTablePrefix();
                $childsProxy = Proxy::whereRaw("(pid = ".$proxy->id." or pid in (select id from ".$prefix."proxy where pid = ".$proxy->id."))")
                    ->get()
                    ->toArray();
                if(!empty($sellerLists)){
                    $result['code'] = 40945;
                    return $result;
                }

                if(!empty($orderLists)){
                    $result['code'] = 40946;
                    return $result;
                }

                if(!empty($districtLists)){
                    $result['code'] = 40947;
                    return $result;
                }

                if(!empty($childsProxy)){
                    $result['code'] = 40948;
                    return $result;
                }
            }
        }

        $delete_rs = Proxy::whereIn('id', $ids)->delete();
        if(!$delete_rs){
            $result['code'] = 40925;
        }
        return $result;
    }

    /**
     * 审核代理
     */
    public static function audit($id, $checkVal, $isCheck){
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );
        if(empty($checkVal)){
            $result['code'] = 40105;
            return $result;
        }
        Proxy::where('id', $id)
            ->update(['check_val'=>$checkVal, 'is_check'=>$isCheck]);
        return $result;
    }

    /**
     * 代理数据统计
     *
     * 额业额 ＝ 实付金额+平台满减+首单减+优惠券+积分抵扣
     *
     * 佣金=（实付金额+平台满减+首单减+优惠券+积分抵扣）* 佣金比例 = 营业额 * 佣金比例
     *
     * 入账金额 = （实付金额+平台满减+首单减+优惠券+积分抵扣）- 佣金 = 营业额 - 佣金
     */
    public static function getStatisticsList($name, $month, $year, $cityId, $page, $pageSize){

        $prefix = DB::getTablePrefix();
        if($name){
            $totalCount = Proxy::where('name', $name)
                ->count();
            $proxy = Proxy::where('name', $name)->first();
            if(empty($proxy)){
                return ["list" => '', "totalCount" => '', "sum" => ''];
            } else {
                switch ($proxy->level) {
                    case '2':
                        $data['firstLevel'] = (int)$proxy->pid;
                        $data['secondLevel'] = (int)$proxy->id;
                        $data['thirdLevel'] = 0;
                        break;
                    case '3':
                        $parentProxy = Proxy::find($proxy->pid);
                        $data['firstLevel'] = (int)$parentProxy->id;
                        $data['secondLevel'] = (int)$proxy->pid;
                        $data['thirdLevel'] = (int)$proxy->id;
                        break;

                    default:
                        $data['firstLevel'] = $proxy->id;
                        $data['secondLevel'] = 0;
                        $data['thirdLevel'] = 0;
                        break;
                }
                $qstr = ' and first_level = '.$data['firstLevel'].' and second_level = '.$data['secondLevel'].' and third_level = '.$data['thirdLevel'];
            }
        } else {
            $totalCount = Proxy::count();
        }
        $startTime = Time::toTime($year.'-'.$month.'-01');
        $endTime = strtotime("+1months", $startTime);
        /*$sql = "select sl.id, 
                    sl.name, 
                    IFNULL(sum(pay_fee + system_full_subsidy + activity_new_money + discount_fee + integral_fee), 0) AS totalPayfee,
                    count(od.id) AS totalNum,
                    IFNULL(sum(drawn_fee), 0) AS totalDrawnfee,
                    IFNULL(SUM(IF(pay_type <> 'cashOnDelivery', pay_fee, 0)), 0) AS totalOnline, 
                    IFNULL(SUM(IF(pay_type = 'cashOnDelivery', pay_fee, 0)), 0) AS totalCash,
                    IFNULL(SUM(IF(discount_fee > total_fee, total_fee, discount_fee)),0) AS totalDiscountFee,
                    IFNULL(sum(integral_fee), 0) as totalIntegralFee,
                        IFNULL(sum(system_full_subsidy), 0) as systemFullSubsidy,
                        IFNULL(sum(seller_full_subsidy), 0) as sellerFullSubsidy,
                        IFNULL(sum(activity_goods_money), 0) as activityGoodsMoney,
                        IFNULL(sum(activity_new_money), 0) as activityNewMoney
                from ".$prefix."seller as sl left join ".$prefix."order as od
                on od.seller_id = sl.id 
                ".($data['firstLevel'] > 0 ? " AND od.first_level = ".$data['firstLevel'] : " ")."
                ".($data['secondLevel'] > 0 ? " AND od.second_level = ".$data['secondLevel'] : " ")."
                ".($data['thirdLevel'] > 0 ? " AND od.third_level = ".$data['thirdLevel'] : " ")." 
                and od.pay_status = 1 
                and od.create_time BETWEEN ".$startTime." AND ".$endTime."
                and (od.status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.") OR (od.status = ".ORDER_STATUS_USER_DELETE." AND od.buyer_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_SELLER_DELETE." AND od.auto_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_ADMIN_DELETE." AND od.auto_finish_time > 0 AND od.cancel_time IS NULL)) 
                WHERE 1 ".($data['firstLevel'] > 0 ? " AND sl.first_level = ".$data['firstLevel'] : " ")."
                ".($data['secondLevel'] > 0 ? " AND sl.second_level = ".$data['secondLevel'] : " ")."
                ".($data['thirdLevel'] > 0 ? " AND sl.third_level = ".$data['thirdLevel'] : " ")." 
                and sl.type in (1,2)
                group by sl.id 
                order by totalPayfee DESC
                limit ".($page - 1) * $pageSize.", ".$pageSize ; 
				*/
        $sql = "SELECT P.id, P.name, P.level, 
                    IFNULL(sum(O.pay_fee + O.system_full_subsidy + O.activity_new_money + O.discount_fee + O.integral_fee), 0) as totalPayfee,
                    count(O.id) as totalNum,
                    IFNULL(sum(drawn_fee), 0) as totalDrawnfee ,
                    IFNULL(SUM(IF(pay_type <> 'cashOnDelivery', pay_fee, 0)), 0) AS totalOnline, 
                    IFNULL(SUM(IF(pay_type = 'cashOnDelivery', pay_fee, 0)), 0) AS totalCash,
                    IFNULL(sum(IF(discount_fee > total_fee, total_fee, discount_fee)), 0) as totalDiscountFee ,
                    IFNULL(sum(integral_fee), 0) as totalIntegralFee,
                        IFNULL(sum(system_full_subsidy), 0) as systemFullSubsidy,
                        IFNULL(sum(seller_full_subsidy), 0) as sellerFullSubsidy,
                        IFNULL(sum(activity_goods_money), 0) as activityGoodsMoney,
                        IFNULL(sum(activity_new_money), 0) as activityNewMoney
                FROM ".$prefix."proxy AS P
                LEFT JOIN ".$prefix."order AS O ON (O.first_level = P.id OR O.second_level = P.id OR O.third_level = P.id) 
                AND (
                    O.status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.")
                    OR (O.status = ".ORDER_STATUS_USER_DELETE." AND O.buyer_finish_time > 0 AND O.cancel_time IS NULL) 
                    OR (O.status = ".ORDER_STATUS_SELLER_DELETE." AND O.auto_finish_time > 0 AND O.cancel_time IS NULL) 
                    OR (O.status =  ".ORDER_STATUS_ADMIN_DELETE." AND O.auto_finish_time > 0 AND O.cancel_time IS NULL)
                )
                AND O.create_time BETWEEN ".$startTime." AND ".$endTime."
                WHERE 1 ". ($name ? ' and P.id = '.$proxy->id : '') ."
                GROUP BY P.id 
                ORDER BY totalPayfee DESC,
                P.id ASC
                limit ".($page - 1) * $pageSize.", ".$pageSize;

        $list = DB::select($sql);
        return ["list" => $list, "totalCount" => $totalCount];
    }

    /**
     *  代理商家列表
     */
    public static function getSellerListByMonth($proxyId, $month, $year, $page, $pageSize){
        $proxy = Proxy::find($proxyId);
        if(empty($proxy)){
            return ["list" => '', "totalCount" => '', "sum" => ''];
        }
        switch ($proxy->level) {
            case '2':
                $data['firstLevel'] = (int)$proxy->pid;
                $data['secondLevel'] = (int)$proxy->id;
                $data['thirdLevel'] = 0;
                break;
            case '3':
                $parentProxy = Proxy::find($proxy->pid);
                $data['firstLevel'] = (int)$parentProxy->pid;
                $data['secondLevel'] = (int)$proxy->pid;
                $data['thirdLevel'] = (int)$proxy->id;
                break;

            default:
                $data['firstLevel'] = $proxy->id;
                $data['secondLevel'] = 0;
                $data['thirdLevel'] = 0;
                break;
        }
        // DB::connection()->enableQueryLog(); 
        $prefix = DB::getTablePrefix();
        $totalCount = Seller::orderBy('id', 'DESC');

        if($data['firstLevel'] > 0){
            $totalCount->where('first_level', '=', $data['firstLevel']);
        }

        if($data['secondLevel'] > 0){
            $totalCount->where('second_level', '=', $data['secondLevel']);
        }

        if($data['thirdLevel'] > 0){
            $totalCount->where('third_level', '=', $data['thirdLevel']);
        }

        $totalCount = $totalCount->count();

        $proxySqlStr = '';

        if($data['firstLevel'] > 0){
            $proxySqlStr .= "and od.first_level = ".$data['firstLevel'];
        }

        if($data['secondLevel'] > 0){
            $proxySqlStr .= " and od.second_level = ".$data['secondLevel'];
        }

        if($data['thirdLevel'] > 0){
            $proxySqlStr .= " and od.third_level = ".$data['thirdLevel'];
        }

        $startTime = Time::toTime($year.'-'.$month.'-01');
        $endTime = strtotime('');
        $endTime = strtotime("+1months", $startTime);
        $sumsql = "select sl.id, 
                    sl.name, 
                    IFNULL(sum(pay_fee + system_full_subsidy + activity_new_money + discount_fee + integral_fee), 0) AS totalPayfee,
                    count(od.id) AS totalNum,
                    IFNULL(sum(drawn_fee), 0) AS totalDrawnfee,
                    IFNULL(SUM(IF(pay_type <> 'cashOnDelivery', pay_fee, 0)), 0) AS totalOnline, 
                    IFNULL(SUM(IF(pay_type = 'cashOnDelivery', pay_fee, 0)), 0) AS totalCash,
                    IFNULL(SUM(IF(discount_fee > total_fee, total_fee, discount_fee)),0) AS totalDiscountFee,
                    IFNULL(sum(integral_fee), 0) as totalIntegralFee,
                        IFNULL(sum(system_full_subsidy), 0) as systemFullSubsidy,
                        IFNULL(sum(seller_full_subsidy), 0) as sellerFullSubsidy,
                        IFNULL(sum(activity_goods_money), 0) as activityGoodsMoney,
                        IFNULL(sum(activity_new_money), 0) as activityNewMoney
                from ".$prefix."seller as sl left join ".$prefix."order as od
                on od.seller_id = sl.id 
                ".($data['firstLevel'] > 0 ? " AND od.first_level = ".$data['firstLevel'] : " ")."
                ".($data['secondLevel'] > 0 ? " AND od.second_level = ".$data['secondLevel'] : " ")."
                ".($data['thirdLevel'] > 0 ? " AND od.third_level = ".$data['thirdLevel'] : " ")." 
                and od.pay_status = 1 
                and od.create_time BETWEEN ".$startTime." AND ".$endTime."
                and (od.status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.") OR (od.status = ".ORDER_STATUS_USER_DELETE." AND od.buyer_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_SELLER_DELETE." AND od.auto_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_ADMIN_DELETE." AND od.auto_finish_time > 0 AND od.cancel_time IS NULL)) 
                WHERE 1 ".($data['firstLevel'] > 0 ? " AND sl.first_level = ".$data['firstLevel'] : " ")."
                ".($data['secondLevel'] > 0 ? " AND sl.second_level = ".$data['secondLevel'] : " ")."
                ".($data['thirdLevel'] > 0 ? " AND sl.third_level = ".$data['thirdLevel'] : " ")." 
                and sl.type in (1,2)";
        $sum = DB::select($sumsql);

        $sql = "select sl.id, 
                    sl.name, 
                    IFNULL(sum(pay_fee + system_full_subsidy + activity_new_money + discount_fee + integral_fee), 0) AS totalPayfee,
                    count(od.id) AS totalNum,
                    IFNULL(sum(drawn_fee), 0) AS totalDrawnfee,
                    IFNULL(SUM(IF(pay_type <> 'cashOnDelivery', pay_fee, 0)), 0) AS totalOnline, 
                    IFNULL(SUM(IF(pay_type = 'cashOnDelivery', pay_fee, 0)), 0) AS totalCash,
                    IFNULL(SUM(IF(discount_fee > total_fee, total_fee, discount_fee)),0) AS totalDiscountFee,
                    IFNULL(sum(integral_fee), 0) as totalIntegralFee,
                        IFNULL(sum(system_full_subsidy), 0) as systemFullSubsidy,
                        IFNULL(sum(seller_full_subsidy), 0) as sellerFullSubsidy,
                        IFNULL(sum(activity_goods_money), 0) as activityGoodsMoney,
                        IFNULL(sum(activity_new_money), 0) as activityNewMoney
                from ".$prefix."seller as sl left join ".$prefix."order as od
                on od.seller_id = sl.id 
                ".($data['firstLevel'] > 0 ? " AND od.first_level = ".$data['firstLevel'] : " ")."
                ".($data['secondLevel'] > 0 ? " AND od.second_level = ".$data['secondLevel'] : " ")."
                ".($data['thirdLevel'] > 0 ? " AND od.third_level = ".$data['thirdLevel'] : " ")." 
                and od.pay_status = 1 
                and od.create_time BETWEEN ".$startTime." AND ".$endTime."
                and (od.status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.") OR (od.status = ".ORDER_STATUS_USER_DELETE." AND od.buyer_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_SELLER_DELETE." AND od.auto_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_ADMIN_DELETE." AND od.auto_finish_time > 0 AND od.cancel_time IS NULL)) 
                WHERE 1 ".($data['firstLevel'] > 0 ? " AND sl.first_level = ".$data['firstLevel'] : " ")."
                ".($data['secondLevel'] > 0 ? " AND sl.second_level = ".$data['secondLevel'] : " ")."
                ".($data['thirdLevel'] > 0 ? " AND sl.third_level = ".$data['thirdLevel'] : " ")." 
                and sl.type in (1,2)
                group by sl.id 
                order by totalPayfee DESC
                limit ".($page - 1) * $pageSize.", ".$pageSize ;
        $list = DB::select($sql);
        //file_put_contents('/mnt/test/sq/storage/logs/asdfsdf.log', print_r(DB::getQueryLog(),true)."\n", FILE_APPEND);
        return ["list" => $list, "totalCount" => $totalCount, "sum" => $sum[0],"proxy" => $proxy];
    }
}
