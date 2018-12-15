<?php
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Models\UserIntegral;
use YiZan\Services\Buyer\UserService;
use YiZan\Services\Buyer\DistrictService;
use YiZan\Services\Buyer\UserAddressService;
use YiZan\Services\PushMessageService;
use YiZan\Services\UserIntegralService;
use YiZan\Services\UserMobileService;
use YiZan\Services\Buyer\ReadMessageService;
use YiZan\Services\Buyer\PropertyService;
use YiZan\Services\Buyer\PuserDoorService;
use YiZan\Services\Buyer\PropertyUserService;
use YiZan\Services\Buyer\DoorOpenLogService;
use YiZan\Services\OrderService;
use YiZan\Services\RegionService;
use Lang, Validator,Log;

class UserController extends BaseController {
    /**
     * 手机号码验证
     */
    public function mobileverify() {
        $result = UserService::sendVerifyCode($this->request('mobile'), $this->request('type'));
        return $this->output($result);
    }
    /**
     * 订单
     */
    public function endorder() {
        $result =  OrderService::endOrder(1);
        die($result);
    }

    /**
     * 会员登录
     */
    public function login()
    {
        if($this->request('verifyCode') == true)
        {
            return $this->verifylogin();
        }

        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> Lang::get('api.success.user_login')
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

        $mobile = $this->request('mobile');
        $pwd 	= strval($this->request('pwd'));

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

        $user = UserService::getByMobile($mobile);

        //未找到会员时
        if (!$user)
        {
            $result['code'] = 10108;
            return $this->output($result);
        }

        $pwd = md5(md5($pwd) . $user->crypt);

        //登录密码错误
        if ($user->pwd != $pwd) {
            $result['code'] = 10109;
            return $this->output($result);
        }
        //状态锁定
        if ($user->status != 1) {
            $result['code'] = 10122;
            return $this->output($result);
        }
        //更新登陆时间IP信息
        UserService::updateIp($user->id);
        $this->createToken($user->id, $user->pwd);
        $propertyUser = PropertyUserService::getByUserId($user->id);
        $user =
            [
                "id"=>$user->id,
                "mobile"=>$user->mobile,
                "name"=>$user->name,
                "avatar"=>$user->avatar,
                "isDelUser"=>UserService::isUpdateM($user->id),
                "address"=>UserAddressService::getAddressList($user->id),
                "defaultMobile"=>UserMobileService::getMobile($user->id),
                "propertyUser"=>$propertyUser,
                "balance"=>$user->balance,
                "totalMoney"=>$user->total_money,
                "openid"=>$user->openid,
                "unionid"=>$user->unionid,
                "fanweId"=>$user->fanwe_id,
                "invitationId"=>$user->invitation_id
            ];

        $result['data'] = $user;
        $result['token'] = $this->token;
        $result['userId'] = $user['id'];


        return $this->output($result);
    }
    /**
     * 会员登录
     */
    public function verifylogin()
    {
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> Lang::get('api.success.user_login')
        );

        $rules = array(
            'mobile' => ['required','regex:/^1[0-9]{10}$/']
        );

        $messages = array(
            'mobile.required'	=> '10101',
            'mobile.regex'		=> '10102'
        );

        $mobile = $this->request('mobile');

        $verifyCode = $this->request('verifyCode');

        $validator = Validator::make([
            'mobile' => $mobile
        ], $rules, $messages);

        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $this->output($result);
        }

        if(UserService::checkVerifyCode($verifyCode, $mobile) == false)
        {
            $result['code'] = 10104; // 验证码不正确

            return $this->output($result);
        }

        $user = UserService::getByMobile($mobile);

        //未找到会员时
        if (!$user)
        {
            $result['code'] = 10108;
            return $this->output($result);
        }
        //状态锁定
        if ($user->status != 1) {
            $result['code'] = 10122;
            return $this->output($result);
        }
        UserService::updateIp($user->id);
        $this->createToken($user->id, $user->pwd);
        $propertyUser = PropertyUserService::getByUserId($user->id);

        $user =
            [
                "id"=>$user->id,
                "mobile"=>$user->mobile,
                "name"=>$user->name,
                "avatar"=>$user->avatar,
                "isDelUser"=>UserService::isUpdateM($user->id),
                "address"=>UserAddressService::getAddressList($user->id),
                "defaultMobile"=>UserMobileService::getMobile($user->id),
                "propertyUser"=>$propertyUser,
                "balance"=>$user->balance,
                "totalMoney"=>$user->total_money,
                "openid"=>$user->openid,
                "unionid"=>$user->unionid,
                "fanweId"=>$user->fanwe_id,
                "invitationId"=>$user->invitation_id
            ];

        $result['data'] = $user;
        $result['token'] = $this->token;
        $result['userId'] = $user['id'];
        return $this->output($result);
    }

    /**
     * 会员退出
     */
    public function logout() {
        return $this->outputCode(0, Lang::get('api.success.user_logout'));
    }

    /**
     * 会员注册
     */
    public function reg() {
        $result = UserService::createUser(
            $this->request('mobile'),
            $this->request('verifyCode'),
            $this->request('pwd'),
            $this->request('type', 'reg'),
            $this->request('invitationType'),
            $this->request('invitationId'),
            $this->request('fanweId'),
            $this->request('recommendUser')
        );
        if ($result['code'] == 0) {
            $user = $result['data'];
            $this->createToken($user->id, $user->pwd);
            $user = $user->toArray();
            $user['address'] = [];//UserAddressService::getAddress($user['id']);
            $result['data'] = $user;
            $result['token'] = $this->token;
            $result['userId'] = $user['id'];
            return $this->output($result);
        }
        return $this->output($result);
    }

    /**
     * 修改密码
     */
    public function repwd() {
        $result = UserService::createUser(
            $this->request('mobile'),
            $this->request('verifyCode'),
            $this->request('pwd'),
            $this->request('type', 'repwd')
        );
        if ($result['code'] == 0) {
            $user = $result['data'];
            $this->createToken($user->id, $user->pwd);
            $user = $user->toArray();
            $user['address'] = UserAddressService::getAddress($user['id']);
            $result['data'] = $user;
            $result['token'] = $this->token;
            $result['userId'] = $user['id'];
            return $this->output($result);
        }
        return $this->output($result);
    }

    /**
     * 修改密码(新)
     */
    public function renewpwd() {
        $result = UserService::repwd(
            $this->userId,
            $this->request('oldPwd'),
            $this->request('pwd')
        );
        if ($result['code'] == 0) {
            $user = $result['data'];
            $this->createToken($user->id, $user->pwd);
            $user = $user->toArray();
            $user['address'] = UserAddressService::getAddress($user['id']);
            $result['data'] = $user;
            $result['token'] = $this->token;
            $result['userId'] = $user['id'];
            return $this->output($result);
        }
        return $this->output($result);
    }

    /**
     * 更新手机号
     */
    public function updatemobile() {

        $result = UserService::updateMobile(
            $this->userId,
            $this->request('oldMobile'),
            $this->request('mobile'),
            $this->request('verifyCode')
        );
        return $this->output($result);
    }


    /**
     * 卖家状态
     */
    public function msgStatus()
    {
        $reslut = ReadMessageService::hasNewMessage($this->userId);

        $info = array(
            'code' 	=> 0,
            'data' 	=> ["hasNewMessage"=>$reslut],
            'msg'	=> ""
        );

        return $this->output($info);
    }


    /*
    * 小区身份认证
    */
    public function villagesauth(){
        $result = PropertyUserService::auth(
            $this->userId,
            $this->request('villagesid'),
            $this->request('buildingid'),
            $this->request('roomid'),
            $this->request('username'),
            $this->request('usertel'),
            $this->request('type')
        );
        return $this->output($result);
    }


    /**
     * 获取开门钥匙信息
     */
    public function getdoorkeys(){

        $user = $this->user->toArray();
        //单个门钥匙
        $doorId = (int)$this->request('doorId');
        if($doorId>0){
            $result = PuserDoorService::getUserDoorsKey($user['id']);//获取门禁钥匙
            foreach($result as $door){
                if($door['doorid'] == $doorId){
                    $result = $door;
                    break;
                }
            }
        }else{
            $districtId = (int)$this->request('villagesid');
            if($districtId == 0){
                $result = PuserDoorService::getUserDoorsAll($user['id']);//获取全部可用门禁钥匙
            } else {
                foreach ($user['propertyUser'] as $userInfo) {
                    if($userInfo['districtId'] == $districtId){
                        $propertyUser = $userInfo;
                        break;
                    }
                }
                $result = PuserDoorService::getUserDoors(
                    $propertyUser['id']//获取小区门禁列表
                );

            }
        }

        return $this->outputData($result);
    }

    /**
     * 修改门禁信息
     */
    public function editdoorinfo(){
        $result = PuserDoorService::updateUserDoor(
            $this->user['propertyUser']->id ,
            $this->request('doorid'),
            $this->request('doorname')
        );
        return $this->outputData($result);
    }

    /**
     * 小区身份认证检查
     */
    public function checkvillagesauth(){
        $result = PropertyUserService::getByUserId(
            $this->userId
        );
        if(empty($result)){
            $msg = '您还未申请身份认证';
        } else {
            if($result['status'] == 0){
                $msg = '身份认证审核中';
            } else if($result['status'] == 1){
                $msg = '身份认证成功';
            } else {
                $msg = '身份认证失败，请完善信息后再试';
            }
        }
        return $this->outputData($result, $msg);
    }

    /**
     * 小区门禁申请
     */
    public function applyaccess(){
        $result = PropertyUserService::applyDoorAccess(
            $this->userId,
            $this->request('districtId')
        );
        return $this->output($result);
    }

    /**
     * 记录开门日志
     */
    public function opendoor(){
        $result = DoorOpenLogService::doorOpenRecord(
            $this->user->propertyUser,
            $this->request('errorCode'),
            $this->request('districtId'),
            $this->request('doorId'),
            $this->request('buildId'),
            $this->request('roomId')
        );
        return $this->output($result);
    }

    public function getbalance(){
        $result['paylogs'] = UserService::getBalance(
            $this->userId,
            (int)$this->request('type'),
            max((int)$this->request('page'), 1)
        );
        return $this->outputData($result);
    }

    public function balance(){
        $result = UserService::balance($this->userId);
        return $this->outputData($result);
    }

    public function userinfo(){
        $result = UserService::getInfo($this->userId);
        return $this->outputData($result);
    }

    public function charge(){
        $result = UserService::payCharge(
            $this->userId,
            $this->request('payment'),
            $this->request('extend'),
            $this->request('money'),
            $this->request('isFx')
        );
        return $this->output($result);
    }


    /**
     * 检测用户是否注册
     */
    public function checkuser(){
        $result2 = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );

        $result = UserService::checkUser(
            $this->request('mobile'),
            $this->request('nickname'),
            $this->request('avatar'),
            $this->request('openId')
        );

        //if ($result['code'] == 0) {
            $user = $result;
            $this->createToken($user['id'], $user['pwd']);
            $user['address'] = [];//UserAddressService::getAddress($user['id']);
            $result2['data'] = $user;
            $result2['token'] = $this->token;
            $result2['userId'] = $user['id'];
            return $this->output($result2);
        //}
    }

    public function regpush(){
        $result = PushMessageService::regPush(
            $this->request('id'),
            $this->request('devive'),
            $this->request('apns')
        );
        return $this->outputData($result);
    }


    /**
     * 修改支付密码
     */
    public function repaypwd(){
        $result = UserService::rePayPwd(
            $this->userId,
            trim($this->request('oldPwd')),
            trim($this->request('newPwd')),
            trim($this->request('verifyCode')),
            (int)$this->request('type')
        );
        return $this->output($result);
    }

    /**
     *会员积分列表
     */
    public function integral(){
        $result = UserIntegralService::getUserList(
            $this->userId,
            (int)$this->request('type'),
            max((int)$this->request('page'), 1),
            max((int)$this->request('pageSize'), 20)
        );
        return $this->outputData($result);
    }

    /**
     * 验证支付密码
     */
    public function checkpaypwd() {
        $result = UserService::checkPayPwd($this->userId, trim($this->request('password')));
        return $this->output($result);
    }

    /**
     * 签到送积分
     */
    public function signin() {
        $result = UserIntegralService::createIntegralLog($this->userId,  '1', '1', '0');
        return $this->output($result);
    }


    public function getbyopenid(){
        $result2 = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );

        $result = UserService::checkUserOpenId(
            $this->request('unionid'),
            $this->request('openid')
        );

        $user = $result;
        $this->createToken($user['id'], $user['pwd']);
        $user['address'] = [];//UserAddressService::getAddress($user['id']);
        $result2['data'] = $user;
        $result2['token'] = $this->token;
        $result2['userId'] = $user['id'];
        return $this->output($result2);
    }
    public function regweixin(){
        $result = UserService::createUserWeixin(
            $this->request('mobile'),
            $this->request('verifyCode'),
            $this->request('pwd'),
            $this->request('type', 'reg'),
            $this->request('openId'),
            $this->request('unionid'),
            $this->request('name'),
            $this->request('avatar'),
            $this->request('invitationType'),
            $this->request('invitationId')
        );
        if ($result['code'] == 0) {
            $user = $result['data'];
            $this->createToken($user->id, $user->pwd);
            $user = $user->toArray();
            $user['address'] = [];//UserAddressService::getAddress($user['id']);
            $result['data'] = $user;
            $result['token'] = $this->token;
            $result['userId'] = $user['id'];
            return $this->output($result);
        }
        return $this->output($result);
    }


    public function weixinbind(){
        $result = UserService::bindWeixin(
            $this->userId,
            $this->request('openid'),
            $this->request('unionid')
        );
        return $this->output($result);
    }

    /**
     * 绑定手机(新)
     */
    public function bindmobile() {
        $result = UserService::bindmobile(
            $this->userId,
            $this->request('unionid'),
            $this->request('openid'),
            $this->request('mobile'),
            $this->request('verifyCode'),
            $this->request('pwd')
        );
        if ($result['code'] == 0) {
            $user = $result['data'];
            $this->createToken($user->id, $user->pwd);
            $user = $user->toArray();
            $user['address'] = UserAddressService::getAddress($user['id']);
            $result['data'] = $user;
            $result['token'] = $this->token;
            $result['userId'] = $user['id'];
            return $this->output($result);
        }
        return $this->output($result);
    }

    /**
     * 绑定手机 强行换
     */
    public function moveunionid(){
        $result = UserService::moveunionid(
            $this->userId,
            $this->request('unionid'),
            $this->request('openid'),
            $this->request('mobile'),
            $this->request('pwd')
        );
        if ($result['code'] == 0) {
            $user = $result['data'];
            $this->createToken($user->id, $user->pwd);
            $user = $user->toArray();
            $user['address'] = UserAddressService::getAddress($user['id']);
            $result['data'] = $user;
            $result['token'] = $this->token;
            $result['userId'] = $user['id'];
            return $this->output($result);
        }
        return $this->output($result);
    }

    public function fanwe_id(){
        $result = UserService::fanweId($this->request('userId'));
        return $this->outputData($result);
    }

    public function userbyfanweid(){
        $result = UserService::getUserByfanweId($this->request('fanweId'));
        return $this->outputData($result);
    }

    public function changeuserinfo(){
        $result = UserService::changeFanwe(
            $this->request('userId'),
            $this->request('fanweId')
        );
        return $this->outputData($result);
    }

    public function loginsaas(){
        $user = UserService::getUserByfanweId($this->request('fanweId'));
        if ($user) { //登录
            $user =
                [
                    "id"=>$user->id,
                    "mobile"=>$user->mobile,
                    "name"=>$user->name,
                    "avatar"=>$user->avatar,
                    "isDelUser"=>UserService::isUpdateM($user->id),
                    "address"=>UserAddressService::getAddressList($user->id),
                    "defaultMobile"=>UserMobileService::getMobile($user->id),
                    "balance"=>$user->balance,
                    "totalMoney"=>$user->total_money,
                    "openid"=>$user->openid,
                    "unionid"=>$user->unionid,
                    "fanweId"=>$user->fanwe_id,
                    "invitationId"=>$user->invitation_id
                ];
            $result['data'] = $user;
            $result['token'] = $this->token;
        }
    }

    public function updatebalance() {
        $result = UserService::updatebalance(
            (int)$this->request('userId'),
            $this->request('money'),
            (int)$this->request('type'),
            $this->request('remark')
        );
        return $this->output($result);
    }

    public function showData(){
        $result = UserService::isShowData(
            $this->userId
        );
        return $this->outputData($result);
    }

    /**
     * 创建分销商
     */
    public function regsharechapman() {
        $result = UserService::createSharechapman(
            $this->userId,
            $this->request('remark')
        );
        return $this->output($result);
    }

    /**
     * 获取分销商
     */
    public function getsharechapman() {
        $result = UserService::getSharechapman(
            $this->userId
        );
        return $this->output($result);
    }

    /**
     * 获取首单活动
     */
    public function firstOrder() {
        $result = UserService::getFirstOrder(
            $this->userId,
            $this->request('sellerId')
        );
        return $this->outputData($result);
    }

}