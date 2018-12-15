<?php 
namespace YiZan\Services\Staff;

//use YiZan\Models\System\Goods;
use YiZan\Models\Sellerweb\Goods;
use YiZan\Models\SystemGoods;
use YiZan\Models\GoodsExtend;
use YiZan\Models\GoodsSeller;
use YiZan\Models\GoodsNorms;
use YiZan\Models\GoodsCate;
use YiZan\Models\SellerStaff;
use YiZan\Models\GoodsTagRelated;
use YiZan\Models\Seller;
use YiZan\Models\SystemTagList;

use YiZan\Utils\String;

use DB, Validator, Lang;

class GoodsService extends \YiZan\Services\GoodsService
{
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
    public static function  getLists($sellerId, $page, $cateId, $status = 1, $keywords) {
        $list = Goods::where('cate_id', $cateId)
                     ->where('seller_id', $sellerId)
                     ->select('goods.*')
                     ->with('cate', 'extend','norms', 'goodsStaff.staffers'); 

        if ($status > 1) {
            $list->where('status', 0);
        } else {
            $list->where('status', $status);
        }
        if (!empty($keywords)) {
           $list->where('name', 'like', '%'.$keywords.'%');
        }

        $list = $list->skip(($page - 1) * 20)
                     ->take(20)
                     ->get()
                     ->toArray();
 //print_r(DB::getQueryLog());exit;
        foreach ($list as $key => $value) {
            $list[$key]['saleCount'] = $value['extend']['salesVolume'];
            $list[$key]['date'] = yzday($value['createTime']);
            $list[$key]['imgs'] = $value['images'];
            foreach ($list[$key]['goodsStaff'] as $k => $v) {
                $list[$key]['staff'][] = $v['staffers'];
            }
           
           // $list[$key]['staff'] = SellerStaff::whereIn('id', $list[$key]['staffIds'])->get()->toArray();
        }
        // print_r($list);
        // exit;
        return $list;
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
    public static function goodsCreate($sellerId, $cateId, $name, $images, $norms, $brief, $price, 
        $duration, $staffIds, $stock, $sort = 100, $type, $sellerType, $systemTagListPid, $systemTagListId,$systemGoodsId,$goodsSn="") {

        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );
        if($sellerType != \YiZan\Models\Seller::SELF_ORGANIZATION && $type == \YiZan\Models\Goods::SELLER_SERVICE)
        {
            $staffs = $staffIds[0];
            if(is_array($staffIds) == false || count($staffIds) == 0 || $staffs == '')
            {
                $result['code'] = 30201;    // 请选择服务人员
                return $result;
            }
        }

        //开始事务
        DB::beginTransaction();
        $dbPrefix = DB::getTablePrefix();   
        try{
            if(count($norms) == 0 && ($type == \YiZan\Models\Goods::SELLER_GOODS || $type == \YiZan\Models\Goods::SELLER_SERVICE))
            {
                if($type == \YiZan\Models\Goods::SELLER_SERVICE)
                {
                    $rules = array(
                        'images'   => ['required'],
                        'name'     => ['required'],
                        'price'    => ['required','min:0'],
                        'duration' => ['required','min:1'],
                        'cateId'   => ['min:1'],
                        'brief'    => ['required']
                    );
                    $messages = array
                    (
                        'images.required'   => 30208,   // 请上传图片
                        'name.required'     => 30202,   // 名称不能为空
                        'price.required'    => 11001,   // 单价不能为空
                        'price.min'         => 11003,   // 单价错误
                        'duration.required' => 30209,   // 请设置服务时长
                        'duration.min'      => 30210,   // 服务时长不能小于1分钟
                        'cateId.min'        => 30206,   // 请选择服务分类
                        'brief.required'    => 30207,   // 简介不能为空
                    );

                    

                    $validator = Validator::make(
                        [
                            'images'   => is_array($images) ? implode(',', $images) : "",
                            'name'     => $name,
                            'price'    => $price,
                            'duration' => $duration,
                            'cateId'   => $cateId,
                            'brief'    => $brief
                        ], $rules, $messages);
                    //验证信息
                    if ($validator->fails())
                    {
                        $messages = $validator->messages();
                        $result['code'] = $messages->first();
                        return $result;
                    }
                }
                else
                {
                    $rules = array(
                        'images' => ['required'],
                        'name'   => ['required'],
                        'price'  => ['required','min:0'],
                        'stock'  => ['required','min:0'],
                        'cateId' => ['min:1'],
                        'brief'  => ['required']
                    );
                    $messages = array
                    (
                        'images.required' => 30208,   // 请上传图片
                        'name.required'   => 30202,   // 名称不能为空
                        'price.required'  => 11001,   // 单价不能为空
                        'price.min'       => 11003,   // 单价错误
                        'stock.required'  => 11002,   // 库存不能为空
                        'stock.min'       => 11004,   // 库存错误
                        'cateId.min'      => 30206,   // 请选择服务分类
                        'brief.required'  => 30207,   // 简介不能为空
                    );

                    $validator = Validator::make(
                        [
                            'images'    => is_array($images) ? implode(',', $images) : "",
                            'name'      => $name,
                            'price'     => $price,
                            'stock'     => $stock,
                            'cateId'    => $cateId,
                            'brief'     => $brief
                        ], $rules, $messages);
                    //验证信息
                    if ($validator->fails())
                    {
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
                $goods = new Goods();
                $goods->seller_id       = $sellerId;
                $goods->type            = $type;
                $goods->name            = $name;
                $goods->cate_id         = $cateId;
                $goods->brief           = $brief;

                $newImages = [];

                if (!empty($images))
                {
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
                }

                $goods->images = count($newImages) ? implode(',', $newImages) : "";

                if(!preg_match('/^[0-9]+\.?[0-9]{0,2}$/', $price)){
                    $result['code'] = 11003;
                    return $result;
                }
                if ($type == \YiZan\Models\Goods::SELLER_GOODS)
                {
                    $goods->stock           = $stock;
                    $goods->price           = $price;
                    $goods->goods_sn        = $goodsSn;
                }
                else
                {
                    $goods->price           = $price;
                    $goods->duration        = $duration;
                    $goods->unit            = 0; //分钟
                }
            }
            else
            {
                $rules = array(
                    'cateId' => ['min:1'],
                    'images' => ['required'],
                    'name'   => ['required'],
                );
                $messages = array
                (
                    'cateId.min'      => 30206,   // 请选择分类
                    'images.required' => 30208,   // 请上传图片
                    'name.required'   => 30202,   // 名称不能为空
                );
                $validator = Validator::make(
                    [
                        'cateId'    => $cateId,
                        'images'    => is_array($images) ? implode(',', $images) : "",
                        'name'      => $name
                    ], $rules, $messages);
                //验证信息
                if ($validator->fails())
                {
                    $messages = $validator->messages();
                    $result['code'] = $messages->first();
                    return $result;
                }

                $goods = new Goods();
                $goods->seller_id       = $sellerId;
                $goods->type            = $type;
                $goods->name            = $name;
                $goods->cate_id         = $cateId;

                $goods->system_tag_list_pid   =  $systemTagListPid;
                $goods->system_tag_list_id   =  $systemTagListId;

                $newImages = [];

                if (!empty($images))
                {
                    foreach ($images as $image)
                    {
                        if (!empty($image))
                        {
                            $image = self::moveGoodsImage($goods->seller_id, $goods->id, $image);
                            //转移图片失败
                            if (!$image) {
                                $result['code'] = 30213;
                                return $result;
                            }
                            $newImages[] = $image;
                        }
                    }
                }
                $goods->images = count($newImages) ? implode(',', $newImages) : "";

                foreach($norms as $keys => $values)
                {
                    if(str_replace(' ','',$values['name']) == ''){
                        $result['code'] = 11000;
                        return $result;
                    }
                    if(str_replace(' ','',$values['price']) == ''){
                        $result['code'] = 11001;
                        return $result;
                    }
                    if(!preg_match('/^[0-9]+\.?[0-9]{0,2}$/', $values['price'])){
                        $result['code'] = 11003;
                        return $result;
                    }
                    if(sprintf("%.2f", $values['price']) < 0){
                        $result['code'] = 11003;
                        return $result;
                    }
                    if($values['stock'] == ''){
                        $result['code'] = 11002;
                        return $result;
                    }
                    if(!preg_match('/^[0-9]+$/', $values['stock'])){
                        $result['code'] = 11004;
                        return $result;
                    }
                }
                foreach($norms as $v)
                {
                    $stocks = $v['stock'];
                    $prices = $v['price'];
                    break;
                }
                $goods->stock = (int)$stocks;
                $goods->price = (double)$prices;

                if(str_replace(' ', '', $brief) == ''){
                    $result['code'] = 30207;
                    return $result;
                }
                $goods->brief           = $brief;
                $goods->system_goods_id  = $systemGoodsId;
            }

            $goods->system_tag_list_pid   =  $systemTagListPid;
            $goods->system_tag_list_id   =  $systemTagListId;
            $goods->status          = 1; //默认上架
            $goods->sort            = $sort;
            $goods->create_time     = UTC_TIME;
            //添加商品
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


            if($type == \YiZan\Models\Goods::SELLER_GOODS) {
                //添加商品规格信息
                //var_dump($norms);
                //$norms = json_decode($norms, true);
                foreach ($norms as $key => $value) {
                    $goods_norms = new GoodsNorms();
                    $goods_norms->seller_id     = $sellerId;
                    $goods_norms->goods_id      = $goods->id;
                    $goods_norms->name          = $value['name'];
                    $goods_norms->price         = $value['price'];
                    $goods_norms->stock         = (int)$value['stock'];
                    $goods_norms->save(); 
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
        $result['data'] = $goods;
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
    public static function goodsUpdate( $sellerId, $id, $cateId, $name, $images, $norms, $brief, $price, 
        $duration, $staffIds, $stock, $systemTagListPid, $systemTagListId,$systemGoodsId,$goodsSn) {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );
        $type = GoodsCate::where('id', $cateId)->pluck('type');
        $sellerType = Seller::where('id', $sellerId)->pluck('type');
        if ($id < 1) {
            return self::goodsCreate($sellerId, $cateId, $name, $images, $norms, $brief, $price, $duration, $staffIds, $stock, 100, $type, $sellerType, $systemTagListPid, $systemTagListId,$systemGoodsId,$goodsSn);//, $deductType(提成方式1固定2百分比'), $deductVal('提成值')
        } else {
            if($sellerType != \YiZan\Models\Seller::SELF_ORGANIZATION && $type == \YiZan\Models\Goods::SELLER_SERVICE)
            {
                $staffs = $staffIds[0];
                if(is_array($staffIds) == false || 
                    count($staffIds) == 0 || $staffs == '')
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
//            DB::beginTransaction();
//            try{
                if($goods->seller_id != 0){
                    $rules = array(
                        'imgs'  => ['required'],
                        'name'  => ['required'],
                        'brief' => ['required'],
                        'systemTagListPid'  => ['required', 'gt:0'],
                        'systemTagListId'   => ['required', 'gt:0'],
                    );

                    $messages = array
                    (
                        'imgs.required'  => 30208,    // 请上传服务图片
                        'name.required'  => 30202,   // 名称不能为空
                        'brief.required' => 30207,   // 简介不能为空
                        'systemTagListPid.required' => 32000,   //请选择商品标签一级分类
                        'systemTagListPid.gt'       => 32000,        //请选择商品标签一级分类
                        'systemTagListId.required'  => 32001,    //请选择商品标签二级分类
                        'systemTagListId.gt'        => 32001,         //请选择商品标签二级分类
                    );

                    $validator = Validator::make(
                        [
                            'imgs'    => is_array($images) ? implode(',', $images) : "",
                            'name'      => $name,
                            'cateId'    => $cateId,
                            'brief'     => $brief,
                            'systemTagListPid'  => $systemTagListPid,
                            'systemTagListId'   => $systemTagListId,
                        ], $rules, $messages);

                    //验证信息
                    if ($validator->fails())
                    {
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
                    if (!empty($images)) {
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
                    }

                    $update_info = [  
                           'name'            => $name, 
                           'cate_id'         => $cateId,
                           'brief'           => $brief, 
                           'images'          => implode(',', $newImages),
                           'system_tag_list_pid'    =>  $systemTagListPid,
                           'system_tag_list_id'     =>  $systemTagListId,
                       ];
                    if ($type == Goods::SELLER_GOODS) {
                        if($norms)
                        {
                            foreach($norms as $keys => $values)
                            {
                                if(str_replace(' ','',$values['name']) == ''){
                                    $result['code'] = 11000;
                                    return $result;
                                }
                                if(str_replace(' ','',$values['price']) == ''){
                                    $result['code'] = 11001;
                                    return $result;
                                }
                                if(!preg_match('/^[0-9]+\.?[0-9]{0,2}$/', $values['price'])){
                                    $result['code'] = 11003;
                                    return $result;
                                }
                                if(sprintf("%.2f", $values['price']) < 0){
                                    $result['code'] = 11003;
                                    return $result;
                                }
                                if($values['stock'] == ''){
                                    $result['code'] = 11002;
                                    return $result;
                                }
                                if((int)$values['stock'] < 0){
                                    $result['code'] = 11004;
                                    return $result;
                                }
                            }
                            foreach($norms as $v){
                                $stocks = $v['stock'];
                                $prices = $v['price'];
                                break;
                            }
                        }
                        else
                        {
                            if(!preg_match('/^[0-9]+\.?[0-9]{0,2}$/', $price)){
                                $result['code'] = 11003;
                                return $result;
                            }
                            if(!preg_match('/^[0-9]+$/', $stock)){
                                $result['code'] = 11004;
                                return $result;
                            }
                        }
                        $update_info['stock']       = $norms ? (int)$stocks : $stock;
                        $update_info['price']       = $norms ? (double)$prices : $price;
                        $update_info['system_goods_id']  = $systemGoodsId;
                    } else {
                        if(!preg_match('/^[0-9]+\.?[0-9]{0,2}$/', $price)){
                            $result['code'] = 30203;
                            return $result;
                        }
                        if(!preg_match('/^[0-9]+$/', $duration)){
                            $result['code'] = 11006;
                            return $result;
                        }
                        $update_info['price']       = $price;
                        $update_info['duration']    = $duration;
                        $update_info['unit']        = 0; 
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
                if($type == \YiZan\Models\Goods::SELLER_GOODS) {
                    //添加商品规格信息
                   // $norms = json_decode($norms, true); 
                    //删除不在编辑列表中的规格
                    if($norms){
                        foreach ($norms as $key => $value) {
                            $nids[] = $value['id'];
                        }
                        if(!empty($nids)){
                            GoodsNorms::whereNotIn('id',$nids)
                                ->where('seller_id', $sellerId)
                                ->where('goods_id', $id)
                                ->delete();
                        }
                    } else {
                        GoodsNorms::where('seller_id', $sellerId)
                            ->where('goods_id', $id)
                            ->delete();
                    }

                    foreach ($norms as $key => $value) {          
                        $norms_item = GoodsNorms::where('seller_id', $sellerId)
                                                ->where('goods_id', $id)
                                                ->where('id', $value['id'])
                                                ->first();
                        if($norms_item){
                            $norms_item->price = $value['price'];
                            $norms_item->name = $value['name'];
                            $norms_item->stock = (int)$value['stock']; 
                            $norms_item->save();
                        } else {
                            $goods_norms = new GoodsNorms();
                            $goods_norms->seller_id     = $sellerId;
                            $goods_norms->goods_id      = $goods->id;
                            $goods_norms->name          = $value['name'];
                            $goods_norms->price         = $value['price'];
                            $goods_norms->stock         = (int)$value['stock'];
                            $goods_norms->save();
                        }
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
                 
//                DB::commit();
//            } catch(Exception $e) {
//                DB::rollback();
//                $result['code'] = 30217;
//            }
        }
 
        return $result;
    }    

    /**
     * 操作服务，上下架，删除
     * @param int  $id 服务id
     * @return array   结果
     */
    public static function opGoods($sellerId, $ids, $type) 
    {
        $result = 
        [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];
        if (!is_array($ids)) {
            $ids = (int)$ids;
            if ($ids < 1) {
                return false;
            }
            $ids = [$ids];
        }

        switch ($type) {
            case 1: //上架
                Goods::whereIn('id', $ids)->where('seller_id',$sellerId)->update(['status'=> 1]);
                break;
            case 2: //下架
                Goods::whereIn('id', $ids)->where('seller_id',$sellerId)->update(['status'=>0]);
                break;
            case 3: //删除
                DB::beginTransaction();
                try{
                    Goods::whereIn('id', $ids)->where('seller_id',$sellerId)->delete();

                    //删除服务属性
                   // \YiZan\Models\GoodsAttr::whereIn('goods_id', $ids)->where('seller_id',$sellerId)->delete();
                    //删除服务举报
                  //  \YiZan\Models\GoodsComplain::whereIn('goods_id', $ids)->where('seller_id',$sellerId)->delete();
                    //删除服务扩展
                    \YiZan\Models\GoodsExtend::whereIn('goods_id', $ids)->where('seller_id',$sellerId)->delete();
                    //删除商品规格
                     \YiZan\Models\GoodsNorms::whereIn('goods_id', $ids)->where('seller_id',$sellerId)->delete();
                   // \YiZan\Models\GoodsModel::whereIn('goods_id', $ids)->where('seller_id',$sellerId)->delete();
                    //删除服务标签
                    // \YiZan\Models\GoodsTagRelated::whereIn('goods_id', $ids)
                    //                             ->where('seller_id',$sellerId)
                    //                             ->where('type', 0)
                    //                             ->delete();
                    //删除提供服务的员工
                    \YiZan\Models\GoodsStaff::whereIn('goods_id', $ids)->where('seller_id',$sellerId)->delete();
                    //删除收藏的服务商品
                    \YiZan\Models\UserCollect::whereIn('goods_id', $ids)->where('seller_id',$sellerId)->delete();
                    DB::commit();
                } catch (Exception $e) {
                    DB::rollback();
                    $result['code'] = 99999;
                }
                break;
        }
        
        return $result;
    }


    /**
     * [activityAllLists 查询未选商品活动列表]
     * @param  [type] $sellerId [description]
     * @param  [type] $notIds   [description]
     * @param  [type] $page     [description]
     * @param  [type] $pageSize [description]
     * @return [type]           [description]
     */
    public static function activityAllLists($sellerId, $notIds, $page, $pageSize) {
        $list = Goods::where('seller_id', $sellerId)
                     ->whereNotIn('id', $notIds)
                     ->skip(($page - 1) * $pageSize)
                     ->take($pageSize)
                     ->get()
                     ->toArray();

        return $list;
    }
    /**
     * [activityLists 查询已选商品活动列表]
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
    
}
