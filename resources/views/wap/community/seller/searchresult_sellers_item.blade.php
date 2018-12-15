<?php
function loopGoods($data){
    $str = "";
    foreach($data as $key=>$d){
        $class = $key>0?' none':'';
        $str .= " <div class='item-subtitle pr goodslst c-black f14 pr10 ".$class."'>
                                    <span class='mr10'><a href='".u('Goods/detail',['goodsId'=>$d['id']])."'>".$d['name']."</a></span>
                                    <span class='fr c-red f16'>￥".$d['price']."</span>
                                </div>";
    }
    //echo $str;

}

?>
@foreach($data as $key => $item)
    <php>
        if($item['countGoods'] > 0 && $item['countService'] == 0){
        $gourl = u('Goods/index',['id'=>$item['id'],'type'=>1,'urltype'=>1]);
        }elseif($item['countGoods'] == 0 && $item['countService'] > 0){
        $gourl = u('Goods/index',['id'=>$item['id'],'type'=>2,'urltype'=>1]);
        }else{
        $gourl = u('Seller/detail',['id'=>$item['id'],'urltype'=>1]);
        }
    </php>
    <li  class="@if($item['serviceTimesCount'] < 1 && $item['storeType'] != 1)x-rest @endif c-bgfff">
        <div class="item-link item-content">
            <div class="item-media"><a href="{{ $gourl }}"><img src="{{formatImage($item['logo'],100,100)}}" onerror='this.src="{{ asset("wap/community/newclient/images/no.jpg") }}"' width="73"></a></div>
            <div class="item-inner">
                <div class="item-title-row f16"  onclick="$.href('{{ $gourl }}')" >
                    <div class="item-title c-black">{{ $item['name'] }}</div>
                    @if($item['serviceTimesCount'] < 1 && $item['storeType'] != 1)
                        <div class="item-after rest f12 mr5">休息中</div>
                    @endif

                    <div class="item-after f14">进店<i class="icon iconfont f14">&#xe602;</i></div>
                </div>
                <div class="item-subtitle f12 c-gray mt5">
                    <span>{{$item['countGoods']}}件商品</span>
                    <span class="c-gray f12 ml5 mr5">已售{{$item['orderCount']}}单</span>
                    <span>评分{{$item['score']}}分</span>
                </div>
                <div class="item-title-row c-gray">
                    @if($item['storeType'] == 1 )
                        <div class="item-title f12">来自:{{ $item['province']['name'].$item['city']['name'] }}</div>
                    @else
                        <div class="item-subtitle f12">
                            起送<span class="c-red mr5">￥{{$item['serviceFee']}}</span>
                            <span class="mr5">|</span>
                            配送<span class="c-red">￥{{$item['deliveryFee']}}</span>
                            @if($item['avoidFee'])
                                <span class="c-gray">(满{{$item['avoidFee']}}免)</span>
                            @endif
                        </div>
                    @endif
                    <div class="item-after"><i class="icon iconfont c-gray2 f18">&#xe60d;</i><span class="compute-distance  f14" data-map-point-x="{{ $item['mapPoint']['x'] }}" data-map-point-y="{{ $item['mapPoint']['y'] }}">{{$item['distance']}}m</span></div>
                </div>
            </div>
        </div>
        <div class="c-bgfff y-tag">
            <div class="c-orange f12">
                @if($item['storeType'] == 0)周边店@else全国店@endif<img src="{{ asset('wap/community/newclient/images/y15.png')}}" class="va-1 ml2" width="12">
                @foreach($item['sellerAuthIcon'] as $val)
                    {{ $val['icon']['name'] }}<img src="{{ $val['icon']['icon'] }}" class="va-1 ml5" width="12">
                @endforeach
            </div>
        </div>
        <ul class="y-mjyh">
            <?php $first = true; ?>
            @if(!empty($item['activity']['full']))
                <li><p class="f12 c-gray"><img src="{{ asset('wap/community/newclient/images/ico/jian.png')}}" width="16" class="va-3 mr5">在线支付
                        @foreach($item['activity']['full'] as $key => $value)
                            @if($first)
                                <?php $first = false; ?>
                                满{{$value['fullMoney']}}减{{$value['cutMoney']}}元
                            @else
                                ,满{{$value['fullMoney']}}减{{$value['cutMoney']}}元
                            @endif

                        @endforeach
                    </p></li>
            @endif
            @if(count($item['activity']['special']) > 0)
                <li><p class="f12 c-gray"><img src="{{ asset('wap/community/newclient/images/ico/tei.png')}}" width="16" class="va-3 mr5"> 商家特价优惠</p></li>
            @endif
            @if(!empty($item['activity']['new']))
                <li><p class="f12 c-gray"><img src="{{ asset('wap/community/newclient/images/ico/xin.png')}}" width="16" class="va-3 mr5">新用户在线支付立减{{$item['activity']['new']['cutMoney']}}元</p></li>
                @endif
                        <!-- 未展开 -->
                <i class="icon iconfont f12 c-gray y-i1 y-unfold none">&#xe601;</i>
                <!-- 已展开 -->
                <i class="icon iconfont f12 c-gray y-unfold none">&#xe603;</i>
        </ul>

    </li>
@endforeach