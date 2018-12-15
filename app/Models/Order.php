<?php namespace YiZan\Models;

use YiZan\Utils\Time;
use Lang;
class Order extends Base {

    protected $appends = array(
        'orderStatusStr',
        'orderNewStatusStr',
        "isCanDelete",
        "isCanRate",
        "isCanCancel",
        "isCanPay",
        "isCanContact",
        "isCanConfirm",
        "statusFlowImage",
        "Schedule",
        "Designate",
        "isReceivability",
        "isCanRefund",
        "isCanReminder",
        "isCashOnDelivery",
        'isContactCancel',
        'isCanStartService',
        'orderStatus',
        'PaymentType',
        'isNewCanRefund',
        'isCanLogistics',
        'isCancel'
    );

    protected $casts = [
        'isRate' => 'boolean',
    ];
    public function userPayLog(){
        return $this->belongsTo('YiZan\Models\UserPayLog','id','order_id');
    }

    public function user(){
        return $this->belongsTo('YiZan\Models\User');
    }

    public function users(){
        return $this->belongsTo('YiZan\Models\User', 'user_id','id');
    }
    public function staff(){
        return $this->belongsTo('YiZan\Models\SellerStaff','seller_staff_id','id');
    }
    public function seller(){
        return $this->belongsTo('YiZan\Models\Seller','seller_id','id');
    }
    public function promotion(){
        return $this->belongsTo('YiZan\Models\OrderPromotion', 'id', 'order_id');
    }

    public function userRefund(){
        return $this->hasMany('YiZan\Models\Refund', 'order_id', 'id');
    }

    public function orderGoods(){
        return $this->hasMany('YiZan\Models\OrderGoods', 'order_id', 'id');
    }

    public function goods(){
        return $this->hasMany('YiZan\Models\OrderGoods', 'order_id', 'id');
    }
    public function cartSellers(){
        return $this->hasMany('YiZan\Models\OrderGoods', 'order_id', 'id');
    }

    public function refundCount(){
        return $this->hasManyCount('YiZan\Models\Refund', 'order_id', 'id');
    }

    public function payment(){
        return $this->belongsTo('YiZan\Models\Payment','pay_type', 'code');
    }

    public function orderTrack(){
        return $this->belongsTo('YiZan\Models\OrderTrack','id','order_id');
    }

    /**
     * 是否可以催单
     * @return bool
     */
    public function getPaymentTypeAttribute(){
        return $this->attributes['pay_type'];
    }
    /**
     * 是否可以催单
     * @return bool
     */
    public function getIsCanReminderAttribute()
    {
        $status 	= $this->attributes['status'];

        return $status == ORDER_STATUS_PAY_SUCCESS ||
        $status == ORDER_STATUS_AFFIRM_SELLER ||
        $status == ORDER_STATUS_START_SERVICE ||
        $status == ORDER_STATUS_PAY_DELIVERY;
    }


    /**
     * 是否可以催单
     * @return bool
     */
    public function getIsRefundAttribute()
    {
        $status 	= $this->attributes['status'];
        if( $this->attributes['is_all'] == 1){
            return
                $status == ORDER_STATUS_REFUND_AUDITING   ||
                $status == ORDER_STATUS_CANCEL_REFUNDING  ||
                $status == ORDER_STATUS_REFUND_HANDLE     ||
                $status == ORDER_STATUS_REFUND_FAIL       ||
                $status == ORDER_STATUS_REFUND_SUCCESS    ||
                $status == ORDER_REFUND_SELLER_AGREE      ||
                $status == ORDER_REFUND_SELLER_REFUSE     ||
                $status == ORDER_REFUND_ADMIN_AGREE       ||
                $status == ORDER_REFUND_SELLER_REFUSE_LOGISTICS       ||
                $status == ORDER_REFUND_USER_REFUSE_LOGISTICS       ||
                $status == ORDER_REFUND_ADMIN_REFUSE;
        }
        return false;
    }

    /**
     * 是否可以呼叫配送人员
     * @return bool
     */
    public function getIsCanCallAttribute()
    {
        $status 	= $this->attributes['status'];
        $order_type 	= $this->attributes['order_type'];
        $pay_type = $this->attributes['pay_type']; //不能等于货到付款
        $is_cancel_call 	= $this->attributes['is_cancel_call'];
        if($status == ORDER_STATUS_AFFIRM_SELLER && $order_type == 1 && $pay_type != 'cashOnDelivery'){
            return true;
        }
        return false;
    }

    /**
     * 是否可以取消呼叫配送人员
     * @return bool
     */
    public function getIsCanCancelCallAttribute()
    {
        $status 	= $this->attributes['status'];
        $order_type 	= $this->attributes['order_type'];
        $is_cancel_call 	= $this->attributes['is_cancel_call'];
        $pay_type = $this->attributes['pay_type']; //不能等于货到付款
        if($status == ORDER_STATUS_CALL_SYSTEM_SEND && $order_type == 1 && $is_cancel_call == 0 && $pay_type != 'cashOnDelivery'){
            return true;
        }
        return false;
    }


    /**
     * 报表订单状态
     * @return bool
     */
    public function getOrderStatusAttribute()
    {
        $status              = $this->attributes['status'];
        $buyerFinishTime     = (int)$this->attributes['buyer_finish_time'];
        $cancelTime          = (int)$this->attributes['cancle_time'];

        if(in_array($status, [ORDER_STATUS_CANCEL_USER_SELLER])){
            $orderStatus = '申请取消订单';
        }else if(in_array($status, [ORDER_STATUS_FINISH_SYSTEM, ORDER_STATUS_FINISH_USER, ORDER_STATUS_USER_DELETE, ORDER_STATUS_SELLER_DELETE, ORDER_STATUS_ADMIN_DELETE])){
            if($buyerFinishTime == 0 && $cancelTime >= 0){
                $orderStatus = '已取消';
            } else {
                $orderStatus = '已完成';
            }
        } else if(in_array($status, [ORDER_STATUS_PAY_SUCCESS,ORDER_STATUS_START_SERVICE,ORDER_STATUS_PAY_DELIVERY,ORDER_STATUS_AFFIRM_SELLER,ORDER_STATUS_FINISH_STAFF])){
            $orderStatus = '未完成';
        } else {
            $orderStatus = '已取消';
        }

        return $orderStatus;
    }

    /**
     * 是否可以接单(服务站)
     * @return bool
     */
    public function getIsReceivabilityAttribute()
    {
        $status 	= $this->attributes['status'];

        return $status == ORDER_STATUS_PAY_SUCCESS;
    }
    public function getPayTypeAttribute() {
        $type = $this->attributes['pay_type'];
        if (!empty($type)){
            $types =   Lang::get('admin.pay_type.'.$type);
        }else{
            $types =  "未支付";
        }
        return  $types;
    }

//     public function getorderTypeAttribute() {
//         $type =   $this->attributes['order_type'] == 1 ? "商品类" : "服务类" ;
//         return  $type;
//     }

    public function getcreateTimeAttribute() {
        return Time::toDate($this->attributes['create_time'],'Y-m-d H:i:s');
    }

    public function getrefundTimeAttribute() {
        return Time::toDate($this->attributes['refund_time'],'Y-m-d H:i:s');
    }

    public function getappTimeAttribute() {
        return Time::toDate($this->attributes['app_time'],'Y-m-d H:i:s');
    }

    public function getfreTimeAttribute() {
        return Time::toDate($this->attributes['fre_time'],'Y-m-d H:i:s');
    }

    public function getBuyerFinishTimeAttribute() {
        return Time::toDate($this->attributes['buyer_finish_time'],'Y-m-d H:i:s');
    }

    /**
     * 是否可以开始订单
     * @return bool
     */
    public function getIsCanStartServiceAttribute()
    {
        $status     = $this->attributes['status'];

        if($status == ORDER_STATUS_AFFIRM_SELLER || $status == ORDER_STATUS_GET_SYSTEM_SEND){
            return true;
        }else{
            return '';
        }
    }

    /**
     * 是否可以完成服务(卖家员工)
     * @return bool
     */
    public function getIsCanFinishAttribute()
    {
        $status     = $this->attributes['status'];

        return $status == ORDER_STATUS_START_SERVICE;
    }


    /**
     * 是否可以删除(卖家)
     * @return bool
     */
    public function getIsCanDeleteAttribute()
    {
        return false;
        $status 	= $this->attributes['status'];
        $isRate	    = $this->attributes['is_rate'];
        return ($status == ORDER_STATUS_CANCEL_USER   ||
            $status == ORDER_STATUS_CANCEL_AUTO   ||
            $status == ORDER_STATUS_REFUND_FAIL   ||
            $status == ORDER_STATUS_CANCEL_SELLER ||
            $status == ORDER_STATUS_CANCEL_ADMIN) ||
        (($status == ORDER_STATUS_FINISH_USER   ||
                $status == ORDER_STATUS_FINISH_SYSTEM) &&
            $isRate == true);
    }
    /**
     * 是否可以评价(买家)
     * @return bool
     */
    public function getIsCanRateAttribute()
    {
        $status 	= $this->attributes['status'];
        $isRate	    = $this->attributes['is_rate'];

        return ($status == ORDER_STATUS_FINISH_SYSTEM || $status == ORDER_STATUS_FINISH_USER) &&  $isRate == false;
    }
    /**
     * 是否可以举报(买家)
     * @return bool
     */
    public function getIsCanComplainAttribute()
    {
        $status 	= $this->attributes['status'];

        return
            $status == ORDER_STATUS_AFFIRM_ASSIGN_SERVICE ||
            $status == ORDER_STATUS_ASSIGN_SERVICE ||
            $status == ORDER_USER_CANCEL_SERVICE ||
            $status == ORDER_RESTAURANT_REFUSE_SERVICE ||
            $status == ORDER_SELLER_REFUSE_SERVICE ||
            $status == ORDER_ADMIN_REFUSE_SERVICE;
    }
    /**
     * 是否可以取消(买家)
     * @return bool
     */
    public function getIsCanCancelAttribute()
    {
        $status 	= $this->attributes['status'];
        //全国店订单 or 周边店订单
        if( $this->attributes['is_all'] != 1)
        {
            return in_array($status,
                [
                    ORDER_STATUS_BEGIN_USER,
                    ORDER_STATUS_PAY_DELIVERY,
                    ORDER_STATUS_PAY_SUCCESS,
                    ORDER_STATUS_AFFIRM_SELLER,
                    ORDER_STATUS_START_SERVICE,
                    ORDER_STATUS_SYSTEM_SEND,
                    ORDER_STATUS_CALL_SYSTEM_SEND,
                    ORDER_STATUS_GET_SYSTEM_SEND
                ]
            );
//                $status == ORDER_STATUS_BEGIN_USER ||
//                $status ==  ORDER_STATUS_PAY_DELIVERY ||
//                $status == ORDER_STATUS_PAY_SUCCESS ||
//                $status == ORDER_STATUS_AFFIRM_SELLER ||
//                $status == ORDER_STATUS_START_SERVICE;
        }else{
            return in_array($status,[ORDER_STATUS_BEGIN_USER,ORDER_STATUS_PAY_SUCCESS]);
        }
    }

    /**
     * 是否可以付款(买家)
     * @return bool
     */
    public function getIsCanPayAttribute()
    {
        $status  = $this->attributes['status'];
        return $status == ORDER_STATUS_BEGIN_USER;
    }
    /**
     * 是否可以联系(买家)
     * @return bool
     */
    public function getIsCanContactAttribute()
    {
        $status 	= $this->attributes['status'];

        return  $status == ORDER_STATUS_BEGIN_USER ||
        $status == ORDER_STATUS_PAY_SUCCESS ||
        $status == ORDER_STATUS_START_SERVICE ||
        $status == ORDER_STATUS_PAY_DELIVERY ||
        $status == ORDER_STATUS_AFFIRM_SELLER;

    }
    /**
     * 是否可以确认订单完成(买家)
     * @return bool
     */
    public function getIsCanConfirmAttribute()
    {
        $status 	= $this->attributes['status'];

        //全国店订单 or 周边店订单
        if( $this->attributes['is_all'] == 1)
        {
            return $status == ORDER_REFUND_ADMIN_REFUSE  ||
            $status == ORDER_REFUND_SELLER_REFUSE||
            $status == ORDER_STATUS_AFFIRM_SELLER;
        }
        else
        {
            return $status == ORDER_STATUS_FINISH_STAFF ||
            $status == ORDER_STATUS_START_SERVICE;
        }
    }

    /**
     * 是否可以确认订单完成(员工)
     * @return bool
     */
    public function getIsCanFinishServiceAttribute()
    {
        $status 	= $this->attributes['status'];
        return $status == ORDER_STATUS_START_SERVICE ;
    }


    /**
     * 订单状态
     * @return string
     */
    public function getOrderStatusStrAttribute()
    {
        $isAll = $this->attributes['is_all'];
        $statusStr =
            [
                ORDER_STATUS_BEGIN_USER      => $isAll == 1 ? '等待买家付款' : '下单成功',
                ORDER_STATUS_PAY_SUCCESS     => $isAll == 1 ? '等待卖家发货' :'已付款',
                ORDER_STATUS_PAY_DELIVERY    => '货到付款',
                ORDER_STATUS_AFFIRM_SELLER   => $isAll == 1 ? '卖家已发货' : '已接单',
                ORDER_STATUS_FINISH_STAFF    => '服务人员完成',
                ORDER_STATUS_FINISH_SYSTEM   => '交易完成',
                ORDER_STATUS_FINISH_USER     => '交易完成',
                ORDER_STATUS_CANCEL_USER     => '交易关闭',
                ORDER_STATUS_CANCEL_AUTO     => '支付超时',
                ORDER_STATUS_CANCEL_SELLER   => '交易关闭',
                ORDER_STATUS_CANCEL_ADMIN    => '交易关闭',
                ORDER_STATUS_USER_DELETE     => '会员删除订单',
                ORDER_STATUS_SELLER_DELETE   => '商家删除订单',
                ORDER_STATUS_ADMIN_DELETE    => '总后台删除订单',
                ORDER_STATUS_AUTH_CODE       => '消费码验证成功',

                ORDER_STATUS_REFUND_AUDITING     => '申请退款',
                ORDER_STATUS_CANCEL_REFUNDING    => '取消且退款中',
                ORDER_STATUS_REFUND_HANDLE       => '退款处理中',
                ORDER_STATUS_REFUND_FAIL         => '退款失败',
                ORDER_STATUS_REFUND_SUCCESS      => '退款成功',
                ORDER_REFUND_SELLER_AGREE        => '商家同意退款',
                ORDER_REFUND_SELLER_REFUSE       => '待收货',
                ORDER_REFUND_ADMIN_AGREE         => '总后台同意退款',
                ORDER_REFUND_ADMIN_REFUSE        => '待收货',
                ORDER_REFUND_USER_REFUSE_LOGISTICS => '退款处理中',
                ORDER_REFUND_SELLER_REFUSE_LOGISTICS=> '退款处理中',
                ORDER_STATUS_CANCEL_USER_SELLER     => '待商家确认取消订单'
            ];
        $orderType = $this->attributes['order_type'];
        if ($orderType == 1) {
            $statusStr[ORDER_STATUS_FINISH_STAFF] = $isAll == 1 ?  '确认收货' : "配送完成";
            $statusStr[ORDER_STATUS_START_SERVICE] = $isAll == 1 ?  '已发货' : "配送中";
            $statusStr[ORDER_STATUS_SYSTEM_SEND] = "取货中";
            $statusStr[ORDER_STATUS_CALL_SYSTEM_SEND] = "平台处理中";
            $statusStr[ORDER_STATUS_GET_SYSTEM_SEND] = "取货中";
        } else {
            $statusStr[ORDER_STATUS_FINISH_STAFF] = '服务完成';
            $statusStr[ORDER_STATUS_START_SERVICE] = '服务中';
        }
        $status     = $this->attributes['status'];
        $isRate     = $this->attributes['is_rate'];

        if(($status == ORDER_USER_CONFIRM_SERVICE && $isRate == false) ||
            ($status == ORDER_STATUS_SYSTEM_CONFIRM && $isRate == false))
        {
            return "待评价";
        }

        return array_key_exists($status, $statusStr) ? $statusStr[$status] : "";
    }

    /**
     * 新订单状态+提示
     */
    public function getOrderNewStatusStrAttribute()
    {
        $cancelRemark = $this->attributes['cancel_remark'] ? $this->attributes['cancel_remark'] :  '无';

        $is_all = $this->attributes['is_all'] == 1 ? "发货" :  '接单';
        $statusStr =
            [
                ORDER_STATUS_BEGIN_USER          => ['title'=>'等待支付', 'tag'=>'请在提交订单后<TIME>分钟内完成支付', 'time'=>$this->attributes['create_time']],
                ORDER_STATUS_PAY_SUCCESS         => ['title'=>'已支付，等待'.$is_all, 'tag'=>'请耐心等待商家确认', 'time'=>$this->attributes['create_time']],
                ORDER_STATUS_PAY_DELIVERY        => ['title'=>'货到付款，等待'.$is_all, 'tag'=>'请耐心等待商家确认', 'time'=>$this->attributes['create_time']],
                ORDER_STATUS_AFFIRM_SELLER       => ['title'=>'商家已'.$is_all, 'tag'=>'请等待商家服务', 'time'=>$this->attributes['seller_confirm_time']],
                ORDER_STATUS_FINISH_STAFF        => ['title'=>'服务完成', 'tag'=>'服务已完成，请确认完成订单', 'time'=>$this->attributes['staff_finish_time']],
                ORDER_STATUS_AUTH_CODE           => ['title'=>'消费码验证成功', 'tag'=>'您的消费码已验证，等待商家确认完成', 'time'=>$this->attributes['auth_code_use_time']],
                ORDER_STATUS_FINISH_SYSTEM       => ['title'=>'订单已完成', 'tag'=>'任何意见和吐槽，都欢迎联系我们', 'time'=>$this->attributes['buyer_finish_time']],
                ORDER_STATUS_FINISH_USER         => ['title'=>'订单已完成', 'tag'=>'任何意见和吐槽，都欢迎联系我们', 'time'=>$this->attributes['buyer_finish_time']],
                ORDER_STATUS_CANCEL_USER         => ['title'=>'交易关闭', 'tag'=>'您已取消订单，原因：'.$cancelRemark, 'time'=>$this->attributes['cancel_time']],
                ORDER_STATUS_CANCEL_AUTO         => ['title'=>'支付超时', 'tag'=>'支付超时，请重新下单', 'time'=>$this->attributes['cancel_time']],
                ORDER_STATUS_CANCEL_SELLER       => ['title'=>'交易关闭', 'tag'=>'商家已取消订单，原因：'.$cancelRemark, 'time'=>$this->attributes['cancel_time']],
                ORDER_STATUS_CANCEL_ADMIN        => ['title'=>'交易关闭', 'tag'=>'总后台已取消订单，原因：'.$cancelRemark, 'time'=>$this->attributes['cancel_time']],
                ORDER_STATUS_USER_DELETE         => ['title'=>'会员删除订单', 'tag'=>'订单已删除', 'time'=>''],
                ORDER_STATUS_SELLER_DELETE       => ['title'=>'商家删除订单', 'tag'=>'订单已删除', 'time'=>''],
                ORDER_STATUS_ADMIN_DELETE        => ['title'=>'总后台删除订单', 'tag'=>'订单已删除', 'time'=>''],

                ORDER_STATUS_REFUND_AUDITING     => ['title'=>'申请退款', 'tag'=>'', 'time'=>''],
                ORDER_STATUS_CANCEL_REFUNDING    => ['title'=>'取消且退款中', 'tag'=>'', 'time'=>''],
                ORDER_STATUS_REFUND_HANDLE       => ['title'=>'退款处理中', 'tag'=>'', 'time'=>''],
                ORDER_STATUS_REFUND_FAIL         => ['title'=>'退款失败', 'tag'=>'', 'time'=>''],
                ORDER_STATUS_REFUND_SUCCESS      => ['title'=>'退款成功', 'tag'=>'', 'time'=>''],
                ORDER_REFUND_SELLER_AGREE        => ['title'=>'商家同意退款', 'tag'=>'', 'time'=>''],
                ORDER_REFUND_SELLER_REFUSE       => ['title'=>'商家拒绝退款', 'tag'=>'', 'time'=>''],
                ORDER_REFUND_ADMIN_AGREE         => ['title'=>'总后台同意退款', 'tag'=>'', 'time'=>''],
                ORDER_REFUND_ADMIN_REFUSE        => ['title'=>'总后台拒绝退款', 'tag'=>'', 'time'=>''],
                ORDER_REFUND_SELLER_REFUSE_LOGISTICS   =>  ['title'=>'商家确认收货', 'tag'=>'待平台处理', 'time'=>''],
                ORDER_REFUND_USER_REFUSE_LOGISTICS =>   ['title'=>'商家确认收货', 'tag'=>'待商家收货', 'time'=>''],
                ORDER_STATUS_CANCEL_USER_SELLER =>   ['title'=>'待商家确认取消订单', 'tag'=>'两天后商家没处理,自动退款', 'time'=>''],

            ];

        $orderType = $this->attributes['order_type'];
        if ($orderType == 1) {
            $statusStr[ORDER_STATUS_FINISH_STAFF]   = ['title'=>'配送完成', 'tag'=>'任何意见和吐槽，都欢迎联系我们', 'time'=>$this->attributes['fre_time']];
            $statusStr[ORDER_STATUS_START_SERVICE]  = ['title'=>'配送中', 'tag'=>'正在配送，请耐心等待', 'time'=>$this->attributes['seller_confirm_time']];
            $statusStr[ORDER_STATUS_SYSTEM_SEND]    = ['title'=>'取货中', 'tag'=>'服务人员正在取货，请耐心等待', 'time'=>''];
            $statusStr[ORDER_STATUS_CALL_SYSTEM_SEND] = ['title'=>'平台处理中', 'tag'=>'平台正在处理该订单', 'time'=>''];
            $statusStr[ORDER_STATUS_GET_SYSTEM_SEND]  = ['title'=>'取货中', 'tag'=>'服务人员正在取货，请耐心等待', 'time'=>''];
        } else {
            $statusStr[ORDER_STATUS_FINISH_STAFF]   = ['title'=>'服务完成', 'tag'=>'任何意见和吐槽，都欢迎联系我们', 'time'=>$this->attributes['fre_time']];
            $statusStr[ORDER_STATUS_START_SERVICE]  = ['title'=>'服务中', 'tag'=>'', 'time'=>$this->attributes['seller_confirm_time']];
        }

        //全国店替换提示
        if( $this->attributes['is_all'] == 1 )
        {
            $statusStr[ORDER_STATUS_BEGIN_USER]    = ['title'=>'等待支付', 'tag'=>'请在<TIME>前完成支付', 'time'=>$this->attributes['create_time']];
            $statusStr[ORDER_STATUS_PAY_SUCCESS]   = ['title'=>'已支付，等待发货', 'tag'=>'', 'time'=>$this->attributes['create_time']];
            $statusStr[ORDER_STATUS_AFFIRM_SELLER] = ['title'=>'卖家已发货', 'tag'=>'', 'time'=>$this->attributes['seller_confirm_time']];
            $statusStr[ORDER_STATUS_FINISH_STAFF]  = ['title'=>'订单完成', 'tag'=>'', 'time'=>$this->attributes['staff_finish_time']];
        }

        $status     = $this->attributes['status'];
        $isRate     = $this->attributes['is_rate'];

        if(($status == ORDER_USER_CONFIRM_SERVICE && $isRate == false) ||
            ($status == ORDER_STATUS_SYSTEM_CONFIRM && $isRate == false))
        {
            return "待评价";
        }

        return array_key_exists($status, $statusStr) ? $statusStr[$status] : "";
    }


    /**
     * 状态流程图片
     * @return string
     */
    public function getStatusFlowImageAttribute()
    {
        $orderType = $this->attributes['order_type'];
        $status = $this->attributes['status'];
        $confirmTime = $this->attributes['seller_confirm_time'];

        if ($orderType == 1) {
            switch($status)
            {
                case ORDER_STATUS_BEGIN_USER:
                case ORDER_STATUS_PAY_SUCCESS:
                case ORDER_STATUS_PAY_DELIVERY:
                    return asset("wap/community/client/images/order_01.png");

                case ORDER_STATUS_AFFIRM_SELLER:
                    return asset("wap/community/client/images/order_02.png");

                case ORDER_STATUS_START_SERVICE:
                case ORDER_STATUS_FINISH_STAFF:
                    return asset("wap/community/client/images/order_03.png");


                case ORDER_STATUS_FINISH_SYSTEM:
                case ORDER_STATUS_FINISH_USER:
                    return asset("wap/community/client/images/order_04.png");

            }
        } else {

            switch($status)
            {
                case ORDER_STATUS_BEGIN_USER:
                case ORDER_STATUS_PAY_SUCCESS:
                case ORDER_STATUS_PAY_DELIVERY:
                    return asset("wap/community/client/images/order_05.png");

                case ORDER_STATUS_AFFIRM_SELLER:
                    return asset("wap/community/client/images/order_06.png");

                case ORDER_STATUS_START_SERVICE:
                case ORDER_STATUS_FINISH_STAFF:
                    return asset("wap/community/client/images/order_07.png");


                case ORDER_STATUS_FINISH_SYSTEM:
                case ORDER_STATUS_FINISH_USER:
                    return asset("wap/community/client/images/order_08.png");

            }
        }

        if ($status == ORDER_STATUS_CANCEL_USER ||
            $status == ORDER_STATUS_CANCEL_USER_SELLER ||
            $status == ORDER_STATUS_CANCEL_SELLER ||
            $status == ORDER_STATUS_CANCEL_ADMIN ||
            $status == ORDER_STATUS_CANCEL_AUTO ||
            $status == ORDER_STATUS_REFUND_AUDITING ||
            $status == ORDER_STATUS_CANCEL_REFUNDING ||
            $status == ORDER_STATUS_REFUND_HANDLE ||
            $status == ORDER_STATUS_REFUND_FAIL ||
            $status == ORDER_STATUS_REFUND_SUCCESS ||
            $status == ORDER_REFUND_SELLER_AGREE ||
            $status == ORDER_REFUND_SELLER_REFUSE ||
            $status == ORDER_REFUND_ADMIN_AGREE ||
            $status == ORDER_REFUND_ADMIN_REFUSE

        ){
            if ($confirmTime > 0) {
                return asset("wap/community/client/images/order_10.png");
            } else {
                return asset("wap/community/client/images/order_09.png");
            }
        }
    }
    /**
     * 状态时间与名称
     * @return array
     */
    public function getStatusNameDateAttribute()
    {
        $status     = $this->attributes['status'];

        $type = $this->attributes['order_type'] == 1 ? 1 : 3;
        $typeArr = [
            1 => "配送中",
            2 => "配送完成",
            3 => "服务中",
            4 => "服务完成"
        ];
        $isAll = $this->attributes['is_all'];
        if($isAll == 1){
            $type = 1;
            $typeArr = [
                1 => "已发货",
                2 => "确认收货"
            ];
        }

        $name = $this->attributes['pay_type'] == 'cashOnDelivery' ? '货到付款' : '付款成功';
        if($status == ORDER_REFUND_SELLER_AGREE || $status == ORDER_REFUND_SELLER_REFUSE){
            $time = $this->attributes["dispose_refund_seller_time"];
        }else{
            $time = $this->attributes["dispose_refund_time"];
        }

        //订单删除状态处理
        if ($status == ORDER_STATUS_USER_DELETE ||
            $status == ORDER_STATUS_SELLER_DELETE ||
            $status == ORDER_STATUS_ADMIN_DELETE
        ) {
            $payStatus = $this->attributes['pay_status'];
            $autoFinishTime = (int)$this->attributes['auto_finish_time'];
            $buyerFinishTime = (int)$this->attributes['buyer_finish_time'];
            $staffFinishTime = (int)$this->attributes['staff_finish_time'];
            $sellerConfirmTime = (int)$this->attributes['seller_confirm_time'];
            $payTime = (int)$this->attributes['pay_time'];
            $cancelTime = (int)$this->attributes['cancel_time'];
            $autoCancelTime = (int)$this->attributes['auto_cancel_time'];
            if ($buyerFinishTime > 0 ||($autoFinishTime > 0 && $autoFinishTime < UTC_TIME)) {
                $status = ORDER_STATUS_FINISH_USER;
            } elseif ($staffFinishTime > 0) {
                $status = ORDER_STATUS_FINISH_STAFF;
            } elseif ($sellerConfirmTime > 0) {
                $status = ORDER_STATUS_AFFIRM_SELLER;
            } elseif ($payTime > 0) {
                $status = ORDER_STATUS_PAY_SUCCESS;
            } elseif ($cancelTime > 0 || $autoCancelTime < UTC_TIME) {
                $status = ORDER_STATUS_CANCEL_USER;
            }
        }


        switch($status)
        {
            case ORDER_STATUS_BEGIN_USER: //提交订单
                return
                    [
                        ["name"=>"提交订单", "date"=>$this->attributes["create_time"]],
                        ["name"=> $name, "date"=>0],
                        ["name"=>$typeArr[$type], "date"=>0],
                        ["name"=>$typeArr[$type+1], "date"=>0],
                        ["name"=>"订单完结", "date"=>0]

                    ];
            case ORDER_STATUS_PAY_SUCCESS: //付款成功
            case ORDER_STATUS_PAY_DELIVERY://货到付款
                return
                    [
                        ["name"=>"提交订单", "date"=>$this->attributes["create_time"]],
                        ["name"=> $name,  "date"=>$this->attributes["pay_time"]],
                        ["name"=>$typeArr[$type], "date"=>0],
                        ["name"=>$typeArr[$type+1], "date"=>0],
                        ["name"=>"订单完结", "date"=>0]
                    ];

            case ORDER_STATUS_AFFIRM_SELLER://已接单
                return
                    [
                        ["name"=>"提交订单", "date"=>$this->attributes["create_time"]],
                        ["name"=>$name,  "date"=>$this->attributes["pay_time"]],
                        ["name"=>$this->attributes["is_all"]==1?"已发货":"已接单", "date"=>$this->attributes["seller_confirm_time"]],
                        ["name"=>$typeArr[$type+1], "date"=>0],
                        ["name"=>"订单完结", "date"=>0]
                    ];
            case ORDER_STATUS_START_SERVICE://服务/配送 中
                return
                    [
                        ["name"=>"提交订单", "date"=>$this->attributes["create_time"]],
                        ["name"=>$name,  "date"=>$this->attributes["pay_time"]],
                        ["name"=>$typeArr[$type], "date"=>$this->attributes["seller_confirm_time"]],
                        ["name"=>$typeArr[$type+1], "date"=>0],
                        ["name"=>"订单完结", "date"=>0]
                    ];
            case ORDER_STATUS_FINISH_STAFF://服务完成
                if(!empty($this->attributes["staff_finish_time"])){
                    return
                        [
                            ["name"=>"提交订单", "date"=>$this->attributes["create_time"]],
                            ["name"=>$name,  "date"=>$this->attributes["pay_time"]],
                            ["name"=>$typeArr[$type], "date"=>$this->attributes["seller_confirm_time"]],
                            ["name"=>$typeArr[$type+1], "date"=>$this->attributes["staff_finish_time"]],
                            ["name"=>"订单完结", "date"=>0]
                        ];
                }else{
                    return
                        [
                            ["name"=>"提交订单", "date"=>$this->attributes["create_time"]],
                            ["name"=>$name,  "date"=>$this->attributes["pay_time"]],
                            ["name"=>$typeArr[$type], "date"=>$this->attributes["seller_confirm_time"]],
                            ["name"=>'用户确认', "date"=>$this->attributes["buyer_finish_time"]],
                            ["name"=>"订单完结", "date"=>0]
                        ];
                }

            case ORDER_STATUS_FINISH_SYSTEM:
            case ORDER_STATUS_FINISH_USER://订单完结
                if(!empty($this->attributes["staff_finish_time"])){
                    return
                        [
                            ["name"=>"提交订单", "date"=>$this->attributes["create_time"]],
                            ["name"=>$name,  "date"=>$this->attributes["pay_time"]],
                            ["name"=>$typeArr[$type], "date"=>$this->attributes["seller_confirm_time"]],
                            ["name"=>$typeArr[$type+1], "date"=>$this->attributes["staff_finish_time"]],
                            ["name"=>"订单完结", "date"=>$this->attributes["buyer_finish_time"] > 0 ? $this->attributes["buyer_finish_time"] : $this->attributes["auto_finish_time"]],
                        ];
                }else{
                    return
                        [
                            ["name"=>"提交订单", "date"=>$this->attributes["create_time"]],
                            ["name"=>$name,  "date"=>$this->attributes["pay_time"]],
                            ["name"=>$typeArr[$type], "date"=>$this->attributes["seller_confirm_time"]],
                            ["name"=>'用户确认', "date"=>$this->attributes["buyer_finish_time"]],
                            ["name"=>"订单完结", "date"=>$this->attributes["buyer_finish_time"] > 0 ? $this->attributes["buyer_finish_time"] : $this->attributes["auto_finish_time"]],
                        ];
                }
            case ORDER_STATUS_REFUND_AUDITING:
                return
                    [
                        ["name"=>"退款申请", "date"=>$this->attributes["refund_time"]],
                        ["name"=>"退款处理中", "date"=>0],
                        ["name"=>"退款完成", "date"=>0],
                    ];
            case ORDER_STATUS_CANCEL_REFUNDING:
                return
                    [
                        ["name"=>"取消且退款", "date"=>$this->attributes["cancel_time"]],
                        ["name"=>"退款处理中", "date"=>$this->attributes["cancel_time"]],
                        ["name"=>"退款完成", "date"=>0],
                    ];
            case ORDER_STATUS_REFUND_HANDLE:
            case ORDER_REFUND_SELLER_AGREE:
            case ORDER_REFUND_ADMIN_AGREE:
                return
                    [
                        ["name"=>"取消且退款", "date"=>$this->attributes["cancel_time"]],
                        ["name"=>"退款处理中", "date"=>$time],
                        ["name"=>"退款完成", "date"=>0],
                    ];
            case ORDER_STATUS_REFUND_REFUSE:
            case ORDER_STATUS_REFUND_FAIL:
            case ORDER_REFUND_SELLER_REFUSE:
            case ORDER_REFUND_ADMIN_REFUSE:
                return
                    [
                        ["name"=>"取消且退款",    "date"=>$this->attributes["cancel_time"]],
                        ["name"=>"核审中",    "date"=>$time],
                        ["name"=>"退款未通过",  "date"=>$time],
                    ];

            case ORDER_STATUS_REFUND_SUCCESS:
                return
                    [
                        ["name"=>"取消且退款", "date"=>$this->attributes["cancel_time"]],
                        ["name"=>"退款处理中", "date"=>$time],
                        ["name"=>"退款完成", "date"=>$time],
                    ];
            case ORDER_STATUS_CANCEL_USER:
                return
                    [
                        ["name"=>"提交订单", "date"=>$this->attributes["create_time"]],
                        ["name"=>"买家订单取消", "date"=>$this->attributes["cancel_time"]],
                    ];
            case ORDER_STATUS_CANCEL_AUTO:
            case ORDER_STATUS_CANCEL_SELLER:
                return
                    [
                        ["name"=>"提交订单", "date"=>$this->attributes["create_time"]],
                        ["name"=>"商家订单取消", "date"=>$this->attributes["cancel_time"]],
                    ];
            case ORDER_STATUS_CANCEL_ADMIN:

                return
                    [
                        ["name"=>"提交订单", "date"=>$this->attributes["create_time"]],
                        ["name"=>"总后台订单取消", "date"=>$this->attributes["cancel_time"]],
                    ];
            /*case ORDER_STATUS_USER_DELETE:
            case ORDER_STATUS_SELLER_DELETE:
            case ORDER_STATUS_ADMIN_DELETE:

                return
                    [
                        ["name"=>"提交订单", "date"=>$this->attributes["create_time"]],
                        ["name"=>"订单删除", "date"=>$this->attributes["create_time"]],
                    ];*/

        }
        return [];
    }


    /**
     * 是否可以退款(买家)
     * @return bool
     */
    public function getIsCanRefundAttribute()
    {
        $status     = $this->attributes['status'];
        $payFee    = $this->attributes['pay_fee'];

        return ($status == ORDER_STATUS_PAY_SUCCESS && $payFee >= 0.0001) ||
        ($status == ORDER_STATUS_CANCEL_ADMIN && $payFee >= 0.0001) ||
        ($status == ORDER_STATUS_CANCEL_SELLER && $payFee >= 0.0001) ||
        ($status == ORDER_STATUS_REFUND_AUDITING  && $payFee >= 0.0001);

    }
    /**
     * 是否可以接单(卖家)
     * @return bool
     */
    public function getIsCanAcceptAttribute()
    {
        $status 	= $this->attributes['status'];

        return $status == ORDER_STATUS_PAY_SUCCESS ||  $status == ORDER_STATUS_CANCEL_USER_SELLER ||
        $status == ORDER_STATUS_PAY_DELIVERY;
    }

    /**
     * 是否确定或取消订单退款
     * @return bool CANCEL
     */
    public function getIsCancfOrderAttribute()
    {
        $status 	= $this->attributes['status'];

        return $status == ORDER_STATUS_CANCEL_USER_SELLER;
    }
    //派发方式
    public function getDesignateAttribute() {
        return $this->attributes['designate_type'] == 1 ? '指定派发' : '随机派发';
    }


    /***************************************************不要的**********************************************************/


    /**
     * 退款url
     * @return string
     */
    public function getPathUrlAttribute()
    {
        $url  ="";

        $status  = $this->attributes['status'];

        if($status == ORDER_ADMIN_REFUSE_SERVICE)
        {
            $url =  u('wap#Order/details',array('id'=>$this->attributes['id']));
        }
        return  $url;
    }
    public function getMapPointAttribute() {
        if (!isset($this->attributes['map_point'])) {
            return ['x'=>0,'y'=>0];
        }
        $point = explode(',', $this->attributes['map_point']);

        if(is_array($point) && count($point) == 2)
        {
            return ['x'=>$point[0],'y'=>$point[1]];
        }

        return [];
    }


    //服务之前的图片
    public function getBeginImgAttribute() {
        if (!isset($this->attributes['begin_img']) || empty($this->attributes['begin_img'])) {
            return [];
        }
        return explode(',', $this->attributes['begin_img']);
    }

    //服务之后的图片
    public function getEndImgAttribute() {
        if (!isset($this->attributes['end_img']) || empty($this->attributes['end_img'])) {
            return [];
        }
        return explode(',', $this->attributes['end_img']);
    }

    //日程时间段
    public function getScheduleAttribute() {
        $beginTime = $this->attributes['appoint_time'];
        $endTime = $this->attributes['service_end_time'];
        return Time::toDate($beginTime,'H:i').' - '.Time::toDate($endTime, 'H:i');
    }
    //时长(时-分)
    public function getDurationAttribute() {
        if (!isset($this->attributes['duration'])) {
            return 0;
        }
        $hour = (int)($this->attributes['duration']/60);
        $minute = (int)($this->attributes['duration']%60);
        return $minute > 0 ? $hour.'小时'.$minute.'分' : $hour.'小时';
    }
    /****************************************************************************************************************/

    public function isCashOnDelivery(){
        if($this->attributes['pay_type'] == 'cashOnDelivery'){
            return true;
        }
        return false;
    }

    public function getPayType(){
        return $this->attributes['pay_type'];
    }

    public function getIsCashOnDeliveryAttribute() {
        return $this->attributes['pay_type'] == 'cashOnDelivery';
    }


    /**
     * 是否为联系商家取消(买家)
     * @return bool
     */
    public function getIsContactCancelAttribute()
    {
        $status 	= $this->attributes['status'];
        return
            $status ==  ORDER_STATUS_AFFIRM_SELLER ||
            $status == ORDER_STATUS_START_SERVICE ||
            $status == ORDER_STATUS_FINISH_STAFF;
    }

    /**
     * 是否可以退款(买家)
     * @return bool
     */
    public function getIsNewCanRefundAttribute()
    {
        $status     = $this->attributes['status'];
        $payFee    = $this->attributes['pay_fee'];
        return ( ($status == ORDER_STATUS_AFFIRM_SELLER || $status == ORDER_STATUS_CANCEL_SELLER) && $this->attributes['is_all'] == 1);
        return false;

    }
    /**
     * 是否可以催单
     * @return bool
     */
    public function getIsCanLogisticsAttribute()
    {
        $status 	= $this->attributes['status'];
        if( $this->attributes['is_all'] == 1){
            return $status == ORDER_STATUS_AFFIRM_SELLER;
        }
        return false;

    }

    /**
     * 订单是否取消
     */
    public function getIsCancelAttribute()
    {
        $cancel = [
            ORDER_STATUS_CANCEL_USER,
            ORDER_STATUS_CANCEL_AUTO,
        ];
        return in_array($this->attributes['status'], $cancel);
    }
}