<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

class UserAuthController extends BaseController {
	/**
	 * 检测会员是否登录
	 */
	public function __construct() {
		parent::__construct();

        if (!$this->user) {
			//return $this->outputCode(99996);
		}
	}
}