<?php namespace YiZan\Services\Proxy;
 
use YiZan\Models\Proxy;

use YiZan\Utils\Time;
use YiZan\Utils\String;
use DB, Exception,Validator, Lang;

/**
 * 代理
 */
class ProxyService extends \YiZan\Services\ProxyService { 

    /**
     * 修改密码
     * @param int $id 编号
     * @param int $oldPwd 原密码
     * @param int $newPwd 新密码
     * @return array 
     */
    public static function updateProxyPassword($id, $oldPwd, $newPwd){
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => Lang::get('api.success.create_user_repass')
        );

        $rules = array(
            'oldPwd' => ['required'],
            'newPwd' => ['required'],
        );
        
        $messages = array (
            'oldPwd.required'       => 10107,   // 原密码不正确
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

        $proxy = Proxy::find($id);

        $oldPwd = md5(md5($oldPwd) . $proxy->crypt);

        if($proxy->pwd != $oldPwd){
            $result['code'] = 10108;
            return $result;
        }

        $update_result = Proxy::where('id', $id)
                              ->where('pwd', $oldPwd)
                              ->update(['pwd'=>md5(md5($newPwd) . $proxy->crypt)]);
        if(!$update_result){
            $result['code'] = 10110;
        }
        return $result;
    }

    /**
     * 搜索2级代理
     * @param $proxy array 代理信息
     * @param $id int 代理编号
     * @param $name string 代理账户
     * @return 结果
     */
    public static function getSecondLists($proxy, $name){
        $list = Proxy::where('pid', $proxy->id);
        if($name){
            $list->where('name', $name);
        }

        $list = $list->get();
        return $list;
    }

    /**
     * 代理列表
     * @param $proxy array 代理信息
     * @param $id int 代理编号
     * @param $name string 代理账户
     * @param $mobile string 代理电话
     * @param $provinceId int  省份
     * @param $cityId int  城市
     * @param $areaId int  区域
     * @param $page int  页码
     * @param $pageSize int  数量
     * @return array 结果集
     */
    public static function getLists($proxy, $id, $name, $mobile, $provinceId, $cityId, $areaId, $page, $pageSize){

        if($proxy->level == 3){
            return [];
        }

        $prefix = DB::getTablePrefix();   
        $list = Proxy::where('is_check', 1)
                     ->orderBy('id', 'DESC');

        if($id > 0){
            $curentProxy = Proxy::find($id);
            if($curentProxy->level == 2 && $proxy->id == $curentProxy->pid){
                $list->where('pid', $curentProxy->id);
            } else {
                return [];
            }
        } elseif(empty($name) && empty($mobile)) { 
            $list->whereRaw("(pid = ".$proxy->id." or pid in (SELECT id FROM ".$prefix."proxy WHERE pid = ".$proxy->id."))");
        }

        
        if(!empty($name)){
            $list->where('name', 'like', '%'.$name.'%');
        }

        if(!empty($mobile)){
            $list->where('mobile', 'like', '%'.$mobile.'%');
        }

        if(!empty($name) || !empty($mobile)){
            $list->whereRaw("(pid = ".$proxy->id." or pid in (SELECT id FROM ".$prefix."proxy WHERE pid = ".$proxy->id."))");
        }

        if($provinceId > 0){
            $list->where('province_id', $provinceId);
        }

        if($cityId > 0){
            $list->where('city_id', $cityId);
        }

        if($areaId > 0){
            $list->where('area_id', $areaId);
        }

        $totalCount = $list->count();

        $list = $list->skip(($page - 1) * $pageSize)
                     ->take($pageSize) 
                     ->with('province', 'city', 'area')
                     ->get()
                     ->toArray();

        return ["list" => $list, "totalCount" => $totalCount, "proxy" => $curentProxy];

    } 


    /**
     * 查询审核代理列表
     * @param string $name      代理账户 
     * @param int $provinceId   省份编号
     * @param int $cityId       省份编号
     * @param int $areaId       区域编号
     * @param int $isCheck      审核状态
     * @param int $page         分页
     * @param int $pageSize     每页数量
     * @param int $level        等级
     * @param int $isAll        是否取全部
     * @return $result 结果集
     */
    public static function getAuthLists($proxy, $name, $mobile, $provinceId, $cityId, $areaId, $isCheck, $page, $pageSize){

        if($proxy->level == 3){
            return [];
        }

        $prefix = DB::getTablePrefix();   
        $list = Proxy::whereRaw("(pid = ".$proxy->id." or pid in (SELECT id FROM ".$prefix."proxy WHERE pid = ".$proxy->id."))")
                     ->orderBy('id', 'DESC');


        if($isCheck){
            $list->where('is_check', $isCheck - 2);
        } 
        
        if(!empty($name)){
            $list->where('name', 'like', '%'.$name.'%');
        }  
        
        if($provinceId > 0){
            $list->where('province_id', $provinceId);
        }

        if($cityId > 0){
            $list->where('city_id', $cityId);
        }

        if($areaId > 0){
            $list->where('area_id', $areaId);
        }

        $totalCount = $list->count();

        $list = $list->skip(($page - 1) * $pageSize)
                     ->take($pageSize) 
                     ->with('province', 'city', 'area')
                     ->get()
                     ->toArray();

        return ["list" => $list, "totalCount" => $totalCount, "proxy" => $curentProxy];
    }

    /**
     * 代理明细
     * @param $proxy array 代理信息
     * @param $id int 代理编号 
     * @return array 结果集
     */
    public static function getProxyById($proxy, $id){ 
        $prefix = DB::getTablePrefix();   
        $data = Proxy::where('id', $id)
                     ->whereRaw("(pid = ".$proxy->id." or pid in (SELECT id FROM ".$prefix."proxy WHERE pid = ".$proxy->id."))")
                     ->with('province', 'city', 'area', 'childs','parentProxy')
                     ->first();
        return $data;
    } 

    /**
     * 添加/编辑代理
     * @param array $proxy      代理
     * @param int $id           代理编号 
     * @param string $name      代理账户 
     * @param string $pwd       代理账户 
     * @param string $realName  真实姓名 
     * @param string $mobile    电话号码 
     * @param int $pid          父代理 
     * @param int $level        代理级别 
     * @param int $provinceId   省份编号
     * @param int $cityId       省份编号
     * @param int $areaId       区域编号
     * @param string $thirdArea 三级代理区域
     * @param int $status       状态 
     * @return $result 结果集
     */
    public static function save($parentProxy, $id, $name, $pwd, $realName, $mobile, $pid, $level, $provinceId, $cityId, $areaId, $thirdArea, $status){
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );

        if($id > 0){
            $proxy = Proxy::where('id', $id)
                          ->first(); 
            $level = $proxy->level;
            $pid = $proxy->pid;
            $rules = array( 
                'realName'      => ['required', 'max:10'],
                'mobile'        => ['required','regex:/^1[0-9]{10}$/'],
                'level'         => ['required']
            );

            $messages = array
            ( 
                'realName.required' => 40929,   // 请输入真实姓名
                'realName.max'      => 40937,   // 长度限制
                'mobile.required'   => 40930,   // 请输入电话号码
                'mobile.regex'      => 40931,   // 请输入正确的电话号码
                'level.required'    => 40932    // 请输入代理级别
            );

            $checks = [ 
                    'realName'      => $realName,
                    'mobile'        => $mobile,
                    'level'         => $level
                ];
        } else {
            $proxy = new Proxy();
            $rules = array(
                'name'          => ['required', 'regex:/^[0-9A-Za-z]{6,15}$/'],
                'pwd'           => ['required','min:6'],
                'realName'      => ['required', 'max:10'],
                'mobile'        => ['required','regex:/^1[0-9]{10}$/'],
                'level'         => ['required']
            );

            $messages = array
            (
                'name.required'     => 40926,   // 请输入代理账户
                'name.regex'        => 40936,   // 名字正则
                'pwd.required'      => 40927,
                'pwd.min'           => 40928,
                'realName.required' => 40929,   // 请输入真实姓名
                'realName.max'      => 40937,   // 长度限制
                'mobile.required'   => 40930,   // 请输入电话号码
                'mobile.regex'      => 40931,   // 请输入正确的电话号码
                'level.required'    => 40932    // 请输入代理级别
            );

            $checks = [
                    'name'          => $name,
                    'pwd'           => $pwd,
                    'realName'      => $realName,
                    'mobile'        => $mobile,
                    'level'         => $level
            ];

            $proxyInfo = Proxy::where('name', $name)
                              ->first();
            if($proxyInfo){
                $result['code'] = 40939;
                return $result;
            }

            $proxyInfo = Proxy::where('mobile', $mobile)
                              ->first(); 
            if($proxyInfo){
                $result['code'] = 40940;
                return $result;
            }
            $proxy->name = $name;
            $proxy->create_time = UTC_TIME;
        }

        $validator = Validator::make($checks, $rules, $messages);

        //验证信息
        if ($validator->fails())
        {
            $messages = $validator->messages();

            $result['code'] = $messages->first();

            return $result;
        }

        $proxy->real_name   = $realName;
        $proxy->mobile      = $mobile;
        $proxy->level       = $level;
        //设置密码
        if($id <= 0){
            $crypt  = String::randString(6);
            $proxy->pwd = md5(md5($pwd).$crypt);
            $proxy->crypt = $crypt;
        } else {
            if($pwd && strlen($pwd) < 6){
                $result['code'] = 40928;
                return $result;
            } else if($pwd && strlen($pwd) > 20){
                $result['code'] = 40938;
                return $result;
            } else if($pwd && strlen($pwd) >= 6){ 
                $proxy->pwd = md5(md5($pwd).$proxy->crypt);
            }
        }

        switch ($level) {
            case '2':  
                if(!empty($provinceId) && $parentProxy->province_id != $provinceId){
                    $result['code'] = 40933;
                }
                $proxy->pid = $parentProxy->id;
                $zx = ['1','18','795','2250'];
                if(in_array((int)$parentProxy->province_id, $zx)){
                  $proxy->province_id = (int)$parentProxy->province_id;
                  $proxy->city_id     = $cityId;
                } else {
                  $proxy->province_id = (int)$parentProxy->province_id;
                  $proxy->city_id     = (int)$parentProxy->city_id;
                }
                $proxy->area_id     = (int)$areaId;
                $proxy->third_area  = $thirdArea;
                break;
            
            default:
                if(empty($thirdArea)){
                    $result['code'] = 40934;
                    return $result;
                }
                $sProxy = Proxy::where('id', $pid)
                               ->first();
                if(empty($sProxy)){
                    $result['code'] = 40935;
                    return $result;
                }
                if(!empty($provinceId) && $sProxy->province_id != $provinceId){
                    $result['code'] = 40933;
                    return $result;
                }
                if(!empty($cityId) && $sProxy->city_id != $cityId){
                    $result['code'] = 40933;
                    return $result;
                }
                $proxy->pid = $pid;
                $proxy->province_id = (int)$sProxy->province_id;
                $proxy->city_id     = (int)$sProxy->city_id;
                $proxy->area_id     = (int)$sProxy->area_id;
                $proxy->third_area  = $thirdArea;
                break; 
        }  
        //判断当前地理位置是否已经存在此代理  
        $proxyData = Proxy::where('province_id', $proxy->province_id)
                          ->where('city_id', $proxy->city_id)
                          ->where('area_id', $proxy->area_id)
                          ->where('third_area', $proxy->third_area)
                          ->first();  
        if($proxyData){
            $result['code'] = 40944; 
            return $result;
        }
        $proxy->status = $status;
        $proxy->save(); 
        return $result;
    }

}
