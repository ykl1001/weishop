<?php
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\Activity;
use View, Input, Lang, Route, Page, Validator, Session, Response, Time;
/**
 * 营销管理
 */
class ActivityController extends AuthController{
    /**
     * 首页
     */
    public function index(){
        $post = Input::all();

        $args['name'] = !empty($post['name']) ? strval($post['name']) : null;
        $args['status'] = !empty($post['status']) ? strval($post['status']) : 0;
        $args['startTime'] = !empty($post['startTime']) ? Time::toTime($post['startTime']) : null;
        $args['endTime'] = !empty($post['endTime']) ? Time::toTime($post['endTime']) : null;
        $args['type'] = !empty($post['type']) ? strval($post['type']) : 0;
        $args['page'] = !empty($post['page']) ? strval($post['page']) : 1;

        $result = $this->requestApi('activity.lists', $args);
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }

        return $this->display();
    }

    /**
     * 创建活动
     */
    public function create(){
        return $this->display();
    }

    /**
     * 添加活动
     * '1:分享活动 2:注册活动 3:线下优惠券发放活动 4:新用户立减 5:',
     */
    public function add(){
        $args = Input::all();
        if(empty($args['type']))
        {
            return $this->error('请选择添加类型',u('Activity/create'));
        }
        if($args['type'] == 1)
        {
            return $this->display('share_activity');
        }
        elseif($args['type'] == 2)
        {
            return $this->display('register_activity');
        }
        elseif($args['type'] == 4)
        {
            $data = Session::get('Activity.full');  //活动信息
            $ids = Session::get('Activity.checkSellerIds'); //选择的商家ID
            $result = $this->requestApi('seller.activityLists', ['ids'=>$ids]); //获取选择的商家信息
            //返回保存的活动信息
            if($data)
            {
                View::share('data', $data['form']);
                View::share('sellerIds', $data['sellerIds']);
            }
            //返回已选择的商家列表
            if($result['code'] == 0)
            {
                View::share('sellerLists', $result['data']);
            }

            return $this->display('new_activity');
        }
        elseif($args['type'] == 5)
        {
            $data = Session::get('Activity.full');  //活动信息
            $ids = Session::get('Activity.checkSellerIds'); //选择的商家ID
            $result = $this->requestApi('seller.activityLists', ['ids'=>$ids]); //获取选择的商家信息
            //返回保存的活动信息
            if($data)
            {
                View::share('data', $data['form']);
                View::share('sellerIds', $data['sellerIds']);
            }
            //返回已选择的商家列表
            if($result['code'] == 0)
            {
                View::share('sellerLists', $result['data']);
            }

            return $this->display('full_activity');
        }
        else
        {
            return $this->display();
        }
    }

    /**
     * 编辑活动
     */
    public function edit(){
        $args = Input::all();
        $result = $this->requestApi('activity.get', $args);

        if($result['data']['type'] == 1)
        {
            //获取所有的优惠券
            $promotion = $this->requestApi('Promotion.lists');
            $promotionList[] = ['id'=>0,'name'=>'选择优惠券'];
            foreach ($promotion['data']['list'] as $key => $value) {
                $promotionList[] = $value;
            }
            View::share("promotionList", $promotionList);

            View::share("data", $result['data']);

            return $this->display('share_activity');
        }
        elseif($result['data']['type'] ==2)
        {
            //获取所有的优惠券
            $promotion = $this->requestApi('Promotion.lists');
            $promotioncount = count($promotion['data']['list']);
            $promotionList[] = ['id'=>0,'name'=>'选择优惠券'];
            foreach ($promotion['data']['list'] as $key => $value) {
                $promotionList[] = $value;
            }

            //print_r($result['data']);
            View::share("promotionList", $promotionList);
            View::share("promotioncount", $promotioncount);
            View::share("data", $result['data']);
            return $this->display('register_activity');
        }
        elseif($result['data']['type'] ==4)
        {
            if( ! function_exists('array_column'))
            {
                $ids = \YiZan\Http\Controllers\YiZanViewController::array_column($result['data']['activitySeller'], 'sellerId');
            }
            else{
                $ids = array_column($result['data']['activitySeller'], 'sellerId');
            }
            $seller = $this->requestApi('seller.activityLists', ['ids'=>$ids]); //获取选择的商家信息

            //返回已选择的商家列表
            if($seller['code'] == 0)
            {
                View::share('sellerLists', $seller['data']);
            }

            View::share("data", $result['data']);
            View::share("edit", 1);
            return $this->display('new_activity');
        }
        elseif($result['data']['type'] ==5)
        {
            if( ! function_exists('array_column'))
            {
                $ids = \YiZan\Http\Controllers\YiZanViewController::array_column($result['data']['activitySeller'], 'sellerId');
            }
            else{
                $ids = array_column($result['data']['activitySeller'], 'sellerId');
            }
            $seller = $this->requestApi('seller.activityLists', ['ids'=>$ids]); //获取选择的商家信息

            //返回已选择的商家列表
            if($seller['code'] == 0)
            {
                View::share('sellerLists', $seller['data']);
            }

            View::share("data", $result['data']);
            View::share("edit", 1);
            return $this->display('full_activity');
        }
        else
        {
            return $this->display();
        }
    }

    /**
     * 添加或编辑分享活动
     */
    public function save_register_activity(){
        $args = Input::all();

        $args['type'] = 2;

        $result = $this->requestApi('activity.registerUpdate',$args); //更新和创建

        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }

        return $this->success( Lang::get('admin.code.98008'), u('Activity/index'), $result['data'] );
    }

    /**
     * 获取优惠券
     */
    public function getpromotion(){
        $args = Input::all();

        if(Time::totime($args['endTime2']) < UTC_TIME)
        {
            $args['endTime2'] = Time::todate(UTC_TIME, 'Y-m-d');
        }

        $promotion = $this->requestApi('Promotion.lists',$args);
        echo json_encode($promotion);
    }

    /**
     * 分享活动
     */
    public function share_activity(){
        //查看是否有分享活动
        $args['type'] = 1;
        $result = $this->requestApi('activity.activity', $args);

        //获取所有的优惠券
        $promotion = $this->requestApi('Promotion.getPromotionLists');
        $promotionList[] = ['id'=>0,'name'=>'选择优惠券'];
        foreach ($promotion['data'] as $key => $value) {
            $promotionList[] = $value;
        }
        View::share("promotionList", $promotionList);

        if(!empty($result['data'])){
            //获取活动的优惠券
            $promotion2 = $this->requestApi('activity.getPromotionLists');
            View::share("promotionList2", $promotion2['data']);
        }

        View::share("data", $result['data']);

        return $this->display();
    }

    /**
     * 添加或编辑分享活动
     */
    public function save_share_activity(){
        $args = Input::all();

        $args['type'] = 1;
        $result = $this->requestApi('activity.shareUpdate',$args); //更新

        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }

        return $this->success( Lang::get('admin.code.98008'), u('Activity/index'), $result['data'] );
    }
    

    /**
     * [destroy]
     */
    public function destroy(){
        $args['id'] = explode(',', Input::get('id'));
        if( $args['id'] > 0 ) {
            $result = $this->requestApi('activity.delete',$args);
        }
        if( $result['code'] > 0 ) {
            return $this->error($result['msg']);
        }
        return $this->success(Lang::get('admin.code.98005'), u('Activity/index'), $result['data']);
    }

    /**
     * ------------------------------
     * + 满减活动
     * ------------------------------
     */

    /**
     * 添加满减活动
     */
    public function save_full_activity() {
        $args = Input::all();
        $args['isSystem'] = 1; //标识 是否属于平台
        $args['type'] = 5; //标识 满减活动

        $result = $this->requestApi('activity.saveFull',$args);
        //添加成功，删除闪存数据
        if($result['code'] == 0)
        {
            Session::put('Activity.full', null);
            Session::put('Activity.checkSellerIds', null);
            Session::save();

            return $this->success( $result['msg'], u('Activity/index'), $result['data'] );
        }
        else
        {
            return $this->error($result['msg']);
        }
    }

    /**
     * 添加首单立减
     */
    public function save_new_activity() {
        $args = Input::all();
        $args['isSystem'] = 1; //标识 是否属于平台
        $args['type'] = 4; //标识 首单立减

        $result = $this->requestApi('activity.saveNew',$args);
        //添加成功，删除闪存数据
        if($result['code'] == 0)
        {
            Session::put('Activity.full', null);
            Session::put('Activity.checkSellerIds', null);
            Session::save();

            return $this->success( $result['msg'], u('Activity/index'), $result['data'] );
        }
        else
        {
            return $this->error($result['msg']);
        }
    }

    /**
     * 保存活动数据
     */
    public function save_full_data() {
        $args = Input::all();
        foreach ($args['form'] as $key => $value) {
            $data['form'][$value['name']] = $value['value'];
        }
        $data['sellerIds'] = $args['sellerIds'];

        Session::put('Activity.full', $data);
        Session::save();
    }

    /**
     * 添加活动商家
     */
    public function addSeller() {
        $args = Input::all();
        $checkSellerIds = Session::get('Activity.checkSellerIds');
        $args['notIds'] = !empty($checkSellerIds) ? $checkSellerIds : null;

        $result = $this->requestApi('seller.lists', $args);
        if ($result['code'] == 0)
            View::share('list', $result['data']['list']);

        View::share('args', $args);
        return $this->display();
    }

    /**
     * 保存已经选择的商家编号数据
     */
    public function saveSellerIds() {
        $data = Input::all();

        foreach ($data['sellerIds'] as $key => $value) {
            $newData[$value] = $value;
        }
        
        if(empty($data))
        {
            exit;
        }

        $oldData = Session::get('Activity.checkSellerIds');

        if(!empty($oldData))
        {
            $allData = $newData + $oldData;
        }
        else
        {
            $allData = $newData;
        }
        
        Session::put('Activity.checkSellerIds', $allData);
        Session::save();
    }

    /**
     * 删除已经选择的商家编号
     */
    public function deleteSellerIds() {
        $args = Input::all();
        $ids = Session::get('Activity.checkSellerIds');
        unset($ids[$args['id']]);

        Session::put('Activity.checkSellerIds', $ids);
        Session::save();

        return 1;
    }

    /**
     * 作废
     */
    public function cancellation() {
        $args = Input::all();
        $result = $this->requestApi('activity.cancellation', $args);
        return Response::json($result);
    }

}  