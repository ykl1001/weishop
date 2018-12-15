@extends('wap.community._layouts.base')

@section('css')
@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <a class="button button-link button-nav pull-right" href="{{ u('Integral/index') }}">
            积分商城
        </a>
        <h1 class="title f16">每日签到</h1>
    </header>
@stop

@section('content')
    @include('wap.community._layouts.bottom')
    <div class="content infinite-scroll infinite-scroll-bottom" id=''>
        <div class="y-signin">
            <div class="y-signc f18 tc c-red">
                <div>
                    <p>恭喜你</p>
                    <p class="mt5">签到成功</p>
                </div>
            </div>
            <p class="c-white f15 tc">明日可领<span>{{ $signIntegral }}</span>积分</p>
        </div>
        <div class="content-block-title f15">签到记录</div>
        <div class="list-block media-list mb0">
            @if(count($integral) > 0)
            <ul id="list">
                @include('wap.community.usercenter.signin_item')
            </ul>
            @else
            <p style="text-align: center;" class="f12">暂无签到记录</p>
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
    </div>
@stop

@section($js)

    <script>
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

                $.post("{{ u('UserCenter/signin') }}", data, function(result){
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


        })
    </script>
@stop



