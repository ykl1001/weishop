@foreach($data as $item)
    <li class="col-50">
        <div class="card m0">
            <div class="card-header p0" onclick="$.href('{{u('Goods/detail',['goodsId'=>$item['goods_id']])}}')">
                <img class="card-cover" src="{{ formatImage($item['images'],320,320,2)}}" alt="">
            </div>
            <div class="card-content">
                <div class="card-content-inner">
                    <div class="list-block media-list m0">
                        <ul>
                            <li>
                                <a href="#" class="item-link item-content pl0">
                                    <div class="item-inner">
                                        <div class="item-subtitle f13" onclick="$.href('{{u('Goods/detail',['goodsId'=>$item['goods_id']])}}')">{{$item['goods_name']}}</div>
                                        <div class="item-title-row">
                                            <div class="item-title c-red">￥<span class="f18">{{$item['price']}}</span></div>
                                            <div class="item-after c-gray f12">{{$item['sales_volume']}}人付款</div>
                                        </div>
                                        {{--<div class="item-title-row f12  c-bgpale y-m-10">--}}
                                        {{--<div class="item-title c-gray">返利￥<span>2.69</span></div>--}}
                                        {{--<div class="item-after c-gray">201人分享</div>--}}
                                        {{--</div>--}}
                                        <div class="item-title-row f12  c-bgpale y-m-10">
                                            <div class="item-title c-orange y-width50">@if($item['storeType'] == 0)周边店@else全国店@endif<img src="{{asset('images/y15.png')}}" class="va-1 ml2" width="12"></div>
                                            <div class="item-after c-gray">来自:{{$item['province']['name']}}{{$item['city']['name']}}</div>
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