<ul class="y-xcoupon mt10">
    @foreach($list as $val)
        <li class="clearfix f12" id="li-{{ $val['id'] }}">
            <div class="y-xcouponleft tc" onclick="$.href('{{ u('Order/order',['proId'=>$val['id'],'cartIds'=>$args['cartIds'],'addressId'=>$args['addressId'],'appTime'=>$args['appTime'],'sendWay'=>$args['sendWay'],'sendType'=>$args['sendType']]) }}')">
                <div class="c-red">￥<span class="f24">{{$val['money']}}</span></div>
                <p class="c-gray">满减券</p>
            </div>
            @if(!empty($val['brief']))
                <a href="javascript:$.checkBrief({{ $val['id'] }})" class="c-blue f12 y-viewdetails">查看详情></a>
            @endif
            <div class="y-xcouponright" onclick="$.href('{{ u('Order/order',['proId'=>$val['id'],'cartIds'=>$args['cartIds'],'addressId'=>$args['addressId'],'appTime'=>$args['appTime'],'sendWay'=>$args['sendWay'],'sendType'=>$args['sendType']]) }}')">
                <p class="c-black f14 name">{{ $val['name'] }}</p>
                <p class="c-gray">满{{$val['limitMoney']}}元减{{$val['money']}}元</p>
                <p class="c-gray">{{$val['beginTimeStr']}}至{{$val['expireTimeStr']}}有效</p>
            </div>
            <div class="brief none">
                <ul>
                    <li>
                        <p>1、满{{$val['limitMoney']}}元减{{$val['money']}}元</p>
                    </li>
                    <li>
                        <p>2、{{$val['beginTimeStr']}}至{{$val['expireTimeStr']}}有效</p>
                    </li>
                    <li>
                        <p>3、{{ $val['brief'] }}</p>
                    </li>
                    <li>
                        <p>4、{{ $val['useTypeStr'] }}</p>
                    </li>
                </ul>
            </div>
        </li>
    @endforeach
</ul>
