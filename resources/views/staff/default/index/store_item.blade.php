@foreach($list['orders'] as $v)
    <div class="card y-ddcard z-gray">
        <div class="card-header">
            <div class="f_333 f12 max30 y-textoverflow">{{$v['userName']}}</div>
            <div class="y-textoverflow">
                <span class="f_999 y-ordernumber">
                    订单号:<span>{{ substr($v['sn'], 8) }}</span>
                </span>
                <span class="f_red">{{$v['newOrderStatusStr']}}</span>
            </div>
        </div>
        <div class="card-content" onclick="JumpURL('{{u('Index/detail',['id'=>$v['id']])}}','#order_detail_view',2)">
            <div class="list-block media-list y-ddlistblock" >
                <ul>
                    @foreach($v['goods'] as $g)
                        <li>
                            <a href="#" class="item-link item-content">
                                <div class="item-media">
                                    <img src="{{ formatImage($g['goodsImages'],100,100) }}" width="45.5">
                                </div>
                                <div class="item-inner f12">
                                    <div class="item-title-row">
                                        <div class="item-title">{{$g['goodsName']}}</div>
                                        <div class="item-after">
                                            @if($g['salePrice'] <= 0)
                                                <p>￥{{sprintf("%.2f",$g['price'])}}</p>
                                            @else
                                                <p>￥{{sprintf("%.2f",$g['salePrice'])}}</p>
                                            @endif
                                            @if($g['salePrice'] > 0)
                                                <del class="c-gray">￥{{sprintf("%.2f",$g['price'])}}</del>
                                            @endif
                                        </div>
                                    </div>
                                    @if($g['goodsNorms'])
                                        <div class="item-title-row c-gray">
                                            <div class="item-title">{{str_replace(':','-',$g['goodsNorms']['skuName'])}}</div>
                                            <div class="item-after c-gray">x{{$g['num']}}</div>
                                        </div>
                                    @else
                                        <div class="item-title-row c-gray">
                                            <div class="item-title"></div>
                                            <div class="item-after c-gray">x{{$g['num']}}</div>
                                        </div>
                                    @endif
                                </div>
                            </a>
                        </li>
                    @endforeach
                    <li class="tr f12 p10">
                        共<span>{{$v['num']}}</span>件商品 实收款：￥<span>{{sprintf("%.2f",$v['payFee'])}}</span>(含运费￥{{sprintf("%.2f",$v['freight'])}})
                    </li>
                </ul>
            </div>
        </div>
        @if($v['isPay'] || $v['isCanAccept'] || $v['isLogistics'])
        <div class="card-footer">
            <span class="y-ddlistbtn fr">
                @if($v['isPay'])
                    <a href="#" class="y-viewlogistics" onclick="$.isCanCancel({{$v['id']}},1 )">关闭订单</a>
                @endif
                @if($v['isCanAccept'])
                    <a href="#" onclick="JumpURL('{{u('Order/deliver',['id'=>$v['id']])}}','#order_deliver_view',2)">发货</a>
                @endif
                @if($v['isLogistics'])
                    <a href="#" onclick="JumpURL('{{u('Order/logistics',['id'=>$v['id']])}}','#order_logistics_view',2)" class="y-viewlogistics" external>查看物流</a>
                @endif
            </span>
        </div>
        @endif
    </div>
@endforeach