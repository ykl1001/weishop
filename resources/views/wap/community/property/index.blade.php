@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav y-property y-proptsnav" xmlns="http://www.w3.org/1999/html"><!-- y-proptsnav  有黄色提示条时加 -->
        <a class="button button-link button-nav pull-right" onclick="javascript:$.href('{{u('District/index')}}');" data-popup=".popup-about" external>
            我的小区
        </a>
        <div class="y-proptopbox">
            <div class="y-proptitle">
                <span class="f12">我的生活在我家</span>
            </div>
            <p class="f18 mb10">{{$data['name']}}</p>
            <div class="y-proptopbtn">{{$data['address']}}</div>
        </div>
    </header>
@stop

@section($css)
    <link rel="stylesheet" type="text/css" href="{{ asset('wap/community/newclient/css/xqbb.css') }}?{{ TPL_VERSION }}" />

    <style type="text/css">
        .y-backtop{
            position: fixed;right: .5rem;bottom: 12%;width: 35px;height: 35px;
            background: url('{{asset('/images/ico/top.png')}}') no-repeat center center #fff;
            background-size: 70%;display: block;z-index: 111;border-radius: 100%;
            border: 1px solid #a9a9a9;
        }
		.y-property{
			background: url('{{asset('wap/community/newclient/images/qgdimg1.jpg')}}') no-repeat;
			background-size: 100% 100%;
			height: auto;
			padding-bottom: 0;
		}
        .bar-nav.y-property~.content{top: 159px;}
        @media screen and (min-width: 400px){
            .bar-nav.y-property~.content{top: 163px;}
        }
    </style>
@stop
@section('content')
    @include('wap.community._layouts.bottom')

    <div class="content infinite-scroll infinite-scroll-bottom"  data-distance="50" id="">
       @if( $data['isCheck'] || $data['isJoin'] || $data['isProperty'])
            <div class="y-propts" style="margin:0;">
                @if($data['isCheck'])
                    <span>您的身份信息已提交审核，请耐心等待</span>
                @elseif($data['isJoin'] || $data['isProperty'])
                    <span>您的小区物业未入驻</span>
                @endif
                <span class="fr close_par">X</span>
            </div>
        @else
            <ul class="row no-gutter y-poropnav">
                 <?php
                $type = [
                        "1"=>['icon'=>'&#xe608;','url'=>u('Property/brief',['districtId'=>$data['id']])],
                        "2"=>['icon'=>'&#xe6a6;','url'=>$is_url],
                        "3"=>['icon'=>'&#xe6a4;','url'=>'javascript:$.toast("程序正在开发");'],
                        "4"=>['icon'=>'&#xe6a2;','url'=>u('Repair/index',['districtId'=>$data['id']])],
                        "5"=>['icon'=>'&#xe6a3;','url'=>'javascript:$.toast("程序正在开发");'],
                        "6"=>['icon'=>'&#xe6a5;','url'=>u('PropertyFee/index',['sellerId'=>$data['sellerId']])],
                        "7"=>['icon'=>'&#xe774;','url'=>u('Property/livipayment')]
                ];
                ?>
                @foreach($data['system'] as $val)
                    <li class="col-33 tc @if($data['isVerify']) y-isveriy @endif"  data-id="{{$data['id']}}" data-user="{{$data['proprty']['id']}}">
                        <a @if($val['type'] == 2)data-url="{{$type[$val['type']]['url']}}"@endif @if($data['isVerify']) @else @if($data['accessStatus'] != 1 && $val['type'] == 2) class="dooraccess" @else  href="javascript:$.href('{{$type[$val['type']]['url']}}')" @endif @endif><i class="icon iconfont vat">{{$type[$val['type']]['icon']}}</i>{{$val['name']}}</a>
                    </li>
                @endforeach
            </ul>

            <div class="m-headlines flex-box mt10">
                <div class="left">
                    <img alt="" src="{{asset('wap/community/newclient/images/m-headlines.png')}}"/>
                </div>
                <div class="middle flex-1 l-line j-headlines ">
                    <div class="swiper-wrapper">
                        @if(!empty($articlelist))
                            @foreach($articlelist as $v)
                                <a class="swiper-slide" onclick="javascript:$.href('{{u('Property/articledetail',['id'=>$v['id']])}}')">{{$v['title']}}</a>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="right l-line">
                    <a onclick="javascript:$.href('{{u('Property/article',['districtId'=>$data['id']])}}')">更多</a>
                </div>
            </div>
        @endif

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
        @if($type == 1)
            <!-- 附近推荐商户 -->
            <div class="content-block-title f14 c-red"><i class="icon iconfont mr5">&#xe652;</i>好货推荐</div>
            <ul id="wdddmain" class="row no-gutter y-recommend">
                @if(!empty($orderData))
                    @include('wap.community.index.lists_item2')
                @endif
            </ul>
            @else
                    <!-- 附近推荐商户 -->
            <div class="content-block-title f14 c-gray"><i class="icon iconfont mr5">&#xe632;</i>附近推荐商户</div>
            <div class="list-block media-list y-sylist">
                <ul id="wdddmain">
                    @if(!empty($orderData))
                        @include('wap.community.index.lists_item')
                    @endif
                </ul>
            </div>
        @endif

        <div class="pa w100 tc allEnd none">
            <p class="f12 c-gray mt5 mb5">没有更多了</p>
        </div>

        <!-- 加载提示符 -->
        <div class="infinite-scroll-preloader none">
            <div class="preloader"></div>
        </div>
        <!-- 回到顶部 -->
        <a href="javascript:$('.content').scrollTop(0)" class="y-backtop none"></a>
    </div>
    @include('wap.community._layouts.js_share')
@stop

@section($js)
    <script type="text/javascript" src="{{ asset('wap/js/swiper.js') }}?{{ TPL_VERSION }}"></script>

    <script type="text/tpl" id="x-tkmodaltext-udb">
        <div class="x-tkmodaltext">
            <p class="f18 x-tktitle mb5">{{$content}}</p>
            <p class="f12 tc">可用于抵扣在线支付金额!</p>
        </div>
    </script>
    <script type="text/tpl" id="x-tkmodaltitle-udb">
        <img src="{{ asset('wap/community/newclient/images/couponspic.png') }}" class="x-yhqtktop"><i class="icon iconfont c-white x-over">&#xe604;</i>
    </script>
    @include('wap.community._layouts.gps')
    <script type="text/javascript" src="{{ asset('wap/community/newclient/js/jweixin-1.0.0.js') }}"></script>
    <script type="text/javascript">
	
		var districtId = "{{$data['districtId']}}";
		var url = 
		$(document).on("touchend",".dooraccess",function(){
			url = $(this).attr('data-url');
			$.confirm('您暂未开通手机智能开锁功能。点击确定申请开通门禁', '申请门禁', function () {				
				$.doorAccess();
			});
		})
	   
		$.doorAccess = function () {
			alert(url);
		   $.post("{{u('Property/applyaccess')}}",{'districtId':districtId},function(result){
				if(result.code == 0){
					$.modal({
						title:  '提示',
						text: '申请成功~',
						buttons: [
							{
								text: '取消',
								bold:true,
								onClick: function() {
									$(".dooraccess").attr("href","javascript:$.href('"+url+"')").removeClass('dooraccess');
								}
							},
							{
								text: '进入门禁',
								bold:true,
								onClick: function() {
									$.href(url);
								}
							}
						]
					})
				} else {
					$.alert(result.msg);
				}
			},'json');
		}
        if("{{$content}}"){
            $.alert($("#x-tkmodaltext-udb").html(),$("#x-tkmodaltitle-udb").html(),function () {
                $.href("{{u("Coupon/index")}}");
            });
            $(".modal-button,.modal-button-bold").html("立即查看");
        }
        $(document).off('click','.x-over');
        $(document).on('click','.x-over', function () {
            $(".modal").removeClass("modal-in").addClass("modal-out").remove();
            $(".modal-overlay").removeClass("modal-overlay-visible");
        });

        $(document).on('click','.y-isveriy', function () {
            var id = "{{$data['id']}}";
            var propertyUserId = "{{$data['proprty']['id']}}";
            $.modal({
                title:  '提示',
                text: '您还没有进行小区身份认证哦~',
                buttons: [
                    {text: '取消'},
                    {
                        text: '确定',
                        bold:true,
                        onClick: function() {
                            $.href("{{u('District/userapply')}}" + "?districtId=" + id+"&propertyUserId="+propertyUserId);
                        }
                    }
                ]
            })

          //  $.toast('你还没有进行小区身份认证哦~');
        });

        var BACK_URL = "{{u('property/index')}}";

        //精确定位
        $(function(){
            var swiper = new Swiper('.j-headlines', {
                pagination: '',
                direction: 'vertical',
                slidesPerView: 1,
                paginationClickable: true,
                spaceBetween: 0,
                mousewheelControl: true,
                autoplay: 2000,
                loop: true
            });

            $(".close_par").click(function(){
                $(this).parent().hide();
            })

            if(window.App){
                $("#saosao").css('display','block');
            }
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
            var now_mapPointStr = "{{$orderData['mapPointStr']}}";

            var clientLatLngs = "{{ $orderData['mapPointStr'] }}".split(',');
            clientLatLng = new qq.maps.LatLng(clientLatLngs[0], clientLatLngs[1]);


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
    </script>
@stop