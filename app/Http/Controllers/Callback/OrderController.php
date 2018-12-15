<?php
namespace YiZan\Http\Controllers\Callback;

use YiZan\Models\Order;
use YiZan\Models\OrderTrack;
use Request,Log;

class OrderController extends BaseController {
    public function track() {
        $param = Request::get('param');
        if (empty($param)) {
            die('param empty');
        }

        $param = json_decode($param,true);
        $lastResult = $param['lastResult'];

        $orderTrack = OrderTrack::where('express_code', $lastResult['com'])->where('express_number', $lastResult['nu'])->first();
        if($orderTrack){
            $orderTrack->state = $lastResult['state'];
            $orderTrack->ischeck = $lastResult['ischeck'];
            $orderTrack->data =  json_encode($lastResult['data']);
            $orderTrack->save();
        }

        die('{"result":"true", "returnCode":"200", "message":"成功"}');
    }
}
