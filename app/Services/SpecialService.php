<?php
namespace YiZan\Services;

use YiZan\Models\Special;

use YiZan\Utils\Time;
use YiZan\Utils\String;
use DB, Exception, Validator, Lang;

/**
 * 专题管理
 */
class SpecialService extends BaseService {

    /**
     * 获取列表
     */
    public function getLists($status){
        $lists = new Special();
        if($status == 1){
            $lists = $lists->where('status',1);
        }
        $lists = $lists->get()->toArray();
        return $lists;
    }

    /**
     * 根据id获取专题列表
     */
    public  function  getSpecial($id){
        return Special::find($id);
    }
    /**
     * 编辑专题
     */

    public static function saveSpecial($id, $name, $image,$content, $status = 1) {
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> Lang::get('api_system.success.handle')
        );


        $special = Special::find($id);
        if (!$special) {
            $result['code'] = 89000;
            return $result;
        }

        DB::beginTransaction();
        try{
            $special->name            = $name;
            $special->image           = $image;
            $special->content           = $content;
            $special->create_time    = UTC_TIME;
            $special->status          = $status;
            $special->save();
            DB::commit();
        }catch (Exception $e){
            DB::rollback();
            $result['code'] = 99999;
        }
        return $result;
    }

}
