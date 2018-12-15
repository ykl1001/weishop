<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\RegionService;
use Input;
/**
 * 开通城市管理
 */
class CityController extends BaseController 
{
   	/**
     * 城市列表
	 */
	public function lists()
    {
        $data = RegionService::getSystemServiceCitys 
        (
            $this->request('provinceId'),
            $this->request('cityId'),
            $this->request('areaId'),
            $this->request('cityName'),
            max((int)$this->request('page'), 1),
            max((int)$this->request('pageSize'), 20),
            (int)$this->request('nonew')
        );

		return $this->outputData($data);
    }
    /**
     * 添加开通城市
     */
    public function create()
    {
        $result = RegionService::create(intval($this->request('cityId')), intval($this->request('sort')));
        
        return $this->output($result);
    }
    /**
     * 删除开通城市
     */
    public function delete()
    {
        $result = RegionService::delete(
            $this->request('cityId')
        );
        
        return $this->output($result);
    }

    /**
     * 设置默认开通城市
     */
    public function setdefault() {
        $result = RegionService::setDefault(intval($this->request('id')));
        return $this->outputCode(0);
    }

    /**
     * 开通的城市列表
     */
    public function getcitylists()
    {
        $data = RegionService::getSystemOpenCitys();

        return $this->outputData($data);
    }

    /**
     * 一键开通城市
     */
    public function open(){
        $result = RegionService::open();
        return $this->outputCode(0);
    }

}