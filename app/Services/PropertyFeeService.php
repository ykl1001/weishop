<?php namespace YiZan\Services;

use YiZan\Models\PropertyBuilding;
use YiZan\Models\Seller;
use YiZan\Models\PropertyFee;
use YiZan\Models\PropertyUser;
use YiZan\Models\PayItem;
use YiZan\Models\RoomFee;
use YiZan\Models\PropertyRoom;
use YiZan\Models\District;

use YiZan\Services\PushMessageService;

use YiZan\Utils\String;
use Lang, DB, Validator, Time;

class PropertyFeeService extends BaseService {
	
	/**
	 * 列表
	 * @param $sellerId int  物业编号
	 * @param $buildId 	int	 楼宇编号
	 * @param $roomId 	int	 房间编号
	 * @param $name string 		 编号
	 * @param $payitemId int   收费项目编号
	 * @param $status int   物业费项目
	 * @param $propertyFeeId array   房间编号
	 * @param $beginTime string   开始时间
	 * @param $endTime string     结束时间
 	 * @param $page int 页码
 	 * @param $pageSize int 页数量 
	 * @return
	 */
	public static function getLists($sellerId, $buildId, $roomId, $name, $payitemId, $status, $beginTime, $endTime, $propertyFeeId, $page, $pageSize) { 
		DB::connection()->enableQueryLog();
		$list = PropertyFee::where('property_fee.seller_id', $sellerId);
		
		if ($name == true) {
            $sellerIds = PropertyRoom::where('owner', 'like', '%'.$name.'%')
                ->lists('seller_id');
            $list->whereIn('seller_id', $sellerIds);

          //  $list->where('property_fee.name', 'like', '%'.$name.'%');
		}

		if ($buildId == true ) {
			$list->where('build_id', $buildId);
		}

		if ($roomId == true ) {
			$list->where('room_id', $roomId);
		}

		if($status > 0){
			$list->where('status', $status - 1);
		}

		if($payitemId > 0){
			$roomFeeIds = RoomFee::where('payitem_id', $payitemId)
								 ->lists('id');
			$list->whereIn('roomfee_id', $roomFeeIds);
		} 

		if($beginTime && empty($endTime)){
			$list->where('begin_time', '>', Time::toTime($beginTime));
		} elseif (empty($beginTime) && $endTime){
			$list->where('end_time', '<', Time::toTime($beginTime));
		} elseif($beginTime && $endTime) {
			$beginTime = Time::toTime($beginTime);
			$endTime = Time::toTime($endTime);
			$list->whereRaw("(begin_time between ".$beginTime." and ".$endTime." or end_time between ".$beginTime." and ".$endTime.")");
		}

		if($propertyFeeId){
			$list = $list->whereIn('id', explode(',', $propertyFeeId))
						 ->with('seller', 'build', 'room', 'puser', 'roomfee.payitem')
						 ->get(); 
			$total_fee = 0;
			foreach ($list as $key => $value) {
				$total_fee += $value->fee;
			}
			$data = PropertyFee::whereIn('id', explode(',', $propertyFeeId))
							   ->where('seller_id', $sellerId)
							   ->groupBy('build_id','room_id')
							   ->selectRaw('build_id,room_id')
							   ->first(); 
			$users = PropertyUser::where('seller_id', $sellerId)
								 ->where('build_id', $data->build_id)
								 ->where('room_id', $data->room_id)
								 ->get()
								 ->toArray();
			return ["list" => $list, "users" => $users, "totalFee" => $total_fee];
		} else {

			$total_count = $list->count();

			$list->orderBy('property_fee.id', 'desc');

			$list = $list->with('seller', 'build', 'room', 'puser', 'roomfee.payitem')
						 ->select('property_fee.*')
						 ->skip(($page - 1) * $pageSize)
						 ->take($pageSize)
						 ->get()
						 ->toArray();
						 // print_r(DB::getQueryLog());exit;
			return ["list" => $list, "totalCount" => $total_count];
		} 
	}

	/**
	 * 详情
	 * @param $sellerId int  物业编号
	 * @param $id int 		 编号 
	 * @return
	 */
	public static function getById($sellerId, $id) {
		$data = PropertyFee::with('seller', 'build', 'room', 'puser', 'roomfee.payitem')->find($id); 
		return $data;
	}

	/**
	 * 添加、编辑
	 * @param $sellerId int  物业编号 
	 * @param $buildId 	int	 楼宇编号
	 * @param $roomId int    房间编号
 	 * @param $payitemId int 收费项目编号
	 * @param $beginTime string   开始时间
	 * @param $num int     缴费数量
	 * @param $isAutoSet int 是否自动取 开始时间
	 * @return
	 */
	public static function save($sellerId, $buildId, $roomId, $roomFeeId, $beginTime, $num, $isAutoSet = true) {
	
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '操作成功'
		); 

		$rules = array( 
            'buildId'      	=> ['required'],
            // 'roomId'        => ['required'],
            'roomFeeId'     => ['required'], 
            // 'beginTime'     => ['required'], 
            'num'     		=> ['required'], 
        );
        
        $messages = array
        ( 	
        	'buildId.required'		=> 80201,	
            // 'roomId.required'		=> 80204,	
            'roomFeeId.required'	=> 80301,	 
            // 'beginTime.required'	=> 80302,	 
            'num.required'			=> 80303,	 	 
        );

        $validator = Validator::make(
            [ 
                'buildId'    => $buildId,
                // 'roomId'     => $roomId,
                'roomFeeId'  => $roomFeeId, 
                // 'beginTime'  => $beginTime, 
                'num'  		 => $num,  
            ], $rules, $messages);
        
        //验证信息
        if ($validator->fails()) 
        {
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }  

		if ($sellerId < 1) {
			$result['code'] = 80304;
			return $result;
		}
		$roomFee = RoomFee::where('id', $roomFeeId)
						  ->with('payitem')
						  ->first(); 

		switch ($roomFee->payitem['charging_unit']) {
			default:
				$timeStr = 'months';
				break;
		}  
		$districtId = District::where('seller_id', $sellerId)->pluck('id'); 
		if($roomId > 0 && empty($beginTime)){
			$isAutoSet = true;
		}
		if($isAutoSet){//指定房间号
			if($roomId > 0){    
				$propertyFeeInfo = PropertyFee::where('seller_id', $sellerId)
											  ->where('build_id', $buildId)
											  ->where('roomfee_id', $roomFeeId)
											  ->where('room_id', $roomId)
											  ->orderBy('id', 'DESC')
											  ->first(); 
				//如果是能查找上次的记录 则按上次结束时间计算下次开始时间否则取入住时间作为下次开始时间
				if($propertyFeeInfo && $propertyFeeInfo->end_time > 0){
					$begin_time = $propertyFeeInfo->end_time + 86400; 
				} else {
					$user_room = PropertyRoom::find($roomId); 
					$begin_time = $user_room->intake_time;
				} 
				DB::beginTransaction();
				try{
					for($i = 0; $i < $num; $i++){ 
						$curentTime = $begin_time; 
						$endTime = strtotime("+1".$timeStr, $begin_time); 
						$createMonth = Time::toTime(Time::toDate($curentTime, 'Y-m')); 
						$fee = new PropertyFee(); 
						$fee->seller_id 		= $sellerId;
						$fee->district_id 		= $districtId;
						$fee->build_id			= $buildId;
						$fee->room_id			= $roomId;
						$fee->roomfee_id 		= $roomFeeId;
						// $fee->puser_id			= $roomFee->puser_id;
						$fee->begin_time 		= $curentTime;
						$fee->end_time 			= $endTime;
						$fee->create_month 		= $createMonth;
						$fee->fee 				= $roomFee->fee;
						$fee->status			= 0;  
						$fee->save(); 
					}

                    //cz
                    $user = PropertyUser::where('seller_id', $sellerId)
                        ->where('build_id', $buildId)
                        ->where('room_id', $roomId)
                        ->where('status',1)
                        ->first();

                    if($user){
                        $user = $user->toArray();
                        PushMessageService::notice($user['userId'], '', 'message.propertybill', $sellerId,['app'],'buyer', 1, $sellerId);
                    }

                    DB::commit();
				} catch(Exception $e){
					DB::rollback();
					$result['code'] = 80312;
				}
			} else {
				// 如果未指定房间号 则查询楼栋下所有的房间缴费项目
				$roomFees = RoomFee::where('payitem_id', $roomFee->payitem_id)
									 ->where('build_id', $buildId)
									 ->where('seller_id', $sellerId)
									 ->with('payitem')
									 ->get()
									 ->toArray(); 
				DB::beginTransaction();
				try{
					foreach ($roomFees as $item) { 
						for($i = 0; $i < $num; $i++){  
								$propertyFeeInfo = PropertyFee::where('seller_id', $sellerId)
															  ->where('build_id', $buildId)
															  ->where('roomfee_id', $item['id'])
															  ->where('room_id', $item['roomId']) 
											  				  ->orderBy('id', 'DESC')
															  ->first(); 
								//如果是能查找上次的记录 则按上次结束时间计算下次开始时间否则取入住时间作为下次开始时间
								if($propertyFeeInfo && $propertyFeeInfo->end_time > 0){
									$begin_time = $propertyFeeInfo->end_time + 86400; 
								} else {
									$user_room = PropertyRoom::find($item['roomId']); 
									$begin_time = $user_room->intake_time;
								} 
								$curentTime = $begin_time; 
								$endTime = strtotime("+1".$timeStr, $begin_time); 
								$createMonth = Time::toTime(Time::toDate($curentTime, 'Y-m')); 
								$fee = new PropertyFee(); 
								$fee->seller_id 		= $sellerId;
								$fee->district_id 		= $districtId;
								$fee->build_id			= $buildId;
								$fee->room_id			= $item['roomId'];
								$fee->roomfee_id 		= $item['id'];
								// $fee->puser_id			= $roomFee->puser_id;
								$fee->begin_time 		= $curentTime;
								$fee->end_time 			= $endTime - 86400;
								$fee->create_month 		= $createMonth;
								$fee->fee 				= $item['fee'];
								$fee->status			= 0;  
								$fee->save(); 
							}
					}

                    //cz
                    $user = PropertyUser::where('seller_id', $sellerId)
                        ->where('build_id', $buildId)
                        ->where('room_id', $roomId)
                        ->where('status',1)
                        ->first();
                    if($user){
                        $user = $user->toArray();
                        PushMessageService::notice($user['userId'], '', 'message.propertybill', $sellerId,['app'],'buyer', 1, $sellerId);
                    }

					DB::commit();
				} catch(Exception $e) {
					DB::rollback();
					$result['code'] = 80312;
				} 
			}
		} else { 
			$begin_time = strtotime($beginTime);
			$end_time = strtotime("+".$num.$timeStr, $begin_time);
			//指定房间号
			if($roomId > 0){   
				 
				$propertyFeeInfo = PropertyFee::where('seller_id', $sellerId)
											  ->where('build_id', $buildId)
											  ->where('roomfee_id', $roomFeeId)
											  ->where('room_id', $roomId)
							 				  ->whereBetween('begin_time', [$begin_time, $end_time])
							 				  ->whereBetween('end_time', [$begin_time, $end_time]) 
											  ->first(); 
				if($propertyFeeInfo){
					$result['code'] = 80306;
					return $result;
				}  
				
				DB::beginTransaction();
				try{
					for($i = 0; $i < $num; $i++){ 
						$curentTime = strtotime("+".$i.$timeStr, $begin_time); 
						$endTime = strtotime("+".($i+1).$timeStr, $begin_time) - 3600 * 24; 
						$createMonth = Time::toTime(Time::toDate($curentTime, 'Y-m')); 
						$fee = new PropertyFee(); 
						$fee->seller_id 		= $sellerId;
						$fee->district_id 		= $districtId;
						$fee->build_id			= $buildId;
						$fee->room_id			= $roomId;
						$fee->roomfee_id 		= $roomFeeId;
						// $fee->puser_id			= $roomFee->puser_id;
						$fee->begin_time 		= $curentTime;
						$fee->end_time 			= $endTime;
						$fee->create_month 		= $createMonth;
						$fee->fee 				= $roomFee->fee;
						$fee->status			= 0;  
						$fee->save(); 
					}

                    //cz
                    $user = PropertyUser::where('seller_id', $sellerId)
                        ->where('build_id', $buildId)
                        ->where('room_id', $roomId)
                        ->where('status',1)
                        ->first();
                    if($user){
                        $user = $user->toArray();
                        PushMessageService::notice($user['userId'], '', 'message.propertybill', $sellerId,['app'],'buyer', 1, $sellerId);
                    }
					DB::commit();
				} catch(Exception $e){
					DB::rollback();
					$result['code'] = 80312;
				}
			} else {
				// 如果未指定房间号 则查询楼栋下所有的房间缴费项目
				$roomFees = RoomFee::where('payitem_id', $roomFee->payitem_id)
									 ->where('build_id', $buildId)
									 ->where('seller_id', $sellerId)
									 ->with('payitem')
									 ->get()
									 ->toArray(); 
				DB::beginTransaction();
				try{
					foreach ($roomFees as $item) { 
						for($i = 0; $i < $num; $i++){ 
								$curentTime = strtotime("+".$i.$timeStr, $begin_time); 
								$endTime = strtotime("+".($i+1).$timeStr, $begin_time); 
								$propertyFeeInfo = PropertyFee::where('seller_id', $sellerId)
															  ->where('build_id', $buildId)
															  ->where('roomfee_id', $item['id'])
															  ->where('room_id', $item['roomId'])
															  ->whereRaw("(begin_time between ".$curentTime." and ".$endTime." or end_time between ".$curentTime." and ".$endTime.")")
															  ->first(); 
								if($propertyFeeInfo){ 
									DB::rollback();
									$result['code'] = 80316;
									return $result;
								}  
								$createMonth = Time::toTime(Time::toDate($curentTime, 'Y-m')); 
								$fee = new PropertyFee(); 
								$fee->seller_id 		= $sellerId;
								$fee->district_id 		= $districtId;
								$fee->build_id			= $buildId;
								$fee->room_id			= $item['roomId'];
								$fee->roomfee_id 		= $item['id'];
								// $fee->puser_id			= $roomFee->puser_id;
								$fee->begin_time 		= $curentTime;
								$fee->end_time 			= $endTime - 24 * 3600;
								$fee->create_month 		= $createMonth;
								$fee->fee 				= $item['fee'];
								$fee->status			= 0;  
								$fee->save(); 
							}
					}

                    //cz
                    $user = PropertyUser::where('seller_id', $sellerId)
                        ->where('build_id', $buildId)
                        ->where('room_id', $roomId)
                        ->where('status',1)
                        ->first();
                    if($user){
                        $user = $user->toArray();
                        PushMessageService::notice($user['userId'], '', 'message.propertybill', $sellerId,['app'],'buyer', 1, $sellerId);
                    }
	 
					DB::commit();
				} catch(Exception $e) {
					DB::rollback();
					$result['code'] = 80312;
				} 
			}
		} 
 
		return $result;
	}

	/**
	 * 删除
	 * @param  $sellerId int    物业编号
	 * @param  $id int     编号
	 * @return 
	 */
	public static function delete($sellerId, $id) {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '操作成功'
		);  
		if (!$id) {
			$result['code'] = 80307;
			return $result;
		}

		$id = explode(',', $id);
		
		//删除，待完善，相关信息
		foreach ($id as $key => $value) {
			$propertyFee = PropertyFee::where('id', $value)
									  ->first();
			if(!$propertyFee){
				$result['code'] = 80307;
				return $result;
			}
		}
		DB::beginTransaction();
        try {
        	if(count($id) > 1){
            	$rs = PropertyFee::where('seller_id', $sellerId)
            			   		 ->whereIn('id', $id)
            			   		 ->where('status', 0)
            			   		 ->delete();
			} else {
            	$rs = PropertyFee::where('seller_id', $sellerId)
		            			 ->where('id', $id)
            			   		 ->where('status', 0)
		            			 ->delete();
			}   
			if($rs){
	            DB::commit(); 
	        } else {
        		$result = 80308;
	        }
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        } 
	    return $result;
	}  

}
