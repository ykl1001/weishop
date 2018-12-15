@extends('wap.community._layouts.base')

@section('show_top')
	<header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="{{ u('MakeMoney/index') }}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">订单列表</h1>
    </header>
@stop

@section('content')

    <div class="bar bar-header-secondary y-orderlistnav">
        <div class="y-scbtn">
            <div class="buttons-row">
                <a href='#' onclick='$.href("{{ u('MakeMoney/order',['status' => 0,'userId' =>$args['userId'] ] ) }}")' class="button f14 @if($args['status'] == 0) on @endif">客户订单</a>
                <a href='#' onclick='$.href("{{ u('MakeMoney/order',['status' => 1,'userId' =>$args['userId'] ]) }}")' class="button f14  @if($args['status'] == 1) on @endif">团队订单</a>
            </div>
        </div>
    </div>

    <div class="content infinite-scroll infinite-scroll-bottom pull-to-refresh-content" data-ptr-distance="55" data-distance="50" id="" style="top: 2.8rem;">

        <div class="pull-to-refresh-layer">
            <div class="preloader"></div>
            <div class="pull-to-refresh-arrow"></div>
        </div>
        <div id="wdddmain">

            @if($list)

                @include('wap.community.makemoney.order_item')

                @else
                    <div class="x-null pa w100 tc mt20">
                        <i class="icon iconfont">&#xe645;</i>
                        <p class="f12 c-gray mt10">暂无订单</p>
                    </div>
            @endif
        </div>
        <!-- 加载提示符 -->
        <div class="infinite-scroll-preloader none">
            <div class="preloader"></div>
        </div>
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
            var nopost = 0;
            $(document).off('infinite', '.infinite-scroll-bottom');
            $(document).on('infinite', '.infinite-scroll-bottom', function() {
                if(nopost == 1){
                    return false;
                }
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
                data.status = "{{ $args['status'] }}";
                data.userId = "{{ $args['userId'] }}";

                $.post("{{ u('MakeMoney/orderList') }}", data, function(result){
                    groupLoading = false;
                    $('.infinite-scroll-preloader').addClass('none');
                    result  = $.trim(result);
                    if (result != '') {
                        groupPageIndex++;
                        $('#wdddmain').append(result);
                        $.refreshScroller();
                    }else{
                        $(".allEnd").removeClass('none');
                        nopost = 1;
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
                data.status = "{{ $args['status'] }}";
                data.userId = "{{ $args['userId'] }}";

                $.post("{{ u('MakeMoney/orderList') }}", data, function(result){
                    groupLoading = false;
                    result  = $.trim(result);
                    if (result != "") {
                        groupPageIndex = 2;
                    }
                    $('#wdddmain').html(result);
                    $.pullToRefreshDone('.pull-to-refresh-content');
                });
            });

        });
    </script>
@stop