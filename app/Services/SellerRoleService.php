<?php 
namespace YiZan\Services;

use YiZan\Models\SellerRole;
use YiZan\Models\Seller;
use YiZan\Models\SellerAdminUser;
use YiZan\Models\SellerRoleAccess;
use YiZan\Utils\Time;
use DB, Exception, Cache;

/**
 * 管理员组
 */
class SellerRoleService extends BaseService {
	/**
	 * 管理员组列表
	 * @param  int $page     页码
	 * @param  int $pageSize 每页数
	 * @return array          管理员组信息
	 */
	public static function getlist($sellerId,$page, $pageSize) {

		$list = SellerRole::where('seller_id',$sellerId)->orderBy('id');

		$totalCount = $list->count();

		$list = $list->where("status",1)->orderBy('name', 'ASC')
					 ->skip(($page - 1) * $pageSize)
					 ->take($pageSize)
					 ->get()
					 ->toArray();

		foreach ($list as $key => $value) {
			$has = SellerAdminUser::where('rid', $value['id'])->first();
			if($has)
			{
				$list[$key]['canDelete'] = 0;
			}
			else
			{
				$list[$key]['canDelete'] = 1;
			}
			
		}
		return ["list" => $list, "totalCount" => $totalCount];
	}

	/**
	 * 添加管理员组
	 * @param  string $name   名称
	 * @param  string $access 组权限
	 * @return array   添加信息
	 */
	public static function create($sellerId,$name, $access) {
		$result = array('code' => self::SUCCESS, 'data' => null, 'msg' => '');

		$sellerRole = new SellerRole();

		if ($name == false) {
			$result['code'] = 10301; // 名称不能为空

			return $result;
		}

		if ($access == false) {
			$result['code'] = 10302; // 请设置组权限

			return $result;
		}

		$sellerRole->name   = $name;
		$sellerRole->status = 1;
        $sellerRole->seller_id 	 = $sellerId;

		$sellerRole->save();

		foreach ( $access as $value ) {
			$roleAccess = new SellerRoleAccess();

			$roleAccess->rid    	 = $sellerRole->id;
			$roleAccess->controller  = $value["controller"];
			$roleAccess->action 	 = $value["action"];

            $roleAccess->save();
		}

		return $result;
	}

	/**
	 * 根据id获取管理员组
	 * @param  int $id 管理员编号
	 * @return array   管理员组信息
	 */
	public static function getById($sellerId,$id) {
		return SellerRole::where('id', $id)
                        ->where('seller_id',$sellerId)
						->with('access')
						->first();
	}

	/**
	 * 更新管理员组
	 * @param  int    $id     管理员编号
	 * @param  string $name   名称
	 * @param  string $access 组权限
	 * @return array   更新信息
	 */
	public static function update($id, $name, $access) {
		$result = array('code' => self::SUCCESS, 'data' => null, 'msg' => '');

		if ($name == false) {
			$result['code'] = 10301; // 名称不能为空

			return $result;
		}

		if ($access == false) {
			$result['code'] = 10302; // 请设置组权限

			return $result;
		}

		SellerRole::where('id', $id)
				 ->update(["name" => $name]);

		SellerRoleAccess::where("rid", $id)
					   ->delete();

		foreach ( $access as $value ) {
			$roleAccess = new SellerRoleAccess();

			$roleAccess->rid    = $id;
			$roleAccess->controller  = $value["controller"];
			$roleAccess->action = $value["action"];

            $roleAccess->save();
		}

		Cache::forget('_seller_controller_action_navs_'.$id);

		return $result;
	}

	/**
	 * 删除管理员组
	 * @param  int $id 管理员编号
	 * @return array   删除信息
	 */
	public static function delete($id) {
		$result = ['code' => 0, 'data' => null, 'msg' => ""];
        $ids = $id;
		foreach ($ids as $key => $v) {
			$sellerUser = SellerAdminUser::where('rid', $v)->first();

			if (empty($sellerUser)) {
				// AdminRole::where('id', $id)->update(array("status" => 0));
				SellerRole::where('id', $v)->delete();
			} else {
				$result['code'] = 60204;
			}

			Cache::forget('_seller_controller_action_navs_'.$v);
		}
		return $result;
	}
}
