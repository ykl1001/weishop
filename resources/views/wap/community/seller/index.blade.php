@extends('wap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="javascript:$.href('{{ u('Index/index') }}');" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <!--<div class="title y-sjflbtn">
            <div class="buttons-row">
                <a href="#" onclick="$.href('{{u('Seller/index',['id' =>$args['id'] ,'types' =>'goods'])}}')" class="button @if(!$args['types'] || $args['types'] == 'goods') active @endif">商品</a>
                <a href="#" onclick="$.href('{{u('Seller/index',['id' =>$args['id'] ,'types' =>'seller'])}}')" class="button @if($args['types'] == "seller") active @endif">店铺</a>
            </div>
        </div>-->
		<h1 class="title f16">@if(!$args['types'] || $args['types'] == 'goods') 商品分类 @else {{$cate['name']}} @endif</h1>
        @if($args['types'] == 'seller')
            <a class="button button-link button-nav pull-right" href="#"  onclick="$.href('{{ u('Seller/search')}}')" data-transition='slide-out'>
                <span class="icon iconfont c-gray">&#xe65e;</span>
            </a>
        @else
            <a href="#"  onclick="$.href('{{ u('Seller/cates',['type' => 1])}}')"   class="button button-link button-nav pull-right">
                <div class="dib tc mr5" style="margin-top: -8px">
                    <i class="icon iconfont mr0">&#xe636;</i>
                    <p class="f12"  style="margin-top: -25px">分类</p>
                </div>
            </a>
        @endif

    </header>
@stop
@section('css')
    <style>
		.button.active, .button.active:active{
			color: #393939;
			border-color: #fff;
			border-radius: 0;
		}
    </style>
@stop
<?php

$sorts = array(
        0 => '综合排序',
        1 => '按销量倒序',
        2 => '按起送价',
        3 => '距离最近',
        4 => '评分最高',
);
$tab1 = $tab2 = 1;
foreach($seller_cates as $key=>$val) {
    if($val['id'] == $args['id']) {
        $tab1 = $tab2 = $key+1;
        break;
    } else {
        foreach($val['childs'] as $k=>$v) {
            if($v['id'] == $args['id']) {
                $tab1 = $tab2 = $key+1;
                break;
            }
        }
    }
}
// $tab1 = $tab2 = 1;
?>
@section('content')
    <div class="bar bar-header-secondary p0">
        <div class="buttons-tab x-sjfl mb10">
            @if($args['types'] == 'seller')
            <a href="#tab1" class="tab-link button f13 x-searchname active y-sort">
                <span>@if($args['id'] > 0 && $args['id'] == $cate['id']){{$cate['name']}} @else 全部 @endif</span>
                <i class="icon iconfont up">&#xe623;</i>
                <i class="icon iconfont down ml0 none">&#xe624;</i>
            </a>
            @endif
            <a href="#" onclick=" $.rightSort(0);" class="show0 button f13 y-sort  @if($args['sort'] == 0)c-red @else c-gray5 @endif">综合</a>
            <a href="#"  onclick=" $.rightSort(1);" class="show1 button f13 y-sort @if($args['sort'] == 1)c-red @else c-gray5 @endif">销量</a>
            @if($args['types'] == 'seller')
                <a href="#"  onclick=" $.rightSort(3);" class="show3 button f13 y-sort @if($args['sort'] == 3)c-red @else c-gray5 @endif">距离</a>
                <a href="#"  onclick=" $.rightSort(4);" class="show4 button f13 y-sort @if($args['sort'] == 4)c-red @else c-gray5 @endif">评分</a>
            @else
                <a href="#"  onclick=" $.rightSort(3);" class="show3 button f13 y-sort @if($args['sort'] == 3)c-red @else c-gray5 @endif">佣金</a>
                <a href="#"  onclick=" $.rightSort(4);" class="show4 button f13 y-sort @if($args['sort'] == 4)c-red @else c-gray5 @endif">价格</a>
            @endif
        </div>
    </div>
		
     <div class="contents" style="bottom: 0px;">
		<!-- 全部筛选 -->
		<div class="x-sjfltab x-goodstab clearfix pa none">
			<div class="buttons-tab fl pr ">
				@foreach ($seller_cates as $key => $value)
					<a href="#tab1_{{$key+1}}" class="tab-link button  @if($tab1 == ($key+1))active @endif">{{ $value['name'] }}</a>
				@endforeach
			</div>
			<div class="tabs c-bgfff fl">
				@foreach($seller_cates as $k => $item)
					<div id="tab1_{{$k+1}}" class="button height100 tab @if($tab2 == ($k+1)) active @endif">
						<div class="list-block x-sortlst f14 seller-cate">
							<ul>
								<li class="item-content @if($item['id'] == $args['id']) on active @endif" data-id="{{$item['id']}}">
									<div class="item-inner">
										<div class="item-title" data-id="{{$item['id']}}">全部</div>
										<i class="icon iconfont c-red f20">&#xe60f;</i>
									</div>
								</li>
								@foreach($seller_cates[$k]['childs'] as $val)
									<li class="item-content @if($val['id'] == $args['id']) on active @endif" data-id="{{$val['id']}}">
										<div class="item-inner typecatess">
											<div class="item-title seller-cate" data-id="{{$val['id']}}">{{$val['name']}}</div>
											<i class="icon iconfont c-red f20">&#xe60f;</i>
										</div>
									</li>
								@endforeach
							</ul>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	</div>
    <div class="content infinite-scroll infinite-scroll-bottom pull-to-refresh-content" data-ptr-distance="55" data-distance="50" id="">
        <div class="pull-to-refresh-layer">
            <div class="preloader"></div>
            <div class="pull-to-refresh-arrow"></div>
        </div>
        @if($args['types']  == "seller")
            @if(!empty($banner))
            <div class="y-sjflban">
                <a href="{{ $banner['url'] }}">
                    <img src="{{ $banner['image'] }}">
                </a>
            </div>
            @endif
        @endif
        <div id="seller-list">
            <div class="tabs mt10">
                <div id="tab1" class="tab active">
                    @if($args['types']  == "seller")
                    <div class="list-block media-list y-sylist">
                        <ul id="appendList"></ul>
                    </div>
                        @else
                        <div class="list-block media-list y-sylist">
                            <ul class="row no-gutter y-recommend mt10"  id="appendList"></ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>

                <!-- 加载提示符 -->
            <div class="infinite-scroll-preloader none">
                <div class="preloader"></div>
            </div>
    </div>
    @if($args['types']  != "seller")
        @include('wap.community._layouts.js_share')
    @endif
@stop

@section($js)
    @include('wap.community._layouts.gps')
    <script type="text/tpl" id="ajax-list-no">
        <div class="x-null pa w100 tc">
                <i class="icon iconfont">&#xe645;</i>
                <p class="f12 c-gray mt10">很抱歉！没有找到相关@if($args['types'] == 'seller')商家@else商品@endif！</p>
            </div>
    </script>
    <script type="text/javascript">
	
		
        var qqcGeocoder = null;
        var clientLatLngs = "{{ $args['mapPoint'] }}".split(',');
        clientLatLng = new qq.maps.LatLng(clientLatLngs[0], clientLatLngs[1]);
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
                $(obj).html(Math.round(distance) + 'm');
            } else
            {
                $(obj).html(Math.round(distance / 1000 * 100) / 100 + 'Km');
            }
        }



        var pageArgs = {!! json_encode($args) !!};

        $(function(){
            $(".x-sjfltab").css("top",$(".bar-nav").height()*2-1);
            var sjfltabs_h = $(".x-sjfltab .x-sjflheight").height();
			if(!sjfltabs_h){
				sjfltabs_h = $(window).height()-44;
			}
            $(".x-sjfltab .buttons-tab").css("height",sjfltabs_h);
			$(".page-current .content").css({"bottom":0});

            // 下拉
            $(document).off("touchend",".x-sjfl .x-searchname");
            $(document).on("touchend",".x-sjfl .x-searchname",function(){
                if($(this).hasClass("on")){
                    $(".x-sjfltab").addClass("none");
                    $(this).removeClass("on");
                    $(this).find(".up").removeClass("none");
                    $(this).find(".down").addClass("none");
                }else{
                    $(".x-sjfltab").removeClass("none");
                    $(this).addClass("on");
                    $(this).find(".up").addClass("none");
                    $(this).find(".down").removeClass("none");
                }
            });
            $(document).on("touchend",".x-sjfltab .x-sortlst li",function(){
                $(this).addClass("active").siblings().removeClass("active");
                $(".x-sjfl .x-searchname span").text($(this).find(".item-title").text());
                $(this).parents(".x-sjfltab").addClass("none");
                $(".x-sjfl .x-searchname").removeClass("on");
                $(".x-sjfl .x-searchname .up").removeClass("none");
                $(".x-sjfl .x-searchname .down").addClass("none");
            });
            $(document).on("touchend",".x-sjfltab .mask",function(){
                $(this).parent().addClass("none");
                $(".x-sjfltab").addClass("none");
                $(".x-sjfl .x-searchname").removeClass("on");
                $(".x-sjfl .up").removeClass("none");
                $(".x-sjfl .down").addClass("none");
                return false;
            });
            $(document).off('touchend','.create-actions');
            $(document).on('touchend','.create-actions', function () {
                $(".x-sjfltab").addClass("none");
                $(".x-sjfl .x-searchname").removeClass("on");
                $(".x-sjfl .up").removeClass("none");
                $(".x-sjfl .down").addClass("none");
                var buttons1 = [
                    {
                        text: '请选择',
                        label: true
                    },
                    {
                        text: '综合排序',
                        bold: true,
                        color: 'danger',
                        onClick: function() {
                            $(".create-actions").text("综合排序");
                            $.rightSort(0);
                        }
                    },
                    {
                        text: '按销量倒序',
                        onClick: function() {
                            $(".create-actions").text("按销量倒序");
                            $.rightSort(1);
                        }
                    },
                    {
                        text: '按起送价',
                        onClick: function() {
                            $(".create-actions").text("按起送价");
                            $.rightSort(2);
                        }
                    },
                    {
                        text: '距离最近',
                        onClick: function() {
                            $(".create-actions").text("距离最近");
                            $.rightSort(3);
                        }
                    },
                    {
                        text: '评分最高',
                        onClick: function() {
                            $(".create-actions").text("评分最高");
                            $.rightSort(4);
                        }
                    }
                ];
                var buttons2 = [
                    {
                        text: '取消',
                        bg: 'danger'
                    }
                ];
                var groups = [buttons1, buttons2];
                $.actions(groups);
            });


            //ajax加载商家或者商品列表
            var ajaxData = new Object;
            ajaxData.page = 1;
            ajaxData.id = "{{ $args['id'] }}";
            ajaxData.sort = "{{ $args['sort'] }}";
            ajaxData.types = "{{ $args['types'] }}";
            var ajaxObj = $("#appendList");
            var ajaxUrl = "{{ u('Seller/indexList') }}";
            $.ajaxListFun(ajaxObj, ajaxUrl, ajaxData, function(result){
                if(result == ""){
                    $("#seller-list").html($("#ajax-list-no").html());
                }else{
                    $.computeDistanceBegin();
                }

            });
			//左侧
			$(document).on("touchend", ".seller-cate ul li", function(){
				$(this).addClass("on").siblings().removeClass("on");
				var id = $(this).attr('data-id');
				pageArgs.id = id;
				$.router.load("{!! u('Seller/index')!!}?" + $.param(pageArgs), true);
			});

			//右侧
			$.rightSort = function(sort) {
				pageArgs.sort = sort;

				if (sort == 3) {
					if (clientLatLng == null) {
						isSortPosition = true;
						$("#showalertposition").removeClass('none');
						return false;
					}
					pageArgs.mapPoint = clientLatLng.getLat() + "," + clientLatLng.getLng();
				}
				$(".show"+sort).addClass("c-red").removeClass('c-gray5');
				$.href("{!! u('Seller/index')!!}?" + $.param(pageArgs));
			};

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
				data.id = "{{ $args['id'] }}";
				data.sort = "{{ $args['sort'] }}";
				data.types = "{{ $args['types'] }}";

				$.post("{{ u('Seller/indexList') }}", data, function(result){

					$('.infinite-scroll-preloader').addClass('none');
					result  = $.trim(result);
					if (result.length!=0) {
                        groupLoading = false;
						groupPageIndex++;
						$('#appendList').append(result);
						$.computeDistanceBegin();
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
				data.page = 1;
				data.id = "{{ $args['id'] }}";
				data.sort = "{{ $args['sort'] }}";
				data.types = "{{ $args['types'] }}";

				$.post("{{ u('Seller/indexList') }}", data, function(result){

					result  = $.trim(result);
					if (result.length!=0) {
						groupPageIndex = 2;
                        groupLoading = false;
					}
					$('#appendList').html(result);
					$.computeDistanceBegin();
					$.pullToRefreshDone('.pull-to-refresh-content');
				});
			});


			//是否有展开箭头
			$.each($(".list-block ul li.each"),function(k,v){
				var innerh = $(this).find(".y-mjyh").height();
				if (innerh > 54) {
					$(this).find(".y-mjyh .y-i1").removeClass("none");
					$(this).find(".y-mjyh").css("height",44);
				}
			});
			// 促销展开与收起
			$(document).on('click','.y-mjyh', function () {
				if($(this).find("li").length <= 2){
					return false;
				}
				if($(this).hasClass("active")){
					$(this).removeClass("active");
					$(this).find(".y-unfold").addClass("none").siblings("i.y-i1").removeClass("none");
				}else{
					$(this).addClass("active");
					$(this).css("height",44);
					$(this).find(".y-unfold").removeClass("none").siblings("i.y-i1").addClass("none");
				}
			});

        });

    </script>
@stop
