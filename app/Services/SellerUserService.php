<?php namespace YiZan\Services;

use YiZan\Models\SellerAdminUser;
use YiZan\Models\Seller;
use YiZan\Utils\Time;
use YiZan\Utils\String;
use DB, Validator, Exception, Lang,Config;

class SellerUserService extends BaseService
{
    /**
     * 管理员列表
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          管理员信息
     */
    public static function getAdminUserlist($sellerId, $page, $pageSize) {
        $list = SellerAdminUser::where('relation_id',$sellerId)->with('role.access');

        $totalCount = $list->count();

        $list = $list->orderBy('id', 'DESC')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();

        return ["list"=>$list, "totalCount"=>$totalCount];
    }
    /**
     * 根据登录名称获取管理员
     * @param  string $name 登录名称
     * @return array          管理员信息
     */
    public static function getByName($name)
    {
        return SellerAdminUser::where('name', $name)
            ->with('role.access')
            ->first();
    }
    /**
     * 添加管理员
     * @param  string $name 登录名称
     * @param  string $pwd 密码
     * @param  int $rid 角色编号
     */
    public static function saveAdminUser($id, $name, $pwd, $rid,$relation_id)
    {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );


        if(strlen($name) > 20){
            $result['code'] = 10118;
            return $result;
        }

        if ($id > 0) {//管理员不存在
            $adminUser = SellerAdminUser::find($id);
            if (!$adminUser) {
                $result['code'] = 10704;
                return $result;
            }

            $existsName = SellerAdminUser::where('id','!=',$id)->where('name',$name)->first();
            if($existsName){
                $result['code'] = 30902;
                return $result;
            }

            $adminUser->name        = $name;


        } else {
            $adminUser = new SellerAdminUser;
            $adminUser->name        = $name;
            $adminUser->create_time = UTC_TIME;
            $adminUser2 = SellerAdminUser::where('name',$name)->first();
            if(!empty($adminUser2)){
                $result['code'] = 30902;
                return $result;
            }
        }



        if (!empty($pwd)) {
            $crypt  = String::randString(6);
            $pwd    = md5(md5($pwd) . $crypt);

            $adminUser->crypt   = $crypt;
            $adminUser->pwd     = $pwd;
        }

        DB::beginTransaction();
        try {
            $adminUser->rid = $rid;
            $adminUser->relation_id = $relation_id;
            $adminUser->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
        return $result;
    }

    /**
     * 根据id获取管理员
     * @param  int $id 登录名称
     * @return array   管理员信息
     */
    public static function getById($sellerId,$id) {
        return SellerAdminUser::where('relation_id',$sellerId)->where('id', $id)
            ->with('role.access')
            ->first();
    }

    /**
     * 删除管理员
     * @param  int $id 管理员编号
     */
    public static function deleteAdminUser($id) {
        SellerAdminUser::whereIn('id', $id)->delete();
    }

    /**
     * 更新密码
     * @param  int $id 管理员编号
     * @param  string $oldPwd 旧密码
     * @param  string $newPwd 新密码
     */
    public static function updateAdminUserPassword($id, $oldPwd, $newPwd) {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => Lang::get('api.success.create_user_repwd')
        );

        $rules = array(
            'oldPwd' => ['required'],
            'newPwd' => ['required'],
        );

        $messages = array (
            'oldPwd.required'       => 10108,   // 原密码不正确
            'newPwd.required'       => 10109,   // 新密码不能为空
        );

        $validator = Validator::make([
            'oldPwd' => $oldPwd,
            'newPwd' => $newPwd,
        ], $rules, $messages);

        //验证信息
        if ($validator->fails()) {
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        $adminUser = SellerAdminUser::find($id);
        if (!$adminUser) {
            $result['code'] = 10102;
            return $result;
        }

        //登录密码错误
        if ($adminUser->pwd != md5(md5($oldPwd) . $adminUser->crypt)) {
            $result['code'] = 10108;
            return $result;
        }

        $crypt  = String::randString(6);
        $pwd    = md5(md5($newPwd) . $crypt);

        $adminUser->crypt   = $crypt;
        $adminUser->pwd     = $pwd;
        $adminUser->save();
        return $result;
    }


    /**
     * 根据登录名称获取管理员
     * @param  string $name 登录名称
     * @return array          管理员信息
     */
    public static function updatesql($adminId,$sysVersion)
    {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => Lang::get('api.success.updatesql')
        );
        //if(in_array($adminId,'Project')) 检查权限
        if($sysVersion == ""){
            $result['code'] = 99998;
            return $result;
        }
        $newSql = base_path()."/releases/update_".$sysVersion.".sql";
        if(file_exists( base_path()."/releases/"."update.lock") == false){
            if(file_exists($newSql)){
                @set_time_limit(3600);
                if(function_exists('ini_set')){
                    ini_set('max_execution_time',3600);
                }
                $sql =  iconv("gb2312", "utf-8//IGNORE",file_get_contents($newSql));
                $sql = removeComment($sql);
                $sql = trim($sql);
                $sql = str_replace("\r", '', $sql);
               $segmentSql = explode(";\n", $sql);
                try{
                    DB::transaction(function()  use($segmentSql){
                        foreach( $segmentSql as $k=>$itemSql) {
                            $item = trim(str_replace("%DB_PREFIX%",DB::getTablePrefix(),$itemSql));
                            DB::select($item);
                        }
                    });
                    $result['code'] = 0;
                } catch (Exception $e) {
//                    print_r($e->getMessage());
                    $result['code'] = 99998;
				}
                return $result;
            }else{
                $result['code'] = 1;
                return $result;
            }
        }else{
            $result['code'] = 2;
            return $result;
        }
    }
}