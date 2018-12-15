<?php 
namespace YiZan\Services;
use YiZan\Models\YellowPages;
use YiZan\Utils\String;
use DB, Validator;
/**
 * 文章
 */
class YellowPagesService extends BaseService
{
    /**
     * 公告列表
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          文章信息
     */
    public static function getList($sellerId,$name, $page, $pageSize)
    {
        $list = YellowPages::orderBy('id', 'desc')->where('seller_id',$sellerId);
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
    public static function save($sellerId,$id,$mobile, $name,$status)
    {
        $result = array(
            'code'	=> self::SUCCESS,
            'data'	=> null,
            'msg'	=> ''
        );

        $rules = array(
            'mobile' => ['required'],
            'name'       => ['required']
        );

        $messages = array
        (

            'mobile.required'	    => 10102,	// 请输入标题
            'name.required'	    => 10107	// 请输入详细
        );

        $validator = Validator::make(
            [
                'mobile'      => $mobile,
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
            $yellowpages = YellowPages::where('id',$id)->first();
        }else{
            $yellowpages = new YellowPages();
        }
        $yellowpages->seller_id = $sellerId;
        $yellowpages->mobile     = $mobile;
        $yellowpages->name   = $name;
        $yellowpages->status 	 = $status;
        $yellowpages->create_time 	 = UTC_TIME;
        $yellowpages->save();
        return $result;
    }

    /**
     * 获取黄页
     * @param  int $id 黄页id
     */
	public static function getById($sellerId,$id)
    {
		return YellowPages::where('id', $id)
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

        YellowPages::where('seller_id', $sellerId)
            ->whereIn("id", $ids)
            ->delete();

        return $result;
	}



}
