<?php 
namespace YiZan\Http\Controllers\Api\System\Goods;

use YiZan\Services\GoodsCateService;
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
        $data = GoodsCateService::getList($this->request('sellerId'), $this->request('type'));
        
		return $this->outputData($data);
    }
    /**
     * 添加分类
     */
    public function create()
    {
        $result = GoodsCateService::create
        (
            intval($this->request('tradeId')),
            intval($this->request('pid')),
            $this->request('type'),
            $this->request('name'),
            $this->request('img'),
            intval($this->request('sort')),
            intval($this->request('status')),
            intval($this->request('sellerId'))
        );
        
        return $this->output($result);
    }
    /**
     * 更新分类
     */
    public function update()
    {
        $result = GoodsCateService::update
        (
            intval($this->request('id')),
            intval($this->request('tradeId')),
            intval($this->request('pid')),
            $this->request('type'),
            $this->request('name'),
            $this->request('img'),
            intval($this->request('sort')),
            intval($this->request('status')),
            intval($this->request('sellerId'))
        );

        return $this->output($result);
    }
	    /**
     * 添加分类
     */
    public function OneselfTagCreate()
    {
        $result = GoodsCateService::OneselfCreate
        (
            intval($this->request('id')),
            intval($this->request('tradeId')),
            intval($this->request('pid')),
            $this->request('type'),
            $this->request('name'),
            $this->request('img'),
            intval($this->request('sort')),
            intval($this->request('status')),
            ONESELF_SELLER_ID
        );
        return $this->output($result);
    }
	
    /**
     * 删除分类
     */
    public function delete()
    {
        $result = GoodsCateService::delete(
            $this->request('id'), 
            intval($this->request('sellerId'))
        );
        
        return $this->output($result);
    }

    public function get()
    {
        $result = GoodsCateService::getSellerCate(intval($this->request('sellerId')),intval($this->request('id')));
        
        return $this->output($result);
    }
	public function getOneself()
    {
        $result = GoodsCateService::getSellerCate(ONESELF_SELLER_ID,intval($this->request('id')));

        return $this->output($result);
    }

    /**
     * 删除分类
     */
    public function oneselfDelete()
    {
        $result = GoodsCateService::delete(
            $this->request('id'), 
            ONESELF_SELLER_ID
        );

        return $this->output($result);
    }

    /**
     * 是否推荐
     */
    public function isWapStatus()
    {
        $result = GoodsCateService::isWapStatus(intval($this->request('id')),intval($this->request('isWapStatus')));

        return $this->output($result);
    }

    public function updatestatus()
    {
        $result = GoodsCateService::updateStatus(
            intval($this->request('id')),
            intval($this->request('status'))
        );
        return $this->output($result);
    }

}