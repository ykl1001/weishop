<?php 
namespace YiZan\Services;

use YiZan\Models\SystemTagList;
use YiZan\Models\SystemTag;
use YiZan\Utils\Time;

use Lang, Validator;

/**
 * 标签列表
 */
class SystemTagListService extends BaseService
{
    /**
     * 【staff端】
     * [lists 获取商品标签分类列表]
     * @param  [type] $status [状态]
     * @return [type]         [description]
     */
    public function lists($status) {
        $lists =SystemTagList::orderBy('sort', 'asc');

        if($status)
        {
            $lists->where('status', $status);
        }


        $lists = $lists->with(['tag' => function($query){
                                $query->orderBy('sort', 'asc')
                                    ->where('status', 1);
                            }])
                        ->get()
                        ->toArray();
        return $lists;
    }
	
	

    /**
     * 【staff端】
     * [checktag  商家在添加和编辑商品时候选择的分类]
     * @param  [type] $tagPid [description]
     * @param  [type] $tagId  [description]
     * @return [type]         [description]
     */
    public function checktag($tagPid, $tagId) {
        return SystemTagList::with('pid')->where('pid', $tagPid)->find($tagId);
    }

	/**
	 * [getList 获取标签分类内列表]
	 * @param  [type] $page     [分页]
	 * @param  [type] $pageSize [分页大小]
	 * @return [type]           [description]
	 */
	public static function getList($status) {
		$list = SystemTagList::where('pid',0);

        if(!empty($status))
        {
            $list = $list->where('status', $status);
        }

        $list = $list->with(['childs' => function($query){
                            $query->with('tag','useTag');
                     }])
                     ->orderBy('sort', 'ASC')
                     ->get()
                     ->toArray();

        return $list;
	}
	
	 public function getList2($status) {
        $list = SystemTagList::where('pid',0);

        if(!empty($status))
        {
            $list = $list->where('status', $status);
        }

        $list = $list->orderBy('sort', 'ASC')
                     ->get()
                     ->toArray();

        return $list;
    }

    public static function getListItem($status,$pid = 0,$page,$pageSize) {

        $list = SystemTagList::where('pid',$pid);

        if(!empty($status))
        {
            $list = $list->where('status', $status);
        }
        if($pid <= 0 ){
            $list = $list->with('hasOneItem');
        }else{
            $list = $list->with('useTag');
        }
        $totalCount = $list->count();
        $list =  $list->orderBy('sort', 'ASC')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();

        return ["list"=>$list, "totalCount"=>$totalCount];
    }

   /**
    * [save 保存标签]
    * @param  [type] $id          [编号]
    * @param  [type] $name        [标签名称]
    * @param  [type] $sort        [标签排序]
    * @param  [type] $status      [标签状态]
    * @param  [type] $pid         [标签级别，顶级分类无标签分类编号和图标]
    * @param  [type] $systemTagId [标签分类编号]
    * @param  [type] $img         [图标]
    * @return [type]              [description]
    */
    public static function save($id, $name, $sort, $status, $pid, $systemTagId, $img)
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
            'name.required'     => 31100,   // 请填写分类名称
            'sort.required'     => 31101,   // 请输入排序
            'status.required'   => 31102,   // 请选择状态
            'pid.required'      => 31103,   // 请选择所属标签
        );

        $validator = Validator::make([
                'name'          => $name,
                'sort'          => $sort,
                'status'        => $status,
                'pid'           => $pid,
            ], $rules, $messages);
        
        //验证信息
        if ($validator->fails())
        {
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }


        if($id > 0)
        {
        	$tag = SystemTagList::find($id);
        	$result['msg'] = Lang::get('api.success.update_info');  //更新成功

            //更新时 二级分类可以切换成一级，如果一级下面存在二级 不能从一级切换到二级 需要删除一级下面的所有子分类才可切换
            if(SystemTagList::where('pid', $tag->id) && $pid != $tag->pid)
            {
                $result['code'] = 31106;  //该分类下存在子分类，不允许切换所属标签级别
                return $result;
            }

        }
        else
        {
        	$tag = new SystemTagList();
            $result['msg'] = Lang::get('api.success.add');  //添加成功
        }

        //如果是顶级分类 清空分类和清空上传图标
        if($pid == 0)
        {
            $tag->system_tag_id = 0;
            $tag->img = null;
        }
        else
        {
            //如果是二级分类 可以不选择分类和上传图标
            // if(!SystemTag::find($systemTagId))
            // {
            //     $result['code'] = 31104;  //请选择正确的标签分类
            //     return $result;
            // }

            // if(empty($img))
            // {
            //     $result['code'] = 31105;  //请上传标签图标
            //     return $result;
            // }
        }

        $tag->pid = $pid;
        $tag->name = $name;
        $tag->sort = $sort;
        $tag->status = $status;
        $tag->img = $img;
        $tag->system_tag_id = $systemTagId;
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
    	return SystemTagList::where('status',1)->find($id);
    }

    /**
     * [secondLevel 通过一级分类获取二级分类]
     * @param  [type] $pid [父级编号]
     * @return [type]      [description]
     */
    public static function secondLevel($pid) {
        return SystemTagList::where('pid', $pid)->where('status',1)->get()->toArray();
    }


    public static function delete($id) 
    {
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];

        SystemTagList::whereIn('id', $id)->delete();
        return $result;
    }

}
