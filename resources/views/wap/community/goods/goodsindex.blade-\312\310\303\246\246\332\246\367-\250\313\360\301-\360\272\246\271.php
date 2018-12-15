@extends('wap.community._layouts.base')

@section($css)
    <style type="text/css">
        /*.tabs .tab{display: block;}*/
        .y-scr{width: 25%;overflow: hidden;}
        .y-scroll{margin-right: -10px;}
        .y-scr p{line-height: 40px;display: block;text-align: center;border-bottom: 1px solid #ccc;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;}
        .y-scr p.active{color: #ff2d4b;background: #fff;}
        .tabs{overflow-y: scroll;overflow-x: hidden;}
        .x-goodstab .x-sortlst.list-block{overflow: hidden;}
    </style>
@stop

@section('show_top')
    @include('wap.community.goods.sellergoodshead')
    @include('wap.community._layouts.base_cart')
@stop

@section('content')
<script type="text/javascript">
    //BACK_URL = "{!! $nav_back_url !!}";
</script>
    <?php
    $cartgoods = [];
    foreach($cart["data"]["goods"] as $good)
    {
        $cartgoods[$good["goodsId"]][$good["normsId"]]  = ["num"=>$good["num"], "price"=>$good["price"]];
        //$cartgoods[$good["goodsId"]][$good["normsId"]] = $good["num"];
    }
    ?>
    <div class="content" id=''>
        <!-- 菜单列表 -->
        <div class="x-sjfltab x-goodstab clearfix">
            <div class="y-scr fl pr" id="scroll_menu">
                <div class="y-scroll">
                    <?php $leftsort = 0; ?>
                    @foreach($cate as $ckey => $item)
                        @if(count($item['goods']) > 0)
                            <p href="#tab_{{$ckey}}"  class="herfid{{$ckey}} @if($item['id'] == Input::get('cateId')) active @else @if(Input::get('cateId') == "" && $leftsort == 0) active @endif  @endif">{{$item['name']}}</p>
                            <?php $leftsort++; ?>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="tabs c-bgfff fl">
                <?php $leftsort = 0;
                $i = 0;
                ?>
                @foreach($cate as $ckey => $item)
                    @if(count($item['goods']) > 0)
                        <div id="tab_{{$ckey}}" data-tabid = "{{$ckey}}" class="y-tab @if($item['id'] == Input::get('cateId')) active @else @if(Input::get('cateId') == "" && $leftsort == 0) active @endif  @endif">
                            <div class="x-goodstit">
                                <div class="item-title f15 c-gray">
                                    @if($item['id'] == Input::get('cateId')) {{$item['name']}}({{count($item['goods'])}}) @else {{$item['name']}}({{count($item['goods'])}}) @endif
                                </div>
                            </div>
                            <div class="list-block media-list x-sortlst f14 nobor pr "><!--y-pull pull-to-refresh-content-->
                                <ul>
                                    @foreach($item['goods'] as $k=>$v)
                                        <?php
                                        //存在规格和折扣 获取规格最低价 根据折扣结算出新的特价
                                        if(count($v['norms']) > 0 && !empty($v['activity']))
                                        {
                                            $f = true;
                                            foreach ($v['norms'] as $key => $value) {
                                                $salePrice = $value['price'] * $v['activity']['sale'] / 10;

                                                $v['norms'][$key]['salePrice'] = $salePrice;

                                                if($f)
                                                {
                                                    $v['activity']['minNormsPrice'] = $salePrice;
                                                    $v['price'] = $value['price'];
                                                    $f = false;
                                                }
                                                elseif(!$f && $salePrice <= $v['activity']['minNormsPrice'])
                                                {
                                                    $v['activity']['minNormsPrice'] = $salePrice; //最低折扣价
                                                    $v['price'] = $value['price']; //最低原价
                                                }

                                            }
                                        }

                                        ?>
                                        <li class="item-content">
                                            <div class="item-inner pl0">
                                                <div class="item-title">
                                                    <div onclick="$.href('{{u('Goods/detail',['goodsId'=>$v['id'],'type'=>$v['type']])}}')">
                                                        <div class="goodspic fl mr5">
                                                            <img class="lazyload" data-original="@if($v['image']) {{ formatImage($v['image'],150,150) }} @else {{ asset('wap/community/client/images/wykdimg.png') }} @endif">
                                                        </div>
                                                        <span class="goodstit">{{$v['name']}}</span>
                                                    </div>
                                                    <div class="mt5">
                                                        <span class="c-red f15">
                                                            @if(empty($v['activity']))
                                                                ￥{{number_format($v['price'], 2)}}
                                                            @else
                                                                @if(empty($v['activity']['minNormsPrice']))
                                                                    ￥{{number_format($v['activity']['salePrice'], 2)}} <!-- 折扣价 -->
                                                                @else
                                                                    ￥{{number_format($v['activity']['minNormsPrice'], 2)}} <!-- 规格最低价 -->
                                                                @endif
                                                                <del class="f12 c-gray ml5">￥{{number_format($v['price'], 2)}}</del>
                                                            @endif
                                                        </span>
                                                        @if($seller['serviceTimesCount'] > 0)
                                                            @if(count($v['norms']) < 1)
                                                                <div class="x-num fr  goodsId_show{{$v['id']}}">
                                                                    <i class="icon iconfont c-gray subtract fl <?php if(empty($cartgoods[$v['id']][0]['num'])) echo "none"; ?>">&#xe622;</i>
                                                                    <span class="val tc pl0 fl <?php if(empty($cartgoods[$v['id']][0]['num'])) echo "none"; ?>" data-goodsid="{{$v['id']}}" data-normsid="0" data-price="{{ round($v['price'], 2) }}" data-saleprice="{{ round($v['activity']['salePrice'], 2) }}"><?php if(empty($cartgoods[$v['id']][0]['num'])) echo "0"; else echo$cartgoods[$v['id']][0]['num']; ?></span>
                                                                    <i class="icon iconfont c-red add fl">&#xe61f;</i>
                                                                </div>
                                                            @else
                                                                <div class="fr c-red f12 y-xgg totalPrice" data-ids="{{$v['id']}}" data-name="{{$v['name']}}">选规格</div>
                                                            @endif
                                                        @else
                                                            <span class="c-gray f12 fr">商家休息中</span>
                                                        @endif

                                                        @if(!empty($v['activity']))
                                                            <div class="y-specialprice f12"><a href="" class="f12">{{$v['activity']['sale']}}折特价</a></div>
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
                                                            <li class="@if($nk == 0) active @endif show_item_id_{{ $n['id']}}" data-ns="{{$cartgoods[$v['id']][$n['id']]['num']  or 0 }}" data-salePrice="{{round($n['salePrice'], 2)}}" data-prs="{{$n['price']}}" onclick='$.showItemNorms({{$v['id']}},"{{ $n['id'] }}","{{round($n['price'], 2)}}", "{{round($n['salePrice'], 2)}}")'>
                                                                <a href="#">{{$n['name']}}</a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                <div class="y-gmnum  pb10 clearfix">
                                                    @if(empty($v['activity']))
                                                        <span class="f14 c-red">
                                                            ￥<span class="money_toal" id="money_toal_{{$v['id']}}">{{ number_format($v['norms'][0]['price'], 2) }}</span>
                                                        </span>
                                                    @else
                                                        <span class="f14 c-red">
                                                            ￥<span class="money_toal" id="money_toal_{{$v['id']}}">{{ number_format($v['norms'][0]['salePrice'], 2) }}</span>
                                                        </span>
                                                        <del class="c-gray f12 ml5">￥<span class="delPrice">{{ number_format($v['norms'][0]['price'], 2) }}</span></del>
                                                    @endif

                                                    <span class="f14 msg_show msg_show{{$v['id']}} none" style="color: red;font-size: 0.2rem !important;">抱歉：商品库存不足</span>
                                                    <div class="y-num fr goodsId_show{{$v['id']}} " >
                                                        <i class="icon  iconfont c-gray subtract fl <?php if(empty($cartgoods[$v['id']][$v['norms'][0]])) echo "none"; ?>">&#xe621;</i>
                                                        <span class="show_item_id_mnum val tc pl0 fl <?php if(empty($cartgoods[$v['id']][$v['norms'][0]])) echo "none"; ?>" data-newold="false"  data-goodsid="{{$v['id']}}" data-normsid="{{$v['norms'][0]['id']}}" data-price="{{ round($v['norms'][0]['price'], 2) }}" data-saleprice="{{ round($v['norms'][0]['salePrice'], 2) }}"><?php if(empty($cartgoods[$v['id']][$v['norms'][0]])) echo "0"; else echo$cartgoods[$v['id']][$v['norms'][0]['id']]; ?></span>
                                                        <i class="icon iconfont c-red add fl">&#xe61e;</i>
                                                    </div>
                                                    @if(!empty($v['activity']))
                                                        <div class="y-specialprice f12 ml0"><a href="">{{$v['activity']['sale']}}折特价</a></div>
                                                    @endif
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
    @include('wap.community.goods.share')
@stop

@section("ajax")
    <script src="{{ asset('wap/community/client/js/cel.js') }}"></script>
    <script src="{{ asset('wap/community/newclient/js/jquery.lazyload.js') }}"></script>
    <script type="text/javascript">
        Zepto(function($){

            $("img.lazyload").lazyload({
                placeholder:"{{asset('wap/community/newclient/images/loading.gif')}}"
            });

            //导航和content位置
            var toph = $(".y-sjlistnav").height();
            $(".bar-header-secondary").css("top",toph);
            toph += $(".bar-header-secondary").height();
            $(".content").css({"bottom":0,"top":toph+1,"overflow":"hidden"});
            //菜单高度
            var height = $(".bar-footer").height();
            height += toph;
            $(".y-scroll").css("height",$(window).height()-height);
            $(".tabs").css("height",$(window).height()-height);

            //菜单点击
            var is_do = 0;
            $(document).on('click','.y-scroll p', function(){
                var id = $(this).attr("href");
                var topheight = $(id).offset().top+$('.tabs').scrollTop()-$(".y-sjlistnav").height()-$(".bar-header-secondary").height();
                //alert($(id).offset().top+"/"+$('.tabs').scrollTop()+"/"+$(this).attr("href"))
                is_do = 1;
                if(id == "#tab1_1"){
                    $('.tabs').scrollTop(0);
                    // $('.tabs').animate({scrollTop:0}, 600);
                }else{
                    $('.tabs').scrollTop(topheight);
                    // $('.tabs').animate({scrollTop:topheight}, 600);
                }
                $('.y-scroll p').removeClass("active");
                $(this).addClass("active");
            });

            //滑动显示头部
            var start = 0,move = 0;
            $(document).off('touchstart',".tabs");
            $(document).on('touchstart',".tabs", function (e) {
                var point = e.touches ? e.touches[0] : e;
                start = point.screenY;
            });

            var startnavh = $(".y-sjlistnav").height();
            // if($(window).width() >= 414){
            //     startnavh = 160;
            // }
            var bhsh = $(".bar-header-secondary").height();
            var scrobox = '';
            $(document).off('touchmove',".tabs");
            $(document).on('touchmove',".tabs", function (e) {
                var point = e.touches ? e.touches[0] : e;
                move = point.screenY;
                var s = move - start, nav = $(".y-sjlistnav"), navh = nav.height(), navimg = nav.find(".y-sjxq .item-media img");
                if(navh >= 44 && navh <= startnavh){
                    var y;
                    $(".tabs").scroll(function(){
                        scrobox = $(this).scrollTop();
                        if(is_do == 1){
                            return false;
                        }else{
                            $(".y-tab").each(function(){
                                var topz = $("#tab_"+$(this).attr('data-tabid')+" .x-goodstit").offset().top;
                                if(topz != null){
                                    if(topz<= 200 && topz >= 0 ){
                                        y = $(this).attr('data-tabid');
                                    }
                                }
                            });
                            $(".y-scroll .herfid"+y).addClass("active").siblings().removeClass("active");
                        }
                    });
                    is_do = 0;
                    if(s > 0  && scrobox > 90){
                    }else{
                        //nav高度
                        var navheight = navh+s;
                        if(navheight >= startnavh) navheight = startnavh;
                        if(navheight <= 44) navheight = 44;
                        nav.css("height", navheight);
                        //nav里面的图片
                        var logow = navimg.attr("width")*1+s/2;
                        if(logow >= 45) logow = 45;
                        if(logow <= 0) logow = 0;
                        navimg.attr("width",logow);
                        //透明度
                        $(".y-opacity").css("opacity",navheight/startnavh);
                        //导航
                        $(".bar-header-secondary").css("top",navheight);
                        $(".content").css("top",bhsh+navheight+1);
                        //内容高度
                        var main = $(window).height()-navheight-bhsh-$(".bar-footer").height();
                        $(".y-scroll").css("height",main);
                        $(".tabs").css("height",main);
                    }
                }
            });
            $.showTop = function(){
                var top = $(".tabs .active").offset().top+$('.tabs').scrollTop()-$(".y-sjlistnav").height()-$(".bar-header-secondary").height();
                $('.tabs').scrollTop(top);
            }
            $.showTop();
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
                var salePrice  = $(".modal_show_item_norms_"+id+" .y-ggpsize li").eq(0).data('saleprice');
                if(salePrice <= 0){
                    prs = prs;
                }else{
                    prs = salePrice;
                }
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
        })
        $.showItemNorms = function(pid,id,price,salePrice){

            $(".msg_show"+pid).addClass('none');
            $(".modal_show_item_norms_"+pid +" .y-ggpsize li").removeClass('active');
            $(".show_item_id_"+id +"").addClass('active');
            var val  = $(".modal_show_item_norms_"+pid+" .show_item_id_"+id).attr('data-ns');
            $(".modal_show_item_norms_"+pid +" .show_item_id_mnum").attr('data-normsid',id);
            $(".modal_show_item_norms_"+pid +" .show_item_id_mnum").attr('data-price',price);
            $(".modal_show_item_norms_"+pid +" .show_item_id_mnum").attr('data-salePrice',salePrice);
            var m = val * price;
            if(salePrice > 0)
            {
                //特价商品
                $(".modal_show_item_norms_"+pid+" .money_toal").html(salePrice);
                $(".modal_show_item_norms_"+pid+" .delPrice").html(price);
            }
            else
            {
                //正常商品
                $(".modal_show_item_norms_"+pid+" .money_toal").html(price);
            }

            if(val > 0){
                $(".modal_show_item_norms_"+pid+" .subtract ").removeClass('none');
                $(".modal_show_item_norms_"+pid+" .show_item_id_mnum ").removeClass('none');
                $(".modal_show_item_norms_"+pid+" .show_item_id_mnum ").text(val);
            }else{
                $(".modal_show_item_norms_"+pid+" .subtract ").addClass('none');
                $(".modal_show_item_norms_"+pid+" .show_item_id_mnum ").addClass('none');
                $(".modal_show_item_norms_"+pid+" .show_item_id_mnum ").text(0);
                if(salePrice > 0)
                {
                    //特价商品
                    $(".modal_show_item_norms_"+pid+" .money_toal").html(salePrice);
                    $(".modal_show_item_norms_"+pid+" .delPrice").html(price);
                }
                else
                {
                    //正常商品
                    $(".modal_show_item_norms_"+pid+" .money_toal").html(price);
                }
            }

        }

        <?php
        echo "var cartgoods = ";
        echo json_encode((array)$cartgoods);
        echo ";"
        ?>

        // 处理返回值
        function HandleResult(res)
        {
            if (res.code < 0)
            {
                // $.toast("请登录", function(){
                    // setTimeout(function () { $.router.load("{{u('User/login')}}", true); }, 2000);
                // }); 
				alert('您未登录，无法加入购物车');
				window.location.href = "{{u('User/login')}}";
            }
            else if (res.code > 0)
            {
                $.toast(res.msg);
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
            $.post("{{u('Goods/saveCart')}}", { sellerId:"{{Input::get('id')}}",type:"{{Input::get('type')}}",goodsId: sender.data("goodsid"), normsId: sender.data("normsid"), num: value, serviceTime: 0 }, function(res){
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
                        $(".show_item_norms_"+sender.data("goodsid") +" .subtract").addClass("none");
                        $(".show_item_id_"+sender.data("normsid")).attr("data-ns",0);
                    }
                }
                HandleResult(res);
            } );
        });
        // 添加数量
        $(document).on("touchend", ".add", function ()
        {
            var thisVal = $(this);

            var sender = thisVal.siblings(".val");

            var value = parseInt(sender.html()) + 1;

            $.post("{{u('Goods/saveCart')}}", {sellerId:"{{Input::get('id')}}",type:"{{Input::get('type')}}", goodsId: sender.data("goodsid"), normsId: sender.data("normsid"), num: value, serviceTime: 0 }, function(res){
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

                }else{
                    $(".msg_show"+sender.data("goodsid")).removeClass('none');
                }
                HandleResult(res);
            } );

        });
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

        $(document).on("touchend",".collect_opration .collect",function(){
            var obj = new Object();
            var collect = $(this);
            obj.id = "{{$seller['id']}}";
            obj.type = 2;
            if(collect.hasClass("on")){
                $.post("{{u('UserCenter/delcollect')}}",obj,function(result){
                    if(result.code == 0){
                        collect.removeClass("on");
                        $.toast(result.msg,function(){
                            collect.html('&#xe653;');
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
                        $.toast(result.msg,function(){
                            collect.html('&#xe654;');
                        });
                    } else if(result.code == 99996){
                        $.router.load("{{u('User/login')}}", true);
                    } else {
                        $.toast(result.msg);
                    }
                },'json');
            }
        });
    </script>
@stop 