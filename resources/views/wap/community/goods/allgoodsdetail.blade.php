@extends('wap.community._layouts.base')

@section('show_top')
    <?php
    //存在规格和折扣 获取规格最低价 根据折扣结算出新的特价
    if(count($data['norms']) > 0 && !empty($seller['activity']['special']))
    {
        $f = true;
        foreach ($data['norms'] as $key => $value) {
            $salePrice = $value['price'] * $seller['activity']['special']['sale'] / 10;

            $data['norms'][$key]['salePrice'] = $salePrice;

            if($f)
            {
                $seller['activity']['special']['minNormsPrice'] = $salePrice;
                $data['price'] = $value['price'];
                $f = false;
            }
            elseif(!$f && $salePrice <= $seller['activity']['special']['minNormsPrice'])
            {
                $seller['activity']['special']['minNormsPrice'] = $salePrice; //最低折扣价
                $data['price'] = $value['price']; //最低原价
            }

        }
    }

    if(Input::get('backindex') == 1)
    {
        $nav_back_url = u('Index/index');

    }

    $goodsId = input::get('goodsId');
    ?>

    <header class="bar bar-nav y-qgdxqymnav y-qgdcommdetailnav">
        <a class="button button-link button-nav pull-left" href="javascript:$.href('@if(!empty($nav_back_url)) {{$nav_back_url}} @else {{ u('Goods/index',['id'=>$data['sellerId'],'type'=>1,'urltype'=>1]) }} @endif')" data-transition='slide-out' external>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <a class="button button-link button-nav pull-right" data-transition='slide-out' external>
            <span class="icon iconfont" onclick="$.href('{{ u('GoodsCart/index')}}')">&#xe673;</span>
            <span class="icon iconfont ml10" onclick="$.href('{{ u('Index/index') }}')">&#xe66e;</span>
        </a>
    </header>
    <nav class="bar bar-tab y-xqynav">
        <a class="tab-item y-afterbtmbor" href="tel://{{$wap_service_tel}}" external>
            <span class="icon icon-home iconfont f18">&#xe67d;</span>
            <span class="tab-label">客服</span>
        </a>
        <a class="tab-item y-afterbtmbor collect" href="#" external>
            <span class="icon icon-cart iconfont f18">&#xe651;</span><!-- &#xe652; -->
            <span class="tab-label">收藏</span>
        </a>
        <a class="tab-item c-white c-bgff9000 y-xqybtn y-addshoppingcart_old @if($option['shareUserId'])none @endif" onclick="$.showGoodsSkus({{$data['id']}}, 'cart');" external>
            <span class="tab-label">加入购物车</span>
        </a>

        <a class="tab-item c-white c-bg y-xqybtn y-choicegg" external>
            <span class="tab-label" onclick="$.showGoodsSkus({{$data['id']}}, 'buy');">立即购买</span>
        </a>
    </nav>
@stop

@section('content')
    <script type="text/javascript">
        BACK_URL = "{!! Request::server('HTTP_REFERER') !!}";
    </script>
    <div class="content" id=''>
        <div class="swiper-container commAdvSwiper" data-space-between='0'>
            <div class="swiper-wrapper">
                @foreach($data['images'] as $key => $value)
                    <div class="swiper-slide pageloading">
                        <img _src="{{ formatImage($value,640) }}" src="{{ formatImage($value,640) }}" />
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination swiper-pagination-comm"></div>
        </div>
        <div class="list-block media-list">
            <ul>
                <li>
                    <a href="#" class="item-link item-content" external>
                        <div class="item-inner pr10">
                            <div class="item-text c-black f16 lh20 mb5">{{ $data['name'] }}</div>
                            <div class="item-title-row y-qgdtxfl">
                                @if(empty($seller['activity']['special']))
                                    <div class="item-title f14 c-red">
                                        ￥<span class="f24">{{ $data['price'] }}</span>
                                    </div>
                                @else
                                    @if(empty($seller['activity']['special']['minNormsPrice']))
                                        <div class="item-title f14 c-red">
                                            @if(empty($data['norms']))
                                                ￥<span class="f24">{{ number_format($data['price'] * $seller['activity']['special']['sale'] / 10,2) }}</span>
                                            @else
                                                ￥<span class="f24">{{ number_format($data['norms'][0]['price'] * $seller['activity']['special']['sale'] / 10,2) }}</span>
                                            @endif
                                            @if($seller['activity']['special']['sale'] > 0)
                                                <span class="c-red f12 ml5">{{ $seller['activity']['special']['sale']}}折特价</span>
                                            @endif
                                            <div><del class="c-gray f12">￥{{ $data['price'] }}</del></div>
                                        </div>
                                    @else
                                        <div class="item-title f14 c-red">
                                            ￥<span class="f24">{{ number_format($seller['activity']['special']['minNormsPrice'], 2) }}</span>
                                            @if($seller['activity']['special']['sale'] > 0)
                                                <span class="c-red f12 ml5">{{ $seller['activity']['special']['sale']}}折特价</span>
                                            @endif
                                            <div><del class="c-gray f12">￥{{ $data['price'] }}</del></div>
                                        </div>
                                    @endif
                                @endif
                                <!--<div class="item-after y-twoh44 share mr5">
                                    <i class="icon iconfont va-2 mr5 f20"></i>
                                    <p>分享</p>
                                </div>-->
                                <div class="item-after share">
                                    <p class="c-black f13">
                                        <i class="icon iconfont c-red va-2 mr5">&#xe616;</i>
                                        推销返利
                                    </p>
                                    <p class="c-red f12">推销回报:￥
									@if(empty($seller['activity']['special']))
										{{ number_format($data['isAllUserPrimary'] * $data['price'],2) }}
									@else
										@if(empty($seller['activity']['special']['minNormsPrice']))
												@if(empty($data['norms']))
													{{ $data['isAllUserPrimary'] * number_format($data['price'] * $seller['activity']['special']['sale'] / 10,2) }}
												@else
													{{ $data['isAllUserPrimary'] * number_format($data['norms'][0]['price'] * $seller['activity']['special']['sale'] / 10,2) }}
												@endif
										@else
                                                {{ $data['isAllUserPrimary'] * number_format($seller['activity']['special']['minNormsPrice'], 2) }}
										@endif
									@endif

									元</p>
                                </div>
                            </div>

                        </div>
                    </a>
                </li>
                <li>
                    <a href="#" class="item-link item-content" external>
                        <div class="item-inner pr10">
                            <div class="item-title-row f12 c-gray">
                                <div>销售<span>{{ $data['extend']['salesVolume'] }}</span>笔</div><!-- 数量超过万，用万为单位显示 -->
                                <div>来自:<span>{{ $data['seller']['province']['name'] }}-{{ $data['seller']['city']['name'] }}</span></div><!-- 只显示两级 -->
                                 <div><span>{{ $data['extend']['shareNum'] }}</span>人分享</div>
                            </div>
                        </div>
                    </a>
                    {{--<div class="c-bgfff y-tag c-bgpale pl10">--}}
                        {{--<div class="c-orange f12">@if($data['seller']['storeType'] == 0)周边店@else全国店@endif<img src="{{ asset('wap/community/newclient/images/y15.png')}}" class="va-1 ml5" width="12"></div>--}}
                        {{--@foreach($data['sellerAuthIcon'] as $val)--}}
                            {{--<div class="c-orange f12">{{ $val['icon']['name'] }}<img src="{{ $val['icon']['icon'] }}" class="va-1 ml5" width="12"></div>--}}
                        {{--@endforeach--}}
                    {{--</div>--}}
                </li>
            </ul>
        </div>

        @if(!empty($seller['activity']['full']) || !empty($seller['activity']['special']) || !empty($seller['activity']['new']))
            <div class="list-block media-list y-nocenter">
                <ul>
                    <li class="item-content">
                        <div class="item-media f14 c-gray">促销</div>
                        <div class="item-inner">
                            <?php $first = true; ?>
                            @if(!empty($seller['activity']['full']))
                                <div class="item-title f14">
                                    <img src="{{ asset('wap/community/newclient/images/ico/jian.png') }}" width="16" class="icon iconfont c-gray va-3 mr5">
                                <span>
                                    在线支付
                                    @foreach($seller['activity']['full'] as $key => $value)
                                        @if($first)
                                            <?php $first = false; ?>
                                            满{{$value['fullMoney']}}减{{$value['cutMoney']}}元
                                        @else
                                            ,满{{$value['fullMoney']}}减{{$value['cutMoney']}}元
                                        @endif
                                    @endforeach
                                </span>
                                </div>
                            @endif
                            @if(count($seller['activity']['special']) > 0)
                                <div class="item-title f14">
                                    <img src="{{ asset('wap/community/newclient/images/ico/tei.png') }}" width="16" class="icon iconfont c-gray va-3 mr5">
                                    <span>商家特价优惠</span>
                                </div>
                            @endif
                            @if(!empty($seller['activity']['new']))
                                <div class="item-title f14">
                                    <img src="{{ asset('wap/community/newclient/images/ico/xin.png') }}" width="16" class="icon iconfont c-gray va-3 mr5">
                                    <span>新用户在线支付立减{{$seller['activity']['new']['cutMoney']}}元</span>
                                </div>
                            @endif
                        </div>
                        <i class="icon iconfont c-gray f13 mt5 mr10 y-unfold none y-i1">&#xe602;</i>
                        <i class="icon iconfont c-gray f13 mt5 mr10 y-unfold none">&#xe601;</i>
                    </li>
                </ul>
            </div>
        @endif
        <input type="text" value="@if($data['num']){{max(intval($data['num']),1)}}@else 0 @endif" readonly="readonly" id="goodsval" class="val tc pl0 none" />
        <input type="hidden" class="goods_stock" value="{{$data['stock']}}" />

        <div class="list-block f14 nobor">
            <ul>
                <li>
                    <a href="#" onclick="$.href('{{ u('Seller/detail',['id'=>$data['sellerId']]) }}')" class="item-link item-content">
                        <div class="item-inner pr10">
                            <div class="item-title">
                                <i class="icon iconfont c-gray vat mr5">&#xe632;</i><span>{{ $data['seller']['name'] }}</span>
                            </div>
                            <i class="icon iconfont c-gray f13 mr-2">&#xe602;</i>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{ u('Goods/commentall',['id'=>$goodsId]) }}" class="item-link item-content">
                        <div class="item-inner pr10">
                            <div class="item-title">
                                <i class="icon iconfont c-gray vat mr5">&#xe687;</i><span>查看商品评价</span>
                            </div>
                            <i class="icon iconfont c-gray f13 mr-2">&#xe602;</i>
                        </div>
                    </a>
                </li>
            </ul>
            <!-- 回到顶部 -->
            <a href="javascript:$('.content').scrollTop(0)" class="y-backtop none"></a>
        </div>


        <div class="y-splitlinebox">
            <div class="y-splitlinemain c-black f12"><span>继续向上滑动查看图文详情</span></div>
        </div>
        <div class="c-bgfff pb10 y-img">
            <p class="p10">{!!$data['brief']!!}</p>
        </div>
    </div>
    @include('wap.community.goods.share')
    @include('wap.community._layouts.base_stock')
@stop

@section($js)
    <script type="text/javascript">
        BACK_URL = "{!! Request::server('HTTP_REFERER') !!}";
    </script>
    <script type="text/javascript">
        $(function() {
            //回到顶部
            $(".content").scroll(function(){
                var windowheight =  $(window).height();
                var topheight = $(".content").scrollTop();
                if (topheight > windowheight) {
                    $(".y-backtop").removeClass("none");
                }else{
                    $(".y-backtop").addClass("none");
                }
            })

            $(".commAdvSwiper").swiper({"pagination": ".swiper-pagination-comm"});
            $(".content").css("top", 0);

            var cartIds = "{{$cartIds}}";
            var goodsNorms = "<?php echo !empty($data['norms']) ? 1 : 0; ?>";
            $.showItemNorms_item = function(id){
                $(".show_item_norms_item .msg_show").addClass("none");
                $(".show_item_norms_item .y-ggpsize2 span").removeClass('active');
                $(".show_item_id_"+id +"_norms").addClass('active');
                $(".add_norms").attr("data-id",id);
                $(".subtract_norms").attr("data-id",id);
                var val  = $(".show_item_id_"+id +"_norms").attr('data-info');
                var prs  = $(".show_item_id_"+id +"_norms").attr('data-prs');
                var stock  = $(".show_item_id_"+id +"_norms").attr('data-stock');
                var salePrice  = $(".show_item_id_"+id +"_norms").attr('data-salePrice');

                $(".show_item_norms_item .subtract_norms").attr('data-salePrice',salePrice);
                $(".show_item_norms_item .add_norms").attr('data-salePrice',salePrice);
                $("#normsval_span").html(val);
                $("#normsval").val(val);
                $(".add_norms").attr("data-price",prs);
                $(".subtract_norms").attr("data-salePrice",salePrice);

                if(salePrice > 0){
                    $(".show_item_norms_item .money_toal").html(salePrice);
                    $(".show_item_norms_item .delPrice").html('￥'+prs);
                    $(".show_item_norms_item .goods_stock").html(stock);
                }else{
                    $(".show_item_norms_item .money_toal").html(prs);
                    $(".show_item_norms_item .goods_stock").html(stock);
                }
            }

            //滑动显示头部
            var start = 0,move = 0,opacity = 1,opacity2 = 0;
            $(document).off('touchstart',".content");
            $(document).on('touchstart',".content", function (e) {
                var point = e.touches ? e.touches[0] : e;
                start = point.screenY;
            });
            var scrobox = '';
            $(document).off('touchmove',".content");
            $(document).on('touchmove',".content", function (e) {
                var point = e.touches ? e.touches[0] : e;
                move = point.screenY;
                var s = move - start;
                $(".content").scroll(function(){
                    scrobox = $(this).scrollTop();
                });
                if(s < 0 && scrobox >20){
                    if (opacity <= 0.1) {
                        opacity2 += 0.3;
                        if (opacity2 > 1) { opacity2 = 1; }
                        $(".y-qgdxqymnav").removeClass("y-qgdcommdetailnav").css("opacity",opacity2);
                    }else{
                        opacity -= 0.3;
                        if (opacity < 0) { opacity = 0; }
                        $(".y-qgdxqymnav").addClass("y-qgdcommdetailnav").css("opacity",opacity);
                    }
                }else{
                    if (opacity2 <= 0.1) {
                        opacity += 0.3;
                        if (opacity > 1) { opacity = 1; }
                        $(".y-qgdxqymnav").addClass("y-qgdcommdetailnav").css("opacity",opacity);
                    }else{
                        opacity2 -= 0.3;
                        if (opacity2 < 0) { opacity2 = 0; }
                        $(".y-qgdxqymnav").removeClass("y-qgdcommdetailnav").css("opacity",opacity2);
                    }
                }
            });

            //收藏
            $(document).on("touchend",".collect",function(){
                var obj = new Object();
                var collect = $(this);
                obj.id = "{{$data['id']}}";
                obj.type = 1;

                if(collect.hasClass("c-red")){
                    $.post("{{u('UserCenter/delcollect')}}",obj,function(result){
                        if(result.code == 0){
                            $.toast("已取消收藏～");

                            collect.removeClass("c-red");
                            collect.html('<span class="icon icon-cart iconfont f18">&#xe651;</span><span class="tab-label">收藏</span>');
                        } else if(result.code == 99996){
                            $.router.load("{{u('User/login')}}", true);
                        } else {
                            $.alert(result.msg);
                        }
                    },'json');
                }else{
                    $.post("{{u('UserCenter/addcollect')}}",obj,function(result){
                        if(result.code == 0){
                            $.toast("收藏成功，可以在我的收藏找到TA啦～");
                            collect.addClass("c-red");
                            collect.html('<span class="icon icon-cart iconfont f18">&#xe652;</span><span class="tab-label">已收藏</span>');
                        } else if(result.code == 99996){
                            $.router.load("{{u('User/login')}}", true);
                        } else {
                            $.alert(result.msg);
                        }
                    },'json');
                }
            });
            // 关闭规格弹框
            $(document).on("touchend",".x-closebg .x-closeico",function(){
                $(this).parents(".f-bgtk").addClass('none');
            });
            // 规格选择
            $(document).on("touchend",".x-psize span",function(){
                $(this).addClass("c-bg").siblings().removeClass("c-bg");
            });
        })
    </script>
@stop