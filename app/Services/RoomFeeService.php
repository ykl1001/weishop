<?php namespace YiZan\Services;

use YiZan\Models\PropertyBuilding;
use YiZan\Models\Seller;
use YiZan\Models\RoomFee;
use YiZan\Models\PayItem;
use YiZan\Models\PropertyUser;
use YiZan\Models\PropertyRoom;
use YiZan\Models\District;

use YiZan\Utils\String;
use Lang, DB, Validator, Time;

class RoomFeeService extends BaseService {
	
	/**
	 * 列表
	 * @param $sellerId int  物业编号
	 * @param $name string 		 编号
	 * @param $build 	int	 楼宇编号
	 * @param $roomNum int    房间编号
 	 * @param $page int 页码
 	 * @param $pageSize int 页数量 
	 * @return
	 */
	public static function getLists($sellerId, $name, $build, $roomNum, $payitemId, $page, $pageSize) {
		$list = RoomFee::where('room_fee.seller_id', $sellerId);
		
		if ($name == true) {
			$list->where('room_fee.name', 'like', '%'.$name.'%');
		}

		if ($build == true ) {
			$list->join('property_building', function($join) use($build){
				$join->on('property_building.id', '=', 'room_fee.build_id')
					->where('property_building.name', 'like', "%{$build}%");
			});
		}

		if ($roomNum == true ) {
			$list->join('property_room', function($join) use($roomNum){
				$join->on('property_room.id', '=', 'room_fee.room_id')
					->where('property_room.room_num', 'like', "%{$roomNum}%");
			});
		}

		if($payitemId > 0){
			$list->where('payitem_id', $payitemId);
		}

		$total_count = $list->count();

		$list = $list->with('seller', 'build', 'room', 'puser', 'payitem', 'PropertyFeeCount', 'NotPropertyFeeCount')
					 ->select('room_fee.*')
					 ->skip(($page - 1) * $pageSize)
					 ->take($pageSize)
					 ->get()
					 ->toArray();

		return ["list" => $list, "totalCount" => $total_count];
	}

		/**
	 * 列表
	 * @param $sellerId int  物业编号 
	 * @param $buildId 	int	 楼宇编号
	 * @param $roomId int    房间编号 
	 * @param $name string   业主信息 
	 * @return
	 */
	public static function getSearchLists($sellerId, $buildId, $roomId, $name) {
		if($roomId > 0) {
			$list = RoomFee::where('room_fee.seller_id', $sellerId) 
						   ->where('build_id', $buildId)
						   ->where('room_id', $roomId)
						   ->orderBy('room_fee.id', 'desc') 
						   ->with('payitem')
						   ->select('room_fee.*') 
						   ->get()
						   ->toArray(); 
		} else {
			$list = RoomFee::where('room_fee.seller_id', $sellerId) 
						   ->where('build_id', $buildId) 
						   ->orderBy('room_fee.id', 'desc') 
						   ->groupBy('payitem_id')
						   ->with('payitem')
						   ->select('room_fee.*') 
						   ->get()
						   ->toArray(); 
		}
		

		foreach ($list as $key => $value) { 
            $list[$key]['chargingItem'] = Lang::get('api_seller.property.charging_item.'.$value['payitem']['chargingItem']);
            $list[$key]['chargingUnit'] = Lang::get('api_seller.property.charging_unit.'.$value['payitem']['chargingUnit']);
		}
		
		return $list;
	}


	/**
	 * 详情
	 * @param $sellerId int  物业编号
	 * @param $id int 		 编号 
	 * @return
	 */
	public static function getById($sellerId, $id) {
		$data = RoomFee::with('seller', 'build', 'room', 'puser', 'payitem')->find($id);
		if (!$data) {
			$result['code'] = 80203;
			return $data;
		}

		return $data;
	}

	/**
	 * 添加、编辑
	 * @param $sellerId int  物业编号
	 * @param $id int 		 编号
	 * @param $buildId 	int	 楼宇编号
	 * @param $roomId int    房间编号
 	 * @param $payitemId int 收费项目编号
	 * @param $remark string 备注
	 * @return
	 */
	public static function save($sellerId, $id, $buildId, $roomId, $payitemId, $remark) {
		if ((int)$payitemId < 1) {
            $result['code'] = 80224;
            return $result;
        }
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '操作成功',
			'flag'  => true
		);  

		if ($id > 0) {
			$fee = RoomFee::find($id);
			if (!$fee) {
				$result['code'] = 80223;
				return $result;
			}
			$fee->remark			= $remark;  
			$fee->save();
		} else {
			$rules = array( 
	            'buildId'      	=> ['required'], 
	            'payitemId'     => ['required'], 
	        );
	        
	        $messages = array
	        ( 	
	        	'buildId.required'		=> 80201,	 
	            'payitemId.required'	=> 80224,	 	 
	        );

	        $validator = Validator::make(
	            [ 
	                'buildId'    => $buildId, 
	                'payitemId'  => $payitemId,  
	            ], $rules, $messages);
	        
	        //验证信息
	        if ($validator->fails()) 
	        {
	            $messages = $validator->messages();
	            $result['code'] = $messages->first();
	            return $result;
	        } 

			if ($buildId < 1) {
				$result['code'] = 80201;
				return $result;
			}

			if ($sellerId < 1) {
				$result['code'] = 80202;
				return $result;
			}  
 			$payitem = PayItem::find($payitemId);
			$districtId = District::where('seller_id', $sellerId)->pluck('id'); 
			if($roomId > 0){  
				$build_room = PropertyRoom::where('build_id', $buildId)
										  ->where('seller_id', $sellerId)
										  ->where('id', $roomId)
										  ->first();
				if (!$build_room) {
					$result['code'] = 80219;
					return $data;
				}
				$total_fee = 0;
				switch ($payitem->charging_item) {
					case '0':
						$total_fee = $payitem->price * $build_room->structure_area;
						break;
					case '1':
						$total_fee = $payitem->price * $build_room->room_area;
						break; 
					default:
						$total_fee = $payitem->price;
						break;
				}  
				$fee = new RoomFee();
				$fee->build_id			= $buildId;
				$fee->district_id 		= $districtId;
				$fee->seller_id 		= $sellerId;
				$fee->room_id			= $roomId;
				$fee->payitem_id 		= $payitemId;
				$fee->fee				= $total_fee;
				$fee->remark			= $remark;  
				$fee->save();
			} else {
				$errorMsg = '';
				$rooms = PropertyRoom::where('district_id', $districtId)
									   ->where('seller_id', $sellerId)
									   ->where('build_id', $buildId)
									   ->get()
									   ->toArray(); 
				foreach ($rooms as $value) { 
					$k++;
					$roomFee = RoomFee::where('district_id', $districtId)
									  ->where('seller_id', $sellerId)
									  ->where('build_id', $buildId)
									  ->where('room_id', $value['id'])
									  ->where('payitem_id', $payitemId)
									  ->first(); 

					$total_fee = 0;
					switch ($payitem->charging_item) {
						case '0':
							$total_fee = $payitem->price * $value['structureArea'];
							break;
						case '1':
							$total_fee = $payitem->price * $value['roomArea'];
							break; 
						default:
							$total_fee = $payitem->price;
							break;
					}  
					if(!$roomFee){
						$fee = new RoomFee();
						$fee->build_id			= $buildId;
						$fee->district_id 		= $districtId;
						$fee->seller_id 		= $sellerId;
						$fee->room_id			= $value['id'];
						$fee->payitem_id 		= $payitemId; 
						$fee->fee 				= $total_fee; 
						$fee->remark			= $remark;  
						$fee->save();
					} else {
						if($k < count($rooms)){
							$errorMsg .= $value['roomNum'].',';
						} else {
							$errorMsg .= $value['roomNum'];
						}
					} 
				}
				if($errorMsg){
					$result['msg'] = '以下房间'.$errorMsg.'添加失败';
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
		if (!$id) {
			$result['code'] = 80203;
			return $result;
		}

		if(!is_array($id))
		{
			$id = (array)$id;
		}

		//删除，待完善，相关信息
		DB::beginTransaction();
        try {
            RoomFee::where('seller_id', $sellerId)
            	   ->whereIn('id', $id)
            	   ->delete();
            
            DB::commit();

        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
	    return $result;
	}  
}
