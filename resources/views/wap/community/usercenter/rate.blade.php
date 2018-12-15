@extends('wap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left isExternal" href="{{ u('UserCenter/index') }}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">我的评价</h1>
    </header>
@stop
@section('content')
    <div class="content infinite-scroll infinite-scroll-bottom pull-to-refresh-content" data-ptr-distance="55" data-distance="50" id="">
        <div class="pull-to-refresh-layer">
            <div class="preloader"></div>
            <div class="pull-to-refresh-arrow"></div>
        </div>
        <div id="wdddmains">
            @include('wap.community.usercenter.rate_item')
        </div>
        <div class="allEnd content-block-title f12 tc none">没有更多了……</div>
    </div>
@stop
@section($js)
    <script type="text/javascript">
        $(function(){
            BACK_URL = "{{$nav_back_url or u('UserCenter/index')}}";
            // 加载开始
            // 上拉加载
            var groupLoading = false;
            var groupPageIndex = 2;
            $(document).off('infinite', '.infinite-scroll-bottom');
            $(document).on('infinite', '.infinite-scroll-bottom', function() {
                // 如果正在加载，则退出
                if (groupLoading) {
                    return false;
                }
                //隐藏加载完毕显示
                $(".allEnd").addClass('none');

                groupLoading = true;

                $('.infinite-scroll-preloader').removeClass('none');
                $.pullToRefreshDone('.pull-to-refresh-content');

                var data = new Object;
                data.page = groupPageIndex;
                data.top = 1;
                $.reat(data);
            });
            $.reat = function(data){
                data.tpl = "item";
                $.post("{{ u('UserCenter/rate') }}", data, function(result){
                    groupLoading = false;
                    $('.infinite-scroll-preloader').addClass('none');
                    if (result != '') {
                        if(data.top == 0){
                            groupPageIndex = 2;
                            $('#wdddmains').html(result);
                            $.pullToRefreshDone('.pull-to-refresh-content');
                        }else{
                            groupPageIndex++;
                            $('#wdddmains').append(result);
                            $.refreshScroller();
                        }
                    }else{
                        $(".allEnd").removeClass('none');
                    }
                });
            }
            //下拉刷新
            $(document).off('refresh', '.pull-to-refresh-content');
            $(document).on('refresh', '.pull-to-refresh-content',function(e) {
                // 如果正在加载，则退出
                if (groupLoading) {
                    return false;
                }
                groupLoading = true;
                var data = new Object;
                data.page = 1;
                data.top =0;
                $.reat(data);
            });
            //js刷新
            //$.pullToRefreshTrigger('.pull-to-refresh-content');
            //加载结束
            var images = new Array();
            //点击时打开图片浏览器
            $(document).on('click','.page-current .pb-standalone',function () {
                images = [];
                $(".page-current .img"+$(this).data("ids")).each(function(i,v){
                    images[i] = $(this).attr("_src");
                });
                var myPhotoBrowserStandalone = $.photoBrowser({
                        photos :  images
                    }
                );
                //移除上一次加载
                $(".photo-browser").remove();
                myPhotoBrowserStandalone.open();
                $(".photo-browser-close-link").addClass("pull-right").removeClass("pull-left").removeClass("icon-left").addClass("iconfont").html('&#xe604;');
            });

            //点击时打开图片浏览器
            $(document).on('click','.pull-right',function () {
                $(".photo-browser").remove();
            });
            $.init();

            //部分IOS返回刷新
            if($.device['os'] == 'ios')
            {
                $(".isExternal").addClass('external');
            }
        });
    </script>
@stop



