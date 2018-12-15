<?php 
namespace YiZan\Http\Controllers\Api\System\Seller;

use YiZan\Services\SellerCateService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang, Validator;

/**
 * 服务分类
 */
class CateController extends BaseController
{
    /**
     * 分类列表
     */
    public function lists()
    {
        $data = SellerCateService::getList(
            max($this->request('page'),1),
            max($this->request('pageSize'),20)
        );
        
        return $this->outputData($data);
    }
    /**
     * 添加分类
     */
    public function create()
    {
        $result = SellerCateService::create
        (
            $this->request('name'), 
            $this->request('pid', 0),
            $this->request('status'),
            $this->request('logo'),
            (int)$this->request('type'),
            intval($this->request('sort'))
        );
        
        return $this->output($result);
    }
    /**
     * 更新分类
     */
    public function update()
    {
        $result = SellerCateService::update
        (
            (int)$this->request('id'),
            $this->request('name'),
            $this->request('pid', 0),
            $this->request('status'),
            $this->request('logo'),
            (int)$this->request('type'),
            intval($this->request('sort'))
        );
        
        return $this->output($result);
    }
    /**
     * 删除分类
     */
    public function delete()
    {
        $result = SellerCateService::delete((array)$this->request('id'));
        
        return $this->output($result);
    }

    /**
     * 获取分类
     */

    public function get() {
        $data = SellerCateService::get((int)$this->request('id'));
        return $this->outputData($data);
    }

    /**
     * 无分页分类
     */
    public function all() {
        $list = SellerCateService::getAll();
        return $this->outputData($list);
    }

    /**
     * 无分页分类
     */
    public function catesall() {
        $list = SellerCateService::getSellerCatesAll();
        return $this->outputData($list);
    }
	
    /**
     * 分类列表
     */
    public function getSellerCateOneselfLists()
    {
        $data = SellerCateService::getCatesAll(
            $this->request('id'),
            $this->request('type')
//            max($this->request('page'),1),
//            max($this->request('pageSize'),20)
        );

        return $this->outputData($data);
    }
    /**
     * 分类列表
     */
    public function getSellerCateOneselfB()
    {
        $data = SellerCateService::getSellerCateLists(
            ONESELF_SELLER_ID,
            max($this->request('page'),1),
            max($this->request('pageSize'),20)
        );

        return $this->outputData($data);
    }

    /**
     * 分类列表2
     */
    public function catelists() {
        $data = SellerCateService::getSellerCateLists(
            intval($this->request('sellerId')),
            max($this->request('page'),1),
            intval($this->request('type'))
        );
        return $this->outputData($data);
    }
}