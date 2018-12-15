<div class="order-wrap">
    <div class="delivery-info">
        <div class="delivery-title">配送信息</div>
        <div class="delivery-content">
            <div class="flex delivery-name-phone btop1">
                <div class="flex-2 ml0575 br-1">
                    <span>{{$data['name']}}</span>
                    <span class="ml0575 f-light-color">{{$data['mobile']}}</span>
                </div>
                <span class="flex-1 text-align-center"><a href="tel:{{$data['mobile']}}" external class=" fred"><i class="iconfont">&#xe60e;</i></a></span>
            </div>
            <div class="flex delivery-location fine-bor-top clearfix">
                <span class="flex-2 ml0575 br-1 delivery-location-padding">{{$data['province'] . $data['city'] . $data['area'] . $data['address']}}</span>
                <div class="flex-1 delivery-distance">
                    <div class=""><i class="iconfont">&#xe615;</i><span>{{$data['distance']}}km</span></div>
                </div>
            </div>
            <div class="servertime fine-bor-top">
                服务时间:&nbsp;&nbsp;{{$data['appTime']}}
            </div>
        </div>
    </div>
    <div class="good-info">
        <div class="good-title">@if($data['orderType'] == 1)商品信息@else服务信息@endif</div>
        <div class="good-content btop1">
            <ul>
                @foreach($data['orderGoods'] as $v)
                    <li class="item-content good-padding">
                        <div class="item-inner flex">
                            <div class="item-title flex-1">{{$v['goodsName']}}@if($v['goodsNorms']){{str_replace(':','-',$v['goodsNorms']['skuName'])}}@endif</div>
                            <div class="item-after flex-1">×{{$v['num']}}</div>
                            <div class="item-after">￥{{$v['price']}}</div>
                        </div>
                    </li>
                @endforeach
                    <li class="item-content good-padding">
                        <div class="item-inner flex">
                            <div class="item-title flex-1">配送费</div>
                            <div class="item-after flex-1"></div>
                            <div class="item-after">￥{{$data['freight']}}</div>
                        </div>
                    </li>
                    <li class="item-content good-padding">
                        <div class="item-inner flex">
                            <div class="item-title flex-1">优惠券</div>
                            <div class="item-after flex-1"></div>
                            <div class="item-after">￥{{$data['discountFee']}}</div>
                        </div>
                    </li>
                    <li class="item-content good-padding">
                        <div class="item-inner flex">
                            <div class="item-title flex-1">积分抵扣金额</div>
                            <div class="item-after flex-1"></div>
                            <div class="item-after">￥{{$data['integralFee']}}</div>
                        </div>
                    </li>
                    <li class="item-content good-padding">
                        <div class="item-inner flex">
                            <div class="item-title flex-1">使用积分</div>
                            <div class="item-after flex-1"></div>
                            <div class="item-after">{{$data['integral']}}</div>
                        </div>
                    </li>
                    @if($data['activityNewMoney'] > 0)
                        <li class="item-content good-padding">
                            <div class="item-inner flex">
                                <div class="item-title flex-1">首单优惠</div>
                                <div class="item-after flex-1"></div>
                                <div class="item-after">{{$data['activityNewMoney']}}</div>
                            </div>
                        </li>
                    @endif
                    @if($data['activityFullMoney'] > 0)
                        <li class="item-content good-padding">
                            <div class="item-inner flex">
                                <div class="item-title flex-1">满减优惠</div>
                                <div class="item-after flex-1"></div>
                                <div class="item-after">{{$data['activityFullMoney']}}</div>
                            </div>
                        </li>
                    @endif
                    @if($data['activityGoodsMoney'] > 0)
                        <li class="item-content good-padding">
                            <div class="item-inner flex">
                                <div class="item-title flex-1">特价优惠</div>
                                <div class="item-after flex-1"></div>
                                <div class="item-after">{{$data['activityGoodsMoney']}}</div>
                            </div>
                        </li>
                    @endif
                    <li class="item-content good-padding y-good-padding">
                        <div class="item-inner flex">
                            <div class="item-title flex-1">本单收入</div>
                            <div class="item-title fred">￥{{$data['totalFee']}}</div>
                        </div>
                    </li>
            </ul>
        </div>
    </div>
    <div class="order-title">@if($data['orderType'] == 1)商品信息@else服务信息@endif</div>
    <div >
        <div class="order-content btop1 mb10 pb5">
            <div class="order-padding">支付方式:&nbsp;&nbsp;{{$data['payType']}}</div>

            @if($data['isAll'] == 0)
            <div class="order-padding">配送方式:&nbsp;&nbsp;{{$data['freType']}}</div>
            @endif

            <div class="order-padding">订单编号:&nbsp;&nbsp;{{$data['sn']}}</div>
            <div class="order-padding">店铺:&nbsp;&nbsp;{{$data['sellerName']}}</div>
            @if(!empty($data['buyRemark']))
                <div class="order-padding ">订单备注:&nbsp;&nbsp;{{$data['buyRemark']}}</div>
            @endif
            <div class="good-padding ">下单时间:&nbsp;&nbsp;{{$data['createTime']}}</div>
            @if(in_array($role,['1','3','5','7']))
                <div class="order-padding fine-bor-top flex pt5">
                    <div class="flex-2">
                        <div class="f_l flex-1">@if($data['orderType'] == 1)配送员@else服务人员@endif:&nbsp;&nbsp;</div>
                        @if($data['status'] == ORDER_STATUS_CALL_SYSTEM_SEND || $data['status'] == ORDER_STATUS_SYSTEM_SEND)
                            <div class="f_l flex-1">待平台指派</div>
                        </div>
                        @else
                            <div class="f_l flex-1">{{$data['staff']['name']}}</div>
                        </div>
                        <a href="tel:{{$data['staff']['mobile']}}" external class="fred f_r mr_percentage_5">{{$data['staff']['mobile']}}<i class="iconfont">&#xe60e;</i></a>
                        @endif
                    @if($data['isCanChangeStaff'])
                        <a id="isCanChangeStaff" href="#"  onclick="JumpURL('{{u('Order/staff',['id'=>$data['id'],'staffId'=>$data['staff']['id'],'type'=>$data['orderType'],'tpl'=>$tpl])}}','#order_staff_view',2)"  class="f_r text-align-right" >
                            更换
                            <i class="iconfont">&#xe64b;</i>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>