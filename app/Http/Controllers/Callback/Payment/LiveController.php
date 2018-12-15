<?php
namespace YiZan\Http\Controllers\Callback\Payment;

use YiZan\Http\Controllers\Callback\BaseController;
use YiZan\Models\UserPayLog;
use YiZan\Models\LiveLog;
use YiZan\Models\User;

use YiZan\Services\LiveService;
use YiZan\Services\PushMessageService;
use YiZan\Services\PaymentService;
use Illuminate\Database\Query\Expression;

use DB,
    Exception,
    Log,
    YiZan\Models\Refund,
    YiZan\Models\UserRefundLog;

/**
 * 生活缴费
 */
class LiveController extends BaseController {
    public function notify(){
        //file_put_contents('/mnt/test/shequ/storage/logs/live.log',print_r($_REQUEST,true));

        $liveLog = LiveLog::where('sn',$_REQUEST['orderid'])->with('user')->first();

        if(!$liveLog){
            die('fail');
        }
        $liveLog = $liveLog->toArray();

        $extend = json_decode(base64_decode($liveLog['extend']),true);
        $data['provid'] = $extend['provinceId'];
        $data['cityid'] = $extend['cityId'];
        $data['type'] = "00".$extend['type'];
        $data['corpid'] = $extend['code'];
        $data['cardid'] = $extend['cardid'];
        $data['account'] = $extend['account'];
        $data['orderid'] = $liveLog['sn'];
        $data['sign'] = md5($data['provid'].$data['cityid'].$data['type'].$data['corpid'].$data['cardid'].$data['account'].$data['orderid']);

        // if($data['sign'] == $_REQUEST['sign']){
        if($_REQUEST['state'] == 1){
            LiveLog::where('sn',$_REQUEST['orderid'])->update(['is_pay'=>'2']);

            //充值失败
            $extend = json_decode(base64_decode($liveLog['extend']),true);
            if(empty($extend)){
                return false;
            }
            $live_type = ['1'=>'水','2'=>'电','1'=>'燃气'];
            $live_state = ['失败','成功'];
            $live_arrs['type'] = $live_type[$extend['type']];
            $live_arrs['account'] = $extend['account'];
            $live_arrs['state'] = $live_state[1];
            PushMessageService::notice($liveLog['user']['id'], $liveLog['user']['mobile'], 'order.live', $live_arrs,['app'],'buyer', 1, 0);

            die("success");
        }else{
            $liveLog = liveLog::where('sn', $_REQUEST['orderid'])->first();
            $userPayLog = UserPayLog::where('sn', $_REQUEST['orderid'])->first()->toArray();
            if($liveLog->money != $userPayLog->money && $liveLog->money >$userPayLog->money){
                //加余额
                $money = $liveLog->money-$userPayLog->money;
                User::where('id',$userPayLog->user_id)->increment('balance', $money);
            }

            $refund = new Refund;
            $refund->user_id        = $userPayLog['userId'];
            $refund->order_id       = 0;
            $refund->seller_id      = 0;
            $refund->content        = '生活缴费';
            $refund->money          = $userPayLog['money'];
            $refund->create_time    = UTC_TIME;
            $refund->create_day     = UTC_DAY;
            $refund->status         = 0;
            $refund->sn             = $userPayLog['sn'];
            $refund->trade_no       = $userPayLog['tradeNo'];
            $refund->payment_type   = $userPayLog['paymentType'];
            $refund->save();

            //修改状态失败
            LiveLog::where('sn', $userPayLog->sn)->update([
                'is_pay' 		            => '-1'
            ]);

            //充值失败
            $extend = json_decode(base64_decode($liveLog['extend']),true);
            if(empty($extend)){
                return false;
            }
            $live_type = ['1'=>'水','2'=>'电','3'=>'燃气'];
            $live_state = ['失败','成功'];
            $live_arrs['type'] = $live_type[$extend['type']];
            $live_arrs['account'] = $extend['account'];
            $live_arrs['state'] = $live_state[0];
            PushMessageService::notice($liveLog['user']['id'], $liveLog['user']['mobile'], 'order.live', $live_arrs,['app'],'buyer', 1, 0);

            die($_REQUEST['err_msg']);
        }

    }

}
