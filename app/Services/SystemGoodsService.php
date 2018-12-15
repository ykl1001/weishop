<?php
namespace YiZan\Services;

use YiZan\Models\SystemGoods;
use YiZan\Models\Goods;
use YiZan\Models\SystemGoodsNorms;
use YiZan\Models\GoodsNorms;
use YiZan\Models\GoodsExtend;
use YiZan\Models\SystemTagList;
use YiZan\Models\Seller;
use YiZan\Models\GoodsStock;
use YiZan\Models\GoodsSkuItem;
use YiZan\Utils\String;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use Illuminate\Database\Query\Expression;
use DB, Validator;

class SystemGoodsService extends BaseService {
    /**
     * 根据编号获取通用服务
     * @param  integer $id 通用服务编号
     * @return array       通用服务信息
     */
    public static function getById($id) {
        if($id < 1){
            return false;
        }
        return SystemGoods::with('norms')->find($id);
    }
    /**
     * 根据编号获取通用服务
     * @param  integer $id 通用服务编号
     * @return array       通用服务信息
     */
    public static function oneChannelCk($cateId,$systemTagListPid,$systemTagListId,$ids) {

        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );
        $rules = array(
            'cateId' => ['required', 'gt:0'],
            'systemTagListPid' => ['required', 'gt:0'],
            'systemTagListId' => ['required', 'gt:0'],
        );

        $messages = array(
            'cateId.required' => 32003,
            'cateId.gt' => 32003,
            'systemTagListPid.required' => 32000,   //请选择商品标签一级分类
            'systemTagListPid.gt' => 32000,        //请选择商品标签一级分类
            'systemTagListId.required' => 32001,    //请选择商品标签二级分类
            'systemTagListId.gt' => 32001,         //请选择商品标签二级分类
        );
        $validator = Validator::make(
            [
                'cateId' => $cateId,
                'systemTagListPid' => $systemTagListPid,
                'systemTagListId' => $systemTagListId,
            ], $rules, $messages);

        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();

            $result['code'] = $messages->first();

            return $result;
        }
        $where = [
            'system_tag_list_pid' => $systemTagListPid,
            'system_tag_list_id' => $systemTagListId,
        ];
        $count = SystemGoods::where('type', 1)->where('status',1)->where($where);
        if($ids){
            $count->whereIn('id',$ids);
        }
        $count = $count->count();
        if($count <= 0){
            $result['code'] = 32004;
            return $result;
        }
        $result['data'] = ['count'=>$count];
        return $result;
    }

    /**
     * 根据编号获取通用服务
     * @param  integer $id 通用服务编号
     * @return array       通用服务信息
     */
    public static function oneChannel($sellerId,$cateId,$systemTagListPid,$systemTagListId,$ids) {

        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );
        $rules = array(
            'cateId' => ['required', 'gt:0'],
            'systemTagListPid' => ['required', 'gt:0'],
            'systemTagListId' => ['required', 'gt:0'],
        );

        $messages = array(
            'cateId.required' => 32003,
            'cateId.gt' => 32003,
            'systemTagListPid.required' => 32000,   //请选择商品标签一级分类
            'systemTagListPid.gt' => 32000,        //请选择商品标签一级分类
            'systemTagListId.required' => 32001,    //请选择商品标签二级分类
            'systemTagListId.gt' => 32001,         //请选择商品标签二级分类
        );
        $validator = Validator::make(
            [
                'cateId' => $cateId,
                'systemTagListPid' => $systemTagListPid,
                'systemTagListId' => $systemTagListId,
            ], $rules, $messages);

        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();

            $result['code'] = $messages->first();

            return $result;
        }
        $list = SystemGoods::where('type', 1)
            ->where('system_tag_list_pid', $systemTagListPid)
            ->where('system_tag_list_id',$systemTagListId)
            ->where('status',1);
        if($ids){
            $list->whereIn('id',$ids);
        }
        $count = $list->count();
        if($count <= 0){
            $result['code'] = 32004;
            return $result;
        }
        $data = $list->orderBy('id', 'desc')->get()->toArray();
        DB::beginTransaction();
        @set_time_limit(3600);
        if(function_exists('ini_set')){
            ini_set('max_execution_time',3600);
        }
        try{
            $new_g = [];
            foreach ($data as $v) {
                $new_g[] = [
                    'system_goods_id' => $v['id'],
                    'cate_id' => $cateId,
                    'type' => $v['type'],
                    'seller_id' => $sellerId,
                    'system_tag_list_id' => $v['systemTagListId'],
                    'system_tag_list_pid' => $v['systemTagListPid'],
                    'is_virtual' => 0,
                    'is_integral' =>0,
                    'name' => $v['name'],
                    'unit' => $v['unit'],
                    'duration' => $v['duration'],
                    'images' => $v['imageStr'],
                    'price' => $v['price'],
                    'stock' => $v['stock'],
                    'brief' => $v['brief'],
                    'total_stock' => $v['totalStock'],
                    'buy_limit' => $v['buyLimit'],
                    'status' => $v['status'],
                    'create_time' => UTC_TIME,
                    'dispose_status' => 1,
                    'sort' => $v['sort'],
                    'deduct_type' => $v['deductType'],
                    'deduct_val' => $v['deductVal'],
                    'deduct_type' => $v['deductType'],
                    'dispose_time' => UTC_TIME,
                    'dispose_result' => "一键导入商品库",
                    'is_updata' => 1,
                    'stock_type_id' => $v['stockTypeId']
                ];
            }
            Goods::insert($new_g);
            $goodslist = Goods::where('is_updata', 1)->where('seller_id', $sellerId)->get()->toArray();
            $gn = [];
            $gnItem = [];
            $systemGoodsId = [];
            $sort = [
                1 =>'first',
                2 =>'second',
                3 =>'vessel'
            ];
            foreach($goodslist as $key => $n){
                $norms = null;;

                $goodsIds[] = [
                    'goods_id' => $n['id']
                ];

                $systemGoodsId[] = $n['systemGoodsId'];
                $goodsIdsSku[] = $n['id'];


                $ge[] = [
                    'goods_id' => $n['id'],
                    'seller_id' => $sellerId
                ];
                if($n['stockTypeId']){
                    $gnItems = GoodsSkuItem::where('is_system',1)->where('goods_id',$n['systemGoodsId'])->get();

                    $norms['stock'] = $n['stockTypeId'];

                    foreach($gnItems as $k => $vs){
                        $norms['skuItem'][$sort[$vs['sort']]][] = $vs->name;
                    }

                    $gskuStock = GoodsStock::where('is_system',1)->where('goods_id',$n['systemGoodsId'])->get();

                    foreach($gskuStock as $k => $gskuStocks){
                        $norms['skuPrice'][] = $gskuStocks->price;
                        $norms['skuStock'][] = $gskuStocks->stock_count;
                    }

                    $normsLog =  \YiZan\Services\GoodsService::allStockUpdate($n['id'],$sellerId,$norms,0);

                    if (is_numeric($normsLog))
                    {
                        $result['code'] = abs($normsLog);
                        return $result;
                    }

                    $update_infos['price']   =min($norms['skuPrice']);
                    $update_infos['stock']   = array_sum($norms['skuStock']);
                    $update_infos['stock_type_id']   = $norms['stock'];
                    Goods::where('id', $n['id'])
                        ->where('seller_id',$sellerId)
                        ->update($update_infos);
                }else{
                    \YiZan\Services\GoodsService::getStockDelete($n['id']);
                }
            }

            //跟新商家信息 caiq
            $sellerGoods = Goods::where(['seller_id'=>$sellerId,'status'=>1])->select('name')->get();
            $goods_keywords = '';
            foreach($sellerGoods as $goodss){
                $goods_keywords .= preg_replace("/[^\x{4e00}-\x{9fa5}^0-9^A-Z^a-z]+/u", '', $goodss['name']).'|';//删除特殊字符
            }
            $goods_keywords_arr = explode("|",$goods_keywords);
            $goods_keywords_arr = array_flip(array_flip($goods_keywords_arr));
            $goods_keywords = implode("|",$goods_keywords_arr);
            Seller::where('id',$sellerId)->update(['goods_keywords'=>$goods_keywords]);
            GoodsExtend::insert($ge); //更新扩展表            
            Goods::whereIn('id', $goodsIds)->update(['is_updata'=>0]);
            DB::commit();
        } catch(Exception $e) {
            DB::rollback();
            $result['code'] = 10000;
            return $result;
        }
        return $result;
    }

    /**
     * 获取通用服务列表
     * @param  [type] $page        页码
     * @param  [type] $pageSize    分页大小
     * @param  [type] $name    	   关键字
     * @param  [type] $type      服务类型
     */
    public static function getList($page, $pageSize, $name = '',$type,$status) {
        $list = SystemGoods::where('type', $type)
            ->orderBy('id', 'desc')
            ->with('goods','systemTagListPid');
        if (!empty($name)) {
            $list->where('name','like','%'.$name.'%');
        }
        if (!empty($status)) {
            $list->where('status',$status);
        }

        $data['totalCount'] = $list->count();
        $data['list'] = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();
        return $data;
    }

    /*
     * 获取指定类型的通用服务
     */
    public static function getlists($page, $pageSize, $name = '', $type, $status, $systemTagListPid, $systemTagListId) {
        $list = SystemGoods::where('type', $type)
            ->orderBy('id', 'desc');
        if (!empty($name)) {
            $list->where('name','like','%'.$name.'%');
        }
        if (!empty($status)) {
            $list->where('status',$status);
        }
        if($systemTagListPid > 0){
            $list->where('system_tag_list_pid', $systemTagListPid);
        }
        if($systemTagListId > 0){
            $list->where('system_tag_list_id', $systemTagListId);
        }
        $data['totalCount'] = $list->count();
        $list->with('goods', 'systemTagListPid', 'systemTagListId');
        $data['list'] = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();
        return $data;
    }

    /**
     * 获取通用服务列表
     * @param  [type] $page        页码
     * @param  [type] $pageSize    分页大小
     * @param  [type] $name    	   关键字
     * @param  [type] $type      服务类型
     */
    public static function getSellerList($page, $pageSize, $name = '',$type,$status,$systemTagListPid,$systemTagListId) {

        $list = SystemGoods::where('type', $type)
            ->orderBy('sort', 'desc')
            ->with('goods','systemTagListPid');
        if($systemTagListPid <= 0 ){
            $data['list'] = null;
            return $data;
        }else{
            $list->where('system_tag_list_pid',$systemTagListPid);
        }
        if (!empty($name)) {
            $list->where('name','like','%'.$name.'%');
        }
        if (!empty($status)) {
            $list->where('status',$status);
        }
        if($systemTagListId != 0 ){
            $list->where('system_tag_list_id',$systemTagListId);
        }
        $data['totalCount'] = $list->count();
        $data['list'] = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();

        return $data;
    }
    /**
     * 获取通用服务列表
     * @param  [type] $sellerId        卖家编号
     * @param  [type] $page        页码
     * @param  [type] $pageSize    分页大小
     * @param  [type] $name        关键字
     * @param  [type] $cateId      分类编号
     * @param  [type] $status      状态
     */
    public static function getListForSellerAdd($sellerId, $page, $pageSize, $name = '', $cateId = 0, $status = 0) {

        $list = Goods::with('cate')
            ->where('seller_id', 0)
            ->orderBy('sort', 'asc')
            ->orderBy('id', 'desc');

        $name = empty($name) ? '' : String::strToUnicode($name,'+');
        if (!empty($name)) {
            $list->whereRaw('MATCH(name_match) AGAINST(\'' . $name . '\' IN BOOLEAN MODE)');
        }

        if ($cateId > 0) {
            $list->where('cate_id', $cateId);
        }

        if ($status > 0) {
            $list->where('status', $status - 1);
        }

        $tablePrefix    = DB::getTablePrefix();
        $goods_table    = DB::getTablePrefix().'goods';

        $list->whereNotExists(function($query) use($sellerId, $goods_table) {
            $query->select(DB::raw(1))
                ->from('goods_seller')
                ->where('goods_seller.goods_id', '=', new Expression("{$goods_table}.id"))
                ->where('goods_seller.seller_id', '=', $sellerId);
        });

        $data['totalCount'] = $list->count();

        $data['list'] = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();

        return $data;
    }

    /**
     * 更改服务状态
     * @param int $id;  服务编号
     * @return [type] [description]
     */
    public static function updateStatus($id, $status){
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];

        if ($id < 1) {
            $result['code'] = 30214;
            return $result;
        }
        $status = $status > 0 ? 1 : 0;

        Goods::where('id',$id)->update(['status' => $status]);

        //加上蔡强那个
        if($status == 1){
            $sellerId = Goods::where('id',$id)->pluck('seller_id');
            //跟新商家信息 caiq
            $sellerGoods = Goods::where(['seller_id'=>$sellerId,'status'=>1])->select('name')->get();
            $goods_keywords = '';
            foreach($sellerGoods as $goodss){
                $goods_keywords .= preg_replace("/[^\x{4e00}-\x{9fa5}^0-9^A-Z^a-z]+/u", '', $goodss['name']).'|';//删除特殊字符
            }
            $goods_keywords_arr = explode("|",$goods_keywords);
            $goods_keywords_arr = array_flip(array_flip($goods_keywords_arr));
            $goods_keywords = implode("|",$goods_keywords_arr);
            Seller::where('id',$sellerId)->update(['goods_keywords'=>$goods_keywords]);
        }

        if($status == 0)
        {
            DB::table("shopping_cart")->where('goods_id', $id)->delete();
        }

        return $result;
    }

    public static function systemUpdate($id, $sellerId, $type, $name, $price = 0,$brief, $images, $duration, $unit, $stock, $buyLimit, $norms, $status, $sort = 100, $systemTagListPid, $systemTagListId,$isSystem =0) {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );
        //验证服务信息
        if($type == Goods::SELLER_GOODS) {
            $rules = array(
                'name' => ['required'],
//                'price' => ['required', 'regex:/^[0-9]+\.?[0-9]{0,2}$/', 'gt:0'],
//                'stock' => ['required', 'regex:/^[0-9]+$/', 'gt:0'],
                'images' => ['required'],
                'brief' => ['required'],
                'systemTagListPid' => ['required', 'gt:0'],
                'systemTagListId' => ['required', 'gt:0'],
            );

            $messages = array(
                'name.required' => 50403,
                'price.required' => 30203,
//                'price.gt' => 50405,
//                'price.regex' => 30203,
               // 'stock.required' => 88891,
               // 'stock.regex' => 88893,
              //  'stock.gt' => 88892,
                'images.required' => 10110,
                'brief.required' => 30502,
                'systemTagListPid.required' => 32000,   //请选择商品标签一级分类
                'systemTagListPid.gt' => 32000,        //请选择商品标签一级分类
                'systemTagListId.required' => 32001,    //请选择商品标签二级分类
                'systemTagListId.gt' => 32001,         //请选择商品标签二级分类
            );
            $validator = Validator::make(
                [
                    'name' => $name,
                   // 'price' => $price,
                    //'stock' => $stock,
                    'images' => is_array($images) ? implode(',', $images) : "",
                    'brief' => $brief,
                    'systemTagListPid' => $systemTagListPid,
                    'systemTagListId' => $systemTagListId,
                ], $rules, $messages);
        }else{
            $rules = array(
                'name'         => ['required'],
                'cateId'       => ['min:1'],
                'price'        => ['required', 'regex:/^[0-9]+\.?[0-9]{0,2}$/'],
                'duration'     => ['required', 'regex:/^[0-9]+$/'],
                'images'       => ['required'],
                'brief'        => ['required'],
                'systemTagListPid'  => ['required', 'gt:0'],
                'systemTagListId'   => ['required', 'gt:0'],
            );

            $messages = array (
                'name.required'     => 30102,
                'price.required'    => 30203,
                'price.regex'       => 30203,
                'duration.required' => 30209,
                'duration.regex'    => 11008,
                'images.required'   => 50225,
                'brief.required'    => 30502,
                'systemTagListPid.required' => 32000,   //请选择商品标签一级分类
                'systemTagListPid.gt'       => 32000,        //请选择商品标签一级分类
                'systemTagListId.required'  => 32001,    //请选择商品标签二级分类
                'systemTagListId.gt'        => 32001,         //请选择商品标签二级分类
            );

            $validator = Validator::make(
                [
                    'name'     => $name,
                   // 'price'    => $price,
                    'duration' => $duration,
                    'images'   => is_array($images) ? implode(',', $images) : "",
                    'brief'    => $brief,
                    'systemTagListPid'  => $systemTagListPid,
                    'systemTagListId'   => $systemTagListId,
                ], $rules, $messages);
        }
        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();

            $result['code'] = $messages->first();

            return $result;
        }
        $newImages = [];
        if($id){
            $system_goods = SystemGoods::find($id);
            if($system_goods == false) {
                $result['code'] = 30215;    // 服务不存在
                return $result;
            }

            $oldImages = $system_goods->images;

            foreach ($images as $image)  {
                if (!empty($image))  {
                    if (false !== ($key = array_search($image, $oldImages)))  {
                        unset($oldImages[$key]);

                        $newImages[] = $image;
                    } else{
                        $image = self::moveGoodsImage($sellerId, $system_goods->id, $image);
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
        }else{
            $newImages = $images;
            $system_goods = new SystemGoods();
        }
        $system_goods->stock_type_id   = 0;
        $system_goods->name  = $name;
        if($norms['stock'] <= 0 && $price <= 0){
             $result['code'] = 30203;
              return $result;
        }
        $system_goods->price  = $price;
        $system_goods->brief  = $brief;
        $system_goods->images  = implode(',', $newImages);
        $system_goods->status  = $status;
        $system_goods->sort   = $sort;
        $system_goods->system_tag_list_pid  = $systemTagListPid;
        $system_goods->system_tag_list_id  = $systemTagListId;
        $system_goods->create_time  = UTC_TIME;

        if ($type == Goods::SELLER_GOODS) {
            $system_goods->stock      = $stock;
        } else {
            $system_goods->duration        = $duration;
            $system_goods->unit            = $unit;
            $system_goods->deduct_type     = 0;
            $system_goods->deduct_val      = 0;
        }
        //验证分类是否正确
        if(!SystemTagList::where('pid', $systemTagListPid)->where('id', $systemTagListId)->first())
        {
            $result['code'] = 32002;    //两级商品标签不匹配，请刷新页面重新选择

            return $result;
        }
        //开始事务
        DB::beginTransaction();
        try{

            $system_goods->save();
            //如果是商品则编辑规格
            if($type == Goods::SELLER_GOODS) {
                if($norms['stock'] > 0){
                    $normsLog =  \YiZan\Services\GoodsService::allStockUpdate($system_goods->id,$sellerId,$norms,$isSystem);
                    if (is_numeric($normsLog))
                    {
                        $result['code'] = abs($normsLog);
                        return $result;
                    }
                    $update_infos['price']   = min($norms['skuPrice']);
                    $update_infos['stock']   = array_sum($norms['skuStock']);
                    $update_infos['stock_type_id']   = $norms['stock'];
                    SystemGoods::where('id', $system_goods->id)
                        ->update($update_infos);
                }else{
                    \YiZan\Services\GoodsService::getStockDelete($id,$isSystem);
                }
            }
            DB::commit();
        } catch(Exception $e) {
            DB::rollback();
            $result['code'] = 30217;
        }
        return $result;
    }

    /**
     * 删除服务
     * @param int  $id 服务id
     * @return array   删除结果
     */
    public static function deleteGoods($id) {
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];

        $goods = SystemGoods::where('id',$id)->first();
        if (!$goods) {
            $result['code'] = 30214;
            return $result;
        }

        DB::beginTransaction();

        try
        {
            $goods = Goods::where('system_goods_id',$id)->first();
            if(!$goods->id){
                SystemGoods::where('id', $id)->delete();
                \YiZan\Models\SystemGoodsNorms::where('system_goods_id', $id)->delete();
            }else{
                $result['code'] = 30216;
            }
            DB::commit();
        }
        catch (Exception $e)
        {
            DB::rollback();
            $result['code'] = 99999;
        }
        return $result;
    }
}