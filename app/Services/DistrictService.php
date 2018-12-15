<?php namespace YiZan\Services;

use YiZan\Models\Region;
use YiZan\Models\PropertyBuilding;
use YiZan\Models\PropertyRoom;


use YiZan\Models\District;
use YiZan\Models\Area;
use YiZan\Models\Proxy;
use Exception, DB, Lang, Validator, App, Config;

class DistrictService extends BaseService{

    public static function getById($id){
        $data = District::where('id', $id)
                        ->with('seller', 'province', 'city', 'area')
                        ->first();
        $data = $data ? $data->toArray() : [];
        return $data;
    }

    /**
     * 保存
     */
    public static function save($id, $name, $address, $provinceId, $cityId, $areaId, $houseNum, $areaNum, $houseType, $mapPoint, $departTel, $departMail, $departStreet, $departCommon,$proxyId = 0,$proxy = 0){
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => '',
            'flag'  => true
        );
        if($id > 0){
            $district = District::find($id);
            if(empty($district)){
                $result['code'] = 21026;
                return $result;
            }
        } else {
            $district = new District();
        }

        $rules = array(
            'name'              => ['required'],
            'address'           => ['required'],
            'mapPoint'          => ['required'], 
        );
        
        $messages = array(
            'name.required'          => 21027,   // 小区名称
            'address.required'       => 21028,   // 小区地址
            'mapPoint.required'      => 21029,   // 定位 
        );

        $validator = Validator::make(
            [
                'name'              => $name,
                'address'           => $address,
                'mapPoint'          => $mapPoint, 
            ], $rules, $messages);
        
        //验证信息
        if ($validator->fails()) {
            $messages = $validator->messages();
            
            $result['code'] = $messages->first();
            
            return $result;
        }

        $district->name             = $name;
        $district->address          = $address;
        $district->province_id      = !empty($provinceId) ? $provinceId : $proxy->province_id;///!!!
        $district->city_id          = !empty($cityId) ? $cityId : $proxy->city_id;
        $district->area_id          = !empty($areaId) ? $areaId : $proxy->area_id;
        $district->map_point        = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");;
        $district->map_point_str    = $mapPoint;
        $district->house_num        = $houseNum;
        $district->area_num         = $areaNum;
        $district->house_type       = $houseType;
        $district->depart_tel       = $departTel;
        $district->depart_mail      = $departMail;
        $district->depart_street    = $departStreet;
        $district->depart_common    = $departCommon;

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
                    $district->first_level = $proxy->pid;
                    $district->second_level = $proxy->id;
                    $district->third_level = 0;
                    break;
                case '3':
                    $district->first_level = $proxy->parentProxy['pid'];
                    $district->second_level = $proxy->parentProxy['id'];
                    $district->third_level = $proxy->id;
                    break;
                default:
                    $district->first_level = $proxy->id;
                    $district->second_level = 0;
                    $district->third_level = 0;
                    break;
            }
        }

        try{
            if(!empty($areaId)){
                $area = Region::find($areaId);
            }
            if(!empty($cityId)){
                $city = Region::find($cityId);
            }else{
                if(!empty($area['pid'])){
                    $city = Region::find($area['pid']);
                }
            }
            if(!empty($city['pid'])){
                $province = Region::find($city['pid']);
            }

            $city_code = $province->name . $city->name . $area->name; 
            $isOpenProperty = Config::get('app.is_open_property');
            //如果系统开启了物业功能的话 访问妙兜接口
            if($isOpenProperty){
                if(!empty($district->departid)){
                    $rs = DoorAccessService::getDistrict($district->departid);
                } else {
                    $rs = DoorAccessService::addDistrict($name, $city_code, $departTel, $departMail, $departStreet, $address, $departCommon);
                } 
                if($rs['status'] == 'success'){
                    $district->departid = $district->departid ? $district->departid : $rs['data']['departid'];
                    $district->save();
                } else {
                    foreach($rs['msg'] as $key => $value){
                        $msg .= '['.$key.']'.$value.' ';
                    }
                    $result['msg'] = $msg;
                    throw new Exception("Error Processing Request", 1); 
                }
            } else {
                $district->save();
            }
        } catch(Exception $e){
            $result['code'] = 30034;
            return $result;
        }
        return $result;
    }

    /**
     * 删除
     */
    public static function delete($id){
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );
        $district = District::whereIn($id);
        if (!$district) {
            $result['code'] = 60313;
            return $result;
        }
        //删除，相关信息, 待完善
        DB::beginTransaction();
        try {
            District::whereIn('id', $id)->delete();
            $puserId = \YiZan\Models\PropertyUser::whereIn('district_id', $id)->lists('id');
            \YiZan\Models\PropertyUser::whereIn('id', $puserId)->delete();
            \YiZan\Models\PropertyFee::whereIn('puser_id', $puserId)->delete();
            \YiZan\Models\DoorOpenLog::whereIn('puser_id', $puserId)->delete();
            \YiZan\Models\Repair::whereIn('puser_id', $puserId)->delete();

            DB::commit();

        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
        return $result;
    }

    /**
     * 前台获取
     */
    public static function get($userId, $id){
        $dbPrefix = DB::getTablePrefix();
        $data = District::where('district.id', $id)
                        ->leftJoin('property_user', function($join) use($userId) {
                            $join->on('property_user.district_id', '=', 'district.id')
                                 ->where('property_user.user_id', '=', $userId);
                        })
                        ->select('district.*','property_user.id as property_user_id','property_user.type','property_user.name as property_name','property_user.status','property_user.build_id','property_user.room_id')
                        ->addSelect(DB::raw("IF({$dbPrefix}property_user.district_id > 0, 1, 0) AS isUser"))
                        ->with('seller', 'province', 'city', 'area')
                        ->first();

        if ($data) {
            $data = $data->toArray();

            $houseData = Lang::get('api.house_type');
            $data['houseTypeName'] = $houseData[$data['houseType']];
            $data['sellerName'] = $data['seller']['name'];
            $data['isEnter'] = $data['sellerId'] > 0 ? 1 : 0;
            $floors = PropertyBuilding ::where('id',$data['buildId'])->first();


            $data['buildIds'] = $floors->id;

            $rooms = propertyRoom ::where('id',$data['roomId'])->first();
            $data['roomNum'] = $rooms->room_num;
            $data['owner'] = $data['propertyName'];


        }

        return $data;
    }
    
    /**
     * 小区列表
     */
    public static function getLists($name, $provinceId, $cityId, $areaId, $isUser, $isPropertyAdd, $isTotal, $page, $pageSize){
        $list = District::orderBy('id', 'DESC');

        if($name == true){
            $list->where('district.name', 'like', '%'.$name.'%');
        }

        if($provinceId == true){
            $list->where('district.province_id', $provinceId);
        }

        if($cityId == true){
            $list->where('district.city_id', $cityId);
        }

        if($areaId == true){
            $list->where('district.area_id', $areaId);
        }

        if($isUser == true){
            // $list->where('is_user', $isUser - 1);
        }

        if($isPropertyAdd == true){
            // $list->where('is_roperty_add', $isPropertyAdd - 1);
        }

        if($isTotal == true){
            $tablePrefix = DB::getTablePrefix();
            $list = $list->where('seller_id', '0')
                         ->select('district.*')
                         ->with('province', 'city', 'area')
                         ->get()
                         ->toArray();
            return $list;
        } else {
            $totalCount = $list->count();
            $list = $list->skip(($page - 1) * $pageSize)
                         ->take($pageSize)
                         ->with('province', 'city', 'area')
                         ->get()
                         ->toArray();
            return ["list" => $list, "totalCount" => $totalCount];
        }

    }

    /**
     * 获取小区总数
     * @return mixed
     */
    public static function count(){
        return District::count();
    }

}