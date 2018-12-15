@foreach($data as $item)
    <li class="col-50  {{$item['id']}}">
        <div class="card m0">
            <div class="card-header p0" onclick="$.href('{{u('Goods/detail',['goodsId'=>$item['id']])}}')">
                <img class="card-cover" src="{{ formatImage($item['image'],320,320,2)}}" alt="">
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
                                                <del>
                                                    <span>
                                                        @if($item['salePrice'])
                                                            ￥{{$item['price']}}
                                                        @endif
                                                    </span>
                                                </del>
                                            </div>
                                            <div class="item-after rest f12" data-sellerId="{{$item['sellerId']}}" data-goodsId="{{$item['id']}}" data-normsId="{{$item['normsId']}}" data-storeType="{{$item['storeType']}}" data-num="{{$item['num']}}">购买</div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </li>
@endforeach