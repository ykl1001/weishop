@foreach ($data as $items)
    @foreach($items['goods'] as $item)
        <?php
        $url = u('Goods/detail',['goodsId'=>$item['id']]);
        ?>
        <li class="col-50">
            <div class="card m0" onclick="$.href('{{ $url}}')">
                <div class="card-header p0">
                    <img class="card-cover" src="{{formatImage($item['image'],200,200)}}" alt="">
                </div>
                <div class="card-content">
                    <div class="card-content-inner">
                        <div class="list-block media-list m0">
                            <ul  class='pl0'>
                                <li>
                                    <a href="#" class="item-link item-content pl0">
                                        <div class="item-inner">
                                            <div class="item-subtitle f13">{{$item['name']}}</div>
                                            <div class="item-title-row c-bgpale">
                                                <div class="item-title c-red">￥
                                                    <span class="f18">
                                                        @if($item['salePrice'])
                                                            {{$item['salePrice']}}
                                                        @else
                                                            {{$item['price']}}
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="item-after c-gray f12">{{$item['extend']['salesVolume']}}人付款</div>
                                            </div>
                                            <div class="item-title-row f12  c-bgpale y-m-10">
                                                <div class="item-title c-gray">
                                                    @if($item['storeType'])
                                                        返利￥<span>{{$item['isAllUserPrimary']}}</span>
                                                        @else
                                                        <del>
                                                            <span>
                                                                @if($item['salePrice'])
                                                                    ￥{{$item['price']}}
                                                                @endif
                                                             </span>
                                                        </del>
                                                    @endif
                                                </div>
                                                <div class="item-after c-gray">{{$item['extend']['shareNum']}}人分享</div>
                                            </div>
                                            <div class="item-title-row f12  c-bgpale y-m-10">
                                                @if($item['storeType'])
                                                    <div class="item-title c-orange y-width50">
                                                        全国店
                                                    </div>
                                                    <div class="item-after c-gray">来自:{{ $item['province']['name'].$item['city']['name'] }}</div>
                                                @else
                                                    <div class="item-title c-orange y-width50">
                                                        周边店
                                                    </div>
                                                @endif
                                                {{--<img src="images/y15.png" class="va-1 ml2" width="12">--}}
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @if($item['storeType'])
                <div class="y-txzq f12 share_js" data-js_share_id="{{$item['id']}}">
                    <i class="icon iconfont va-1 f14">&#xe616;</i>
                    推销赚钱
                    <?php
                    $datas["sellerId"] = $item['sellerId'];
                    $datas["name"] = $item['name'];
                    $datas["id"] = $item['id'];
                    $datas["brief"] = $item['brief'];
                    $datas["image"] = $item['images'];
                    $datas["images"] = $item['image'];
                    ?>
                    <div class="show_js_share none" data-val="{{ json_encode($datas) }}"></div>
                </div>
            @endif
        </li>
    @endforeach
@endforeach