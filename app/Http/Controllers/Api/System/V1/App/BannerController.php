<?php 
namespace YiZan\Http\Controllers\Api\System\App;

use YiZan\Services\UserService;
use YiZan\Services\UserAddressService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang, Validator;

/**
 * Banner管理
 */
class BannerController extends BaseController 
{
    /**
     * Banner列表
     */
    public function lists()
    {
        return $this->outputData(
            [[
                "id"=>1,
                "city"=>[],
                "app"=>"test",
                "name"=>"test",
                "image"=>"test",
                "type"=>10,
                "arg"=>"test",
                "sort"=>10,
                "status"=>true
            ]]);
    }
    /**
     * 添加Banner
     */
    public function create()
    {
        return $this->output(["result"=>true]);
    }
    /**
     * 获取Banner
     */
    public function get()
    {
        return $this->output([
                "id"=>1,
                "city"=>[],
                "app"=>"test",
                "name"=>"test",
                "image"=>"test",
                "type"=>10,
                "arg"=>"test",
                "sort"=>10,
                "status"=>true
            ]);
    }    
    /**
     * 更新Banner
     */
    public function update()
    {
        return $this->output(["result"=>true]);
    }
    /**
     * 删除Banner
     */
    public function delete()
    {
        return $this->output(["result"=>true]);
    }
}