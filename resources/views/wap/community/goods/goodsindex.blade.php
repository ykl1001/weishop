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
                        @if($item['goodscounts'] > 0)
                            <p href="#tab_{{$ckey}}" data-id="{{$item['id']}}"  class="herfid{{$ckey}} @if($item['id'] == Input::get('cateId')) active @else @if(Input::get('cateId') == "" && $leftsort == 0) active @endif  @endif">{{$item['name']}}</p>
                            <?php $leftsort++; ?>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="tabs c-bgfff fl" id="wdddmain">
                @include('wap.community.goods.goodsindex_item')
            </div>
            <div class="pa w100 tc allEnd none">
                <p class="f12 c-gray mt5 mb5">没有更多了</p>
            </div>
            <!-- 加载提示符 -->
            <div class="infinite-scroll-preloader none">
                <div class="preloader"></div>
            </div>
        </div>
    </div>
    @include('wap.community._layouts.base_stock')
    @include('wap.community.goods.share')
@stop

@section("js_ajax")
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
            var groupPageIndex = 2;
            $(document).on('click','.y-scroll p', function(){
                $('.tabs').scrollTop(0);
                $('.y-scroll p').removeClass("active");
                $(this).addClass("active");
                groupPageIndex = 2;
                var groupPageIndexCate = 0;
                var data = new Object;
                data.page = groupPageIndexCate;
                data.type = 1;
                data.ajax = 1;
                data.id = "{{Input::get('id')}}";
                data.cateId = $(this).attr('data-id');

                $('#wdddmain').html("").css('height','0');
                $('.infinite-scroll-preloader').removeClass('none');
                $.post("{{ u('Goods/sellergoods_list') }}", data, function(result){
                    groupLoading = false;
                    $('.infinite-scroll-preloader').addClass('none');
                    result  = $.trim(result);
                    if (result != '') {
                        $('#wdddmain').html(result);
                        var height = $(".bar-footer").height();
                        height += toph;
                        $(".tabs").css("height",$(window).height()-height);

                        $.refreshScroller();
                    }
                });
            });
            //上拉
            //滑动到底部触发
            $(".tabs").scroll(function() {
                viewH =$(this).height(),//可见高度
                        contentH =$(this).get(0).scrollHeight,//内容高度
                        scrollTop =$(this).scrollTop();//滚动高度

                var groupLoading = true;
                var nopost = 0;
                if (contentH - viewH - scrollTop <= 1) {
                    var data = new Object;
                    data.page = groupPageIndex;
                    data.type = 1;
                    data.ajax = 1;
                    data.id = "{{Input::get('id')}}";
                    var cateId = 0;
                    $(".y-scroll p").each(function(){
                        if($(this).hasClass('active')){
                            cateId = $(this).attr('data-id')
                        }
                    })
                    data.cateId = cateId;

                    $('.infinite-scroll-preloader').removeClass('none');
                    $.post("{{ u('Goods/sellergoods_list') }}", data, function(result){
                        groupLoading = false;
                        $('.infinite-scroll-preloader').addClass('none');
                        result  = $.trim(result);
                        if (result != '') {
                            groupPageIndex++;
                            $('#wdddmain').append(result);

                            $.refreshScroller();
                        }
                    });
                }
            });

            //滑动显示头部
            var start = 0,move = 0;
            $(document).off('touchstart',".tabs");
            $(document).on('touchstart',".tabs", function (e) {
                var point = e.touches ? e.touches[0] : e;
                start = point.screenY;
            });

            var startnavh = $(".y-sjlistnav").height();
            var bhsh = $(".bar-header-secondary").height();
            var scrobox = '';
            $(document).off('touchmove',".tabs");
            $(document).on('touchmove',".tabs", function (e) {
                var point = e.touches ? e.touches[0] : e;
                move = point.screenY;
                var s = move - start, nav = $(".y-sjlistnav"), navh = nav.height(), navimg = nav.find(".y-sjxq .item-media img");
                if(navh >= 44 && navh <= startnavh){
                    var y;
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
            // $(document).on('click','.y-paytop .icon', function () {
            // $(".modal").removeClass("modal-in").addClass("modal-out").remove();
            // $(".modal-overlay").removeClass("modal-overlay-visible");
            // $(" .y-ggpsize li").removeClass('active');
            // });
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
        $(document).off("touchend", ".subtract");
        $(document).on("touchend", ".subtract", function ()
        {
            var thisVal = $(this);

            var sender = thisVal.siblings(".val");

            var value = parseInt(sender.html()) - 1;
            $(".msg_show"+sender.attr("data-goodsid")).addClass('none');

            if (value <= 0)
            {
                value = 0;

                $(this).siblings(".add").siblings().addClass("none");
            }
            $.post("{{u('Goods/saveCart')}}", { sellerId:"{{Input::get('id')}}",type:"{{Input::get('type')}}",goodsId: sender.attr("data-goodsid"), skuSn: sender.attr("data-normsid"), num: value, serviceTime: 0 }, function(res){
                if(res.code == 0){
                    var pr = 0;
                    sender.html(value);
                    if(sender.attr("data-saleprice") <= 0){
                        pr = sender.attr("data-price");
                    }else{
                        pr = sender.attr("data-saleprice");
                    }
                    var newNormId = 0;
                    if(sender.data("normsid") != 0){
                        newNormId = sender.data("normsid").replace(/:/g, '_');
                    }
                    CalculationTotal(sender.attr("data-goodsid"), sender.attr("data-normsid"), value, parseFloat(pr),res,sender.attr("data-newold"));
                    $(".show_item_norms_"+sender.attr("data-goodsid") +" .show_item_id_mnum").attr('data-normsid',sender.attr("data-normsid"));
                    $(".show_item_norms_"+sender.attr("data-goodsid") +" .show_item_id_mnum").attr('data-price',sender.attr("data-price"));
                    if(value == 0){
                        $(".show_item_norms_"+sender.data("goodsid") +" .show_item_id_mnum").text(0);
                        $(".show_item_norms_"+sender.data("goodsid") +" .subtract").addClass("none");
                        $(".show_item_id_"+newNormId).attr("data-ns",0);
                    }
                    HandleResult(res);
                }
            } );


        });
        // 添加数量
        $(document).off("touchend", ".add");
        $(document).on("touchend", ".add", function ()
        {
            var thisVal = $(this);

            var sender = thisVal.siblings(".val");

            var value = parseInt(sender.html()) + 1;
            //$.showPreloader();
            $.post("{{u('Goods/saveCart')}}", {sellerId:"{{Input::get('id')}}",type:"{{Input::get('type')}}", goodsId: sender.attr("data-goodsid"), skuSn: sender.attr("data-normsid"), num: value, serviceTime: 0 }, function(res){
                //$.hidePreloader();
                if(res.code == 0){
                    var pr = 0;
                    sender.html(value);
                    if(sender.attr("data-saleprice") <= 0){
                        pr = sender.attr("data-price");
                    }else{
                        pr = sender.attr("data-saleprice");
                    }
                    var newNormId = 0;
                    if(sender.data("normsid") != 0){
                        newNormId = sender.data("normsid").replace(/:/g, '_');
                    }
                    CalculationTotal(sender.attr("data-goodsid"), sender.attr("data-normsid"), value, parseFloat(pr),res,sender.attr("data-newold"));
                    $(".show_item_norms_"+sender.attr("data-goodsid") +" .show_item_id_mnum").attr('data-normsid',sender.attr("data-normsid"));
                    $(".show_item_norms_"+sender.attr("data-goodsid") +" .show_item_id_mnum").attr('data-price',sender.attr("data-price"));
                    $(".show_item_norms_"+sender.attr("data-goodsid") +" .show_item_id_mnum").text(value);
                    thisVal.siblings().removeClass("none");
                    $(".show_item_id_"+newNormId).attr("data-ns",value);

                }else{
                    $(".msg_show"+sender.attr("data-goodsid")).removeClass('none');
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
    </script>
@stop