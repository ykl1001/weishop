@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back" href="{{ u('MakeMoney/order') }}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">订单详情</h1>
    </header>
@stop
@section('content')

    <div class="content" id=''>

        <div class="card m0 y-ordercont">
            <div class="card-content">
                <div class="card-content-inner clearfix f13">
                    <div class="y-tccenter">
                        <span>收货人:{{$data['userName']}}<span class="ml10">{{$data['userMobile']}}</span></span>

                        @if($data['orderStatus'] == 1)
                            <span class="c-yellow">
                                已入账					
								</span>
                        @elseif($data['orderStatus'] == 0)
                            @if($data['orderIsRefund'] == 0)
                                <span class="c-blue">
                                    待入账					
								</span>
                            @else
                                <span class="c-red">
                                    平台退回					
									</span>
                            @endif
                        @endif
                    </div>
                    <div class="c-gray f13 lh20 col-10 mt5">{{$data['userProvince'].$data['userCity'].$data['userArea'].$data['userAddress']}}</div>
                </div>
            </div>

            <div class="card-footer">
                <div class="f13 c-black">订单编号:{{$data['orderSn']}}</div>
            </div>

        </div>

        <div class="list-block media-list f14 y-xddxqlist">

            <ul>

                <li class="item-content">
                    <div class="item-media"><img src="{{$data['goodsImages']}}" width="65"></div>
                    <div class="item-inner f12 pr10">
                        <div class="item-title-row">
                            <div class="item-title f13">{{$data['goodsName']}}*{{$data['num']}}</div>
                            <div class="item-after tr">
                                <p class="12 c-red">￥<span class="f16">{{$data['price']}}</span></p>
                                <p class="c-gray5">x{{$data['num']}}</p>
                            </div>
                        </div>
                        @if($data['goodsNorms'])
                            <div class="item-title c-gray mt5">规格:{{$data['goodsNorms']}}</div>
                        @endif
                    </div>
                </li>

                <li class="item-content">
                    <div class="y-tccenter w100 pr10 f13">
                        <div class="c-black"><span class="c-gray mr10 f12">{{$data['num']}}件商品</span>总计￥<span>{{$data['orderTotalFee']}}</span></div>
                        <div>奖励￥{{$data['reFee']}}元</div>
                    </div>
                </li>

            </ul>
        </div>

        <div class="c-bgfff p10 c-gray f12">

            <p>购买人:{{$data['uName']}}</p>
            <p>联系方式:{{$data['uMobile']}}</p>
            <p>下单时间:{{ Time::toDate($data['orderCreateTime'],'Y-m-d H:i:s')  }}</p>

        </div>

    </div>
@stop
