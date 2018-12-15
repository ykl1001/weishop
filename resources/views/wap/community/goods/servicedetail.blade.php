@extends('wap.community._layouts.base')

@section('css')
    <style type="text/css">
        .y-nocenter.list-block .item-content .item-inner{min-height: 2rem;}
        .swiper-container-horizontal > .swiper-pagination{bottom: 5px;}
    </style>
@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="javascript:$.href('@if(!empty($nav_back_url)) {{$nav_back_url}} @else {{ u('Goods/index',['id'=>$data['sellerId'],'type'=>2,'urltype'=>1]) }} @endif')" data-transition='slide-out'>            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">服务详情</h1>
        <a class="button button-link button-nav pull-right open-popup collect_it collect_opration @if($data['iscollect']) on @endif" data-id="{{$data['id']}}" data-popup=".popup-about">
            <!-- 分享 -->
            <i class="icon share iconfont c-black">&#xe616;</i>
            @if($data['iscollect'] == 1)
                <i class="icon collect iconfont c-red m0">&#xe654;</i><!-- 已收藏图片  -->
            @else
                <i class="icon collect iconfont c-black m0">&#xe653;</i><!-- 未收藏图标 -->
            @endif
        </a>
    </header>
@stop

@section('content')
    @if($seller['serviceTimesCount'] > 0)
        @include('wap.community._layouts.base_cart_item')
    @endif

    <div class="content" id=''>
        <div class="x-bigpic pr">
            <div id="indexAdvSwiper" class="swiper-container my-swiper" data-space-between='0' style="max-height: 350px;">
                <div class="swiper-wrapper">
                    @foreach($data['images'] as $key => $value)
                        <div class="swiper-slide pageloading">
                            <img _src="{{ formatImage($value,640) }}" src="{{ formatImage($value,640) }}" />
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination swiper-pagination-adv"></div>
            </div>
        </div>
        <!-- 选择数量 -->
        <div class="list-block x-goods m0 nobor">
            <ul>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title f14">{{$data['name']}}</div>
                        <div class="item-after fr">
                            @if($seller['serviceTimesCount'] > 0)
                                <i class="icon iconfont c-gray subtract">&#xe621;</i>
                                <input type="text" value="{{ max(intval($cart['goods'][$option['goodsId']]['num']),1)}}" id="goodsval{{$data['id']}}" readonly="readonly" class="val tc pl0 count" />
                                <i class="icon iconfont c-red add">&#xe61e;</i>
                            @else
                                商家休息中
                            @endif
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">
                            <!-- <span class="c-red f18 mr10">￥{{$data['price']}}</span> -->
                            <!-- <span class="c-gray f14">{{$data['seller']['name']}}</span> -->
                            @if(empty($seller['activity']['special']))
                                <span class="c-red f18 mr10">￥{{ $data['price']}}</span>
                            @else
                                @if(empty($seller['activity']['special']['minNormsPrice']))
                                    <span class="c-red f18 mr10">￥{{ number_format($seller['activity']['special']['salePrice'], 2) }}</span> <!-- 折扣价 -->
                                @else
                                    <span class="c-red f18 mr10">￥{{ number_format($seller['activity']['special']['minNormsPrice'], 2) }}</span> <!-- 规格最低价 -->
                                @endif
                                <del class="c-gray f12 ml5">￥{{ $data['price'] }}</del> <!-- 原价 -->
                                @if($seller['activity']['special']['sale'] > 0)
                                    <div class="y-specialprice f12 ml0"><a href="">{{ $seller['activity']['special']['sale']}}折特价</a></div>
                                @endif
                            @endif
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        @if(!empty($seller['activity']['full']) || !empty($seller['activity']['special']) || !empty($seller['activity']['new']))
            <div class="list-block media-list y-nocenter">
                <ul>
                    <li class="item-content">
                        <div class="item-media f12 c-gray">促销</div>
                        <div class="item-inner">
                            <?php $first = true; ?>
                            @if(!empty($seller['activity']['full']))
                                <div class="item-title f12">
                                    <img src="{{ asset('wap/community/newclient/images/ico/jian.png') }}" width="16" class="icon iconfont c-gray va-3 mr5">
                            <span>
                                在线支付
                                @foreach($seller['activity']['full'] as $key => $value)
                                    @if($first)
                                        <?php $first = false; ?>
                                        满{{ number_format($value['fullMoney'], 2) }}减{{ number_format($value['cutMoney'], 2) }}元
                                    @else
                                        ,满{{ number_format($value['fullMoney'], 2) }}减{{ number_format($value['cutMoney'], 2) }}元
                                    @endif
                                @endforeach
                            </span>
                                </div>
                            @endif
                            @if(count($seller['activity']['special']) > 0)
                                <div class="item-title f12">
                                    <img src="{{ asset('wap/community/newclient/images/ico/tei.png') }}" width="16" class="icon iconfont c-gray va-3 mr5">
                                    <span>商家特价优惠</span>
                                </div>
                            @endif
                            @if(!empty($seller['activity']['new']))
                                <div class="item-title f12">
                                    <img src="{{ asset('wap/community/newclient/images/ico/xin.png') }}" width="16" class="icon iconfont c-gray va-3 mr5">
                                    <span>新用户在线支付立减{{ number_format($seller['activity']['new']['cutMoney'], 2) }}元</span>
                                </div>
                            @endif
                        </div>
                        <i class="icon iconfont c-gray f13 mt5 mr10 y-unfold none y-i1">&#xe602;</i>
                        <i class="icon iconfont c-gray f13 mt5 mr10 y-unfold none">&#xe601;</i>
                    </li>
                </ul>
            </div>
        @endif

        <div class="list-block f14 nobor" onclick="$.href('{{ u('Seller/detail',['id'=>$seller['id']]) }}')">
            <ul>
                <li class="item-content active">
                    <div class="item-inner pr10">
                        <div class="item-title">
                            <i class="icon iconfont c-gray vat mr5">&#xe632;</i><span>{{$seller['name']}}</span>
                        </div>
                        <i class="icon iconfont c-gray f13 mr-2">&#xe602;</i>
                    </div>
                </li>
            </ul>
        </div>
        <!-- 选择规格 -->
        <div class="list-block f14 nobor">
            <ul>
                @if($seller['serviceTimesCount'] > 0)
                    <li class="item-content">
                        <div class="item-inner">
                            <div class="item-title">服务时间
                                <input type="text" id='datetime-picker' class="x-servicetime service_time ml20 pl0" />
                            </div>
                            <!-- <i class="icon iconfont c-gray f13">&#xe602;</i> -->
                        </div>
                    </li>
                @endif
                <li class="item-content" onclick="$.href('{{ u('Goods/appbrief',['goodsId'=>$data['id']]) }}')">
                    <div class="item-inner">
                        <div class="item-title">商品详情</div>
                        <i class="icon iconfont c-gray f13">&#xe602;</i>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="f-bgtk size-frame1 show_item_norms_item_udb none">
        <div class="x-closebg">
            <div class="x-probox c-bgfff">
                <div class="x-prott pr">
                    <div class="x-propic">
                        <img src="{{$data['images'][0]}}" />
                    </div>
                    <div class="x-prottr pt10">
                        @if(!empty($data['norms']))
                            @if(empty($seller['activity']['special']))
                                <p class="c-red pt5 f14">￥<span class="f20 money_toal" id="money_toal_{{$data['norms'][0]['id']}}">{{ number_format($data['norms'][0]['price'], 2) }}</span></p>
                            @else

                                @if($seller['activity']['special']['sale'] > 0)
                                    <p class="c-red pt5 f14">
                                        ￥<span class="f20 money_toal" id="money_toal_{{$data['norms'][0]['id']}}">{{ $data['norms'][0]['price'] * $seller['activity']['special']['sale'] / 10 }}</span>
                                    </p>
                                @else
                                    <p class="c-red pt5 f14">
                                        ￥<span class="f20 money_toal" id="money_toal_{{$data['norms'][0]['id']}}">{{ $data['norms'][0]['price'] }}</span>
                                    </p>
                                @endif
                                <p><del class="c-gray f13 delPrice">￥{{ $data['norms'][0]['price'] }}</del>
                                    @if($seller['activity']['special']['sale'] > 0)
                                        <span class="c-red f12 ml5">{{ $seller['activity']['special']['sale']}}折特价</span>
                                    @endif
                                    @endif
                                    @else
                                        @if(empty($seller['activity']['special']))
                                <p class="c-red pt5 f14">￥<span class="f20 money_toal" id="money_toal_">{{ number_format($data['price'], 2) }}</span></p>
                                @else
                                    <p class="c-red pt5 f14">
                                        ￥<span class="f20 money_toal" id="money_toal_{{$data['norms'][0]['id']}}">{{ $seller['activity']['special']['salePrice'] }}</span>
                                    </p>
                                    <p><del class="c-gray f13 delPrice">￥{{ $data['price'] }}</del>
                                        @if($seller['activity']['special']['sale'] > 0)
                                            <span class="c-red f12 ml5">{{ $seller['activity']['special']['sale']}}折特价</span>
                                        @endif
                                        @endif
                                        @endif
                                        <i class="icon iconfont x-closeico c-gray">&#xe604;</i>
                    </div>
                </div>
                <div class="y-max-height">
                    @if(!empty($data['norms']))
                        <div class="x-prott pr">
                            <p class="f14">请选择商品属性</p>
                            <div class="x-psize clearfix c-gray f12 y-ggpsize2">
                                @foreach($data['norms'] as $key => $item)
                                    <span class="@if($key == 0) c-bg @endif show_item_id_{{$item['id']}}_norms_udb" data-info="{{$item['inCart']}}" data-id="{{$item['id']}}" data-prs="{{$item['price']}}" data-stock="{{$item['stock']}}" data-salePrice="{{ round($item['salePrice'], 2) }}" onclick="$.showItemNorms_item_udb({{$item['id']}},{{ round($item['salePrice'], 2) }})">{{$item['name']}}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="x-pnum pl10 pr10">
                            <span class="f14">购买数量</span>
                            <div class="fr x-num">
                                <i class="icon iconfont c-gray subtract_norms_cz  @if(!$cart['goods'][$option['goodsId']]['num']) none  @endif" data-id="{{$data['norms'][0]['id']}}" data-price="{{ number_format($data['norms'][0]['price'],2) }}">&#xe621;</i>
                                <span class="val tc pl0 @if(!$cart['goods'][$option['goodsId']]['num']) none @endif" id="normsval_span">@if($cart['goods'][$option['goodsId']]['num']){{$cart['goods'][$option['goodsId']]['num'] or 0 }} @endif</span>
                                <input type="hidden" value="0" class="val tc pl0 " id="normsval"  readonly="readonly" />
                                <i class="icon iconfont c-red add_norms_cz " data-id="{{$data['norms'][0]['id']}}"  data-price="{{ round($data['norms'][0]['price'], 2) }}" data-salePrice="{{ round($v['norms'][0]['salePrice'], 2) }}">&#xe61e;</i>
                            </div>
                        </div>
                    @else
                        <div class="x-pnum pl10 pr10">
                            <span class="f14">购买数量</span>
                            <div class="fr x-num">
                                <i class="icon iconfont c-gray subtract_cz @if($cart['goods'][$option['goodsId']]['num'] == 0) none @endif" data-id="{{$data['id']}}" data-price="{{ round($data['price'], 2) }}" data-salePrice="{{ round($data['norms']['salePrice'], 2) }}">&#xe621;</i>
                                <span class="val tc pl0 @if(!$cart['goods'][$option['goodsId']]['num'])none @endif" id="normsval_span">{{$cart['goods'][$option['goodsId']]['num'] or 0}}</span>
                                <input type="hidden" value="{{$cart['goods'][$option['goodsId']]['num']}}" class="val tc pl0 " id="normsval"  readonly="readonly" />
                                <i class="icon iconfont c-red add_cz" data-id="{{$data['id']}}"  data-price="{{ round($data['price'], 2) }}" data-salePrice="{{ round($data['norms']['salePrice'], 2) }}">&#xe61e;</i>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="x-pbtn c-white none udb_btn_show" id="cz_btn">
                    <button class="join f16 c-bg w100" onclick="$.shoppingcart()">确定</button>
                </div>
            </div>
        </div>
    </div>
    @include('wap.community.goods.share')
@stop

@section($js)
    <script src="{{ asset('js/dot.js') }}"></script>
    <script type="text/javascript">
        $("#datetime-picker").datetimePicker({
            value: ["{{Time::toDate(UTC_TIME+1800,'Y')}}", "{{Time::toDate(UTC_TIME+1800,'m')}}", "{{Time::toDate(UTC_TIME+1800,'d')}}", "{{ intval(Time::toDate(UTC_TIME+1800,'H')) }}", "{{Time::toDate(UTC_TIME+1800,'i')}}"]
        });
        $(".y-addshoppingcart").click(function(){
            $(".x-cart").removeClass("active");
            $(".f-bgtk").addClass("none");

            $(".size-frame1").removeClass('none');
            $(".show_item_norms_item_udb .y-ggpsize2 span").eq(0).addClass('active');
            var prs  = $(".show_item_norms_item_udb .y-ggpsize2 span").eq(0).attr('data-prs');
            var salePrice  =  $(".show_item_norms_item_udb .y-ggpsize2 span").eq(0).attr('data-salePrice');
            if(salePrice <= 0){
                prs = prs;
            }else{
                prs = salePrice;
            }
            $(".show_item_norms_item_udb .subtract_norms").attr('data-salePrice',salePrice);
            $(".show_item_norms_item_udb .subtract").attr('data-salePrice',salePrice);
            $(".show_item_norms_item_udb .add_norms").attr('data-salePrice',salePrice);
            $(".show_item_norms_item_udb .add").attr('data-salePrice',salePrice);

            $(".udb_btn_show").removeClass("none");
            $(".y-shopbtm").addClass("none");
            $(".show_item_norms_item_udb .val").removeClass('none');
            var  n = $("#goodsval{{$data['id']}}").val();
            if(n<=0){
                $(".show_item_norms_item_udb .subtract_cz").addClass("none");
                $(".show_item_norms_item_udb #normsval_span").addClass("none");
                $(".show_item_norms_item_udb #normsval_span").html(0);
            }else{
                $(".show_item_norms_item_udb .subtract_cz").removeClass("none");
                $(".show_item_norms_item_udb #normsval_span").html(n);
            }
            return false;
        })

        //弹出规格1
        $(".y-choicegg").click(function(){
            $(".size-frame2").removeClass('none');
        })
        // 关闭规格弹框
        $(".x-closebg .x-closeico").click(function(){
            $(this).parents(".f-bgtk").addClass('none');
            $(".udb_btn_show").addClass("none");
            $(".y-shopbtm").removeClass("none");
            //$("#goodsval").val($("#cartTotalAmount").html());
            $(".show_item_norms_item_udb #normsval_span").html($("#goodsval{{$data['id']}}").val());
        });

        // 规格选择
        $(".x-psize span").click(function(){
            $(this).addClass("c-bg").siblings().removeClass("c-bg");
        });
        var serviceFee = "{{ $seller['serviceFee'] }}";
        $("#indexAdvSwiper").swiper({"pagination":".swiper-pagination-adv", "autoplay":2000});

        // 减少数量
        $(document).on("touchend", ".udb_subtract", function ()
        {
            var thisVal = $(this);

            var sender = thisVal.siblings(".val");

            var value = parseInt(sender.html()) - 1;
            $(".msg_show"+sender.data("goodsid")).addClass('none');

            if (value <= 0)
            {
                value = 0;

                $(this).siblings(".add").siblings().addClass("none");
            }
            $.post("{{u('Goods/saveCart')}}", { sellerId:"{{$data['sellerId']}}",type:"{{$data['type']}}",goodsId: sender.data("goodsid"), normsId: sender.data("normsid"), num: value, serviceTime: 0 }, function(res){
                if(res.code == 0){
                    var pr = 0;
                    sender.html(value);
                    if(sender.data("saleprice") <= 0){
                        pr = sender.data("price");
                    }else{
                        pr = sender.data("saleprice");
                    }
                    CalculationTotal(sender.data("goodsid"), sender.data("normsid"), value, parseFloat(pr),res,sender.data("newold"));
                    $(".show_item_norms_"+sender.data("goodsid") +" .show_item_id_mnum").attr('data-normsid',sender.data("normsid"));
                    $(".show_item_norms_"+sender.data("goodsid") +" .show_item_id_mnum").attr('data-price',sender.data("price"));

                    if(value == 0){
                        $(".show_item_norms_"+sender.data("goodsid") +" .show_item_id_mnum").text(0);
                        $(".subtract").addClass("none");
                        $(".show_item_id_"+sender.data("normsid")+"_norms_udb").attr("data-info",0);
                        $(".show_item_id_"+sender.data("normsid")+"_norms").attr("data-info",0);
                        $("#goodsval{{$data['id']}}").val(0).addClass("none");
                    }else{
                        $(".show_item_id_"+sender.data("normsid")+"_norms_udb").attr("data-info",value);
                        $(".show_item_id_"+sender.data("normsid")+"_norms").attr("data-info",value);
                    }
                    if(sender.data("normsid") <= 0){
                        $("#goodsval{{$data['id']}}").val(value);

                    }else{
                        $(".show_item_id_"+sender.data("normsid")+"_norms_udb").attr("data-info",value);
                        $(".show_item_id_"+sender.data("normsid")+"_norms").attr("data-info",value);
                    }
                }else{
                    $.toast(res.msg);
                    $("#goodsval{{$data['id']}}").val($("#cartTotalAmount").html());
                }
            } );


        });
        $(document).on("touchend", ".x-closeico", function (){
            $(this).parents(".f-bgtk").addClass('none');
            $(".udb_btn_show").addClass("none");
            $(".y-shopbtm").removeClass("none");
        });
        // 添加数量
        $(document).on("touchend", ".udb_add", function ()
        {
            var thisVal = $(this);

            var sender = thisVal.siblings(".val");

            var value = parseInt(sender.html()) + 1;

            $.post("{{u('Goods/saveCart')}}", { sellerId:"{{$data['sellerId']}}",type:"{{$data['type']}}",goodsId: sender.data("goodsid"), normsId: sender.data("normsid"), num: value, serviceTime: 0 }, function(res){
                if(res.code == 0){
                    var pr = 0;
                    sender.html(value);
                    if(sender.data("saleprice") <= 0){
                        pr = sender.data("price");
                    }else{
                        pr = sender.data("saleprice");
                    }
                    CalculationTotal(sender.data("goodsid"), sender.data("normsid"), value, parseFloat(pr),res,sender.data("newold"));
                    $(".show_item_norms_"+sender.data("goodsid") +" .show_item_id_mnum").attr('data-normsid',sender.data("normsid"));
                    $(".show_item_norms_"+sender.data("goodsid") +" .show_item_id_mnum").attr('data-price',sender.data("price"));
                    $(".show_item_norms_"+sender.data("goodsid") +" .show_item_id_mnum").text(value);
                    var m = value * sender.data("price");
                    thisVal.siblings().removeClass("none");
                    $(".show_item_id_"+sender.data("normsid")).attr("data-ns",value);
                    if(sender.data("normsid") <= 0){
                        $("#goodsval{{$data['id']}}").val(value);

                    }else{
                        $(".show_item_id_"+sender.data("normsid")+"_norms_udb").attr("data-info",value);
                        $(".show_item_id_"+sender.data("normsid")+"_norms").attr("data-info",value);
                    }

                }else{
                    $.toast(res.msg);
                    $("#goodsval{{$data['id']}}").val($("#cartTotalAmount").html());
                    $(".msg_show"+sender.data("goodsid")).removeClass('none');
                }
            } );

        });
        // 数量加载
        $(document).on("touchend",".subtract",function(){
            var val = $(this).siblings(".val").val();
            val = parseInt(val) - 1;
            if(val <= 0){
                val = 1;
                $.alert('数量必须大于0！');
                return false;
            }
            $(this).siblings(".val").val(val);
            updateCart(val);
        });
        $(document).on("touchend",".add",function(){
            var val = $(this).siblings(".val").val();
            val = parseInt(val) + 1;
            $(this).siblings(".val").val(val);
            updateCart(val);
        });
        // 关闭规格弹框
        $(document).on("touchend",".showAlaertCartDsy .x-closebg",function(){
            $(this).parents(".size-frame").addClass('none');
        });
        // 规格选择
        $(document).on("touchend",".x-psize span",function(){
            $(this).addClass("c-bg").siblings().removeClass("c-bg");
        });

        // 数量加载
        $(document).on("click",".subtract_cz",function(){
            var val = $(".show_item_norms_item_udb #normsval_span").html();
            val = parseInt(val) - 1;
            if(val <= 0){
                val = 1;
                $.toast('数量必须大于0！');
                return false;
            }
            $(".show_item_norms_item_udb #normsval_span").html(val);
            $.funcMoney2(0,val,0);
        });

        $(document).on("click",".add_cz",function(){
            var val = $(".show_item_norms_item_udb #normsval_span").html();
            if(val  == ""){
                val = 0;
                $(".show_item_norms_item_udb .subtract").removeClass("none");
            }
            val = parseInt(val) + 1;
            $(".show_item_norms_item_udb #normsval_span").html(val);
            $.funcMoney2(0,val,0);
        });
        $.funcMoney2 = function(id,value,price){
            $(".show_item_norms_item_udb .subtract_norms_cz ").removeClass('none');
            $(".show_item_norms_item_udb .subtract_cz ").removeClass('none');
            $(".show_item_norms_item_udb .val ").removeClass('none');
            $(".show_item_norms_item_udb #normsval_span ").text(value);
            $(".show_item_norms_item_udb #normsval").val(value);
            //$(".show_item_id_"+id+"_norms_udb").attr("data-info",value);
        }
        $(document).on("touchend",".norms_choose",function(){
            $(".size-frame").removeClass('none');
        });
        $(document).on("touchend",".x-closeico",function(){
            $(".size-frame").addClass('none');
        });
        $(document).on("touchend",".norms_item",function(){
            var data = $(this).data('info');
            $(".norms_price").text(data.price);
            $(".norms_stock").text(data.stock);
            $(".current_price").text("￥ "+data.price);
            $(".norms_item").removeClass("c-bg");
            $(this).addClass("c-bg");
        })

        $(".norms_item.c-bg").trigger("click");

        //规格加入购物车
        $(document).on('touchend', '.cart_join', function(){
            var data = new Object();
            var norms =  $(".norms_item.c-bg").data('info');
            data.goodsId = norms.goodsId;
            data.normsId = norms.id;
            data.num = $('.norms_amount').text();
            $.post("{{u('Goods/saveCart')}}", data, function(res){
                if(res.code < 0){
                    $.router.load("{{u('User/login')}}", true);
                    return;
                } else if(res.code == 0) {
                    $('.x-bgtk').removeClass('none').show().find('.ts').text('操作成功');
                    $('.x-bgtk1').css({
                        position:'absolute',
                        left: ($(window).width() - $('.x-bgtk1').outerWidth())/2,
                        top: ($(window).height() - $('.x-bgtk1').outerHeight())/2 + $(document).scrollTop()
                    });
                    setTimeout(function(){
                        $('.x-bgtk').fadeOut('2000',function(){
                            $('.x-bgtk').addClass('none');
                        });
                    },'1000');
                } else {
                    $.alert(res.msg);
                }
                $(".total_amount").text(res.data.totalAmount);
                $(".total_price").text(res.data.totalPrice);
                $(".current_norms").text(norms.name);
                $(".size-frame").addClass('none');

            });
        })
        //收藏
        $(document).on("touchend",".collect_it .collect",function(){
            var obj = new Object();
            var collect = $(this);
            obj.id = "{{$data['id']}}";//$(this).data('id');
            obj.type = 1;
            if(collect.hasClass("c-red")){
                $.post("{{u('UserCenter/delcollect')}}",obj,function(result){
                    if(result.code == 0){
                        collect.removeClass("on");
                        $.toast(result.msg, function(){
                            collect.removeClass('c-red').addClass('c-black').html('&#xe653;');
                        });
                    } else if(result.code == 99996){
                        $.router.load("{{u('User/login')}}", true);
                    } else {
                        $.toast(result.msg);
                    }
                },'json');
            }else{
                $.post("{{u('UserCenter/addcollect')}}",obj,function(result){
                    if(result.code == 0){
                        collect.addClass("on");
                        $.alert(result.msg, function(){
                            collect.removeClass('c-black').addClass('c-red').html('&#xe654;');
                        });
                    } else if(result.code == 99996){
                        $.router.load("{{u('User/login')}}", true);
                    } else {
                        $.toast(result.msg);
                    }
                },'json');
            }
        });
        
        function toLogin(){
            $.router.load("{{u('User/login')}}", true);
        }
        $.shoppingcart = function(status){
            var val = $(".show_item_norms_item_udb #normsval_span").html();
            if(val == 0){
                $.toast('请选择数量!');
                return false;
            }
            updateCart(val);
        }

        //更新购物车 caiq
        function updateCart(goodsNum){
            $.showIndicator();
            $.post("{{u('Goods/saveCart')}}", { sellerId:"{{$data['sellerId']}}",type:"{{$data['type']}}",goodsId: {{$data['id']}}, normsId:0, num: goodsNum, serviceTime: 0 }, function(res){
                if(res.code < 0){
                    $.alert("请先登录");
                    setTimeout('toLogin()', 2000);
                    return;
                }
                if(res.code == 0){
                    var totalPrice = 0;
                    var totalAmount = 0;
                    for(var i = 0; i < res.data.list.length; i++){
                        if(res.data.list[i].id == "{{$data['seller']['id']}}"){
                            var cartGoods = res.data.list[i].goods;
                            for(var j = 0; j < cartGoods.length; j++){
                                totalAmount += parseInt(cartGoods[j].num);
                                if(cartGoods[j].sale <= 0){
                                    totalPrice += parseInt(cartGoods[j].num) * parseFloat(cartGoods[j].price);
                                }else{
                                    totalPrice += parseInt(cartGoods[j].num) * parseFloat((cartGoods[j].price * cartGoods[j].sale) / 10);
                                }
                            }
                        }
                    }
                    $(".count").val(goodsNum);
                    $('#cartTotalAmount').text(totalAmount);
                    $('#cartTotalPrice').text(totalPrice.toFixed(2));
                    var newGoodss = [];
                    $.each(res.data.list,function(ks,vs){
                        $.each(vs.goods,function(k,v){
                            if(v.goodsId == {{$data['id']}}){
                                newGoodss['name'] = v.name;
                                newGoodss['mun'] = v.num;
                                newGoodss['price'] = v.price;
                                newGoodss['goodsId'] = v.goodsId;
                                newGoodss['sale'] = v.sale;
                                newGoodss['servicetime'] = v.serviceTime;
                                return false;
                            }
                        });
                    });
                    var has = $(".page-current li#dsyId-"+newGoodss['goodsId']);
                    //修改
                    if(has.html()){

                        if(newGoodss['sale'] == 10){
                            // newDsyM = '';
                            money = newGoodss['price'] * newGoodss['mun'];
                        }else{
                            money = newGoodss['price'] * newGoodss['mun'] * (newGoodss['sale']/10);
                        }
                        var moneys = 0;
                        if(money == 0 ){
                            moneys  = "0.00";
                        }else{
                            moneys = money.toFixed(2)
                        }
                        $(".page-current #cartTotalPrice_DsyPrice_"+newGoodss['goodsId']).html(moneys);
                        $(".page-current #cartTotalPrice_DsyNum_"+newGoodss['goodsId']).html(newGoodss['mun']);
                    }
                    //追加
                    else{
                        var money =  0;;
                        // var newDsyM = 0;
                        if(newGoodss['sale'] == 10){
                            // newDsyM = '';
                            money = newGoodss['price'] * newGoodss['mun'];
                        }else{
                            money = newGoodss['price'] * newGoodss['mun'] * (newGoodss['sale']/10);
                        }
                        var moneys = 0;
                        if(money == 0 ){
                            moneys  = "0.00";
                        }else{
                            moneys = money.toFixed(2)
                        }
                        var html = $("#dsyHtml").html()
                                .replace('GOODSId',newGoodss['goodsId'])
                                .replace('GOODSId',newGoodss['goodsId'])
                                .replace('GOODSId',newGoodss['goodsId'])
                                .replace('GOODSId',newGoodss['goodsId'])
                                .replace('GOODSIDS',newGoodss['goodsId'])
                                .replace('NORMSID',newGoodss['normsId'] ? newGoodss['normsId'] : 0)
                                .replace('NORMSID',newGoodss['normsId'] ? newGoodss['normsId'] : 0)
                                .replace('NORMSID',newGoodss['normsId'] ? newGoodss['normsId'] : 0)
                                .replace('NORMSIDS',newGoodss['normsId'] ? newGoodss['normsId'] : 0)
                                .replace('SALEPRICE',newGoodss['price'] * (newGoodss['sale']/10))
                                .replace('DSYPRICE',newGoodss['price'])
                                .replace('SERVICRTIME',newGoodss['servicetime'] ? newGoodss['servicetime'] : 0 )
                                .replace('NAME',newGoodss['name'])
                                .replace('MONERY',moneys)
                                .replace('MUN',newGoodss['mun']);
                        // .replace('DELMONEY',newDsyM);
                        $("#dsyShowUl ul").append(html);
                    }

                    if(goodsNum > 0){
                        $(".modal_show_item_norms_item,.show_item_norms_item .subtract_norms ").removeClass('none');
                        $(".modal_show_item_norms_item,.show_item_norms_item_udb .subtract_norms_cz ").removeClass('none');
                        $(".modal_show_item_norms_item,.show_item_norms_item_udb .val ").removeClass('none');
                        $(".modal_show_item_norms_item,.show_item_norms_item .val ").removeClass('none');
                        $(".modal_show_item_norms_item,.show_item_norms_item_udb #normsval_span ").text(goodsNum);
                        $(".modal_show_item_norms_item,.show_item_norms_item #normsval_span ").text(goodsNum);
                        $(".modal_show_item_norms_item,.show_item_norms_item_udb #normsval").val(goodsNum);
                        $(".modal_show_item_norms_item,.show_item_norms_item #normsval").val(goodsNum);
                        $(".show_item_id_{{$data['id']}}_norms_udb").attr("data-info",goodsNum);
                        $(".show_item_id_{{$data['id']}}_norms").attr("data-info",goodsNum);
                        $(".subtract ").removeClass('none');
                        $("#goodsval{{$data['id']}}").val(goodsNum).removeClass('none');
                    }else{
                        $(".modal_show_item_norms_item,.show_item_norms_item .subtract_norms_cz ").addClass('none');
                        $(".modal_show_item_norms_item,.show_item_norms_item_udb .subtract_norms ").addClass('none');
                        $(".modal_show_item_norms_item,.show_item_norms_item,.show_item_norms_item_udb .val ").addClass('none');
                        $(".modal_show_item_norms_item,.show_item_norms_item #normsval_span ").text(0);
                        $(".modal_show_item_norms_item,.show_item_norms_item_udb #normsval_span ").text(0);
                        $(".modal_show_item_norms_item,.show_item_norms_item #normsval").val(0);
                        $(".modal_show_item_norms_item,.show_item_norms_item_udb #normsval").val(0);
                        $(".show_item_id_{{$data['id']}}_norms").attr("data-info",0);
                        $(".show_item_id_{{$data['id']}}_norms_udb").attr("data-info",0);
                        $("#goodsval{{$data['id']}}").val(0);
                    }

                    var totalPrice = 0;
                    var totalAmount = 0;
                    for(var i = 0; i < res.data.list.length; i++){
                        if(res.data.list[i].id == "{{$data['seller']['id']}}"){
                            var cartGoods = res.data.list[i].goods;
                            for(var j = 0; j < cartGoods.length; j++){
                                totalAmount += parseInt(cartGoods[j].num);
                                if(cartGoods[j].sale <= 0){
                                    totalPrice += parseInt(cartGoods[j].num) * parseFloat(cartGoods[j].price);
                                }else{
                                    totalPrice += parseInt(cartGoods[j].num) * parseFloat((cartGoods[j].price * cartGoods[j].sale) / 10);
                                }
                            }
                            break;
                        }
                    }
                    $("#cartTotalAmount").html(totalAmount);
                    $("#cartTotalPrice").html(totalPrice.toFixed(2));

                    if(res.data['firstOrder'] == 1){
                        if(res.data['sale'] > 0){
                            $(".font_text").html('已减<span id="DsySale">'+res.data['sale'].toFixed(2)+'</span>元');
                            var cartTotalPrice = totalPrice-res.data['sale'];
                            if(cartTotalPrice < 0){
                                cartTotalPrice = 0;
                            }
                            $("#cartTotalPrice").html(cartTotalPrice.toFixed(2));
                        }else{
                            $(".font_text").html(res.data['activity_name']+'还差<span id="DsySale">'+res.data['que_fee'].toFixed(2)+'</span>元');
                        }
                    }else{
                        $("#DsySale").html(res.data['sale'].toFixed(2));
                    }

                }else{
                    $.toast(res.msg);
                    $("#goodsval{{$data['id']}}").val($("#goodsval{{$data['id']}}").val()-1);
                    $(".modal_show_item_norms_item,.show_item_norms_item .msg_show").removeClass("none");
                }
                $(this).parents(".f-bgtk").addClass('none');
                $(".udb_btn_show").addClass("none");
                $(".y-shopbtm").removeClass("none");
                $(".show_item_norms_item_udb").addClass("none");
                $(".x-cart").removeClass("active");
                $(".f-bgtk").addClass("none");
                $.hideIndicator();
            });

        }

        $(function(){
            //是否有展开箭头
            var innerh = $(".y-nocenter .item-inner .item-title").length;
            if (innerh >= 3) {
                $(".y-nocenter .y-i1").removeClass("none");
                $(".y-nocenter .item-inner .item-title").last().addClass("none");
            }
            // 促销展开与收起
            $(document).off('click','.y-nocenter');
            $(document).on('click','.y-nocenter', function () {
                if($(this).find(".item-inner .item-title").length <= 2){
                    return false;
                }
                if($(this).hasClass("active")){
                    $(this).removeClass("active");
                    $(this).find(".y-unfold").addClass("none").siblings(".y-i1").removeClass("none");
                    $(this).find(".item-title").last().addClass("none");
                }else{
                    $(this).addClass("active");
                    // $(this).css("height",44);
                    $(this).find(".y-unfold").removeClass("none").siblings(".y-i1").addClass("none");
                    $(this).find(".item-title").last().removeClass("none");
                }
            });
			
			$.showGoodsAddMgo = function(){
						var data = new Object();
						data.goodsId = "{{$data['id']}}";
						data.serviceTime = $(".service_time").val();
						data.num  = $(".count").val();
						if(parseInt(data.num) <= 0){

							$.toast('数量必须大于0！');
							return false;
						}
						$.post("{{u('Goods/saveCart')}}", data, function(res){
							if(res.code < 0){
								$.router.load("{{u('User/login')}}", true);
							} else if(res.code == 0){
								$('.x-bgtk').removeClass('none').show().find('.ts').text('添加成功');
								$('.x-bgtk1').css({
									position:'absolute',
									left: ($(window).width() - $('.x-bgtk1').outerWidth())/2,
									top: ($(window).height() - $('.x-bgtk1').outerHeight())/2 + $(document).scrollTop()
								});
								setTimeout(function(){
									//$('.x-bgtk').fadeOut('2000',function(){
									$('.x-bgtk').addClass('none');
									//});
								},'1000');
								// $.router.load("{{u('GoodsCart/index')}}", true);
								if(res.data.cartIds <= 0){
									$.toast('数量必须大于0！');
								}else{
									window.location.href = "{{u('Order/order')}}?cartIds="+res.data.cartIds;
								}
							} else {
								$.toast(res.msg);
							}
						});
				};
		})

        //进入后购物车显示默认数量
        //updateCart($(".add").siblings(".val").val());
    </script>
@stop 
