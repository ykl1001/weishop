<div class="list-block media-list y-notwobor">
    <ul>
        <li class="item-content">
            <div class="item-media"><i class="icon iconfont f30 f_999">&#xe604;</i></div>
            <div class="item-inner pr10">
                <div class="item-title-row mt10">
                    <div class="item-after f16">{{$data['orderNewStatusStr']['title']}}</div>
                    @if($data['orderNewStatusStr']['time'])<div class="item-title f16">{{Time::toDate($data['orderNewStatusStr']['time'],"Y-m-d H:i")}}</div>@endif
                </div>
            </div>
        </li>
    </ul>
</div>
<div class="list-block">
    <ul>
        <li class="item-content p0">
            <div class="item-inner pl10">
                <div class="f14 w100">
                    <span class="f_l">收货地址：</span>
                    <div class="y-xddxqcont f_999">
                        <p><span class="mr10">{{$data['name']}}</span><span>{{$data['mobile']}}</span></p>
                        <p>{{$data['province'] . $data['city'] . $data['area'] . $data['address']}}</p>
                    </div>
                </div>
            </div>
        </li>
    </ul>
</div>
<div class="card y-card">
    <div class="card-content">
        <div class="list-block media-list f14 y-xddxqlist">
            <ul>
                @foreach($data['orderGoods'] as $v)
                    <li>
                        <div class="item-content">
                            <div class="item-media"><a href=""><img src="{{ formatImage($v['goodsImages'],100,100) }}" width="45.5"></a></div>
                            <div class="item-inner f12">
                                <div class="item-title-row">
                                    <a href="#">
                                        <div class="item-title">{{$v['goodsName']}}</div>
                                    </a>
                                    <div class="item-after">
                                        @if($v['salePrice'] <= 0)
                                            <p>￥{{sprintf("%.2f",$v['price'])}}</p>
                                        @else
                                            <p>￥{{sprintf("%.2f",$v['salePrice'])}}</p>
                                        @endif
                                        @if($v['salePrice'] > 0)
                                            <del class="f_999">￥{{sprintf("%.2f",$v['price'])}}</del>
                                        @endif
                                    </div>
                                </div>
                                @if($v['goodsNorms'])
                                    <div class="item-title-row f_999">
                                        <div class="item-title">{{str_replace(':','-',$v['goodsNorms']['skuName'])}}</div>
                                        <div class="item-after f_999">x{{$v['num']}}</div>
                                    </div>
                                @else
                                    <div class="item-title-row c-gray">
                                        <div class="item-title"></div>
                                        <div class="item-after c-gray">x{{$v['num']}}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
                <li class="item-content">
                    <div class="item-inner pr10">
                        <div class="item-title-row">
                            <div class="item-title">配送费</div>
                            <div class="item-after">￥{{sprintf("%.2f",$data['freight'])}}</div>
                        </div>
                    </div>
                </li>
                <li class="item-content udb_dsy_item_li">
                    <div class="item-inner pr10">
                        @if($data['discountFee'] > 0)
                            <div class="item-title-row pb5">
                                <div class="item-title">优惠券</div>
                                <div class="item-after">-￥{{ number_format($data['discountFee'], 2) }}</div>
                            </div>
                        @endif
                        @if($data['integralFee'] > 0)
                            <div class="item-title-row pb5">
                                <div class="item-title">积分抵扣</div>
                                <div class="item-after">-￥{{ $data['integralFee'] ? number_format($data['integralFee'], 2) : '0.00'}}</div>
                            </div>
                        @endif
                        @if($data['activityNewMoney'] > 0)
                            <div class="item-title-row pb5">
                                <div class="item-title">首单优惠</div>
                                <div class="item-after">-￥{{ number_format($data['activityNewMoney'], 2) }}</div>
                            </div>
                        @endif
                        @if($data['activityFullMoney'] > 0)
                            <div class="item-title-row pb5">
                                <div class="item-title">满减优惠</div>
                                <div class="item-after">-￥{{ number_format($data['activityFullMoney'], 2) }}</div>
                            </div>
                        @endif
                        @if($data['activityGoodsMoney'] > 0)
                            <div class="item-title-row pb5">
                                <div class="item-title">特价优惠</div>
                                <div class="item-after">-￥{{ number_format($data['activityGoodsMoney'], 2) }}</div>
                            </div>
                        @endif
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner pr10">
                        <div class="item-title-row">
                            <div class="item-title"></div>
                            <div class="item-after f12">共<span>{{$data['count']}}</span>件商品 合计：￥<span>{{sprintf("%.2f",$data['payFee'])}}</span></div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="list-block">
    <ul>
        <li class="item-content p0">
            <div class="item-inner pr10 pl10">
                <div class="f14 w100">
                    <span class="f_l">订单号：</span>
                    <div class="y-xddxqcont f_999">{{$data['sn']}}</div>
                </div>
            </div>
        </li>
        @if($data['buyRemark'])
            <li class="item-content p0">
                <div class="item-inner pr10 pl10">
                    <div class="f14 w100">
                        <span class="f_l">备注信息：</span>
                        <div class="y-xddxqcont f_999">{{$data['buyRemark']}}</div>
                    </div>
                </div>
            </li>
        @endif
        <li class="item-content p0">
            <div class="item-inner pr10 pl10">
                <div class="f14 w100">
                    <span class="f_l">支付方式：</span>
                    <div class="y-xddxqcont f_999">{{$data['payType']}}</div>
                </div>
            </div>
        </li>
        <li class="item-content p0">
            <div class="item-inner pr10 pl10">
                <div class="f14 w100">
                    <span class="f_l">下单时间：</span>
                    <div class="y-xddxqcont f_999">{{$data['createTime']}}</div>
                </div>
            </div>
        </li>
    </ul>
</div>
<script type="text/javascript">
    $(function() {
        if($(".udb_dsy_item_li .item-title-row").length <= 0){
            $(".udb_dsy_item_li").remove();
        }
    });
</script>