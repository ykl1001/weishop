@extends('wap.community._layouts.base')

@section('css')
@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading" external href="@if((int)$args['id'] > 0 && (int)$args['type'] > 0) {{u('Goods/index',$args)}} @else {{u('Index/index')}} @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <a class="button button-link button-nav pull-right pageloading clearall" href="#" data-transition='slide-out'>
            <i class="icon iconfont c-gray x-searchico">&#xe630;</i>
        </a>
        <h1 class="title f16">购物车</h1>
    </header>
@stop

@section('content')
    <script type="text/javascript">
        //BACK_URL = "{!! Request::server('HTTP_REFERER') !!}";
    </script>
    @include('wap.community._layouts.bottom')
    <div class="content pull-to-refresh-content gwcontent">
        <div class="pull-to-refresh-layer">
            <div class="preloader"></div>
            <div class="pull-to-refresh-arrow"></div>
        </div>

        <div class="x-null pa w100 tc y-ordpay @if(!empty($cart) && $loginUserId > 0) none @endif">
            <img src="{{ asset('wap/community/newclient/images/null.png') }}" width="110">
            @if( $loginUserId > 0 )
                <p class="f12 c-gray mt10">购物车是空的,您可以<br> <a class="f14 c-white x-btn db mt15" external href="{{u('Index/index')}}">逛逛首页</a></p>
            @else
                <p class="f12 c-gray mt10">登录后才能查看购物车哦<br> <a class="f14 c-white x-btn db mt15" external href="{{u('User/login')}}">立即登录</a></p>
            @endif
        </div>

        @if(empty($cart))

        @else
            @if(!empty($address))
                <div class="card y-card active" onclick="$.href('{!! u('UserCenter/address',['cartIds' => 1]) !!}')">
                    <div class="card-content">
                        <div class="fl ml10 mt20">送至：</div>
                        @if($address['id'] > 0)
                            <div class="card-content-inner y-gwcaddr">
                                <php>
                                    $name = mb_substr($address['name'], 0, 5, "utf-8") . (mb_strlen($address['name'], 'UTF8') > 5 ? "……" : "");
                                    ;
                                </php>
                                <p style="margin-top:5px;"><span class="mr10">{{ $name }}</span><span>{{ $address['mobile'] }}</span></p>
                                <p class="mt5"  style="width: 90%;">{{ $address['realAddress'] }}</p>
                                <input type="hidden" name="addressId" id="addressId" value="{{ (int)$address['id'] }}">
                            </div>
                        @else
                            <div class="card-content-inner y-gwcaddr">
                                <p class="mt5" style="width: 90%;">{{ $address['address'] }}</p>
                                <input type="hidden" name="addressId" id="addressId" value="{{ (int)$address['id'] }}">
                            </div>
                        @endif
                        <div class="y-xzshaddrr"><i class="icon iconfont c-gray">&#xe602;</i></div>
                    </div>
                </div>
            @else
                <?php
                $cartIds = $cartIds ? $cartIds : 1;
                ?>
                <div class="c-bgfff pt15 pb15 mt10 pl10 pr10 mb10" onclick="$.href('{!! u('UserCenter/address',['cartIds' => $cartIds,'newadd' => 1] ) !!}')">
                    <div class="f12">
                        <span>添加地址</span>
                        <i class="icon iconfont fr c-gray">&#xe602;</i>
                    </div>
                </div>
            @endif
            @foreach($cart as $ckey => $citem)
                <php>
                    $isnoCheck = 0;
                    foreach($citem['goods'] as $gkey => $gitem){
                    $cancheck = ($citem['canService'] == 1 && $gitem['status'] == 1 && ($gitem['stock'] > 0 || $gitem['type'] == 2) && $citem['serviceTimesCount'] > 0) ? 1 : 0;
                    $citem['price'] = $citem['serviceTimesCount'] > 0 ? $citem['price'] : '0.00';
                    if($cancheck){
                    $isnoCheck = 1;
                    }
                    }
                </php>
                <div class="card y-shopcart y-shopcart{{$citem['id']}}" data-sellerid="{{$citem['id']}}">
                    <div class="card-header">
                        <div class="w100">
                            <i class="icon iconfont y-checkbox mr5 c-red @if($citem['canService'] == 1 && $isnoCheck) active @endif vat" @if($citem['canService'] == 1 && $isnoCheck) checked='true' @endif data-canservice="{{$citem['canService']}}" data-isnocheck={{$isnoCheck}} data-price="{{$citem['price']}}" data-type="all" @if($citem['serviceTimesCount'] < 1) checked="true" @else  @endif data-sellerid="{{$citem['id']}}">&#xe612;</i>
                        <span class="c-black f14 vat">
                            <i class="icon iconfont c-gray2 mr5">&#xe632;</i>
                            <a href="#" @if($citem['storeType'] == 1) onclick='$.href("{{u('Seller/detail', ['id'=>$citem['id']])}}")' @else onclick='$.href("{{u('Goods/index', ['id'=>$citem['id'],'type'=>$citem['type'],'urltype'=>$citem['type']])}}")' @endif >
                                {{$citem['name']}} @if($citem['serviceTimesCount'] < 1) （商家休息中） @elseif(!$citem['canService']) （不在服务范围） @endif
                            </a>
                        </span>
                        </div>
                    </div>
                    <div class="card-content card-content{{$citem['id']}}">
                        <div class="list-block media-list y-shoplist">
                            <ul>
                                @foreach($citem['goods'] as $gkey => $gitem)
                                    <li class="y-ddcontent on" data-itemid="{{$gitem['id']}}">
                                        <a href="#" class="item-link item-content">
                                            <div class="item-media">
                                                <php>
                                                    $cancheck = ($citem['canService'] == 1 && $gitem['status'] == 1 && ($gitem['stock'] > 0 || $gitem['type'] == 2) && $citem['serviceTimesCount'] > 0) ? 1 : 0;
                                                </php>
                                                <i class="icon iconfont y-checkbox mr5 c-red @if($cancheck) active @endif" data-cancheck="{{$cancheck}}" data-type="single" @if($cancheck) checked="true" @else checked="false" @endif data-sellerid="{{$citem['id']}}" data-itemid="{{$gitem['id']}}">&#xe612;</i>
                                                <img @if($gitem['status'] == 1 && ($gitem['type'] == 2 || $gitem['stock'] > 0)) onclick="$.href('{{u('Goods/detail',['goodsId'=>$gitem['goodsId']])}}')" @endif src="{{ formatImage($gitem['logo'], 100, 100) }}" width="54">
                                            </div>
                                            <div class="item-inner">
                                                <div class="item-title-row">
                                                    <div class="item-title f13 y-maxw" id="onc" @if($gitem['status'] == 1 && $gitem['stock'] > 0) onclick="$.href('{{u('Goods/detail',['goodsId'=>$gitem['goodsId']])}}')" @elseif($gitem['status'] == 1 && $gitem['type'] == 2) onclick="$.href('{{u('Goods/detail',['goodsId'=>$gitem['goodsId'], 'type'=>2, 'num'=>$gitem['num']])}}')" @endif>{{$gitem['name']}}</div>
                                                </div>
                                                @if($gitem['normsName'])
                                                    <div class="item-title f12 c-gray">{{ str_replace(':','-',$gitem['normsName'])}}</div>
                                                @endif
                                                <div class="item-title-row @if(!$gitem['normsName'])mt10  @endif">
                                                    <div class="item-title f14 c-red">
                                                        @if( $gitem['sale'] == 10 )
                                                            ￥<span class="y-price">{{number_format($gitem['price'], 2)}}</span>
                                                        @else
                                                            ￥<span class="y-price">{{number_format($gitem['price'] * ($gitem['sale']/10), 2)}}</span>
                                                            <del class="c-gray f12 ml5">￥{{number_format($gitem['price'], 2)}}</del>
                                                        @endif
                                                    </div>
                                                    <div class="item-after">
                                                        <div class="x-num">
                                                            @if($citem['serviceTimesCount'] < 1)

                                                            @elseif($gitem['status'] == 0 || ($gitem['type'] == 1 && $gitem['stock'] <= 0))
                                                                已卖光 <span class="c-red f14 delete" data-sellerid="{{$citem['id']}}" data-itemid="{{$gitem['id']}}">删除</span>
                                                            @else
                                                                <i class="icon iconfont subtract fuhao fl" data-sellerid="{{$citem['id']}}" data-itemid="{{$gitem['id']}}">&#xe621;</i>
                                                                <input style="width: 30px" name="gitem" type="text" value="{{$gitem['num']}}" data-sellerid="{{$citem['id']}}" data-servicetime="{{$gitem['serviceTime']}}" data-itemid="{{$gitem['id']}}" @if($cancheck) checked="true" @else checked="false" @endif data-goodsid="{{$gitem['goodsId']}}" data-normsid="{{$gitem['skuSn']}}" data-price="{{$gitem['price']}}" data-sale="{{$gitem['sale']}}" data-type="{{$gitem['type']}}" class="tc pl0 fl" readonly="readonly">
                                                                <i class="icon iconfont add fuhao fr" data-itemid="{{$gitem['id']}}">&#xe61e;</i>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            @if($citem['deliveryFee'] > 0.001 && $citem['isAvoidFee'] == 1 && $citem['type'] == 1)
                                <div class="y-spcd f12 gocollect @if($citem['avoidFee'] - $citem['price'] <= 0 ) none @endif">
                                    <a href="{{u('Goods/index', ['id'=>$citem['id'],'type'=>$citem['type'],'urltype'=>$citem['type']])}}">
                                        <div>
                                            <input type="hidden" class="isAvoidFee" value="{{$citem['isAvoidFee']}}">
                                            <input type="hidden" class="avoidFee" value="{{$citem['avoidFee']}}">
                                            <span>运费{{ number_format($citem['deliveryFee'],2) }}元</span>,
                                            <span> 再买<span class="surplusFee">{{ number_format( $citem['avoidFee'] - $citem['price'] , 2) }}</span>元免运费</span>
                                <span class="fr">
                                    <span>去凑单</span>
                                    <i class="icon iconfont vat ml5 f14">&#xe602;</i>
                                </span>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer c-gray2 f12">
                        <div class="f12 c-black y-maxwidth">
                            合计：<span class="c-red f16">￥<b class="total_price" data-sellerid="{{$citem['id']}}">{{number_format((float)$citem['price'], 2)}}</b></span>
                            @if($citem['storeType'] == 0)
                                @if($citem['type'] == 1)
                                    @if($citem['deliveryFee'] > 0.001 && $citem['isAvoidFee'] == 1)
                                        @if($citem['avoidFee'] - $citem['price'] <= 0)
                                            <span class="c-gray ml5 notAvoidFee">(免运费)</span>
                                        @else
                                            <span class="c-gray ml5 notAvoidFee">(不含运费)</span>
                                        @endif
                                    @else
                                        <span class="c-gray ml5 notAvoidFee">(<?php if($citem['deliveryFee'] > 0.001) echo "不含运费"; else echo "免运费"; ?>)</span>
                                    @endif
                                @endif
                            @endif
                        </div>
                        @if($citem['serviceTimesCount'] < 1)
                            <div class="f14 y-shopcartxbtn c-gray97">
                                <a href="#" data-url="{{u('Goods/index', ['id'=>$citem['id'],'urltype'=>$citem['type']])}}" data-servicefee="{{$citem['serviceFee']}}" settlement="false" unpay="2" data-sellerid="{{$citem['id']}}" class="y-shopbtn c-bg c-white c-gray97">商家休息中</a>
                            </div>
                        @elseif( (float)$citem['price'] < $citem['serviceFee'])
                            <div class="f14 y-shopcartxbtn" id="nojump" data-url="{{u('Goods/index', ['id'=>$citem['id'],'type'=>$citem['type']])}}">
                                <a href="javascript:$.href('{{u('Goods/index', ['id'=>$citem['id'],'type'=>$citem['type']])}}')" data-servicefee="{{$citem['serviceFee']}}" settlement="false" unpay="1" data-sellerid="{{$citem['id']}}" class="y-shopbtn c-bg c-white">差{{number_format($citem['serviceFee'] - (float)$citem['price'], 2)}}元起送,去凑单</a>
                            </div>
                        @else
                            @if($citem['canService'] && $citem['canPay'])
                                <div class="f14 y-shopcartxbtn">
                                    <a href="#" data-url="{{u('Goods/index', ['id'=>$citem['id'],'urltype'=>$citem['type']])}}" data-servicefee="{{$citem['serviceFee']}}" settlement="true" data-sellerid="{{$citem['id']}}" class="y-shopbtn c-bg c-white">去结算</a>
                                </div>
                            @else
                                <div class="f14 y-shopcartxbtn c-gray97">
                                    <a href="#" data-url="{{u('Goods/index', ['id'=>$citem['id'],'urltype'=>$citem['type']])}}" data-servicefee="{{$citem['serviceFee']}}" settlement="false" unpay="2" data-sellerid="{{$citem['id']}}" class="y-shopbtn c-bg c-white c-gray97">去结算</a>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@stop

@section($js)
    <script src="{{ asset('js/dot.js') }}"></script>


    <script type="text/javascript">
        $(function(){
            $(document).on("touchend","#nojump",function(){
                $.href($(this).data('url'));
            });

            $(document).unbind("refresh", ".pull-to-refresh-content");
            $(document).on('refresh', '.pull-to-refresh-content',function(e) {
                window.location.reload();
            });

            //清空购物车
            $(document).unbind("touchend", ".clearall");
            $(document).on("touchend", ".clearall", function ()
            {
                $.confirm("确认清空购物车吗？", function(){
                    //加载提示
                    $.showPreloader("正在清空购物车<br/>请稍候...");

                    $.post("{{u('Goods/cartDelete')}}", { id: 0 }, function(){
                        //隐藏加载
                        $.hidePreloader();

                        //成功失败提示
                        $.toast("购物车已清空");
                        //$.toast("购物清空失败，请稍候重试");
                        window.location.reload();
                    });
                })

            });

            // 处理返回值
            function HandleResult(res)
            {
                if (res.code < 0)
                {
                    $.alert("请登录");
                    setTimeout(function () { window.location.href = "{{u('User/login')}}"; }, 2000);
                }
                else if (res.code > 0)
                {
                    $.alert(res.msg);
                }
            }
            // 减少数量
            $(document).unbind("touchend", ".subtract");
            $(document).on("touchend", ".subtract", function ()
            {

                if($(this).parents(".y-ddcontent").find(".y-checkbox").data('cancheck') == 0){
                    return;
                }

                var sender = $("input[data-itemid='" + $(this).data("itemid") + "']");

                var value = parseInt(sender.val()) - 1;
                var sellerId = $(this).attr("data-sellerid");
                if (value <= 0)
                {
                    $(this).parents(".y-ddcontent").hide();
                    var deleted_goods = $(this).parents(".y-ddcontent");
                    if($(".y-shopcart"+sellerId).find(".y-ddcontent").length <= 0){
                        $(".y-shopcart"+sellerId).remove();
                    }
                    //$.post("{{u('Goods/cartDelete')}}", { id: sender.data("itemid") });
                    if($(".y-ddcontent").length <= 0){
                        $(".card").remove();
                        $(".gwcontent").html('<div class="x-null pa w100 tc y-ordpay">\
                                <img src="{{ asset('wap/community/newclient/images/null.png') }}" width="110">\
                                <p class="f12 c-gray mt10">购物车信息已删完,您可以<br> <a class="f14 c-white x-btn db mt15" external href="{{u('Index/index')}}">逛逛首页</a></p></div>');
                    }
                }

                $.showIndicator();
                $.post("{{u('Goods/saveCart')}}", { goodsId: sender.data("goodsid"), skuSn: sender.data("normsid"), num: value, serviceTime: sender.data("servicetime") }, function(res){
                    if(res.code == 0){

                        sender.val(value);

                        var obj = sender.parents(".y-shopcart");

                        CalculationTotal(obj);
                        if(value<=0)
                        {
                            deleted_goods.remove();
                            if(obj.find('.y-ddcontent').length <= 0){
                                obj.remove();
                            }
                        }
                    }
                    HandleResult(res);
                    $.hideIndicator();
                });

                var type = sender.data("type");
                if(type == 2){
                    var before = "$.href('";
                    var after = "')";
                    var urls = "{{ u('Goods/detail',['type'=>2])}}";
                    var gooId = '&goodsId='+sender.data("goodsid");
                    var url2 = "&num="+value;
                    $("#onc").attr("onclick", before+urls+gooId+url2+after);
                }

            });
            // 添加数量
            $(document).unbind("touchend", ".add");
            $(document).on("touchend", ".add", function()
            {
                if($(this).parents(".y-ddcontent").find(".y-checkbox").data('cancheck') == 0){
                    return;
                }

                var sender = $("input[data-itemid='" + $(this).data("itemid") + "']");

                var value = parseInt(sender.val()) + 1;
                $.showIndicator();
                $.post("{{u('Goods/saveCart')}}", { goodsId: sender.data("goodsid"),skuSn : sender.data("normsid"), num: value, serviceTime: sender.data("servicetime") }, function(res){
                    if(res.code == 0){

                        sender.val(value);

                        var obj = sender.parents(".y-shopcart");

                        CalculationTotal(obj);

                    }
                    HandleResult(res);
                    $.hideIndicator();
                });

                var type = sender.data("type");
                if(type == 2){
                    var before = "$.href('";
                    var after = "')";
                    var urls = "{{ u('Goods/detail',['type'=>2])}}";
                    var gooId = '&goodsId='+sender.data("goodsid");
                    var url2 = "&num="+value;
                    $("#onc").attr("onclick", before+urls+gooId+url2+after);
                }

                var obj = sender.parents(".y-shopcart");

                CalculationTotal(obj);
            });
            // 选择
            $(document).unbind("touchend", ".y-checkbox");
            $(document).on("touchend", ".y-checkbox", function()
            {
                var sender = $(this);

                if(sender.data('checked') == "true" || sender.data('checked') == true){
                    return;
                }

                var list = $(this).parents(".y-shopcart").find(".y-checkbox[data-type='single']");

                //var list = $(".y-checkbox[data-type='single'][data-sellerid='" + sender.data("sellerid") + "']");

                if (sender.data("type") == "all")
                {

                    if(sender.data('canservice') == 0 || sender.data('isnocheck') == 0){
                        return;
                    }

                    if (sender.attr("checked") == "true")
                    {
                        sender.attr({ "checked": "false" }).removeClass("active");

                        list.attr({ "checked": "false" }).removeClass("active");

                        $(this).parents(".y-shopcart").find("input[data-sellerid='" + sender.data("sellerid") + "']").attr({ "checked": "false" });
                    }
                    else
                    {
                        var list = $(this).parents(".y-shopcart").find(".y-checkbox[data-cancheck='1']");
                        sender.attr({ "checked": "true" }).addClass("active");
                        list.attr({ "checked": "true" }).addClass("active");
                        $(this).parents(".y-shopcart").find("input[data-sellerid='" + sender.data("sellerid") + "']").attr({ "checked": "true" });
                    }
                }
                else
                {
                    if(sender.data('cancheck') == 0){
                        return;
                    }
                    if (sender.attr("checked") == "true")
                    {
                        sender.attr({ "checked": "false" }).removeClass("active");

                        $(this).parents(".y-shopcart").find("input[data-itemid='" + $(this).data("itemid") + "']").attr({ "checked": "false" });
                    }
                    else
                    {
                        sender.attr({ "checked": "true" }).addClass("active");

                        $(this).parents(".y-shopcart").find("input[data-itemid='" + $(this).data("itemid") + "']").attr({"checked": "true"});
                    }

                    var result = true;

                    list.each(function ()
                    {
                        if ($(this).attr("checked") != "true")
                        {
                            result = false;

                            return false;
                        }
                    });

                    var all = $(this).parents(".y-shopcart").find(".y-checkbox[data-type='all'][data-sellerid='" + sender.data("sellerid") + "']");
                    //console.log(all);
                    if (result == true)
                    {
                        all.attr({ "checked": "true" }).addClass("active");
                    }
                    else
                    {
                        all.attr({ "checked": "false" }).removeClass("active");
                    }
                }

                var obj = sender.parents(".y-shopcart");

                CalculationTotal(obj);
            });
            // 删除
            $(document).unbind("touchend", ".delete");
            $(document).on("touchend", ".delete", function ()
            {
                var sender = $(this);

                var obj = sender.parents(".y-shopcart");

                $.confirm("请确认是否删除？", "删除", function ()
                {
                    if(sender.parents(".y-shopcart").find(".y-ddcontent").length <= 1){
                        sender.parents(".y-shopcart").remove();
                    } else {
                        $(".y-ddcontent[data-itemid='" + sender.data("itemid") + "']").remove();
                    }

                    var shopcart = $(".y-shopcart[data-sellerid='" + sender.data("sellerid") + "']");

                    if ($(".y-ddcontent", shopcart).length == 0)
                    {
                        shopcart.remove();

                        if ($(".y-ddcontent").length == 0)
                        {
                            var src = "{{ asset('wap/community/newclient/images/null.png') }}";
                            $(".content").html('<div class="x-null pa w100 tc y-ordpay">\
                                <img src="'+src+'" width="110">\
                                <p class="f12 c-gray mt10">亲，这里什么都没有！</p>\
                            </div>');
                        }
                    }

                    $.post("{{u('Goods/cartDelete')}}", { id: sender.data("itemid") }, HandleResult);

                    CalculationTotal(obj);
                });
            });
            // 去结算
            $(document).unbind("touchend", ".y-shopbtn");
            $(document).on("touchend", ".y-shopbtn", function ()
            {
                if("{{$isAddressNull}}" == -1){
                    $.confirm("你还没有添加收货地址哦,点击确认去添加！", function(){
                        $.href("{{u('UserCenter/address?cartIds=1')}}");
                    })
                    return false;
                }

                $.showPreloader('请稍等，正在进入订单界面...');
                var sender = $(this);

                if(sender.attr("settlement") == "true")
                {
                    var id = sender.attr("data-sellerid");
                    var shopping = new Array();
                    var gitem = sender.parents(".y-shopcart"+id).find("input[name='gitem']");
                    $.each(gitem,function(){
                        var senders = $(this);
                        var bln = senders.attr("checked").replace(/(^\s*)|(\s*$)/g, "");
                        if( bln == true || bln == "true"){
                            shopping[shopping.length] = senders.attr("data-itemid");
                        }
                    });

                    var addressId = $("#addressId").val();
                    var price = $(this).parent().prev().find(".total_price").html();
                    //console.log("{{u('Order/order')}}?addressId="+addressId+"&cartIds=" + shopping.join(","));
                    // $.router.loadPage("{{u('Order/order')}}?addressId="+addressId+"&cartIds=" + shopping.join(","), true);
                    var url = "{{u('Order/order')}}?addressId="+addressId+"&cartIds=" + shopping.join(",")+"&price="+price;
                    window.location.href = url;
                }
                else
                {
                    $.hidePreloader();
                }

            });
            // 计算合计
            function CalculationTotal(content)
            {
                var total = 0;

                var isChecked = false;

                $("input[checked='true']", content).each(function ()
                {
                    var sender = $(this);
                    var sale = sender.data('sale');

                    isChecked = true;

                    if(sale == 10)
                    {
                        total += parseInt(this.value) * parseFloat(sender.data("price"));
                    }
                    else
                    {
                        total += parseInt(this.value) * parseFloat(sender.data("price") * (sale/10) );
                    }

                });
                /*if(total==0){
                 content.remove();
                 }*/
                $(".total_price", content).html(total.toFixed(2));

                var shopbtnObj = content.find(".y-shopbtn");

                var servicefee = parseFloat(shopbtnObj.data("servicefee"));

                var settlement = true;

                if(total == 0 && $("input[checked='true']", content).length == 0){
                    settlement = false;
                }

                if (servicefee > total && isChecked == true)
                {
                    var url = shopbtnObj.attr("data-url");
                    shopbtnObj.addClass("c-bg")
                            .removeClass("c-gray97")
                            .attr({ "settlement": "false" , "unpay": "1", "href": url})
                            .html("差" + (servicefee - total).toFixed(2) + "元起送,去凑单");
                }
                else
                {
					if(content.find('.y-shopbtn').attr('settlement') == 'false'){
						content.find('.y-shopcartxbtn').removeClass("c-gray97");
					}else{
						content.find('.y-shopcartxbtn').addClass("c-gray97");						
					}
                    shopbtnObj.removeClass("c-gray97")
                            .addClass(settlement ? "c-bg" : "c-gray97")
                            .attr({ "settlement": settlement ? "true" : "false", "unpay": "2", "href": "#" })
                            .html("去结算");
                }

                // 购物车数量
                var count = 0;

                $("input[type='text']", $(".content")).each(function ()
                {
                    count += parseInt(this.value);
                });

                if (count == 0)
                {
                    $("#tpGoodsCart").remove();
                    $(".x-null").removeClass("none");
                    $(".card-content").addClass("none");
                }
                else
                {
                    $("#tpGoodsCart").html(count);
                }

                //满减提示
                if(content.find(".isAvoidFee").val() == 1){
                    var surplusFee = content.find(".avoidFee").val() - total;
                    surplusFee = surplusFee.toFixed(2);
                    if(surplusFee > 0){
                        content.find('.surplusFee').text(surplusFee); //还需要好多才减
                        content.find('.notAvoidFee').text("(不含运费)");
                        content.find('.gocollect').removeClass("none");
                    }else{
                        content.find('.notAvoidFee').text("(免运费)");
                        content.find('.gocollect').addClass("none");
                    }
                }

            }

            $.init();
        });
    </script>
@stop 