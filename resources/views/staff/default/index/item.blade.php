@foreach($list['orders'] as $vs)
    {{--<div class="list-block orderlist" onclick="JumpURL('{{u('Index/detail',['id'=>$vs['id']])}}','#index_detail_view',2)">--}}
        {{--<div class="l-ordertitle">--}}
            {{--<div class="f_l">下单时间：{{$vs['createTime']}}</div>--}}
            {{--<div class="f_r focus-color-f">{{$vs['orderStatusStr']}}</div>--}}
        {{--</div>--}}
        {{--<a href="#">--}}
            {{--<div class="l-ordercon">--}}
                {{--<div class="f_l orderconimg">--}}
                    {{--@foreach($vs['images'] as $goods)--}}
                        {{--<img src="{{$goods}}" class="logo_shop_img" />--}}
                    {{--@endforeach--}}
                {{--</div>--}}
                {{--<div class="f_r tot_r">共<em class="focus-color-f">{{$vs['num']}}</em>件<i class="iconfont">&#xe64b;</i></div>--}}
            {{--</div>--}}
        {{--</a>--}}
        {{--<div class="l-orderfoot">--}}
            {{--<span class="f_l">￥<em class="focus-color-f">{{$vs['payFee']}}</em>元</span>--}}
            {{--<span class="f_r">订单号：{{$vs['sn']}}</span>--}}
        {{--</div>--}}
    {{--</div>--}}

    <li>
        <a href="#"  @if($vs['isChange'] > 0) @else onclick="JumpURL('{{u('Index/detail',['id'=>$vs['id']])}}','#index_detail_view',2)" @endif class="item-link item-content" @if($vs['isChange'] > 0) style="border-top-color: #aaa;" @endif>
            <div class="item-inner f12">
                <div class="item-title-row">
                    <div class="item-title y-item-title f13 bold @if($vs['isChange'] > 0) f_aaa @endif ">{{$vs['address']}}</div>
                    <div class="item-after f12 @if($vs['isChange'] > 0) f_aaa @endif  udb_show_map" data-map-point-x="{{$vs['mapPoint']['x']}}" data-map-point-y="{{$vs['mapPoint']['y']}}" >{{$vs['distance']}}km</div>
                </div>
                <div class="item-title mt5 @if($vs['isChange'] > 0) f_aaa @endif "><span>{{$vs['userName']}}</span><span class="ml15 @if($vs['isChange'] > 0) f_aaa @endif ">{{$vs['userMobile']}}</span></div>
            </div>
            <div class="item-inner f12">
                <div class="item-title f12 f_aaa"><span>下单时间:</span><span>{{$vs['createTime']}}</span></div>
                <div class="item-title f12 f_aaa"><span>订单号:</span><span>{{$vs['sn']}}</span></div>
            </div>
            <div class="item-inner f12 y-min-height22">
                <div class="item-title-row">
                    <div class="item-title f12 @if($vs['isChange'] > 0) f_aaa @endif ">本单收入</div>
                    <div class="item-after">
                        <span class="y-paymentstatusbtn @if($vs['isChange'] > 0) bg_a5a5a5 @endif ">{{$vs['orderStatusStr']}}</span>
                        <span class="@if($vs['isChange'] > 0) f_aaa @endif  f12">￥{{$vs['payFee']}}</span>
                    </div>
                </div>
            </div>
            @if($vs['sendWay'] == 1 || $vs['sendWay'] == 0)
                <div class="item-inner f12 y-min-height22">
                    <div class="item-title y-item-title f13 bold @if($vs['isChange'] > 0) f_aaa @endif "><span>取货地址:</span><span>{{$vs['sellerAddress']}}</span></div>
                </div>
            @endif

        </a>
        @if($vs['isChange'] == 1)
            <div class="pt5 pb5 pr10 pl10 f_333 f12" style="background: #ffff99">订单取消</div>
        @elseif($vs['isChange'] == 2)
            <div class="pt5 pb5 pr10 pl10 f_333 f12" style="background: #ffff99">此订单已重新分配</div>
        @endif
    </li>

@endforeach