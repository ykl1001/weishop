@if($lists)
    @foreach($lists as $item)
        <li>
            <div class="item-content">
                <!--formatImage( $item['avatar'] or asset('wap/community/newclient/images/tx1.png') ,100,100,1)-->
                <div class="item-media"><img src="{{$item['avatar'] or asset('wap/community/client/images/wdtt.png') }}" width="40" class="y-br"></div>
                <div class="item-inner">
                    <div class="item-title-row">
                        <div class="item-title f14">{{$item['userName']}}</div>
                        <div class="item-after f12 c-gray">级别：{{$item['level']}}级</div>
                    </div>
                    <div class="item-title f12 c-gray">推荐人：{{$item['partnerName']}}</div>
                </div>
            </div>
            <div class="y-tccenter p10 y-borbefore pr tc c-bgfff">
                <div class="pl5 pr5"><a href="javascript:$.href('{{u('Invitation/userlists',['userId'=>$item['userId']])}}')" class="db"><p class="f14">{{$item['partner'] or 0}}</p><p class="f12">下级合伙人</p></a></div>
                <div class="pl5 pr5"><a href="#" class="db"><p class="f14 c-red">￥{{$item['returnFee'] or 0}}</p><p class="f12">带来收益</p></a></div>
                <div class="pl5 pr5"><a href="javascript:$.href('{{u('MakeMoney/order',['userId'=>$item['userId']])}}')"  class="db"><p class="f14">{{$item['orderCount'] or 0}}</p><p class="f12">返利订单</p></a></div>
            </div>
        </li>

    @endforeach
@endif

{{--<ul class="row no-gutter c-bgfff y-wdhylist">--}}
    {{--<li class="col-33 tc">--}}
        {{--<p class="c-black f14">{{$item['name']}}</p>--}}
    {{--</li>--}}
    {{--<li class="col-33 tc">--}}
        {{--<p class="c-black f14">{{$item['percent']}}</p>--}}
    {{--</li>--}}
    {{--<li class="col-33 tc">--}}
        {{--<p class="c-red f15">@if($item['commision'] <= 0) 等待中 @else ￥{{$item['commision']}} @endif</p>--}}
    {{--</li>--}}
{{--</ul>--}}
