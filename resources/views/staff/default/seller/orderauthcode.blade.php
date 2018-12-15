@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{u('Order/index',['status'=>2])}}','#order_index_view_2',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">订单详情</h1>
    </header>
@stop
@section('contentcss')pull-to-refresh-content @stop
@section('distance')data-ptr-distance="20" @stop
@section('content')

    @include('staff.default._layouts.refresh')
    <div class="order-finish fred text-align-center bt1 title-text-align-center">{{$data['orderStatusStr']}}</div>
    <div class="order-wrap">
        <div class="delivery-info">
            <div class="delivery-title">配送信息</div>
            <div class="delivery-content">
                <div class="flex delivery-name-phone btop1">
                    <div class="flex-2 ml0575 br-1">
                        <span>{{$data['name']}}</span>
                        <span onclick="javascript:;" class="ml0575 f-light-color">{{$data['mobile']}}</span>
                    </div>
                    <a href="tel:{{$data['mobile']}}" external class="flex-1 text-align-center fred"><i class="iconfont">&#xe60e;</i></a>
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
                                <div class="item-title flex-1">{{$v['goodsName']}}</div>
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
                            <div class="item-title flex-1">合&nbsp;&nbsp;&nbsp;&nbsp;计</div>
                            <div class="item-after flex-1"> </div>
                            <div class="item-after fred">￥{{$data['totalFee']}}</div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div>
            <div class="order-title">订单信息</div>
            <div class="order-content btop1">
                <div class="order-padding">支付方式:&nbsp;&nbsp;{{$data['payType']}}</div>
                <div class="order-padding">订单编号:&nbsp;&nbsp;{{$data['sn']}}</div>
                <div class="order-padding">店铺:&nbsp;&nbsp;{{$data['sellerName']}}</div>
                <div class="good-padding">顾客下单时间:&nbsp;&nbsp;{{$data['createTime']}}</div>
                <div class="blank0825"> </div>
            </div>
        </div>
    </div> 
    <div class="blank050"></div>
    <div class="blank0825"> </div>   
@stop


@section('show_nav')    @stop

@section('preview')
<div class="bar bar-footer y-footerbar">
    <p>
        @if($data['authCodeUse'] == 1)
            <!-- 未使用 -->
            <a href="#" class="button button-fill button-danger" onclick="$.isCanFinishService({{$data['id']}},3 )">完成消费</a>
        @else
            <!-- 已使用 -->
            <a  href="#" onclick="JumpURL('{{u('Seller/index')}}','#seller_index_view',2)" class="button button-fill button-danger">返回商家首页</a>
        @endif
    </p>
</div>
@stop

@section($js)
<script type="text/javascript">
    //完成
    $.isCanFinishService = function (id, status) {
        $.showIndicator();
        $.post("{{ u('Order/orderReceiving') }}", {'id': id, 'status': status}, function (res) {
            if(res.code == 0)
            {
                $.alert(res.msg, function(){
                    window.location.reload();
                });
            }
            else
            {
                $.toast(res.msg);
            }

            $.hideIndicator();
        }, "json");
    }
</script>
@stop
