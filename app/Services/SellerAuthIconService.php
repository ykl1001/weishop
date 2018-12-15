<?php 
namespace YiZan\Services;

use YiZan\Models\SellerAuthIcon;
use YiZan\Models\SellerIconRelated;
use YiZan\Models\Seller;
use YiZan\Utils\String;
use DB, Validator, Lang;

/**
 * Class SellerAuthIconService 商家认证图标
 * @package YiZan\Services
 */
class SellerAuthIconService extends BaseService 
{

	/**
     * 商家认证图标列表
     * @return array
     */
	public static function getList($page, $pageSize) 
    {
        $list = SellerAuthIcon::with('seller')->orderBy('sort', 'ASC');
        $totalCount = $list->count();
        $list = $list->skip($pageSize * ($page - 1))
                        ->take($pageSize)
                        ->get()
                        ->toArray();
         return ['list' => $list, 'totalCount' => $totalCount];
	}

    /**
     * 商家认证图标详情
     * @return array
     */
    public static function get($id)
    {
        $data = SellerAuthIcon::find($id);
        return $data;
    }

    /**
     * 保存商家认证图标
     * @param int $id 编号
     * @param string $name 名称
     * @param string $icon 图标
     * @param int $status 状态
     * @param int $sort 排序
     * @return array
     */
    public static function save($id, $name, $icon, $status, $sort)
    {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> Lang::get('api_system.success.handle')
		);

        if ($name == '') {
            $result['code'] = 10301;
            return $result;
        }

        if ($icon == '') {
            $result['code'] = 30903;
            return $result;
        }

        if ($id > 0) {
            $authIcon = SellerAuthIcon::where('id', $id)->first();
        } else {
            $authIcon = new SellerAuthIcon();
        }

        $authIcon->name      = $name;
        $authIcon->icon      = $icon;
        $authIcon->status    = 1;
        $authIcon->sort 	 = $sort > 0 ? $sort : 100;
        $authIcon->save();
        
        return $result;
    }
    
    /**
     * 删除商家分类
     * @param array  $ids 商家分类id
     * @return array   删除结果
     */
	public static function delete($ids)
    {
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api_system.success.delete')
		];
        self::replaceIn(implode(',', $ids));
        $iconRelated = SellerIconRelated::whereIn('icon_id', $ids)->first();
        if (!$iconRelated) {
            SellerAuthIcon::whereIn('id', $ids)->delete();
        }
		return $result;
	}


}
