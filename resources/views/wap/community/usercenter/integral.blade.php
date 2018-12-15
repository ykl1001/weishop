@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="javascript:$.href('@if(!empty($nav_back_url)){{$nav_back_url}} @else {{ u('UserCenter/index') }} @endif')" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">我的积分</h1>
    </header>
@stop

@section('content')
    <nav class="bar bar-tab y-ddxqbtnh">
        <p class="buttons-row y-ddbtn">
            <a href="{{u('Integral/index')}}" class="button">积分兑换商品</a>
        </p>
    </nav>
    <div class="content infinite-scroll infinite-scroll-bottom" id=''>
        <div class="row no-gutter c-bg ml0 y-wdjf c-white f12">
            <div class="col-75">当前积分<span class="ml10 mr5">{{ $list['integral'] }}</span>分</div>
            <div class="col-25 tr" onclick="$.href('{{ u('More/detail',['code'=>10]) }}');"><i class="icon iconfont mr5">&#xe664;</i>积分说明</div>
        </div>
        <div class="content-block-title f12 c-gray">积分记录</div>
        <div class="list-block media-list y-syt lastbor">
            @if(count($list['list']) > 0)
            <ul id="list">
                @include('wap.community.usercenter.integral_item')
            </ul>
            @endif
        </div>
        <!-- 加载完毕提示 -->
        <div class="pa w100 tc allEnd none" style="padding-bottom:100px;">
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

                $.post("{{ u('UserCenter/integral') }}", data, function(result){
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