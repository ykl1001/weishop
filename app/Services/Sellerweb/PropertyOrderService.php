<?php namespace YiZan\Services\Sellerweb;

use YiZan\Models\Sellerweb\PropertyOrder; 
use YiZan\Models\Sellerweb\PropertyOrderItem; 
use YiZan\Models\Sellerweb\PropertyFee; 
use YiZan\Models\Sellerweb\PropertyItem; 
use YiZan\Models\PropertyUser;

use YiZan\Utils\String;
use YiZan\Utils\Helper;
use Lang, DB, Validator, Time;
class PropertyOrderService extends \YiZan\Services\PropertyOrderService {

    /**
     * 物业订单列表
     * @param int $sellerId 商家编号
     * @param string $name  业主名称
     * @param int $buildId 	楼栋号
     * @param int $roomId 	房间号
     * @param string $sn  	编号
     * @param string $beginTime 开始时间
     * @param string $endTime 	结束时间
     * @param int $page 		页码
     * @param int $pageSize		每页数量
     */
    public static function getLists($sellerId, $name, $buildId, $roomId, $sn, $beginTime, $endTime, $page, $pageSize){
		$tablePrefix = DB::getTablePrefix();
        $list = PropertyOrder::where('property_order.seller_id', $sellerId)
        					 ->where('puser_id', '>', 0)
        					 ->where('pay_status', '=', 1); 
		if ($name == true) {
			$puserIds = PropertyUser::where('name', 'like', '%'.$name.'%')
									->lists('id');
			$list->whereIn('property_order.puser_id', $puserIds);
		} 

		if($sn == true){
			$list->where('sn', $sn);
		}

		if ($buildId == true || $roomId == true) {
			$data = ['buildId' => $buildId,'roomId' => $roomId];
			$list->join('property_user', function($join) use($data) {
					$join->on('property_order.district_id', '=', 'property_user.district_id');
					if($data['buildId'] == true){
						$join->where('property_user.build_id', '=', $data['buildId']);
					}
					if($data['roomId'] == true){
						$join->where('property_user.room_id', '=', $data['roomId']);
					}
				});

		} 

		if($beginTime && empty($endTime)){
			$list->where('property_order.create_time', '>', Time::toTime($beginTime));
		} elseif (empty($beginTime) && $endTime){
			$list->where('property_order.create_time', '<', Time::toTime($beginTime));
		} elseif($beginTime && $endTime) {
			$beginTime = Time::toTime($beginTime);
			$endTime = Time::toTime($endTime);
			$list->whereRaw($tablePrefix."property_order.create_time between ".$beginTime." and ".$endTime);
		}

		$total_count = $list->count();

		$list->orderBy('property_order.id', 'desc');

		$list = $list->with('seller', 'puser.build', 'puser.room')
					 ->select('property_order.*')
					 ->skip(($page - 1) * $pageSize)
					 ->take($pageSize)
					 ->get()
					 ->toArray();
		return ["list" => $list, "totalCount" => $total_count]; 
    }

    /**
     * @param int $sellerId 物业编号
     * @param int $id 		订单编号
     */
    public static function getById($sellerId, $id){  
    	$data = PropertyOrder::where('seller_id', $sellerId)
    						 ->where('id', $id)
    						 ->with('orderItem.propertyFee.build', 'orderItem.propertyFee.room','orderItem.propertyFee.roomfee.payitem', 'userPayLog')
    						 ->first();  
    	return $data;
    }

	/**
	 * 创建物业订单
	 * @param array 		$seller		物业信息
	 * @param array/int $propertyFeeId 	物业费项目编号
	 * @param int 			$puserId 	业主信息
	 */
	public static function createOrder($seller, $propertyFeeId, $puserId){
	    $result =
	    [
	        'code'	=> 0,
	        'data'	=> null,
	        'msg'	=> ''
	    ]; 

		$puser = PropertyUser::find($puserId);

		if(!$puser){
			$result['code'] = 80315;
			return $result;
		}

	    $propertyFeeId = explode(',', $propertyFeeId);

	    $propertyOrderItemInfo = PropertyOrderItem::whereIn('id', $propertyFeeId)
	    										  ->get()
	    										  ->toArray();
	    if($propertyOrderItemInfo){
	    	$result['code'] = 80311;
	    	return $result;
	    }

	    //判断物业费项目是否都存在
		$propertyFeeArr = []; 	
		$totalFee = 0;
		foreach ($propertyFeeId as $key => $value) {
			$propertyFeeInfo = PropertyFee::where('seller_id', $seller->id)
										  ->where('id', $value)
										  ->where('status', 0)
										  ->with('district', 'puser.user', 'seller', 'build', 'room', 'roomfee')
										  ->first();  
			if(!$propertyFeeInfo){
				$result['code'] = 80309;
				return $result;
			}
			$totalFee += $propertyFeeInfo->fee;
			$propertyFeeArr[] = $propertyFeeInfo->toArray();
		} 
		$data = PropertyFee::whereIn('id', $propertyFeeId)
						   ->where('seller_id', $seller->id)
						   ->groupBy('build_id','room_id','district_id')
						   ->selectRaw('build_id,room_id')
						   ->get()
						   ->toArray(); 	 
		if(count($data) != 1){
			$result['code'] = 80314;
			return $result;
		}
		DB::beginTransaction();
        try {
        	$propertyOrder = new PropertyOrder();
        	$propertyOrder->sn = Helper::getSn();
        	$propertyOrder->seller_id = $seller->id;
        	$propertyOrder->user_id = $puser->user_id;
        	$propertyOrder->puser_id = $puser->id;
        	$propertyOrder->district_id = $puser->district_id; 
        	$propertyOrder->pay_fee = $totalFee;
        	$propertyOrder->pay_type = 'offline';//线下支付
        	$propertyOrder->pay_status = 1;//已支付
        	$propertyOrder->pay_time = UTC_TIME;
        	$propertyOrder->create_time = UTC_TIME;
        	$propertyOrder->first_level = $seller->first_level;
        	$propertyOrder->second_level = $seller->second_level;
        	$propertyOrder->third_level = $seller->third_level;
        	$propertyOrder->save();
        	foreach ($propertyFeeArr as $key => $value) {
        		$propertyOrderItem = new PropertyOrderItem();
        		$propertyOrderItem->seller_id = $seller->id;
        		$propertyOrderItem->order_id = $propertyOrder->id;
        		$propertyOrderItem->propertyfee_id = $value['id'];
        		$propertyOrderItem->price = $value['fee'];
        		$propertyOrderItem->num = 1;
        		$propertyOrderItem->save();
        	}
        	//修改物业费项目状态
        	PropertyFee::whereIn('id', $propertyFeeId)
        			   ->update(['status'=>1,'pay_time'=>UTC_TIME,'puser_id'=>$puser->id]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
        return $result;
	}

}