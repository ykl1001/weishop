<?php 
namespace YiZan\Services\Buyer;

use YiZan\Models\IndexNav;
use YiZan\Services\RegionService;
use Lang;
/**
 * 首页底部导航管理
 */
class IndexNavService extends \YiZan\Services\IndexNavService {
	
	/**
	 * 获取状态为1的所有导航
	 */ 
	public static function getLists($onlySystem, $cityId){
		if($onlySystem){
			//只取系统导航菜单
			$list = IndexNav::where('city_id', 0)
							->where('status', 1)
							->orderBy('sort', 'ASC')
							->take(5)
							->get()
							->toArray();

		} else {
			$list = IndexNav::whereRaw("((city_id = {$cityId}) or (city_id = 0))")
							->where('status', 1)
						 	->take(5)
                            ->orderBy('city_id','DESC')
						 	->orderBy('sort', 'ASC')
						 	->get()
						 	->toArray();
		}
		return $list;
	}

    /**
     * 获取状态为1的所有导航
     */
    public static function getIndex(){
        $cityarrs = RegionService::getOpenCityByIp(CLIENT_IP);

        if(empty($cityarrs['id'])){
            return '';
        }

        $cityId = $cityarrs['id'];
        $list = IndexNav::whereRaw("((city_id = {$cityId}) or (city_id = 0))")
            ->where('status', 1)
            ->where('is_index',1)
            ->take(1)
            ->orderBy('city_id', 'DESC')
            ->orderBy('sort', 'ASC')
            ->first();

        if(!empty($list)){
            $list = $list->toArray();
            $url = Lang::get('api_system.index_link.'.$list['type']);
            $list['url'] = $url;
        }

        return $list;
    }

}
