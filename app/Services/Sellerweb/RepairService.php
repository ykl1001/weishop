<?php namespace YiZan\Services\Sellerweb;

use YiZan\Models\Seller;
use YiZan\Models\PropertyBuilding;
use YiZan\Models\District;
use YiZan\Models\PropertyUser;
use YiZan\Models\PropertyRoom;
use YiZan\Models\User;
use YiZan\Models\Repair;
use YiZan\Models\RepairType;
use YiZan\Models\SellerStaff;

use YiZan\Utils\String;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use Illuminate\Database\Query\Expression;
use DB, Lang, Exception;


class RepairService extends \YiZan\Services\RepairService {


	public static function get($id){
		$data = Repair::where('id', $id)
					 ->with('build', 'room', 'puser', 'types','staff')
		             ->first();

		return $data;
	}


}
