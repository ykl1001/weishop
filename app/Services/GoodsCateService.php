<?php 
namespace YiZan\Services;

use YiZan\Models\GoodsCate;
use YiZan\Models\GoodsTag;
use YiZan\Models\Seller;
use YiZan\Models\Goods;
use YiZan\Models\Order;
use YiZan\Models\OrderGoods;
use YiZan\Utils\String;
use YiZan\Utils\Time;
use DB, Validator;

class GoodsCateService extends BaseService 
{

    public static function getGoodsCateLists($sellerId, $type,$page){

        $list = GoodsCate::where('seller_id', $sellerId)
                         ->where("status", 1);
        if($type > 0){
            $list->where('type', $type);
        }
        $list = $list->orderBy('sort', 'ASC')
                     ->with('cates')
                        ->skip(($page - 1) * 20)
                        ->take(20)
                     ->get()
                     ->toArray();
        foreach ($list as $key => $value) {
            $list[$key]['goodsNum'] = Goods::where('seller_id', $sellerId)
                                           ->where('cate_id', $value['id'])
                                           ->where('status', 1)
                                           ->count();
        }
        return $list;
    }

    /**
     * 服务分类列表
     * @return array          服务分类信息
     */
    public static function wapGetList()
    {
        return GoodsCate::where("status", 1)->orderBy('sort', 'ASC')->get()->toArray();
    }
    
    /**
     * 服务二级分类列表
     * @return array          服务分类信息
     */
    public static function wapGetList2($cateId)
    {
        $goods = GoodsCate::where("status", 1)->where("pid", $cateId)->orderBy('sort', "ASC")->get();
        
        foreach($goods as $key => $value) {
            $goods[$key]->tags = GoodsTag::select('goods_tag.*')
                                    ->where('goods_tag.status', STATUS_ENABLED)
                                    ->selectRaw('COUNT(goods_id) AS goods_count')
                                    ->join('goods_tag_related', function($join) {
                                        $join->on('goods_tag_related.tag_id', '=', 'goods_tag.id');
                                    })
                                    ->join('goods', function($join) use($value) {
                                        $join->on('goods.id', '=', 'goods_tag_related.goods_id')
                                            ->where('goods.cate_id', '=', $value->id)
                                            ->where('goods.status', '=', STATUS_ENABLED);
                                    })
                                    ->groupBy('goods_tag.id')
                                    ->orderBy('goods_count','desc')
                                    ->orderBy('goods_tag.sort','asc')
                                    ->skip(0)->take(50)
                                    ->get()->toArray();
        }
        return $goods->toArray();
    }
	/**
     * 服务分类列表
     * @param int $sellerId 商家编号
     * @param int $type 类型
     * @return array          服务分类信息
     */
	public static function getList($sellerId, $type) 
    {
        $list = GoodsCate::orderBy('sort', "ASC");
        if($sellerId > 0){
            $list->where('seller_id', $sellerId);
        }
        if($type > 0){
            $list->where('type', $type);
        }
        $data = $list->with("goodsNmu")->get()->toArray();
		return $data;
	}
    /**
     * 添加服务分类
     * @param int $pid 父编号
     * @param string $name 服务名称
     * @param int $sort 排序
     * @param int $status 状态
     * @return array   创建结果
     */
    public static function create($tradeId, $pid, $type, $name, $img, $sort, $status, $sellerId = 0) 
    {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
		);

		$rules = array(
            'name'          => ['required'],
            'img'           => ['required'],
        );

        $messages = array
        (
            'name.required'     => 51000,   // 分类名称不能为空
            'img.required'      => 30903,   // 图标不能为空
        );

		$validator = Validator::make(
            [
                'name'     => $name,
                'img'      => $img
            ], $rules, $messages
        );
        
        //验证信息
		if ($validator->fails()) 
        {
	    	$messages = $validator->messages();
            
	    	$result['code'] = $messages->first();
            
	    	return $result;
	    }

        if($tradeId < 1){
            $result['code'] = 51001;    // 请选择所属行业分类
            return $result;
        }
        if($status != 1 && $status != 0){
            $result['code'] = 50302;    // 状态不合法
            return $result;
        }
        $cate = new GoodsCate();
        
        $cate->trade_id  = $tradeId;
        $cate->pid       = $pid;
        $cate->name      = $name;
        $cate->img       = $img; 
        $cate->sort      = $sort; 
        $cate->type      = $type; 
        $cate->status    = $status;
        $cate->seller_id    = $sellerId;
        
        $cate->save();
        
        return $result;
    }
    /**
     * 更新服务分类
     * @param int $id 服务分类id
     * @param int $pid 父编号
     * @param string $name 服务名称
     * @param int $sort 排序
     * @param int $status 状态
     * @return array   创建结果
     */
    public static function update($id, $tradeId, $pid = 0, $type, $name, $img, $sort, $status, $sellerId = 0) 
    {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
		);

        $rules = array(
            'name'          => ['required'],
            'img'           => ['required'],
        );

        $messages = array
        (
            'name.required'     => 51000,   // 分类名称不能为空
            'img.required'      => 30903,   // 图标不能为空
        );

        $validator = Validator::make(
            [
                'name'     => $name,
                'img'      => $img
            ], $rules, $messages
        );

        //验证信息
        if ($validator->fails())
        {
            $messages = $validator->messages();

            $result['code'] = $messages->first();

            return $result;
        }
        if($id < 1){
            $result['code'] = 51002;    // 分类不存在
            return $result;
        }
        if($tradeId < 1){
            $result['code'] = 51001;    // 请选择所属行业分类
            return $result;
        }
        if($status != 1 && $status != 0){
            $result['code'] = 50302;    // 状态不合法
            return $result;
        }
        
        GoodsCate::where('id', $id)
                 ->where('seller_id', $sellerId)
                 ->update(array(
                       'trade_id' => $tradeId,
                       'pid'      => $pid,
                       'name'     => $name,
                       'img'      => $img,
                       'sort'     => $sort,
                       'type'     => $type,
                       'status'   => $status
                   ));
       
        return $result;
    }


    /**
     * 更新服务分类
     * @param int $id 服务分类id
     * @param int $pid 父编号
     * @param string $name 服务名称
     * @param int $sort 排序
     * @param int $status 状态
     * @return array   创建结果
     */
    public static function systemUpdateCate($id, $type, $name,$sort, $status, $sellerId)
    {
        $result = array(
            'code'	=> self::SUCCESS,
            'data'	=> null,
            'msg'	=> ''
        );

        $rules = array(
            'name'          => ['required']
        );

        $messages = array
        (
            'name.required'     => 51000   // 分类名称不能为空
        );

        $validator = Validator::make(
            [
                'name'     => $name
            ], $rules, $messages
        );

        //验证信息
        if ($validator->fails())
        {
            $messages = $validator->messages();

            $result['code'] = $messages->first();

            return $result;
        }
        if($id < 1){
            $result['code'] = 51002;    // 分类不存在
            return $result;
        }
        if($status != 1 && $status != 0){
            $result['code'] = 50302;    // 状态不合法
            return $result;
        }
        GoodsCate::where('id', $id)
            ->where('seller_id', $sellerId)
            ->update([
                'name'     => $name,
                'sort'     => $sort,
                'type'     => $type,
                'status'   => $status
            ]);
        return $result;
    }

    /**
     * 删除服务分类
     * @param int  $id 服务分类id
     * @return array   删除结果
     */
	public static function delete($id, $sellerId = 0) 
    {
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ""
		];
        $goodsLists = Goods::whereIn('cate_id', $id)->where('seller_id', $sellerId)->lists('id');
        if(count($goodsLists) > 0){
            $result['code'] = 80400;
            return $result;
        }
		GoodsCate::whereIn('id', $id)->where('seller_id', $sellerId)->delete(); 
		return $result;
	}

    /**
     * 服务分类列表
     * @return array          服务分类信息
     */
    public static function getSystemList() 
    {
        return GoodsCate::where('pid',0)->where('seller_id', 0)->orderBy('sort', "ASC")->get()->toArray();
    }
    /**
     * 商家自定义分类
     */
    public static function getSellerList($sellerId, $type){
        $list = GoodsCate::where('seller_id', $sellerId)
                         ->with('cates')
                         ->orderBy('sort', "ASC");
        if($type > 0){
            $list->where('type', $type);
        }
        return $list->get()->toArray();
    }

    public static function getSellerCate($sellerId, $id){
        $cate = GoodsCate::where('id', $id)->where('seller_id', $sellerId)->first();  
        return $cate ? $cate->toArray() : [];     
    }

    /**
     * 删除分类
     */
    public static function deleteCate($sellerId, $id){

        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );

        GoodsCate::where('seller_id', $sellerId)
                 ->where('id', $id)
                 ->delete();
                 
        return $result;
    }    

    /**
     * 添加，修改分类
     */
    public static function editCate($sellerId, $id, $tradeId, $name, $type) {

        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );

        if($type == false){
            $result['code'] = 50406;
            return $result;
        }

        $name = str_replace(' ', '', $name);
        if(strlen($name) == 0){
            $result['code'] = 30102;
            return $result;
        }

        $cate = GoodsCate::where('seller_id', $sellerId)
                         ->where('id', $id)
                         ->first(); 
        //$seller = Seller::where('id', $sellerId)->with('sellerCate')->first();

        if ($cate) {
            $cate->trade_id = $tradeId;//$cate->trade_id;
            $cate->name = $name;
            $cate->type = $type;
            $cate->save();
        } else {
            $cate = new GoodsCate();
            $cate->seller_id = $sellerId;
            $cate->trade_id = $tradeId;//$seller->sellerCate[0]['cateId'];
            $cate->name = $name;
            $cate->type = $type;
            $cate->save();
        }

        $result['data'] = $cate;
             
        return $result;
    }
    /**
     * 获取行业分类
     */
    public static function getById($sellerId, $id) {

        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );

        $cate = GoodsCate::where('seller_id', $sellerId)
            ->where('id', $id)
            ->with('cates')
            ->first();
        $result['data'] = $cate;

        return $result;
    }

    /**
     * 分类排序
     */
    public static function sortCate($sellerId, $data){

        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );

        $sort = 100; 

        foreach ($data as $id) {
            $sort += 1;
            GoodsCate::where('seller_id', $sellerId)
                     ->where('id', $id)
                     ->update(['sort'=>$sort]);
        } 

        return $result;
    }

    /**
     * 更新状态
     * @param int $id 编号
     * @param int $status 状态
     * @return array   修改结果
     */
    public static function updateStatus($id, $status) {
        $result = array (
            'status'	=> true,
            'code'	    => self::SUCCESS,
            'data'	    => $status,
            'msg'	    => null
        );

        if(GoodsCate::where("id", $id)->first() == false)
        {
            $result["code"] = 51002; // 分类不存在
            return $result;
        }
        GoodsCate::where("id", $id)->update(["status"=>$status]);
        return $result;
    }
	/**
     * 添加服务分类
     * @param int $pid 父编号
     * @param string $name 服务名称
     * @param int $sort 排序
     * @param int $status 状态
     * @return array   创建结果
     */
    public static function OneselfCreate($id,$tradeId, $pid, $type, $name, $img, $sort, $status, $sellerId = 0)
    {
        $result = array(
            'code'	=> self::SUCCESS,
            'data'	=> null,
            'msg'	=> ''
        );

        $rules = array(
            'name'          => ['required'],
            'img'           => ['required'],
        );

        $messages = array
        (
            'name.required'     => 31000,   // 分类名称不能为空
            'img.required'      => 30103,   // 图标不能为空
        );

        $validator = Validator::make(
            [
                'name'     => $name,
                'img'      => $img
            ], $rules, $messages
        );

        //验证信息
        if ($validator->fails())
        {
            $messages = $validator->messages();

            $result['code'] = $messages->first();

            return $result;
        }

        if($tradeId < 1){
            $result['code'] = 51001;    // 请选择所属行业分类
            return $result;
        }
        if($status != 1 && $status != 0){
            $result['code'] = 50302;    // 状态不合法
            return $result;
        }
        if($id > 0){
            $cate = GoodsCate::where('id',$id)->where('seller_id', $sellerId)->first();
        }else{
            $cate = new GoodsCate();
        }
        $cate->trade_id  = $tradeId;
        $cate->pid       = $pid;
        $cate->name      = $name;
        $cate->img       = $img;
        $cate->sort      = $sort;
        $cate->type      = $type;
        $cate->status    = $status;
        $cate->seller_id    = $sellerId;

        $cate->save();

        return $result;
    }



    /**
     * 推荐更新状态
     * @param int $id 编号
     * @param int $status 状态
     * @return array   修改结果
     */
    public static function isWapStatus($id, $status) {
        $result = array (
            'status'	=> true,
            'code'	    => self::SUCCESS,
            'data'	    => $status,
            'msg'	    => null
        );

        if(GoodsCate::where("id", $id)->first() == false)
        {
            $result["code"] = 51002; // 分类不存在
            return $result;
        }
        GoodsCate::where("id", $id)->update(["is_wap_status"=>$status]);
        return $result;
    }

    /**
     * 服务分类列表
     * @param int $sellerId 商家编号
     * @param int $type 类型
     * @return array          服务分类信息
     */
    public static function getIsWapStatusListbak($sellerId, $cityId,$type = "",$page = 1)
    {
        $scope = Seller::find($sellerId);
        $scope = $scope->businessScope;
        array_push($scope,0);
        if(!in_array($cityId,$scope)){
            return null;
        }
        $list = GoodsCate::orderBy('sort', "ASC");
        $list->where('is_wap_status',1);
        if($sellerId > 0){
            $list->where('seller_id', $sellerId);
        }
        if($type > 0){
            $list->where('type', $type);
        }
        $list->
        $data = $list->skip(($page - 1) * 20)->take(20) ->get()->toArray();
        foreach($data as $k => $v){
            $list = Goods::where('goods.cate_id',$v['id'])->where('goods.status',1)
                ->join('goods_extend', 'goods_extend.goods_id', '=', 'goods.id')
                ->orderBy('goods_extend.sales_volume', "desc")
                ->take(3)
                ->get();
            $data[$k]['goods'] = $list;
        }
        return $data;
    }


    /**
     * 服务分类列表
     * @param int $sellerId 商家编号
     * @param int $type 类型
     * @return array          服务分类信息
     */
    public static function getIsWapStatusList($sellerId, $cityId,$type = "",$page = 1)
    {
        $scope = Seller::find($sellerId);
        $scope = $scope->businessScope;
        array_push($scope,0);
        if(!in_array($cityId,$scope)){
            return null;
        }
        $list = GoodsCate::where('seller_id',$sellerId)
                            ->where('is_wap_status', 1)
                            ->orderBy('sort', 'ASC')
                            ->get()->toArray();

        foreach($list as $k => $v){
            $list[$k]['goods'] = Goods::where('cate_id',$v['id'])->where('status',1)
                ->selectRaw("*,(
                                SELECT
                                IFNULL(sum(og.num),0) AS num
                                FROM
                                    ".env('DB_PREFIX')."order_goods AS og
                                    LEFT JOIN ".env('DB_PREFIX')."order AS o ON og.order_id = o.id
                                    WHERE o.seller_id = {$sellerId}
                                    AND  og.goods_id = ".env('DB_PREFIX')."goods.id
                                    AND  o.is_integral_goods = 0
                                    AND  o.pay_status = 1
                                    AND  o.create_time BETWEEN ".Time::getMonthFirstDay()." AND ".Time::getMonthLastDay() ."
                                    AND (
                                        o.status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.")
                                        OR (o.status = ".ORDER_STATUS_USER_DELETE." AND o.buyer_finish_time > 0 AND o.cancel_time IS NULL)
                                        OR (o.status = ".ORDER_STATUS_SELLER_DELETE." AND o.auto_finish_time > 0 AND o.cancel_time IS NULL)
                                        OR (o.status = ".ORDER_STATUS_ADMIN_DELETE." AND o.auto_finish_time > 0 AND o.cancel_time IS NULL)
                                    )
                                ) as num"
                        )->orderBy('num','desc')
                         ->orderBy('sort','desc')
                    ->take(3)
                    ->get()
                    ->toArray();
        }
        return $list;
    }
}