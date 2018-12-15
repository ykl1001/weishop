<?php 
namespace YiZan\Services\System;

use YiZan\Models\System\SellerStaff;
use YiZan\Models\System\SellerStaffExtend;
use YiZan\Models\System\Seller;
use YiZan\Models\System\User;
use YiZan\Models\GoodsStaff;
use YiZan\Models\Order;
use YiZan\Models\StaffAppoint;
use YiZan\Models\StaffMap;
use YiZan\Models\SellerDistrict;
use YiZan\Models\SellerStaffDistrict;

use YiZan\Utils\String;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use DB, Validator, Lang;

class SellerStaffService extends \YiZan\Services\SellerStaffService
{
    

	/**
     * 员工列表
     * @param  string $name 员工名称
     * @param  string $mobile 员工电话
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          员工信息
	 */
	public static function getSystemList($sellerId, $mobile, $name, $page, $pageSize,$isSeller,$provinceId, $cityId, $areaId,$isSystem) {
		$list = SellerStaff::orderBy('id', 'desc');
        if($isSeller == false){
            $list->where('seller_id','<>', ONESELF_SELLER_ID);
        }

        if($sellerId > 0){
            $list->where('seller_id', $sellerId);
        }
        
        if(!empty($mobile)) {
            $list->where('mobile', 'like', '%'.$mobile.'%');
        }
        if (!empty($name)) {
            $list->where('name', 'like', '%'.$name.'%');
        }

        if ($provinceId > 0) {
            $list->where('province_id', $provinceId);
        }

        if ($cityId > 0) {
            $list->where('city_id', $cityId);
        }

        if ($areaId > 0) {
            $list->where('area_id', $areaId);
        }

        if($isSystem > 0){
            $list->where('is_system', 1);
        }

		$totalCount = $list->count(); 
		$list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->with('seller','user','province','city','area')
            ->get()
            ->toArray();
        return ["list"=>$list, "totalCount"=>$totalCount];
	}

    /**
     * 获取员工
     * @param  int $id 员工id
     * @return array   员工
     */
	public static function getSystemSellerStaffById($id) {
		return SellerStaff::where('id', $id)
		    ->with('seller', 'user', "province", "city", "area")
		    ->first();
	}

    /**
     * 删除员工
     * @param int  $id 员工id
     * @return array   删除结果
     */
	public static function deleteSystem($id) {
		$result = [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ""
		];

        $SellerStaff = SellerStaff::whereIn($id);

        if (!$SellerStaff) {
            $result['code'] = 50201;
        }

        DB::beginTransaction();
        try {
            //删除员工
            SellerStaff::whereIn('id', $id)->delete();
            //删除员工提供的服务
            GoodsStaff::whereIn('staff_id', $id)->delete();
            //删除员工扩展信息
            SellerStaffExtend::whereIn('staff_id', $id)->delete();
            //删除员工预约信息
            // StaffAppoint::where('staff_id', $id)->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }

        //DB::beginTransaction();

        /*$staff_del = SellerStaff::where('id', $id)->update(['is_del'=>1]);
        // self::removeSellerImage($id);
        if ($staff_del !== false) {
            $result['code'] = 0;
        } else {
            $result['code'] = 99999;
        }*/
        /*try
        {
            SellerStaff::where('id', $id)->delete();
            SellerStaffExtend::where('staff_id', $id)->delete();
            self::removeStaffImage($SellerStaff->seller_id, $SellerStaff->id);
            
            DB::commit();
        } 
        catch (Exception $e) 
        {
            DB::rollback();
            $result['code'] = 99999;
        }*/
		return $result;
	}
    /**
     * 根据服务选择可用员工
     * @param mixed $goodsId 
     * @return array [description]
     */
    public static function getGoodsStaff($goodsId)
    {
        return DB::table("seller_staff")
            ->join("goods_staff", "seller_staff.id", "=", "goods_staff.staff_id")
            ->where("goods_staff.goods_id", $goodsId)
            ->select("seller_staff.name", "seller_staff.id")
            ->get();
    }
    /**
     * 更改员工状态
     * @param int $id;  员工编号
     * @return [type] [description]
     */
    public static function updateStaffStatus($id,$status){
        $result = array (
        	'status'	=> true,
			'code'	    => self::SUCCESS,
			'data'	    => $status,
			'msg'	    => null
		);
 
        $staff = SellerStaff::where('id',$id)->first();
        if (!$staff) {
            $result['code'] = 50201;
            return $result;
        }
        $staff_result = SellerStaff::where('id',$id)->update(['status'=>$status]);
        if ($staff_result !== true) 
        {
            $result['code'] = 99999;
        }
        return $result;
    }
    /**
	 * 员工搜索
	 * @param  [type] $mobileName 手机或者名称
	 * @return [type]             [description]
	 */
	public static function searchUser($mobileName) {
		$list = SellerStaff::select('id', 'name', 'mobile')->with(['address' => function($query) {
						$query->orderBy('is_default', 'desc')
							  ->orderBy('id', 'desc')
							  ->groupBy('user_id');
					}]);
		
		if (!empty($mobileName)) {
			$list->where('mobile', 'like', $mobileName .'%');
		}

		return $list->orderBy('id', 'desc')->skip(0)->take(30)->get()->toArray();
	}

    /**
     * [saveSellerStaff 创建更新员工]
     * @param  [type] $id       [员工ID]
     * @param  [type] $mobile   [员工电话]
     * @param  [type] $pwd      [密码]
     * @param  [type] $name     [员工昵称]
     * @param  [type] $avatar   [头像]
     * @param  [type] $sellerId [服务站Id]
     * @param  [type] $brief    [简介]
     * @param  [type] $orderStatus   [接单状态]
     * @param  [type] $sort     [排序]
     * @param  [type] $cardNumber     [身份证号码]
     * @param  [type] $beginTime     [开始时间]
     * @param  [type] $endTime     [结束时间]
     * @return [array]          [返回]
     */
    public static function saveSellerStaff($id, $mobile, $pwd, $name, $avatar, $sellerId, $sex, $type, $provinceId, $cityId, $areaId, $address, $authentication, $authenticateImg, $mapPos, $mapPoint, $status,$isSystem, $company) {

            $result = array(
                'code'  => self::SUCCESS,
                'data'  => null,
                'msg'   => ''
            );

            $rules = array(
                'mobile'        => ['required','regex:/^1[0-9]{10}$/'],
                'name'          => ['required'],
                'avatar'        => ['required'],
                'sellerId'      => ['min:1'],
                'type'      => ['required'],
                'address'      => ['required'],
                'authentication'      => ['required'],
                'authenticateImg'      => ['required']
//                'company'       => ['required']
            );

            $messages = array(
                'mobile.required'   => 50224,   //请填写手机号
                'mobile.regex'      => 50203,   //请输入正确的电话号码
                'name.required'     => 50202,   //请输入员工姓名
                'avatar.required'   => 50225,   //请上传员工头像
                'sellerId.min'      => 50226,
                'type.required'      => 70301,
                'address.required'      => 30616,
                'authentication.required'      => 70311,
                'authenticateImg.required'      => 70312,
//                'company.required'  => 50233,   //请填写所属公司信息
            );

            $validator = Validator::make([
                    'mobile'        => $mobile,
                    'name'          => $name,
                    'avatar'        => $avatar,
                    'sellerId'      => $sellerId,
                    'type'          => $type,
                    'address'       => $address,
                    'authentication'      => $authentication,
                    'authenticateImg'      => $authenticateImg,
//                    'company'       => $company,
                ], $rules, $messages);
            
            //验证信息
            if ($validator->fails()){
                $messages = $validator->messages();
                $result['code'] = $messages->first();
                return $result;
            }

            if($id < 1) {
                if(empty($pwd)) {
                    $result['code'] = 30629; //新增员工密码不能为空
                    return $result;
                }
            }

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

            if (!$authentication) {
                $result['code'] = 30617;    // 服务范围错误
                return $result;
            }

            if ($id > 0) {
                $staff = SellerStaff::find($id);
                if (!$staff) {
                    $result['code'] = 50201; //员工不存在
                    return $result;
                }
            }else {
                $staff = new SellerStaff();
            }
            if($sellerId > 0){
                $seller = Seller::where('id', $sellerId)->first();
                if ($id < 1 && $seller->type == 1) {
                    $result['code'] = 50215;    // 个人加盟商家不能添加人员
                    return $result;
                }
            }
            DB::beginTransaction();
            try {
                $remove_images = [];

                //查找手机号码对应的会员编号
                $mobile_user_id = (int)User::where('mobile', $mobile)->pluck('id');
                //要更新会员的信息
                $update_user_info = [];

                if ($mobile_user_id > 0) {
                    //会员存在且在员工表也同时存在
                    $staff_check = (int)SellerStaff::where('user_id', $mobile_user_id)->where('id', '<>', $id)->pluck('id');
                    if ($staff_check > 0) {
                        $result['code'] = 50215;    // 手机号码已被注册
                        return $result;
                    }

                    //如果电话存在商家且员工不属于此商家,则不能添加
                    $seller_check = Seller::where('user_id', $mobile_user_id)->pluck('id');
                    if ($seller_check > 0 && $seller_check != $sellerId) {
                        $result['code'] = 50215;    // 手机号码已被注册
                        return $result;
                    }
                }
                //当为修改机构员工时
                if ($id > 0) {
                    $staff_old_user_id = $staff->user_id;
                    //如果修改手机号码
                    if ($staff->mobile != $mobile) {
                        //如果被注册,则更新服务人员关联的会员编号
                        if ($mobile_user_id > 0) {
                            $staff->user_id = $mobile_user_id;
                        } else {//如果未被注册,则修改关联会员的手机号码
                            $update_user_info['mobile'] = $mobile;
                        }
                    }

                    if (!empty($pwd)) {//当为修改密码时
                        $crypt  = String::randString(6);
                        $pwd    = md5(md5($pwd) . $crypt);

                        $update_user_info['crypt'] = $crypt;
                        $update_user_info['pwd']   = $pwd;
                    }

                } else {//当为新增服务人员时
                    if ($mobile_user_id < 1) {//当手机号没有被注册时,创建会员
                        if (empty($pwd)) {
                            $result['code'] = 50206;
                            return $result;
                        }

                        $user                   = new User();
                        $user->mobile           = $mobile;
                        $user->name_match       = String::strToUnicode($name.$mobile);
                        $user->name             = $name;
                        $user->reg_time         = UTC_TIME;
                        $user->reg_ip           = CLIENT_IP;
                        $user->is_sms_verify    = 1;
                        $user->save();
                        $staff->user_id = $user->id;

                        $avatar_user = self::moveUserImage($user->id, $avatar);
                        if (!$avatar_user) {//转移图片失败
                            $result['code'] = 50216;
                            return $result;
                        }
                        $update_user_info['avatar'] = $avatar_user;
                    } else {//当手机号已存在时关联会员编号
                        $staff->user_id = $mobile_user_id;
                    }
                    
                    if (!empty($pwd)) {
                        $crypt  = String::randString(6);
                        $pwd    = md5(md5($pwd) . $crypt);
                        $update_user_info['crypt'] = $crypt;
                        $update_user_info['pwd']   = $pwd;
                    }
                }

                //当会员数据有更新项时
                if (count($update_user_info) > 0) {
                    User::where('id', $staff->user_id)->update($update_user_info);
                }
                
                $staff->seller_id          = $sellerId;
                $staff->mobile             = $mobile;
                $staff->name               = $name;
                $staff->name_match         = String::strToUnicode($name.$mobile);
                $staff->sex                = $sex;
                $staff->type               = $type;
                $staff->status             = $status;
                $staff->province_id        = $provinceId;
                $staff->city_id            = $cityId;
                $staff->area_id            = $areaId;
                $staff->address            = $address;
                $staff->create_time        = UTC_TIME;
                $staff->create_day         = UTC_DAY;
                $staff->authentication     = $authentication;
                $staff->authenticate_img   = $authenticateImg;
                $staff->map_point_str      = $mapPoint;
                $staff->map_pos_str        = $mapPos["str"];
                $staff->map_point          = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");
                $staff->map_pos            = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
                $staff->is_system          = $isSystem;
                $staff->company            = $company;
                $staff->save();
                
                $avatar = self::moveSellerImage($staff->seller_id, $avatar);
                if (!$avatar) {//转移图片失败
                    $result['code'] = 10202;
                    return $result;
                }
                $staff->avatar = $avatar;
                $staff->save();
                
                //员工修改时查找相同手机商家更新相关信息
                if ($staff_old_user_id) {
                    Seller::where('id', $sellerId)->where('user_id', $staff_old_user_id)->update([
                            'mobile'=>$mobile, 
                            'user_id'=>$staff->user_id
                        ]);
                }
                
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                $result['code'] = 99999;
            }
            
            return $result;

    }


    public function searchGoods($name, $sellerId){
        $list = SellerStaff::orderBy('id', 'DESC')->where("status", 1);
        if($name){
            $list->where('name', 'like', '%'.$name.'%');
        }
        if($sellerId > 0){
            $list->where('seller_id', $sellerId);
        }
        return $list->get()->toArray();
    }

    /**
     * @param $orderId 获取配送服务人员
     */
    public function getsendstaff($orderId,$name){
        $order = Order::where('id',$orderId)->where('is_all', 0)->with('seller')->first();
        //没有订单
        if ($order == false)
        {
            $result['code'] = 50101;
            return $result;
        }

        //订单状态不对
        if($order->status == ORDER_STATUS_AFFIRM_SELLER || $order->status == ORDER_STATUS_SYSTEM_SEND || $order->status == ORDER_STATUS_CALL_SYSTEM_SEND || $order->status == ORDER_STATUS_GET_SYSTEM_SEND || $order->status == ORDER_STATUS_START_SERVICE){

        }else{
            $result['code'] = 50103;
            return $result;
        }


        //找配送人员
        $dbPrefix = env('DB_PREFIX');

        $sql = "SELECT
                    s.id,s.name,
                    COUNT(distinct(o.id)) AS getorderNum,
                    COUNT(distinct(o1.id)) AS sendingNum
                FROM {$dbPrefix}seller_staff AS s
                LEFT JOIN `{$dbPrefix}order` AS o ON o.seller_staff_id = s.id AND o.is_all = 0 AND o.status = '".ORDER_STATUS_GET_SYSTEM_SEND."'
                LEFT JOIN `{$dbPrefix}order` AS o1 ON o1.seller_staff_id = s.id AND o1.is_all = 0 AND o1.status = '".ORDER_STATUS_START_SERVICE."'
                where s.status = 1 AND s.is_system = 1 AND s.is_work = 1";

        if($name){
            $sql .= " AND s.name like '%{$name}%'";
        }
        if($order->seller->province_id > 0){
            $sql .= " AND s.province_id = '{$order->seller->province_id}'";
        }
        if($order->seller->city_id > 0){
            $sql .= " AND s.city_id = '{$order->seller->city_id}'";
        }
        if($order->seller->area_id > 0){
            $sql .= " AND s.area_id = '{$order->seller->area_id}'";
        }

        $sql .= " GROUP BY s.id
                ORDER BY getorderNum ASC,sendingNum ASC,s.id ASC";

        $list = DB::select($sql);

        $result['code'] = 0;

        if(!empty($list)){
            $result['data'] = [];
            foreach($list as $k=>$v){
                $result['data'][] = (array)$v;
            }
        }else{
            $result['data'] = '';
        }
        
        return $result;
    }


}
