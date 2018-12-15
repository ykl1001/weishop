<?php
namespace YiZan\Services\Proxy;

use YiZan\Models\SellerPayLog;
use YiZan\Models\System\Seller;
use YiZan\Models\SellerMap;
use YiZan\Models\Proxy;
use YiZan\Models\Order;
use YiZan\Models\SellerBank;
use YiZan\Models\SellerCateRelated;
use YiZan\Models\System\User;
use YiZan\Models\GoodsCate;
use YiZan\Models\SystemConfig;
use YiZan\Models\PropertyUser;
use YiZan\Models\System\SellerExtend;
use YiZan\Models\System\SellerAuthenticate;
use YiZan\Models\System\SellerMoneyLog;
use YiZan\Models\ReadMessage;
use YiZan\Services\PushMessageService;
use YiZan\Models\System\Promotion;
use YiZan\Models\System\PromotionSn;
use YiZan\Models\System\OrderRate;
use YiZan\Models\System\Goods;
use YiZan\Models\System\GoodsExtend;
use YiZan\Models\SellerStaff;
use YiZan\Models\SellerStaffExtend;
use YiZan\Models\UserVerifyCode;
use YiZan\Models\SellerDeliveryTime;
use YiZan\Models\District;
use YiZan\Models\SellerCate;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Utils\String;
use DB, Validator, Lang;

class SellerService extends \YiZan\Services\SellerService {
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
	 * 商家搜索
	 * @param  [type] $mobileName 手机或者名称
	 * @return [type]             [description]
	 */
	public static function searchSeller($mobileName) {
		$list  = Seller::select('id', 'name', 'mobile');
		//$match = empty($mobileName) ? '' : String::strToUnicode($mobileName, '+');
		$list->where('is_del', 0);
		if (!empty($mobileName)) {
			$list->where(function($query) use ($mobileName){
              $query->where('name', 'like', '%'.$mobileName.'%')
                    ->orWhere('mobile',$mobileName);
            });//->selectRaw("IF(name = '{$mobileName}' or mobile = '{$mobileName}',1,0) AS eq,
                 //       MATCH(name_match) AGAINST('{$match}') AS similarity")
				 //->whereRaw('MATCH(name_match) AGAINST(\'' . $match . '\' IN BOOLEAN MODE)')

		}

		return $list->whereIn('type', [1,2])
                    ->orderBy('id', 'desc')
					->skip(0)
					->take(30)
					->get()
					->toArray();
	}

	/**
	 * 代理下商家列表
	 * @param  string $name     	  名称
	 * @param  int    $status         状态
	 * @param  int    $page           页码
     * @param  int    $pageSize       每页数
	 * @return array          订单列表
	 */
	public static function getSystemList($proxy, $mobileName, $mobile, $provinceId, $cityId, $areaId, $status,$cateId, $page, $pageSize)
    {
		$list = Seller::orderBy('id', 'desc');  

		switch ($proxy->level) {
			case '2':
				$list->where('first_level', $proxy->pid)
					 ->where('second_level', $proxy->id);
				break;
			case '3':
				$parentProxy = Proxy::find($proxy->pid);
				$list->where('first_level', $parentProxy->pid)
					 ->where('second_level', $parentProxy->id)
					 ->where('third_level', $proxy->id);
				break;
			
			default:
				$list->where('first_level', $proxy->id);
				break;
		}

		if (!empty($mobileName)) {
			$list->where('name', 'like', '%'.$mobileName.'%');
		}

		$list->where('is_del', 0);
		$list->where('is_check', 1);
		$list->whereIn('type', [1,2]);

		if ($provinceId > 0) {
			$list->where('province_id', $provinceId);
		}

		if ($cityId > 0) {
			$list->where('city_id', $cityId);
		}

		if ($areaId > 0) {
			$list->where('area_id', $areaId);
		}

		if ($status > 0) {
			$list->where('status', $status - 1);
		}

        if ($mobile != '') {
            $list->where('mobile', $mobile);
        }

        if($cateId > 0) {
            $list->whereIn('id', function($query) use ($cateId) {
                $query->select('seller_id')
                    ->from('seller_cate_related')
                    ->where('cate_id', $cateId);
            });
        }

		$totalCount = $list->count();
        $list->select(DB::raw("*, (select count(1) from ".env('DB_PREFIX')."seller_staff as s where s.seller_id = ".env('DB_PREFIX')."seller.id) as staffCount"));
		$list       = $list->skip(($page - 1) * $pageSize)
						   ->take($pageSize)
						   ->with('province', 'city', 'area', 'extend', 'first', 'second', 'third')
						   ->get()
						   ->toArray();
						  
		//获取商家商品服务分类及人员统计
		foreach ($list as $key => $value) {
            $list[$key]['balance'] = (double)$value['extend']['money'];
			$list[$key]['goodscount'] = Goods::where('seller_id', $value['id'])->where('type', 1)->count();
			$list[$key]['servicecount'] = Goods::where('seller_id', $value['id'])->where('type', 2)->count();
			$list[$key]['goodscatecount'] = GoodsCate::where('seller_id', $value['id'])->where('type', 1)->count();
			$list[$key]['servicecatecount'] = GoodsCate::where('seller_id', $value['id'])->where('type', 2)->count();
			$list[$key]['staffcount'] = SellerStaff::where('seller_id', $value['id'])->where('status', 1)->count();
		}
		// print_r($list);
		// exit;
		return ["list" => $list, "totalCount" => $totalCount];
	}

	/*
	* 商家审核
	*/
	public static function getSystemSellerList($proxy, $name, $isCheck, $page, $pageSize)
    {
		$list = Seller::orderBy('id', 'desc'); 

		switch ($proxy->level) {
			case '2':
				$list->where('first_level', $proxy->pid)
					 ->where('second_level', $proxy->id);
				break;
			case '3':
				$parentProxy = Proxy::find($proxy->pid);
				$list->where('first_level', $parentProxy->pid)
					 ->where('second_level', $parentProxy->id)
					 ->where('third_level', $proxy->id);
				break;
			
			default:
				$list->where('first_level', $proxy->id);
				break;
		}

		// $match = empty($name) ? '' : String::strToUnicode($name, '+');
		// if (!empty($match)) {
		// 	$list->whereRaw('MATCH(name_match) AGAINST(\'' . $match . '\' IN BOOLEAN MODE)');
		// }
		if (!empty($name)) {
			$list->where('name', 'like', '%'. $name.'%');
		}

		$list->where('is_del', 0);
		$list->where('is_authshow', 0);

		if ($isCheck > 0) {
			$list->where('is_check', $isCheck - 2);
		}

		$totalCount = $list->count();
        $list->select(DB::raw("*, (select count(1) from ".env('DB_PREFIX')."seller_staff as s where s.seller_id = ".env('DB_PREFIX')."seller.id) as staffCount"));
		$list       = $list->skip(($page - 1) * $pageSize)
						   ->take($pageSize)
						   ->with('province', 'city', 'area')
						   ->get()
						   ->toArray();

		return ["list" => $list, "totalCount" => $totalCount];
	}

	/**
	 * 添加服务人员
	 * @param  int   $type           机构类型
	 * @param string $mobile         手机号
	 * @param string $pwd            密码
	 * @param string $name           名称
     * @param string $contacts       负责人
	 * @param string $address        地址
	 * @param int    $provinceId     所在省编号
	 * @param int    $cityId         所在市编号
	 * @param int    $areaId         所在县编号
	 * @param string $brief          简介
	 * @param int    $sort           排序
	 * @param int    $businessStatus 营业状态
	 * @param string $businessDesc   营业说明
	 * @param int    $status         状态
	 * @param string $refundAddress  退款地址
	 * @return array   添加结果
	 */
	public static function saveSeller($proxy, $id,  $mobile, $pwd, $name, $type, $contacts,  $address, $logo, $image, $provinceId, $cityId, $areaId, $brief, $mapPos, $mapPoint, $idcardSn, $businessLicenceImg, $idcardPositiveImg, $idcardNegativeImg, $cateIds, $serviceFee = 0, $deliveryFee = 0, $isAvoidFee, $avoidFee, $isAuthenticate, $certificateImg, $serviceTel, $deliveryTime, $deduct, $isCashOnDelivery, $storeType, $refundAddress) {
        $cateIds = is_array($cateIds) ? $cateIds : [$cateIds]; 
		$result = array('code' => self::SUCCESS,
						'data' => null,
						'msg' => '');

		$rules = array(
					   'mobile' 			 => ['required','regex:/^1[0-9]{10}$/'],
					   'serviceTel' 		 => ['regex:/^[0-9]{8,11}$/'],
					   'name' 				 => ['required', 'max:20'],
					   'pwd' 		 		 => ['max:20'],
					   'contacts'			 => ['max:10'],
					   'provinceId' 		 => ['min:1'],
					   'cityId' 			 => ['min:1'],
					   'areaId' 			 => ['min:1'],
					   'address' 			 => ['required'],
//					   'idcardSn'      		 => ['required'],
//			           'idcardPositiveImg'   => ['required'],
//			           'idcardNegativeImg'   => ['required'],
			           'mapPoint'            => ['required'],
			           // 'mapPos'              => ['required'],
			           'logo'              	 => ['required'],
			           'serviceFee'          => ['max:5'],
			           'deliveryFee'         => ['max:5'], 
			           'brief'				 => ['max:200'],
			           'storeType'			 => ['required'],
                    );

		$messages = array(
						'mobile.required' 				=> 30601,// 请输入手机号码
						'mobile.regex'					=> 30602,// 手机号码格式错误
						'serviceTel.regex'				=> 30924,
						'name.required' 				=> 30604,
						'name.max' 						=> 30922,
						'contacts.max' 					=> 30923,
						'pwd.max' 						=> 30921,
						'provinceId.min' 				=> 30607,
						'cityId.min' 					=> 30609,
						'areaId.min' 					=> 30611,
						'address.required' 				=> 30613,
//						'idcardSn.required'           	=> 10114,
//			            'idcardSn.regex'                => 10115,
//			            'idcardPositiveImg.required'    => 10116,
//			            'idcardNegativeImg.required'    => 10117,
			            'mapPoint.required'             => 30614,   // 请选择地图定位
			            // 'mapPos.required'               => 30616,    // 请选择服务范围
			            'logo.required'                 => 30633,
			            'serviceFee.max'     			=> 10617,
			            'brief.max' 					=> 30923,
			            'deliveryFee.max'    			=> 10618,
			            'storeType.required'            => 30931,   // 请选择店铺类型
		);

		$validator = Validator::make([
									'mobile' 				=> $mobile,
									'name' 					=> $name,
									'provinceId' 			=> $provinceId,
									'cityId' 				=> $cityId,
									'areaId' 				=> $areaId,
									'address' 				=> $address,
//									'idcardSn'              => $idcardSn,
//					                'idcardPositiveImg'     => $idcardPositiveImg,
//					                'idcardNegativeImg'     => $idcardNegativeImg,
					                'mapPoint'              => $mapPoint,
					                'mapPos'                => $mapPos,
					                'logo'                	=> $logo,
					                'serviceFee' 			=> $serviceFee,
					                'deliveryFee'			=> $deliveryFee,
					                'contacts' 				=> $contacts,
					                'serviceTel'			=> $serviceTel,
					                'brief'					=> $brief,
					                'storeType'             => $storeType,
									  ], $rules, $messages);

		//验证信息
		if ($validator->fails()) {
			$messages       = $validator->messages();
			$result['code'] = $messages->first();

			return $result;
		}

		//全国店验证退货地址等参数
        //周边店验证服务范围
        if($storeType == 1)
        {
            $refundAddress = trim($refundAddress);
            if( empty($refundAddress) )
            {
                $result['code'] = 30632; //全国店务必填写退货地址
                return $result;
            }
            
        }
        else
        {
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

	        //如果设置了满减 存入满减金额 如果没有设置 清空满减金额
			$isAvoidFee = $isAvoidFee == 1 ? $isAvoidFee : 0;
			$avoidFee = $isAvoidFee == 1 ? $avoidFee : null;
	    }
//        $resu = parent::isCreditNo($idcardSn);
//        if(!$resu){
//            $result['code'] = 10115;    // 身份证号格式错误
//            return $result;
//        }

		if ($id > 0) {
			$seller = Seller::find($id);

			if (!$seller) {//服务站不存在
				$result['code'] = 30211;

				return $result;
			}

			if($seller->status == 1){
				$result['code'] = 30943;

				return $result;
			}
		} else {
			$seller = new Seller();
			$seller->type = $type;
		}

		

		$seller_id = (int)Seller::where("mobile", $mobile)->pluck('id');
		if ($seller_id > 0 && $id != $seller_id) {
			$result['code'] = 30603;    // 手机号码已被注册
			return $result;
		}

		DB::beginTransaction();
		try { 
        	if($proxy->pid){
				$parentProxy = Proxy::find($proxy->pid);
        	}
			switch ($proxy->level) {
				case '2':
					$seller->first_level = $proxy->pid;
					$seller->second_level = $proxy->id; 
					$seller->third_level = 0;
					break;
				case '3':
					$seller->first_level = $parentProxy->pid;
					$seller->second_level = $parentProxy->id;
					$seller->third_level = $proxy->id;
					break;
				
				default:
					$seller->first_level = $proxy->id;
					$seller->second_level = 0;
					$seller->third_level = 0;
					break;
			}
			//查找手机号码对应的会员编号
			$mobile_user_id = (int)User::where('mobile', $mobile)->pluck('id');
			//要更新会员的信息
			$update_user_info = [];
			
			if ($mobile_user_id > 0) {
                //会员存在且在员工表也同时存在
                $staff_check = (int)SellerStaff::where('user_id', $mobile_user_id)->where('seller_id', '!=', (int)$seller->id)->pluck('id');
                if ($staff_check > 0) {
                    $result['code'] = 50215;    // 手机号码已被注册
                    return $result;
                }
            }
            
			//当为修改服务站时
			if ($id > 0) {
				//如果修改手机号码
				$seller_old_user_id = $seller->user_id;
				if ($seller->mobile != $mobile) {
					//如果被注册,则更新服务人员关联的会员编号
					if ($mobile_user_id > 0) {
						$seller->user_id = $mobile_user_id;
					} else {//如果未被注册,则修改关联会员的手机号码
						$update_user_info['mobile'] = $mobile;
					}
				}

				if (!empty($pwd)) {//当为修改密码时
					$crypt = String::randString(6);
					$pwd   = md5(md5($pwd) . $crypt);

					$update_user_info['crypt'] = $crypt;
					$update_user_info['pwd']   = $pwd;
				}
			} else {//当为新增服务人员时
				if ($mobile_user_id < 1) {//当手机号没有被注册时,创建会员
					if (empty($pwd)) {
						$result['code'] = 30629;

						return $result;
					}

					$user                = new User();
					$user->mobile        = $mobile;
					$user->name_match    = String::strToUnicode($name, '+');
					$user->name          = $name;
					$user->reg_time      = UTC_TIME;
					$user->reg_ip        = CLIENT_IP;
					$user->province_id   = $provinceId;
					$user->city_id       = $cityId;
					$user->area_id       = $areaId;
					$user->is_sms_verify = 1;
					$user->save();
					$seller->user_id = $user->id;
				} else {
                    $seller->user_id = $mobile_user_id;
                }

				if (!empty($pwd)) {
					$crypt                     = String::randString(6);
					$pwd                       = md5(md5($pwd) . $crypt);
					$update_user_info['crypt'] = $crypt;
					$update_user_info['pwd']   = $pwd;
				}

                $seller->create_time     = UTC_TIME;
                $seller->create_day      = UTC_DAY;
			}

			//当会员数据有更新项时
			if (count($update_user_info) > 0) {
				User::where('id', $seller->user_id)->update($update_user_info);
			}
            $deduct =  (double)$deduct;
            $serviceFee =  (double)$serviceFee;
            $deliveryFee =  (double)$deliveryFee;
            if($deduct > 100){
                $result['code'] = 80220; //佣金比例只能是 0% ~ 100% 范围
                return $result;
            }

			$seller->mobile          = $mobile;
			$seller->name            = $name;
			$seller->name_match      = String::strToUnicode($name . $mobile);
            $seller->contacts        = $contacts;
			$seller->address         = $address;
			$seller->province_id     = $provinceId;
			$seller->city_id         = $cityId;
			$seller->area_id         = $areaId;
			$seller->brief           = $brief;
			$seller->service_fee 	 = $serviceFee;
			$seller->delivery_fee    = $deliveryFee;
			$seller->is_avoid_fee    = $isAvoidFee;
			$seller->avoid_fee    	 = $avoidFee;
			$seller->is_authenticate = (int)$isAuthenticate;
			$seller->service_tel 	 = $serviceTel;
			$seller->map_point_str   = !empty($mapPoint) ? $mapPoint : null;
            $seller->map_pos_str     = !empty($mapPos["str"]) ? $mapPos["str"] : null;
            $seller->map_point       = !empty($mapPoint) ? DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')") : null;
            $seller->map_pos         = !empty($mapPos["pos"]) ? DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')") : null;
            $seller->is_check 		 = 0;
            $seller->deduct 		 = $deduct;
            $seller->is_cash_on_delivery 	 = $isCashOnDelivery;
            $seller->store_type      = $storeType;
            $seller->refund_address  = $refundAddress;
			$seller->save();
			
			$logo = self::moveSellerImage($seller->id, $logo);
            if (!$logo) {//转移图片失败
                $result['code'] = 10202;
                return $result;
            }
            if (!empty($image)) {
            	$image = self::moveSellerImage($seller->id, $image);
	            if (!$image) {//转移图片失败
	                $result['code'] = 10202;
	                return $result;
	            }
            }
            $seller->image = $image;
            $seller->logo = $logo;
            $seller->save();

            //员工获取商家关联的会员id
           	$staffUserId = $seller->user_id;
            // if($seller->type == Seller::SELF_ORGANIZATION){
                 //如果个人加盟版 则保存至员工表
	           	//个人加盟、商家加盟都生成一个员工
	       		if ($id < 1) {
	            	$staff = new SellerStaff();
	            	$staff->seller_id 		   = $seller->id;
					$staff->create_time        = UTC_TIME;
	            	$staff->create_day         = UTC_DAY;
	            	if($seller->type == Seller::SELF_ORGANIZATION) {
	                    $staff->type           = 0;
	                } else {
	                    $staff->type           = 3;
	                }
	            } else {
	            	$staff = SellerStaff::where('seller_id', $seller->id)->where('user_id', $seller_old_user_id)->first();
	            }
	           	// var_dump($staff);
	           	// exit;
	            $staff->user_id 		   = $staffUserId;
	            $staff->avatar             = $logo;
	            $staff->mobile             = $mobile;
	            $staff->name               = $name;
	            $staff->name_match         = String::strToUnicode($name.$mobile);
	            $staff->address            = $address;
	            // $staff->map_point          = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");
	            // $staff->map_point_str      = $mapPoint;
	            $staff->province_id        = $provinceId;
	            $staff->city_id            = $cityId;
	            $staff->area_id            = (int)$areaId;
	            $staff->brief              = $brief;
                $staff->order_status       = 1;
	            $staff->save();

	            //保存员工扩展信息
	            $staffextend = SellerStaffExtend::where('staff_id', $staff->id)->where('seller_id', $seller->id)->first();
	            if (!$staffextend) {
	            	$sellerStaffExtend = new SellerStaffExtend();
	                $sellerStaffExtend->staff_id = $staff->id;
	                $sellerStaffExtend->seller_id = $seller->id;;
	                $sellerStaffExtend->save();
	        	}

            // }
            //保存扩展信息
            $sellerextend = SellerExtend::where('seller_id', $seller->id)->first();
            if (!$sellerextend) {
	            $sellerExtend = new SellerExtend();
	            $sellerExtend->seller_id = $seller->id;;
	            $sellerExtend->save();
	        }

            SellerCateRelated::where('seller_id', $seller->id)->delete();
            $cateIds = is_array($cateIds) ? $cateIds : explode(',', $cateIds);
            //全国店需要排除的服务编号
            if($storeType == 1)
            {
                $notCate = SellerCate::where("type", 2)->lists('id');
            }
            foreach ($cateIds as $key => $value) {
            	if($storeType == 1 && in_array($value, $notCate) )
                {
                    continue;
                }
            	$cate = new SellerCateRelated();
            	$cate->seller_id = $seller->id;
            	$cate->cate_id = $value;
            	$cate->save();
            }

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
            	
            DB::commit();

			// $sellerMap = new SellerMap();
           //  $sellerMap->seller_id       = $seller->id;
           //  $sellerMap->map_point       = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");
           //  $sellerMap->map_pos         = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
           //  $sellerMap->save();
			
			//周边店保存地理信息
            $addMapArr['map_point'] = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");

            if($storeType == 1)
            {
            	//全国店无范围,默认范围值 无效的 只为填充数据
                $mapPos = '31.90797991052 102.20781720873,31.913517218413 102.2418346142,31.894941463557 102.25701865688,31.887726973701 102.23846511045,31.887216933335 102.21802328888,31.90797991052 102.20781720873';
                $addMapArr['map_pos'] = DB::raw("GeomFromText('Polygon((". $mapPos ."))')");
            }
            else
            {
                $addMapArr['map_pos'] = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
            }

            if ($id > 0) {
                SellerMap::where("seller_id", $seller->id)->update($addMapArr);
            } else {
                $addMapArr['seller_id'] = $seller->id;
                SellerMap::insert($addMapArr);
            }

		} catch (Exception $e) {
			DB::rollback();
			$result['code'] = 99999;
		}

		return $result;
	}

	/**
	 * 获取服务人员
	 * @param  int $id 服务人员id
	 * @return array   服务人员
	 */
	public static function getSystemSellerById($proxy, $id) {
		$seller = Seller::where('id', $id) ;

		switch ($proxy->level) {
			case '2':
				$seller->where('first_level', $proxy->pid)
					 ->where('second_level', $proxy->id);
				break;
			case '3':
				$parentProxy = Proxy::find($proxy->pid);
				$seller->where('first_level', $parentProxy->pid)
					 ->where('second_level', $parentProxy->id)
					 ->where('third_level', $proxy->id);
				break;
			
			default:
				$seller->where('first_level', $proxy->id);
				break;
		}

		$seller = $seller->with('province', 'city', 'area', 'authenticate', 'banks', 'sellerCate.cates', 'deliveryTimes', 'district', 'first', 'second', 'third')
					 	 ->first();

		return $seller;
	}

	/**
	 * 删除服务人员
	 * @param int $id 服务人员id
	 * @return array   删除结果
	 */
	public static function deleteSystem($id) {
		$result = ['code' => 0,
				   'data' => null,
				   'msg' => ""
		];

		$seller = Seller::find($id);
        
		if (!$seller) {
			$result['code'] = 30630;
            return $result;
		}

		DB::beginTransaction();
        try {
            //删除机构或个人
            Seller::destroy($id);
            //删除员工
            \YiZan\Models\SellerStaff::where('seller_id', $id)->delete();
            //删除银行
            \YiZan\Models\SellerBank::where('seller_id', $id)->delete();
            //删除扩展
            \YiZan\Models\SellerExtend::where('seller_id', $id)->delete();
            //删除资金日志
            \YiZan\Models\SellerMoneyLog::where('seller_id', $id)->delete();
            //删除认证信息
            \YiZan\Models\SellerAuthenticate::where('seller_id', $id)->delete();
            //删除员工扩展信息
            \YiZan\Models\SellerStaffExtend::where('seller_id', $id)->delete();
           // 删除员工预约信息
            \YiZan\Models\SellerCateRelated::where('seller_id', $id)->delete();
            // 删除配送时间
            \YiZan\Models\SellerDeliveryTime::where('seller_id', $id)->delete();
           //删除提现
           \YiZan\Models\SellerWithdrawMoney::where('seller_id', $id)->delete();
           //删除员工请假
           \YiZan\Models\StaffLeave::where('seller_id', $id)->delete();
           //删除服务时间
           \YiZan\Models\StaffServiceTime::where('seller_id', $id)->delete();
           \YiZan\Models\StaffServiceTimeSet::where('seller_id', $id)->delete();

            //删除服务商品相关
            \YiZan\Models\Goods::where('seller_id', $id)->delete();
            \YiZan\Models\GoodsCate::where('seller_id', $id)->delete();
            \YiZan\Models\GoodsExtend::where('seller_id', $id)->delete();
            \YiZan\Models\GoodsNorms::where('seller_id', $id)->delete();
            \YiZan\Models\GoodsStaff::where('seller_id', $id)->delete();

            //删除物业相关
            \YiZan\Models\PropertyBuilding::where('seller_id', $id)->delete();
            \YiZan\Models\PropertyRoom::where('seller_id', $id)->delete();
            $puserId = \YiZan\Models\PropertyUser::where('seller_id', $id)->pluck('id');
            \YiZan\Models\PuserDoor::where('puser_id', $id)->delete();
            \YiZan\Models\DoorOpenLog::where('seller_id', $id)->delete();
            \YiZan\Models\PropertyUser::where('seller_id', $id)->delete();
            \YiZan\Models\District::where('seller_id', $id)->delete();


            DB::commit();

            //删除地图
           \YiZan\Models\SellerMap::where('seller_id', $id)->delete();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }

		return $result;
	}

	/**
	 * 查询所有的服务站
	 */
	public static function all() {
		$result = [
					'code' => 0,
				   'data' => null,
				   'msg' => ""
				];

		$result['data'] = Seller::all();
		return $result;
	}

	public static function updateCheckStatus($id, $status, $content, $deduct) {

		$result = [
					'code' => 0,
				   'data' => null,
				   'msg' => ""
				];

		$seller = Seller::find($id);
		if (!$seller) {
			$result['code'] = 30630;
            return $result;
		}

		if($status == 1){
			$model = 'seller.reg.success';
		} else if($status == -1){
			if (empty($content)) {
				$result['code'] = 40105;
            	return $result;
			}
			$model = 'seller.reg.reject';
		}

		Seller::where('id', $id)->update(['is_check'=>$status, 'check_val'=> $content, 'deduct'=> $deduct]);

		if($status != 0){  
			PushMessageService::notice($seller->user_id, $seller->mobile, $model, '', ['sms','app'], 'seller', 1, 0, '');
		} 

		return $result;
	}

	/**
	 * 更新商家状态
	 */
	public static function updateStatus($id, $status, $field) {
		$result = [
					'code' => 0,
				   'data' => null,
				   'msg' => ""
				];

		$seller = Seller::find($id);
		if (!$seller) {
			$result['code'] = 30630;
            return $result;
		}

		Seller::where('id', $id)->update([$field=>$status]);
		return $result;
	}

	public static function updateProStatus($id, $isCheck, $content) {
		$result = [
			'code' => 0,
		   	'data' => null,
		   	'msg' => ""
		];

		$seller = Seller::find($id);
		if (!$seller) {
			$result['code'] = 30630;
            return $result;
		}

		if($isCheck == -1){
			if (empty($content)) {
				$result['code'] = 40105;
            	return $result;
			}
		}

		Seller::where('id', $id)->update(['is_check'=>$isCheck, 'check_val'=> $content]);

		return $result;
	}

	public static function updateBankInfo($sellerId, $bank, $bankNo, $mobile, $name,$verifyCode){  
	   
		$result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
		);

		$rules = array( 
			'bank'          => ['required'],
			'bank_no'       => ['required'], 
		    'mobile' 	 	=> ['required','mobile'],
		    'name' 	 		=> ['required'],
		    'verifyCode' 	=> ['required','max:6']
		);
		$messages = array(
		    'bank.required'			=> 10150,	// 请输入银行 
            'bank_no.required'	    => 10151,	// 请输入银行卡号
		    'mobile.required'		=> 10101,
		    'mobile.mobile'			=> 10156,
		    'name.required' 		=> 30604,
		    'verifyCode.required' 	=> 10155,
		    'verifyCode.max'        => 10154
        );
		$validata = array(
		    'seller_id' => $sellerId,
		    'bank' 		=> $bank,
		    'bank_no'   => $bankNo,
		    'mobile'	=> $mobile,
		    'name'		=> $name,
		    'verifyCode'=> $verifyCode
		);
		$validator = Validator::make($validata, $rules, $messages);
		
	    //验证信息
	    if ($validator->fails()) {
	        $messages       = $validator->messages();
	        $result['code'] = $messages->first();
	    
	        return $result;
	    }
	    //检测验证码
	    $verifyCodeId = UserService::checkVerifyCode($verifyCode, $mobile, UserVerifyCode::TYPE_BANKINFO);
	    if (!$verifyCodeId) {
	        $result['code'] = 10153;
	        return $result;
	    }
	    $banks = SellerBank::where('bank_no', $bankNo)->where('name', $name)->first();
	    if ($banks) { //银行卡已存在
	    	$result['code'] = 10106;
	    	return $result;
	    }
		$bankObj = new SellerBank();
    	$bankObj->seller_id 	= $sellerId;
    	$bankObj->bank 			= $bank;
    	$bankObj->bank_no 		= $bankNo;
	    $bankObj->mobile 		= $mobile;
    	$bankObj->name 			= $name;
		$bankObj->save();

		$result['data'] = SellerBank::where('seller_id', $sellerId)->get()->toArray();
        return $result; 
	}

	public static function deleteBankInfo($id, $sellerId) {
		$result = ['code' => 0,
				   'data' => null,
				   'msg' => ""
		];

		$seller = Seller::find($sellerId);
        
		if (!$seller) {
			$result['code'] = 30630;
            return $result;
		}

		SellerBank::where('seller_id', $sellerId)->where('id',$id)->delete();
		
		return $result;
	}

	/**
	 * 获取物业列表
	 */
	public static function getPropertysList($proxy,$name, $districtName, $provinceId, $cityId, $areaId, $status, $isTotal, $page, $pageSize){
		$list = Seller::orderBy('seller.id', 'DESC')
					  ->where('seller.type', 3);//物业

        switch ($proxy->level) {
            case '2':
                $list->where('seller.first_level', $proxy->pid)
                    ->where('seller.second_level', $proxy->id);
                break;
            case '3':
                $parentProxy = Proxy::find($proxy->pid);
                $list->where('seller.first_level', $parentProxy->pid)
                    ->where('seller.second_level', $parentProxy->id)
                    ->where('seller.third_level', $proxy->id);
                break;

            default:
                $list->where('seller.first_level', $proxy->id);
                break;
        }


		if($name == true){
			$list->where('seller.name', $name);
		}

		if($districtName == true || $provinceId == true || $cityId == true || $areaId == true){
			$list->join('district', function($join) use($districtName, $provinceId, $cityId, $areaId){
	            $join->on('district.seller_id', '=', 'seller.id');
	            if($districtName == true){
	            	$join->where('district.name', 'like', "%{$districtName}%");
	            }
	            if($provinceId == true){
	            	$join->where('district.province_id', '=', $provinceId);
	            }
	            if($cityId == true){
	            	$join->where('district.city_id', '=', $cityId);
	            }
	            if($areaId == true){
	            	$join->where('district.area_id', '=', $areaId);
	            }
	        });
		} 

		if($status > 0){ // 审核状态
			$list->where('seller.is_check', $status - 2);
		}
		
    	$totalCount = $list->count();
 		
 		if($isTotal){
	 		$list = $list->with('district')
			             ->get()
			             ->toArray();
 		} else {
	 		$list = $list->select('seller.*')
	 					 ->skip(($page - 1) * $pageSize)
			             ->take($pageSize)
			             ->with('district')
			             ->get()
			             ->toArray();
 		} 
    	return ["list"=>$list, "totalCount"=>$totalCount];
	}

	/**
	 * 保存物业信息
	 */
	public static function createProperty($proxy, $id = 0, $name, $mobile, $pwd, $contacts, $districtId, $serviceTel, $idcardSn, $idcardPositiveImg, $idcardNegativeImg, $businessLicenceImg){
		$pwd = strval($pwd);
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.create_property')
        );

        $rules = array(
            'mobile'                        => ['required','regex:/^1[0-9]{10}$/'], 
           // 'pwd'                           => ['required','min:6','max:20'],
            'name'                          => ['required'],
            'contacts'                    	=> ['required'],
            'districtId'                    => ['required'],
            'idcardSn'                      => ['required','regex:/^[0-9]{18}|[0-9]{15}|[0-9]{17}[xX]{1}$/'],
            'idcardPositiveImg'             => ['required'],
            'idcardNegativeImg'             => ['required'],
            'businessLicenceImg'            => ['required'],
        );

        $messages = array(
            'mobile.required'               => '10101',
            'mobile.regex'                  => '60009',
          //  'pwd.required'                  => '10105',
          //  'pwd.min'                       => '10106',
          //  'pwd.max'                       => '10106',
            'name.required'                 => '30023',
            'contacts.required'             => '30024',
            'districtId.min'                => '30025',
            'idcardSn.required'             => '10114',
            'idcardSn.regex'                => '10115',
            'idcardPositiveImg.required'    => '10116',
            'idcardNegativeImg.required'    => '10117',
            'businessLicenceImg.required'   => '10207',
        );

        $validator = Validator::make([
                'mobile'                => $mobile, 
               // 'pwd'                   => $pwd,
                'name'                  => $name,
                'contacts'              => $contacts,
                'provinceId'            => $provinceId,
                'cityId'                => $cityId,
                'areaId'                => $areaId,
                'districtId'            => $districtId,
                'idcardSn'              => $idcardSn,
                'idcardPositiveImg'     => $idcardPositiveImg,
                'idcardNegativeImg'     => $idcardNegativeImg, 
                'businessLicenceImg'    => $idcardNegativeImg, 
            ], $rules, $messages);
        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        if ($id > 0) {
			$seller = Seller::find($id);
			if (!$seller) {//服务站不存在
				$result['code'] = 30211;
				return $result;
			}
		} else {
			$seller = new Seller();
			$seller->type = 3;
		}

		$seller_id = (int)Seller::where("mobile", $mobile)->pluck('id');
		if ($seller_id > 0 && $id != $seller_id) {
			$result['code'] = 30603;    // 手机号码已被注册
			return $result;
		}


        if(!(preg_match("/^1[0-9]{10}$/", $serviceTel)) && !(preg_match("(^((\d{11,12})|(\d{7,8})|(\d{4}|\d{3})-(\d{7,8}))$)", $serviceTel))){
            $result['code'] = 30022;
            return $result;
        }

        DB::beginTransaction();
        try {

            if($proxy->pid){
				$parentProxy = Proxy::find($proxy->pid);
        	}
			switch ($proxy->level) {
				case '2':
					$seller->first_level = $proxy->pid;
					$seller->second_level = $proxy->id; 
					break;
				case '3':
					$seller->first_level = $parentProxy->pid;
					$seller->second_level = $parentProxy->id;
					$seller->third_level = $proxy->id;
					break;
				default:
					$seller->first_level = $proxy->id;
					break;
			}

            //查找手机号码对应的会员编号
			$mobile_user_id = (int)User::where('mobile', $mobile)->pluck('id');
			//要更新会员的信息
			$update_user_info = [];
			//当为修改服务站时
			if ($id > 0) {
				//如果修改手机号码
				if ($seller->mobile != $mobile) {
					//如果被注册,则更新服务人员关联的会员编号
					if ($mobile_user_id > 0) {
						$seller->user_id = $mobile_user_id;
					} else {//如果未被注册,则修改关联会员的手机号码
						$update_user_info['mobile'] = $mobile;
					}
				}

				if (!empty($pwd)) {//当为修改密码时
					$crypt = String::randString(6);
					$pwd   = md5(md5($pwd) . $crypt);

					$update_user_info['crypt'] = $crypt;
					$update_user_info['pwd']   = $pwd;
				}
			} else {//当为新增服务人员时
				if ($mobile_user_id < 1) {//当手机号没有被注册时,创建会员
					if (empty($pwd)) {
						$result['code'] = 30629;
						return $result;
					}

					$user = new User();
					$user->mobile        = $mobile;
					$user->name_match    = String::strToUnicode($name . $mobile);
					$user->name          = $name;
					$user->reg_time      = UTC_TIME;
					$user->reg_ip        = CLIENT_IP;
					$user->province_id   = $provinceId;
					$user->city_id       = $cityId;
					$user->area_id       = $areaId;
					$user->is_sms_verify = 1;
					$user->save();
					$seller->user_id = $user->id;
				} else {
                    $seller->user_id = $mobile_user_id;
                }

				if (!empty($pwd)) {
					$crypt                     = String::randString(6);
					$pwd                       = md5(md5($pwd) . $crypt);
					$update_user_info['crypt'] = $crypt;
					$update_user_info['pwd']   = $pwd;
				}

                $seller->create_time     = UTC_TIME;
                $seller->create_day      = UTC_DAY;
			}

			//当会员数据有更新项时
			if (count($update_user_info) > 0) {
				User::where('id', $seller->user_id)->update($update_user_info);
			}

            //$seller->type             = 3;//物业
           // $seller->user_id          = $user->id;
            $seller->mobile           = $mobile;
            $seller->name             = $name; 
            $seller->name_match       = String::strToUnicode($name . $mobile);
            $seller->service_tel 	  = $serviceTel;
            //$seller->district_id 	  = $districtId;
            // $seller->map_pos          = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
            // $seller->create_time      = UTC_TIME;
            // $seller->create_day       = UTC_DAY;
            $seller->contacts         = $contacts;
            $seller->is_check 		  = 0;
           // $seller->status 		  = 1;
            $seller->save();   

            //如果是添加多个小区
	        if(is_array($districtId)){
	        	foreach ($districtId as $did) {

	        		$district = District::find($districtId);

					if(empty($district)){
						$result['code'] = 30027;
						return $result;
					}

					$district->seller_id = $seller->id;
					$district->save();
	        	} 
	        } else { 
				$district = District::find($districtId);

				if(empty($district)){
					$result['code'] = 30027;
					return $result;
				}

				$district->seller_id = $seller->id;
				$district->save();
	        }

	        //保存小区的地址信息到物业信息
            $seller->address          = $district->address;
            $seller->map_point_str    = $district->map_point_str; 
            $seller->map_point        = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', str_replace(',', ' ', $district->map_point_str)) . ")')");
            $seller->province_id      = $district->province_id;
            $seller->city_id          = $district->city_id;
            $seller->area_id          = $district->area_id; 
            $seller->save(); 

            $seller_idcard = SellerAuthenticate::where('idcard_sn', $idcardSn)->where('seller_id', '!=', $seller->id)->first();
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
           
            if($businessLicenceImg == false)
            {
                $result['code'] = 10207; // 公司营业执照相片不能为空
                return $result;
            }
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
            
            if ($id > 0) {
            	$auth = SellerAuthenticate::where('seller_id', $id)->first();
            } else {
            	$auth = new SellerAuthenticate();
            	$auth->seller_id            = $seller->id;
            }
            $auth->idcard_sn            = $idcardSn;
           // $auth->real_name            = $name;
            $auth->idcard_positive_img  = $idcardPositiveImg;
            $auth->idcard_negative_img  = $idcardNegativeImg; 
           // $auth->business_licence_sn  = $businessLicenceSn;
            $auth->business_licence_img = $businessLicenceImg;
            $auth->update_time          = UTC_TIME;
            $auth->save();
  			
  			//更新所有在小区关联物业公司之前的业主
  			PropertyUser::where('district_id', $districtId)->update(['seller_id'=>$seller->id]);
            DB::commit(); 
            $result['data'] = Seller::find($seller->id);
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 10119;
        }
        return $result; 
	}

	/**
	 * 更新物业信息
	 */
	public static function updateProperty($id, $name, $mobile, $pwd, $contacts, $districtId, $serviceTel, $idcardSn, $idcardPositiveImg, $idcardNegativeImg, $businessLicenceImg, $proxyId){
		$pwd = strval($pwd);
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.update_property')
        );

        $rules = array(
            'name'                          => ['required','min:2','max:10'],
            'contacts'                    	=> ['required'],  
            'districtId'                    => ['required'],   
            'idcardSn'                      => ['required','regex:/^[0-9]{18}|[0-9]{15}|[0-9]{17}[xX]{1}$/'],
            'idcardPositiveImg'             => ['required'],
            'idcardNegativeImg'             => ['required'], 
            'businessLicenceImg'            => ['required'], 
        );

        $messages = array( 
            'name.required'                 => '30023',  
            'contacts.required'             => '30023',
            'districtId.min'                => '30025',
            'idcardSn.required'             => '10114',
            'idcardSn.regex'                => '10115',
            'idcardPositiveImg.required'    => '10116',
            'idcardNegativeImg.required'    => '10117',
            'businessLicenceImg.required'   => '10207',
        );

        $validator = Validator::make([
                'mobile'                => $mobile, 
                'pwd'                   => $pwd,
                'name'                  => $name,
                'contacts'              => $contacts, 
                'districtId'            => $districtId,
                'idcardSn'              => $idcardSn,
                'idcardPositiveImg'     => $idcardPositiveImg,
                'idcardNegativeImg'     => $idcardNegativeImg, 
                'businessLicenceImg'     => $idcardNegativeImg, 
            ], $rules, $messages);
        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        preg_match('/^1[0-9]{10}$/', $serviceTel, $arr );

        if($serviceTel == true && empty($arr) ){
            $result['code'] = 30022;
            return $result;
        }
		
        $seller = Seller::find($id); 
        if (!$seller) {//服务人员不存在
            $result['code'] = 30026;
            return $result;
        } 

        $district = District::find($districtId); 

		if(empty($district)){
			$result['code'] = 30027;
			return $result;
		}
		//如果传入代理信息，则设置代理数据
		if($proxyId > 0) {
			$proxy = Proxy::where('id', $proxyId)
						  ->with('parentProxy')
						  ->first();
		    if(empty($proxy)){
		    	$result['code'] = 30942;
		    	return $result;
		    }

			switch ($proxy->level) {
				case '2':
					$seller->first_level = $proxy->pid;
					$seller->second_level = $proxy->id; 
					$seller->third_level = 0;
					break;
				case '3':
					$seller->first_level = $proxy->parentProxy['pid'];
					$seller->second_level = $proxy->parentProxy['id'];
					$seller->third_level = $proxy->id;
					break;
				
				default:
					$seller->first_level = $proxy->id;
					$seller->second_level = 0;
					$seller->third_level = 0;
					break;
			}
		}

		$seller->mobile 		  = $mobile;
        $seller->name             = $name; 
        $seller->address          = $district->address; 
        $seller->service_tel 	  = $serviceTel; 
        // $seller->map_pos          = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
        $seller->create_time      = UTC_TIME;
        $seller->create_day       = UTC_DAY; 
        $seller->contacts         = $contacts; 
        // if($status != 0){
        // 	$seller->status 	  = $status;
        // } 


        //如果是添加多个小区
        if(is_array($districtId)){
        	foreach ($districtId as $did) {

        		$district = District::find($districtId);

				if(empty($district)){
					$result['code'] = 30027;
					return $result;
				}

				$district->seller_id = $seller->id;
        	} 
        } else { 
			$district = District::find($districtId);

			if(empty($district)){
				$result['code'] = 30027;
				return $result;
			}

			$district->seller_id = $seller->id;
        }

        //保存小区的地址信息到物业信息
        $seller->address          = $district->address;
        $seller->map_point_str    = $district->map_point_str; 
        $seller->map_point        = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', str_replace(',', ' ', $district->map_point_str)) . ")')");
        $seller->province_id      = $district->province_id;
        $seller->city_id          = $district->city_id;
        $seller->area_id          = $district->area_id;  

        $seller_idcard = SellerAuthenticate::where('idcard_sn', $idcardSn)->where('seller_id','!=', $id)->first();
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
       
        if($businessLicenceImg == false)
        {
            $result['code'] = 10207; // 公司营业执照相片不能为空
            return $result;
        }

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

        if ($seller->save()) { 
            $auth = SellerAuthenticate::where('seller_id', $seller->id)->first(); 
            $auth->idcard_sn            = $idcardSn;
           // $auth->real_name            = $name;
            $auth->idcard_positive_img  = $idcardPositiveImg;
            $auth->idcard_negative_img  = $idcardNegativeImg; 
           // $auth->business_licence_sn  = $businessLicenceSn;
            $auth->business_licence_img = $businessLicenceImg;
            $auth->update_time          = UTC_TIME;
            $auth->save();
            return $result;
        } else {
            $result['code'] = 99999;
            return $result;
        }

        return $result; 
	}


	/**
	 * 商家营业统计列表
	 */
	public static function getBusinessStatisticsList($proxy, $name, $month, $year, $cityId, $page, $pageSize){  
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
		if($name){ 
			$sellerIds = Seller::where('mobile', 'like', "%$name%");
			$totalCount = Seller::where('mobile', 'like', "%$name%");

            if($data['firstLevel'] > 0){
                $sellerIds->where('first_level', '=', $data['firstLevel']);
                $totalCount->where('first_level', '=', $data['firstLevel']);
            }

            if($data['second_level'] > 0){
                $sellerIds->where('second_level', '=', $data['secondLevel']);
                $totalCount->where('second_level', '=', $data['secondLevel']);
            }

            if($data['third_level'] > 0){
                $sellerIds->where('third_level', '=', $data['thirdLevel']);
                $totalCount->where('third_level', '=', $data['thirdLevel']);
            } 

            $sellerIds = $sellerIds->lists('id');   

			$sellerIds = implode(',', $sellerIds);

			$totalCount = $totalCount->count();  
			if(empty($sellerIds) || $sellerIds == ''){
				return ["list" => [], "totalCount" => 0, "sum" => []];
			}
		} else { 
			$totalCount = Seller::orderBy('id', 'DESC');  

            if($data['firstLevel'] > 0){ 
                $totalCount->where('first_level', '=', $data['firstLevel']);
            }

            if($data['second_level'] > 0){ 
                $totalCount->where('second_level', '=', $data['secondLevel']);
            }

            if($data['third_level'] > 0){ 
                $totalCount->where('third_level', '=', $data['thirdLevel']);
            } 

            $totalCount = $totalCount->count(); 
		}

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
	/***
	SELECT 
	IFNULL(sum(total_fee), 0) AS totalPayfee,
	count(id) AS totalNum,
	IFNULL(sum(drawn_fee), 0) AS totalDrawnfee,
	SUM(IF(pay_type = 'cashOnDelivery', pay_fee, 0)) AS totalOnline, 
	SUM(IF(pay_type <> 'cashOnDelivery', pay_fee, 0)) AS totalCash,
  IFNULL(SUM(IF(discount_fee > total_fee, total_fee, discount_fee)),0) AS totalDiscountFee
FROM yz_order 
WHERE pay_status = 1
AND yz_order.first_level = 18
AND (
	STATUS IN (201, 202)
	OR (
		STATUS = 500
		AND buyer_finish_time > 0
		AND cancel_time IS NULL
	)
	OR (
		STATUS = 501
		AND auto_finish_time > 0
		AND cancel_time IS NULL
	)
	OR (
		STATUS = 502
		AND auto_finish_time > 0
		AND cancel_time IS NULL
	)
)
AND FROM_UNIXTIME(
	yz_order.create_time,
	'%Y-%m'
) = '2016-05'
	*/
		$startTime = Time::toTime($year.'-'.$month.'-01');
		$endTime = strtotime('');
		$endTime = strtotime("+1months", $startTime);  
		$sumsql = "SELECT 
					IFNULL(sum(pay_fee + system_full_subsidy + activity_new_money + discount_fee + integral_fee), 0) AS totalPayfee,
					count(id) AS totalNum,
					IFNULL(sum(drawn_fee), 0) AS totalDrawnfee,
					IFNULL(SUM(IF(pay_type <> 'cashOnDelivery', pay_fee, 0)), 0) AS totalOnline, 
					IFNULL(SUM(IF(pay_type = 'cashOnDelivery', pay_fee, 0)), 0) AS totalCash,
					IFNULL(SUM(IF(discount_fee > total_fee, total_fee, discount_fee)),0) AS totalDiscountFee,
					IFNULL(sum(integral_fee), 0) as totalIntegralFee,
						IFNULL(sum(system_full_subsidy), 0) as systemFullSubsidy,
                        IFNULL(sum(seller_full_subsidy), 0) as sellerFullSubsidy,
                        IFNULL(sum(activity_goods_money), 0) as activityGoodsMoney,
                        IFNULL(sum(activity_new_money), 0) as activityNewMoney
					FROM ".$prefix."order
					WHERE pay_status = 1 
					".($data['firstLevel'] > 0 ? "AND ".$prefix."order.first_level = ".$data['firstLevel']." " : " ")."
					".($data['secondLevel'] > 0 ? "AND ".$prefix."order.second_level = ".$data['secondLevel']." " : " ")."
					".($data['thirdLevel'] > 0 ? "AND ".$prefix."order.third_level = ".$data['thirdLevel']." " : " ")."
					". ($name ? " AND seller_id in ($sellerIds)" : '') ."
					AND (status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.")
					OR (status = ".ORDER_STATUS_USER_DELETE." AND buyer_finish_time > 0 AND cancel_time IS NULL) 
					OR (status = ".ORDER_STATUS_SELLER_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL) 
					OR (status = ".ORDER_STATUS_ADMIN_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL))
					AND create_time BETWEEN ".$startTime." AND ".$endTime; 
 		$sum = DB::select($sumsql);

		/**
		 *
		 SELECT
			yz_seller.id,
			yz_seller. NAME,
			IFNULL(sum(yz_order.total_fee), 0) AS totalPayfee,
			count(yz_order.id) AS totalNum,
			IFNULL(sum(yz_order.drawn_fee), 0) AS totalDrawnfee,  
			SUM(IF(yz_order.pay_type = 'cashOnDelivery', pay_fee, 0)) AS totalOnline, 
			SUM(IF(yz_order.pay_type <> 'cashOnDelivery', pay_fee, 0)) AS totalCash,
			IFNULL(
				sum(

					IF (
						discount_fee > total_fee,
						total_fee,
						discount_fee
					)
				),
				0
			) AS totalDiscountFee
		FROM yz_seller 
		LEFT JOIN yz_order ON yz_order.seller_id = yz_seller.id
		AND yz_order.first_level = 18
				AND yz_order.pay_status = 1 
				
		AND FROM_UNIXTIME(
			yz_order.create_time,
			'%Y-%m'
		) = '2016-05'
		AND (
			yz_order. STATUS IN (201, 202)
			OR (
				yz_order. STATUS = 500
				AND yz_order.buyer_finish_time > 0
				AND yz_order.cancel_time IS NULL
			)
			OR (
				yz_order. STATUS = 501
				AND yz_order.auto_finish_time > 0
				AND yz_order.cancel_time IS NULL
			)
			OR (
				yz_order. STATUS = 502
				AND yz_order.auto_finish_time > 0
				AND yz_order.cancel_time IS NULL
			)
		)
		WHERE
			1
		AND yz_seller.first_level = 18
		GROUP BY
			yz_seller.id
		ORDER BY
			totalPayfee DESC
		LIMIT 0,
		 20
		 */
		/**
		 sql说明：左连接order表 查询 id, name, totalPayfee(总金额), totalNum(总数量), totalDrawnfee(总佣金), totalOnline(总在线支付), totalCash(总现金支付), totalDiscountFee(总优惠金额)
		 在线和现金支付数据 通过子查询实现
		 */
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
				WHERE 1 ". ($name ? " AND sl.id in ($sellerIds)" : '') ." ".($data['firstLevel'] > 0 ? " AND sl.first_level = ".$data['firstLevel'] : " ")."
				".($data['secondLevel'] > 0 ? " AND sl.second_level = ".$data['secondLevel'] : " ")."
				".($data['thirdLevel'] > 0 ? " AND sl.third_level = ".$data['thirdLevel'] : " ")." 
                and sl.type in (1,2)
				group by sl.id 
				order by totalPayfee DESC
				limit ".($page - 1) * $pageSize.", ".$pageSize ;  
		$list = DB::select($sql); 
		// print_r(DB::getQueryLog());exit;  
		//file_put_contents('/mnt/test/sq/storage/logs/asdfsdf.log', print_r(DB::getQueryLog(),true)."\n", FILE_APPEND);
		return ["list" => $list, "totalCount" => $totalCount, "sum" => $sum[0]];
	}

	/**
     * 商家月统计
     */
    public static function getBusinessListByMonth($proxy, $sellerId, $month, $year, $cityId){
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

        DB::connection()->enableQueryLog();
        $prefix = DB::getTablePrefix();
        $current = $year.'-'.sprintf("%02d", $month);
        $t = Time::toDate(Time::toTime($current), 't');  
        if($current == Time::toDate(UTC_TIME, 'Y-m')){
            $t = Time::toDate(UTC_TIME, 'd');
        } else if(Time::toTime($current) > Time::toTime(Time::toDate(UTC_DAY, 'Y-m'))){
            return ["list" => [], "sum" => []];
        }
		
		$startTime = Time::toTime($current.'-01');
		$endTime = strtotime("+1months", $startTime);  
        $sumsql =  "SELECT 
						seller_id,
						IFNULL(sum(pay_fee + system_full_subsidy + activity_new_money + discount_fee + integral_fee), 0) AS totalPayfee,
						IFNULL((SUM(IF(pay_type <> 'cashOnDelivery', pay_fee, 0) + integral_fee + IF(discount_fee > total_fee,total_fee,discount_fee) + system_full_subsidy +activity_new_money - drawn_fee)),0) AS totalSellerFee,
						count(id) AS totalNum,
						IFNULL(sum(drawn_fee), 0) AS totalDrawnfee,
						IFNULL(SUM(IF(pay_type <> 'cashOnDelivery', pay_fee, 0)), 0) AS totalOnline, 
						IFNULL(SUM(IF(pay_type = 'cashOnDelivery', pay_fee, 0)), 0) AS totalCash,
						IFNULL(sum(IF (discount_fee > total_fee,total_fee,discount_fee)),0) AS totalDiscountFee,
						IFNULL(sum(integral_fee), 0) as totalIntegralFee,
							IFNULL(sum(system_full_subsidy), 0) as systemFullSubsidy,
	                        IFNULL(sum(seller_full_subsidy), 0) as sellerFullSubsidy,
	                        IFNULL(sum(activity_goods_money), 0) as activityGoodsMoney,
	                        IFNULL(sum(activity_new_money), 0) as activityNewMoney
					FROM ".$prefix."order
					WHERE pay_status = 1  
					".($data['firstLevel'] > 0 ? "AND ".$prefix."order.first_level = ".$data['firstLevel']." " : " ")."
					".($data['secondLevel'] > 0 ? "AND ".$prefix."order.second_level = ".$data['secondLevel']." " : " ")."
					".($data['thirdLevel'] > 0 ? "AND ".$prefix."order.third_level = ".$data['thirdLevel']." " : " ")."
					AND seller_id = ".$sellerId."
					AND (status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.")
					OR (status = ".ORDER_STATUS_USER_DELETE." AND buyer_finish_time > 0 AND cancel_time IS NULL) 
					OR (status = ".ORDER_STATUS_SELLER_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL) 
					OR (status = ".ORDER_STATUS_ADMIN_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL))
					AND create_time BETWEEN ".$startTime." AND ".$endTime."";
        $sum = DB::select($sumsql); 
        $sum['seller'] = Seller::find($sellerId);
        /**
         sql说明：左连接order表 查询 id, name, totalPayfee(总金额), totalNum(总数量), totalDrawnfee(总佣金), totalOnline(总在线支付), totalCash(总现金支付), totalDiscountFee(总优惠金额), totalSellerFee(商家盈利)
         在线和现金支付数据 通过子查询实现
         */
        $sql = "select IFNULL(sum(pay_fee + system_full_subsidy + activity_new_money + discount_fee + integral_fee), 0) as totalPayfee,
				IFNULL((SUM(IF(pay_type <> 'cashOnDelivery', pay_fee, 0) + integral_fee +IF(discount_fee > total_fee,total_fee,discount_fee) + system_full_subsidy +activity_new_money - drawn_fee)),0) AS totalSellerFee,
                count(id) as totalNum,
                IFNULL(sum(drawn_fee), 0) as totalDrawnfee , 
				IFNULL(SUM(IF(pay_type <> 'cashOnDelivery', pay_fee, 0)), 0) AS totalOnline, 
				IFNULL(SUM(IF(pay_type = 'cashOnDelivery', pay_fee, 0)), 0) AS totalCash,
                IFNULL(sum(IF(discount_fee > total_fee, total_fee, discount_fee)), 0) as totalDiscountFee,
				IFNULL(sum(integral_fee), 0) as totalIntegralFee,
					IFNULL(sum(system_full_subsidy), 0) as systemFullSubsidy,
                    IFNULL(sum(seller_full_subsidy), 0) as sellerFullSubsidy,
                    IFNULL(sum(activity_goods_money), 0) as activityGoodsMoney,
                    IFNULL(sum(activity_new_money), 0) as activityNewMoney,
                FROM_UNIXTIME(create_time,'%Y-%m-%d') as daytime
                from ".$prefix."order 
                where seller_id = ".$sellerId." 
                and pay_status = 1   
				".($data['firstLevel'] > 0 ? "AND first_level = ".$data['firstLevel']." " : " ")."
				".($data['secondLevel'] > 0 ? "AND second_level = ".$data['secondLevel']." " : " ")."
				".($data['thirdLevel'] > 0 ? "AND third_level = ".$data['thirdLevel']." " : " ")."
                and create_time BETWEEN ".$startTime." AND ".$endTime."
                AND (status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.")
                OR (status = ".ORDER_STATUS_USER_DELETE." AND buyer_finish_time > 0 AND cancel_time IS NULL) 
                OR (status = ".ORDER_STATUS_SELLER_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL) 
                OR (status = ".ORDER_STATUS_ADMIN_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL))
                GROUP BY FROM_UNIXTIME(create_time,'%Y-%m-%d')"; 
        $queryData = DB::select($sql);  
        $list = [];
        for($i = 1; $i <= $t; $i++) {
            $daytime = $current . '-' . sprintf("%02d", $i);  
            $dayData = [
                'totalPayfee' => 0,
                'totalNum' => 0,
                'totalDrawnfee' => 0,
                'totalSellerFee' => 0,
                'totalOnline' => 0,
                'totalCash' => 0,
                'totalDiscountFee' => 0, 
                'totalIntegralFee' => 0, 
                'daytime' => $daytime,
                'systemFullSubsidy' => 0,
				'sellerFullSubsidy' => 0,
				'activityGoodsMoney' => 0,
				'activityNewMoney' => 0,
            ]; 
            $bool = false;
            foreach ($queryData as $item) { 
                $item = (array)$item;
                if($item['daytime'] == $daytime){
                    $bool = true; 
                    break;
                }
            }
            if($bool){
                $list[] = $item;
            } else {
                $list[] = $dayData;
            }
        }  
        // print_r(DB::getQueryLog());exit;  
        //file_put_contents('/mnt/test/sq/storage/logs/sellerservice1.log', print_r(DB::getQueryLog(),true)."\n", FILE_APPEND);
        return ["list" => $list, "sum" => $sum[0]];
    }

    /**
     * 商家日统计
     */
    public static function getBusinessListByDay($proxy, $sellerId, $day, $sn, $status, $page, $pageSize){   
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
        $prefix = DB::getTablePrefix();   
        $list = Order::where('seller_id', $sellerId);

        if($data['firstLevel'] > 0){
        	$list->where('first_level', $data['firstLevel']);
        }
        if($data['secondLevel'] > 0){
        	$list->where('second_level', $data['secondLevel']);
        }
        if($data['thirdLevel'] > 0){
        	$list->where('third_level', $data['thirdLevel']);
        } 
        
        if($sn == true){
            $list->where('sn', $sn);
        }
        
        $timestr = 'create_time';
        
        if($status == true){
            switch ($status) {
                case '2'://已取消
                    $arrStatus = [ORDER_STATUS_CANCEL_USER, ORDER_STATUS_CANCEL_AUTO, ORDER_STATUS_CANCEL_SELLER, ORDER_STATUS_CANCEL_ADMIN, ORDER_STATUS_REFUND_AUDITING,ORDER_STATUS_CANCEL_REFUNDING,ORDER_STATUS_REFUND_HANDLE,ORDER_STATUS_REFUND_FAIL,ORDER_STATUS_REFUND_SUCCESS,ORDER_REFUND_SELLER_AGREE,ORDER_REFUND_SELLER_REFUSE,ORDER_REFUND_ADMIN_AGREE,ORDER_REFUND_ADMIN_REFUSE,ORDER_STATUS_USER_DELETE,ORDER_STATUS_SELLER_DELETE, ORDER_STATUS_ADMIN_DELETE]; 
                    break;
                case '3'://未完成
                    $arrStatus = [ORDER_STATUS_PAY_SUCCESS,ORDER_STATUS_START_SERVICE,ORDER_STATUS_PAY_DELIVERY,ORDER_STATUS_AFFIRM_SELLER,ORDER_STATUS_FINISH_STAFF]; 
                    break;
                
                default://已完成
                    $arrStatus = [ORDER_STATUS_FINISH_SYSTEM, ORDER_STATUS_FINISH_USER, ORDER_STATUS_USER_DELETE, ORDER_STATUS_SELLER_DELETE, ORDER_STATUS_ADMIN_DELETE];   
                    break;
            }
            $list->whereIn('status', $arrStatus);
        }   
        
        $list->whereRaw("FROM_UNIXTIME(".$timestr.",'%Y-%m-%d') = '".$day."'");

        $totalCount = $list->count(); 

        $list = $list->skip(($page - 1) * $pageSize)
					 ->selectRaw("*, IFNULL(IF(pay_type <> 'cashOnDelivery',pay_fee,0) + integral_fee + system_full_subsidy + activity_new_money + IF(discount_fee > total_fee,total_fee,discount_fee) - drawn_fee,0)  AS totalSellerFee")
                     ->take($pageSize)
                     ->get()
                     ->toArray();

        $sum = Order::where('seller_id', $sellerId);

        if($data['firstLevel'] > 0){
        	$sum->where('first_level', $data['firstLevel']);
        }
        if($data['secondLevel'] > 0){
        	$sum->where('second_level', $data['secondLevel']);
        }
        if($data['thirdLevel'] > 0){
        	$sum->where('third_level', $data['thirdLevel']);
        }  
        $sum = $sum->whereRaw("FROM_UNIXTIME(create_time,'%Y-%m-%d') = '".$day."' AND (status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.") OR (status = ".ORDER_STATUS_USER_DELETE." AND buyer_finish_time > 0 AND cancel_time IS NULL) OR (status = ".ORDER_STATUS_SELLER_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL) OR (status = ".ORDER_STATUS_ADMIN_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL))")
                   ->where('pay_status', 1) 
                   ->selectRaw("seller_id, count(id) as totalNum, IFNULL((SUM(IF(pay_type <> 'cashOnDelivery', pay_fee, 0) + integral_fee +  system_full_subsidy + activity_new_money + IF(discount_fee > total_fee,total_fee,discount_fee) - drawn_fee)),0) AS totalSellerFee") 
                   ->first() ; 
        $sum['seller'] = Seller::find($sellerId);
        return ["list" => $list, "totalCount" => $totalCount, "sum" => $sum];
    }

	/**
	 * 平台信息统计
	 */
	public static function getBusinessPlatformInfo($proxy, $month, $year, $cityId){
		$prefix = DB::getTablePrefix();
        $current = $year.'-'.sprintf("%02d", $month);
		$t = Time::toDate(Time::toTime($current), 't');  
        if($current == Time::toDate(UTC_TIME, 'Y-m')){
            $t = Time::toDate(UTC_TIME, 'd');
        } else if(Time::toTime($current) > Time::toTime(Time::toDate(UTC_DAY, 'Y-m'))){
        	return ["list" => [], "sum" => []];
        }
		$sql = "SELECT daytime,  
				sum(drawnFee) as totalDrawnFee , 
				sum(sellerFee) as totalSellerFee , 
				sum(sellerCharge) as totalSellerCharge , 
				sum(buyerCharge) as totalBuyerCharge 
				FROM ( 
				SELECT 
						FROM_UNIXTIME(create_time,'%Y-%m-%d') as daytime, 
						0 AS regNum,
						SUM(drawn_fee) as drawnFee,
						0 as sellerFee,
						0 as sellerCharge,
						0 as buyerCharge
				from ".$prefix."order 
				where 
				pay_status = 1
				and FROM_UNIXTIME(create_time,'%Y-%m') = '".$current."'
				AND (status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.")
                OR (status = ".ORDER_STATUS_USER_DELETE." AND buyer_finish_time > 0 AND cancel_time IS NULL) 
                OR (status = ".ORDER_STATUS_SELLER_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL) 
                OR (status = ".ORDER_STATUS_ADMIN_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL))
				GROUP BY FROM_UNIXTIME(create_time,'%Y-%m-%d')
				UNION ALL
				SELECT 
						FROM_UNIXTIME(create_time,'%Y-%m-%d') as daytime, 
						0 AS regNum,
						0 as drawnFee,
						SUM(money) as sellerFee,
						0 as sellerCharge,
						0 as buyerCharge
				from ".$prefix."seller_money_log
				where create_time <> ''
				and type = 'apply_withdraw'
				and status = 1
				and FROM_UNIXTIME(create_time,'%Y-%m') = '".$current."'
				GROUP BY FROM_UNIXTIME(create_time,'%Y-%m-%d')
				UNION ALL
				SELECT 
						FROM_UNIXTIME(pay_time,'%Y-%m-%d') as daytime, 
						0 AS regNum,
						0 as drawnFee,
						0 as sellerFee,
						SUM(money) as sellerCharge,
						0 as buyerCharge
				from ".$prefix."seller_pay_log
				where pay_time <> ''
				and status = 1
				and FROM_UNIXTIME(pay_time,'%Y-%m') = '".$current."'
				GROUP BY FROM_UNIXTIME(pay_time,'%Y-%m-%d')
				UNION ALL
				SELECT 
						FROM_UNIXTIME(pay_time,'%Y-%m-%d') as daytime, 
						0 AS regNum,
						0 as drawnFee,
						0 as sellerFee,
						0 as sellerCharge,
						SUM(money) as buyerCharge
				from ".$prefix."user_pay_log
				where pay_time <> ''
				and pay_type = 2
				and FROM_UNIXTIME(pay_time,'%Y-%m') = '".$current."'
				GROUP BY FROM_UNIXTIME(pay_time,'%Y-%m-%d')
				) as tmp GROUP BY daytime;
				";
		$queryData = DB::select($sql);
        $list = [];
        for($i = 1; $i <= $t; $i++) {
            $daytime = $current . '-' . sprintf("%02d", $i);  
            $dayData = [
                'totalRegNum' => 0,
                'totalDrawnFee' => 0, 
                'totalSellerFee' => 0, 
                'totalSellerCharge' => 0,
                'totalBuyerCharge' => 0, 
                'daytime' => $daytime,
            ]; 
            $bool = false;
            foreach ($queryData as $item) { 
                $item = (array)$item;
                if($item['daytime'] == $daytime){
                    $bool = true; 
                    break;
                }
            }
            if($bool){
                $list[] = $item;
            } else {
                $list[] = $dayData;
            }
        }  

        $sumsql = $sql = "SELECT  
				sum(drawnFee) as totalDrawnFee , 
				sum(sellerFee) as totalSellerFee , 
				sum(sellerCharge) as totalSellerCharge , 
				sum(buyerCharge) as totalBuyerCharge 
				FROM ( 
				SELECT 
						FROM_UNIXTIME(create_time,'%Y-%m-%d') as daytime,  
						SUM(drawn_fee) as drawnFee,
						0 as sellerFee,
						0 as sellerCharge,
						0 as buyerCharge
				from ".$prefix."order
				where 
				pay_status = 1 
				and FROM_UNIXTIME(create_time,'%Y-%m') = '".$current."' 
				AND (status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.")
                OR (status = ".ORDER_STATUS_USER_DELETE." AND buyer_finish_time > 0 AND cancel_time IS NULL) 
                OR (status = ".ORDER_STATUS_SELLER_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL) 
                OR (status = ".ORDER_STATUS_ADMIN_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL))
				UNION ALL
				SELECT 
						FROM_UNIXTIME(create_time,'%Y-%m-%d') as daytime,  
						0 as drawnFee,
						SUM(money) as sellerFee,
						0 as sellerCharge,
						0 as buyerCharge
				from ".$prefix."seller_money_log
				where create_time <> ''
				and type = 'apply_withdraw'
				and status = 1
				and FROM_UNIXTIME(create_time,'%Y-%m') = '".$current."'
				GROUP BY FROM_UNIXTIME(create_time,'%Y-%m-%d')
				UNION ALL
				SELECT 
						FROM_UNIXTIME(pay_time,'%Y-%m-%d') as daytime,  
						0 as drawnFee,
						0 as sellerCharge,
						SUM(money) as sellerCharge,
						0 as buyerCharge
				from ".$prefix."seller_pay_log
				where pay_time <> ''
				and status = 1
				and FROM_UNIXTIME(pay_time,'%Y-%m') = '".$current."' 
				UNION ALL
				SELECT 
						FROM_UNIXTIME(pay_time,'%Y-%m-%d') as daytime,  
						0 as drawnFee,
						0 as sellerFee,
						0 as sellerCharge,
						SUM(money) as buyerCharge
				from ".$prefix."user_pay_log
				where pay_time <> ''
				and pay_type = 2
				and FROM_UNIXTIME(pay_time,'%Y-%m') = '".$current."' 
				) as tmp;
				";
		$sum = DB::select($sumsql);
		return ["list" => $list, "sum" => $sum[0]];
	}

	/**
	 * 平台销售信息统计
	 */
	public static function getBusinessPlatformSelling($proxy, $month, $year, $cityId){
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
        $current = $year.'-'.sprintf("%02d", $month);
		$t = Time::toDate(Time::toTime($current), 't');  
        if($current == Time::toDate(UTC_TIME, 'Y-m')){
            $t = Time::toDate(UTC_TIME, 'd');
        } else if(Time::toTime($current) > Time::toTime(Time::toDate(UTC_DAY, 'Y-m'))){
        	return ["list" => [], "sum" => []];
        }

		$proxySqlStr = '';
		$proxySqlStr1 = '';

		if($data['firstLevel'] > 0){ 
            $proxySqlStr .= "and od.first_level = ".$data['firstLevel'];
            $proxySqlStr1 .= "and ".$prefix."order.first_level = ".$data['firstLevel'];
        }

        if($data['secondLevel'] > 0){ 
            $proxySqlStr .= " and od.second_level = ".$data['secondLevel'];
            $proxySqlStr1 .= " and ".$prefix."order.second_level = ".$data['secondLevel'];
        }

        if($data['thirdLevel'] > 0){ 
            $proxySqlStr .= " and od.third_level = ".$data['thirdLevel'];
            $proxySqlStr1 .= " and ".$prefix."order.third_level = ".$data['thirdLevel'];
        }  
        $sumsql = " SELECT 
        			IFNULL(sum(pay_fee + system_full_subsidy + activity_new_money + discount_fee + integral_fee), 0) as totalPayfee, 
        			count(".$prefix."order.id) as totalNum,
        			((select count(1) from ".$prefix."refund INNER JOIN ".$prefix."order on ".$prefix."order.id = ".$prefix."refund.order_id and ".$prefix."refund.status = 1 and ".$prefix."order.pay_status = 1 ".$proxySqlStr1." and ".$prefix."order.status not in (".ORDER_STATUS_CANCEL_USER.",".ORDER_STATUS_CANCEL_AUTO.",".ORDER_STATUS_CANCEL_SELLER.",".ORDER_STATUS_CANCEL_ADMIN.",".ORDER_STATUS_USER_DELETE.",".ORDER_STATUS_SELLER_DELETE.",".ORDER_STATUS_ADMIN_DELETE.") and FROM_UNIXTIME(".$prefix."refund.create_time,'%Y-%m') = '".$current."') + (select count(1) from ".$prefix."order as od where od.status in (".ORDER_STATUS_CANCEL_USER.",".ORDER_STATUS_CANCEL_AUTO.",".ORDER_STATUS_CANCEL_ADMIN.",".ORDER_STATUS_CANCEL_SELLER.",".ORDER_STATUS_USER_DELETE.",".ORDER_STATUS_SELLER_DELETE.",".ORDER_STATUS_ADMIN_DELETE.") and od.pay_status = 1 ".$proxySqlStr." and FROM_UNIXTIME(od.cancel_time,'%Y-%m') = '".$current."') ) as totalCancleNum,
					IFNULL(SUM(IF(pay_type <> 'cashOnDelivery', pay_fee, 0)), 0) AS totalOnline, 
					IFNULL(SUM(IF(pay_type = 'cashOnDelivery', pay_fee, 0)), 0) AS totalCash,
					IFNULL(sum(integral_fee), 0) as totalIntegralFee,
        			IFNULL(sum(IF(discount_fee > total_fee, total_fee, discount_fee)), 0) as totalDiscountFee,
        				IFNULL(sum(system_full_subsidy), 0) as systemFullSubsidy,
                        IFNULL(sum(seller_full_subsidy), 0) as sellerFullSubsidy,
                        IFNULL(sum(activity_goods_money), 0) as activityGoodsMoney,
                        IFNULL(sum(activity_new_money), 0) as activityNewMoney
        			from ".$prefix."order
        			where pay_status = 1  
					".($data['firstLevel'] > 0 ? "AND first_level = ".$data['firstLevel']." " : " ")."
					".($data['secondLevel'] > 0 ? "AND second_level = ".$data['secondLevel']." " : " ")."
					".($data['thirdLevel'] > 0 ? "AND third_level = ".$data['thirdLevel']." " : " ")."
        			and FROM_UNIXTIME(create_time,'%Y-%m') = '".$current."'
        			and (status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.") OR (status = ".ORDER_STATUS_USER_DELETE." AND buyer_finish_time > 0 AND cancel_time IS NULL) OR (status = ".ORDER_STATUS_SELLER_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL) OR (status = ".ORDER_STATUS_ADMIN_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL))";

        $sum = DB::select($sumsql);
        $sum['seller'] = Seller::find($sellerId);
        /**
         sql说明：左连接order表 查询 id, name, totalPayfee(总金额), totalNum(总数量), totalDrawnfee(总佣金), totalOnline(总在线支付), totalCash(总现金支付), totalDiscountFee(总优惠金额), totalSellerFee(商家盈利)
         在线和现金支付数据 通过子查询实现
         */
        $sql = "SELECT 
        		sum(totalOnline + totalCash + systemFullSubsidy + activityNewMoney + totalDiscountFee + totalIntegralFee) as totalPayfee , 
				sum(totalNum) as totalNum , 
				sum(totalCancleNum) as totalCancleNum , 
				sum(totalOnline) as totalOnline , 
				sum(totalCash) as totalCash , 
				sum(totalIntegralFee) as totalIntegralFee , 
				sum(totalDiscountFee) as totalDiscountFee ,
					sum(systemFullSubsidy) as systemFullSubsidy ,
					sum(sellerFullSubsidy) as sellerFullSubsidy ,
					sum(activityGoodsMoney) as activityGoodsMoney ,
					sum(activityNewMoney) as activityNewMoney , 
				daytime as daytime 
				FROM ( select IFNULL(sum(".$prefix."order.total_fee), 0) as totalPayfee,
                count(".$prefix."order.id) as totalNum, 
    			0 as totalCancleNum,
				IFNULL(SUM(IF(pay_type <> 'cashOnDelivery', pay_fee, 0)), 0) AS totalOnline, 
				IFNULL(SUM(IF(pay_type = 'cashOnDelivery', pay_fee, 0)), 0) AS totalCash,
                IFNULL(sum(integral_fee), 0) as totalIntegralFee,
                IFNULL(sum(IF(discount_fee > total_fee, total_fee, discount_fee)), 0) as totalDiscountFee,
                	IFNULL(sum(system_full_subsidy), 0) as systemFullSubsidy,
                    IFNULL(sum(seller_full_subsidy), 0) as sellerFullSubsidy,
                    IFNULL(sum(activity_goods_money), 0) as activityGoodsMoney,
                    IFNULL(sum(activity_new_money), 0) as activityNewMoney,
                FROM_UNIXTIME(create_time,'%Y-%m-%d') as daytime
                from ".$prefix."order 
                where ".$prefix."order.pay_status = 1  
				".($data['firstLevel'] > 0 ? "AND ".$prefix."order.first_level = ".$data['firstLevel']." " : " ")."
				".($data['secondLevel'] > 0 ? "AND ".$prefix."order.second_level = ".$data['secondLevel']." " : " ")."
				".($data['thirdLevel'] > 0 ? "AND ".$prefix."order.third_level = ".$data['thirdLevel']." " : " ")."
                and FROM_UNIXTIME(".$prefix."order.create_time,'%Y-%m') = '".$current."' 
                AND (".$prefix."order.status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.")
                OR (".$prefix."order.status = ".ORDER_STATUS_USER_DELETE." AND ".$prefix."order.buyer_finish_time > 0 AND ".$prefix."order.cancel_time IS NULL) 
                OR (".$prefix."order.status = ".ORDER_STATUS_SELLER_DELETE." AND ".$prefix."order.auto_finish_time > 0 AND ".$prefix."order.cancel_time IS NULL) 
                OR (".$prefix."order.status = ".ORDER_STATUS_ADMIN_DELETE." AND ".$prefix."order.auto_finish_time > 0 AND ".$prefix."order.cancel_time IS NULL))
                GROUP BY FROM_UNIXTIME(create_time,'%Y-%m-%d')
                UNION ALL
                select 0 as totalPayfee,
                0 as totalNum,
                ((select count(1) from ".$prefix."refund INNER JOIN ".$prefix."order as od on od.id = ".$prefix."refund.order_id and ".$prefix."refund.status = 1 and od.pay_status = 1 ".$proxySqlStr." and od.status not in (".ORDER_STATUS_CANCEL_USER.",".ORDER_STATUS_CANCEL_AUTO.",".ORDER_STATUS_CANCEL_SELLER.",".ORDER_STATUS_CANCEL_ADMIN.",".ORDER_STATUS_USER_DELETE.",".ORDER_STATUS_SELLER_DELETE.",".ORDER_STATUS_ADMIN_DELETE.") where FROM_UNIXTIME(od.cancel_time,'%Y-%m-%d') = FROM_UNIXTIME(".$prefix."order.cancel_time,'%Y-%m-%d')) + (select count(1) from ".$prefix."order as od where od.status in (".ORDER_STATUS_CANCEL_USER.",".ORDER_STATUS_CANCEL_AUTO.",".ORDER_STATUS_CANCEL_SELLER.",".ORDER_STATUS_CANCEL_ADMIN.",".ORDER_STATUS_USER_DELETE.",".ORDER_STATUS_SELLER_DELETE.",".ORDER_STATUS_ADMIN_DELETE.") and od.pay_status = 1 ".$proxySqlStr." and FROM_UNIXTIME(od.cancel_time,'%Y-%m-%d') = FROM_UNIXTIME(".$prefix."order.cancel_time,'%Y-%m-%d'))) as totalCancleNum,
                0 as totalOnline,
                0 as totalCash,
                0 as totalIntegralFee,
                0 as totalDiscountFee,
                0 as systemFullSubsidy,
				0 as sellerFullSubsidy,
				0 as activityGoodsMoney,
				0 as activityNewMoney,
                FROM_UNIXTIME(cancel_time,'%Y-%m-%d') as daytime 
                from ".$prefix."order
                where pay_status = 1  
				".($data['firstLevel'] > 0 ? "AND first_level = ".$data['firstLevel']." " : " ")."
				".($data['secondLevel'] > 0 ? "AND second_level = ".$data['secondLevel']." " : " ")."
				".($data['thirdLevel'] > 0 ? "AND third_level = ".$data['thirdLevel']." " : " ")."
                and FROM_UNIXTIME(cancel_time,'%Y-%m') = '".$current."'
                GROUP BY FROM_UNIXTIME(cancel_time,'%Y-%m-%d') ) as tmp
                GROUP by tmp.daytime 
                "; 

        $queryData = DB::select($sql); 
        $list = [];
        for($i = 1; $i <= $t; $i++) {
            $daytime = $current . '-' . sprintf("%02d", $i);  
            $dayData = [ 
                'totalPayfee' => 0, 
                'totalNum' => 0,
                'totalCancleNum' => 0, 
                'totalOnline' => 0,
                'totalCash' => 0,
                'totalIntegralFee' => 0,
                'totalDiscountFee' => 0, 
                'daytime' => $daytime,
                'systemFullSubsidy' => 0,
				'sellerFullSubsidy' => 0,
				'activityGoodsMoney' => 0,
				'activityNewMoney' => 0,
            ]; 
            $bool = false;
            foreach ($queryData as $item) { 
                $item = (array)$item;
                if($item['daytime'] == $daytime){
                    $bool = true; 
                    break;
                }
            }
            if($bool){
                $list[] = $item;
            } else {
                $list[] = $dayData;
            }
        }  
        // print_r(DB::getQueryLog());exit;  
        //file_put_contents('/mnt/test/sq/storage/logs/sellerservice1.log', print_r(DB::getQueryLog(),true)."\n", FILE_APPEND);
        return ["list" => $list, "sum" => $sum[0]];
	}


    /**
     * @param int $adminId 管理员编号
     * @param int $sellerId 商家编号
     * @param double $money 金额
     * @param int $type 类型 : 1 充值 2扣款
     * @param string $remark 备注
     * @return [array]
     */
    public static function updatebalance($adminId,$sellerId, $money, $type, $remark){
        $result = array(
            'code'	=> 0,
            'data'	=> '',
            'msg'	=> Lang::get('api_system.success.handle')
        );
        $checkSeller = Seller::where('id', $sellerId)->with('extend')->first();
        //商家不存在
        if (!$checkSeller) {
            $result['code'] = '40427';
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
        if ($type == 2 && $checkSeller->extend->money < $money) {
            $result['code'] = '20202';
            return $result;
        }

        DB::beginTransaction();
        try {
            if ($type == 1) {
                $data = [
                    'total_money' => DB::raw("total_money + " . $money),
                    'money' => DB::raw("money + " . $money)
                ];
            } else {
                $data = [
                    'total_money' => DB::raw("total_money - " . $money),
                    'money' => DB::raw("money - " . $money)
                ];
            }
            SellerExtend::where('seller_id', $sellerId)->update($data);
            $extend = SellerExtend::where('seller_id', $sellerId)->first();
            SellerMoneyLog::insert([
                'seller_id' => $sellerId,
                'status' => 1,
                'admin_id' => $adminId,
                'sn' => Helper::getSn(),
                'type' => $type == 1 ? SellerMoneyLog::TYPE_SYSTEM_RECHARGE : SellerMoneyLog::TYPE_SYSTEM_DEBIT,
                'money' => $money,
                'balance' => $extend->money,
                'content' => $remark,
                'create_time' => UTC_TIME,
                'create_day' => UTC_DAY,
            ]);
            if ($type == 1) {
               SellerPayLog::insert([
                   'seller_id' => $sellerId,
                   'admin_id' => $adminId,
                   'payment_type' => 'systemRecharge',
                   'sn' => Helper::getSn(),
                   'money' => $money,
                   'content' => $remark,
                   'create_time' => UTC_TIME,
                   'create_day' => UTC_DAY,
                   'pay_time' => UTC_TIME,
                   'pay_day' => UTC_DAY,
                   'status' => 1
               ]);
            }
            DB::commit();
        } catch (Exception $e) {
            $result['code'] = '99999';
            DB::rollback();
        }

        return $result;
    }

}
