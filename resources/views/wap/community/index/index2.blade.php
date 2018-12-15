@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav y-zgsytop">
        <a class="button button-link button-nav pull-left" external>
            <i class="icon iconfont">&#xe67c;</i>
            <p class="f12">扫一扫</p>
        </a>
        <div class="title tl c-white" onclick="$.href('{{ u('Seller/search')}}')">
            <i class="icon iconfont f13">&#xe65e;</i>
            <input type="text" placeholder="搜索商品、店铺">
            <div class="y-zgsearch"></div>
        </div>
        <a class="button button-link button-nav pull-right" external onclick="$.href('{{ u('Tag/index')}}')">
            <i class="icon iconfont">&#xe636;</i>
            <p class="f12">分类</p>
        </a>
    </header>
@stop

@section($css)
    <style type="text/css">
        .y-backtop{
            position: fixed;right: .5rem;bottom: 12%;width: 35px;height: 35px;
            background: url('{{asset('/images/ico/top.png')}}') no-repeat center center #fff;
            background-size: 70%;display: block;z-index: 111;border-radius: 100%;
            border: 1px solid #a9a9a9;
        }
    </style>
@stop

@section('content')
    @include('wap.community._layouts.bottom')

    @if($cityIsService == 0)
        <div class="x-null pa w100 tc">
            <i class="icon iconfont">&#xe645;</i>
            <p class="f12 c-gray mt10">当前城市未开通服务</p>
            <a class="f14 c-white x-btn db pageloading" href="{{ u('Index/addressmap')}}">切换地址</a>
        </div>
    @else
        <div class="content infinite-scroll infinite-scroll-bottom"  data-distance="50" id="">
            <div id="indexAdvSwiper" class="swiper-container my-swiper indexAdvSwiper" data-space-between='0' >
                <div class="swiper-wrapper">
                    @foreach($data['banner'] as $key => $value)
                        <div class="swiper-slide pageloading" onclick="$.href('{{ $value['url'] }}')">
                            <img _src="{{ formatImage($value['image'],640) }}" src="{{ formatImage($value['image'],640) }}" />
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination swiper-pagination-adv"></div>
            </div>
            <div class="tc c-bgfff y-xsyaddr">
                <div class="y-xsyaddrico"><i class="icon iconfont c-red">&#xe650;</i></div>
                <div class="f12 y-xsyaddrtext" >
                    @if($orderData['address'])
                        <span onclick="$.href('{{ u('Index/addressmap')}}')">{{$orderData['address']}}</span>
                    @else
                        <span id="locationName">定位中请稍候</span>
                    @endif
                    <i class="icon iconfont ml5 f14">&#xe601;</i>
                </div>
            </div>

            <div id="indexNavSwiper" class="swiper-container y-swiper" data-space-between='0'  style="background:#fff;">
                <div class="swiper-wrapper">
                    @for($i = 0; $i < (ceil(count($menu) / 8)); $i++)
                        <div class="swiper-slide">
                            <ul class="y-nav clearfix">
                                @foreach(array_slice($menu,($i * 8),8) as  $v)
                                    <?php
                                    if (!preg_match("/^(http|https):/", $v['url'])){
                                        $v['url'] = 'http://'.$v['url'];
                                    }
                                    ?>
                                    <li><a href="{{ $v['url'] }}" class="db" external><img src="{{ $v['menuIcon'] }}"><p class="f13">{{ $v['name'] }}</p></a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endfor
                </div>
                <div class="swiper-pagination swiper-pagination-nav"></div>
            </div>

            @if(count($data['notice']) > 0)
                <ul class="x-advertising clearfix">
                    @foreach($data['notice'] as $k=>$value)
                        @if($k < 2)
                            <li><a href="{{$value['url']}}" class="br pageloading"><img src="{{ formatImage($value['icon'],293) }}"></a></li>
                        @endif
                    @endforeach
                </ul>
            @endif
            @if(count($data['notice']) > 2)
                <div class="c-bgfff p10">
                    @foreach($data['notice'] as $k=>$value)
                        @if($k > 1)
                            <a href="{{$value['url']}}" class="db pageloading"><img src="{{ formatImage($value['icon'],586) }}" class="w100"></a>
                        @endif
                    @endforeach
                </div>
                @endif

                <!-- 附近推荐商户 -->
                <div class="content-block-title f14 c-red"><i class="icon iconfont mr5">&#xe652;</i>好货推荐</div>
                <div class="list-block media-list y-sylist">
                    <ul id="wdddmain" class="row no-gutter y-recommend">
                        @if(!empty($orderData))
                            @include('wap.community.index.lists_item2')
                        @endif
                    </ul>
                    <div class="pa w100 tc allEnd none">
                        <p class="f12 c-gray mt5 mb5">没有更多了</p>
                    </div>
                </div>

                <!-- 加载提示符 -->
                <div class="infinite-scroll-preloader none">
                    <div class="preloader"></div>
                </div>
                <!-- 回到顶部 -->
                <a href="javascript:$('.content').scrollTop(0)" class="y-backtop none"></a>
        </div>

    @endif
@stop

@section($js)
    @include('wap.community._layouts.gps')
    <script type="text/javascript">
        window.opendoorpage = true;//当前页可以开门

        //精确定位
        $(function(){
            $("#indexAdvSwiper").swiper({"pagination":".swiper-pagination-adv", "autoplay":2500});
            $("#indexNavSwiper").swiper({"pagination":".swiper-pagination-nav"});

			 //回到顶部
			$(".content").scroll(function(){
				var windowheight =  $(window).height();
				var topheight = $(".content").scrollTop();
				if (topheight > windowheight*2) {
					$(".y-backtop").removeClass("none");
				}else{
					$(".y-backtop").addClass("none");
				}
			})

            var qqcGeocoder = null;
            var clientLatLng = null;

            @if(!empty($orderData['mapPointStr']))
            var clientLatLngs = "{{ $orderData['mapPointStr'] }}".split(',');
            clientLatLng = new qq.maps.LatLng(clientLatLngs[0], clientLatLngs[1]);
            @else
                $.gpsPosition(function(gpsLatLng, city, address, mapPointStr){
                        $.router.load("{{u('Index/index')}}?address="+address+"&mapPointStr="+mapPointStr+"&city="+city, true);
                    })
            @endif

            $(document).on("touchend",".data-content ul li",function(){
                        var id = parseInt($(this).data('id'));
                        if (id > 0)
                        {
                            $.router.load("{{u('Seller/detail')}}" + "?staffId=" + id, true);
                        }
                    });

            $.computeDistanceBegin = function ()
            {
                if (clientLatLng == null) {
                    $.gpsposition();
                    return;
                }

                $(".compute-distance").each(function ()
                {
                    var mapPoint = new qq.maps.LatLng($(this).attr('data-map-point-x'), $(this).attr('data-map-point-y'));
                    $.computeDistanceBetween(this, mapPoint);
                    $(this).removeClass('compute-distance');
                })
            }

            $.computeDistanceBetween = function (obj, mapPoint)
            {
                var distance = qq.maps.geometry.spherical.computeDistanceBetween(clientLatLng, mapPoint);
                if (distance < 1000)
                {
                    $(obj).html(Math.round(distance) + 'M');
                } else
                {
                    $(obj).html(Math.round(distance / 1000 * 100) / 100 + 'Km');
                }
            }

            $.SwiperInit = function (box, item, url)
            {
                $(box).infinitescroll({
                    itemSelector: item,
                    debug: false,
                    dataType: 'html',
                    nextUrl: url
                }, function (data)
                {
                    $.computeDistanceBegin();
                });
            }

            $.computeDistanceBegin();

            //重新定位
            $.relocation = function(){
                //异步Session清空
                $.post("{{ u('Index/relocation') }}",function(){
                    $.router.load("{{ u('Index/index') }}", true);
                })
            }


            //是否有展开箭头
            $.lieach = function(){
                $(".list-block ul li.each").each(function(){
                    var innerh = $(this).find(".y-mjyh li").length;
                    if (innerh >= 3) {
                        $(this).find(".y-mjyh .y-i1").removeClass("none");
                        $(this).find(".y-mjyh li").last().addClass("none");
                    }
                })
            }
            $.lieach();

            // 促销展开与收起
            $(document).off('click','.y-mjyh');
            $(document).on('click','.y-mjyh', function () {
                if($(this).find("li").length <= 2){
                    return false;
                }
                if($(this).hasClass("active")){
                    $(this).removeClass("active");
                    $(this).find(".y-unfold").addClass("none").siblings("i.y-i1").removeClass("none");
                    $(this).find("li").last().addClass("none");
                }else{
                    $(this).addClass("active");
                    // $(this).css("height",44);
                    $(this).find(".y-unfold").removeClass("none").siblings("i.y-i1").addClass("none");
                    $(this).find("li").last().removeClass("none");
                }
            });

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

                var data = new Object;
                data.page = groupPageIndex;
                data.status = "{{ $args['status'] }}";

                $.post("{{ u('Index/indexList') }}", data, function(result){
                    groupLoading = false;
                    $('.infinite-scroll-preloader').addClass('none');
                    result  = $.trim(result);
                    if (result != '') {
                        groupPageIndex++;
                        $('#wdddmain').append(result);
                        $.computeDistanceBegin();
                        $.refreshScroller();
                    }else{
                        $(".allEnd").removeClass('none');
                        nopost = 1;
                    }
                });
            });

        });


        if(window.App && parseInt({{$loginUserId}})>0){
            var result = getDoorKeys();
            window.App.doorkeys(result.responseText);
        }

    </script>
@stop