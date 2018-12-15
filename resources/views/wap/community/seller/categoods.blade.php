@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav y-shoptop">
        <a class="button button-link button-nav pull-left" onclick="$.href('{{u('Seller/goodscate',['sellerId'=>$args['sellerId']])}}')" external>
            <i class="icon iconfont">&#xe600;</i>返回
        </a>
        <h1 class="title">商品列表</h1>
        <a class="button button-link button-nav pull-right" onclick="$.href('{{u('Seller/goodscate',['sellerId'=>$args['sellerId']])}}')" external>
            <div class="dib tc mr5">
                <i class="icon iconfont mr0">&#xe636;</i>
                <p class="f12">分类</p>
            </div>
        </a>
    </header>
    <div class="bar bar-header-secondary">
        <div class="buttons-tab c-bgfff y-qgdpurchase pt0 ">
            <a href="tab1" class="tab-link active button" data-order="0">综合</a>
            <a href="tab2" class="tab-link button" data-order="sales_volume">销量</a>
            {{--<a href="tab3" class="tab-link button">佣金</a>--}}
            <a href="tab4" class="tab-link button y-qgdpricebtn" data-order="price">价格<i class="y-qgdpricejt" id="price_desc"></i></a>
        </div>
    </div>
@stop

@section('content')
    <div class="content infinite-scroll infinite-scroll-bottom">
        <ul class="row no-gutter y-recommend mt10" id="wdddmain">
            @include('wap.community.index.lists_item2')
        </ul>

        <!-- 加载提示符 -->
        <div class="infinite-scroll-preloader none">
            <div class="preloader"></div>
        </div>
        <!-- 回到顶部 -->
        <a href="javascript:$('.content').scrollTop(0)" class="y-backtop none"></a>
    </div>
@stop

@section($js)
    <script type="text/javascript">
        $(function() {
            //上拉
            var groupLoading = false;
            var groupPageIndex = 2;
            var nopost = 0;
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
                data.sellerId = "{{ $args['sellerId'] }}";
                data.cateId = "{{ $args['id'] }}";
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

            //回到顶部
            $(".content").scroll(function(){
                var windowheight =  $(window).height();
                var topheight = $(".content").scrollTop();
                if (topheight > windowheight) {
                    $(".y-backtop").removeClass("none");
                }else{
                    $(".y-backtop").addClass("none");
                }
            })

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
                $.postgoods();
            })
        })
    </script>
@stop

 