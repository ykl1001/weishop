<?php
namespace YiZan\Http\Controllers\Admin;

use View, Input, Lang, Route, Page, Validator, Session, Response, Time;
/**
 * 公告管理
 */
class OneselfNoticeController extends ArticleController{
    /**
     * 首页
     */
    public function index(){
        $result = $this->requestApi('Article.noticeLists');
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }

        return $this->display();
    }


    public function save() {
        $args = Input::all();
        $result = $this->requestApi('article.noticeCreate',$args);  //创建
        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98008'), u('OneselfNotice/index'), $result['data']);
    }


    /**
     * 文章分类-删除分类
     */
    public function destroy() {
        $args = Input::all();
        $args['id'] = explode(',', $args['id']);
        if( !empty( $args['id'] ) ) {
            $result = $this->requestApi('article.delete',$args);
        }
        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98005') , u('OneselfNotice/index'), $result['data']);
    }
}  