@extends('wap.community._layouts.base')

@section('css')
@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="javascript:$.href('{{ u('Goods/detail',['backindex'=>1,'goodsId'=>Input::get('id')]) }}')" data-transition='slide-out' external>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">商品评价</h1>
    </header>
@stop

@section('content')
    <!-- new -->
    <div class="content infinite-scroll infinite-scroll-bottom pull-to-refresh-content" data-ptr-distance="40" data-distance="40" id='goodsComment'>
        <!-- 加载提示符 -->
        <div class="pull-to-refresh-layer">
            <div class="preloader"></div>
            <div class="pull-to-refresh-arrow"></div>
        </div>

        <!-- 总体评价 -->
        <div class="x-pjtotal c-bgfff">
            <div class="clearfix pt15 pr bor">
            <div class="x-pjtl fl tc pr">
                <p class="c-red f18">{{ $count['star'] }}</p>
                <p class="f14">总体评价:</p>
            </div>
            <div class="y-starcont ml20 mt10">
                <div class="c-gray4 y-star">
                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                </div>
                <div class="c-red y-startwo" style="width:{{$count['star'] * 20}}%;">
                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                    <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                </div>
            </div>
            </div>
        </div>


        <!-- 评价列表 -->
        <div class="buttons-tab x-commenttab c-bgfff mt10">
            <a href="{{ u('Goods/commentall',['id'=>$args['goodsId']]) }}"           class="@if($args['type'] == 0) active @endif  button f12">全部({{$count['totalCount'] or 0}})</a>
            <a href="{{ u('Goods/commentall',['id'=>$args['goodsId'],'type'=>1]) }}" class="@if($args['type'] == 1) active @endif  button f12">好评({{$count['goodCount']  or 0}})</a>
            <a href="{{ u('Goods/commentall',['id'=>$args['goodsId'],'type'=>2]) }}" class="@if($args['type'] == 2) active @endif  button f12">中评({{$count['neutralCount']  or 0}})</a>
            <a href="{{ u('Goods/commentall',['id'=>$args['goodsId'],'type'=>3]) }}" class="@if($args['type'] == 3) active @endif  button f12">差评({{$count['badCount']  or 0}})</a>
        </div>
        <!-- 评价 -->
        <div class="tabs">
            <div id="tab1" class="tab active">
                @if(!empty($list))
                    <div class="list-block media-list x-comment nobor">
                        <ul id="list">
                            @include('wap.community.goods.commentall_item')
                        </ul>
                    </div>
                @else
                    <div class="x-null pa w100 tc">
                        <i class="icon iconfont" style>&#xe645;</i>
                        <p class="f12 c-gray mt10">暂时还没有评论</p>
                    </div>
                @endif
            </div>
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
	
    @include('wap.community.goods.share')
@stop 
 
@section($js)
<script type="text/javascript" src="{{ asset('wap/community/newclient/js/jweixin-1.0.0.js') }}"></script>

<script type="text/javascript">
    $(function() {
        //导航和content位置
        var toph = $(".y-sjlistnav").height();
        $(".bar-header-secondary").css("top",toph);
        toph += $(".bar-header-secondary").height();
        var sxheight = $(".pull-to-refresh-layer").height();
        // $("#goodsComment").css({"bottom":0,"top":sxheight+1});
        //菜单高度
        var height = $(".bar-footer").height();
        height += toph;
        $(".y-scroll").css("height",$(window).height()-height);
        height += $(".x-goodstit").height();
        $(".x-goodstab .x-sortlst").css("height",$(window).height()-height);

        $(".x-pjtab").css("min-height",$(window).height()-135);

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
            data.id = "{{$_GET['id']}}";
            data.type = "{{$_GET['type']}}";

            $.post("{{ u('Goods/commentAllList') }}", data, function(result){
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
            data.id = "{{$_GET['id']}}";
            data.type = "{{$_GET['type']}}";

            $.post("{{ u('Goods/commentAllList') }}", data, function(result){
                groupLoading = false;
                result  = $.trim(result);
                if (result != "") {
                    groupPageIndex = 2;
                }
                $('#list').html(result);
                $.pullToRefreshDone('.pull-to-refresh-content');
            });
        
    })
</script>
@stop
