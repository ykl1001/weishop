<?php 
namespace YiZan\Services;

use YiZan\Models\SystemTag;
use YiZan\Utils\Time;

use Lang, Validator,DB;

/**
 * 标签分类管理
 */
class SystemTagService extends BaseService
{

	/**
	 * [getList 获取标签分类内列表]
	 * @param  [type] $page     [分页]
	 * @param  [type] $pageSize [分页大小]
	 * @return [type]           [description]
	 */
	public static function getList($status,$name = "") {

        $list = SystemTag::orderBy('sort','asc')->orderBy('id', 'desc');
        if(!empty($status))
        {
            $list = $list->where('status', $status);
        }
        if(!empty($name))
        {
            $list = $list->where('name','like', '%'.$name.'%');
        }
        $list = $list->with('systemTagList')->get()->toArray();

        return $list;
	}

   /**
     * [save ]
     * @param  [type] $id   [编号]
     * @param  [type] $name [分类名称]
     * @param  [type] $sort  [排序]
     * @param  [type] $status  [状态]
     * @return [type]           [description]
     */
    public static function save($id, $name, $sort, $status)
    {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg' => null,
        );

        $rules = array(
            'name'          => ['required'],
            'sort'          => ['required']
        );
        
        $messages = array (
            'name.required'     => 31000,   // 请填写分类名称
            'sort.required'     => 31001,   // 请输入排序
        );

        $validator = Validator::make([
                'name'          => $name,
                'sort'          => $sort,
            ], $rules, $messages);
        
        //验证信息
        if ($validator->fails()) {
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        if($id > 0){
        	$tag = SystemTag::find($id);
        	$result['msg'] = Lang::get('api.success.update_info'); //更新成功
        }else{
        	$tag = new SystemTag();
            $result['msg'] = Lang::get('api.success.add');  //添加成功
        }

        $tag->name = $name;
        $tag->sort = $sort;
        $tag->status = $status;
        $tag->create_time = UTC_TIME;
        $tag->save();

        return $result;

    } 

    /**
     * [get 获取单个分类]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public static function get($id) {
    	return SystemTag::find($id);
    }


    public static function delete($id) 
    {
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];
        SystemTag::whereIn('id', $id)->delete(); 
        return $result;
    }

}
