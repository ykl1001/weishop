<?php
namespace YiZan\Services\Sellerweb;

use YiZan\Models\User;
use YiZan\Models\District;
use YiZan\Models\SellerAdminUser;
use YiZan\Models\SellerMap;
use YiZan\Models\UserVerifyCode;
use YiZan\Models\Sellerweb\Seller;
use YiZan\Models\Sellerweb\SellerExtend;
use YiZan\Models\Sellerweb\SellerStaffExtend;
use YiZan\Models\SellerAppoint;
use YiZan\Models\SellerStaff;
use YiZan\Models\SystemConfig;
use YiZan\Models\SellerCateRelated;
use YiZan\Models\Sellerweb\SellerAppointHour;
use YiZan\Models\Sellerweb\SellerAuthenticate;
use YiZan\Models\Sellerweb\SellerCertificate;
use YiZan\Models\Sellerweb\SellerCreditRank;
use YiZan\Models\SellerComplain;
use YiZan\Models\Sellerweb\SellerMoneyLog;
use YiZan\Models\Sellerweb\SellerWithdrawMoney;
use YiZan\Models\ReadMessage;
use YiZan\Models\Sellerweb\Promotion;
use YiZan\Models\Sellerweb\PromotionSn;
use YiZan\Models\Sellerweb\OrderRate;
use YiZan\Models\Sellerweb\Goods;
use YiZan\Models\Sellerweb\GoodsExtend;
use YiZan\Models\SellerDeliveryTime;
use YiZan\Models\PropertyUser;
use YiZan\Models\Region;

use YiZan\Models\FreightTmp;
use YiZan\Models\FreightTmpCity;

use YiZan\Services\SmsService;
use YiZan\Utils\Http;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Utils\String;
use Request, DB, Lang, Validator ,Exception;

class SellerService extends \YiZan\Services\SellerService
{
    /**
     * 服务人员状态锁定
     */
    const SEARCH_STATUS_NO = 1;
    /**
     * 服务人员状态正常
     */
    const SEARCH_STATUS_OK = 2;
    /**
     * 服务人员接单状态(不接单)
     */
    const SEARCH_BUSINESS_STATUS_NO = 1;
    /**
     * 服务人员接单状态正常
     */
    const SEARCH_BUSINESS_STATUS_OK = 2;

    /**
     * 根据手机号码获取会员
     * @param  string $mobile 手机号码
     * @return array          会员信息
     */
    public static function getByMobile($mobile) {
        return Seller::where('mobile', $mobile)->first();
    }
    /**
     * 根据登录名称获取管理员
     * @param  string $name 登录名称
     * @return array          管理员信息
     */
    public static function getByName($name)
    {
        return SellerAdminUser::where('name', $name)
        ->with('role.access')
        ->first();
    }
    /**
     * 获取卖家
     * @param int $sellerId 卖家id
     * @return object 卖家信息
     */
    public static function getById($sellerId = 0, $userId = 0)
    {
        if($sellerId > 0) {
            return Seller::with('province', 'city', 'area','authenticate', 'banks', 'sellerCate.cates', 'deliveryTimes', 'extend', 'district')->find($sellerId);
        }

        if($userId > 0) {
            return Seller::where("user_id", $userId)->with('province', 'city', 'area','authenticate', 'banks', 'sellerCate.cates', 'deliveryTimes', 'extend', 'district')->first();
        }
        return null;

    }

    /**
     * 创建seller
     * @param  int $sellerType 机构类型
     * @param  [type] $mobile     [description]
     * @param  [type] $verifyCode [description]
     * @param  [type] $pwd        [description]
     * @param  [type] $name     [description]
     * @param  [int] $sex     [description]
     * @param  [type] $avatar     [description]
     * @param  [int] $birthday     [description]
     * @param  [int] $provinceId     [description]
     * @param  [int] $cityId     [description]
     * @param  [int] $areaId     [description]
     * @param  [type] $idcardSn     [description]
     * @param  [type] $idcardPositiveImg     [description]
     * @param  [type] $idcardNegativeImg     [description]
     * @param  [type] $companyName           [description]
     * @param  [type] $businessLicenceSn     [description]
     * @param  [type] $businessLicenceImg     [description]
     * @param string $address 地址
     * @param string $mapPoint 纬度,经度(QQ地图坐标)
     * @param array $mapPos QQ地图坐标数组
     * @param  string $type       [description]
     * @return [type]             [description]
     */
    public static function createSeller($sellerType, $mobile, $verifyCode, $pwd, $name, $avatar, $serviceTel, $districtId, $provinceId, $cityId, $areaId, $idcardSn, $idcardPositiveImg, $idcardNegativeImg, $certificateImg, $businessLicenceImg, $address, $mapPos, $mapPoint, $cateIds, $contacts, $type = 'reg',$storeType, $refundAddress) {

        $pwd = strval($pwd);
        $mapPoint = $sellerType == 3 ? '26.074508,119.296494' : $mapPoint;
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.create_user_'.$type)
        );

        $rules = array(
            'mobile'                        => ['required','regex:/^1[0-9]{10}$/'],
            'code'                          => ['required','size:6'],
            'pwd'                           => ['required','min:6','max:20'],
            'name'                          => ['required','min:2','max:30'],
            // 'birthday'                      => ['required'],
            // 'avatar'                        => ['required'],
            'provinceId'                    => ['min:1'],
            'cityId'                        => ['min:1'],
            'areaId'                        => ['min:1'],
            'idcardSn'                      => ['required','regex:/^[0-9]{18}|[0-9]{15}|[0-9]{17}[xX]{1}$/'],
            'idcardPositiveImg'             => ['required'],
            'idcardNegativeImg'             => ['required'],
            // 'address'                       => ['required'],
            'mapPoint'                      => ['required'],
            'mapPos'                        => ['required'],
        );

        $messages = array(
            'mobile.required'               => '10101',
            'mobile.regex'                  => '10102',
            'code.required'                 => '10103',
            'code.size'                     => '10104',
            'pwd.required'                  => '10105',
            'pwd.min'                       => '10106',
            'pwd.max'                       => '10106',
            'name.required'                 => '10107',
            'name.min'                      => '10108',
            'name.max'                      => '10108',
            // 'birthday.required'             => '10109',
            // 'avatar.required'               => '10110',
            'provinceId.min'                => '10111',
            'cityId.min'                    => '10112',
            'areaId.min'                    => '10113',
            'idcardSn.required'             => '10114',
            'idcardSn.regex'                => '10115',
            'idcardPositiveImg.required'    => '10116',
            'idcardNegativeImg.required'    => '10117',
            //'address.required'              => '30613',   // 请输入地址
            'mapPoint.required'             => '30614',   // 请选择地图定位
            'mapPos.required'               => '30616',    // 请选择服务范围

        );

        $validator = Validator::make([
            'mobile'                => $mobile,
            'code'                  => $verifyCode,
            'pwd'                   => $pwd,
            'name'                  => $name,
            'provinceId'            => $provinceId,
            'cityId'                => $cityId,
            'areaId'                => $areaId,
            //  'birthday'              => $birthday,
            //  'avatar'                => $avatar,
            'idcardSn'              => $idcardSn,
            'idcardPositiveImg'     => $idcardPositiveImg,
            'idcardNegativeImg'     => $idcardNegativeImg,
            // 'address'               => $address,
            'mapPoint'              => $mapPoint,
            'mapPos'                => $mapPos,
        ], $rules, $messages);
        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        //检测验证码
        $verifyCodeId = UserService::checkVerifyCode($verifyCode, $mobile, UserVerifyCode::TYPE_REG);
        if (!$verifyCodeId) {
            $result['code'] = 10104;
            return $result;
        }
        $mapPoint = Helper::foramtMapPoint($mapPoint);
        if (!$mapPoint){
            $result['code'] = 30615;    // 地图定位错误
            return $result;
        }
        if($storeType == 0){
            $mapPos = Helper::foramtMapPos($mapPos);
            if (!$mapPos) {
                $result['code'] = 30617;    // 服务范围错误
                return $result;
            }
        }else{
            $allmapPos  = $mapPos;
            $mapPos = null;
            $mapPos['str']  = $allmapPos;
            //全国店无范围,默认范围值 无效的 只为填充数据
            $mapPos['pos'] = "31.90797991052 102.20781720873,31.913517218413 102.2418346142,31.894941463557 102.25701865688,31.887726973701 102.23846511045,31.887216933335 102.21802328888,31.90797991052 102.20781720873";

        }

        $seller = self::getByMobile($mobile);
        if ($seller) {
            $result['code'] = 10118;
            return $result;
        }
        DB::beginTransaction();
        try {
            $user = UserService::getByMobile($mobile);
            if (!$user) {
                $user = new User;
                $user->mobile           = $mobile;
                $user->name             = $name;
                $user->name_match       = String::strToUnicode($name, '+');
                $user->reg_time         = UTC_TIME;
                $user->reg_ip           = CLIENT_IP;
                $user->province_id      = (int)$provinceId;
                $user->city_id          = (int)$cityId;
                $user->area_id          = (int)$areaId;
                // $user->sex              = (int)$sex;
                // $user->birthday         = $birthday;
                $user->is_sms_verify    = 1;
                $user->save();
            } else {
                //会员存在且在员工表也同时存在
                $staff_check = (int)SellerStaff::where('user_id', $user->id)->pluck('id');
                if ($staff_check > 0) {
                    $result['code'] = 10118;    // 手机号码已被注册
                    return $result;
                }
            }

            if (!empty($avatar)) {
                $user_avatar = self::moveUserImage($user->id, $avatar);
                if (!$user_avatar) {//转移图片失败
                    $result['code'] = 10201;
                    return $result;
                }
            }

            $crypt        = String::randString(6);
            $pwd          = md5(md5($pwd) . $crypt);
            $user->crypt  = $crypt;
            $user->pwd    = $pwd;
            $user->avatar = $avatar;
            $user->save();

            if( (int)$sellerType <= 0 ){
                 $sellerType = 2;
            }
            $seller = new Seller;
            $seller->type             = $sellerType;
            $seller->user_id          = $user->id;
            $seller->mobile           = $mobile;
            $seller->name             = $name;
            $seller->name_match       = String::strToUnicode($name.$mobile);
            $seller->address          = $address;
            $seller->map_point_str    = $mapPoint;
            $seller->map_pos_str      = $mapPos["str"];
            $seller->map_point        = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");
            $seller->map_pos          = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
            $seller->create_time      = UTC_TIME;
            $seller->create_day       = UTC_DAY;
            $seller->province_id      = (int)$provinceId;
            $seller->city_id          = (int)$cityId;
            $seller->area_id          = (int)$areaId;
            $seller->contacts         = $contacts;
            $seller->service_tel      = $serviceTel;
            $seller->store_type   = $storeType;
            $seller->refund_address = $refundAddress;

            $seller->save();

            if (!empty($avatar)) {
                $logo = self::moveSellerImage($seller->id, $avatar);
                if (!$logo) {//转移图片失败
                    $result['code'] = 10202;
                    return $result;
                }
            }

            $seller->logo = $logo;
            $seller->save();

            //var_dump($seller);
            //创建商家扩展信息表
            $sellerExtend = new SellerExtend();
            $sellerExtend->seller_id = $seller->id;
            $sellerExtend->save();

            if ($sellerType == Seller::PROPERTY_ORGANIZATION) { // 是物业公司关联小区
                District::where('id', $districtId)->update(['seller_id'=>$seller->id]);
                //更新所有在小区关联物业公司之前的业主
                PropertyUser::where('district_id', $districtId)->update(['seller_id'=>$seller->id]);
            }

            $seller_idcard = SellerAuthenticate::where('idcard_sn', $idcardSn)->first();
            if($seller_idcard){
                $result['code'] = 30621;    //身份证号码已存在
                DB::rollback();
                return $result;
            }

            $idcardPositiveImg = self::moveSellerImage($seller->id, $idcardPositiveImg);
            if (!$idcardPositiveImg) {//转移图片失败
                $result['code'] = 10203;
                return $result;
            }

            $idcardNegativeImg = self::moveSellerImage($seller->id, $idcardNegativeImg);
            if (!$idcardNegativeImg) {//转移图片失败
                $result['code'] = 10204;
                return $result;
            }

            if($seller->type == Seller::SERVICE_ORGANIZATION)
            {

                if($businessLicenceImg == false)
                {
                    $result['code'] = 10207; // 公司营业执照相片不能为空
                    return $result;
                }

                $seller_auth = SellerAuthenticate::where('seller_id',$seller->id)->select('business_licence_img','certificate_img')->get();
                //如果不相同才有水印
                if($seller_auth->business_licence_img != $businessLicenceImg){
                    $watermark = SystemConfig::getConfig('watermark_logo');
                    if(!empty($watermark)){
                        //水印图片
                        $businessLicenceImg = \YiZan\Utils\Image::watermark($businessLicenceImg);
                    }else{
                        $businessLicenceImg = self::moveSellerImage($seller->id, $businessLicenceImg);

                        if (!$businessLicenceImg) {//转移图片失败
                            $result['code'] = 10204;
                            return $result;
                        }
                    }
                }


            }
            if ($seller->type == Seller::SELF_ORGANIZATION) {
                if($certificateImg == false)
                {
                    $result['code'] = 10207; // 资质证书不能为空
                    return $result;
                }

                $seller_auth = SellerAuthenticate::where('seller_id',$seller->id)->select('business_licence_img','certificate_img')->get();
                //如果不相同才有水印
                if($seller_auth->certificate_img != $certificateImg){
                    $watermark = SystemConfig::getConfig('watermark_logo');
                    if(!empty($watermark)){
                        //水印图片
                        $certificateImg = \YiZan\Utils\Image::watermark($certificateImg);
                    }else{
                        $certificateImg = self::moveSellerImage($seller->id, $certificateImg);

                        if (!$certificateImg) {//转移图片失败
                            $result['code'] = 10204;
                            return $result;
                        }
                    }
                }

            }

            $auth = new SellerAuthenticate();
            $auth->seller_id            = $seller->id;
            $auth->idcard_sn            = $idcardSn;
            // $auth->real_name            = $name;
            $auth->idcard_positive_img  = $idcardPositiveImg;
            $auth->idcard_negative_img  = $idcardNegativeImg;
            $auth->certificate_img      = $certificateImg;
            // $auth->business_licence_sn  = $businessLicenceSn;
            $auth->business_licence_img = $businessLicenceImg;
            $auth->update_time          = UTC_TIME;
            $auth->save();

            if($sellerType != PROPERTY_ORGANIZATION){  // 不是物业公司类型
                //如果个人加盟版 则保存至员工表
                $staff = new SellerStaff();
                if($sellerType == Seller::SELF_ORGANIZATION) {
                    $staff->type           = 0;
                } else {
                    $staff->type           = 3;
                }
                $staff->user_id            = $user->id;
                $staff->seller_id          = $seller->id;
                $staff->avatar             = $avatar;
                $staff->mobile             = $mobile;
                $staff->name               = $name;
                $staff->name_match         = String::strToUnicode($name.$mobile);
                $staff->address            = $address;
                // $staff->map_point          = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");
                // $staff->map_point_str      = $mapPoint;
                $staff->province_id        = $provinceId;
                $staff->city_id            = $cityId;
                $staff->area_id            = (int)$areaId;
                $staff->status             = 1;
                $staff->create_time        = UTC_TIME;
                $staff->create_day         = UTC_DAY;
                $staff->save();

                //保存员工扩展信息
                $sellerStaffExtend = new SellerStaffExtend();
                $sellerStaffExtend->staff_id = $staff->id;
                $sellerStaffExtend->seller_id = $seller->id;;
                $sellerStaffExtend->save();

            }

            UserVerifyCode::destroy($verifyCodeId);
            if ($cateIds != '') {
                SellerCateRelated::where('seller_id', $seller->id)->delete();
                $cateIds = is_array($cateIds) ? $cateIds : explode(',', $cateIds);
                foreach (array_filter($cateIds) as $key => $value) {
                    $cate = new SellerCateRelated();
                    $cate->seller_id = $seller->id;
                    $cate->cate_id = $value;
                    $cate->save();
                }
            }


            DB::commit();

            $sellerMap = new SellerMap();
            $sellerMap->seller_id       = $seller->id;
            $sellerMap->map_point       = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");
            $sellerMap->map_pos         = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
            $sellerMap->save();
            $result['data'] = $seller;
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 10119;
        }
        return $result;
    }

    /**
     * 服务人员搜索
     * @param  [type] $mobileName 手机或者名称
     * @return [type]             [description]
     */
    public static function searchSeller($mobileName) {
        $list = Seller::select('id', 'name', 'mobile');
        // $match = empty($mobileName) ? '' : String::strToUnicode($mobileName,'+');
        if (!empty($mobileName)) {
            $list->where('mobile', 'like', $mobileName.'%')
                //->selectRaw("IF(name = '{$mobileName}' or mobile = '{$mobileName}',1,0) AS eq,
                //        MATCH(name_match) AGAINST('{$match}') AS similarity")
                // ->whereRaw('MATCH(name_match) AGAINST(\'' . $match . '\' IN BOOLEAN MODE)')
                ->where('type', 2)
                ->orderBy('eq', 'desc')
                ->orderBy('similarity', 'desc');
        }
        return $list->orderBy('id', 'desc')->skip(0)->take(30)->get()->toArray();
    }


    /**
     * 获取服务人员
     * @param  int $id 服务人员id
     * @return array   服务人员
     */
    public static function getSystemSellerById($id)
    {
        return Seller::where('id', $id)
            ->with('province', 'city', 'area','authenticate','certificate')
            ->first();
    }
    /**
     * 删除服务人员
     * @param int  $id 服务人员id
     * @return array   删除结果
     */
    public static function deleteSystem($id)
    {
        $result = [
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> ""
        ];

        $seller = Seller::find($id);
        if (!$goods) {
            $result['code'] = 30630;
        }

        DB::beginTransaction();
        try {
            Seller::where('id', $id)->delete();
            SellerExtend::where('seller_id', $id)->delete();
            SellerAppointHour::where('seller_id', $id)->delete();
            SellerAuthenticate::where('seller_id', $id)->delete();
            SellerBank::where('seller_id', $id)->delete();
            SellerCertificate::where('seller_id', $id)->delete();
            SellerComplain::where('seller_id', $id)->delete();
            SellerMoneyLog::where('seller_id', $id)->delete();
            SellerWithdrawMoney::where('seller_id', $id)->delete();
            ReadMessage::where('seller_id', $id)->delete();
            Promotion::where('seller_id', $id)->delete();
            PromotionSn::where('seller_id', $id)->delete();
            OrderRate::where('seller_id', $id)->delete();
            Goods::where('seller_id', $id)->delete();
            GoodsExtend::where('seller_id', $id)->delete();

            self::removeSellerImage($id);
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
        return $result;
    }

    /**
     * 修改服务人员资料
     * @param  int $sellerId 服务人员id
     * @param array $logo LOGO
     * @param array $photos 人个相册
     * @param string $address 地址
     * @param string $mapPoint 纬度,经度(QQ地图坐标)
     * @param array $mapPos QQ地图坐标数组
     * @param int $provinceId 所在省编号
     * @param int $cityId 所在市编号
     * @param int $areaId 所在县编号
     * @param string $brief 简介
     * @param int $status 状态
     * @return [type]  [description]
     */
    public static function updateSeller($sellerId, $logo, $image, $name, $address, $mapPoint, $mapPos, $provinceId, $cityId, $areaId, $contacts, $cateIds, $status, $deliveryFee, $isAvoidFee,
                                        $avoidFee, $serviceFee, $deliveryTime, $deduct, $isCashOnDelivery, $serviceTel, $sendWay, $serviceWay, $reserveDays, $sendLoop, $refundAddress, $sendType)
    {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );

        $rules = array(
            'provinceId'    => ['min:1'],
            'cityId'        => ['min:1'],
            'areaId'        => ['min:1'],
            'address'       => ['required'],
            'mapPoint'      => ['required'],
            // 'mapPos'        => ['required'],
            'logo'          => ['required'],
            'name'          => ['required','min:2','max:30'],
            // 'reserveDays'   => ['required'],
            // 'sendLoop'      => ['required']
        );

        $messages = array(
            'provinceId.min'    => 10111,   // 请选择所在省
            'cityId.min'        => 10112,   // 请选择所在市
            'areaId.min'        => 10113,   // 请选择所在县
            'address.required'  => 30613,   // 请输入地址
            'mapPoint.required' => 30614,   // 请选择地图定位
            // 'mapPos.required'   => 30616,    // 请选择服务范围
            'logo.required'     => 30605,   //请上传LOGO图片
            'name.required'     => 10107,
            'name.min'          => 10108,
            'name.max'          => 10108,
            // 'reserveDays.required' => 30925,   // 请填写可预约天数
            //'reserveDays.min'      => 30926,   // 请设置可预约天数范围在0~30之间
            //'reserveDays.max'      => 30926,   // 请设置可预约天数范围在0~30之间
            // 'sendLoop.required'    => 30927,   // 请设置配送时间周期
            //'sendLoop.gt'          => 30928,   // 配送时间周期必须大于0
        );

        $validator = Validator::make([
            'provinceId'    => $provinceId,
            'cityId'        => $cityId,
            'areaId'        => $areaId,
            'address'       => $address,
            'mapPoint'      => $mapPoint,
            // 'mapPos'        => $mapPos,
            'logo'          => $logo,
            'name'          => $name,
            // 'reserveDays'   => $reserveDays,
            // 'sendLoop'      => $sendLoop,
        ], $rules, $messages);

        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        $seller = Seller::find($sellerId);
        if (!$seller) {//服务人员不存在
            $result['code'] = 30629;
            return $result;
        }

        if($seller->store_type == 1)
        {
            $refundAddress = trim($refundAddress);
            if( empty($refundAddress) )
            {
                $result['code'] = 30631; //全国店商家务必填写退货地址
                return $result;
            }
        }
        else
        {
            //如果设置了满减 存入满减金额 如果没有设置 清空满减金额
            $isAvoidFee = $isAvoidFee == 1 ? $isAvoidFee : 0;
            $avoidFee = $isAvoidFee == 1 ? $avoidFee : null;

            $mapPoint = Helper::foramtMapPoint($mapPoint);
            if (!$mapPoint){
                $result['code'] = 30615;    // 地图定位错误
                return $result;
            }

            $mapPos = Helper::foramtMapPos($mapPos);

            if (!$mapPos) {
                $result['code'] = 30617;    // 服务范围错误
                return $result;
            }

            if(count($sendWay) < 1) {
                $result['code'] = 30929;    // 请至少选择一个配送方式
                return $result;
            }

            if(count($serviceWay) < 1)
            {
                $result['code'] = 30930;    //请至少选择一个服务方式
                return $result;
            }

            if($reserveDays < 0 || $reserveDays >30 || !is_numeric($reserveDays)) {
                $result['code'] = 30926;    //请设置可预约天数范围在0~30之间
                return $result;
            }

            if($sendLoop <= 0 || !is_numeric($sendLoop)) {
                $result['code'] = 30928;    //配送时间周期必须大于0
                return $result;
            }
        }

        $logo = self::moveSellerImage($seller->id, $logo);
        if (!$logo) {//转移图片失败
            $result['code'] = 30606;
            return $result;
        }
        if (!empty($image)) {
            $image = self::moveSellerImage($seller->id, $image);
            if (!$image) {//转移图片失败
                $result['code'] = 30606;
                return $result;
            }
        }

        if($seller->store_type == 1)
        {
            //全国店无范围,默认范围值 无效的 只为填充数据
            $mapPos['pos'] = '31.90797991052 102.20781720873,31.913517218413 102.2418346142,31.894941463557 102.25701865688,31.887726973701 102.23846511045,31.887216933335 102.21802328888,31.90797991052 102.20781720873';
        }

        $seller->logo               = $logo;
        $seller->image              = $image;
        $seller->name               = $name;
        $seller->name_match         = String::strToUnicode($name.$seller->mobile);
        $seller->address            = $address;
        $seller->map_point_str      = $mapPoint;
        $seller->map_pos_str        = $mapPos["str"];
        $seller->map_point          = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");
        $seller->map_pos            = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
        $seller->province_id        = $provinceId;
        $seller->city_id            = $cityId;
        $seller->area_id            = $areaId;
        $seller->contacts           = $contacts;
        $seller->status             = $status == 1 ? 1 : 0;
        $seller->delivery_fee       = $deliveryFee;
        $seller->is_avoid_fee       = $isAvoidFee;
        $seller->avoid_fee          = $avoidFee;
        $seller->service_fee        = $serviceFee;
        $seller->service_tel        = $serviceTel;
        $seller->send_way           = count($sendWay) > 1 ? implode(",", $sendWay) : $sendWay[0];
        $seller->service_way        = count($serviceWay) > 1 ? implode(",", $serviceWay) : $serviceWay[0];
        $seller->reserve_days       = $reserveDays;
        $seller->send_loop          = $sendLoop;
        $seller->is_cash_on_delivery= $isCashOnDelivery;
        $seller->refund_address     = $refundAddress;
        $seller->send_type          = $sendType;

        if ($seller->save()) {
            SellerCateRelated::where('seller_id', $seller->id)->delete();
            $cateIds = is_array($cateIds) ? $cateIds : explode(',', $cateIds);
            foreach ($cateIds as $key => $value) {
                $cate = new SellerCateRelated();
                $cate->seller_id = $seller->id;
                $cate->cate_id = $value;
                $cate->save();
            }

            if($seller->store_type != 1)
            {
                $deliveryTime = json_decode($deliveryTime, true); //配送时间
                $dtime = SellerDeliveryTime::where('seller_id', $seller->id)->get();
                if ($dtime) {
                    SellerDeliveryTime::where('seller_id', $seller->id)->delete();
                }

                foreach ($deliveryTime['stimes'] as $key => $value) {
                    $delivery = new SellerDeliveryTime();
                    $delivery->seller_id     = $seller->id;
                    $delivery->stime         = $value;
                    $delivery->etime         = $deliveryTime['etimes'][$key];
                    $delivery->save();
                }
            }

            //周边店保存地理信息
            $addMapArr['map_point'] = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");
            $addMapArr['map_pos'] = DB::raw("GeomFromText('Polygon((". $mapPos["pos"] ."))')");

            if ($sellerId > 0) {
                SellerMap::where("seller_id", $sellerId)->update($addMapArr);
            } else {
                $addMapArr['seller_id'] = $sellerId;
                SellerMap::insert($addMapArr);
            }

            $result['code'] = 0;
            return $result;
        } else {
            $result['code'] = 99999;
            return $result;
        }

        return $result;
    }

    /**
     * 资质设置
     * @param int  $sellerId 服务人员id
     * @param array $certs 资质图片
     * @param int $status 状态
     * @return array   删除结果
     */
    public static function saveCert($sellerId, $authenticate)
    {
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];
        // var_dump($authenticate);
        // exit;
        $seller = Seller::find($sellerId);
        if (!$seller) {//服务人员不存在
            $result['code'] = 30629;
            return $result;
        }

        $cert = SellerAuthenticate::where("seller_id", $sellerId)->first();
        if (!$cert) {
            $cert = new SellerAuthenticate();
        }
        $idcardNegativeImg = $authenticate['idcardNegativeImg'];
        $idcardPositiveImg = $authenticate['idcardPositiveImg'];
        $idcardSn = $authenticate['idcardSn'];
        $businessLicenceImg = isset($authenticate['businessLicenceImg']) ? $authenticate['businessLicenceImg'] : '';
        $certificateImg = isset($authenticate['certificateImg']) ? $authenticate['certificateImg'] : '';

        $seller_idcard = SellerAuthenticate::where('idcard_sn', $idcardSn)->where("seller_id", '!=',$sellerId)->first();
        if($seller_idcard){
            $result['code'] = 30621;    //身份证号码已存在
            DB::rollback();
            return $result;
        }

        $idcardPositiveImg = self::moveSellerImage($seller->id, $authenticate['idcardPositiveImg']);
        if (!$idcardPositiveImg) {//转移图片失败
            $result['code'] = 10203;
            return $result;
        }

        $idcardNegativeImg = self::moveSellerImage($seller->id, $idcardNegativeImg);
        if (!$idcardNegativeImg) {//转移图片失败
            $result['code'] = 10204;
            return $result;
        }

        if($seller->type == Seller::SERVICE_ORGANIZATION)
        {

            if($businessLicenceImg == false)
            {
                $result['code'] = 10207; // 公司营业执照相片不能为空
                return $result;
            }

            $seller_auth = SellerAuthenticate::where('seller_id',$seller->id)->select('business_licence_img','certificate_img')->get();
            //如果不相同才有水印
            if($seller_auth->business_licence_img != $businessLicenceImg){
                $watermark = SystemConfig::getConfig('watermark_logo');
                if(!empty($watermark)){
                    //水印图片
                    $businessLicenceImg = \YiZan\Utils\Image::watermark($businessLicenceImg);
                }else{
                    $businessLicenceImg = self::moveSellerImage($seller->id, $businessLicenceImg);

                    if (!$businessLicenceImg) {//转移图片失败
                        $result['code'] = 10204;
                        return $result;
                    }
                }
            }


        }
        if ($seller->type == Seller::SELF_ORGANIZATION) {
            if($certificateImg == false)
            {
                $result['code'] = 10207; // 资质证书不能为空
                return $result;
            }

            $seller_auth = SellerAuthenticate::where('seller_id',$seller->id)->select('business_licence_img','certificate_img')->get();
            //如果不相同才有水印
            if($seller_auth->certificate_img != $certificateImg){
                $watermark = SystemConfig::getConfig('watermark_logo');
                if(!empty($watermark)){
                    //水印图片
                    $certificateImg = \YiZan\Utils\Image::watermark($certificateImg);
                }else{
                    $certificateImg = self::moveSellerImage($seller->id, $certificateImg);

                    if (!$certificateImg) {//转移图片失败
                        $result['code'] = 10204;
                        return $result;
                    }
                }
            }

        }

        $cert->seller_id            = $seller->id;
        $cert->idcard_positive_img    = $idcardPositiveImg;
        $cert->idcard_negative_img    = $idcardNegativeImg;
        $cert->business_licence_img   = $businessLicenceImg;
        $cert->certificate_img       = $certificateImg;
        $cert->idcard_sn             = $idcardSn;
        $cert->update_time          = UTC_TIME;
        $cert->status =1;
        $cert->save();
        return $result;
    }

    /**
     * [getList 获取卖家某一天日程列表]
     * @param  [int] $sellerId [卖家编号]
     * @return [array] $list          [description]
     */
    /*
    public static function getDayList($sellerId, $date) 
    {
        if ($sellerId < 1) 
        {
            return [];
        }
        
        $hours = [];
        
        $beginTime = Time::toTime($date);
        
        $endTime = $beginTime + 24 * 60 * 60 - 1;
                
        $appoint = SellerAppoint::where('seller_id', $sellerId)
            ->whereBetween('appoint_time', [$beginTime, $endTime])
            ->select('appoint_time', 'status')
            ->get();
        
        foreach($appoint as $value)
        {
            $hour = Time::toDate($value->appoint_time, 'H:i');
            
            $iHour = (int)Time::toDate($value->appoint_time, 'H');
            
            if($iHour * 3600 >= SellerAppoint::DEFAULT_BEGIN_ORDER_DATE &&
                $iHour * 3600 <= SellerAppoint::DEFAULT_END_ORDER_DATE)
            {
                $hours[$hour] = 
                [
                    'hour'      => $hour,
                    'status'    => $value->status
                ];
            }
        }
        
        //当表中无预约时间数据,返回默认数据
        for (; $beginTime <= $endTime; $beginTime += SellerAppoint::SERVICE_SPAN)
        {
            $iHour = (int)Time::toDate($beginTime, 'H');
            
            if($iHour * 3600 >= SellerAppoint::DEFAULT_BEGIN_ORDER_DATE &&
                $iHour * 3600 <= SellerAppoint::DEFAULT_END_ORDER_DATE)
            {
                $hour = Time::toDate($beginTime, 'H:i');
                
                if(array_key_exists($hour, $hours) == false)
                {
                    $hours[$hour] = 
                    [
                        'hour'      => $hour,
                        'status'    => SellerAppoint::ACCEPT_APPOINT_STATUS
                    ];
                }
            }
        }

        ksort($hours);
        
        return ['day' => Time::toTime($date), 'hours' => array_values($hours)];
    }
    */

    /**
     * 其他设置
     * @param  int $sellerId 服务人员id
     * @param  int $businessStatus 接单状态
     * @param  sting $businessDesc 接单说明
     * @param  int $sort 排序
     * @param  int $status 状态
     * @return
     */
    public static function extendSet($sellerId, $brief, $sort, $status)
    {
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];
        $seller = Seller::find($sellerId);
        if (!$seller) {//服务人员不存在
            $result['code'] = 30629;
            return $result;
        }

        $seller->brief        = $brief;
        $seller->sort         = $sort;
        $seller->status       = $status;

        if ($seller->save()) {
            $result['code'] = 0;
        } else {
            $result['code'] = 99999;
        }


        return $result;
    }

    /**
     * 检测手机号验证码
     * @param  sting $mobile 手机号
     * @param  sting $verifyCode 验证码
     * @return
     */
    public static function checkTelcode($mobile, $verifyCode) {
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];
        $rules = array(
            'mobile'           => ['required','regex:/^1[0-9]{10}$/'],
            'code'             => ['required','size:6'],
        );

        $messages = array(
            'mobile.required'               => '10101',
            'mobile.regex'                  => '10102',
            'code.required'                 => '10103',
            'code.size'                     => '10104',
        );

        $validator = Validator::make([
            'mobile'     => $mobile,
            'code'       => $verifyCode,
        ], $rules, $messages);

        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        //检测验证码
        $verifyCodeId = UserService::checkVerifyCode($verifyCode, $mobile, UserVerifyCode::TYPE_REG);
        if (!$verifyCodeId) {
            $result['code'] = 10104;
            return $result;
        }

        $mobilecheck = self::getByMobile($mobile);
        if ($mobilecheck) { //手机号已存在
            $result['code'] = 10118;
            return $result;
        }

        return $result;
    }

    /**
     * 更改手机号
     * @param  int $sellerId 服务人员id
     * @param  int $mobile 原手机号
     * @param  string $pwd 密码
     * @param  int $newMobile 新手机号
     * @param  int $verifyCode 验证码
     * @return
     */
    public static function updateMobile($sellerId, $oldMobile, $pwd, $mobile, $verifyCode)
    {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );
        $rules = array(
            'mobile'           => ['required','regex:/^1[0-9]{10}$/'],
            'code'             => ['required','size:6'],
            'pwd'              => ['required'],
            'newMobile'        => ['required','regex:/^1[0-9]{10}$/']
        );

        $messages = array(
            'mobile.required'               => '10101',
            'mobile.regex'                  => '10102',
            'code.required'                 => '10103',
            'code.size'                     => '10104',
            'pwd.required'                  => '10105',
            'newMobile.required'            => '10101',
            'newMobile.regex'               => '10102',
        );

        $validator = Validator::make([
            'mobile'     => $oldMobile,
            'code'       => $verifyCode,
            'pwd'        => $pwd,
            'newMobile'  => $mobile,
        ], $rules, $messages);

        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        if(strlen($pwd) >= 21 || strlen($pwd) <= 5){
            $result['code'] = 10106;
            return $result;
        }
        //检测验证码
        $verifyCodeId = UserService::checkVerifyCode($verifyCode, $oldMobile, UserVerifyCode::TYPE_REG);
        if (!$verifyCodeId) {
            $result['code'] = 10104;
            return $result;
        }
        $seller = Seller::find($sellerId);
        if (!$seller) {//服务人员不存在
            $result['code'] = 30629;
            return $result;
        }
        $user = User::where('id',$seller->user_id)->first();
        $pwd = md5(md5($pwd) . $user->crypt);

        if ($user->pwd !== $pwd ) { //原密码不对
            $result['code'] = 11106;
            return $result;
        }

        $mobilecheck = self::getByMobile($mobile);
        if ($mobilecheck) { //新手机号已存在
            $result['code'] = 10118;
            return $result;
        }
        //不属于此商家的员工手机已存在
        $staff_check = (int)SellerStaff::where('mobile', $mobile)->where('seller_id', '!=', $sellerId)->pluck('id');
        if ($staff_check > 0) {
            $result['code'] = 10118;    // 手机号码已被注册
            return $result;
        }

        $seller->mobile  = $mobile;
        $staff_check = SellerStaff::where('seller_id', $sellerId)->where('user_id', $seller->user_id)->first();
        DB::beginTransaction();
        try {
            User::where('id', $seller->user_id)->update(['mobile' => $mobile]);
            $seller->save();
            if ($staff_check !== false) {
                $staff_check->mobile = $mobile;
                $staff_check->save();
            }
            DB::commit();
            $result['code'] = 0;
        } catch (Exception $e) {
            print_r([22111]);
            DB::rollback();
            die;
        }
        return $result;
    }

    /**
     * 找回密码/修改密码
     * @param  int $sellerId 服务人员id
     * @param  int $mobile 原手机号
     * @param  string $idcardSn 证件号
     * @param  sting $pwd 新密码
     * @param  int $newpwd 确认密码
     * @param  int $verifyCode 验证码
     * @param  type $type 类型back/change
     * @return
     */
    public static function updatePass($sellerId, $mobile, $idcardSn, $pwd, $newpwd, $verifyCode, $type) {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );

        $rules = array(
            'mobile'           => ['required','regex:/^1[0-9]{10}$/'],
            'code'             => ['required','size:6'],
            'idcardSn'         => ['required','regex:/^[0-9]{18}|[0-9]{15}|[0-9]{17}[xX]{1}$/'],
            'pwd'              => ['required','min:6','max:20'],
            'newpwd'           => ['required','min:6','max:20'],
        );

        $messages = array(
            'mobile.required'               => '10101',
            'mobile.regex'                  => '10102',
            'code.required'                 => '10103',
            'code.size'                     => '10104',
            'idcardSn.required'             => '10114',
            'idcardSn.regex'                => '10115',
            'pwd.required'                  => '10105',
            'pwd.min'                       => '10106',
            'pwd.max'                       => '10106',
            'newpwd.required'               => '10105',
            'newpwd.min'                    => '10106',
            'newpwd.max'                    => '10106',
        );

        $validator = Validator::make([
            'mobile'     => $mobile,
            'code'       => $verifyCode,
            'pwd'        => $pwd,
            'idcardSn'   => $idcardSn,
            'newpwd'     => $newpwd,
        ], $rules, $messages);

        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        //检测验证码
        $verifyCodeId = UserService::checkVerifyCode($verifyCode, $mobile, UserVerifyCode::TYPE_REG);
        if (!$verifyCodeId) {
            $result['code'] = 10104;
            return $result;
        }

        if (trim($pwd) !== trim($newpwd)) { //两次密码不一致
            $result['code'] = 10121;
            return $result;
        }
        if ($type == 'change') {
            $seller = Seller::find($sellerId);
        } else {
            $seller = self::getByMobile($mobile);
        }
        if (!$seller) {//服务人员不存在
            $result['code'] = 30629;
            return $result;
        }
        $auth = SellerAuthenticate::where('seller_id', $seller->id)->first();
        if ($auth->idcard_sn !== $idcardSn) {//证件号不对
            $result['code'] = 10122;
            return $result;
        }
        $crypt  = String::randString(6);
        $pwd    = md5(md5($pwd) . $crypt);
        $users = User::where('id',$seller->user_id)->update(['pwd' => $pwd, 'crypt'=> $crypt]);

        if ($users) {
            $result['code'] = 0;
            return $result;
        } else {
            $result['code'] = 99999;
            return $result;
        }
        return $result;

    }

    /**
     * 员工搜索
     * @param  [type] $mobileName 手机或者名称
     * @return [type]             [description]
     */
    public static function searchStaff($sellerId,$mobileName){
        $list = SellerStaff::select('id', 'name', 'mobile')->where('seller_id',$sellerId)->where("status", 1)->whereIn('type', [0, 2, 3]);
        // $match = empty($mobileName) ? '' : String::strToUnicode($mobileName,'+');
        if (!empty($mobileName)) {
            //->selectRaw("IF(name = '{$mobileName}' or mobile = '{$mobileName}',1,0) AS eq,
            //      MATCH(name_match) AGAINST('{$match}') AS similarity")
            $list->where(function($query) use ($mobileName){
                $query->where('mobile', $mobileName)
                    ->orWhere('name', 'like', '%'.$mobileName.'%');
            });
            // ->whereRaw('MATCH(name_match) AGAINST(\'' . $match . '\' IN BOOLEAN MODE)')
            //->orderBy('eq', 'desc')
            //->orderBy('similarity', 'desc');
        }
        return $list->orderBy('id', 'desc')->skip(0)->take(30)->get()->toArray();
    }

    /*
    * 物业公司修改
    */
    public static function updateBasic($sellerId, $businessLicenceImg) {
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];

        $seller = Seller::find($sellerId);
        if (!$seller) {
            $result['code'] = 30629;
            return $result;
        }

        if (!empty($businessLicenceImg)) {
            $seller_auth = SellerAuthenticate::where('seller_id',$seller->id)->select('business_licence_img','certificate_img')->get();
            //如果不相同才有水印
            if($seller_auth->business_licence_img != $businessLicenceImg){
                $watermark = SystemConfig::getConfig('watermark_logo');
                if(!empty($watermark)){
                    //水印图片
                    $businessLicenceImg = \YiZan\Utils\Image::watermark($businessLicenceImg);
                }else{
                    $businessLicenceImg = self::moveSellerImage($seller->id, $businessLicenceImg);

                    if (!$businessLicenceImg) {//转移图片失败
                        $result['code'] = 10204;
                        return $result;
                    }
                }
            }

            SellerAuthenticate::where('seller_id', $sellerId)->update(['business_licence_img'=>$businessLicenceImg]);
        }

        return $result;
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


                if($value['isDefault'] == 1){
                    $update = [
                        'seller_id' => $sellerId,
                        'num' => $value["defaultNum"],
                        'money' => $value["defaultMoney"],
                        'add_num' => $value["defaultAddNum"],
                        'add_money' => $value["defaultAddMoney"],
                        'is_default' => $value["isDefault"],
                    ];
                }else{
                    $update = [
                        'seller_id' => $sellerId,
                        'num' => $value["otherNum"],
                        'money' =>$value["otherMoney"],
                        'add_num' => $value["otherAddNum"],
                        'add_money' =>$value["otherAddMoney"],
                        'is_default' =>0,
                    ];
                }
                $id = FreightTmp::insertGetId($update);
                if($value['isDefault'] != 1){
                    $tmpCity = [];
                    $newpid = explode(",",$value["pid"]);
                    foreach ($newpid as $k => $v) {
                        if(!$value["cid"][$v]){
                            $tmpCity[] = [
                                'freight_tmp_id' => $id,
                                'seller_id' => $sellerId,
                                'region_id' => $v
                            ];
                        }else{
                            foreach ($value["cid"][$v] as $ks => $vs) {
                                $tmpCity[] = [
                                    'freight_tmp_id' => $id,
                                    'seller_id' => $sellerId,
                                    'region_id' => $vs
                                ];
                            }
                        }
                    }
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
}
