@extends('wap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left isExternal" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">我的{{$wap_promotion}}</h1>
    </header>
@stop


@section('content')
    <!--<nav class="bar bar-tab y-cdkey">
        <div class="searchbar row">
            <div class="search-input col-80">
                <input type="search" placeholder="我有兑换码..."  id="sn">
            </div>
            <a class="button button-fill button-primary col-20"  id="exchange">立即兑换</a>
        </div>
    </nav>-->
    <div class="bar bar-header-secondary">
        <div class="buttons-tab y-couponsnav">
            <a href="{{ u('Coupon/index',['status' => 0]) }}" class="button @if($args['status'] != 1) active @endif pageloading">未使用</a>
            <a href="{{ u('Coupon/index',['status' => 1]) }}" class="button @if($args['status'] == 1) active @endif pageloading">已失效</a>
        </div>
    </div>
    
    <div class="content infinite-scroll infinite-scroll-bottom pull-to-refresh-content" data-ptr-distance="55" data-distance="50" id=''>
        <!-- 加载提示符 -->
        <div class="pull-to-refresh-layer">
            <div class="preloader"></div>
            <div class="pull-to-refresh-arrow"></div>
        </div>
        
        <div class="content-block-title f12">
            <span>有<span class="c-red">{{ $list['count'] }}</span>张{{$wap_promotion}}</span>
            <span class="fr c-red" onclick="$.href('{{ u('More/detail',['code' => 5]) }}')"><i class="icon iconfont mr5">&#xe64c;</i>{{$wap_promotion}}说明</span>
        </div>

        @if(!empty($list['list']))
            <div id="list" class="tab active">
                @include('wap.community.coupon.item')
            </div>
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
$(function(){
    var conth = window.innerHeight;

    $(document).off("touchend", "#exchange");
    $(document).on("touchend", "#exchange", function(){
        var sn = $("#sn").val();
        $.post("{{ u('Coupon/excoupon') }}",{sn:sn},function(res){
            if(res.code == 0){
                $.alert(res.msg, function(){
                    $.router.load("{{ u('Coupon/index') }}", true);
                });
            }else{
                $.alert(res.msg);
            }
        },"json");
    });

    /*查看优惠券详情*/
    $.checkBrief = function(id){
        var html = $("#li-"+id).find('div.brief').html();
        // $.alert('<div class="y-xcoupontc f12 tl"><ul><li><p>1、满300元可使用此券</p></li><li><p>2、没什么想的讲的，快去下单吧</p></li></ul></div>', '自营商城专用'); 
        $.alert('<div class="y-xcoupontc f12 tl">'+html+'</div>', $("#li-"+id).find('.name').text());
        $(".modal-buttons .modal-button-bold").text("知道了").css({"color":"#313233","font-size":"14px"});
        return false;
    }


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
        data.status = "{{$args['status']}}";

        $.post("{{ u('Coupon/indexList') }}", data, function(result){

            $('.infinite-scroll-preloader').addClass('none');
            result  = $.trim(result);
            if (!result) {
                groupPageIndex++;
                groupLoading = false;
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
        data.status = "{{$args['status']}}";

        $.post("{{ u('Coupon/indexList') }}", data, function(result){

            result  = $.trim(result);
            if (!result) {
                groupPageIndex = 2;
                groupLoading = false;
            }
            $('#list').html(result);
            $.pullToRefreshDone('.pull-to-refresh-content');
        });
    });
    // 加载结束
    $.init();

    //部分IOS返回刷新
    if($.device['os'] == 'ios')
    {
        $(".isExternal").addClass('external');
    }

    //防止优惠码被键盘遮挡
    $(".y-cdkey input").click(function(){
        if($.device['os'] == 'ios'){
        }else{
            $(".y-cdkey").css("height","55%");
        }
    });
    $(".y-cdkey input").blur(function(){
        $(".y-cdkey").css("height","3rem");
    });
    window.onresize = function(){
        if(conth == window.innerHeight){
            $("input").blur();
        }
    }

});
</script>
@stop
