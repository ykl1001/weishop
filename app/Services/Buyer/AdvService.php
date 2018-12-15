<?php 
namespace YiZan\Services\Buyer;

use YiZan\Models\Adv;
use YiZan\Models\AdvPosition;

use YiZan\Utils\String;
use DB, Validator;
/**
 * 获取广告
 */
class AdvService extends \YiZan\Services\ArticleService{

	public static function getAdv($code) 
    {

    	$positionId = AdvPosition::where('code', $code)->pluck('id');
        return Adv::where("position_id", $positionId)->where('status', 1)->first();
    }
}
