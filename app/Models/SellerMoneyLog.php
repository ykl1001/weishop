<?php namespace YiZan\Models;
use YiZan\Models\SellerWithdrawMoney;

class SellerMoneyLog extends Base {
    const TYPE_ORDER_PAY        = 'order_pay';//收入
    const TYPE_ORDER_CONFIRM    = 'order_confirm';// 确认订单已到账
    const TYPE_ORDER_REFUND     = 'order_refund';// 订单取消的退款
    const TYPE_APPLY_WITHDRAW   = 'apply_withdraw';// 商家提现
    const TYPE_WITHDRAW_SUCCESS = 'withdraw_success';//支出
    const TYPE_WITHDRAW_ERROR   = 'withdraw_error';//收入
    const TYPE_DELIVERY_MONEY   = 'delivery_money';//货到付款抽成 支出
    const TYPE_SELLER_RECHARGE  = 'seller_recharge';//商家付款 收入
    const TYPE_SYSTEM_RECHARGE  = 'system_recharge';//平台充值
    const TYPE_SYSTEM_DEBIT     = 'system_debit';//平台扣款
    const TYPE_INVITATION_BACK  = 'invitation_back';//邀请返现
    const TYPE_PROPERTY_FEE     = 'property_fee';//物业缴费
    const TYPE_SEND_FEE     = 'send_fee';//平台配送服务费
 const TYPE_SHARE_FEE     = 'share_fee';//返利
    protected $appends = array(
        'typeStr',
        'statusStr',
        'refundInfo'
    );

    public function getTypeStrAttribute() {
        $type = $this->attributes['type'];
        $money = $this->attributes['money'];
        if (in_array($type, ['order_confirm','seller_recharge', 'system_recharge', 'property_fee', 'invitation_back', 'withdraw_error'])){
            $typeStr = '收入';            
        }else if($type == 'delivery_money'){
            if($money > 0){
                $typeStr = '收入';
            } else {
                $typeStr = '支出';
            }
        }else if(!in_array($type, ['order_pay', 'order_refund']) ){
            $typeStr = '支出';   
        }
        return $typeStr;
    }

    public function getStatusStrAttribute() {
        $status = $this->attributes['status'];
        $type = $this->attributes['type'];
        $statusStr = '成功';
        if($type == 'apply_withdraw' && $status == 0){
            $statusStr = '冻结';
        }else if($type == 'apply_withdraw' && $status == 2){
            $statusStr = '拒绝';
        }
        return $statusStr;
    }

    public function getRefundInfoAttribute() {
        $status = $this->attributes['status'];
        $type = $this->attributes['type'];
        if($type == 'apply_withdraw' && $status == 2){
            $sellerWithdrawMoney = SellerWithdrawMoney::where('id',$this->attributes['related_id'])->first();
            if(!empty($sellerWithdrawMoney)){
                $sellerWithdrawMoney = $sellerWithdrawMoney->toArray();
            }else{
                $sellerWithdrawMoney = "";
            }
        }else{
            $sellerWithdrawMoney = "";
        }
        return $sellerWithdrawMoney;
    }
}