<?php namespace YiZan\Services\Buyer;

use YiZan\Models\User;
use YiZan\Models\UserBank;
use YiZan\Models\UserPayLog;
use YiZan\Services\RegionService as baseRegionService;
use YiZan\Services\PaymentService as basePaymentService;
use YiZan\Models\UserVerifyCode;

use YiZan\Services\UserIntegralService as baseUserIntegralService;
use YiZan\Services\SellerMoneyLogService as baseSellerMoneyLogService;
use YiZan\Models\Seller;
use YiZan\Models\Order;
use YiZan\Models\SellerWithdrawMoney;
use YiZan\Utils\Image;
use YiZan\Utils\String;
use YiZan\Utils\Helper;
use YiZan\Utils\Encrypter;

use Lang, DB, Time,Validator,Config;

class UserService extends \YiZan\Services\UserService {

    /**
     * 检测验证码
     * @param $mobile 手机号码
     * @param $verifyCode 验证码
     */
    public static function verifymobile($mobile, $verifyCode) {
        $result = [
            'code'	=> 0,
            'data' => true,
            'msg' => Lang::get('api.success.verify_success')
        ];
        if(self::checkVerifyCode($verifyCode, $mobile) == false)
        {
            $result['code'] = 10104;
            $result['data'] = false;
        }
        return $result;
    }
    /**
     * 检测是否可以更改手机号码
     * @param $mobile 手机号码
     * @param $verifyCode 验证码
     */
    public static function isUpdateM($id) {

        $isUpdate = Seller::where("user_id",$id)->count();
        if($isUpdate == 0 ){
            return true;
        }
        return false;
    }

    public static function updateIp($userId) {
        $location = baseRegionService::getCityByIp(CLIENT_IP);
        $user = User::find($userId);
        $user->login_time         = UTC_TIME;
        $user->login_province_id  = $location['province'];
        $user->login_city_id      = $location['city'];
        $user->login_ip           = CLIENT_IP;

        $user->save();
    }



    /**
     * 充值
     * @param  [type] $userId  [description]
     * @param  [type] $payment [description]
     * @return [type]          [description]
     */
    public static function payCharge($userId, $payment, $extend = [], $money,$isFx = 0) {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.user_pay_order')
        );
        $user = User::find($userId);
        if (!$user) {
            $result['code'] = 99996;
            return $result;
        }
        $payLog = basePaymentService::createPayLog($money, $payment, $extend, $userId,$isFx);

        if (is_numeric($payLog))
        {
            $result['code'] = abs($payLog);

            return $result;
        }

        $result['data'] = $payLog;

        return $result;
    }

    public static function getBalance($userId, $type = 0,$page, $pageSize = 20){
        if($type == 0){
            $type = [2,3,4,9,10];
            $paymentType = ['balancePay', 'systemRecharge', 'systemDebit','withdrawals'];
        }else{
            $type = [9,10];
            $paymentType = ['withdrawals'];
        }
        $paylogs = UserPayLog::where('user_id', $userId)
            ->where('status', 1)
            ->where(function($query) use($type,$paymentType){
                $query->whereIn('payment_type',$paymentType)
                    ->orWhere(function($queryOne)use($type){
                        $queryOne->whereIn('pay_type', $type)
                            ->where('payment_type', '<>', 'balancePay');
                    });
            })
            ->with('withdrawal')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->orderBy('id', 'DESC')
            ->get()
            ->toArray();
        foreach ($paylogs as $key => $value) {
            $paylogs[$key]['createTime'] = Time::toDate($value['createTime'], 'Y-m-d H:i:s');
            $paylogs[$key]['payTypeStr'] = Lang::get('wap.pay_type.'.$value['payType']);
        }
        return $paylogs;
    }

    public static function balance($userId){
        $balance = User::where('id', $userId)
            ->pluck('balance');
         $lockAmount = User::where('id', $userId)
			->pluck('lock_amount');
        return ['balance'=> $balance,'lockAmount'=> $lockAmount];
    }

    public static function isShowData($userId){
        $data = User::where('id', $userId)
            ->select('invitation_id', 'invitation_type')->first();
        $res = [];
        if($data->invitation_id > 0){
            if(strtolower($data->invitation_type) == "user"){
                $res['name'] = User::where('id',$data->invitation_id)->pluck("name");
            }else{
                $res['name'] = Seller::where('id',$data->invitation_id)->pluck("name");
            }
            $res['count'] = User::where("invitation_type",$data->invitation_type)->where('invitation_id',$data->invitation_id)->count();
        }
        $res['url'] =  \YiZan\Models\SystemConfig::where('code', 'public_address_number')->pluck('val');
        $res['downloadAddress'] =  \YiZan\Models\SystemConfig::where('code', 'download_address')->pluck('val');
        return $res;
    }

    public static function getbank($userId,$id){
        $balance = UserBank::where('user_id', $userId);
        if($id > 0){
            $balance->where('id',$id);
        }
        $bank = $balance->first();
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

    public static function getAccount($userId){
        $data = [
        'money' => 0,
        'lockMoney' => 0,
        'waitConfirmMoney' => 0,
        ];
        $result = User::where('id',$userId)->first();
        if ($result) {
            $data['money'] = $result->balance;
        }
        if($data['money'] >= 100){
            $data['moneyCycle'] = $result->balance;
        }else{
            $data['moneyCycle'] = 0;
        }
        $lockCycl = false;
        //验证服务会员银行卡信息
        $bankinfo =UserBank::where('user_id', $userId)->first();
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
        $data['notice'] =  \YiZan\Models\SystemConfig::where('code', 'staff_bank_info')->pluck('val');
        $data['moneyCycleDay'] = Time::toDate( $result->money_cycle_day?$result->money_cycle_day:UTC_DAY,"Y-m-d");
        return $data;
    }
    public static function getInfo($userId){
        $user = User::where('id', $userId)->first();
        $isPayPwd = !empty($user->pay_pwd) ? '1' : '0';
        return ['integral' => $user->integral, 'isPayPwd' => $isPayPwd];
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
    public static function saveBankInfo($userId,$id, $bank, $bankNo, $mobile,$name, $verifyCode){
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
            'user_id'   => $userId,
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
            $bankObj = UserBank::where("user_id",$userId)->where('id', $id)->first();
            if (!$bankObj) {
                $result['code'] = 10154;
                return $result;
            }
        }else{
            $bankObj = new UserBank();
        }
        //检测验证码
        $verifyCodeId = UserService::checkVerifyCode($verifyCode, $mobile, UserVerifyCode::TYPE_BANKINFO);
        if (!$verifyCodeId) {
            $result['code'] = 10104;
            return $result;
        }
        $bankObj->user_id       = $userId;
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
     * 修改支付密码
     * @param int $userId 会员编号
     * @param string $oldPwd 旧密码
     * @param string $newPwd 新密码
     * @param string $verifyCode 验证码
     * @param int $type 是否为忘记密码操作 1:是 2:否
     * @return array
     */
    public static function rePayPwd($userId, $oldPwd, $newPwd, $verifyCode, $type) {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.user_repaypass')
        );
        $user = User::where('id', $userId)->first();

        //当为忘记密码时,检测验证码
        if (!empty($verifyCode)) {
            $verifyCodeId = self::checkVerifyCode($verifyCode, $user->mobile, 'repaypass');
            if (!$verifyCodeId) {
                $result['code'] = 10104;
                return $result;
            }
        }
        //当为修改密码时
        if (!empty($user->pay_pwd) && $type != 1) {
            //原密码错误
            if ($user->pay_pwd != md5(md5($oldPwd) . $oldPwd)) {
                $result['code'] = 10119;
                return $result;
            }
            //新密码与原密码相同
            if ($user->pay_pwd == md5(md5($newPwd) . $newPwd)) {
                $result['code'] = 10124;
                return $result;
            }
        }

        //新密码格式不正确
        if (empty($newPwd) || strlen($newPwd) != 6 ) {
            $result['code'] = 10123;
            return $result;
        }
        if(!is_numeric($newPwd)){
            $result['code'] = 10126;
            return $result;
        }
        //修改支付密码
        $user->pay_pwd = md5(md5($newPwd) . $newPwd);
        $user->save();

        return $result;
    }

    /**
     * 检查用户 通过openId查找出用户
     */
    public function checkUserOpenId($unionid,$openId){
        if(empty($openId)){
            return '';
        }

        $user = false;
        if(!empty($unionid)){
            $user = User::where('unionid', $unionid)->first();
        }

        if(!$user){
            $user = User::where('openid', $openId)->first();
        }

        if(empty($user)){
            return '';
        }else{

            $user = $user->toArray();
            self::updateIp($user['id']);
            return $user;
        }
    }

    public static function createUserWeixin($mobile, $verifyCode, $pwd, $type = 'reg',$openId,$unionid,$name,$avatar,$invitationType="",$invitationId="") {
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> Lang::get('api.success.create_user_'.$type)
        );
        $rules = array(
            'openid' 	 => ['required']
        );
        $messages = array(
            'openid.required' 		=> '99999'
        );
        $validator = Validator::make([
            'openid' 	 => $openId
        ], $rules, $messages);
        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        $pwd = String::randString(6);
        $pwd = strval($pwd);
        $crypt 	= String::randString(6);
        $pwd 	= md5(md5($pwd) . $crypt);

        $user = self::checkUserOpenId($unionid,$openId);

        if($type == 'reg' && $user){
            $result['code'] = 10117; //用户名已存在
            return $result;
        }

        $is_new_user = false;
        if (!$user) {
            $is_new_user = true;
            $location = baseRegionService::getCityByIp(CLIENT_IP);

            $image_url = $avatar;
            $ch = curl_init();
            //设置选项，包括URL
            curl_setopt($ch, CURLOPT_URL, $image_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            //执行并获取HTML文档内容
            $content = curl_exec($ch);
            //释放curl句柄
            curl_close($ch);
            //打印获得的数据
            $name2 = Image::getFormArgs(1);
            $path = $name2['image_url'];
            $imageUrl = Image::upload($content, $path ,1);
            $mobile 			= '';
			
			$name = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '',$name);
            $name   = !empty($name) ? $name : '';			
            $name_match		= String::strToUnicode($name);
            $reg_time 		= UTC_TIME;
            $province_id 		= $location['province'];
            $city_id 			= $location['city'];
            $reg_ip 			= CLIENT_IP;
            $reg_province_id 	= $location['province'];
            $reg_city_id 		= $location['city'];
            $is_sms_verify 	= 1;
            $avatar = !empty($imageUrl) ? $imageUrl : '';

            $dbPrefix = env('DB_PREFIX');
            if($invitationType != "" && $invitationId  !="" ) {
                $invitation_type 	= strtolower($invitationType);
                $invitation_id 	= $invitationId;
                $sql = "INSERT INTO `{$dbPrefix}user` (`mobile`, `name_match`, `name`, `reg_time`, `province_id`, `city_id`, `reg_ip`, `reg_province_id`, `reg_city_id`, `is_sms_verify`, `crypt`, `pwd`
                        ,`invitation_type`, `invitation_id`,`openid`,`unionid`,`avatar`)
                        SELECT '{$mobile}', '{$name_match}', '{$name}', '{$reg_time}', {$province_id}, {$city_id}, '{$reg_ip}', {$reg_province_id}, {$reg_city_id}, {$is_sms_verify},
                        '{$crypt}', '{$pwd}','{$invitation_type}', '{$invitation_id}', '{$openId}','{$unionid}', '{$avatar}'
                         FROM DUAL
                        WHERE
                        NOT EXISTS (
                                SELECT
                                'openid'
                            FROM
                                {$dbPrefix}user
                            WHERE
                                openid = '{$openId}'
                        )";
            }else{
                $sql = "INSERT INTO `{$dbPrefix}user` (`mobile`, `name_match`, `name`, `reg_time`, `province_id`, `city_id`, `reg_ip`, `reg_province_id`, `reg_city_id`, `is_sms_verify`,
                        `crypt`, `pwd`,`openid`,`unionid`,`avatar`)
                        SELECT '{$mobile}', '{$name_match}', '{$name}', '{$reg_time}', {$province_id}, {$city_id}, '{$reg_ip}', {$reg_province_id}, {$reg_city_id}, {$is_sms_verify},
                        '{$crypt}', '{$pwd}', '{$openId}','{$unionid}', '{$avatar}'
                         FROM DUAL
                        WHERE
                        NOT EXISTS (
                                SELECT
                                'openid'
                            FROM
                                {$dbPrefix}user
                            WHERE
                                openid = '{$openId}'
                        )";
            }
            $is_suceess = DB::affectingStatement($sql);

            if($is_suceess > 0){
				$user = User::where('openid', $openId)->first();
                //$user = self::getByMobile($mobile);
            }else{
                $result['code'] = 10107;
                return $result;
            }
        }else{
            $user->crypt 			= $crypt;
            $user->pwd 				= $pwd;
            $user->save();
        }

        if ($user) {
            if ($is_new_user) {
                PromotionService::issueUserRegPromotion($user->id);

                //积分活动
                baseUserIntegralService::createIntegralLog($user->id, 1, 2, 0);
            }
            $result['data'] = $user;
        } else {
            $result['code'] = 10107;
        }
        return $result;
    }

    public function bindWeixin($userId,$openid,$unionid){
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );

        if(empty($openid)){
            $result['code'] = '88001';
            return $result;
        }

        if(empty($unionid)){
            $user = User::where('openid', $openid)->first();
        }else{
            $user = User::where('unionid', $unionid)->first();
        }
        if(!empty($user)){
            $result['code'] = '88002';
            return $result;
        }

        $user2 = User::where('id', $userId)->first();
        if(!empty($user2['union'])){
            $result['code'] = '88003';
            return $result;
        }

        $user = User::find($userId);
        $user->openid         = $openid;
        $user->unionid  = $unionid;
        $user->save();

        return $result;
    }

    /**
     * 微信手机号绑定
     */
    public function bindmobile($userId,$unionid,$openid,$mobile,$verifyCode,$pwd){
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );

        $rules = array(
            'mobile' => ['required','regex:/^1[0-9]{10}$/'],
            'pwd' 	 => ['required','min:6','max:20']
        );

        $messages = array(
            'mobile.required'	=> '10101',
            'mobile.regex'		=> '10102',
            'pwd.required' 		=> '10105',
            'pwd.min' 			=> '10106',
            'pwd.max' 			=> '10106',
        );

        $validator = Validator::make([
            'mobile' => $mobile,
            'pwd' 	 => $pwd
        ], $rules, $messages);
        //验证信息
        if ($validator->fails())
        {
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $this->output($result);
        }

        if(empty($unionid)){
            $user = User::where('openid', $openid)->first();
        }else{
            $user = User::where('unionid', $unionid)->first();
        }
        if(empty($user) || (empty($unionid) && empty($openid))){
            $result['code'] = 60404;
            return $result;
        }

        $verifyCodeId = UserService::checkVerifyCode($verifyCode, $mobile);
        if($verifyCodeId == false){
            $result['code'] = 10104;
            return $result;
        }

        $xuser = UserService::getByMobile($mobile);
        //找到会员时
        if ($xuser)
        {
            $result['code'] = 30603;
            return $result;
        }

        //把unionid用户添加mobile
        DB::beginTransaction();
        try {
            $crypt 	= String::randString(6);
            //cz fanwe
            $encrypter = new Encrypter(md5(Config::get('app.fanwefx.appsys_id')));
            $pwd2 = $encrypter->encrypt($pwd);

            $newpwd 	= md5(md5($pwd) . $crypt);
            User::where('id', $user->id)->update(['mobile'=>$mobile,'crypt' => $crypt, 'pwd' => $newpwd, 'mine_pwd' => $pwd2]);

            UserVerifyCode::destroy($verifyCodeId);
            PromotionService::issueUserRegPromotion($user->id);
            //积分活动
            baseUserIntegralService::createIntegralLog($user->id, 1, 2, 0);
            DB::commit();
            $result['data'] = UserService::getByMobile($mobile);
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 10121;
        }

        return $result;
    }

    public function moveunionid($userId,$unionid,$openid,$mobile,$pwd){
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );

        $rules = array(
            'mobile' => ['required','regex:/^1[0-9]{10}$/'],
            'pwd' 	 => ['required','min:6','max:20']
        );

        $messages = array(
            'mobile.required'	=> '10101',
            'mobile.regex'		=> '10102',
            'pwd.required' 		=> '10105',
            'pwd.min' 			=> '10106',
            'pwd.max' 			=> '10106',
        );

        $validator = Validator::make([
            'mobile' => $mobile,
            'pwd' 	 => $pwd
        ], $rules, $messages);
        //验证信息
        if ($validator->fails())
        {
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $this->output($result);
        }

        if(empty($unionid)){
            $user = User::where('openid', $openid)->first();
        }else{
            $user = User::where('unionid', $unionid)->first();
        }

        $is_new_user = false;
        if(empty($user) || (empty($unionid) && empty($openid))){
            $is_new_user = true;
            $result['code'] = 60404;
            return $result;
        }
        $xuser = UserService::getByMobile($mobile);
        //未找到会员时
        if (empty($xuser))
        {
            $result['code'] = 60404;
            return $result;
        }

        //删除当前会员 把unionid添加到mobile那个用户那里
        DB::beginTransaction();
        try {
            $crypt 	= String::randString(6);
            //cz fanwe
            $encrypter = new Encrypter(md5(Config::get('app.fanwefx.appsys_id')));
            $pwd2 = $encrypter->encrypt($pwd);

            $newpwd 	= md5(md5($pwd) . $crypt);
            User::where('id', $xuser->id)->update(['openid'=>$openid,'unionid'=>$unionid,'crypt' => $crypt, 'pwd' => $newpwd, 'mine_pwd' => $pwd2]);
            User::where('id',$user->id)->delete();
            if ($is_new_user) {
                PromotionService::issueUserRegPromotion($xuser->id);
                //积分活动
                baseUserIntegralService::createIntegralLog($user->id, 1, 2, 0);
            }
            DB::commit();
            $result['data'] = UserService::getByMobile($mobile);
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 10121;
        }
        return $result;

    }
    /**
     * 商家申请提现
     * @param int $sellerId             商家编号
     * @param int   $amount             提现金额
     * @return
     */
    public static function applyUserAccount($userId, $amount){
        $where = ['user_id'=>$userId];
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> '申请成功'
        );
        $user = User::where("id",$userId)->first();
        if (!$user) {
            $result['code'] = 10108;
            return $result;
        }
        if ($amount < 0.001) {
            $result['code'] = 10152;
            return $result;
        }
        if ($amount < 100) {
            $result['code'] = 11153;
            return $result;
        }
        if ($amount > $user->balance) {
            $result['code'] = 10153;
            return $result;
        }
        if ($user) {
            $data['money'] = $user->balance;
        }
        if($data['money'] >= 100){
            $data['moneyCycle'] = $user->balance;
        }else{
            $data['moneyCycle'] = 0;
        }
        $bankinfo = UserBank::where($where)->first();
        if (!$bankinfo) {
            $result['code'] = 10154;
            return $result;
        }
        $lockCycl = false;
        if($data['moneyCycle'] >= 100){
            if($bankinfo){
                $bankinfo = $bankinfo->toArray();
                if($user->money_cycle_day != "" || $user->money_cycle_day > 1){
                    if($user->money_cycle_day <= UTC_DAY && $data['moneyCycle'] >= 100){
                        $lockCycl = true;
                    }
                }else{
                    $lockCycl = true;
                }
            }
        }
        if(!$lockCycl){
            $result['code'] = 11001;
            return $result;
        }
        $withdraw = new SellerWithdrawMoney();
        $withdraw->sn 			=	Helper::getSn();
        $withdraw->user_id	    =	$userId;
        $withdraw->money 		=	$amount;
        $withdraw->name 		=	$bankinfo['name'];
        $withdraw->bank 		=	$bankinfo['bank'];
        $withdraw->bank_no 		=	$bankinfo['bankNo'];
        $withdraw->create_time 	=	UTC_TIME;
        $withdraw->create_day 	=	UTC_DAY;
        $keyinfo  = \YiZan\Models\SystemConfig::where('code', 'money_cycle_day')->first();
        DB::beginTransaction();
        //插入取款表
        $withdraw_status = $withdraw->save();
        //修改会员可提现日期
        $time = UTC_DAY + 24 * 3600 * ($keyinfo->val+1);
		
		$user->balance = $user->balance - $amount;
        $user->money_cycle_day =$time;
        $user->lock_amount += $amount;
        $user_status = $user->save();

        if($withdraw_status && $user_status){
            DB::commit();
            //插入资金流水表

            baseSellerMoneyLogService::createUserLog($userId,9,$amount,'提款银行：'.$withdraw->bank.', 提款帐号：'.$withdraw->bank_no,1,$withdraw->id);
            $result['data'] = ['money' => $user->money - $amount];
        } else {
            DB::rollback();
            $result['code'] = 10155;
            return $result;
        }

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
        $verifyCodeId = UserService::checkVerifyCode($verifyCode, $mobile, UserVerifyCode::TYPE_BANKINFO);
        if (!$verifyCodeId) {
            $result['code'] = 10104;
            return $result;
        }
        return $result;
    }

    public static function fanweId($userId){
        $fanweId = User::where('id', $userId)->pluck('fanwe_id');
        return ['fanweId'=> $fanweId];
    }

    public static function getUserByfanweId($fawneId){
        return $result = User::where('fanwe_id', $fawneId)->first();
    }

    public function changeFanwe($userId,$fanweId){
        return  User::where('id',$userId)->update(['fanwe_id'=>$fanweId]);
    }

     /**
     * @param int $adminId 管理员编号 
     * @param int $userId 会员编号
     * @param double $money 金额
     * @param int $type 类型 : 1 充值 2扣款
     * @param string $remark 备注
     * @return [array]
     */
    public static function updatebalance($userId, $money, $type, $remark){
        $result = array(
            'code'  => 0,
            'data'  => '',
            'msg'   => Lang::get('api_system.success.handle')
        );
        $checkUser = User::where('id', $userId)->first();
        //会员不存在
        if (!$checkUser) {
            $result['code'] = '20101';
            return $result;
        }
        //金额不对
        if (!preg_match('/^[0-9]+\.?[0-9]{0,2}$/',$money) || (double)$money  < 0.01) {
            $result['code'] = '20201';
            return $result;
        }
        //类型不正确
        if (!in_array($type, [1,2])) {
            $result['code'] = '40417';
            return $result;
        }

        //余额不足
        if ($type == 2 && $checkUser->balance < $money) {
            $result['code'] = '20202';
            return $result;
        }

        DB::beginTransaction();
        try {
            if ($type == 1) {
                $data = [
                    'balance'       => DB::raw("IFNULL(balance, 0) + " . $money),
                    'total_money' => DB::raw("IFNULL(total_money, 0) + " . $money)
                ];
            } else {
                $data = [
                    'balance'       => DB::raw("balance - " . $money),
                    'total_money' => DB::raw("total_money - " . $money)
                ];
            }
            User::where('id', $userId)->update($data);
            UserPayLog::insert([
                'payment_type' => $type == 1 ? 'systemRecharge' : 'systemDebit',
                'pay_type' => $type == 1 ? 4 : 5,
                'user_id' => $userId,
                'order_id' => 0,
                'activity_id' => 0,
                'seller_id' => 0,
                'money' => (double)$money,
                'balance' => $type == 1 ? $checkUser->balance + $money : $checkUser->balance - $money,
                'content' => $remark,
                'pay_time' => UTC_TIME,
                'pay_day' => UTC_DAY,
                'create_time' => UTC_TIME,
                'create_day' => UTC_DAY,
                'status' => 1,
                'admin_id' => 0,    //平台
                'sn' => Helper::getSn()
            ]);
            DB::commit();
        } catch (Exception $e) {
            $result['code'] = '99999';
            DB::rollback();
        }

        return $result;
    }

    public function getFirstOrder($userId,$sellerId){
        //验证是否满足首单立减 cz
        $notStatus = [
            ORDER_STATUS_CANCEL_USER,
            ORDER_STATUS_CANCEL_AUTO,
            ORDER_STATUS_CANCEL_SELLER,
            ORDER_STATUS_CANCEL_ADMIN,
        ];
        $firstOrder = Order::where('user_id', $userId)->whereNotIn('status', $notStatus)->first();
        if(!empty($firstOrder)){
            return false;
        }
        return true;
    }

}
