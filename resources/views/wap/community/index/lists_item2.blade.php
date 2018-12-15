@foreach($data['goods'] as $item)
    <li class="col-50  {{$item['id']}}">
        <div class="card m0">
            <div class="card-header p0" onclick="$.href('{{u('Goods/detail',['goodsId'=>$item['id']])}}')">
                <img class="card-cover" src="{{ formatImage($item['images'],320,320,2)}}" alt="">
            </div>
            <div class="card-content">
                <div class="card-content-inner">
                    <div class="list-block media-list m0">
                        <ul>
                            <li>
                                <a href="#" class="item-link item-content pl0">
                                    <div class="item-inner">
                                        <div class="item-subtitle f13" onclick="$.href('{{u('Goods/detail',['goodsId'=>$item['id']])}}')">{{$item['name']}}</div>
                                        <div class="item-title-row">
                                            <div class="item-title c-red">￥
                                                <span class="f18">
                                                     @if($item['salePrice'])
                                                        {{$item['salePrice']}}
                                                         @else
                                                        {{$item['price']}}
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="item-after c-gray f12">{{$item['salesVolume']}}人付款</div>
                                        </div>
                                        {{--<div class="item-title-row f12  c-bgpale y-m-10">--}}
                                            {{--@if($item['store_type'] ==1)--}}
                                            {{--<div class="item-title c-gray">推销回报￥<span>{{$item['isAllUserPrimary'] or 0.00}}</span></div>--}}
                                             {{--@else--}}
                                                {{--<div class="item-title c-gray"><del><span>--}}
                                                {{--@if($item['salePrice'])--}}
                                                    {{--￥{{$item['price']}}--}}
                                                {{--@endif--}}
                                                 {{--</span></del></div>--}}
                                            {{--@endif--}}
                                            {{--<div class="item-after c-gray"><span class="share_num{{$item['id']}}">{{$item['share_num'] or $item['shareNum']}}</span>人分享</div>--}}
                                        {{--</div>--}}
                                        {{--<div class="item-title-row f12  c-bgpale y-m-10">--}}
                                            {{--<div class="item-title c-orange y-width50">@if($item['store_type'] == 0)周边店@else全国店@endif<img src="{{asset('images/y15.png')}}" class="va-1 ml2" width="12"></div>--}}
                                            {{--<div class="item-after c-gray">来自:@if($item['stype']==1) {{$item['name2']}} @else {{$item['province']['name']}}{{$item['city']['name']}} @endif</div>--}}
                                        {{--</div>--}}
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @if($item['store_type'] == 1 || $item['storeType'] == 1)
        <div class="y-txzq f12 share_js" data-js_share_id="{{$item['id']}}">
            <i class="icon iconfont va-1 f14">&#xe616;</i>
            {{--推销赚钱--}}
            <?php
                $datas["sellerId"] = $item['seller_id']?$item['seller_id']:$item['sellerId'];
                $datas["name"] = $item['name'];
                $datas["id"] = $item['id'];
                $datas["brief"] = $item['brief'];
                $datas["image"] = $item['images'];
                $datas["images"] = $item['image'];
            ?>
            <div class="show_js_share none " data-val="{{ json_encode($datas) }}"></div>
        </div>
        @endif
    </li>
@endforeach