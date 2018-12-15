@extends('wap.community._layouts.base')

@section('css')
<style type="text/css">
.y-fwmaxw{max-width: 70%;display: inline-block;overflow: hidden;text-overflow:ellipsis;white-space: nowrap;}
</style>
@stop

@section('show_top')
    @include('wap.community.goods.sellergoodshead')
@stop

@section('content')
    <!-- new -->
    <div class="content infinite-scroll infinite-scroll-bottom pull-to-refresh-content" data-ptr-distance="55" data-distance="50" id='goodsServiceindex'>
        <!-- 加载提示符 -->
        <div class="pull-to-refresh-layer">
            <div class="preloader"></div>
            <div class="pull-to-refresh-arrow"></div>
        </div>

        @if(!empty($cate))
            <ul class="x-fwlst pl5 pr5 mt10 clearfix" id="list">
                @include('wap.community.goods.service_item')
            </ul>
        @endif

         <!-- 加载完毕提示 -->
        <div class="pa w100 tc allEnd none">
            <p class="f12 c-gray mt5 mb5">数据加载完毕</p>
        </div>
        <!-- 加载提示符 -->
        <div class="infinite-scroll-preloader none">
            <div class="preloader"></div>
        </div>
    </div>	
    @include('wap.community.goods.share')
@stop

@section($js)
    <script type="text/javascript">
        $(function() {
            //导航和content位置
            var toph = $(".y-sjlistnav").height();
            $(".bar-header-secondary").css("top",toph);
            toph += $(".bar-header-secondary").height();
            var sxheight = $(".pull-to-refresh-layer").height();
            $("#goodsServiceindex").css({"bottom":0,"top":toph-sxheight+1});

            // 加载开始
            // 上拉加载
            var serviceIndexLoading = false;
            var serviceIndexNum = 2;
            $(document).off('infinite', '.infinite-scroll-bottom');
            $(document).on('infinite', '.infinite-scroll-bottom', function() {
                // 如果正在加载，则退出
                if (serviceIndexLoading) {
                    return false;
                }
                //隐藏加载完毕显示
                $(".allEnd").addClass('none');

                serviceIndexLoading = true;

                $('.infinite-scroll-preloader').removeClass('none');
                $.pullToRefreshDone('.pull-to-refresh-content');

                var data = new Object;
                data.page = serviceIndexNum;
                data.id = "{{$option['id']}}";
                data.type = "{{$option['type']}}";

                $.post("{{ u('Goods/indexList') }}", data, function(result){
                    serviceIndexLoading = false;
                    $('.infinite-scroll-preloader').addClass('none');
                    result  = $.trim(result);
                    if (result != '') {
                        serviceIndexNum++;
                        $('#list').append(result);
                        $.refreshScroller();
                    }else{
                        $(".allEnd").removeClass('none');
                    }
                });
            });

            // 下拉刷新
            $(document).off('refresh', '.pull-to-refresh-content');
            $(document).on('refresh', '.pull-to-refresh-content',function(e) {
                // 如果正在加载，则退出
                if (serviceIndexLoading) {
                    return false;
                }
                serviceIndexLoading = true;
                window.location.reload(true);
            });
            // 加载结束
            
            
            // 弹窗
            $(document).off('click','.y-xgg');
            $(document).on('click','.y-xgg', function () {
                $.modal({
                    title:  '<div class="y-paytop"><i class="icon iconfont c-gray fr">&#xe604;</i><p class="c-black f18 tl">青豆</p></div>',
                    text: '<div class="y-xzggtc tl">\
                                <p class="f14">选择规格</p>\
                                <ul class="y-ggpsize clearfix">\
                                    <li class="active"><a href="#">大盘</a></li>\
                                    <li><a href="#">小盘</a></li>\
                                </ul>\
                            </div>\
                            <div class="y-gmnum clearfix">\
                                <span class="f14 c-red">￥111.00</span>\
                                <div class="y-num fr">\
                                    <i class="icon iconfont c-gray subtract fl none">&#xe621;</i>\
                                    <span class="val tc pl0 fl none">0</span>\
                                    <i class="icon iconfont c-red add fl">&#xe61e;</i>\
                                </div>\
                            </div>',
                   
                })
                return false;
            });
            $(document).off('click','.y-paytop .icon');
            $(document).on('click','.y-paytop .icon', function () {
                $(".modal").removeClass("modal-in").addClass("modal-out").remove();
                $(".modal-overlay").removeClass("modal-overlay-visible");
            });
            // 数量加载
            $(document).off('click','.subtract');
            $(document).on('click','.subtract', function () {
                var text = $(this).siblings(".val").text();
                text = parseInt(text) - 1;
                if(text <= 0){
                    $(this).siblings(".add").siblings().addClass("none");
                    text = 0;
                }
                $(this).siblings(".val").text(text);
            });
            $(document).off('click','.add');
            $(document).on('click','.add', function () {
                var text = $(this).siblings(".val").text();
                text = parseInt(text) + 1;
                $(this).siblings(".val").text(text);
                $(this).siblings().removeClass("none");
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
                            $.alert(result.msg,function(){
                                collect.html('&#xe653;');
                            });
                            
                        } else if(result.code == 99996){
                            $.router.load("{{u('User/login')}}", true);
                        } else {
                            $.alert(result.msg);
                        }
                    },'json');
                }else{
                    $.post("{{u('UserCenter/addcollect')}}",obj,function(result){
                        if(result.code == 0){
                            collect.addClass("on");
                           $.alert(result.msg,function(){
                                collect.html('&#xe654;');
                            });
                        } else if(result.code == 99996){
                            $.router.load("{{u('User/login')}}", true);
                        } else {
                            $.alert(result.msg);
                        }
                    },'json');
                }
            });
            /*
            $(".content").scroll(function(){
                if($(this).scrollTop() == 0){
                    $(".y-sjxq").removeClass("none");
                    $(".y-sjnotice").removeClass("none");
                    $(".y-sjlistnav").css({"background-size":"100% 100%","height":"auto"});
                    var h = $(".y-sjlistnav").height();
                    $(".bar-header-secondary").css("top",h);
                    h += $(".bar-header-secondary").height();
                    $(this).css("top",h+1);
                }else{
                    $(".y-sjxq").addClass("none");
                    $(".y-sjnotice").addClass("none");
                    $(".y-sjlistnav").css({"background-size":"100%","height":"2.2rem"});
                    var h = $(".pull-left").height();
                    $(".bar-header-secondary").css("top",h);
                    h += $(".bar-header-secondary").height();
                    $(this).css("top",h+1);
                }
            })*/
        });
    </script>
@stop 