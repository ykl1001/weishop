<?php namespace YiZan\Services\Buyer;

use YiZan\Models\PayItem; 
use DB, Exception,Validator, Lang;


class PayItemService extends \YiZan\Services\PayItemService 
{
	/**
	 * 收费项目列表
	 * @param $sellerId int 物业编号 
	 */
	public static function getLists($sellerId) {
		$list = PayItem::where('seller_id', $sellerId);  
        return $list->get()->toArray(); 
	}	  

}
