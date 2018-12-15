<?php 
namespace YiZan\Services;
use YiZan\Models\PropertySystem;
use YiZan\Utils\String;
use DB, Validator;
/**
 * 文章
 */
class PropertySystemService extends BaseService
{
    /**
     * 公告列表
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          文章信息
     */
    public static function getList($sellerId,$name, $page, $pageSize)
    {
        $list = PropertySystem::orderBy('id', 'desc')->where('seller_id',$sellerId);
        if($name == true)
        {
            $list->where("name", "LIKE", "%{$name}%");
        }
        $totalCount = $list->count();
        $list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();
        return ["list"=>$list, "totalCount"=>$totalCount];
    }


    /**
     * 添加黄页
     */
    public static function save($sellerId,$id,$sort, $name, $type,$status)
    {
        $result = array(
            'code'	=> self::SUCCESS,
            'data'	=> null,
            'msg'	=> ''
        );

        $rules = array(
            'name'       => ['required']
        );

        $messages = array
        (
            'name.required'	    => 10107	// 请输入详细
        );

        $validator = Validator::make(
            [
                'name'    => $name
            ], $rules, $messages);

        //验证信息
        if ($validator->fails())
        {
            $messages = $validator->messages();

            $result['code'] = $messages->first();

            return $result;
        }
        if($id > 0){
            $yellowpages = PropertySystem::where('id',$id)->first();
        }else{
            $yellowpages = new PropertySystem();
        }
        $yellowpages->seller_id = $sellerId;
        $yellowpages->sort     = !empty($sort) ? $sort : 100;
        $yellowpages->name   = $name;
        $yellowpages->status 	 = $status;
        $yellowpages->create_time 	 = UTC_TIME;
        $yellowpages->type     = $type;
        $yellowpages->save();
        return $result;
    }

    /**
     * 获取黄页
     * @param  int $id 黄页id
     */
	public static function getById($sellerId,$id)
    {
		return PropertySystem::where('id', $id)
            ->where('seller_id', $sellerId)
            ->first();
	}
    /**
     * 删除黄页
     * @param string  $ids 黄页id
     * @return array   删除结果
     */
	public static function delete($sellerId,$ids)
    {
        $result =
            [
                'code'	=> 0,
                'data'	=> null,
                'msg'	=> ""
            ];

        PropertySystem::where('seller_id', $sellerId)
            ->whereIn("id", $ids)
            ->delete();

        return $result;
	}



}
