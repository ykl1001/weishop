<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\SystemTagListService; 
use Lang, Validator;

class SystemTagController extends BaseController {
	
	/**
	 * 获取平台商品分类
	 */
	public function lists(){  
		$data = SystemTagListService::lists(
            $this->request('status')
        );
		return $this->outputData($data);
	}
	
	/**
	 * 商家选择平台分类
	 * @return [type] [description]
	 */
	public function checktag() {
		$data = SystemTagListService::checktag(
            intval($this->request('tagPid')),
            intval($this->request('tagId'))
        );
		return $this->outputData($data);
	}

}