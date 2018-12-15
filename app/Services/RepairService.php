<?php namespace YiZan\Services;

use YiZan\Models\Seller;
use YiZan\Models\PropertyBuilding;
use YiZan\Models\District;
use YiZan\Models\PropertyUser;
use YiZan\Models\PropertyRoom;
use YiZan\Models\User;
use YiZan\Models\Repair;
use YiZan\Models\SellerStaff;
use YiZan\Models\RepairRate;
use YiZan\Models\RepairType;
use YiZan\Utils\String;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use Illuminate\Database\Query\Expression;
use DB, Lang, Exception, Validator;

class RepairService extends BaseService {
	/*
	* 后台报修列表
	*/
	public static function getLists($sellerId, $name, $build, $roomNum, $page, $pageSize, $status,$userName = '',$staffName=''){
    //   DB::connection()->enableQueryLog();
		$list = Repair::orderBy('repair.id', 'DESC')
            ->with('build', 'room', 'puser', 'types','staff')
            ->where('repair.seller_id', $sellerId);


        if($status > -1){
            $list ->where('repair.status', $status);
        }

		if($name == true){

            $list->whereIn('puser_id', function($query) use ($name){
                $query->select('id')
                    ->from('property_user')
                    ->where('name', 'like', '%'.$name.'%');
            });
		}

		if ($build == true ) {

            $list->whereIn('build_id', function($query) use ($build){
                $query->select('id')
                    ->from('property_building')
                    ->where('name', 'like', '%'.$build.'%');
            });
		}

		if ($roomNum == true ) {

            $list->whereIn('room_id', function($query) use ($roomNum){
                $query->select('id')
                    ->from('property_room')
                    ->where('room_num', 'like', '%'.$roomNum.'%');
            });


		}

       if($userName == true){
           $list->whereIn('puser_id', function($query) use ($userName){
               $query->select('id')
                   ->from('property_user')
                   ->where('name', 'like', '%'.$userName.'%');
           });
        }

        if($staffName == true){
            $list->whereIn('seller_staff_id', function($query) use ($staffName){
                $query->select('id')
                    ->from('seller_staff')
                    ->where('name', 'like', '%'.$staffName.'%');
            });

        }

    	$totalCount = $list->count();

 		$list = $list->skip(($page - 1) * $pageSize)
		             ->take($pageSize)
		             ->get();

        if($list){
            $list = $list->toArray();
            foreach($list as $k => $v){
              //  $list[$k]['star'] = RepairRate::where('repair_id',$v['id'])->pluck('star');
                $list[$k]['rateContent'] = RepairRate::where('repair_id',$v['id'])->pluck('content');
                $list[$k]['star'] = RepairRate::where('repair_id',$v['id'])->count();


            }
        }else{
            $list = null;
        }
    	return ["list"=>$list, "totalCount"=>$totalCount];
	}

	public static function getById($id){
		$data = Repair::where('id', $id)
					 ->with('build', 'room', 'puser', 'types','staff')
		             ->first();
        $data['star'] = RepairRate::where('repair_id',$id)->pluck('star');
        $data['rateContent'] = RepairRate::where('repair_id',$id)->pluck('content');
		return $data;
	}

	/*
	* 处理报修
	*/
	public static function save($id, $sellerId, $status){
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '操作成功'
		];	

		if((int)$sellerId < 1){
			$result['code'] = 80202;
			return $result;
		} 
		$repair = Repair::where('id', $id)->first();
		if (!$repair) {
			$result['code'] = 80215;
			return $result;
		}

		if ($repair->status != 0 && $repair->status == $status) {
			$result['code'] = 80216;
			return $result;
		}

		if ($status == 1) {
			$repair->dispose_time = UTC_TIME;
		} else {
			$repair->finish_time = UTC_TIME;
		}
		$repair->status = $status;
		$repair->save();

		return $result; 
	}

	public static function getRepairLists($userId, $districtId, $page){
		//DB::connection()->enableQueryLog();
		$list = Repair::orderBy('create_time', 'DESC')
						->where('district_id', $districtId)
						->where('user_id', $userId)
						->skip(($page - 1) * 20)
			            ->take(20)
			            ->with('build', 'room', 'puser', 'types', 'staff')
			            ->get()
			            ->toArray();
		foreach ($list as $key => $value) {
			$list[$key]['images'] = $value['images'] ? explode(',', $value['images']) : null;
			$list[$key]['repairType'] = $value['types']['name'];
			$list[$key]['createTime'] = yztime($value['createTime']);
		}
		  // print_r($list);
		  // print_r(DB::getQueryLog());exit;
    	return $list;
	}

	public static function get($id, $districtId){
		$data = Repair::where('id', $id)
					 ->where('district_id', $districtId)
					 ->with('build', 'room', 'puser', 'types', 'district','staff','rate')
		             ->first();
		$data = $data ? $data->toArray() : NULL;
		$data['images'] = $data['images'] ? explode(',', $data['images']) : null;
		$data['repairType'] = $data['types']['name'];
		$data['createTime'] = yztime($data['createTime']);
		// print_r($data);
		// exit;
		return $data;
	}

	public static function getRepairTypeLists() {
        $list = RepairType::orderBy('id', 'desc')->get()->toArray();
        
        return $list;
	}

	public static function createRepair($userId, $districtId, $type, $images, $content,$apiTime) {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );
        if($userId == 0){
            $result['code'] = 30015;
            return $result;
        }
        $rules = array(
			'districtId' => ['required', 'min:1'],
			'type'       => ['required', 'min:1'],
            'content'    => ['required'],
			'apiTime'    => ['required']
        );
        $messages = array
        (
			'districtId.required' => 60313,
			'districtId.min'      => 60313,
			'type.required'       => 30333,
			'type.min'            => 30335,
            'content.required'    => 30334,
			'apiTime.required'    => 30337
        );
        $validator = Validator::make(
            [
				'districtId' => $districtId,
				'type'       => $type,
                'content'    => $content,
				'apiTime'    => $apiTime
            ], $rules, $messages);
        //验证信息
        if ($validator->fails())
        {
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        $apiTime = Time::toTime($apiTime);
		$puser = PropertyUser::where('user_id', $userId)->where('district_id', $districtId)->first();
		if (!$puser) {
			$result['code'] = 30336;
			return $result;
		}
		if (count($images) > 0) {
            foreach ($images as $key => $image) {
                $images[$key] = self::moveSellerImage($puser->seller_id, $image);
                if (!$images[$key]) {
                    $result['code'] = 50004;
                    return $result;
                }
            }
            $images = implode(',', $images);
        } else {
            $images = '';
        }

		$repair = new Repair();
        $content = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '',$content);
		
		$repair->build_id			= $puser->build_id;
		$repair->seller_id 			= $puser->seller_id;
		$repair->room_id			= $puser->room_id;
		$repair->district_id 		= $districtId;
		$repair->puser_id			= $puser->id;
		$repair->user_id 			= $userId;
		$repair->type 				= $type;
		$repair->content 			= $content;
		$repair->images 			= $images;
		$repair->status 			= 0;
        $repair->create_time		= UTC_TIME;
        $repair->api_time		    = $apiTime;
		$repair->save();
		
		return $result;
	}

    public function designate($id,$staffId,$status,$sellerId){
        $result =
            [
                'code'	=> 0,
                'data'	=> null,
                'msg'	=> '操作成功'
            ];

        if((int)$sellerId < 1){
            $result['code'] = 80202;
            return $result;
        }
        $repair = Repair::where('id', $id)->with('staff','seller','puser')->first();

        if (!$repair) {
            $result['code'] = 80215;
            return $result;
        }

        if ($repair->status != 0 && $repair->status == $status) {
            $result['code'] = 80216;
            return $result;
        }

        if ($status == 1) {
            $repair->dispose_time = UTC_TIME;
        } else {
            $repair->finish_time = UTC_TIME;
        }
        $repair->seller_staff_id = $staffId;
        $repair->status = $status;
        $repair->save();
        if($repair->status == 1){
            $staffuser =  SellerStaff::where('id', $staffId)->first();
            /*报修人*/
            PushMessageService::notice($repair->user_id, '', 'property.ongoing',['id'=>$repair->id,'districtId'=>$repair->district_id],['app'],'buyer',7,0);
            PushMessageService::notice( $staffuser->user_id,'', 'property.staffgoing', ['id'=>$repair->id],['app'],'staff',7 ,0);
        }else if($repair->status == 2){
            PushMessageService::notice( $repair->user_id, '', 'property.over', ['id'=>$repair->id,'districtId'=>$repair->district_id],['app'],'buyer',7,0);
        }


        return $result;
    }


    public static  function getRepair($type,$sellerId){
        $data = SellerStaff::where('type', 4)
            ->where('repair_type_id',$type)
            ->where('seller_id',$sellerId)
            ->where('status',1)
            ->get();
        if($data){
            $data = $data->toArray();
        }else{
            $data = null;
        }
        return $data;
    }

    public static function delete($id,$sellerId) {
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
            $repair = Repair::where('id', $value)
                ->first();
            if(!$repair){
                $result['code'] = 80307;
                return $result;
            }
        }
        DB::beginTransaction();
        try {
            if(count($id) > 1){
                $rs = Repair::where('seller_id', $sellerId)
                    ->whereIn('id', $id)
                    //   ->where('status', 0)
                    ->delete();
            } else {
                $rs = Repair::where('seller_id', $sellerId)
                    ->where('id', $id)
                    //   ->where('status', 0)
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

    public function createRate($userId,$id,$content,$star){
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> Lang::get('api.success.create_order_rate')
        );
        $rules = array(
            'id' => ['required'],
            'content' => ['required'],
            'star' => ['required']
        );
        $messages = array(
            'id.required'	=> '50007',
            'content.required'	=> '50003',
            'star.required'	    => '50006'
        );
        $validator = Validator::make([
            'id' => $id,
            'content' => $content,
            'star' => $star
        ], $rules, $messages);
        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        //评价星级不在1-5之间
        if ($star < 1 || $star > 5) {
            $result['code'] = '50010';
            return $result;
        }

        $order = Repair::where('id', $id)->where('user_id', $userId)->first();
        if (!$order) {//没有订单
            $result['code'] = 50001;
            return $result;
        }
        if ($order->status <> 2) {//未确认,不能评价
            $result['code'] = 50002;
            return $result;
        }
        //订单已评价
        if ($order->is_rate) {
            $result['code'] = 50011;
            return $result;
        }

        $orderRate = new RepairRate;
        $orderRate->repair_id 			= $order->id;
        $orderRate->seller_id 			= $order->seller_id;
        $orderRate->staff_id 		    = $order->seller_staff_id;
        $orderRate->user_id 			= $order->user_id;
        $orderRate->content 			= $content;
        $orderRate->create_time			= UTC_TIME;
        $orderRate->star                = $star;
        if ($orderRate->star == 5) {//好评
            $orderRate->result = 'good';
        }elseif ($orderRate->star == 1) {//差评
            $orderRate->result = 'bad';
        }else{//中评
            $orderRate->result = 'neutral';
        }
        $orderRate->save();

        $order->is_rate = 1;
        $order->save();

        return $result;
    }
}
