<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\Sellerweb\UserService;
use YiZan\Services\UserAddressService;
use YiZan\Services\SellerCateService;
use YiZan\Services\Sellerweb\SellerService;
use YiZan\Services\System\ScheduleService;
use YiZan\Models\UserVerifyCode;
use Lang, Validator,Session;

class UserController extends BaseController {
    /**
     * 注册、修改密码/找回密码、更改手机号发送验证码
     */
    public function mobileverify() 
    {
        $seller = SellerService::getByMobile($this->request('mobile')); 
        //注册
        if($this->request('type') == UserVerifyCode::TYPE_REG){
            if($seller){
                return $this->outputCode(10118);
            }
            $result = UserService::sendVerifyCode($this->request('mobile'), UserVerifyCode::TYPE_REG);
        } else {
            //找回密码
            if(!$seller){
                return $this->outputCode(10120);
            }
            $result = UserService::sendVerifyCode($this->request('mobile'), UserVerifyCode::TYPE_REPWD);
        }
        return $this->output($result);
    }

    /**
     * 会员登陆
     */
    public function login() 
    {   
        $mobile = trim($this->request('mobile'));
        $pwd    = strval($this->request('pwd'));
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.user_login')
        );
        
        
        $seller_user = UserService::getByName($mobile);
        if(!empty($seller_user->id)){
            $pwd = md5(md5($pwd) . $seller_user->crypt);
          
            //登录密码错误
            if ($seller_user->pwd != $pwd) {
                $result['code'] = 10123;
                return $this->output($result);
            }
            
            //账号锁定
            if($seller_user->status == 0)
            {
                $result['code'] = 10123;
                return $this->output($result);
            }
            
            $seller_user->login_time   = UTC_TIME;
            $seller_user->login_ip     = CLIENT_IP;
            $seller_user->login_count++;
            $seller_user->save();

            $name_auth = SellerService::getByName($seller_user->name);
            $sauir = !empty($name_auth) ? $name_auth->toArray() : '';
            $user = SellerService::getById((int)$seller_user->relation_id, 0);
            $this->createToken($user->id, $user->pwd);
            $user = $user->toArray();
            
            $result['data'] =  $user;
            $result['token'] = $this->token;
            $result['sauir'] = $sauir;
                        
            return $this->output($result);
        }
  //---------------------------------------------------------------------------------------------------      
        
        $rules = array(
            'mobile' => ['required','regex:/^1[0-9]{10}$/'],
            'pwd'    => ['required','min:6','max:20']
        );

        $messages = array(
            'mobile.required'   => '10101',
            'mobile.regex'      => '10102',
            'pwd.required'      => '10105',
            'pwd.min'           => '10106',
            'pwd.max'           => '10106',
        );

        

        $validator = Validator::make([
                'mobile' => $mobile,
                'pwd'    => $pwd
            ], $rules, $messages);
        
        //验证信息
        if ($validator->fails())
        {
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $this->output($result);
        }
//----------------------------------------------------------------------------------------------------------

        $user = UserService::getByMobile($mobile);

        //未找到会员时
        if (!$user) 
        {
            $result['code'] = 10123;
            return $this->output($result);
        }

        $pwd = md5(md5($pwd) . $user->crypt);
    
        //登陆密码错误
        if ($user->pwd != $pwd) {
            $result['code'] = 10124;
            return $this->output($result);
        }
 //-----------------------------------------------------------------------------------------------------------       
        $this->createToken($user->id, $user->pwd);
        
        $seller = SellerService::getById(0, $user->id);
        
        //找不到卖家信息,则返回
        if ($seller == false || $seller->is_del == true) 
        {
            $result['code'] = 10131;
            
            return $this->output($result);
        }
        if ( $seller->is_check < 1) {
            $result['code'] = 10132;
            
            return $this->output($result);
        }
        
        if($seller->status == 0){
            $result['code'] = 10133;
            return $this->output($result);
        }

        $seller = $seller->toArray();
        $user = $user->toArray();
        
        $seller['loginTime'] = $user['loginTime'] ? $user['loginTime'] : UTC_TIME;

        //更新本次登录信息
        UserService::updateLoginInfo($user['id']);

        $user['address'] = UserAddressService::getAddress($user['id']); 
        $result['data'] = $seller;
        $result['token'] = $this->token; 
        
        return $this->output($result);
    }
    /**
     * 会员登陆
     */
    public function verifylogin() 
    {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.user_login')
        );

        $rules = array(
            'mobile' => ['required','regex:/^1[0-9]{10}$/']
        );

        $messages = array(
            'mobile.required'   => '10101',
            'mobile.regex'      => '10102'
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

        // $verifyCode != "123456" 验证码默认
        if($verifyCode != "123456" &&
            UserService::checkVerifyCode($verifyCode, $mobile) == false)
        {
            $result['code'] = 10104; // 验证码不正确
            
            return $this->output($result);
        }
        
        $user = UserService::getByMobile($mobile);
        
        //未找到会员时
        if (!$user) 
        {
            $result['code'] = 10123;
            return $this->output($result);
        }

        $this->createToken($user->id, $user->pwd);
        
        $seller = SellerService::getById(0, $user->id);
        
        //找不到卖家信息,则返回
        if ($seller == false || $seller->is_del == true) 
        {
            $result['code'] = 10131;
            
            return $this->output($result);
        }
        
        // 卖家没有通过审核
        if($seller->is_authenticate == false)
        {
            $result['code'] = 10132;
            
            return $this->output($result);
        }

        $seller = $seller->toArray();
        
        $user = $user->toArray();
        $seller['loginTime'] = $user['loginTime'];
        $user['address'] = UserAddressService::getAddress($user['id']);
        $result['data'] = $seller;
        $result['token'] = $this->token;
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
    public function reg() 
    {
        $result = SellerService::createSeller(
                intval($this->request('sellerType', \YiZan\Models\Seller::SELF_ORGANIZATION)),
                $this->request('mobile'), 
                $this->request('verifyCode'),
                $this->request('pwd'),
                $this->request('name'),
                $this->request('avatar'),
                $this->request('serviceTel'),
                (int)$this->request('districtId'),
                $this->request('provinceId'),
                $this->request('cityId'),
                $this->request('areaId'),
                $this->request('idcardSn'),
                $this->request('idcardPositiveImg'),
                $this->request('idcardNegativeImg'),
                $this->request('certificateImg'),
                //$this->request('businessLicenceSn'),
                $this->request('businessLicenceImg'),
                $this->request('address'),
                $this->request('mapPos'),
                $this->request('mapPoint'),
                $this->request('cateIds'),
                $this->request('contacts'),
                $this->request('type', 'reg'),
                $this->request('storeType'),
                $this->request('refundAddress')
            );
        if ($result['code'] == 0) {
            $seller = $result['data'];
            $this->createToken($seller->id, $seller->pwd);
            $seller = $seller->toArray();
            $result['data'] = ['seller' => $seller];
            $result['token'] = $this->token;
            $result['sellerId'] = $seller['id'];
            return $this->output($result);
        }
        return $this->output($result);
    }

    /**
     * 更新会员
     */
    public function update() {
        $result = SellerService::updateSeller(
                (int)$this->request('sellerId'),
                $this->request('logo'),
                $this->request('image'),
                $this->request('name'),
                $this->request('address'),
                $this->request('mapPoint'),
                $this->request('mapPos'),
                intval($this->request('provinceId')), 
                intval($this->request('cityId')), 
                intval($this->request('areaId')), 
                $this->request('contacts'),
                $this->request('cateIds'), 
                intval($this->request('status')),
                (float)$this->request('deliveryFee'),
                intval($this->request('isAvoidFee')),
                (float)$this->request('avoidFee'),
                (float)$this->request('serviceFee'),
                $this->request('deliveryTime'),
                (int)$this->request('deduct'),
                (int)$this->request('isCashOnDelivery'),
                $this->request('serviceTel'),
                $this->request('sendWay'),
                $this->request('serviceWay'),
                $this->request('reserveDays'),
                $this->request('sendLoop'),
                $this->request('refundAddress'),
                $this->request('sendType')
            );
        return $this->output($result);
    }

    /**
     * 资质认证
     */
    public function certificate() {
        $result = SellerService::saveCert(
            (int)$this->request('sellerId'),
            $this->request('authenticate')
        );
        return $this->output($result);
    }

    /**
     * 获取服务人员
     */
    public function get()
    {
        $seller = SellerService::getById(intval($this->request('sellerId')));
        
        return $this->outputData($seller == false ? [] : $seller->toArray());
    }

    /**
     *  获取日程列表
     */
    public function lists() {
        $list = SellerService::getDayList(
            (int)$this->request('sellerId'),
            $this->request('date')
        );

        return $this->outputData($list);
    }


    /**
     * [updatesch 更新预约时间]
     */
    public function updatesch() {
        $result = SellerService::updateStatus(
            $this->request('hours'),
            $this->request('status'),
            (int)$this->request('sellerId')
        );
        return $this->output($result);
    }

    /**
     *  其他设置
     */
    public function moreset() {
        $result = SellerService::extendSet(
                    (int)$this->request('sellerId'),
                    $this->request('brief'),
                    (int)$this->request('sort'),
                    (int)$this->request('status')
                );

        return $this->output($result);
    }

    /**
     *  更改手机号
     */
    public function changetel() {
        $result = SellerService::updateMobile(
            (int)$this->request('sellerId'),
            $this->request('mobile'),
            $this->request('pwd'),
            $this->request('newMobile'),
            $this->request('verifyCode')
        );
        return $this->output($result);
    }

     /**
     *  找回密码/修改密码
     */
    public function changepwd() {
        $result = SellerService::updatePass(
            (int)$this->request('sellerId'),
            $this->request('mobile'),
            $this->request('idcardSn'),
            $this->request('pwd'),
            $this->request('pwdold'),
            $this->request('verifyCode'),
            $this->request('type')
        );

        return $this->output($result);
    }

    /**
     * 搜索员工
     */
    public function search(){
        $result = SellerService::searchStaff($this->sellerId,$this->request('name'));
        return $this->outputData($result);
    }

    /**
     * 验证手机、验证码
     */
    public function checktelcode(){
        $result = SellerService::checkTelcode(
            $this->request('mobile'),
            $this->request('verifyCode')
        );
        return $this->output($result);
    }

    public function all() {
        $list = SellerCateService::getAll();
        return $this->outputData($list);
    }

    public function updatebasic() {
        $result = SellerService::updateBasic(
                $this->sellerId,
                $this->request('businessLicenceImg')
            );
        return $this->output($result);
    }
}