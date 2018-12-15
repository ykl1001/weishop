<?php 
namespace YiZan\Services\System;
 
use YiZan\Models\IndexNav;
use Validator;
/**
 * 首页底部导航管理
 */
class IndexNavService extends \YiZan\Services\IndexNavService {
	  
	/**
	 * 获取列表
	 */
	public static function getLists($name, $cityId, $status, $page, $pageSize){
		$list = IndexNav::orderBy('id', 'DESC');

		if($name == true){
			$list->where('name', $name);
		}

		if($cityId > 0){
			$list->where('city_id', $cityId);
		}

		if($status > 0){
			$list->where('status', $status - 1);
		}

        $totalCount = $list->count();
        $list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize) 
            ->with('city')
            ->get()
            ->toArray();

        return ["list"=>$list, "totalCount"=>$totalCount];
	}

	/**
	 * 添加
	 */
	public static function save($id, $name, $cityId, $icon, $type, $sort, $isSystem, $status,$isIndex){

		$result = array('code' => self::SUCCESS,
						'data' => null,
						'msg' => '');

		$rules = array(
		   'name' 				 => ['required', 'max:5'],
		   'cityId'          	 => ['required'],
		   'icon'              	 => ['required'],
		   'type'         		 => ['required'],  
		);

		$messages = array(
			'name.required' 				=> 33001,
			'name.max' 						=> 33002,
			'cityId.required'               => 33003,
			'icon.required'     			=> 33004, 
			'type.required'     			=> 33005, 
		);

		$validator = Validator::make([
			'name' 					=> $name,
			'cityId'                => $cityId,
			'icon' 					=> $icon,
			'type'					=> $type, 
		], $rules, $messages);

		//验证信息
		if ($validator->fails()) {
			$messages       = $validator->messages();
			$result['code'] = $messages->first(); 
			return $result;
		}

		if($id > 0){
			$indexNav = IndexNav::find($id);
			if(empty($indexNav)){
				$result['code'] = 33006;
				return $result;
			}
			$indexNav->name = $name;
			$indexNav->city_id = $cityId;
			$indexNav->icon = $icon;
			$indexNav->type = $type;
			$indexNav->sort = $sort;
            $indexNav->is_system = $isSystem;
            $indexNav->is_index = $isIndex;
			$indexNav->status = $status;
		} else { 
			$indexNav = new IndexNav();
			$indexNav->name = $name;
			$indexNav->city_id = $cityId;
			$indexNav->icon = $icon;
			$indexNav->type = $type;
			$indexNav->sort = $sort;
			$indexNav->is_system = $isSystem;
            $indexNav->is_index = $isIndex;
            $indexNav->status = $status;
		}
		$indexNav->save();
		return $result;
	}

	public static function getById($id){
		$indexNav = IndexNav::where('id', $id)
							->first();
		return $indexNav;
	}

    public static function delete($id){
        $result =
            [
                'code'	=> 0,
                'data'	=> null,
                'msg'	=> '删除成功'
            ];
        IndexNav::whereIn('id', $id)
        		->where('is_system', 0)
            	->delete();
        return $result;
    }
}
