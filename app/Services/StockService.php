<?php
namespace YiZan\Services;

use YiZan\Models\Stock;

use DB;
class StockService extends BaseService {


    public function getLists($status = '',$page,$pageSize)
    {
        $stockFirst = Stock::orderBy('id', 'desc');
        if($status != ""){
            $stockFirst->where('status',$status);
        }
        $dbPrefix = DB::getTablePrefix();
        $stockFirst->select(DB::raw('*,(select count(*) from ' . $dbPrefix . 'goods_sku_item where ' .  $dbPrefix . 'stock.id = ' .  $dbPrefix . 'goods_sku_item.group_id) as count'));
        $totalCount = $stockFirst->count();
        $list = $stockFirst->skip(($page - 1) * $pageSize)->take($pageSize)->get();
        $data = [];
        foreach($list as $key => $v){
            $data[$key] = $v;
            $data[$key]['checkedDisabled'] = $v->count ? 1 : 0;

        }
        return ["list"=>$data, "totalCount"=>$totalCount];
    }

    public function detail($id)
    {
        if($id > 0){
            $stockFirst = Stock::where('id',$id)->first();
            return $stockFirst;
        }
        return;
    }

    public function save($id,$name,$stock,$status = 1)
    {
        $result = [
            'code' => 0,
            'data' => null,
            'msg' =>''
        ];
        $name = trim($name);
        if($name == ""){
            $result['code']  = 11009;
            return $result;
        }
        $isStock = false;
        $stockData = [];
        foreach($stock as $s){
            if(trim($s) != ""){
                $stockData[] = $s;
                $isStock = true;
            }
        }
        if($isStock == false){
            $result['code']  = 11010;
            return $result;
        }
        if($id > 0){
            $stockFirst = Stock::where('id',$id)->select(DB::raw('*,(select count(*) from ' . env('DB_PREFIX') . 'goods_sku_item where ' . env('DB_PREFIX') . 'stock.id = ' . env('DB_PREFIX') . 'goods_sku_item.group_id) as count'))->first();
            if($stockFirst->count != 0){
                $result['code']  = 11011;
                return $result;
            }
        }else{
            $stockFirst = new Stock();
            $stockFirst->create_time = UTC_TIME;
        }
        $stockFirst->name = $name;
        $stockFirst->stock = serialize($stockData);
        $stockFirst->status = $status;

        DB::beginTransaction();
        try {
            $stockFirst->save();
            DB::commit();
            $stockFirst->stock = $stockData;
            $result['data'] = $stockFirst;
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 11011;
        }
        return $result;
    }

    public static function remove($ids) {
        if (!is_array($ids)) {
            $ids = (int)$ids;
            if ($ids < 1) {
                 return false;
            }
            $ids = [$ids];
        }
        DB::beginTransaction();
        try {
            Stock::whereIn('id', $ids)->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return false;
        }
        return true;
    }
}
