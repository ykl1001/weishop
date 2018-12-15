<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\ArticleService;
use Lang, Validator;

/**
 * 文章管理
 */
class ArticleController extends BaseController 
{
    /**
     * 文章列表
     */
    public function lists()
    {
        $data = ArticleService::getList
        (
            $this->request('title'),
            intval($this->request('cateId')),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
		return $this->outputData($data);
    }
    /**
     * 公告列表
     */
    public function noticeLists()
    {
        $data = ArticleService::noticeLists
        (
            ONESELF_SELLER_ID,
            max((int)$this->request('page'), 1),
            max((int)$this->request('pageSize'), 20)
        );

        return $this->outputData($data);
    }



    /**
     * 添加公告
     */
    public function noticeCreate()
    {
        $result = ArticleService::noticeCreate
        (
            ONESELF_SELLER_ID,
            $this->request('id'),
            $this->request('title'),
            $this->request('content'),
            intval($this->request('sort')),
            intval($this->request('status'))
        );

        return $this->output($result);
    }

    /**
     * 添加公告
     */
    public function create()
    {
        $result = ArticleService::create
        (
            $this->request('title'),
            intval($this->request('cateId')),
            $this->request('brief'),
            $this->request('image'),
            $this->request('content'),
            intval($this->request('sort')),
            intval($this->request('status'))
        );
        
        return $this->output($result);
    }
    /**
     * 获取文章
     */
    public function get()
    {
        $article = ArticleService::getById(intval($this->request('id')));
        
        return $this->outputData($article == false ? [] : $article->toArray());
    }
    /**
     * 更新文章
     */
    public function update()
    {
        $result = ArticleService::update
        (
            intval($this->request('id')),
            $this->request('title'),
            intval($this->request('cateId')),
            $this->request('brief'),
            $this->request('image'),
            $this->request('content'),
            intval($this->request('sort')),
            intval($this->request('status'))
        );
        
        return $this->output($result);
    }
    /**
     * 删除文章
     */
    public function delete()
    {
        $result = ArticleService::delete($this->request('id'));
        
        return $this->output($result);
    }
}