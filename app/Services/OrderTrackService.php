<?php
namespace YiZan\Services;

use YiZan\Models\OrderTrack;
use YiZan\Models\Seller;
use YiZan\Models\Region;
use YiZan\Models\SystemConfig;

use YiZan\Utils\Http;
use YiZan\Utils\Time;
use YiZan\Utils\String;
use DB, Exception, Validator, Lang,Config;

/**
 * 快递
 */
class OrderTrackService extends BaseService {
    /**
     * 发送订阅
     * @param $expressNumber
     * @param $from
     * @param $to
     * @param $key
     * @return string
     */
    public function get($expressCode, $expressNumber, $from, $to, $key, $orderId, $userId, $sellerId,$company,$type,$remark) {

        if($type == 1 || $type == 2){
            $count = OrderTrack::where('order_id', $orderId)->count();
            if ($count > 0) {
                $ordertrack = [
                    'seller_id' => $sellerId,
                    'user_id' => $userId,
                    'express_company' => $company,
                    'express_code' => 0,
                    'express_number' => $expressNumber,
                    'state' => 0,
                    'ischeck' => 0,
                    'data' => '',
                    'type' => $type,
                    'remark' => $remark
                ];
                OrderTrack::where('order_id', $orderId)->update($ordertrack);
            } else {
                $ordertrack = [
                    'order_id' => $orderId,
                    'seller_id' => $sellerId,
                    'user_id' => $userId,
                    'express_company' => $company,
                    'express_code' => 0,
                    'express_number' => $expressNumber,
                    'state' => 0,
                    'ischeck' => 0,
                    'data' => '',
                    'type' => $type,
                    'remark' => $remark
                ];
                OrderTrack::insert($ordertrack);
            }
            $result['message'] = 'success';
            return $result;
        }

        $data['company'] = $expressCode;
        $data['number'] = $expressNumber;
        $data['from'] = $from;
        $data['to'] = $to;
        $data['key'] = SystemConfig::where('code','order_track_key')->pluck('val');
        $data['parameters']['callbackurl'] = Config::get('app.callback_url').'Order/track';
        $data['parameters']['salt'] = '';

        $args = json_encode($data);
        $url = 'http://highapi.kuaidi.com/openapi-receive.html';

        $result = Http::post($url, $args);

        $return = json_decode($result,true);

        if($return['message'] == 'success') {
            $count = OrderTrack::where('order_id', $orderId)->count();
            if ($count > 0) {
                $ordertrack = [
                    'seller_id' => $sellerId,
                    'user_id' => $userId,
                    'express_company' => $company,
                    'express_code' => $expressCode,
                    'express_number' => $expressNumber,
                    'state' => 0,
                    'ischeck' => 0,
                    'data' => ''
                ];
                OrderTrack::where('order_id', $orderId)->update($ordertrack);
            } else {
                $ordertrack = [
                    'order_id' => $orderId,
                    'seller_id' => $sellerId,
                    'user_id' => $userId,
                    'express_company' => $company,
                    'express_code' => $expressCode,
                    'express_number' => $expressNumber,
                    'state' => 0,
                    'ischeck' => 0
                ];
                OrderTrack::insert($ordertrack);
            }
        }

        return $result;
    }

    /**
     * 获取快递状态
     */
    public function getOrder($sellerId,$userId,$orderId){
        if(!empty($sellerId)){
            $ordertrack = OrderTrack::where('seller_id',$sellerId)->where('order_id',$orderId)->with('seller')->first();
        }

        if(!empty($userId)){
            $ordertrack = OrderTrack::where('user_id',$userId)->where('order_id',$orderId)->with('seller')->first();
        }

        if(!empty($ordertrack)){
            $ordertrack = $ordertrack->toArray();
            $ordertrack['data'] = json_decode($ordertrack['data'],true);
        }else{
            return '';
        }

        return $ordertrack;
    }
    /**
     * [addressStr 获取from to字符串]
     * @param  [type] $provinceId [description]
     * @param  [type] $cityId     [description]
     * @param  [type] $areaId     [description]
     * @return [type]             [description]
     */
    public static function addressStr($provinceId, $cityId, $areaId) {
        $str = '';
        if($provinceId > 0)
        {
            $str .= Region::where('id', $provinceId)->pluck('name');
        }
        if($cityId > 0)
        {
            $str .= Region::where('id', $cityId)->pluck('name');
        }
        if($areaId > 0)
        {
            $str .= Region::where('id', $areaId)->pluck('name');
        }

        return $str;
    }
}
