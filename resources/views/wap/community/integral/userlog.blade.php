@extends('wap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">兑换记录</h1>
    </header>
@stop
@section('css')
@stop

@section('content')
    <div class="content infinite-scroll infinite-scroll-bottom pull-to-refresh-content" data-ptr-distance="55" data-distance="50" id="">
        <div class="pull-to-refresh-layer">
            <div class="preloader"></div>
            <div class="pull-to-refresh-arrow"></div>
        </div>
        <div class="list-block media-list y-sylist">
            <ul class="y-jfscmain_show">
                @include("wap.community.integral.log_item")
            </ul>
        </div>

        <!-- 未开通物业提示 -->
        <div class="x-null pa w100 tc content-block-title-show-time @if(count($list) < 1) show @else none @endif">
            <img src="{{asset('wap/community/newclient/images/nothing.png')}}" width="108">
            <p class="f12 c-gray mt10">暂无兑换记录</p>
        </div>
        <div class="content-block-title-show content-block-title tc c-gray2 none">没有更多了...</div>
    </div>
@stop
@section($js)
    <script type="text/javascript">
        //精确定位
        $(function(){

            // 上拉加载
            var groupLoading = false;


            var groupPageIndex = 2;
            $(document).off('infinite', '.infinite-scroll-bottom');
            $(document).on('infinite', '.infinite-scroll-bottom', function() {
                // 如果正在加载，则退出
                if (groupLoading) {
                    return false;
                }
                groupLoading = true;

                $('.content-block-title-show').removeClass('none');
                $.pullToRefreshDone('.pull-to-refresh-content');

                var data = new Object;
                data.page = groupPageIndex;
                data.tpl = "item";

                $.post("{{ u('Integral/userlog') }}", data, function(result){
                    groupLoading = false;
                    if (result != '') {
                        groupPageIndex++;
                        $('.page-current .y-jfscmain_show').append(result);
                        $(".page-current .content-block-title-show-time").addClass('none');
                        $.refreshScroller();
                    }else{
                        $(".page-current .content-block-title-show").removeClass('none');
                    }
                });
            });
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
                data.tpl = "log_item";

                $.post("{{ u('Integral/userlog') }}", data, function(result){
                    groupLoading = false;
                    if (result != "") {
                        $(".page-current .content-block-title-show-time").addClass('none');
                        groupPageIndex = 2;
                    }else{
                        $(".page-current .content-block-title-show-time").removeClass('none');
                    }
                    $('.page-current .y-jfscmain_show').html(result);
                    $.pullToRefreshDone('.pull-to-refresh-content');
                });
            });
            $.init();
        });
    </script>
@stop