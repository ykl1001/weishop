<?php
namespace YiZan\Services;

use YiZan\Models\MsgModel;
use YiZan\Utils\Time;
use YiZan\Utils\String;
use DB, Exception,Validator,Lang;

/**
 * 消息模板
 */
class MsgModelService extends BaseService {
    /**
     * 列表
     */
    public static function getList()
    {
        $list = MsgModel::orderBy('id', 'asc')->where('status',1)->get()->toArray();
        $data = [];
        foreach($list as $k=> $v){
            $dir = APP_PATH."resources/views/api/".strtr($v['code'],".","/")."/";
            if (is_dir($dir)){
                $v['is_writable'] = 1;
                if ($dh = opendir($dir)){
                    while (($file = readdir($dh))!= false){
                        if ($file != "." && $file != "..") {
                            $filePath = $dir.$file;
                            if(!is_writable($filePath)){
                                $v['is_writable'] = 0;
                            }
                        }
                    }
                    closedir($dh);
                }
            }
            $data[$v['type']][] = $v;
        }
        return ["list"=>$data];
    }

    /**
     * 列表
     */
    public static function getId($id)
    {
        $data = MsgModel::where('id',$id)->where('status',1)->first();
        $data = $data ? $data->toArray() : null;
        return $data;
    }
    /**
     * 按模版code获取模版
     */
    public static function getByCode($code)
    {
        $data = MsgModel::where('code',$code)->where('status',1)->first();
        $data = $data ? $data->toArray() : null;
        return $data;
    }
    /**
     * 列表
     */
    public static function save($data)
    {   $result =
        [
            'code' => 0,
            'data'	=> null,
            'msg'	=> '添加成功'
        ];

        $rules = array(
            'id' => ['required'],
            'content' => ['required'],
            'title' => ['required']
        );

        $messages = array(
            'id.required' => 10000,// 请输入活动名称
            'content.required' => 80882,// 请输入活动名称
            'title.required' => 80881// 请输入活动名称
        );

        $validator = Validator::make(
            [
                'id' => $data['id'],
                'content' => $data['content'],
                'title' => $data['title']

            ], $rules, $messages);

        //验证信息
        if ($validator->fails()) {
            $messages       = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        $bln = false;
        unset($data['msgModel']);
        $code = $data['code'];
        unset($data['code']);
        DB::beginTransaction();
        try {
            MsgModel::where('id',$data['id'])->update($data);
            DB::commit();
            $bln = true;
        }catch (Exception $e){
            DB::rollback();
            $bln = false;
        }
        if($bln){
            $result['code'] = 0;
            $dir = base_path()."/resources/views/api/".strtr($code,".","/")."/";
            $dirs = str_replace('/content','',$dir);
            @file_put_contents($dirs."title.blade.php",$data['title']);
            if (is_dir($dir)){
                if ($dh = opendir($dir)){
                    while (($file = readdir($dh))!= false){
                        if ($file != "." && $file != "..") {
                            $filePath = $dir.$file;
                            @file_put_contents($filePath,$data['content']);
                        }
                    }
                    closedir($dh);
                }
            }
            return $result;
        }else{
            $result['code'] = 80884;
            return $result;
        }
    }
}
