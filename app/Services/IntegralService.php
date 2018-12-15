<?php namespace YiZan\Services;

use YiZan\Models\Goods;
use YiZan\Models\Order;
use YiZan\Utils\String;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use Illuminate\Database\Query\Expression;
use DB, Lang,Validator;

/**
 * 积分商城
 */
class IntegralService extends BaseService {
    /**
     * [getList description]
     */
    public static function getList($sellerId,$name = "",$page,$pageSize) {
        if($sellerId != 0){
            $id = $sellerId;
        }else{
            $id = ONESELF_SELLER_ID;
        }
        $list =  Goods::orderBy('sort', 'asc')->orderBy('id', 'desc')->where('seller_id',$id)->where('is_integral' ,1);

        if(!empty($name))
        {
            $list = $list->where('name','like', '%'.$name.'%');
        }

        $totalCount = $list->count();
        $list = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();
        return ["list"=>$list,"totalCount"=>$totalCount];//,
    }
    /**
     * [getList description]
     */
    public static function getWapList($page,$pageSize) {
        $list =  Goods::orderBy('sort', 'asc')
            ->where('status' ,1)
            ->where('seller_id' ,ONESELF_SELLER_ID)
            ->where('is_integral' ,1);
        $totalCount = $list->count();
        $list = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();
        return ["list"=>$list,"totalCount"=>$totalCount];//,
    }

    /**
     * 保存
     */
    public static function save($id,$name,$image,$brief,$sort,$isVirtual,$stock,$exchangeIntegral,$status,$sellerId) {

        $result = [
            'code' => 0,
            'msg' => Lang::get('api_system.success.update_info'),
            'data' => null
        ];
        $rules = array(
            'name'            => ['required'],
            'image'           => ['required'],
            'brief'           => ['required'],
            'isVirtual'       => ['required'],
            'stock'           => ['required'],
            'exchangeIntegral'=> ['required'],
            'status'          => ['min:1']
        );

        $messages = array
        (
            'name.required'	            => 50403,	// 名称必须
            'image.required'	        => 41313,	// 名称必须
            'brief.required'	        => 30502,	// 名称必须
            'isVirtual.required'	    => 88890,	// 名称必须
            'stock.required'	        => 88891,	// 名称必须
            'exchangeIntegral.required' => 88892,	// 名称必须
            'status.min'	            => 31102,	// 名称必须
        );

        $validator = Validator::make(
            [
                'name'            => $name,
                'image'           => is_array($image) ? implode(',', $image) : "",
                'brief'           => $brief,
                'isVirtual'       => $isVirtual,
                'stock'           => $stock,
                'exchangeIntegral'=> $exchangeIntegral,
                'status'          => $status
            ], $rules, $messages);

        //验证信息
        if ($validator->fails())
        {
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }


        if($id != ""){
            $goods = Goods::where('id', $id)->first();
            if (!$goods) {
                $result['code'] = 40002;
                return $result;
            }
        }


        $oldImages = $goods->images;
        foreach ($image as $img)  {
            if (!empty($img))  {
                if (false !== ($key = array_search($img, $oldImages)))  {
                    unset($oldImages[$key]);

                    $newImages[] = $img;
                } else{
                    $image = self::moveGoodsImage($goods->seller_id, $goods->id, $img);
                    //转移图片失败
                    if (!$image)
                    {
                        $result['code'] = 30213;
                        return $result;
                    }

                    $newImages[] = $image;
                }
            }
        }

        if($sellerId != 0){
            $sellerId = $sellerId;
        }else{
            $sellerId = ONESELF_SELLER_ID;
        }
        $update = [
            'seller_id'       => $sellerId,
            'name'            => $name,
            'images'           => is_array($newImages) ? implode(',', $newImages) : "",
            'brief'           => $brief,
            'is_virtual'       => $isVirtual,
            'stock'           => $stock,
            'exchange_integral'=> $exchangeIntegral,
            'status'          => $status,
            'cate_id' => 0,
            'create_time' => UTC_TIME,
            'sort' => $sort,
            'type' => 3,
            'is_integral' => 1

        ];
        if($id != 0){
            Goods::where('id', $id)
                ->where('seller_id',$sellerId)
                ->update($update);
        }else{
            Goods::insert($update);
        }
        return $result;
    }

    /**
     * 保存
     */
    public static function userlog($userId,$id,$page,$pageSize) {
        $list = Order::where('user_id',$userId)
            ->where('is_integral_goods',1)
            ->with('orderGoods');
        if($id != "" || $id != 0){
            $list->where('id',$id);
        }
        $list = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();

        $data = [];
        foreach($list as $key => $v){
            $data[$key]['id'] = $v['id'];
            $data[$key]['name'] = $v['orderGoods'][0]['goodsName'];
            $data[$key]['createTime'] = $v['createTime'];
            $data[$key]['images'] = $v['orderGoods'][0]['goodsImages'];
        }
        return ["list"=>$data];
    }
    /**
     * 保存
     */
    public static function get($userId,$id) {
        $list = Order::where('user_id',$userId)
            ->where('integral',">" ,0)
            ->with('orderGoods')->where('id',$id)->first();
        if($list->id){
            $list = $list->toArray();
        }
        $data = [];
        $data['id'] = $list['id'];
        $data['goodsId'] = $list['orderGoods'][0]['goodsId'];
        $data['name'] = $list['name'];
        $data['goodsName'] = $list['orderGoods'][0]['goodsName'];
        $data['createTime'] = $list['createTime'];
        $data['images'] = $list['orderGoods'][0]['goodsImages'];
        $data['address'] = $list['address'];
        $data['mobile'] = $list['mobile'];
        $data['buyRemark'] = $list['buyRemark'];
        $data['mobile'] = $list['mobile'];
        $data['integralFee'] = $list['integralFee'];
        $data['integral'] = $list['integral'];
        return $data;
    }


    /**
     * wap详情
     */
    public static function detail($id) {
        $list = Goods::where('id',$id)->with('seller')->first();
        if($list->id){
            $list = $list->toArray();
        }
        return $list;
    }


    /**
     * 编辑
     */
    public static function getIntegral($id) {
        $list = Goods::where('id',$id)
            ->where('seller_id',ONESELF_SELLER_ID)->first();
        if($list->id){
            $list = $list->toArray();
        }
        return $list;
    }

    /**
     * 保存
     */
    public static function saveIntegral($id,$integral) {

        $result = [
            'code' => 0,
            'msg' => Lang::get('api_system.success.update_info'),
            'data' => null
        ];
        $goods = Goods::where('id',$id)->first();
        if($goods->id){
            Goods::where('id', $goods->id)
                ->where('seller_id',ONESELF_SELLER_ID)
                ->update(['exchange_integral'=> $integral]);
        }
        $result['code'] = 88889;
        return $result;
    }

    /**
     * 删除帖子举报
     */
    public static function delete($id){
        $result =
            [
                'code'  => 0,
                'data'  => null,
                'msg'   => '删除成功'
            ];

        try{
            Goods::whereIn('id', $id)
                ->delete();
        } catch(Exception $e) {
            $result['code'] = 30920;
        }
        return $result;
    }

}