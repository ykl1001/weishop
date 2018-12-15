<?php 
namespace YiZan\Http\Controllers\Api\Staff;

use YiZan\Services\Staff\RegionService;
use Input;
/**
 * 开通城市管理
 */
class CityController extends BaseController 
{
	/**
     * 开通城市列表
	 */
	public function getCityList()
    {
        $data = RegionService::getCityList(
        	$this->request('pid'),
        	$this->request('level')
        	);  
		return $this->outputData($data);
    }

}