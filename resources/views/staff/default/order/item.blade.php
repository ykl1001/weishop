@foreach($list['orders'] as $vs)
    <li>
        <a href="#"  onclick="JumpURL('{{u('Order/detail',['id'=>$vs['id']])}}','#order_detail_view',2)" class="item-link item-content">
            <div class="item-inner f12">
                <div class="item-title-row">
                    <div class="item-title y-item-title">{{$vs['address']}}</div>
                    <div class="item-after f12 f_aaa udb_show_map" data-map-point-x="{{$vs['mapPoint']['x']}}" data-map-point-y="{{$vs['mapPoint']['y']}}" >{{$vs['distance']}}km</div>
                </div>
                <div class="item-title mt5"><span>{{$vs['userName']}}</span><span class="ml15 f_red">{{$vs['userMobile']}}</span></div>
            </div>
            <div class="item-inner f12">
                <div class="item-title f12 f_aaa"><span>下单时间:</span><span>{{$vs['createTime']}}</span></div>
                <div class="item-title f12 f_aaa"><span>订单号:</span><span>{{$vs['sn']}}</span></div>
            </div>
            <div class="item-inner f12 y-min-height22">
                <div class="item-title-row">
                    <div class="item-title f12 f_aaa">本单收入</div>
                    <div class="item-after">
                        <span class="y-paymentstatusbtn">{{$vs['orderStatusStr']}}</span>
                        <span class="f_red f12">￥{{$vs['payFee']}}</span>
                    </div>
                </div>
            </div>
        </a>
    </li>
@endforeach