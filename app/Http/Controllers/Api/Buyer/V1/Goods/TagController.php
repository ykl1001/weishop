<?php
namespace YiZan\Http\Controllers\Api\Buyer\Goods;

use YiZan\Services\GoodsTagService;
use YiZan\Http\Controllers\Api\Buyer\BaseController;
use Lang, Validator;

/**
 * 服务标签
 */
class TagController extends BaseController 
{
    /**
     * 标签列表
     */
    public function lists()
    {
        $data = GoodsTagService::getList();
        
		return $this->outputData($data);
    }


    /**
     * 获取热门标签
     * @return [type] [description]
     */
    public function gethottags(){
        $data = GoodsTagService::getHotTagLists(max((int)$this->request('page'), 1), max((int)$this->request('pageSize'),20));
        return $this->outputData($data);
    }

    /**
     * 获取标签
     */
    public function get()
    {
        $result = GoodsTagService::get( intval($this->request('id')));
        
        return $this->outputData($result);
    }

    /**
     * 添加标签
     */
    public function create()
    {
        $result = GoodsTagService::create
        ( 
            $this->request('name'),  
            intval($this->request('sort')),
            intval($this->request('status'))
        );
        
        return $this->output($result);
    }
    /**
     * 更新标签
     */
    public function update()
    {
        $result = GoodsTagService::update
        (
            intval($this->request('id')), 
            $this->request('name'),  
            intval($this->request('sort')),
            intval($this->request('status'))
        );
        
        return $this->output($result);
    }
    /**
     * 删除标签
     */
    public function delete()
    {
        $result = GoodsTagService::delete(intval($this->request('id')));
        
        return $this->output($result);
    }
}