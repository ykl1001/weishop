@extends('wap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="{{ u('Tag/index') }}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">{{$tag['pid']['name']}} - {{$tag['name']}}</h1>
        <a class="button button-link button-nav pull-right y-splistcd" href="#" data-transition='slide-out'>
            <span class="icon iconfont">&#xe662;</span>
        </a>
    </header>
@stop

@section('content') 
<?php 
    $type = Input::get('type') > 0 ? Input::get('type') : 1;
 ?>
    <div class="bar bar-header-secondary y-twonobor">
        <div class="buttons-tab y-couponsnav @if($type == 1) y-splistm @endif">
            <a href="{{ u('Tag/goodsLists',['pid'=>$args['pid'],'id'=>$args['id'],'type'=>1]) }}" class="@if($type == 1) active @endif button">价格</a>
            <a href="{{ u('Tag/goodsLists',['pid'=>$args['pid'],'id'=>$args['id'],'type'=>2]) }}" class="@if($type == 2) active @endif button">距离</a>
        </div>
    </div>




    <ul class="x-ltmore none">
        <li><a href="{{ u('Index/index') }}"      class="f12 c-gray" external><i class="icon iconfont mr5 vat">&#xe66e;</i>首页</a></li>
        <li><a href="{{ u('GoodsCart/index') }}"  class="f12 c-gray"><i class="icon iconfont mr5 vat">&#xe673;</i>购物车</a></li>
        <li><a href="{{ u('Forum/index') }}"      class="f12 c-gray"><i class="icon iconfont mr5 vat">&#xe680;</i>生活圈</a></li>
        <li><a href="{{ u('UserCenter/index') }}" class="f12 c-gray"><i class="icon iconfont mr5 vat">&#xe66d;</i>我的</a></li>
    </ul>

    <div class="content infinite-scroll infinite-scroll-bottom pull-to-refresh-content" data-ptr-distance="55" data-distance="50" id="">
        <div class="pull-to-refresh-layer">
            <div class="preloader"></div>
            <div class="pull-to-refresh-arrow"></div>
        </div>
        <!-- 商品列表 -->
        <div class="row no-gutter y-cnxh mt10" id="wdddmain">
            @include('wap.community.tag.goodslistsitem')
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
    $(".x-ltmore").addClass("none");
    $(document).off("click", ".y-splistcd");
    $(document).on("click", ".y-splistcd", function(){
        if($(".x-ltmore").hasClass("none")){
            $(".x-ltmore").removeClass("none");
        }else{
            $(".x-ltmore").addClass("none");
        }
    });
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
        data.page   = groupPageIndex;
        data.pid    = "{{$args['pid']}}";
        data.id     = "{{$args['id']}}";
        data.type   = "{{ $type }}";

        $.post("{{ u('Tag/goodsListsItem') }}", data, function(result){

            $('.infinite-scroll-preloader').addClass('none');
            result  = $.trim(result);
            if (result.length!=0 || result != "") {
                groupPageIndex++;
                groupLoading = false;
                $('#wdddmain').append(result);
                $.refreshScroller();
            }else{
                $(".allEnd").removeClass('none');
            }
        });
    });
    //下拉刷新
        $(document).off('refresh', '.pull-to-refresh-content');
        $(document).on('refresh', '.pull-to-refresh-content',function(e) {
            groupLoading = false;
            // 如果正在加载，则退出
            if (groupLoading) {
                return false;
            }
            groupLoading = true;
            var data = new Object;
            data.page   = 1;
            data.pid    = "{{$args['pid']}}";
            data.id     = "{{$args['id']}}";
            data.type   = "{{ $type }}";

            $.post("{{ u('Tag/goodsListsItem') }}", data, function(result){

                result  = $.trim(result);
                if (result.length!=0 || result != "") {
                    groupPageIndex = 2;
                    groupLoading = false;
                }
                $('#wdddmain').html(result);
                $.pullToRefreshDone('.pull-to-refresh-content');
            });
        });
        //js刷新
        //$.pullToRefreshTrigger('.pull-to-refresh-content');
        //加载结束
    $.init();
})
</script>
@stop
