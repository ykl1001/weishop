@extends('wap.community._layouts.base')

@section('show_top')
    <style>
        .bar .icon{line-height: 2.2rem;}
    </style>
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading" href="{{u("Invitation/index")}}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a> 
        <h1 class="title f16">奖励记录</h1>
    </header>
@stop
@section('content')
    <div class="content pull-to-refresh-content infinite-scroll infinite-scroll-bottom" data-ptr-distance="55" style="margin-top: 50px" id=''> 
        @if(!empty($lists)) 
        <ul class="list-container" id="list">
            @include('wap.community.invitation.records_item')
        </ul>
        @else
        <div class="x-null pa w100 tc">
            <i class="icon iconfont">&#xe645;</i>
            <p class="f12 c-gray mt10">亲，这里什么都没有！</p>
        </div>
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
@stop


@section($js)

<script type="text/javascript">
    $(function() {
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

            $.post("{{ u('Invitation/recordsList') }}", data, function(result){
                groupLoading = false;
                $('.infinite-scroll-preloader').addClass('none');
                result  = $.trim(result);
                if (result != '') {
                    groupPageIndex++;
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
            if (groupLoading) {
                return false;
            }
            groupLoading = true;
            var data = new Object;
            data.page = 1;

            $.post("{{ u('Invitation/recordsList') }}", data, function(result){
                groupLoading = false;
                result  = $.trim(result);
                if (result != "") {
                    groupPageIndex = 2;
                }
                $('#list').html(result);
                $.pullToRefreshDone('.pull-to-refresh-content');
            });
        });
        // 加载结束
    });
</script>     
@stop