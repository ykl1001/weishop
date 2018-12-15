<?php 
namespace YiZan\Services\Staff;

use YiZan\Models\Repair;
use YiZan\Models\SellerStaff;
use YiZan\Services\PushMessageService;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Utils\String;
use Exception, DB, Lang, Validator, App;

class  RepairService extends \YiZan\Services\RepairService
{

    /**
     * 订单列表
     * @param int $sellerId 商家编号
     * @param int $staffId 员工编号
     * @param int $status 订单状态 1:新订单 2:进行中 3:已完成 4:已取消
     * @param string $date 日期(格式 20151028)
     * @param string $keywords 搜索关键字
     * @param int $page 页码
     */
    public static function getList($sellerId, $staffId, $status, $date, $keywords, $page,$new) {
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> ''
        );
        DB::connection()->enableQueryLog();

        $list = Repair::orderBy('repair.create_time', 'DESC')
            ->where('seller_staff_id', $staffId);

        if($new > 0){
            $list = $list->whereIn('repair.status',[0,1]);
        }else if($status > 0){
            $list = $list->where('repair.status',$status);
        }

        //日期搜索
        if ($date != '') {
            $beginTime = Time::toTime($date);
            $endTime = $beginTime + 24 * 3600 - 1;
            $list->whereBetween('repair.create_time', [$beginTime, $endTime]);
        }

        if(!empty($keywords)){
            $list->join('property_user','repair.puser_id','=','property_user.id');
            $list->where(function($query) use ($keywords){
                $query->orWhere('name', 'like', '%'.$keywords.'%')
                    ->orWhere('mobile', 'like', '%'.$keywords.'%');
            });
        }

        $list->addSelect('repair.*');

        $ingObj = clone $list;
        $result['data']['ingCount'] = $ingObj->where('repair.status',1)->count();

        $countObj = clone $list;
        $result['data']['count'] = $countObj->count();

        $list = $list->skip(($page - 1) * 10)
            ->take(10)
            ->with('build', 'room', 'puser', 'types', 'staff')
            ->get()
            ->toArray();

        foreach ($list as $key => $value) {
            $list[$key]['images'] = $value['images'] ? explode(',', $value['images']) : null;
            $list[$key]['repairType'] = $value['types']['name'];
            $list[$key]['createTime'] = yztime($value['createTime']);
            $list[$key]['apiTime'] = yztime($value['apiTime']);
        }
        $result['data']['list'] = $list;

        return $result;
    }

    /**
     * 获取订单详情
     * @param int $sellerId 商家编号
     * @param int $staffId 服务人员编号
     * @param int $orderId 订单编号
     * @return array
     */
    public static function getRepairById($sellerId, $staffId, $orderId)
    {
        if ($sellerId > 0) {
            $data = Repair::where('id', $orderId)->where('seller_id', $sellerId)
                ->with('build', 'room', 'puser', 'types', 'staff','rate')
                ->first();
        } elseif($staffId > 0) {
            $data = Repair::where('id', $orderId)
                ->with('build', 'room', 'puser', 'types', 'staff','rate')
                ->first();
        }

        if ($data) {
            $data = $data->toArray();
            $data['images'] = $data['images'] ? explode(',', $data['images']) : null;
            $data['repairType'] = $data['types']['name'];
            $data['createTime'] = yztime($data['createTime']);
            $data['apiTime'] = yztime($data['apiTime']);
        }
        return $data;
    }


    public static function updateRepair($sellerId, $staffId, $id, $status, $remark,$express = ''){
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.update_info')
        ];

        if ($sellerId == 0 && $staffId > 0) {
            $order = Repair::where('id', $id)->where('seller_staff_id', $staffId)->first();
        } else {
            $order = Repair::where('id', $id)->where('seller_id', $sellerId)->first();
        }

        //没有订单
        if ($order == false)
        {
            $result['code'] = 20001; // 没有找到相关订单
            return $result;
        }
        //订单状态不对
        if ($order->status != 1) {
            $result['code'] = 20002;
            return $result;
        }

        $data = [
            'status' => $status,
            'finish_time' => UTC_TIME
        ];

        Repair::where('id', $id)->update($data);

        //报修消息
        PushMessageService::notice($order->user_id, '', 'property.over', ['id'=>$order->id,'districtId'=>$order->district_id], ['app'],'buyer', 7, 0);
        return $result;
    }
    
    /**
     * 服务人员完成订单
     * @param  [type] $userId  [description]
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     */
    public static function completeOrder($staffId, $orderId,$reservationCode) {

    }

}