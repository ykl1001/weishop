@extends('wap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
 <a href="javascript:$.href('@if(!empty($nav_back_url) && strpos($nav_back_url, u('UserCenter/index')) === false ){{$nav_back_url}}@else{{ u('Oneself/Index') }}@endif')" class="button button-link button-nav pull-left isExternal" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">积分商城</h1>
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
        <div class="swiper-container" data-space-between='0' style="max-height:100px;">
            <div class="swiper-wrapper">
                @foreach($data as $key => $value)
                    @if($value['type'] == 1)
                        <div class="swiper-slide" onclick="$.href('{{u('Seller/index',['id'=>$value['arg']])}}')">
                            <img _src="{{ formatImage($value['image'],640) }}" src="{{ formatImage($value['image'],640) }}" />
                        </div>
                    @elseif ($value['type'] == 2 || $value['type'] == 3 || $value['type'] == 6)
                        <div class="swiper-slide" onclick="$.href('{{u('Goods/detail',['goodsId'=>$value['arg']])}}')">
                            <img _src="{{ formatImage($value['image'],640) }}" src="{{ formatImage($value['image'],640) }}" />
                        </div>
                    @elseif ($value['type'] == 4)
                        <div class="swiper-slide" onclick="$.href('{{u('Seller/detail',['id'=>$value['arg']])}}')">
                            <img _src="{{ formatImage($value['image'],640) }}" src="{{ formatImage($value['image'],640) }}" />
                        </div>
                    @elseif ($value['type'] == 7)
                        <div class="swiper-slide" onclick="$.href('{{u('Article/detail',['id'=>$value['arg']])}}')">
                            <img _src="{{ formatImage($value['image'],640) }}" src="{{ formatImage($value['image'],640) }}" />
                        </div>
                    @elseif ($value['type'] == 9)
                        <div class="swiper-slide" onclick="$.href('{{u('Integral/index')}}')">
                            <img _src="{{ formatImage($value['image'],640) }}" src="{{ formatImage($value['image'],640) }}" />
                        </div>
                    @else
                        <div class="swiper-slide" onclick="$.href('{{ $value['arg'] }}')">
                            <img _src="{{ formatImage($value['image'],640) }}" src="{{ formatImage($value['image'],640) }}" />
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
        <div class="buttons-row c-bgfff y-jfsc">
            <a href="{{u('UserCenter/integral')}}" class="button c-gray">
                <p><i class="icon iconfont f20">&#xe669;</i></p>
                <p class="f12 lh20"><span class="c-yellow2">{{$integral}}</span>积分</p>
            </a>
            <a href="{{u('Integral/userlog')}}" class="button c-gray">
                <p><i class="icon iconfont f20">&#xe668;</i></p>
                <p class="f12 lh20">兑换记录</p>
            </a>
            <a href="{{u('More/detail',['code'=>10])}}" class="button c-gray">
                <p><i class="icon iconfont f20">&#xe66a;</i></p>
                <p class="f12 lh20">积分规则</p>
            </a>
        </div>
        <div class="c-bgfff mt10">
            <div class="content-block-title f14 c-black youbor y-cbtitle">积分好礼</div>
            <div class="row no-gutter y-jfscmain lh20">
                @include("wap.community.integral.item")
            </div>
        </div>
        <div class="content-block-title tc c-gray2">没有更多了...</div>
    </div>
@stop
@section($js)
    <script type="text/javascript">
        //精确定位
        $(function(){
            BACK_URL = "{{$nav_back_url or u('UserCenter/index')}}";

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

                $('.infinite-scroll-preloader').removeClass('none');
                $.pullToRefreshDone('.pull-to-refresh-content');

                var data = new Object;
                data.page = groupPageIndex;
                data.tpl = "item";

                $.post("{{ u('Integral/index') }}", data, function(result){
                    groupLoading = false;
                    $('.infinite-scroll-preloader').addClass('none');
                    if (result != '') {
                        groupPageIndex++;
                        $('.y-jfscmain').append(result);
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
                data.tpl = "item";

                $.post("{{ u('Integral/index') }}", data, function(result){
                    groupLoading = false;
                    if (result != "") {
                        groupPageIndex = 2;
                    }
                    $('.y-jfscmain').html(result);
                    $.pullToRefreshDone('.pull-to-refresh-content');
                });
            });
            $.init();

            //部分IOS返回刷新
            if($.device['os'] == 'ios')
            {
                $(".isExternal").addClass('external');
            }
        });
    </script>
@stop