<?php namespace YiZan\Services\Sellerweb;
use YiZan\Models\PropertyFee;
use DB;

class PropertyFeeService extends \YiZan\Services\PropertyFeeService {

	/**
	 * 检查资金
	 */
	public static function check($sellerId, $id){

		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '操作成功'
		); 
 
		$data = PropertyFee::whereIn('id', $id)
						   ->where('seller_id', $sellerId)
						   ->groupBy('build_id','room_id')
						   ->selectRaw('build_id,room_id')
						   ->get()
						   ->toArray(); 	 
		if(count($data) != 1){
			$result['code'] = 80314;
		}
		return $result;
	}

}
