@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left isExternal" href="{{ u('Bank/carry') }}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">提现记录</h1>
    </header>
@stop

@section('content')
    <!-- new -->
    <div class="content c-bgfff infinite-scroll infinite-scroll-bottom pull-to-refresh-content" data-ptr-distance="55" data-distance="50" id=''>
        <!-- 加载提示符 -->
        <div class="pull-to-refresh-layer">
            <div class="preloader"></div>
            <div class="pull-to-refresh-arrow"></div>
        </div>
        <div class="content-block-title f14 c-gray y-blocktitle">提现记录</div>
        <div class="list-block media-list y-syt lastbor">
            <ul id="list">
                @if($data['paylogs'])
                    @include('wap.community.bank.log_item')
                @else
                    <li class="x-null pa w100 tc mt20">
                        <i class="icon iconfont" style>&#xe645;</i>
                        <p class="f12 c-gray mt10">没有交易记录</p>
                    </li>
                @endif
            </ul>
        </div>
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

    <script>
        $(function() {
            $(".y-records").css("min-height",$(window).height()-247);
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
                data.item = "log_item";

                $.post("{{ u('Bank/withdrawlog') }}", data, function(result){
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
                data.item = "log_item";
                $.post("{{ u('Bank/withdrawlog') }}", data, function(result){
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
            //部分IOS返回刷新
            if($.device['os'] == 'ios')
            {
                $(".isExternal").addClass('external');
            }
        })
    </script>

@stop