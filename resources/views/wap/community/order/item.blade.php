@foreach($list['orderList'] as $v)
    <div class="card y-ddcard" id="list_item{{ $v['id'] }}">
        <div class="card-header">
            <div class="y-ordertitle" @if($v['sellerId'] > 0) onclick="$.herf('{{ u('Goods/index',['id'=>$v['sellerId'],'type'=>$v['orderType'],'source'=>'order']) }}')" @else onclick="$.toast('商家已关闭或不存在')" @endif>
                <i class="icon iconfont vat mr5">&#xe632;</i>
                <span class="y-ddmaxwidth">{{ $v['shopName'] or '商家已关闭或不存在'}}</span>
                <i class="icon iconfont vat ml5">&#xe602;</i>
            </div>
            <span class="c-red">{{ $v['orderStatusStr']}}</span>
        </div>
        <div class="card-content">
            <div class="list-block media-list y-ddlistblock">
                <ul>
                    <?php $goodsTotal = 0; ?>
                    @foreach($v['orderGoods'] as $item)
                        <li onclick="window.location.href = '{{ u('Order/detail',['id'=>$v['id']]) }}'">
                            <a href="#" class="item-link item-content">
                                <div class="item-media">
                                    <img src="{{formatImage($item['goodsImages'],200,200)}}" width="45.5">
                                </div>
                                <div class="item-inner f12">
                                    <div class="item-title-row">
                                        <div class="item-title">{{$item['goodsName']}}</div>
                                        <div class="item-after">
                                            @if($item['salePrice'] <= 0)
                                                <p>￥{{$item['price']}}</p>
                                            @else
                                                <p>￥{{$item['salePrice']}}</p>
                                            @endif
                                            @if($item['salePrice'] > 0)
                                                <del class="c-gray">￥{{$item['price']}}</del>
                                            @endif
                                        </div>
                                    </div>
                                    @if($item['goodsNorms'])
                                        <div class="item-title-row c-gray">
                                            <div class="item-title">{{str_replace(':','-',$item['goodsNorms'])}}</div>
                                            <div class="item-after c-gray">x{{$item['num']}}</div>
                                        </div>
                                    @else
                                        <div class="item-title-row c-gray">
                                            <div class="item-title"></div>
                                            <div class="item-after c-gray">x{{$item['num']}}</div>
                                        </div>
                                    @endif

                                    <?php $goodsTotal += $item['num']; ?>
                                </div>
                            </a>
                        </li>
                    @endforeach
                    <li class="tr f12 p10">
                        共<span>{{ $goodsTotal ? $goodsTotal : 0}}</span>件商品 合计：￥<span>{{$v['payFee']}}</span>(含运费￥{{$v['freight'] > 0 ? $v['freight'] : '0.00'}})
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-footer" id="footer-{{ $v['id'] }}">
            <span class="y-ddlistbtn fr">
                @if($v['isCanDelete'])
                    <a href="#" class="okorder" data-id="{{$v['id']}}" >删除</a><!-- 红色按钮 -->
                @endif
                @if($v['isCanRate'])
                    @if($v['storeType'] == 1)
                        <a href="javascript:$.herf('{{ u('Order/logistics',['id'=>$v['id']]) }}');" class="y-viewlogistics">查看物流</a>
                    @endif
                    <a href="{{ u('Order/comment',['orderId' => $v['id']]) }}" class="pageloading y-ddlistbtnblue y-blue external" >评价赚积分</a><!-- 蓝色按钮 -->
                @endif
                @if($v['storeType'] == 1)
                    @if($v['isCanCancel'])
                        <a href="#" class="cancelorder" data-id="{{$v['id']}}" data-status="{{ $v['status'] }}" data-contactcancel="{{ (int)$v['isContactCancel'] }}">取消订单</a><!-- 红色按钮 -->
                    @endif
                    @if($v['isNewCanRefund'])
                        <!-- 申请退款移至订单详情，列表页不显示退款按钮 -->
                        <!-- <a href="javascript:$.herf('{{u('Logistics/ckservice',['id'=>$data['id']])}}');" class="y-viewlogistics" external>申请退款</a> -->
                    @endif
                @else
                    @if($v['isCanCancel'] && $v['activityGoodsIsChange'] != -1)
                        <a href="#" class="cancelorder" data-id="{{$v['id']}}" data-status="{{ $v['status'] }}" data-contactcancel="{{ (int)$v['isContactCancel'] }}">取消订单</a><!-- 红色按钮 -->
                    @endif
                @endif
                @if($v['isCanPay'] && $v['activityGoodsIsChange'] == 1)
                    <a href="{{ u('Order/cashierdesk',['orderId'=>$v['id']]) }}" class="y-ddlistbtnblue y-blue" data-no-cache="true" >去支付</a><!-- 蓝色按钮 -->
                @endif
                @if($v['isCanConfirm'])
                    @if($v['storeType'] == 1)
                        <a href="javascript:$.herf('{{ u('Order/logistics',['id'=>$v['id']]) }}');" class="y-viewlogistics">查看物流</a>
                    @endif
                    <a href="#" class="confirmorder" data-id="{{$v['id']}}" >@if($v['isAll'] == 1)确认收货@else确认完成@endif</a><!-- 蓝色按钮 -->
                @endif
            </span>
        </div>
        <script type="text/javascript">
            if( $("#footer-{{ $v['id'] }} span a").length <= 0 )
            {
                $("#footer-{{ $v['id'] }}").addClass("none");
            }
        </script>
    </div>
@endforeach