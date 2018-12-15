@extends('wap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" onclick="$.href('{{u('UserCenter/systemmessage')}}')" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <a class="button button-link button-nav pull-right showmore" href="javascript:;" data-popup=".popup-about" external>
            <span class="icon iconfont">&#xe692;</span>
            <span class="y-redc"></span>
        </a>
        <h1 class="title f16">订单状态变更消息</h1>
    </header>
    <ul class="x-ltmore f12 c-gray none">
        <link rel="stylesheet" href="{{ asset('wap/community/newclient/index_iconfont/iconfont.css') }}?{{ TPL_VERSION }}">
        @foreach($indexnav as $key => $i_nav)
            <li class="pl20" onclick="$.href('{{ u(Lang::get('api_system.index_link.'.$i_nav['type'])) }}')"><i class="icon iconfont mr5 vat">{{explode(",",$i_nav['icon'])[0].";"}}</i>
                {{$i_nav['name']}}
                @if(Lang::get('api_system.index_nav.'.$i_nav['type']) == 'mine' && (int)$counts['newMsgCount'] > 0)
                    <span class="x-dot f12 none">{{(int)$counts['newMsgCount'] > 99? '99+' : (int)$counts['newMsgCount']}}</span>
                    <script type="text/javascript">
                        $(".y-redc").removeClass("none");
                    </script>
                @endif
                @if(Lang::get('api_system.index_nav.'.$i_nav['type']) == 'goodscart' && (int)$counts['cartGoodsCount'] > 0)
                    <span class="x-dot f12 none" id="tpGoodsCart">{{(int)$counts['cartGoodsCount'] > 99 ? '99+' : (int)$counts['cartGoodsCount']}}</span>
                    <script type="text/javascript">
                        $(".y-redc").removeClass("none");
                    </script>
                @endif
            </li>
        @endforeach
    </ul>
@stop
    @section('content')
    @include('wap.community._layouts.bottom')

        <div class="content infinite-scroll infinite-scroll-bottom pull-to-refresh-content">
            <!-- 加载提示符 -->
            <div class="pull-to-refresh-layer">
                <div class="preloader"></div>
                <div class="pull-to-refresh-arrow"></div>
            </div>
            <div  id='list'>
                @include("wap.community.usercenter.system_item")
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
    <script type="text/javascript">
        $(function(){
            $(".showmore").click(function(){
                if($(".x-ltmore").hasClass("none")){
                    $(".x-ltmore").removeClass("none");
                }else{
                    $(".x-ltmore").addClass("none");
                }
            });

            // 加载开始
            // 上拉加载
            var sellerId = "{{$_GET['sellerId']}}";
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
                data.sellerId = sellerId;

                $.post("{{ u('UserCenter/systemList') }}", data, function(result){
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

                $.post("{{ u('UserCenter/systemList') }}", data, function(result){
                    groupLoading = false;
                    result  = $.trim(result);
                    if (result != "") {
                        groupPageIndex = 2;
                    }
                    $('#list').html(result);
                    $.pullToRefreshDone('.pull-to-refresh-content');
                });
            });

        });
    </script>
@stop

