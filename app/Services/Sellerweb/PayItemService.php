<?php namespace YiZan\Services\Sellerweb;

use YiZan\Models\PayItem; 
use DB, Exception,Validator, Lang;


class PayItemService extends \YiZan\Services\PayItemService 
{
	/**
	 * 收费项目列表
	 * @param $sellerId int 物业编号
	 * @param $name string 名称
	 * @param $page int 页码
	 * @param $pageSize int 页数量
	 * @param $isAll int 是否全部
	 */
	public static function getLists($sellerId, $name, $page, $pageSize, $isAll) {
		$list = PayItem::where('seller_id', $sellerId);
		
		if($name){
			$list->where('name', $name);
		}

		$totalCount = $list->count();


        if($isAll){ 
            return $list->get()->toArray(); 
        } else { 
			$list = $list->take($pageSize)
						 ->skip(($page - 1) * $pageSize)
						 ->get()
						 ->toArray();

	        return ["list"=>$list, "totalCount"=>$totalCount];
	    }
	}	 

	/**
	 * 保存收费项目
	 * @param $sellerId int 物业编号
	 * @param $id int 收费项目编号
	 * @param $name string 名称
	 * @param $price int 单价
	 * @param $chargingItem int 收费项目
	 * @param $chargingUnit int 收费单位
	 */
	public static function save($sellerId, $id, $name, $price, $chargingItem, $chargingUnit) {
		$result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];

        if($id){
        	$payitem = PayItem::where('seller_id', $sellerId)
        					  ->where('id', $id)
        					  ->first();
        } else {
        	$payitem = new PayItem();
        }

        $rules = array(
            'name' => ['required', 'max:10'], 
        );
        
        $messages = array (
            'name.required'       => 10700,   // 
            'name.max'       	  => 10701,   //
        );

        $validator = Validator::make([
                'name' => $name, 
            ], $rules, $messages);
        
        //验证信息
        if ($validator->fails()) {
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        $payitem->seller_id = $sellerId;
        $payitem->name = $name;
        $payitem->price = $price;
        $payitem->charging_item = $chargingItem;
        $payitem->charging_unit = $chargingUnit;
        $payitem->save();
        return $result;
	}	

	/**
	 * 获取详情 
	 * @param $sellerId int 商家编号
	 * @param $id int 编号
	 */
	public static function get($sellerId, $id){
		$payitem = PayItem::where('seller_id', $sellerId)
						  ->where('id', $id)
						  ->first();
		return $payitem;
	}

	/**
	 * 删除 
	 * @param $sellerId int 商家编号
	 * @param $id int 编号
	 */
	public static function delete($sellerId, $id){
		$result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];
        //判断是否有关联数据 10702

		$rs = PayItem::where('seller_id', $sellerId)
						  ->where('id', $id)
						  ->delete();
		if(!$rs){
			$result['code'] = 10703;
		}
		return $result;
	}

}
