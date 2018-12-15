@foreach($list as $item)
    @if($item[0])
        <div class="card y-shopcart mt0 mb10">
            <div class="card-header" onclick="$.href('{{u('Seller/detail',['id'=>$item[0]['sellerId']])}}')">
                <span class="c-gray f14"><i class="icon iconfont mr5 c-gray f14 vat">&#xe632;</i>{{$item[0]['sellerName'] ? $item[0]['sellerName'] : '店铺已关闭'}}</span>
                <i class="icon iconfont c-gray f14">&#xe602;</i>
            </div>
            <div class="card-content">
                <div class="list-block media-list mb10 y-sylist">
                    <ul>
                        @foreach($item as $k => $v)
                        <li class="each c-bgfff">
                            <a href="#" class="item-link item-content y-flex-start">
                                <div class="item-media"><img src="{{$v['goods']['image']}}" width="73"></div>
                                <div class="item-inner">
                                    <div class="item-title-row">
                                        <div class="item-title f16">{{$v['goods']['name']}}</div>
                                    </div>
                                    <div class="item-title-row c-gray">
                                        <div class="item-title">
                                            <div class="y-starcont">
                                                <div class="c-gray4 y-star">
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                </div>
                                                <div class="c-red f12 y-startwo" style="width:{{$v['star'] * 20}}%;">
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item-after f12 c-gray">{{$v['createTime']}}</div>
                                    </div>
                                    <div class="item-text f12 y-noellipsis">{{$v['content']}}</div>
                                     <div class="y-evaluateico mt10 clearfix">
                                        @foreach($v['images'] as $key => $value)
                                            <div class="pb-standalone img{{$v['id']}}" data-ids="{{$v['id']}}" _src="{{$value}}"><img src="{{ formatImage($value,80,80) }}"></div>
                                        @endforeach
                                    </div>
                                    @if($v['replyTime'])
                                        <div class="y-sjreply f12 mt5">
                                            商家回复：{{$v['reply']}}
                                        </div>
                                    @endif
                                </div>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @else
        <div class="card y-shopcart mt0 mb10">
            <div class="card-header" onclick="$.href('{{u('Seller/detail',['id'=>$item['sellerId']])}}')">
                <span class="c-gray f14"><i class="icon iconfont mr5 c-gray f14 vat">&#xe632;</i>{{$item['sellerName'] ? $item['sellerName'] : '店铺已关闭'}}</span>
                <i class="icon iconfont c-gray f14">&#xe602;</i>
            </div>
            <div class="card-content">
                <div class="list-block media-list">
                    <ul class="y-wd">
                        <li>
                            <a href="#" class="item-link item-content">
                                <div class="item-inner">
                                    <div class="item-title-row">
                                        <div class="item-title">
                                            <div class="y-starcont">
                                                <div class="c-gray4 y-star">
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                </div>
                                                <div class="c-red f12 y-startwo" style="width:{{$item['star'] * 20}}%;">
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item-after f12 c-gray">{{$item['createTime']}}</div>
                                    </div>
                                    <div class="item-title-row mt5">
                                        <div class="f14 c-gray5 w100">
                                            <span>{{$item['content']}}</span>
                                            <ul class="y-pjimg clearfix">
                                                @foreach($item['images'] as $v)
                                                <li class="pb-standalone img{{$item['id']}}" data-ids="{{$item['id']}}" _src="{{$v}}"><img src="{{ formatImage($v,80,80) }}"></li>
                                                @endforeach
                                            </ul>
                                            @if($item['replyTime'])
                                                <div class="y-sjhf mt10 c-gray5 f12">
                                                    <p class="mb5 c-gray3">商家回复</p>
                                                    <p>{{$item['reply']}}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <?php $orderId = $item['orderId']; ?>
@endforeach