<?php 
namespace YiZan\Http\Controllers\Api\System\App;

use YiZan\Services\UserService;
use YiZan\Services\UserAddressService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang, Validator;

/**
 * 首页分类管理
 */
class CategoryController extends BaseController 
{
    /**
     * 分类列表
     */
    public function lists()
    {
        return $this->outputData(
            [[
                "id"=>1,
                "city"=>[],
                "app"=>"test",
                "name"=>"test",
                "icon"=>"test",
                "bgColor"=>"test",
                "type"=>10,
                "arg"=>"test",
                "sort"=>10,
                "status"=>true
            ]]);
    }
    /**
     * 添加分类
     */
    public function create()
    {
        return $this->output(["result"=>true]);
    }
    /**
     * 获取分类
     */
    public function get()
    {
        return $this->output([
                "id"=>1,
                "city"=>[],
                "app"=>"test",
                "name"=>"test",
                "icon"=>"test",
                "bgColor"=>"test",
                "type"=>10,
                "arg"=>"test",
                "sort"=>10,
                "status"=>true
            ]);
    }    
    /**
     * 更新分类
     */
    public function update()
    {
        return $this->output(["result"=>true]);
    }
    /**
     * 删除分类
     */
    public function delete()
    {
        return $this->output(["result"=>true]);
    }
}