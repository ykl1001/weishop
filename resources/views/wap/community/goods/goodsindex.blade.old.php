@extends('wap.community._layouts.base')

@section('css')
@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading" href="{{ u('Seller/detail',['id'=>Input::get('id')]) }}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">{{$seller['name']}}</h1>
        <a class="button button-link button-nav pull-right open-popup pageloading" style="display:none;" data-popup=".popup-about" href="{{ u('Seller/search') }}">
            <i class="icon iconfont c-gray x-searchico">&#xe65e;</i>
        </a>
    </header>
@stop

@section('content')
    <?php
    $cartgoods = [];

    foreach($cart["data"]["goods"] as $good)
    {
        $cartgoods[$good["goodsId"]][$good["normsId"]] = $good["num"];
    }
    ?>
    <!-- NEW -->
    @include('wap.community.goods.cartfooter')
    <div class="content native-scroll"  id='' style="bottom: 0px;">
        @include('wap.community.goods.sellergoodshead')
        <!-- 菜单列表 -->
        <div class="x-sjfltab x-goodstab clearfix">
            <div class="buttons-tab fl pr" id="scroll_menu">
                <?php $leftsort = 0; ?>
                @foreach($cate as $ckey => $item)
                    @if(count($item['goods']) > 0)
                        <a href="#tab_{{$ckey}}" class="tab-link button @if($leftsort == 0) active @endif">{{$item['name']}}</a>
                        <?php $leftsort++; ?>
                    @endif
                @endforeach
            </div>
            <div class="tabs c-bgfff fl">
                <?php $leftsort = 0; ?>
                @foreach($cate as $ckey => $item)
                    @if(count($item['goods']) > 0)
                        <div id="tab_{{$ckey}}" class="tab @if($leftsort == 0) active @endif">
                            <div class="x-goodstit">
                                <div class="item-title f15 c-gray">{{$item['name']}}({{count($item['goods'])}})</div>
                            </div>
                            <div class="list-block media-list x-sortlst f14 nobor pr">
                                <ul>
                                    @foreach($item['goods'] as $k=>$v)
                                        <li class="item-content">

                                            <div class="item-inner pl0">
                                                <div class="item-title">
                                                    <div onclick="$.href('{{u('Goods/detail',['goodsId'=>$v['id']])}}')">
                                                        <div class="goodspic fl mr5">
                                                            <img src="@if($v['image']) {{ formatImage($v['image'],150,150) }} @else {{ asset('wap/community/client/images/wykdimg.png') }} @endif">
                                                        </div>
                                                        <span class="goodstit">{{$v['name']}}</span>
                                                    </div>
                                                    <div class="mt5">
                                                        <span class="c-red f15">￥{{number_format($v['price'], 2)}}</span>
                                                        @if($seller['serviceTimesCount'] > 0)
                                                            @if(count($v['norms']) < 1)
                                                                <div class="x-num fr">
                                                                    <i class="icon iconfont c-gray subtract fl <?php if(empty($cartgoods[$v['id']][0])) echo "none"; ?>">&#xe622;</i>
                                                                    <span class="val tc pl0 fl <?php if(empty($cartgoods[$v['id']][0])) echo "none"; ?>" data-goodsid="{{$v['id']}}" data-normsid="0" data-price="{{$v['price']}}"><?php if(empty($cartgoods[$v['id']][0])) echo "0"; else echo$cartgoods[$v['id']][0]; ?></span>
                                                                    <i class="icon iconfont c-red add fl">&#xe61f;</i>
                                                                </div>
                                                            @else
                                                                <div class="fr c-red f12 y-xgg" data-ids="{{$v['id']}}" data-name="{{$v['name']}}">选规格</div>
                                                            @endif
                                                        @else
                                                            <span class="c-gray f12 fr">商家休息中</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        @if(count($v['norms']) > 0)
                                            <!-- 有子菜单 -->
                                            <?php $t = 0; ?>
                                            <div class="show_item_norms_{{$v['id']}} none">
                                                <div class="y-xzggtc tl">
                                                    <p class="f14">选择规格</p>
                                                    <ul class="y-ggpsize clearfix">
                                                        @foreach($v['norms'] as $nk => $n)
                                                            <li class="@if($nk == 0) active @endif show_item_id_{{ $n['id']}}" data-ns="{{$cartgoods[$v['id']][$n['id']]  or 0 }}" data-prs="{{$n['price']}}" onclick='$.showItemNorms({{$v['id']}},"{{ $n['id'] }}","{{$n['price']}}")'>
                                                                <a href="#">{{$n['name']}}</a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                <div class="y-gmnum clearfix">
                                                    <span class="f14 c-red">￥<span class="money_toal" id="money_toal_{{$v['id']}}">{{$v['norms'][0]['price']}}</span></span>
                                                    <span class="f14 msg_show msg_show{{$v['id']}} none" style="color: red;font-size: 0.2rem !important;">抱歉：商品库存不足</span>
                                                    <div class="y-num fr">
                                                        <i class="icon  iconfont c-gray subtract fl <?php if(empty($cartgoods[$v['id']][$v['norms'][0]])) echo "none"; ?>">&#xe621;</i>
                                                        <span class=" show_item_id_mnum val tc pl0 fl <?php if(empty($cartgoods[$v['id']][$v['norms'][0]])) echo "none"; ?>" data-goodsid="{{$v['id']}}" data-normsid="{{$v['norms'][0]['id']}}" data-price="{{$v['norms'][0]['price']}}"><?php if(empty($cartgoods[$v['id']][$v['norms'][0]])) echo "0"; else echo$cartgoods[$v['id']][$v['norms'][0]['id']]; ?></span>
                                                        <i class="icon iconfont c-red add fl">&#xe61e;</i>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <?php $leftsort++; ?>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@stop

@section($js)
    <script src="{{ asset('wap/community/client/js/cel.js') }}"></script>
    <script type="text/javascript">
        $(function(){
            // 弹窗
            $(document).on('click','.y-xgg', function () {
                var id = $(this).data('ids');
                var name = $(this).data('name');
                var html = $(".show_item_norms_" + id).html();
                $.modal({
                    extraClass:"modal_show_item_norms_"+id,
                    title:  '<div class="y-paytop"><i class="icon iconfont c-gray fr">&#xe604;</i><p class="c-black f18 tl">'+name+'</p></div>',
                    text:html

                });
                $(".modal_show_item_norms_"+id+" .y-ggpsize li").eq(0).addClass('active');
                var val  = $(".modal_show_item_norms_"+id+" .y-ggpsize li").eq(0).data('ns');
                var prs  = $(".modal_show_item_norms_"+id+" .y-ggpsize li").eq(0).data('prs');
                if(val > 0){
                    $(".modal_show_item_norms_"+id+" .subtract ").removeClass('none');
                    $(".modal_show_item_norms_"+id+" .show_item_id_mnum ").removeClass('none');
                    $(".modal_show_item_norms_"+id+" .show_item_id_mnum ").text(val);
                    var m = val * prs;
                    $(".modal_show_item_norms_"+id+" .money_toal").html(prs);

                }
                $(".modal_show_item_norms_"+id+" .msg_show").addClass("none");
                return false;
            });
            $(document).on('click','.y-paytop .icon', function () {
                $(".modal").removeClass("modal-in").addClass("modal-out").remove();
                $(".modal-overlay").removeClass("modal-overlay-visible");
                $(" .y-ggpsize li").removeClass('active');

            });
            var height = $(".bar-nav").height();
            height *= 2;
            height += $(".x-goodstop").height();
            height += $(".x-notice").height();
            height += $(".x-goodstit").height();
            // height += 19;
            $(".x-goodstab .x-sortlst").css("height",$(window).height()-height);
        })
        $.showItemNorms = function(pid,id,price){
            $(".msg_show"+pid).addClass('none');
            $(".modal_show_item_norms_"+pid +" .y-ggpsize li").removeClass('active');
            $(".show_item_id_"+id +"").addClass('active');
            var val  = $(".modal_show_item_norms_"+pid+" .show_item_id_"+id).attr('data-ns');
            $(".modal_show_item_norms_"+pid +" .show_item_id_mnum").attr('data-normsid',id);
            $(".modal_show_item_norms_"+pid +" .show_item_id_mnum").attr('data-price',price);
            var m = val * price;
            $(".modal_show_item_norms_"+pid+" .money_toal").html(price);

            if(val > 0){
                $(".modal_show_item_norms_"+pid+" .subtract ").removeClass('none');
                $(".modal_show_item_norms_"+pid+" .show_item_id_mnum ").removeClass('none');
                $(".modal_show_item_norms_"+pid+" .show_item_id_mnum ").text(val);
            }else{
                $(".modal_show_item_norms_"+pid+" .subtract ").addClass('none');
                $(".modal_show_item_norms_"+pid+" .show_item_id_mnum ").addClass('none');
                $(".modal_show_item_norms_"+pid+" .show_item_id_mnum ").text(0);
                $(".modal_show_item_norms_"+pid+" .money_toal").html(price);
            }

        }
        $(".content").css("bottom",0);

        // $(".x-goodstab .x-sortlst").css("height",$(window).height()-210);
        <?php
        $cartgoods = [];
        
        foreach($cart["data"]["goods"] as $good)
        {
            $cartgoods[$good["goodsId"]][$good["normsId"] ? $good["normsId"] : "null"] = ["num"=>$good["num"], "price"=>$good["price"]];
        }
        
        echo "var cartgoods = ";
        echo json_encode((array)$cartgoods);
        echo ";"
        ?>

        // 处理返回值
        function HandleResult(res)
        {
            if (res.code < 0)
            {
                $.alert("请登录", function(){
                    setTimeout(function () { $.router.load("{{u('User/login')}}", true); }, 2000);
                });
                
            }
            else if (res.code > 0)
            {
                $.alert(res.msg);
            }

            return false;
        }
        // 减少数量
        $(document).on("touchend", ".subtract", function ()
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

            $.post("{{u('Goods/saveCart')}}", { goodsId: sender.data("goodsid"), normsId: sender.data("normsid"), num: value, serviceTime: 0 }, function(res){
                if(res.code == 0){
                    sender.html(value);
                    CalculationTotal(sender.data("goodsid"), sender.data("normsid"), value, parseFloat(sender.data("price")));
                    $(".show_item_norms_"+sender.data("goodsid") +" .show_item_id_mnum").attr('data-normsid',sender.data("normsid"));
                    $(".show_item_norms_"+sender.data("goodsid") +" .show_item_id_mnum").attr('data-price',sender.data("price"));
                    var m = value * sender.data("price");
                    $(".modal_show_item_norms_"+sender.data("goodsid")+" .money_toal").html(sender.data("price"));
                    if(value == 0){
                        $(".show_item_norms_"+sender.data("goodsid") +" .show_item_id_mnum").text(0);
                        $(".show_item_norms_"+sender.data("goodsid") +" .subtract").addClass("none");
                        $(".show_item_id_"+sender.data("normsid")).attr("data-ns",0);
                        $(".modal_show_item_norms_"+sender.data("goodsid")+" .money_toal ").html(sender.data("price"));
                    }
                }
                HandleResult(res);
            } );


        });
        // 添加数量
        $(document).on("touchend", ".add", function ()
        {
            var thisVal = $(this);

            var sender = thisVal.siblings(".val")

            var value = parseInt(sender.html()) + 1;

            $.post("{{u('Goods/saveCart')}}", { goodsId: sender.data("goodsid"), normsId: sender.data("normsid"), num: value, serviceTime: 0 }, function(res){
                if(res.code == 0){
                    sender.html(value);
                    CalculationTotal(sender.data("goodsid"), sender.data("normsid"), value, parseFloat(sender.data("price")));
                    $(".show_item_norms_"+sender.data("goodsid") +" .show_item_id_mnum").attr('data-normsid',sender.data("normsid"));
                    $(".show_item_norms_"+sender.data("goodsid") +" .show_item_id_mnum").attr('data-price',sender.data("price"));
                    $(".show_item_norms_"+sender.data("goodsid") +" .show_item_id_mnum").text(value);
                    var m = value * sender.data("price");
                    $(".modal_show_item_norms_"+sender.data("goodsid")+" .money_toal").html(sender.data("price"));
                    thisVal.siblings().removeClass("none");
                    $(".show_item_id_"+sender.data("normsid")).attr("data-ns",value);
                }else if(res.code == -1){
                    $(".show_item_norms_item,.modal-overlay").remove();
                    $('.modal_show_item_norms_'+sender.data("goodsid")).remove();
                    $.router.load("{{u('User/login')}}", true);
                    return;
                }else{
                    $(".msg_show"+sender.data("goodsid")).removeClass('none');
                }
                HandleResult(res);
            } );

        });
        // 计算合计
        function CalculationTotal(goodsid, normsId, num, price)
        {
            if (typeof(cartgoods[goodsid]) == "undefined")
            {
                cartgoods[goodsid] = new Object();
            }

            if (normsId == "0") normsId = "null";

            cartgoods[goodsid][normsId] = { num: num, price: price };

            var totalAmount = 0;

            var totalPrice = 0.0;

            for(var goods in cartgoods)
            {
                for (var item in cartgoods[goods])
                {
                    totalAmount += parseInt(cartgoods[goods][item].num);

                    totalPrice += cartgoods[goods][item].num * cartgoods[goods][item].price;
                }
            }

            $("#cartTotalAmount").html(totalAmount);

            $("#cartTotalPrice").html(totalPrice.toFixed(2));

            var serviceFee = "{{ $seller['serviceFee'] }}";
            if (totalPrice < serviceFee) {
                var differFee = parseFloat(serviceFee) - parseFloat(totalPrice);
                $(".choose_complet").removeClass("c-bg").addClass("c-gray97").html("还差￥" + differFee.toFixed(2));
            } else {
                $(".choose_complet").removeClass("c-gray97").addClass("c-bg").html('选好了');
            }
        }
        $(document).on("touchend",".x-goodsb",function(){
            if ($(this).hasClass("active"))
            {
                $(this).removeClass("active");
                $(this).parent().siblings(".showgoods").addClass("none");
                $(this).find(".up").removeClass("none");
                $(this).find(".down").addClass("none");
            }
            else
            {
                $(this).addClass("active");
                $(this).parent().siblings(".showgoods").removeClass("none");
                $(this).find(".up").addClass("none");
                $(this).find(".down").removeClass("none");
            }
            $(this).parents("li").addClass("none");
        });

    </script>
@stop 