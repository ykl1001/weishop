@extends('wap.community._layouts.base')

@section('css')
@stop

@section('show_top')
    @include('wap.community.goods.sellergoodshead')
@stop

@section('content')
    <!-- new -->
    <div class="content infinite-scroll infinite-scroll-bottom pull-to-refresh-content" data-ptr-distance="55" data-distance="50" id='goodsComment'>
        <!-- 加载提示符 -->
        <div class="pull-to-refresh-layer">
            <div class="preloader"></div>
            <div class="pull-to-refresh-arrow"></div>
        </div>

        @section('notice')
        @stop

        @if(!empty($list))
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
        @endif

        <!-- 评价列表 -->
        <div class="buttons-tab x-commenttab c-bgfff mt10">
            <a href="{{ u('Goods/comment',['id'=>$args['sellerId']]) }}"           class="@if($args['type'] == 0) active @endif  button f12">全部({{$count['totalCount'] or 0}})</a>
            <a href="{{ u('Goods/comment',['id'=>$args['sellerId'],'type'=>1]) }}" class="@if($args['type'] == 1) active @endif  button f12">好评({{$count['goodCount']  or 0}})</a>
            <a href="{{ u('Goods/comment',['id'=>$args['sellerId'],'type'=>2]) }}" class="@if($args['type'] == 2) active @endif  button f12">中评({{$count['neutralCount']  or 0}})</a>
            <a href="{{ u('Goods/comment',['id'=>$args['sellerId'],'type'=>3]) }}" class="@if($args['type'] == 3) active @endif  button f12">差评({{$count['badCount']  or 0}})</a>
        </div>
        <!-- 评价 -->
        <div class="tabs">
            <div id="tab1" class="tab active">
                @if(!empty($list))
                    <div class="list-block media-list x-comment nobor">
                        <ul id="list">
                            @include('wap.community.goods.comment_item')
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
    <!-- 分享到微信好友或朋友圈 -->
    <div class="f-bgtk sha-frame none">
        <div class="x-closebg"></div>
        <img src="{{ asset('wap/images/share2.png') }}" class="x-sharepic">
    </div>
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
        $("#goodsComment").css({"bottom":0,"top":toph-sxheight+1});
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

            $.post("{{ u('Goods/commentList') }}", data, function(result){
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

            $.post("{{ u('Goods/commentList') }}", data, function(result){
                groupLoading = false;
                result  = $.trim(result);
                if (result != "") {
                    groupPageIndex = 2;
                }
                $('#list').html(result);
                $.pullToRefreshDone('.pull-to-refresh-content');
            });
           /* if($(".content").scrollTop() == 0){
                $(".y-sjxq").removeClass("none");
                $(".y-sjnotice").removeClass("none");
                $(".y-sjlistnav").css({"background-size":"100% 100%","height":"auto"});
                var h = $(".y-sjlistnav").height();
                $(".bar-header-secondary").css("top",h);
                h += $(".bar-header-secondary").height();
                $(".content").css("top",h+1);
            }*/
        });
        // 加载结束
/*
        $(".content").scroll(function(){
            if($(this).scrollTop() == 0){
                $(".y-sjxq").removeClass("none");
                $(".y-sjnotice").removeClass("none");
                $(".y-sjlistnav").css({"background-size":"100% 100%","height":"auto"});
                var h = $(".y-sjlistnav").height();
                $(".bar-header-secondary").css("top",h);
                h += $(".bar-header-secondary").height();
                $(this).css("top",h+1);
            }else{
                $(".y-sjxq").addClass("none");
                $(".y-sjnotice").addClass("none");
                $(".y-sjlistnav").css({"background-size":"100%","height":"2.2rem"});
                var h = $(".pull-left").height();
                $(".bar-header-secondary").css("top",h);
                h += $(".bar-header-secondary").height();
                $(this).css("top",h+1);
            }
        })*/
        
    })
</script>
<script type="text/javascript">
        <?php 
            $share['content'] = trim($share['content']); 
            $share['content'] = strip_tags($share['content'],""); 
            $share['content'] = ereg_replace("\t","",$share['content']); 
            $share['content'] = ereg_replace("\r\n","",$share['content']); 
            $share['content'] = ereg_replace("\r","",$share['content']); 
            $share['content'] = ereg_replace("\n","",$share['content']); 
            $share['content'] = ereg_replace(" "," ",$share['content']);
         ?>
        $(function(){
            $(document).off("click",".page-current .sha-frame");
            $(document).on("click",".page-current .sha-frame",function(){
                $(this).addClass('none');
            });
            $(document).off('click','.page-current .collect_opration .share');
            $(document).on('click','.page-current .collect_opration .share', function () {
                if (window.App){
                    var share_data = {share_content:'{!! $share['content'] !!}',share_imageUrl:'{{$share['logo']}}',share_url:'{!! $share['url'] !!}',share_key:'',share_title:'{{$share['title']}}' };
                    window.App.sdk_share(JSON.stringify(share_data));
                }else{
                    $(".page-current .sha-frame").removeClass("none");
                }
            });

            //微信分享配置文件
            wx.config({
                debug: false, // 调试模式
                appId: "{{$weixin['appId']}}", // 公众号的唯一标识
                timestamp: "{{$weixin['timestamp']}}", // 生成签名的时间戳
                nonceStr: "{{$weixin['noncestr']}}", // 生成签名的随机串
                signature: "{{$weixin['signature']}}",// 签名
                jsApiList: ['checkJsApi','onMenuShareAppMessage','onMenuShareTimeline','onMenuShareQQ'] // 需要使用的JS接口列表
            });

            wx.ready(function () {
                // 在这里调用 API
                wx.onMenuShareAppMessage({
                    title: "{{$share['title']}}", // 分享标题
                    desc: "{!! $share['content'] !!}", // 分享描述
                    link: "{!! $share['url'] !!}", // 分享链接
                    imgUrl: "{{$share['logo']}}", // 分享图标
                    type: 'link', // 分享类型,music、video或link，不填默认为link
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        alert('分享成功');
                        location.reload();
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
                wx.onMenuShareTimeline({
                    title: "{{$share['title']}}", // 分享标题
                    link: "{!! $share['url'] !!}", // 分享链接
                    imgUrl: "{{$share['logo']}}", // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        alert('分享成功');
                        location.reload();
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
                wx.onMenuShareQQ({
                    title: "{{$share['title']}}", // 分享标题
                    desc: "{!! $share['content'] !!}", // 分享描述
                    link: "{!! $share['url'] !!}", // 分享链接
                    imgUrl: "{{$share['logo']}}", // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        alert('分享成功');
                        location.reload();
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
            });
        });
    </script>
@stop
