<?php 
namespace YiZan\Services\Sellerweb;

// use YiZan\Models\System\Goods;
use YiZan\Models\Sellerweb\Goods;
use YiZan\Models\SystemGoods;
use YiZan\Models\GoodsExtend;
use YiZan\Models\GoodsSeller;
use YiZan\Models\GoodsNorms;
use YiZan\Models\GoodsTag;
use YiZan\Models\GoodsTagRelated;
use YiZan\Models\SystemTagList;
use YiZan\Models\Activity;
use YiZan\Models\ActivityGoods;

use YiZan\Utils\String;
use YiZan\Utils\Time;
use YiZan\Models\Seller;
use DB, Validator, Lang;

class GoodsService extends \YiZan\Services\GoodsService
{
    /**
     * 服务搜索
     * @param  [type] $mobileName 手机或者名称
     * @return [type]             [description]
     */
    public static function searchGoods($name, $sellerId) {
        $list = Goods::select('id', 'name');
        if ($sellerId > 0) {
            $list->where('seller_id', $sellerId);
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
     * 服务/商品列表
     * @param  string $name 服务名称
     * @param  string $sellerName 服务人员
     * @param  int $cateId 分类编号
     * @param  array $status 状态
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          服务信息
	 */
	public static function getSystemList($sellerId, $type, $name, $cateId, $status, $notIds, $page, $pageSize) {
        $list = Goods::orderBy('sort','asc')->where('goods.seller_id', $sellerId)
                     ->select('goods.*')
                     ->with('cate'); 

        if($type > 0)
        {
            $list = $list->where('goods.type', $type);
        }

        if(!empty($notIds))
        {
            $list = $list->whereNotIn('id', $notIds);
        }

        if (!empty($name)) {
          $list->where('goods.name', 'like', '%'.$name.'%');
        }
    		if ($cateId == true) {
    			$list->where('goods.cate_id', $cateId);
    		} 

        if($status > 0) {
            $list->where('goods.status', $status - 1);
        }

    		$totalCount = $list->count();
            
    		$list = $list->skip(($page - 1) * $pageSize)
                     ->take($pageSize)
                     ->with('systemTagListPid','systemTagListId')
                     ->get()
                     ->toArray();

        return ["list"=>$list, "totalCount"=>$totalCount];
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
    public static function systemCreate($sellerId, $sellerType, $type, $staffIds, $name, $price = 0,
        $cateId, $brief, $images, $duration, $unit, $stock, $buyLimit, $norms, $status, $sort = 100, $deductType, $deductVal = 0, $systemTagListPid, $systemTagListId,$systemGoodsId,$goodsSn) {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
		);
        
        if($cateId == 0){
            $result['code'] = 60011;
            return $result;
        }

        $duration = !empty($duration) ? $duration : 0;

        if($sellerType != \YiZan\Models\Seller::SELF_ORGANIZATION && $type == Goods::SELLER_SERVICE)
        {
            if(is_array($staffIds) == false || count($staffIds) == 0) {
                $result['code'] = 30201;	// 请选择服务人员
                return $result;
            }
        }

        if($type == Goods::SELLER_GOODS)
        {
            $rules = array(
                'name'         => ['required'],
                'cateId'       => ['min:1'],
               // 'price'        => ['required', 'regex:/^[0-9]+\.?[0-9]{0,2}$/'],
                'stock'        => ['required', 'regex:/^[0-9]+$/'],
                'images'       => ['required'],
                'brief'        => ['required'],
                'systemTagListPid' => ['required', 'gt:0'],
                'systemTagListId' => ['required', 'gt:0'],
            );

            $messages = array (
                'name.required'   => 50224,
                'cateId.min'      => 60011,
                //'price.required'  => 30203,
                //'price.regex'     => 30203,
                'stock.required'  => 11002,
                'stock.regex'     => 11004,
                'images.required' => 50225,
                'brief.required'  => 50226,
                'systemTagListPid.required' => 32000,   //请选择商品标签一级分类
                'systemTagListPid.gt'       => 32000,        //请选择商品标签一级分类
                'systemTagListId.required'  => 32001,    //请选择商品标签二级分类
                'systemTagListId.gt'        => 32001,         //请选择商品标签二级分类
            );

            $validator = Validator::make(
                [
                    'name'      => $name,
                    'cateId'    => $cateId,
                    //'price'     => $price,
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
                'price'        => ['required', 'regex:/^[0-9]+\.?[0-9]{0,2}$/'],
                'images'       => ['required'],
                'brief'        => ['required'],
                'systemTagListPid' => ['required', 'gt:0'],
                'systemTagListId' => ['required', 'gt:0'],
            );

            $messages = array (
                'name.required'     => 11005,
                'cateId.min'        => 60011,
                'price.required'    => 30203,
                'price.regex'       => 30203,
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
                $result['code'] = 30203;
                return $result;
            }
            $goods->price           = $price ? $price : 0;
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
                $goods->goods_sn       = $goodsSn;
                $goods->stock_type_id   = 0;
            } else {
            	$goods->stock           = 99999;//
                $goods->duration        = $duration;
                $goods->unit            = $unit;
                $goods->deduct_type     = $deductType;
                $goods->deduct_val      = $deductVal;
            }
            $goods->status          = $status;
            $goods->sort            = $sort;
            $goods->create_time     = UTC_TIME;
            //添加商品
            $goods->save();
            $id = $goods->id;
			//更新商家信息 caiq
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
                if($norms['stock'] > 0 ){
                    $normsLog = self::allStockUpdate($id,$sellerId,$norms,0);
                    if (is_numeric($normsLog))
                    {
                        $result['code'] = abs($normsLog);
                        return $result;
                    }
                    $update_infos['price']   = min($norms['skuPrice']);
                    $update_infos['stock']   = array_sum($norms['skuStock']);
                    $update_infos['stock_type_id']   = $norms['stock'];
                    Goods::where('id', $goods->id)
                        ->where('seller_id',$sellerId)
                        ->update($update_infos);
                }else{
                    self::getStockDelete($id);
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
        $cateId, $brief, $images, $duration, $unit, $stock, $buyLimit, $norms, $status, $sort = 100, $deductType, $deductVal = 0, $systemTagListPid, $systemTagListId,$isSystem) {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );

        $duration = !empty($duration) ? $duration : 0;

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

        if($cateId == 0){
            $result['code'] = 60011;
            return $result;
        }

        //开始事务
        DB::beginTransaction();  
        try{ 
            if($goods->seller_id != 0){  
                //验证服务信息
                if($type == Goods::SELLER_GOODS)
                {
                    $rules = array(
                        'name'         => ['required'],
                        'cateId'       => ['min:1'],
                       // 'price'        => ['required', 'regex:/^[0-9]+\.?[0-9]{0,2}$/'],
                        'stock'        => ['required', 'regex:/^[0-9]+$/'],
                        'images'       => ['required'],
                        'brief'        => ['required'],
                        'systemTagListPid'  => ['required', 'gt:0'],
                        'systemTagListId'   => ['required', 'gt:0'],
                    );

                    $messages = array (
                        'name.required'   => 50224,
                        'cateId.min'      => 60011,
//                        'price.required'  => 30203,
//                        'price.regex'     => 30203,
                        'stock.required'  => 11002,
                        'stock.regex'     => 11004,
                        'images.required' => 50225,
                        'brief.required'  => 50226,
                        'systemTagListPid.required' => 32000,   //请选择商品标签一级分类
                        'systemTagListPid.gt'       => 32000,        //请选择商品标签一级分类
                        'systemTagListId.required'  => 32001,    //请选择商品标签二级分类
                        'systemTagListId.gt'        => 32001,         //请选择商品标签二级分类
                    );

                    $validator = Validator::make(
                        [
                            'name'      => $name,
                            'cateId'    => $cateId,
                            //'price'     => $price,
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
                }
                else
                {
                    $rules = array(
                        'name'         => ['required'],
                        'cateId'       => ['min:1'],
                        'price'        => ['required', 'regex:/^[0-9]+\.?[0-9]{0,2}$/'],
                        'images'       => ['required'],
                        'brief'        => ['required'],
                        'systemTagListPid'  => ['required', 'gt:0'],
                        'systemTagListId'   => ['required', 'gt:0'],
                    );

                    $messages = array (
                        'name.required'     => 11005,
                        'cateId.min'        => 60011,
                        'price.required'    => 30203,
                        'price.regex'       => 30203,
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
                            'images'   => is_array($images) ? implode(',', $images) : "",
                            'brief'    => $brief,
                            'systemTagListPid'  => $systemTagListPid,
                            'systemTagListId'   => $systemTagListId,
                        ], $rules, $messages);

                    //验证信息
                    if ($validator->fails()){
                        $messages = $validator->messages();

                        $result['code'] = $messages->first();

                        return $result;
                    }
                }
                if($norms['stock'] <= 0 && $price <= 0){

                    $result['code'] = 30203;
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
                       'price'           => $price ? $price : 0,
                       'cate_id'         => $cateId,
                       'brief'           => $brief, 
                       'images'          => implode(',', $newImages), 
                       'status'          => $status,
                       'sort'            => $sort,
                       'system_tag_list_pid'    =>  $systemTagListPid,
                       'system_tag_list_id'     =>  $systemTagListId,
                   ];
                if ($type == Goods::SELLER_GOODS) {
                    $update_info['stock']       = $stock;
                    $update_info['buy_limit']   = $buyLimit;
                    $update_info['stock_type_id']   = 0;
                } else {
                    $update_info['duration']        = $duration;
                    $update_info['unit']            = $unit; 
                    $update_info['deduct_type']     = $deductType;
                    $update_info['deduct_val']      = $deductVal;
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

            //验证分类是否正确
            if(!SystemTagList::where('pid', $systemTagListPid)->where('id', $systemTagListId)->first())
            {
                $result['code'] = 32002;    //两级商品标签不匹配，请刷新页面重新选择
                
                return $result;
            }

            //如果是商品则编辑规格
            if($type == Goods::SELLER_GOODS) {
                    if($norms['stock'] > 0){
                        //添加商品规格信息
                        $normsLog = self::allStockUpdate($goods->id,$sellerId,$norms,0);
                        if (is_numeric($normsLog))
                        {
                            $result['code'] = abs($normsLog);
                            return $result;
                        }

                        $update_infos['price']   = min($norms['skuPrice']);
                        $update_infos['stock']   = array_sum($norms['skuStock']);
                        $update_infos['stock_type_id']   = $norms['stock'];
                        Goods::where('id', $goods->id)
                            ->where('seller_id',$sellerId)
                            ->update($update_infos);
                    }else{
                        self::getStockDelete($id);
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
     * 获取服务
     * @param int $sellerId 服务人员编号
     * @param  int $id 服务id
     * @return object   服务
     */
	public static function getSystemGoodsById($sellerId, $id) {
		$goods = Goods::where('goods.id', $id)
            ->select('goods.*','goods_seller.sale_status','goods_seller.call_price','goods_seller.deduct_type','goods_seller.deduct_value')
            ->join('goods_seller', function($join) use($sellerId) {
                $join->on('goods_seller.goods_id', '=', 'goods.id')
                    ->where('goods_seller.seller_id', '=', $sellerId);
            })
            ->with('cate')
		    ->first();
        
        if($goods == true) {
            $goods->staff_ids = DB::table("goods_staff")
                ->select("seller_staff.id", "seller_staff.name")
                ->join("seller_staff", "seller_staff.id", "=", "goods_staff.staff_id")
                ->where("goods_staff.goods_id", $id)
                ->get();
            $goodsTags = DB::table("goods_tag_related")
                ->select("goods_tag.name")
                ->join("goods_tag","goods_tag.id","=","goods_tag_related.tag_id")
                ->where("goods_tag_related.goods_id", $id)
                ->get();
            $tagArr = [];
            foreach ($goodsTags as $value) { 
                $tagArr[] = $value->name;
            } 
            $goods->goods_tags = implode(',', $tagArr); 
        } 
        return $goods;
	}
    
    /**
     * 删除服务
     * @param int  $id 服务id
     * @return array   删除结果
     */
	public static function deleteSystem($sellerId,$id) 
    {
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ""
		];

        DB::beginTransaction();
        try{
            Goods::where('id', $id)->where('seller_id',$sellerId)->delete();

            //删除服务属性
            \YiZan\Models\GoodsAttr::where('goods_id', $id)->where('seller_id',$sellerId)->delete();
            //删除服务举报
            \YiZan\Models\GoodsComplain::where('goods_id', $id)->where('seller_id',$sellerId)->delete();
            //删除服务扩展
            \YiZan\Models\GoodsExtend::where('goods_id', $id)->where('seller_id',$sellerId)->delete();
            //删除服务规格
            \YiZan\Models\GoodsModel::where('goods_id', $id)->where('seller_id',$sellerId)->delete();
            //删除服务标签
            \YiZan\Models\GoodsTagRelated::where('goods_id', $id)
                                        ->where('seller_id',$sellerId)
                                        ->where('type', 0)
                                        ->delete();
            //删除提供服务的员工
            \YiZan\Models\GoodsStaff::where('goods_id', $id)->where('seller_id',$sellerId)->delete();

            self::updateSellerExtend($goods->seller_id);
            self::removeGoodsImage($goods->seller_id, $goods->id);
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
		return $result;
	}

    /**
     * [getGoodsById 获取菜品详细]
     * @param  [type] $sellerId     [服务站编号]
     * @param  [type] $id           [菜品编号]
     * @return [type]               [description]
     */
    public static function getGoodsById($sellerId, $id) { 
        $goods = Goods::where('id', $id)
                    ->where('seller_id', $sellerId) 
                    ->with(['seller', 'cate'])
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
     * 菜单审核列表
     * @param [type]  $name     [美食名称]
     * @param [type]  $status   [状态]
     * @param [type]  $page     [页码]
     * @param integer $pageSize [分页参数]
     * @return [array]             [返回数组]
     */
    public static function GoodsAuditLists($sellerId, $name, $disposeStatus, $page, $pageSize = 20) 
    {
        $list = Goods::where('seller_id', $sellerId)->where('type',1)->orderBy('id', 'desc');
        if(!empty($name)) {
            $list->where('name', 'like', '%'.$name.'%');
        }
        if(!empty($disposeStatus) > 0) {
            $list->where('dispose_status', $disposeStatus - 2 );
        }

        $totalCount = $list->count();
        $list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->with('type', 'restaurant')
            ->get()
            ->toArray();
        return ["list"=>$list, "totalCount"=>$totalCount];   
    }

    /**
     * [add 添加编辑菜品]
     * @param [type] $id           [菜品编号]
     * @param [type] $sellerId     [商家编号]
     * @param [type] $typeId       [分类编号]
     * @param [type] $restaurantId [餐厅编号]
     * @param [type] $name         [菜品名称]
     * @param [type] $images       [菜品图片]
     * @param [type] $joinService  [参与服务]
     * @param [type] $price        [现价]
     * @param [type] $oldPrice     [原价]
     * @param [type] $status       [状态， 默认为下架状态]
     * @param [type] $sort         [排序]
     */
    public static function save($id, $sellerId, $typeId, $restaurantId, $name, $images, $joinService, $price, $oldPrice, $status, $sort) {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );

        $rules = array(
            'sellerId'        => ['min:1'],
            'typeId'          => ['min:1'],
            'restaurantId'    => ['min:1'],
            'name'            => ['required'],
            'images'          => ['required'],
            'joinService'     => ['digits_between:0,4'],
            'price'           => ['numeric','min:0'],
            'oldPrice'        => ['numeric','min:0'],
            'status'          => ['numeric','required'],
            'sort'            => ['numeric','required']
        );
        
        $messages = array
        (
            'sellerId.min'          => 60010,   // 服务站参数错误
            'typeId.required'       => 60011,   // 请选择分类
            'restaurantId.min'      => 60012,   // 餐厅参数错误
            'name.required'         => 60013,   // 菜品名称不能为空
            'images.required'       => 60014,    // 请上传菜品图片
            'joinService.digits_between'   => 60015,    // 请选择参与服务
            'price.numeric'         => 60016,    // 现价必须为数字
            'price.min'             => 60017,    // 现价必须大于0
            'oldPrice.numeric'      => 60018,    // 原价必须为数字
            'oldPrice.min'          => 60019,    // 原价必须大于0
            'status.numeric'        => 60020,    // 状态错误
            'status.required'       => 60021,    // 状态错误
            'sort.numeric'          => 60022,    // 排序必须为数字
            'sort.required'         => 60023,    // 排序不能为空
        );

        $validator = Validator::make(
            [
                'sellerId'      => $sellerId,
                'typeId'        => $typeId,
                'restaurantId'      => $restaurantId,
                'name'      => $name,
                'images'        => $images,
                'joinService'       => $joinService,
                'price'     => $price,
                'oldPrice'      => $oldPrice,
                'status'        => $status,
                'sort'      => $sort,
            ], $rules, $messages);
        
        //验证信息
        if ($validator->fails()) 
        {
            $messages = $validator->messages();
            
            $result['code'] = $messages->first();
            
            return $result;
        }

        if($id > 0) {
            $goods = Goods::where('id', $id)->where('seller_id', $sellerId)->first();
            //处于上架状态不能编辑
            if($goods->status == 1) {
                $result['code'] = 60024;
                $result['msg']  = Lang::get('api_sellerweb.code.60024');
                return $result;
            }
            //处于审核状态 不能编辑
            if($goods->dispose_status == 0) {
                $result['code'] = 60025;
                $result['msg']  = Lang::get('api_sellerweb.code.60025');
                return $result;
            }
        }else{
            $goods = new Goods();
        }

        $goods->seller_id  = $sellerId;
        $goods->type_id  = $typeId;
        $goods->restaurant_id  = $restaurantId;
        $goods->name  = $name;
        $goods->images  = $images;
        $goods->join_service  = $joinService;
        $goods->price  = $price > 0 ? $price : 0;
        $goods->old_price  = $oldPrice > 0 ? $oldPrice : 0;
        $goods->status  = 0;          //不论是添加还是编辑 均为下架状态
        $goods->dispose_status  = 0;  //不论是添加还是编辑 均需要后台审核
        $goods->sort  = $sort >= 0 ? $sort : 0;
        $goods->create_time = UTC_TIME;

        $blu = $goods->save();
        if($blu) {
            $checkExtend = GoodsExtend::where('goods_id', $goods->id)->first();
            if (!$checkExtend) {
                GoodsExtend::insert([
                    'goods_id' => $goods->id,
                    'seller_id' => $goods->seller_id,
                    'restaurant_id' => $goods->restaurant_id
                ]);
            }
            $result['msg'] = Lang::get('api_sellerweb.success.success');
        }else{
            $result['msg'] = Lang::get('api_sellerweb.code.99900');
        }
        return $result;
    }

    /**
     * 删除菜品
     * @param int  $id 服务id
     * @return array   删除结果
     */
    public static function deleteGoods($sellerId,$id) 
    {
        $result = 
        [
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api_sellerweb.success.delete')
        ];

        if(!is_array($id))
            $id = [$id];
		
        DB::beginTransaction();
        try{
            Goods::whereIn('id', $id)->where('seller_id',$sellerId)->delete();
            GoodsExtend::whereIn('goods_id', $id)->where('seller_id', $sellerId)->delete();
            foreach ($id as $key => $value) {
                self::removeGoodsImage($sellerId, $value);
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
				
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
        return $result;
    }

    /**
     * [activityLists 查询商品活动列表]
     * @param  [type] $sellerId [description]
     * @param  [type] $ids      [description]
     * @return [type]           [description]
     */
    public static function activityLists ($sellerId, $ids) {
        $where = [
            'seller_id' => $sellerId,
            'status'    => 1,
        ];
        
        return Goods::where($where)->whereIn('id', $ids)->get()->toArray();
    }

    /**
     * [saveActivity 保存商家添加的活动，满减活动，特价商品]
     * @param  [int] $sellerId     [商家编号]
     * @param  [array] $full       [满减活动信息[数组]]
     * @param  [array] $special    [特价商品信息[数组]]
     * @param  [string] $checkType [保存类型：funll=满减  special=特价商品]
     * @return [array]             [返回信息]
     */
    public static function saveActivity($sellerId, $full, $special, $checkType) {
        $rules = [
            'sellerId'          => ['required'],
            'checkType'         => ['required'],
        ];

        $messages = [
            'sellerId.required'    => 61000,  //参数错误，请检测是否登录。
            'checkType.required'   => 61001,  //参数错误，请刷新网页重试！
        ];

        $validator = Validator::make(
            [
                'sellerId'       => $sellerId,
                'checkType'      => $checkType,
            ], $rules, $messages);

        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();

            $result['code'] = $messages->first();

            return $result;
        }

        if($checkType == 'full')
        {
            $data = self::activityFull($sellerId, $full);   //满减活动
        }
        if($checkType == 'special')
        {
            $data = self::activitySpecial($sellerId, $special); //特价商品
        }

        return $data;
    }

    //满减活动
    public static function activityFull($sellerId, $full) {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api_system.code.28208')
        );

        $rules = [
            'startTime'         => ['required'],
            'endTime'           => ['required'],
            'fullMoney'         => ['required','gt:0'],
            'cutMoney'          => ['required','gt:0'],
        ];

        $messages = [
            'startTime.required'    => 61002,  //请选择开始时间
            'endTime.required'      => 61003,  //请选择结束时间
            'fullMoney.required'    => 61004,  //满减金额必须大于0
            'fullMoney.gt'          => 61004,  //满减金额必须大于0
            'cutMoney.required'     => 61004,  //满减金额必须大于0
            'cutMoney.gt'           => 61004,  //满减金额必须大于0
        ];

        $validator = Validator::make(
            [
                'startTime'      => $full['startTime'],
                'endTime'        => $full['endTime'],
                'fullMoney'      => $full['fullMoney'],
                'cutMoney'       => $full['cutMoney'],
            ], $rules, $messages);

        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();

            $result['code'] = $messages->first();

            return $result;
        }

        //开始结束时间验证
        $startTime = Time::toTime($full['startTime']);
        $endTime = Time::toTime($full['endTime']) + 86400;
        if($startTime > $endTime){
            $result['code'] = 61005; //结束时间需大于开始时间
            return $result;
        }

        if($full['cutMoney'] >= $full['fullMoney'])
        {
            $result['code'] = 61006; //优惠金额不能大于等于满足条件的金额

            return $result;
        }

        //验证次数
        if($full['joinNumber'] <= 0)
        {
            $result['code'] = 61021; //请填写参与次数;
            return $result;
        }

        //保存
        // DB::beginTransaction();
        try {
           $name = "满{$full['fullMoney']}减{$full['cutMoney']}";
            $data = [
                'name'          => $name,
                'name_match'    => String::strToUnicode($name),
                'start_time'    => $startTime,
                'end_time'      => $endTime,
                'type'          => 5,
                'full_money'    => $full['fullMoney'],
                'cut_money'     => $full['cutMoney'],
                'create_time'   => UTC_TIME,
                'title'         => $name,
                'join_number'   => !empty($full['joinNumber']) ? $full['joinNumber'] : null,
                'is_system'     => 0,
                'seller_id'     => $sellerId,
            ];

            //保存活动
            $id = Activity::insertGetId($data);
            
            //刷新活动
            \YiZan\Services\Sellerweb\ActivityService::refreshActicity();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }

        return $result;
    }

    //特价商品
    public static function activitySpecial($sellerId, $special) {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api_system.code.28208')
        );

        $rules = [
            'startTime'         => ['required'],
            'endTime'           => ['required'],
            'sale'              => ['required','gt:0', 'lt:10'],
        ];

        $messages = [
            'startTime.required'    => 61002,  //请选择开始时间
            'endTime.required'      => 61003,  //请选择结束时间
            'sale.required'         => 61020,  //请填写正确的折扣参数
            'sale.gt'               => 61007,  //折扣范围0-10
            'sale.lt'               => 61007,  //折扣范围0-10
        ];

        $validator = Validator::make(
            [
                'startTime'      => $special['startTime'],
                'endTime'        => $special['endTime'],
                'sale'           => $special['sale'],
            ], $rules, $messages);

        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();

            $result['code'] = $messages->first();
            return $result;
        }

        //开始结束时间验证
        $startTime = Time::toTime($special['startTime']);
        $endTime = Time::toTime($special['endTime']) + 86400;
        if($startTime > $endTime){
            $result['code'] = 61005; //结束时间需大于开始时间
            return $result;
        }


        // 特价商品验证
        $sale = $special['sale'] / 10;
        if($sale <= 0 || $sale > 10)
        {
            $result['code'] = 61020; //请填写正确的折扣参数;
            return $result;
        }

        //验证次数
        if($special['joinNumber'] <= 0)
        {
            $result['code'] = 61021; //请填写参与次数;
            return $result;
        }

        //验证是否添加商品
        if(count($special['salePrice']) <= 0)
        {
            $result['code'] = 61008; //请至少添加一件商品
            return $result;
        }
        if( count($special['salePrice']) != count($special['ids']) )
        {
            $result['code'] = 61009; //商品参数不全
            return $result;
        }

        //保存
        // DB::beginTransaction();
        try {
           $name = "特价商品";
            $data = [
                'name'          => $name,
                'name_match'    => String::strToUnicode($name),
                'start_time'    => $startTime,
                'end_time'      => $endTime,
                'type'          => 6,
                'create_time'   => UTC_TIME,
                'title'         => $name,
                'join_number'   => !empty($special['joinNumber']) ? $special['joinNumber'] : null,
                'is_system'     => 0,
                'seller_id'     => $sellerId,
                'sale'          => $special['sale'],
            ];

            //保存活动
            $id = Activity::insertGetId($data);

            \YiZan\Services\Sellerweb\ActivityService::refreshActicity();

            //保存商品
            foreach ($special['salePrice'] as $key => $value)
            {
                $activityGoods[] = [
                    'activity_id'=>$id, 
                    'seller_id'=>$sellerId,
                    'goods_id'=>$special['ids'][$key],
                    'price'=>Goods::where('id', $special['ids'][$key])->pluck('price'),
                    'sale_price'=>$value,
                    'sale'=>$special['sale'],
                    'join_number'=>!empty($special['joinNumber']) ? $special['joinNumber'] : null,
                ];
            }
            ActivityGoods::insert($activityGoods);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
        return $result;
    }

    //查询已经存在的活动商品
    public static function hasActivityGoodsIds($sellerId) {
        $list = ActivityGoods::join('activity', function($join) use($sellerId)
                {
                    $join->on('activity_goods.activity_id', '=', 'activity.id')
                         ->where('activity.seller_id', '=', $sellerId)
                         ->where('activity.type', '=', 6)
                         ->where('activity.start_time', '<', UTC_TIME)
                         ->where('activity.end_time', '>', UTC_TIME)
                         ->where('activity.time_status', '=', 1);
                })
                ->lists('goods_id');

        return array_unique($list);
    }
}
