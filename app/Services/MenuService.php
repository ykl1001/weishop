<?php
namespace YiZan\Services;

use YiZan\Models\Menu;
use DB, Exception,Validator;

/**
 * 管理员组
 */
class MenuService extends BaseService {

    /**
     * 列表
     * @param string $clientType 类型
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          广告信息
     */
    public static function getList($type,$page,$pageSize)
    {
        //刷新活动
        $list = Menu::orderBy('id', 'desc')->where("platform_type",$type);

        $totalCount = $list->count();
        $list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->with('city')
            ->get()
            ->toArray();

        return ["list"=>$list, "totalCount"=>$totalCount];
    }


    /**
     * 列表
     * @param string $clientType 类型
     * @return array          广告信息
     */
    public static function getWapList($cityId,$platformType)
    {

        $list = Menu::where('status',1) ->orderBy('sort', 'asc');
	
        if($platformType != ''){
            $list->where('platform_type',$platformType);
        }else{
            $list->where(
                function($query){
                    $query->whereNull('platform_type')->orwhere('platform_type','!=',"oneself");
                }
            );
        }
        $list = $list->where(function($query) use ($cityId){
                        $query->where('city_id',$cityId)->orwhere('city_id',0);
                    })->get()
                    ->toArray();

        foreach ($list as $key => $val) {
            $url = '';
            switch ($val['type']) {
                case 1 :
                    if($val['arg'] == -1){
                        $url = u('wap#Seller/cates');
                    }else{
                        $url = u('wap#Seller/index',['id'=>$val['arg']]);
                    }
                    break;
                case 3 : $url = u('wap#Goods/detail',['goodsId'=>$val['arg']]); break;
                case 4 : $url = u('wap#Seller/detail',['id'=>$val['arg']]); break;
                case 6 : $url = u('wap#Goods/detail',['id'=>$val['arg']]); break;
                case 7 : $url = u('wap#Article/detail',['id'=>$val['arg']]); break;
                case 8 : $url = u('wap#Property/index',['id'=>1]); break;
                case 9 : $url = u('wap#UserCenter/signin'); break;
                case 10 : $url = u('wap#Property/livipayment'); break;
                case 11 : $url = u('wap#Oneself/index'); break;
                case 12 : $url = u('wap#Integral/index'); break;
                default: $url = $val['arg']; break;
            }
            $list[$key]['url'] = $url;
        }
        return $list;
    }


    /**
     * 根据编号获取活动
     * @param  integer $id 活动编号
     * @return array       活动信息
     */
    public static function getById($id) {
        if($id < 1){
            return false;
        }
        return Menu::find($id);
    }

    /**
     * 删除活动
     */
    public static function delete($id){
        if($id < 1){
            return false;
        }

        $result = array(
            'code'	=> 0,
            'data'	=> '',
            'msg'	=> ''
        );

        return Menu::whereIn('id', $id)->delete();
    }

    /**
     * 修改活动
     */
    public static function update($id,$name,$cityId,$menuIcon,$type,$arg,$sort,$status,$platformType){
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );
        if($type == 8){
            $rules = array(
                'name'          => ['required'],
                'cityId'        => ['required'],
                'menuIcon'        => ['required'],
                'type'        => ['required']
            );
            $messages = array(
                'name.required'     => 10301,
                'cityId.required'   => 41420,
                'menuIcon.required'    => 41313,
                'type.required'        => 41421
            );
            $validator = Validator::make([
                'name'          => $name,
                'cityId'        => $cityId,
                'menuIcon'    => $menuIcon,
                'type'        => $type
            ], $rules, $messages);
        }else{
            $rules = array(
                'name'          => ['required'],
                'cityId'        => ['required'],
                'menuIcon'        => ['required'],
                'type'        => ['required']
                //'arg'        => ['required']
            );
            $messages = array(
                'name.required'     => 10301,
                'cityId.required'   => 41420,
                'menuIcon.required'    => 41313,
                'type.required'        => 41421
                //'arg.required'        => 41422,
            );
            $validator = Validator::make([
                'name'          => $name,
                'cityId'        => $cityId,
                'menuIcon'    => $menuIcon,
                'type'        => $type
                //'arg'        => $arg
            ], $rules, $messages);
        }



        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
//        if($type < 8 && $arg == ""){
//            $result['code'] = 70108;
//            return $result;
//        }
        $sort = empty($sort) ? 100:$sort;

        if(!empty($id)){
            $Menu = Menu::where('id',$id)->first();
            $Menu->id = $id;
            $Menu->name = $name;
            $Menu->city_id = $cityId;
            $Menu->menu_icon = $menuIcon;
            $Menu->arg = $arg;
            $Menu->type = $type;
            $Menu->sort = $sort;
            $Menu->status = $status;
            $Menu->platform_type = $platformType;
            $Menu->save();
        }else{
            $Menu = new Menu();
            $Menu->name = $name;
            $Menu->city_id = $cityId;
            $Menu->menu_icon = $menuIcon;
            $Menu->arg = $arg;
            $Menu->type = $type;
            $Menu->sort = $sort;
            $Menu->status = $status;
            $Menu->create_time = UTC_TIME;
            $Menu->platform_type = $platformType;
            $Menu->save();
        }

        return $result;
    }

    public function updateStatus($id,$status){
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];

        if ($id < 1) {
            $result['code'] = 30214;
            return $result;
        }
        $status = $status > 0 ? 1 : 0;

        Menu::where('id',$id)->update(['status' => $status]);
        return $result;
    }

}
