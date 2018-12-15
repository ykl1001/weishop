@foreach($data['sellers'] as $item)
    <li @if($item['isDelivery'] == 0)style="background:#f3f3f3;"@endif class="c-bgfff @if(!$item['isBussiness']) x-rest @endif each" >
        <php>
            if($item['countGoods'] > 0){
            $url = u('Goods/index',['id'=>$item['id'],'type'=>1,'urltype'=>1]);
            }elseif($item['countGoods'] == 0 && $item['countService'] > 0){
            $url = u('Goods/index',['id'=>$item['id'],'type'=>2,'urltype'=>1]);
            }else{
            $url = u('Seller/detail',['id'=>$item['id'],'urltype'=>1]);
            }
        </php>
        <a href="{{$url}}" class="item-link item-content pageloading" data-no-cache="true">
            <div class="item-media">
                <img src="{{formatImage($item['logo'],100,100)}}" onerror='this.src="{{ asset("wap/community/newclient/images/no.jpg") }}"' width="73">
            </div>
            <div class="item-inner">
                <div class="item-title-row f16">
                    <div class="item-title c-black @if(!$item['isBussiness'])y-w80  @endif">
                        <p>{{ $item['name'] }}
                            @foreach($item['sellerAuthIcon'] as $val)
                                <img src="{{ $val['icon']['icon'] }}" class="ml5 va-3" width="16">
                            @endforeach
                        </p>
                    </div>
                    @if(!$item['isBussiness'])
                        <div class="item-after rest f12">休息中</div>
                    @endif
                </div>
                <div class="item-title-row f12 c-gray mt5 mb5">
                    <div class="item-title">
                        <div class="y-starcont">
                            <div class="c-gray4 y-star">
                                <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                            </div>
                            <div class="c-red f12 y-startwo" style="width:{{$item['score'] * 20}}%;">
                                <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                            </div>
                        </div>
                        @if($item['orderCount'] > 0)
                            <span class="c-gray f12">已售{{$item['orderCount']}}</span>
                        @else
                            <span class="c-gray f12"></span>
                        @endif
                    </div>
                    <span class="item-after">
                        <i class="icon iconfont c-gray2 f18">&#xe60d;</i>
                        <span class="compute-distance" data-map-point-x="{{ $item['mapPoint']['x'] }}" data-map-point-y="{{ $item['mapPoint']['y'] }}"></span>
                    </span>
                </div>
                <!-- <div class="item-subtitle c-gray">
                    <span class="mr10">{!! $item['freight'] !!}</span>
                </div> -->
                <div class="item-subtitle f12">
                    起送<span class="c-red mr5">￥{{$item['serviceFee']}}</span>
                    <span class="mr5">|</span>
                    配送<span class="c-red">￥{{$item['deliveryFee']}}</span>
                    @if($item['isAvoidFee'] == 1)
                        <span class="c-gray">(满{{$item['avoidFee']}}免)</span>
                    @endif
                </div>
            </div>
        </a>
        <ul class="y-mjyh">
            <?php $first = true; ?>
            @if(!empty($item['activity']['full']))
                <li class="pr15">
                    <p class="f12 c-gray">
                        <img src="{{ asset('wap/community/newclient/images/ico/jian.png')}}" width="16" class="vat mr5">
                        <span class="y-indexmaxw">
                        在线支付
                        @foreach($item['activity']['full'] as $key => $value)
                            @if($first)
                                <?php $first = false; ?>
                                满{{ $value['fullMoney'] }}减{{ $value['cutMoney'] }}元
                            @else
                                ,满{{ $value['fullMoney'] }}减{{ $value['cutMoney'] }}元
                            @endif
                        @endforeach
                        </span>
                    </p>
                </li>
            @endif
            @if(count($item['activity']['special']) > 0)
                <li>
                    <p class="f12 c-gray">
                        <img src="{{ asset('wap/community/newclient/images/ico/tei.png')}}" width="16" class="va-3 mr5">
                        商家特价优惠
                    </p>
                </li>
            @endif
            @if(!empty($item['activity']['new']))
                <li>
                    <p class="f12 c-gray">
                        <img src="{{ asset('wap/community/newclient/images/ico/xin.png')}}" width="16" class="va-3 mr5">
                        @if($item['activity']['new']['fullMoney'] > 0)
                            新用户在线支付满{{$item['activity']['new']['fullMoney']}}元立减{{$item['activity']['new']['cutMoney']}}元
                        @else
                            新用户在线支付立减{{$item['activity']['new']['cutMoney']}}元
                        @endif
                    </p>
                </li>
                @endif
                        <!-- 未展开 -->
                <i class="icon iconfont f12 c-gray y-unfold none y-i1">&#xe601;</i>
                <!-- 已展开 -->
                <i class="icon iconfont f12 c-gray y-unfold none">&#xe603;</i>
        </ul>
    </li>
@endforeach