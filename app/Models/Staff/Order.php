<?php namespace YiZan\Models\Staff;

class Order extends \YiZan\Models\Order
{
    /*protected $visible = ['id', 'sn', 'seller', 'goods', 'user', 'staff','name', 'appoint_time', 'promotion', 'service_fee', 'mobile', 'province','city','area',
     'address', 'pay_fee', 'discount_fee', 'total_fee', 'buy_remark', 'is_rate', 'status', 'pay_end_time','payStatusStr','isFinished','user_id',
     'orderStatusStr', 'pay_status',  'pay_time', 'create_time', 'pay_type', 'service_start_time', 'duration', 'goods_duration', 'reservation_code', 'is_to_store','userRefund', 'orderComplain',
     "isCanDelete", "isCanRate", "isCanComplain", "isCanCancel", "isCanRefund", "isCanPay", "isCanContact", "statusFlowImage", "statusNameDate", "isCanAccept", "isCanFinish", 'statusFlowImage',
     'seller_confirm_time', 'buyer_cancel_time', 'seller_confirm_end_time', 'refund_images', 'refund_content', 'order_num', 'deposit_refund_time', 'deposit_refund_content', 'Schedule','designate_type',
     'Designate','cartSellers','count','goods_fee','map_point','orderStr','orderRate','cancel_remark','order_type','isCanStartService','isCanFinishService','service_content','app_time','app_day',
        'orderGoods', 'isCanFinish','pay_type','isCanChangeStaff','freight','promotion_sn_id','drawn_fee','seller_id','discount_fee','seller_fee','isCashOnDelivery','send_way', 'auth_code', 'auth_code_use', 'is_now'];*/

    protected $appends = array('payStatusStr','isFinished','orderStatusStr', "isCanDelete", "isCanRate", "isCanAccept", "isCanFinish", "isCanComplain", "isCanCancel", "isCanRefund", "isCanPay", "isCanContact", 'isCanStartService','isCanFinishService',"statusFlowImage", "Schedule","Designate","duration",'isCanChangeStaff','isCashOnDelivery','orderNewStatusStr','isPay','newOrderStatusStr','isLogistics','orderNewStatusStr','isRefund', 'isCancel', 'isCanCall','isCanCancelCall','isCancfOrder');


    /**
     * 是否可以开始服务(卖家员工)
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
     * 查看物流(卖家员工)
     * @return bool
     */
    public function getIsLogisticsAttribute()
    {
        $status     = $this->attributes['status'];

        return
                $status == ORDER_STATUS_AFFIRM_SELLER ||
                $status == ORDER_STATUS_FINISH_STAFF ||
                $status == ORDER_STATUS_FINISH_SYSTEM ||
                $status == ORDER_STATUS_REFUND_SUCCESS ||
                $status == ORDER_REFUND_SELLER_AGREE ||
                $status == ORDER_REFUND_USER_REFUSE_LOGISTICS ||
                $status == ORDER_REFUND_SELLER_REFUSE_LOGISTICS ||
                $status == ORDER_REFUND_ADMIN_AGREE ||
                $status ==ORDER_STATUS_FINISH_USER;
    }

    /**
     * 是否可以完成服务(卖家员工)
     * @return bool
     */
    public function getIsCanFinishAttribute()
    {
        $status = $this->attributes['status'];

        if( in_array($this->attributes['send_way'],[2,3]) )
        {
            return $status == ORDER_STATUS_AUTH_CODE;  //商家接单（用于到店的服务，且验证码验证成功之后，无需配送）
        }
        else
        {
            return $status == ORDER_STATUS_START_SERVICE;  //开始服务于配送
        }

    }
    /**
     * 是否可以接单(卖家)
     * @return bool
     */
    public function getIsCanAcceptAttribute()
    {
        $status     = $this->attributes['status'];

        return $status == ORDER_STATUS_PAY_SUCCESS || $status == ORDER_STATUS_CANCEL_USER_SELLER ||
        $status == ORDER_STATUS_PAY_DELIVERY;
    }
    /**
     * 是否可以接单(卖家)
     * @return bool
     */
    public function getIsPayAttribute()
    {
        $status     = $this->attributes['status'];

        return $status == ORDER_STATUS_BEGIN_USER || $status == ORDER_STATUS_PAY_SUCCESS;
    }
    /**
     * 是否可以更换服务人员
     * @return bool
     */
    public function getIsCanChangeStaffAttribute()
    {
        $status     = $this->attributes['status'];

        return $status == ORDER_STATUS_AFFIRM_SELLER ||
        $status == ORDER_STATUS_BEGIN_USER ||
        $status == ORDER_STATUS_PAY_SUCCESS ||
        $status == ORDER_STATUS_PAY_DELIVERY ||
        $status == ORDER_STATUS_START_SERVICE;
    }

    /**
     * 付款状态显示
     * @return string
     */
    public function getPayStatusStrAttribute()
    {
        $payStatus     = $this->attributes['pay_status'];
        return $payStatus == 1 ? '已付款' : '未付款';
    }

    /**
     * 是否已完成
     * @return string
     */
    public function getIsFinishedAttribute()
    {
        $status     = $this->attributes['status'];

        return $status == ORDER_STATUS_AFFIRM_SELLER ||
        $status == ORDER_STATUS_BEGIN_USER ||
        $status == ORDER_STATUS_PAY_SUCCESS ||
        $status == ORDER_STATUS_PAY_DELIVERY ||
        $status == ORDER_STATUS_START_SERVICE;
    }


    /**
     * 订单状态
     * @return string
     */
    public function getOrderStatusStrAttribute()
    {
        $statusStr =
            [
                ORDER_STATUS_BEGIN_USER      => '新订单',
                ORDER_STATUS_SYSTEM_SEND     => '新订单',
                ORDER_STATUS_CALL_SYSTEM_SEND=> '呼叫平台',
                ORDER_STATUS_GET_SYSTEM_SEND => '取货中',
                ORDER_STATUS_PAY_SUCCESS     => '已付款',
                ORDER_STATUS_PAY_DELIVERY    => '货到付款',
                ORDER_STATUS_AFFIRM_SELLER   => '已接单',
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
                ORDER_STATUS_CANCEL_USER_SELLER    => '等待商家确认核审',

                ORDER_STATUS_REFUND_AUDITING     => '申请退款',
                ORDER_STATUS_CANCEL_REFUNDING   => '取消且退款中',
                ORDER_STATUS_REFUND_HANDLE   => '退款处理中',
                ORDER_STATUS_REFUND_FAIL   => '退款失败',
                ORDER_STATUS_REFUND_SUCCESS   => '退款成功',
                ORDER_REFUND_SELLER_AGREE   => '商家同意退款',
                ORDER_REFUND_SELLER_REFUSE   => '商家拒绝退款',
                ORDER_REFUND_SELLER_REFUSE_LOGISTICS   => '退款中',
                ORDER_REFUND_ADMIN_AGREE   => '平台同意退款',
                ORDER_REFUND_ADMIN_REFUSE   => '平台拒绝退款',
            ];

        $orderType = $this->attributes['order_type'];
        if ($orderType == 1) {
            $statusStr[ORDER_STATUS_FINISH_STAFF] = '配送完成';
            $statusStr[ORDER_STATUS_START_SERVICE] = '配送中';
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
     * 订单状态
     * @return string
     */
    public function getNewOrderStatusStrAttribute()
    {
        $statusStr =
            [
                ORDER_STATUS_BEGIN_USER      => $this->attributes['is_all'] == 1 ? '等待买家付款' : '下单成功',
                ORDER_STATUS_SYSTEM_SEND     => $this->attributes['is_all'] == 1 ? '等待买家付款' : '下单成功',
                ORDER_STATUS_PAY_SUCCESS     => $this->attributes['is_all'] == 1 ? '等待发货' :'已付款',
                ORDER_STATUS_CALL_SYSTEM_SEND=> '呼叫平台',
                ORDER_STATUS_GET_SYSTEM_SEND => '取货中',
                ORDER_STATUS_PAY_DELIVERY    => '货到付款',
                ORDER_STATUS_AFFIRM_SELLER   => $this->attributes['is_all'] == 1 ? '卖家已发货' : '已接单',
                ORDER_STATUS_FINISH_STAFF    => '交易完成',
                ORDER_STATUS_FINISH_SYSTEM   => '交易完成',
                ORDER_STATUS_FINISH_USER     => '交易完成',
                ORDER_STATUS_CANCEL_USER     => '交易关闭',
                ORDER_STATUS_CANCEL_AUTO     => '支付超时',
                ORDER_STATUS_CANCEL_SELLER   => '交易关闭',
                ORDER_STATUS_CANCEL_ADMIN    => '交易关闭',
                ORDER_STATUS_USER_DELETE     => '会员删除订单',
                ORDER_STATUS_SELLER_DELETE   => '商家删除订单',
                ORDER_STATUS_ADMIN_DELETE    => '总后台删除订单',
                ORDER_STATUS_CANCEL_USER_SELLER    => '等待商家确认核审',
                ORDER_STATUS_REFUND_AUDITING     => '申请退款',
                ORDER_STATUS_CANCEL_REFUNDING   => '取消且退款中',
                ORDER_STATUS_REFUND_HANDLE   => '退款处理中',
                ORDER_STATUS_REFUND_FAIL   => '退款失败',
                ORDER_STATUS_REFUND_SUCCESS   => '退款成功',
                ORDER_REFUND_SELLER_AGREE   => '商家同意退款',
                ORDER_REFUND_SELLER_REFUSE   => '商家拒绝退款',
                ORDER_REFUND_ADMIN_AGREE   => '平台同意退款',
                ORDER_REFUND_ADMIN_REFUSE   => '平台拒绝退款',
                ORDER_REFUND_SELLER_REFUSE_LOGISTICS   => '退款中',
                ORDER_REFUND_USER_REFUSE_LOGISTICS =>  '退款中',
            ];

        $orderType = $this->attributes['order_type'];
        if ($orderType == 1) {
            $statusStr[ORDER_STATUS_FINISH_STAFF] = '配送完成';
            $statusStr[ORDER_STATUS_START_SERVICE] = '配送中';
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


    public function getIsCashOnDeliveryAttribute() {
        return $this->attributes['pay_type'] == 'cashOnDelivery';
    }

    /**
     * 是否可以取消(买家)
     * @return bool
     */
    public function getIsCanCancelAttribute()
    {
        $status 	= $this->attributes['status'];
        return
            $status ==  ORDER_STATUS_PAY_DELIVERY ||
            $status == ORDER_STATUS_PAY_SUCCESS ||
            $status == ORDER_STATUS_AFFIRM_SELLER ||
            $status == ORDER_STATUS_BEGIN_USER ||
            $status == ORDER_STATUS_START_SERVICE ||
            $status == ORDER_STATUS_SYSTEM_SEND ||
            $status == ORDER_STATUS_CALL_SYSTEM_SEND ||
            $status == ORDER_STATUS_GET_SYSTEM_SEND;
    }

    /**
     * 新订单状态+提示
     */
    public function getOrderNewStatusStrAttribute()
    {
        $cancelRemark = $this->attributes['cancel_remark'] ? $this->attributes['cancel_remark'] :  '无';
        $is_all = $this->attributes['is_all'] == 1 ? "发货" :  '接单';
        $title = $this->attributes['is_all'] == 1 ? "待发货" :  '已支付，等待'.$is_all;
        $statusStr =
            [
                ORDER_STATUS_BEGIN_USER          => ['title'=>'等待支付', 'tag'=>'请在提交订单后15分钟内完成支付', 'time'=>$this->attributes['create_time']],
                ORDER_STATUS_SYSTEM_SEND         => ['title'=>'等待支付', 'tag'=>'请在提交订单后15分钟内完成支付', 'time'=>$this->attributes['create_time']],
                ORDER_STATUS_CALL_SYSTEM_SEND    => ['title'=>'平台配送', 'tag'=>'', 'time'=>''],
                ORDER_STATUS_GET_SYSTEM_SEND     => ['title'=>'取货中', 'tag'=>'', 'time'=>''],
                ORDER_STATUS_PAY_SUCCESS         => ['title'=>$title, 'tag'=>'请耐心等待商家确认'],
                ORDER_STATUS_PAY_DELIVERY        => ['title'=>'货到付款，等待'.$is_all, 'tag'=>'请耐心等待商家确认', 'time'=>$this->attributes['create_time']],
                ORDER_STATUS_AFFIRM_SELLER       => ['title'=>'商家已'.$is_all, 'tag'=>$is_all.'时间', 'time'=>$this->attributes['seller_confirm_time']],

                ORDER_STATUS_FINISH_STAFF        => ['title'=>'服务完成', 'tag'=>'服务已完成，请确认完成订单', 'time'=>$this->attributes['staff_finish_time']],
                ORDER_STATUS_AUTH_CODE           => ['title'=>'消费码验证成功', 'tag'=>'您的消费码已验证，等待商家确认完成', 'time'=>$this->attributes['auth_code_use_time']],
                ORDER_STATUS_FINISH_SYSTEM       => ['title'=>'订单已完成', 'tag'=>'任何意见和吐槽，都欢迎联系我们', 'time'=>$this->attributes['buyer_finish_time']],
                ORDER_STATUS_FINISH_USER         => ['title'=>'订单已完成', 'tag'=>'任何意见和吐槽，都欢迎联系我们', 'time'=>$this->attributes['buyer_finish_time']],
                ORDER_STATUS_CANCEL_USER         => ['title'=>'订单取消', 'tag'=>'买家已取消订单，原因：'.$cancelRemark, 'time'=>$this->attributes['cancel_time']],
                ORDER_STATUS_CANCEL_AUTO         => ['title'=>'支付超时', 'tag'=>'支付超时，请重新下单', 'time'=>$this->attributes['cancel_time']],
                ORDER_STATUS_CANCEL_SELLER       => ['title'=>'订单取消', 'tag'=>'商家已取消订单，原因：'.$cancelRemark, 'time'=>$this->attributes['cancel_time']],
                ORDER_STATUS_CANCEL_ADMIN        => ['title'=>'订单取消', 'tag'=>'总后台已取消订单，原因：'.$cancelRemark, 'time'=>$this->attributes['cancel_time']],
                ORDER_STATUS_USER_DELETE         => ['title'=>'会员删除订单', 'tag'=>'订单已删除', 'time'=>''],
                ORDER_STATUS_SELLER_DELETE       => ['title'=>'商家删除订单', 'tag'=>'订单已删除', 'time'=>''],
                ORDER_STATUS_ADMIN_DELETE        => ['title'=>'总后台删除订单', 'tag'=>'订单已删除', 'time'=>''],

                ORDER_STATUS_REFUND_AUDITING     => ['title'=>'申请退款', 'tag'=>$this->attributes['refund_content'], 'time'=>$this->attributes['refund_time']],
                ORDER_STATUS_CANCEL_REFUNDING    => ['title'=>'退款中', 'tag'=>$this->attributes['refund_content'], 'time'=>$this->attributes['refund_time']],
                ORDER_STATUS_REFUND_HANDLE       => ['title'=>'退款中', 'tag'=>$this->attributes['refund_content'], 'time'=>$this->attributes['refund_time']],
                ORDER_STATUS_REFUND_FAIL         => ['title'=>'退款失败', 'tag'=>$this->attributes['refund_content'], 'time'=>$this->attributes['refund_time']],
                ORDER_STATUS_REFUND_SUCCESS      => ['title'=>'退款成功', 'tag'=>$this->attributes['refund_content'], 'time'=>$this->attributes['refund_time']],
                ORDER_REFUND_SELLER_AGREE        => ['title'=>'退款中', 'tag'=>$this->attributes['dispose_refund_seller_remark'], 'time'=>$this->attributes['dispose_refund_seller_time']],
                ORDER_REFUND_SELLER_REFUSE       => ['title'=>'商家拒绝退款', 'tag'=>$this->attributes['dispose_refund_seller_remark'], 'time'=>$this->attributes['dispose_refund_seller_time']],
                ORDER_REFUND_ADMIN_AGREE         => ['title'=>'退款中', 'tag'=>$this->attributes['dispose_refund_seller_remark'], 'time'=>$this->attributes['dispose_refund_seller_time']],
                ORDER_REFUND_ADMIN_REFUSE        => ['title'=>'总后台拒绝退款', 'tag'=>$this->attributes['dispose_refund_remark'], 'time'=>$this->attributes['dispose_refund_time']],
                ORDER_REFUND_SELLER_REFUSE_LOGISTICS   =>  ['title'=>'退款中', 'tag'=>'待平台处理',  'time'=>$this->attributes['dispose_refund_seller_time']],
                ORDER_REFUND_USER_REFUSE_LOGISTICS =>   ['title'=>'退款中', 'tag'=>'待商家收货', 'time'=>$this->attributes['dispose_refund_seller_time']],
		ORDER_STATUS_CANCEL_USER_SELLER =>   ['title'=>'等待商家确认核审', 'tag'=>'等待商家确认核审', 'time'=>$this->attributes['cancel_time']],
            ];
        $orderType = $this->attributes['order_type'];
        if ($orderType == 1) {
            $statusStr[ORDER_STATUS_FINISH_STAFF]   = ['title'=>'配送完成', 'tag'=>'任何意见和吐槽，都欢迎联系我们', 'time'=>$this->attributes['fre_time']];
            $statusStr[ORDER_STATUS_START_SERVICE]  = ['title'=>'配送中', 'tag'=>'正在配送，请耐心等待', 'time'=>$this->attributes['seller_confirm_time']];
        } else {
            $statusStr[ORDER_STATUS_FINISH_STAFF]   = ['title'=>'服务完成', 'tag'=>'任何意见和吐槽，都欢迎联系我们', 'time'=>$this->attributes['fre_time']];
            $statusStr[ORDER_STATUS_START_SERVICE]  = ['title'=>'服务中', 'tag'=>'', 'time'=>$this->attributes['seller_confirm_time']];
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