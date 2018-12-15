@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left external pageloading" data-popup=".popup-about" href="{{ u('Order/order',['addressId'=>$args['addressId'],'cartIds'=>$args['cartIds'],'appTime'=>$args['appTime'],'sendWay'=>$args['sendWay'],'sendType'=>$args['sendType']]) }}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <a class="button button-link button-nav pull-right open-popup pageloading" data-popup=".popup-about" href="{{ u('Order/order',['cartIds'=>$args['cartIds'],'addressId'=>$args['addressId'], 'cancel'=>1,'appTime'=>$args['appTime'],'sendWay'=>$args['sendWay'],'sendType'=>$args['sendType']]) }}">
            取消选择
        </a>
        <h1 class="title f16">选择优惠券</h1>
    </header>
@stop

@section('content')
    <script type="text/javascript">
        //BACK_URL = "{!! Request::server('HTTP_REFERER') !!}";
    </script>
    <div class="content infinite-scroll infinite-scroll-bottom pull-to-refresh-content" data-ptr-distance="55" data-distance="50" id="">
        <div class="pull-to-refresh-layer">
            <div class="preloader"></div>
            <div class="pull-to-refresh-arrow"></div>
        </div>
        @if(!empty($list))
            <div id="list" class="tab active">
                @include('wap.community.coupon.use_item')
            </div>
            <div class="pa w100 tc allEnd none">
                <p class="f12 c-gray mt5 mb5">数据加载完毕</p>
            </div>
        @else
            <div class="x-null pa w100 tc">
                <i class="icon iconfont">&#xe645;</i>
                <p class="f12 c-gray mt10">空空如也，快去兑换吧！</p>
            </div>
            @endif
                    <!-- 加载提示符 -->
            <div class="infinite-scroll-preloader none">
                <div class="preloader"></div>
            </div>
    </div>
@stop

@section($js)
    <script type="text/javascript">
        $(function(){

            $(document).on("click", ".buttons-tab a.button", function(){
                var qhid;
                if(!$(this).hasClass("active")){
                    $(this).addClass("active").siblings().removeClass("active");
                    qhid = $(this).attr("href");
                    $("#"+qhid).addClass("active").siblings().removeClass("active");
                }
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
                data.cartIds = "{{ $_GET['cartIds'] }}";
                data.addressId = "{{ $_GET['addressId'] }}";
                data.sellerId = "{{ $_GET['sellerId'] }}";
                data.money = "{{ $_GET['money'] }}";

                $.post("{{ u('Coupon/usepromotionList') }}", data, function(result){
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
                data.cartIds = "{{ $_GET['cartIds'] }}";
                data.addressId = "{{ $_GET['addressId'] }}";
                data.sellerId = "{{ $_GET['sellerId'] }}";
                data.money = "{{ $_GET['money'] }}";

                $.post("{{ u('Coupon/usepromotionList') }}", data, function(result){
                    groupLoading = false;
                    result  = $.trim(result);
                    if (result != "") {
                        groupPageIndex = 2;
                    }
                    $('#list').html(result);
                    $.pullToRefreshDone('.pull-to-refresh-content');
                });
            });
            //加载结束
            $.init();
        });
    </script>
@stop 

