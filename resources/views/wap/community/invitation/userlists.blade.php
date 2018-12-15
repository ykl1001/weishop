@extends('wap.community._layouts.base')

@section('show_top')
    <style>
        .bar .icon{line-height: 2.2rem;}
    </style>
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading" href="{{u("MakeMoney/index")}}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">我的好友</h1>
    </header>
@stop

@section('content')
    <div class="bar bar-header-secondary y-myteam">
        <div class="buttons-tab">
            <a href="javascript:$.href('{{u('Invitation/userlists')}}')" class=" button @if(!$args['level'])active @endif">全部</a>
            <a href="javascript:$.href('{{u('Invitation/userlists',['level'=>1])}}')" class=" button @if($args['level'] == 1)active @endif">Ⅰ级</a>
            <a href="javascript:$.href('{{u('Invitation/userlists',['level'=>2])}}')" class=" button @if($args['level'] == 2)active @endif">Ⅱ级</a>
            <a href="javascript:$.href('{{u('Invitation/userlists',['level'=>3])}}')" class=" button @if($args['level'] == 3)active @endif">Ⅲ级</a>
        </div>
    </div>

    <div class="content pull-to-refresh-content infinite-scroll infinite-scroll-bottom" data-ptr-distance="55" id=''>
        <div class="pull-to-refresh-layer">
            <div class="preloader"></div>
            <div class="pull-to-refresh-arrow"></div>
        </div>
        <div class="content-block-title c-gray">团队人数：{{$count}}人</div>
        <div class="list-block media-list m0 y-myteamlist">
            @if(!empty($lists))
                <ul id="list">
                    @include('wap.community.invitation.userlists_item')
                </ul>
            @else
            <div class="x-null pa w100 tc" style="margin-top: 100px;">
                <i class="icon iconfont">&#xe645;</i>
                <p class="f12 c-gray mt10">亲，这里什么都没有！</p>
            </div>
            @endif
        </div> 

        @if($lists && count($lists) < 20)
            <div class="content-block-title tc c-gray2 mt20">没有更多了</div>
        @endif
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
            data.level = "{{$args['level']}}";

            $.post("{{ u('Invitation/userinfoList') }}", data, function(result){
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
            data.level = "{{$args['level']}}";

            $.post("{{ u('Invitation/userinfoList') }}", data, function(result){
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