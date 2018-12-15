<?php namespace YiZan\Services\Proxy;

use YiZan\Models\Region;
use YiZan\Models\District;
use YiZan\Models\Area;
use YiZan\Models\Proxy;
use Exception, DB, Lang, Validator, App, Config;

class DistrictService extends \YiZan\Services\DistrictService{

	/**
	 * 小区列表
	 */
	public static function getLists($proxy,$name, $provinceId, $cityId, $areaId, $isUser, $isPropertyAdd, $isTotal, $page, $pageSize){

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

        if($proxy->pid){
            $parentProxy = Proxy::find($proxy->pid);
        }
        switch ($proxy->level) {
            case '2':
                $list->where('first_level',$proxy->pid);
                $list->where('second_level',$proxy->id);
                break;
            case '3':
                $list->where('first_level',$parentProxy->pid);
                $list->where('second_level',$parentProxy->id);
                $list->where('third_level',$proxy->id);
                break;
            default:
                $list->where('first_level',$proxy->id);
                break;
        }
        if($isTotal == true){
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

}