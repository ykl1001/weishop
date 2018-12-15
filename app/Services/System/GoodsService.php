<?php 
namespace YiZan\Services\System;

use YiZan\Models\System\Goods;
use YiZan\Models\System\GoodsExtend;
use YiZan\Models\SystemTagList;
use YiZan\Models\GoodsNorms;
use YiZan\Models\GoodsSeller;
use YiZan\Models\GoodsTag;
use YiZan\Models\GoodsTagRelated;
use YiZan\Models\Seller;
use YiZan\Utils\String;
use Illuminate\Database\Query\Expression;
use DB, Validator;

class GoodsService extends \YiZan\Services\GoodsService {
    /**
     * 菜品搜索
     * @param  [type] $mobileName 手机或者名称
     * @return [type]             [description]
     */
    public static function searchGoods($name, $sellerId) {
        $list = Goods::select('id', 'name')
                     ->where('is_del', 0);

        if ($sellerId > 0) {
            $list->where('seller_id', $sellerId);
        } else {
            $list->where('seller_id', '>', 0);
        }

        $match = empty($name) ? '' : String::strToUnicode($name,'+');
        if (!empty($match)) {
            $list->selectRaw("IF(name = '{$name}',1,0) AS eq,
                        MATCH(name_match) AGAINST('{$match}') AS similarity")
                 ->whereRaw('MATCH(name_match) AGAINST(\'' . $match . '\' IN BOOLEAN MODE)')
                 ->orderBy('eq', 'desc')
                 ->orderBy('similarity', 'desc');
        }
        return $list->orderBy('id', 'desc')->skip(0)->take(30)->get()->toArray();
    }

	/**
     * 菜品列表
     * @param  int $sellerId 商家编号
     * @param  int $type Goods类型
     * @param  string goods名称
     * @param  int $cateId 分类编号
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          菜品信息
	 */
	public static function getSystemList($sellerId, $type, $name, $cateId, $page, $pageSize)
    {
        $list = Goods::where('type', $type)->orderBy('sort', 'desc') ;
        if($sellerId > 0){
            $list->where('seller_id', $sellerId);
        }
        if ($cateId == true) {
            $list->where('goods.cate_id', $cateId);
        } 

        if ($name == true) {
            $list->where('goods.name', 'like', '%'.$name.'%');
        }

        $totalCount = $list->count();
        
        $list = $list->skip(($page - 1) * $pageSize)
                     ->take($pageSize)
                     ->select('goods.*')
                     ->with('cate','seller','systemTagListPid','systemTagListId')
                     ->get()
                     ->toArray();

        return ["list"=>$list, "totalCount"=>$totalCount];
	}


    /**
     * 服务列表
     * @param  int $type 服务类型  2:跑腿 3:家政 4:汽车 5:其他
     * @param  string $name 服务名称
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          菜品信息
     */
    public static function getServiceList($type, $name, $page, $pageSize)
    {

        $list = Goods::orderBy('id','desc');
        if ($type > 1) {
            $list->where('type', $type);
        } else {
            $list->where('type','>', 1);
        }

        if ($name != '') {
            $list->where('name','like','%'.$name.'%');

        }
        $totalCount = $list->count();

        $list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();

        return ["list"=>$list, "totalCount"=>$totalCount];
    }

    /**
     * 更改菜品状态
     * @param int $id;  菜品编号
     * @return [type] [description]
     */
    public static function auditGoods($id, $status, $isSystem, $disposeResult, $cityPrices, $adminId){  
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];

        $status = $status == 1 ? Goods::STATUS_ENABLED : Goods::STATUS_NOT_BY;
        if ($status != 1 && empty($disposeResult)) {
            $result['code'] = 30910;
            return $result;
        }

        $goods = Goods::find($id);
        if (!$goods) {
            $result['code'] = 30214;
            return $result;
        }

        //当状态为待审核时
        if($goods->status == Goods::STATUS_AUDITING){
            DB::beginTransaction();
            try {
                $goods->status              = $status;
                $goods->dispose_result      = $disposeResult;
                $goods->dispose_time        = UTC_TIME;
                $goods->dispose_admin_id    = $adminId;
                $goods->update_time         = UTC_TIME;
                if ($isSystem == 1) {
                    $goods->seller_id = 0;
                }
                $goods->save();
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                $result['code'] = 99999;
            }
        }
        return $result;
    }

    public static function updateSellerExtend($sellerId) {
        $goodsStatistics = Goods::where('seller_id', $sellerId)
                                ->selectRaw('COUNT(id) AS gcount, SUM(price) as tprice')
                                ->first();

        $sellerExtend = SellerExtend::where('seller_id', $sellerId)->first();
        if(!$sellerExtend) {
            $sellerExtend = new SellerExtend;
            $sellerExtend->seller_id = $sellerId;
        }
        
        $sellerExtend->goods_count = $goodsStatistics->gcount;
        $sellerExtend->goods_total_price = $goodsStatistics->tprice;
        $sellerExtend->goods_avg_price = $goodsStatistics->gcount == 0 ? 0 : $goodsStatistics->tprice / $goodsStatistics->gcount;
        $sellerExtend->save();
    }

    /**
     * 获取菜品
     * @param  int $id 菜品id
     * @return array   菜品
     */
	public static function getSystemGoodsById($id) {
        $goods = Goods::where('goods.id', $id)
                      ->select('goods.*') 
                      ->with('seller', 'cate')
                      ->first();
        
        if($goods == true) {
            $goods->staff_ids = DB::table("goods_staff")
                ->select("seller_staff.id", "seller_staff.name")
                ->join("seller_staff", "seller_staff.id", "=", "goods_staff.staff_id")
                ->where("goods_staff.goods_id", $id)
                ->get();  
        } 
        return $goods; 
	}

    /**
     * 删除菜品
     * @param int  $id 菜品id
     * @return array   删除结果
     */
	public static function deleteSystem($id) {
		$result = [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ""
		];

        $goods = Goods::find($id);
        if (!$goods || $goods->seller_id < 1) {
            $result['code'] = 30214;
        }

        DB::beginTransaction();
        
        try
        {
            Goods::where('id', $id)->delete();

            //删除菜品扩展
            \YiZan\Models\GoodsExtend::where('goods_id', $id)->delete();
            
            DB::commit();
        } 
        catch (Exception $e) 
        {
            DB::rollback();
            $result['code'] = 99999;
        }
		return $result;
	} 

    /**
     * 菜品审核
     * @param int $id 菜品编号
     * @param int $status 处理状态
     * @param strint $remark 处理备注
     */
    public static function dispose($id, $status, $remark) {
        $result = [
            'code' => 0,
            'msg' => '',
            'data' => null
        ];
        $check = Goods::where('id', $id)->first();
        if (!$check) {
            $result['code'] = 50406;
            return $result;
        }
        if ($check->dispose_status != 0) {
            $result['code'] = 50407;
            return $result;
        }
        Goods::where('id', $id)->update([
            'dispose_status' => $status,
            'dispose_result' => $remark,
            'dispose_time' => UTC_TIME
        ]);
        return $result;
    }

    /**
     * 添加服务/商品 
     * @param int $sellerId 服务人员编号
     * @param int $sellerType 服务商类型 
     * @param int $type 类型 
     * @param array $staffIds 可以提供此服务的员工
     * @param string $name 服务名称
     * @param double $price 价格 
     * @param int $cateId 分类编号
     * @param string $brief 简介
     * @param array $images 图片数组
     * @param int $duration 时长（秒）
     * @param int $unit 计时单位
     * @param int $stock 库存
     * @param int $buyLimit 每人限制
     * @param string $norms 规格
     * @param int $sort 排序 
     * @return array   创建结果
     */
    public static function systemSave( $sellerId, $sellerType, $type, $staffIds, $name, $price = 0, $cateId, $brief, $images, $duration, $unit, $stock, $buyLimit, $norms, $status, $sort = 100, $systemTagListPid, $systemTagListId,$systemGoodsId = 0,$goodsSn,$isSystem) {

        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );
        if($cateId == 0){
            $result['code'] = 30206;
            return $result;
        }
        if($sellerType != \YiZan\Models\Seller::SELF_ORGANIZATION && $type == Goods::SELLER_SERVICE)
        {
            if(is_array($staffIds) == false || count($staffIds) == 0) {
                $result['code'] = 30201;    // 请选择服务人员
                return $result;
            }
        }

        if($type == Goods::SELLER_GOODS)
        {
            $rules = array(
                'name'         => ['required'],
                'cateId'       => ['min:1'],
                'price'        => ['required', 'regex:/^[0-9]+\.?[0-9]{0,2}$/'],
                'stock'        => ['required', 'regex:/^[0-9]+$/'],
                'images'       => ['required'],
                'brief'        => ['required'],
                'systemTagListPid' => ['required', 'gt:0'],
                'systemTagListId' => ['required', 'gt:0'],
            );

            $messages = array (
                'name.required'   => 30202,
                'cateId.min'      => 30206,
                'price.required'  => 30203,
                'price.regex'     => 30203,
                'stock.required'  => 11002,
                'stock.regex'     => 11004,
                'images.required' => 30208,
                'brief.required'  => 30207,
                'systemTagListPid.required' => 32000,   //请选择商品标签一级分类
                'systemTagListPid.gt'       => 32000,        //请选择商品标签一级分类
                'systemTagListId.required'  => 32001,    //请选择商品标签二级分类
                'systemTagListId.gt'        => 32001,         //请选择商品标签二级分类
            );

            $validator = Validator::make(
                [
                    'name'      => $name,
                    'cateId'    => $cateId,
                    'price'     => $price,
                    'stock'     => $stock,
                    'images'    => is_array($images) ? implode(',', $images) : "",
                    'brief'     => $brief,
                    'systemTagListPid'  => $systemTagListPid,
                    'systemTagListId'   => $systemTagListId
                ], $rules, $messages);

            //验证信息
            if ($validator->fails()){
                $messages = $validator->messages();

                $result['code'] = $messages->first();

                return $result;
            }

            //有就验证商品编码的长度
            if($goodsSn  != ""){
                $patrn = '/^([a-z]|[A-Z]|[0-9]){8}$/';
                if (!$patrn.exec($goodsSn)){
                    $result['code'] = 35018;
                    return $result;
                }
                if(strlen($goodsSn) > 17){
                    $result['code'] = 35017;
                    return $result;
                }
                if(Goods::where('goods_sn', $goodsSn)->count() > 0){
                    $result['code'] = 35019;
                    return $result;
                }
            }
        }
        else
        {
            $rules = array(
                'name'         => ['required'],
                'cateId'       => ['min:1'],
               // 'price'        => ['required', 'regex:/^[0-9]+\.?[0-9]{0,2}$/'],
                'duration'     => ['required', 'regex:/^[0-9]+$/'],
                'images'       => ['required'],
                'brief'        => ['required'],
                'systemTagListPid' => ['required', 'gt:0'],
                'systemTagListId' => ['required', 'gt:0'],
            );

            $messages = array (
                'name.required'     => 11005,
                'cateId.min'        => 30206,
//                'price.required'    => 30203,
//                'price.regex'       => 30203,
                'duration.required' => 30209,
                'duration.regex'    => 11008,
                'images.required'   => 11006,
                'brief.required'    => 11007,
                'systemTagListPid.required' => 32000,   //请选择商品标签一级分类
                'systemTagListPid.gt'       => 32000,        //请选择商品标签一级分类
                'systemTagListId.required'  => 32001,    //请选择商品标签二级分类
                'systemTagListId.gt'        => 32001,         //请选择商品标签二级分类
            );

            $validator = Validator::make(
                [
                    'name'     => $name,
                    'cateId'   => $cateId,
                    'price'    => $price,
                    'duration' => $duration,
                    'images'   => is_array($images) ? implode(',', $images) : "",
                    'brief'    => $brief,
                    'systemTagListPid'  => $systemTagListPid,
                    'systemTagListId'   => $systemTagListId
                ], $rules, $messages);

            //验证信息
            if ($validator->fails()){
                $messages = $validator->messages();

                $result['code'] = $messages->first();

                return $result;
            }
        }

        //验证分类是否正确
        if(!SystemTagList::where('pid', $systemTagListPid)->where('id', $systemTagListId)->first())
        {
            $result['code'] = 32002;    //两级商品标签不匹配，请刷新页面重新选择

            return $result;
        }

        //开始事务
        DB::beginTransaction();
        $dbPrefix = DB::getTablePrefix();   
        try{
            $goods = new Goods();
            
            $goods->seller_id       = $sellerId;
            $goods->type            = $type;
            $goods->name            = $name;
            $goods->cate_id         = $cateId;
            $goods->brief           = $brief;
            $goods->system_tag_list_pid    =  $systemTagListPid;
            $goods->system_tag_list_id     =  $systemTagListId;
            $goods->system_goods_id     =  $systemGoodsId;
            if($norms['stock'] <= 0 && $price <= 0){
                $result['code'] = 30203;    //两级商品标签不匹配，请刷新页面重新选择
                return $result;
            }
            $goods->price           = $price;
            $newImages = [];
            foreach ($images as $image) {
                if (!empty($image)) {
                    $image = self::moveGoodsImage($goods->seller_id, $goods->id, $image);
                    //转移图片失败
                    if (!$image) {
                        $result['code'] = 30213;
                        
                        return $result;
                    }                    
                    $newImages[] = $image;
                }
            }
            $goods->images          = count($newImages) ? implode(',', $newImages) : "";
            if ($type == Goods::SELLER_GOODS) {
                $goods->stock           = $stock;
                $goods->buy_limit       = $buyLimit;
                $goods->goods_sn        = $goodsSn;
                $goods->stock_type_id   = 0;

            } else {
                $goods->stock           = 99999;//
                $goods->duration        = $duration;
                $goods->unit            = $unit;
                $goods->deduct_type     = 0;//$deductType;
                $goods->deduct_val      = 0;//$deductVal;
            }
            $goods->status          = $status;
            $goods->sort            = $sort;
            $goods->create_time     = UTC_TIME;
            $goods->save();
            $id = $goods->id;
            //跟新商家信息 caiq
            if($goods->status==1 && $goods->stock>0){
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
            //添加商品扩展信息
            $goodsExtend = new GoodsExtend();
            $goodsExtend->seller_id = $sellerId;
            $goodsExtend->goods_id  = $id;
            $goodsExtend->save();
            if($type == Goods::SELLER_GOODS) {
                if($norms['stock'] > 0){
                    $normsLog = self::allStockUpdate($goods->id,$sellerId,$norms,$isSystem);
                    if (is_numeric($normsLog))
                    {
                        $result['code'] = abs($normsLog);
                        return $result;
                    }
                    $update_infos['price']   =min($norms['skuPrice']);
                    $update_infos['stock']   = array_sum($norms['skuStock']);
                    $update_infos['stock_type_id']   = $norms['stock'];
                    Goods::where('id', $goods->id)
                        ->where('seller_id',$sellerId)
                        ->update($update_infos);
                }
            } else {
                if(is_array($staffIds) == true && count($staffIds) >= 1) {
                    $staffIds = implode(",", $staffIds);
                    
                    self::replaceIn($staffIds);
                    
                    $sql = "
                            INSERT INTO {$dbPrefix}goods_staff
                            (
                            staff_id,
                            goods_id, 
                            seller_id
                            )
                            SELECT  id,
                                {$id}, 
                                {$sellerId}
                            FROM {$dbPrefix}seller_staff 
                            WHERE id IN ({$staffIds})";
                    
                    DB::unprepared($sql);
                } else {
                    $sql = "
                            INSERT INTO {$dbPrefix}goods_staff
                            (
                            staff_id,
                            goods_id, 
                            seller_id
                            )
                            SELECT  id,
                                {$id}, 
                                {$sellerId}
                            FROM {$dbPrefix}seller_staff 
                            WHERE seller_id = {$sellerId}";
                    
                    DB::unprepared($sql);
                } 
            }    
            DB::commit();
        } catch(Exception $e){
            DB::rollback();
            $result['code'] = 30217; 
        }
        return $result;
    }  

    /**
     * 更新服务/商品
     * @param int $id       服务/商品编号
     * @param int $sellerId 服务人员编号
     * @param int $sellerType 服务商类型 
     * @param int $type 类型 
     * @param array $staffIds 可以提供此服务的员工
     * @param string $name 服务名称
     * @param double $price 价格 
     * @param int $cateId 分类编号
     * @param string $brief 简介
     * @param array $images 图片数组
     * @param int $duration 时长（秒）
     * @param int $unit 计时单位
     * @param int $stock 库存
     * @param int $buyLimit 每人限制
     * @param string $norms 规格
     * @param int $sort 排序 
     * @return array   创建结果
     */
    public static function systemUpdate($id, $sellerId, $sellerType, $type, $staffIds, $name, $price = 0,
        $cateId, $brief, $images, $duration, $unit, $stock, $buyLimit, $norms, $status, $sort = 100, $systemTagListPid, $systemTagListId) {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );
        
        if($sellerType != \YiZan\Models\Seller::SELF_ORGANIZATION && $type == Goods::SELLER_SERVICE)
        {
            if(is_array($staffIds) == false || 
                count($staffIds) == 0)
            {
                $result['code'] = 30201;    // 请选择服务人员
                
                return $result;
            }
        }  

        $goods = Goods::find($id);

        if($goods == false) {
            $result['code'] = 30215;    // 服务不存在
            
            return $result;
        }

        //开始事务
        DB::beginTransaction();
        try{
            if($goods->seller_id != 0){  
                //验证服务信息
                $rules = array( 
                    'name'         => ['required'],
                    //'price'        => ['min:1'],
                    'cateId'       => ['min:1'],
                    'brief'        => ['required'],
                    'images'       => ['required'],
                    'systemTagListPid'  => ['required', 'gt:0'],
                    'systemTagListId'   => ['required', 'gt:0'],
                );
                
                $messages = array ( 
                    'name.required'     => 30202,   // 名称不能为空
                    //'price.min'           => 30204,   // 请设置正确的门店价格
                    'cateId.min'        => 30206,   // 请选择服务分类
                    'brief.required'    => 30207,   // 简介不能为空
                    'images.required'   => 30208,    // 请上传服务图片
                    'systemTagListPid.required' => 32000,   //请选择商品标签一级分类
                    'systemTagListPid.gt'       => 32000,        //请选择商品标签一级分类
                    'systemTagListId.required'  => 32001,    //请选择商品标签二级分类
                    'systemTagListId.gt'        => 32001,         //请选择商品标签二级分类
                );

                $validator = Validator::make(
                    [ 
                        'name'      => $name,
                        //'price'     => $price,
                        'cateId'    => $cateId,
                        'brief'     => $brief,
                        'images'    => is_array($images) ? implode(',', $images) : "",
                        'systemTagListPid'  => $systemTagListPid,
                        'systemTagListId'   => $systemTagListId
                    ], $rules, $messages);
                
                //验证信息
                if ($validator->fails()){
                    $messages = $validator->messages();
                    
                    $result['code'] = $messages->first();
                    
                    return $result;
                } 

                //验证分类是否正确
                if(!SystemTagList::where('pid', $systemTagListPid)->where('id', $systemTagListId)->first())
                {
                    $result['code'] = 32002;    //两级商品标签不匹配，请刷新页面重新选择
                    
                    return $result;
                }
                
                $newImages = [];
                
                $oldImages = $goods->images;
                
                foreach ($images as $image)  {
                    if (!empty($image))  { 
                        if (false !== ($key = array_search($image, $oldImages)))  { 
                            unset($oldImages[$key]);
                            
                            $newImages[] = $image; 
                        } else{
                            $image = self::moveGoodsImage($goods->seller_id, $goods->id, $image); 
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
                $update_info = [  
                       'name'            => $name, 
                       'price'           => $price,
                       'cate_id'         => $cateId,
                       'brief'           => $brief, 
                       'images'          => implode(',', $newImages), 
                       'status'          => $status,
                       'sort'            => $sort,
                       'system_tag_list_pid'    =>  $systemTagListPid,
                        'system_tag_list_id'     =>  $systemTagListId,
                        'stock_type_id'     =>  0
                   ];
                if ($type == Goods::SELLER_GOODS) {
                    $update_info['stock']       = $stock;
                    $update_info['buy_limit']   = $buyLimit;
                } else {
                    $update_info['duration']    = $duration;
                    $update_info['unit']        = $unit; 
                }

                if($norms['stock'] <= 0 && $price <= 0){
                    $result['code'] = 30204;
                    return $result;
                }

                if($norms['stock']<= 0 && $stock <= 0){
                    $result['code'] = 11004;
                    return $result;
                }

                Goods::where('id', $id)
                     ->where('seller_id',$sellerId)
                     ->update($update_info); 
			    

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
            //如果是商品则编辑规格
            if($type == Goods::SELLER_GOODS) {
                if($norms['stock'] > 0){
                    $normsLog = self::allStockUpdate($id,$sellerId,$norms,0);
                    if (is_numeric($normsLog))
                    {
                        $result['code'] = abs($normsLog);
                        return $result;
                    }
                    $update_infos['price']   = min($norms['skuPrice']);
                    $update_infos['stock']   = array_sum($norms['skuStock']);
                    $update_infos['stock_type_id']   = $norms['stock'];
                    Goods::where('id', $id)
                        ->where('seller_id',$sellerId)
                        ->update($update_infos);
                }

            } else {
                //修改服务的服务员工信息
                $dbPrefix = DB::getTablePrefix();
                
                if($sellerType != \YiZan\Models\Seller::SELF_ORGANIZATION) {

                    $sql = "DELETE FROM {$dbPrefix}goods_staff WHERE goods_id = {$id} AND seller_id = {$sellerId}";
                    
                    DB::unprepared($sql);
                    
                    if(is_array($staffIds) == true && count($staffIds) >= 1){
                        $staffIds = implode(",", $staffIds);
                        
                        self::replaceIn($staffIds);
                        
                        $sql = "
                            INSERT INTO {$dbPrefix}goods_staff
                            (
                                staff_id,
                                goods_id, 
                                seller_id
                            )
                            SELECT  id,
                                    {$id}, 
                                    {$sellerId}
                                FROM {$dbPrefix}seller_staff 
                                WHERE id IN ({$staffIds})";
                        
                        DB::unprepared($sql);
                    }
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
     * 删除菜品
     * @param array  $ids 菜品id
     * @return array   删除结果
     */
    public static function deleteService($id) {
        $result = [
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> ""
        ];
        Goods::whereIn('id', $id)->delete();
        return $result;
    }


}
