@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav y-shoptop">
        <a class="button button-link button-nav pull-left" onclick="$.href(' @if(!empty($nav_back_url) && strpos($nav_back_url, u('Goods/index')) === false && strpos($nav_back_url, u('Goods/detail')) === false && strpos($nav_back_url, u('Goods/comment')) === false && strpos($nav_back_url, u('Seller/goodscate')) === false && strpos($nav_back_url, u('Seller/search')) === false)  {{$nav_back_url}} @else {{ u('Index/index') }} @endif')" external>
            <i class="icon iconfont">&#xe600;</i>返回
        </a>
        <div class="title tl c-white" onclick="$.href('{{ u('Seller/search',['sellerId'=>$seller_data['id']] )}}')">
            <i class="icon iconfont f13 c-gray2">&#xe65e;</i>
            <input type="text" class="c-black" placeholder="搜索商品">
            <div class="y-zgsearch"></div>
        </div>
        <a class="button button-link button-nav pull-right" external>
            <div class="dib tc mr5" onclick="$.href('{{ u('Seller/goodscate',['sellerId'=>$seller_data['id']]) }}')">
                <i class="icon iconfont mr0">&#xe636;</i>
                <p class="f12">分类</p>
            </div>
            <div class="dib tc share">
                <i class="icon iconfont mr0">&#xe616;</i>
                <p class="f12">分享</p>
            </div>
        </a>
    </header>
@stop

@section('content')
    <script type="text/javascript">
        BACK_URL = "@if(!empty($nav_back_url) && strpos($nav_back_url, u('Goods/index')) === false && strpos($nav_back_url, u('Goods/detail')) === false && strpos($nav_back_url, u('Goods/comment')) === false) {{$nav_back_url}} @else {{ u('Index/index') }} @endif";
    </script>
    <div class="content infinite-scroll infinite-scroll-bottom" id=''>
        <div class="y-qgdshophead">
            <div class="list-block media-list mb0">
                <ul class="nobg">
                    <li>
                        <a href="#" class="item-link item-content">
                            <div class="item-media mt0"><img src="{{ formatImage($seller_data['logo'],50,50,2) }}" width="45"></div>
                            <div class="item-inner c-white">
                                <div class="item-title-row">
                                    <div class="item-title">
                                        <p>{{ $seller_data['name'] }}</p>
                                        <p class="f12">评分{{ $seller_data['score'] }}分
                                        <span class="y-follow c-bg collect">
                                            @if($seller_data['isCollect'] == 1)
                                                <span class="f14" id="iscollect"></span>已关注
                                            @else
                                                <span class="f14" id="iscollect">+</span>关注
                                            @endif
                                        </span>
                                        </p>
                                    </div>
                                    <div class="item-after clearfix"> <!-- 人数上万了就把数字改为多少万，保留1位小数点eg：99245 改为9.9万
                                        <div class="dib tc c-white c-bg y-popularity f12">
                                            <p id="collectCount">{{ $seller_data['shareNum'] or 0 }}人在卖</p>
                                            <p>{{ $seller_data['collectCount'] or 0 }}人关注</p>
                                        </div> -->
										<div class="dib tc c-white c-blackbg y-popularity f12">
                                                <p>{{ $seller_data['shareNum'] or 0 }}</p>
                                                <p>人在卖</p>
                                            </div>
                                            <div class="dib tc c-white c-bg y-popularity f12">
                                                <p>{{ $seller_data['collectCount'] or 0 }}</p>
                                                <p>人已关注</p>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="buttons-tab c-bgfff y-qgdshopnav">
            <a href="qgdtab1" class="tab-link button active">
                <i class="icon iconfont mr0">&#xe632;</i>
                <p class="f12">店铺首页</p>
            </a>
            <a href="qgdtab2" class="tab-link button">
                <i class="icon iconfont mr0">&#xe696;</i>
                <p class="f12">销售/代购</p>
            </a>
            <a href="qgdtab3" class="tab-link button">
                <i class="icon iconfont mr0">&#xe695;</i>
                <p class="f12">店铺简介</p>
            </a>
        </div>
        <div class="tabs">
            <div id="qgdtab1" class="tab mt10 active">
                <div class="content-block-title m0 p10 c-bgfff"><span class="c-black f14">热销商品</span><span class="fr c-gray f12 y-more">更多</span></div>
            </div>
            <div id="qgdtab2" class="tab">
                <div class="buttons-tab c-bgfff y-qgdpurchase mb10">
                    <a href="tab1" class="tab-link active button" data-order="0">综合</a>
                    <a href="tab2" class="tab-link button" data-order="sales_volume">销量</a>
                    <a href="tab3" class="tab-link button" data-order="money">佣金</a>
                    <a href="tab4" class="tab-link button y-qgdpricebtn" data-order="price">价格<i class="y-qgdpricejt" id="price_desc"></i></a>
                </div>
            </div>
            <ul class="row no-gutter y-recommend" id="wdddmain">
                @include('wap.community.index.lists_item2')
            </ul>
            <div id="qgdtab3" class="tab mt10">
                <div class="list-block">
                    <ul>
                        <li class="item-content">
                            <div class="item-inner">
                                <div class="item-title label f15 c-gray">所在地</div>
                                <div class="item-input f15 mt0 mb0">
                                    {{$seller_data['province']['name']}} {{$seller_data['city']['name']}} {{$seller_data['area']['name']}} {{$seller_data['address']}}
                                </div>
                            </div>
                        </li>
                        <li class="item-content">
                            <div class="item-inner">
                                <div class="item-title label f15 c-gray">开店时间</div>
                                <div class="item-input f15 mt0 mb0">
                                    {{ Time::toDate($seller_data['createTime'], 'Y-m-d') }}
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="list-block media-list">
                    <ul>
                        <li>
                            <a href="tel:{{$wap_service_tel}}" class="item-link item-content" external>
                                <div class="item-inner pr10">
                                    <div class="item-title-row">
                                        <div class="item-title f15">平台客服电话</div>
                                        <div>
                                            <i class="icon iconfont c-red vat">&#xe60a;</i>
                                            <i class="icon iconfont c-gray2 vat">&#xe602;</i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="item-link item-content y-tcimage" external>
                                <div class="item-inner pr10">
                                    <div class="item-title-row">
                                        <div class="item-title f15">店铺二维码</div>
                                        <div>
                                            <i class="icon iconfont c-red vat">&#xe694;</i>
                                            <i class="icon iconfont c-gray2 vat">&#xe602;</i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        @if($seller_data['authenticateImg'])
                            <li>
                                <a href="#" class="item-link item-content y-tcimage2" external>
                                    <div class="item-inner pr10">
                                        <div class="item-title-row">
                                            <div class="item-title f15">营业执照</div>
                                            <div>
                                                <span class="f12 c-red">查看</span>
                                                <i class="icon iconfont c-gray2 vat">&#xe602;</i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="content-block-title">商家介绍</div>
                <div class="card m0">
                    <div class="card-content">
                        <div class="card-content-inner p10">{!!$seller_data['detail']!!}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 加载提示符 -->
        <div class="infinite-scroll-preloader none">
            <div class="preloader"></div>
        </div>
        <!-- 回到顶部 -->
        <a href="javascript:$('.content').scrollTop(0)" class="y-backtop none"></a>
    </div>
    <!-- 弹窗 -->
    <div class="y-qgdshoptc none" id="y-qgdshoptc1">
        <div class="y-qgdshoptcbox">
            <div class="y-tcbg"></div>
            <div class="y-qgdshoptcmain">
                <div class="y-qgdshoptclogo"><img src="{{$seller_data['logo']}}" width="60"></div>
                <p class="f14 c-black">{{$seller_data['name']}}</p>
                <div class="y-qgdshoptcewm"><img src="{{ u('Seller/cancode',['id'=>$seller_data['id']])}}"></div>
                <p class="f12 c-gray">打开{{$site_name}}扫一扫</p>
            </div>
        </div>
    </div>
    <div class="y-qgdshoptc none" id="y-qgdshoptc2">
        <div class="y-qgdshoptcbox">
            <div class="y-tcbg"></div>
            <div class="y-qgdshoptcmain">
                {{-- <div class="y-qgdshoptclogo"><img src="{{$seller_data['logo']}}" width="60"></div>
                 <p class="f14 c-black">{{$seller_data['name']}}</p>--}}
                <div class="y-qgdshoptcewm"><img src="{{ $seller_data['authenticateImg'] }}"></div>
            </div>
        </div>
    </div>
    @include('wap.community.goods.share')
@stop

@section($js)
    <script>
        var isJsShare = false;
        var isJsServer = false;
        var show_js_data;
        var show_data = {};

        show_data.shareSellerId = "{{$seller['id']}}";
        show_data.shareUserId  = "{{$loginUserId}}";
        $(document).off('click','.share_js');
        $(document).on('click','.share_js', function () {

            if("{{$loginUserId or 0}}" == 0){
                $.toast("未登录");
                $.router.load("{{u('User/login')}}", true);
                return false;
            }

            isJsShare = true;
            show_js_data = "";
            var jsObj = {};
            jsObj = $(this).find(".show_js_share").data("val");
            show_js_data = JSON.parse(jsObj);

            openappurl = "{{ u('Seller/shopdetail')}}?goodsId="+show_js_data.id+"&shareType=goods&shareUserId={{$loginUserId}}&shareSellerId="+show_js_data.sellerId;
            show_data.id = show_js_data.id;
            var imge =  "{{ u('Seller/cancode')}}?id="+show_js_data.id+"&shareType=goods";

            $(".showimges_cd").attr("src",imge);
            $(".showimges_img").attr("src",show_js_data.image);
            $(".showimges_t").html(show_js_data.name);
            $(".showimges_cu").html("{!! $share['url'] !!}?goodsId="+show_js_data.id+"&shareUserId={{$loginUserId}}&shareSellerId="+show_js_data.sellerId);


            //  console.log(share_data);
            if (window.App){
                var custom_type = [
                    "CUSTOM_WX",
                    "CUSTOM_WXF",
                    "CUSTOM_IMAGES",
                    "CUSTOM_QR",
                    "CUSTOM_SINA",
                    "CUSTOM_QQ",
                    "CUSTOM_QZ",
                    "CUSTOM_CU"
                ];
                var banner = [show_js_data.images];
                var share_data = {
                    share_content:show_js_data.name,
                    share_imageUrl:show_js_data.image,
                    share_url:"{!! $share['url'] !!}?goodsId="+show_js_data.id+"&shareUserId={{$loginUserId}}&shareSellerId="+show_js_data.sellerId,
                    share_key:'',
                    share_title:"{{$nickname}}为您推荐一件新品！",
                    custom_type: custom_type,
                    share_imageArr:banner
                };
                window.App.sdk_share(JSON.stringify(share_data));
            }else{
                $.showWxBottons();
                $(".y-modal-overlay").addClass("y-modal-overlay-visible");
                $("#y-xffstcmain").addClass("y-modal-in").removeClass("y-modal-out");
                clipCopy();
            }
        });
        //回到顶部
        $(".content").scroll(function(){
            var windowheight =  $(window).height();
            var topheight = $(".content").scrollTop();
            if (topheight > windowheight) {
                $(".y-backtop").removeClass("none");
            }else{
                $(".y-backtop").addClass("none");
            }
            var id = $(".y-qgdshopnav .active").attr("href");
            if($(id).find(".buttons-tab").hasClass("y-qgdpurchase")){
                if($(".y-qgdpurchase").offset().top-$(".bar-nav").height() <= 0){  //有排序标签时
                    $(".y-qgdpurchase").addClass("y-qgdshopnavpa");
                }
            }else{
                if($(".y-qgdshopnav").offset().top-$(".bar-nav").height() <= 0){  //无排序标签时
                    $(".y-qgdshopnav").addClass("y-qgdshopnavpa");
                }
            }
            if($(".tabs").offset().top-$(".bar-nav").height()-$(".y-qgdshopnav").height() >= 0){
                $(".y-qgdshopnav").removeClass("y-qgdshopnavpa");
                $(".y-qgdpurchase").removeClass("y-qgdshopnavpa");
            }
        })

        //上拉
        var groupLoading = false;
        var groupPageIndex = 2;
        var nopost = 0;
        //排序
        $(".y-qgdpurchase .button").click(function(){
            $(this).addClass("active").siblings().removeClass("active");
            //价格箭头
            if($(this).hasClass("y-qgdpricebtn")){
                if($(this).find(".y-qgdpricejt").hasClass("active")){
                    $(this).find(".y-qgdpricejt").addClass("active1").removeClass("active");
                }else{
                    $(this).find(".y-qgdpricejt").addClass("active").removeClass("active1");
                }
            }else{
                $(".y-qgdpricejt").removeClass("active").removeClass("active1");
            }
            groupPageIndex = 1;
            $('#wdddmain').html('');
            groupLoading = true;
            $('.infinite-scroll-preloader').removeClass('none');
            $.pullToRefreshDone('.pull-to-refresh-content');
            $.postgoods();
        })

        $(document).on("click",".y-tcimage",function(){
            $('#y-qgdshoptc1').removeClass("none");
        })
        $(document).on("click",".y-tcimage2",function(){
            $('#y-qgdshoptc2').removeClass("none");
        })

        $(document).on("click",".y-tcbg",function(){
            $('.y-qgdshoptc').addClass("none");
        })

        //点击
        $(".y-qgdshopnav .button").click(function(){
            var id = $(this).attr("href");
            $(this).addClass("active").siblings().removeClass("active");
            $("#"+id).addClass("active").siblings(".tab").removeClass("active");
            if(id == "qgdtab3"){
                $(".y-recommend").addClass("none");
            }else{
                $(".y-recommend").removeClass("none");
            }
        });

        /*更多点击*/
        $('.y-more').click(function(){
            $(".y-qgdshopnav .button").removeClass("active");
            $("a[href='qgdtab2']").addClass('active');
            $("#qgdtab2").addClass("active").siblings(".tab").removeClass("active");
            $(".y-recommend").removeClass("none");

        });


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

            $.postgoods();
        });

        $.postgoods = function(){
            var data = new Object;
            data.page = groupPageIndex;
            data.sellerId = "{{ $seller_data['id'] }}";
            data.noIndex = 1;
            var orderBy = ';'
            $('.y-qgdpurchase a').each(function(){
                if($(this).hasClass('active')){
                    orderBy =$(this).attr('data-order');
                }
            })
            if(orderBy == 'price'){
                if($("#price_desc").hasClass('active')){
                    orderBy += 'desc';
                }else{
                    orderBy += 'asc';
                }
            }
            data.orderBy = orderBy;

            $.post("{{ u('Index/indexList') }}", data, function(result){
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
        }

        $(document).off("touchend", ".collect");
        $(document).on("touchend", ".collect", function(){
            var obj = new Object();
            var collect = $(this);
            obj.id = "{{$seller_data['id']}}";
            obj.type = 2;
            var collectCount = $("#collectCount").html();
            if($("#iscollect").html() == ''){
                var buttons1 = [
                    {
                        text: '取消关注后将无法在我的收藏找到TA啦～',
                        label: true
                    },
                    {
                        text: '取消关注',
                        bold: true,
                        color: 'danger2',
                        onClick: function() {
                            $.post("{{u('UserCenter/delcollect')}}",obj,function(result){
                                if(result.code == 0){
                                    $.toast("已取消关注～");

                                    collect.removeClass("on");
                                    collect.html('<span class="f14" id="iscollect">+</span>关注');
                                    collectCount = collectCount - 1;
                                    $("#collectCount").html(collectCount);
                                } else if(result.code == 99996){
                                    $.router.load("{{u('User/login')}}", true);
                                } else {
                                    $.alert(result.msg);
                                }
                            },'json');
                        }
                    },
                    {
                        text: '容朕想想',
                        onClick: function() {
                        }
                    }
                ];
                $.actions(buttons1);
            }else{
                $.post("{{u('UserCenter/addcollect')}}",obj,function(result){
                    if(result.code == 0){
                        $.toast("关注成功，可以在我的收藏找到TA啦～");

                        collect.html('<span class="f14" id="iscollect"></span>已关注');
                        collectCount = collectCount*1 + 1;
                        $("#collectCount").html(collectCount);
                    } else if(result.code == 99996){
                        $.router.load("{{u('User/login')}}", true);
                    } else {
                        $.alert(result.msg);
                    }
                },'json');
            }
        });
		$.showWxBottons = function(){
            wx.ready(function () {
                // 在这里调用 API
                wx.onMenuShareAppMessage({
                    title: "{{$nickname}}为您推荐一件新品！", // 分享标题
                    desc: show_js_data.name, // 分享描述
                    link: "{!! u('Goods/detail') !!}?goodsId="+show_js_data.id+"&shareUserId={{$loginUserId}}&shareSellerId="+show_js_data.sellerId, // 分享链接
                    imgUrl: show_js_data.image, // 分享图标
                    type: 'link', // 分享类型,music、video或link，不填默认为link
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        $.toast('分享成功');
                        cakShare();
                        location.reload();
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
                wx.onMenuShareTimeline({
                    title: "{{$nickname}}为您推荐一件新品！", // 分享标题
                    desc: show_js_data.name, // 分享描述
                    link: "{!! u('Goods/detail') !!}?goodsId="+show_js_data.id+"&shareUserId={{$loginUserId}}&shareSellerId={{$seller['id']}}", // 分享链接
                    imgUrl: show_js_data.image, // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        $.toast('分享成功');
                        cakShare();
                        location.reload();
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
                wx.onMenuShareQQ({
                    title: "{{$nickname}}为您推荐一件新品！", // 分享标题
                    desc: show_js_data.name, // 分享描述
                    link: "{!! u('Goods/detail') !!}?goodsId="+show_js_data.id+"&shareUserId={{$loginUserId}}&shareSellerId={{$seller['id']}}", // 分享链接
                    imgUrl:show_js_data.image, // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        $.toast('分享成功');
                        cakShare();
                        location.reload();
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });

                wx.onMenuShareWeibo({
                    title: "{{$nickname}}为您推荐一件新品！", // 分享标题
                    desc: show_js_data.name, // 分享描述
                    link: "{!! u('Goods/detail') !!}?goodsId="+show_js_data.id+"&shareUserId={{$loginUserId}}&shareSellerId={{$seller['id']}}", // 分享链接
                    imgUrl: show_js_data.image, // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        $.toast('分享成功');
                        cakShare();
                        location.reload();
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });

                wx.onMenuShareQZone({
                    title: "{{$nickname}}为您推荐一件新品！", // 分享标题
                    desc: show_js_data.name, // 分享描述
                    link: "{!! u('Goods/detail') !!}?goodsId="+show_js_data.id+"&shareUserId={{$loginUserId}}&shareSellerId="+show_js_data.sellerId, // 分享链接
                    imgUrl:show_js_data.image, // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        $.toast('分享成功');
                        cakShare();
                        location.reload();
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
            });
        }
    </script>
@stop

 