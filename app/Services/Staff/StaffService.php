<?php namespace YiZan\Services\Staff;
use YiZan\Models\Staff\SellerStaff;
use YiZan\Models\StaffMap;
use YiZan\Models\Seller;
use YiZan\Models\SellerStaffBank;
use YiZan\Models\SellerStaffMoneyLog;
use YiZan\Models\System\User;
use YiZan\Utils\Helper;
use YiZan\Models\UserVerifyCode;
use YiZan\Models\SystemConfig;

use YiZan\Services\UserService as baseUserService;
use YiZan\Services\SellerStaffService;
use YiZan\Services\SellerService as baseSellerService;

use YiZan\Utils\String;
use DB, Lang,Exception,Validator;
class StaffService extends \YiZan\Services\SellerStaffService {

    /**
     * 更改员工信息
     * @param $staffId 员工编号
     * @param $name 用户昵称
     * @param $avatar 头像
     */
    public static function updateInfo($userId, $avatar) {
        $result = [
            'code'	=> 0,
            'data'	=> null,
            'msg' => Lang::get('api_staff.success.update')
        ];

        if ($avatar == '') {
            $result['code'] = 60001;
            return $result;
        }
        $user = User::where('id', $userId)->first();
        $staff = SellerStaffService::getByUserId($userId);
        $seller = baseSellerService::getById(0, $userId);

        //找不到卖家信息,则返回
        if (!$staff && !$seller) 
        {
            $result['code'] = 10108;
            return $result;
        }
        if ($seller) {
            $role = 1; // 只是商家，不是人员
            $sellerId = $seller->id;
            $staffId = 0;
            $mobile = $seller->mobile;
            $name = $seller->name;
        }
        if ($staff) {
            switch ($staff->type) {
                case 0:
                    $role |= 7; // 既是商家又是人员
                    break;
                case 1:
                    $role |= 2; // 配送人员
                    break;
                case 2:
                    $role |= 4; // 服务人员
                    break;
                case 3:
                    $role |= 6; // 既是配送又是服务人员
                    break;
            }
            $staffId = $staff->id;
            $sellerId = $staff->seller_id;
            $mobile = $staff->mobile;
            $name = $staff->name;
        }
        //头像图片上传
       // var_dump($avatar);
        $logo = $avatar;
        if ($avatar != $user->avatar) {
            $avatar = self::moveUserImage($user->id, $avatar);
          //  var_dump($avatar);
            if (!$avatar) {
                $result['code'] = 10113;
                return $result;
            }
        }

        $data = [
            'avatar' => $avatar,
        ];

        if ($role != 1) {
            if ($logo != $staff->avatar) {
                $logo = self::moveStaffImage($staff->seller_id, $staff->id, $logo);
                if (!$logo) {//转移图片失败
                    $result['code'] = 10202;
                    return $result;
                }
            }
            $staffdata = [
                'avatar' => $logo,
            ];
            if (false === SellerStaff::where('id', $staff->id)->update($staffdata)) {
                $result['code'] = 60004;
                return $result;
            }
        }
        if (false === User::where('id', $userId)->update($data)) {
            $result['code'] = 60004;
            return $result;
        }

        $result['data'] = 
            [
                "id"=> $userId,
                "staffId" => $staffId,
                "sellerId"=> $sellerId,
                "mobile"=>$mobile,
                "name"=> $name,
                "avatar"=>$avatar,
                "role" => $role,
                'bg' => asset('wap/community/client/images/top-bg.png'),
            ];
        
        return $result;
    }

    /**
     *
     * @param $staffId 员工编号
     * @param $address 地址
     * @param $mapPoint 地图坐标
     */
    public static function address($staffId, $address, $mapPoint) {
        $result = [
            'code'	=> 0,
            'data'	=> null,
            'msg' => Lang::get('api_staff.success.update')
        ];
        if ($address == '') {
            $result['code'] = 60005;
            return $result;
        }
        if ($mapPoint == '') {
            $result['code'] = 60006;
            return $result;
        }
        $mapPoint = Helper::foramtMapPoint($mapPoint);
        if (!$mapPoint){
            $result['code'] = 60006;    // 地图定位错误
            return $result;
        }
        //更新员工表
        $format_map_point = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");
        $update = SellerStaff::where('id', $staffId)
                            ->update([
                                'address' => $address,
                                'map_point' => $format_map_point,
                                'map_point_str' => $mapPoint
                            ]);
        if ($update === false) {
            $result['code'] = 60004;
            return $result;
        }
        //更新员工坐标表
        $seller_id = SellerStaff::where('id', $staffId)->pluck('seller_id');
        $map = StaffMap::where('staff_id', $staffId)->first();
        if ($map) {
            StaffMap::where('staff_id', $staffId)->update(['map_point' => $format_map_point]);
        } else {
            StaffMap::insert([
                'seller_id' => $seller_id,
                'staff_id' => $staffId,
                'map_point' => $format_map_point
            ]);
        }
        return $result;
    }

    /**
     * 更新服务范围
     * @param $staffId 员工编号
     * @param $mapPos 服务范围地图坐标
     */
    public static function range($staffId, $mapPos) {
        $result = [
            'code'	=> 0,
            'data'	=> null,
            'msg' => Lang::get('api_staff.success.update')
        ];

        if ($mapPos == '') {
            $result['code'] = 60007;
            return $result;
        }
        $mapPos = Helper::foramtMapPos($mapPos);
        if (!$mapPos){
            $result['code'] = 60007;    // 服务范围错误
            return $result;
        }
        //更新员工表
        $format_map_pos = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
        $update = SellerStaff::where('id', $staffId)
            ->update([
                'map_pos' => $format_map_pos,
                'map_pos_str' => $mapPos["str"]
            ]);
        if ($update === false) {
            $result['code'] = 60004;
            return $result;
        }
        //更新员工坐标表
        $seller_id = SellerStaff::where('id', $staffId)->pluck('seller_id');
        $map = StaffMap::where('staff_id', $staffId)->first();
        if ($map) {
            StaffMap::where('staff_id', $staffId)->update(['map_pos' => $format_map_pos]);
        } else {
            StaffMap::insert([
                'seller_id' => $seller_id,
                'staff_id' => $staffId,
                'map_pos' => $format_map_pos
            ]);
        }
        return $result;
    }	    
    /**
     * 检测验证码
     * @param $mobile 手机号码
     * @param $verifyCode 验证码
     */
    public static function verifymobile($staffId,$mobile, $verifyCode) {
         $result = [
            'code'	=> 0,
            'msg' => Lang::get('api_staff.success.verify_error')
        ];
        if(baseUserService::checkVerifyCode($verifyCode, $mobile) == false)
        {
           $result['code'] = 10121;
           $result['data'] = false;  
        }else {
           $result['code'] = 10122;
           $result['data'] = true;              
        }
        return $result;
    }
    /**
     * 检测验证码
     * @param $mobile 手机号码
     * @param $verifyCode 验证码
     */
    public static function mobile($userId,$oldmobile,$mobile, $verifyCode) {
    
        if(baseUserService::checkVerifyCode($verifyCode, $mobile) == false){
           $result['code'] = 10104;
           return $result;
        }else{
            $xuser = baseUserService::getByMobile($mobile);            
            //找到会员时
            if ($xuser)
            {
                $result['code'] = 10118;
                return $result;
            }else{                
                $ouser = baseUserService::getByMobile($oldmobile);
                if ($ouser->id > 0 && $ouser->id == $userId)
                {
                    DB::beginTransaction();
                    try {                        
                        User::where('mobile',$oldmobile)->where("id",$ouser->id)->update(['mobile'=>$mobile]); 
                        $staff = SellerStaffService::getByUserId($ouser->id);
                        $seller = baseSellerService::getById(0, $ouser->id);
                        if ($staff) {
                            SellerStaff::where('user_id', $ouser->id)->update(['mobile' => $mobile]);
                        }
                        if ($seller) {
                            Seller::where('user_id', $ouser->id)->update(['mobile' => $mobile]);
                        }
                        DB::commit();
                        $result['code'] = 0;
                        $result['data'] = baseUserService::getByMobile($mobile);
                    } catch (Exception $e) {
                        DB::rollback();
                        $result['code'] = 60013;
                    }
                }else{
                    $result['code'] = 10209;
                }                
            }
        }
        return $result;
    }

    /**
     * 商家账单
     * @param int $sellerId             商家编号
     * @param int $status                 类型,1收入、2提现、3充值
     * @return
     */
    public function getStaffAccount($staffId, $type, $status = 0, $page){
        $list = [];
        $sellerStaff = SellerStaff::where('id', $staffId)->first();
        if (!$sellerStaff) {
            $result['code'] = 10108;
            return $result;
        }

        $lists = SellerStaffMoneyLog::where('staff_id', $staffId)
            ->where('money', '>', 0);

        if ($type == 1 && $status != 2) {
            if ($status == 1) {
                $lists->whereIn('type', [SellerStaffMoneyLog::TYPE_ORDER_CONFIRM, SellerStaffMoneyLog::TYPE_SYSTEM_RECHARGE, SellerStaffMoneyLog::TYPE_SEND_FEE]);
            } else if($status == 3){
                $lists->whereIn('type', [SellerStaffMoneyLog::TYPE_SELLER_RECHARGE, SellerStaffMoneyLog::TYPE_SYSTEM_RECHARGE]);
            } else {
                // 取消待到账的余额
                $lists->whereIn('type', [
                    SellerStaffMoneyLog::TYPE_APPLY_WITHDRAW,
                    SellerStaffMoneyLog::TYPE_ORDER_CONFIRM,
                    SellerStaffMoneyLog::TYPE_SELLER_RECHARGE,
                    SellerStaffMoneyLog::TYPE_SYSTEM_RECHARGE,
                    SellerStaffMoneyLog::TYPE_SYSTEM_DEBIT,
                    SellerStaffMoneyLog::TYPE_SEND_FEE
                ]);
            }
        } else {
            $lists->whereIn('type', [SellerStaffMoneyLog::TYPE_APPLY_WITHDRAW]);
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
            ['moneyColor' => 'f_danger', 'statusColor' => 'c_e19c23', 'statusStr' => '已到账'],
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

            if ($v['type'] == SellerStaffMoneyLog::TYPE_APPLY_WITHDRAW) {
                $list[$k]['money'] = '-' . $v['money'];
                $list[$k]['remark'] = '提现';
            } else if($v['type'] == SellerStaffMoneyLog::TYPE_SELLER_RECHARGE){
                $list[$k]['money'] = '+' . $v['money'];
                $list[$k]['remark'] = '充值';
            } else if($v['type'] == SellerStaffMoneyLog::TYPE_SYSTEM_RECHARGE) {
                $list[$k]['money'] = '+' . $v['money'];
                $list[$k]['remark'] = '平台充值';
            } else if($v['type'] == SellerStaffMoneyLog::TYPE_SYSTEM_DEBIT) {
                $list[$k]['money'] = '-' . $v['money'];
                $list[$k]['remark'] = '平台扣款';
            }  else if($v['type'] == SellerStaffMoneyLog::TYPE_SEND_FEE) {
                $list[$k]['money'] = '+'.$v['money'];
                $list[$k]['remark'] = '配送服务费';
            } else {
                $list[$k]['money'] = '+' . $v['money'];
                $list[$k]['remark'] = $v['type'] == SellerStaffMoneyLog::TYPE_ORDER_PAY ? '待到账' : '入余额';
            }

            if ($v['type'] == SellerStaffMoneyLog::TYPE_ORDER_PAY) {
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

    public function getStaffInfo($staffId){
        $sellerstaffbank = SellerStaffBank::where('staff_id',$staffId)->first();
        if(!empty($sellerstaffbank)){
            $result['data'] = $sellerstaffbank->toArray();
            $result['data']['notice'] = SystemConfig::where('code', 'staff_bank_info')->pluck('val');
        }
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
    public static function saveBankInfo($staffId,$id, $bank, $bankNo, $mobile,$name, $verifyCode){
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
            'staff_id' => $staffId,
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
            $bankObj = SellerStaffBank::where("staff_id",$staffId)->where('id', $id)->first();
            if (!$bankObj) {
                $result['code'] = 10154;
                return $result;
            }
        }else{
            $bankObj = new SellerStaffBank();
        }
        //检测验证码
        $verifyCodeId = baseUserService::checkVerifyCode($verifyCode, $mobile, UserVerifyCode::TYPE_BANKINFO);
        if (!$verifyCodeId) {
            $result['code'] = 10104;
            return $result;
        }
        $bankObj->staff_id     = $staffId;
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
    public static function getBankInfo($staffId,$id=0) {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );
        $bank = SellerStaffBank::where('staff_id', $staffId);
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

}
