@extends('wap.community._layouts.base')

@section('show_top')
<style type="text/css">
.clearinput{
	position: absolute;
	top: -6px;
    right: 1px;
    width: 35px;
    height: 42px;
    display: none;
    background: url({{ asset('images/ico/clear.png') }}) no-repeat center;
    -webkit-background-size: 16px;
    background-size: 16px;
    z-index: 2;
}
</style>
	<header class="bar bar-nav">
        @if($option['sellerId'])
            <a class="button button-link button-nav pull-left" href="{{u('Seller/detail',['id'=>$option['sellerId']])}}" data-transition='slide-out' external>
                <span class="icon iconfont">&#xe600;</span>返回
            </a>
        @else
		<a class="button button-link button-nav pull-left" href="{{u('Seller/search')}}" data-transition='slide-out' external>
			<span class="icon iconfont">&#xe600;</span>返回
		</a>
        @endif
		<div class="searchbar x-tsearch">
		<!-- 搜索商家\商品 -->
		
			<div class="search-input pr dib">
				<form id="search_form" >
					<input type="search" id='search' placeholder='搜索附近商品或门店' name="keyword" value="{{$option['keyword']}}"/>
                    <input type="hidden"  name="sellerId" value="{{$option['sellerId']}}"/>
				</form>
				<div class="clearinput"></div>
			</div>
			<a class="button button-fill button-primary c-bg cq_search_btn" onclick="searchSub()" >搜索</a>
		</div>
	</header>
@stop

@section('content')
<style type="text/css">.x-distancetab li.on {border-bottom: 3px solid #ff2d4b;}</style>
    <div class="content" id=''>
            <div class="content-block-title f12 c-gray">&nbsp;@if($goods_count>0) @if(!$option['sellerId'])附近{{$seller_count}}个门店，@else @endif 共{{$goods_count}}个商品@endif
                @if(!$option['sellerId'])
              <a href="{{u('Seller/search',['search_type'=>'seller','keyword'=>$keyword])}}" class="fr c-red f12" external>按门店查看<i class="icon iconfont f12 c-gray">&#xe602;</i></a>
               @endif
            </div>
        @if($data)
            
            <ul class="x-distancetab c-bgfff clearfix mb10">
                @if($option['sellerId'])
                    <li class="@if($option['sort']!=2 && $option['sellerId']) on @endif"><a href="{{u('Seller/search',['search_type'=>'goods','keyword'=>$keyword,'sort'=>3,'sellerId'=>$option['sellerId']])}}" external>销量</a></li>

                @else
                <li class="@if($option['sort']!=2) on @endif"><a href="{{u('Seller/search',['search_type'=>'goods','keyword'=>$keyword,'sort'=>1,'sellerId'=>$option['sellerId']])}}" external>距离</a></li>
                @endif
                <li class="@if($option['sort']==2) on @endif"><a href="{{u('Seller/search',['search_type'=>'goods','keyword'=>$keyword,'sort'=>2,'sellerId'=>$option['sellerId']])}}" external>价格</a></li>
            </ul>

            <ul id="wdddmain" class="row no-gutter y-recommend" >
				@include('wap.community.seller.searchresult_goods_item')
			</ul>

            @else
            
		
            <!-- 没有搜索到信息 -->
            <div class="x-serno tc c-gray" style="margin-top:40%">
                <img src="{{ asset('wap/community/newclient/images/cry.png') }}" class="mr5">
                <span>没有找到符合的商品！</span>
            </div>
        @endif
    </div>
    <!-- @include('wap.community._layouts.swiper')   -->
@stop
@section($js)
    <script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key=2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O&libraries=geometry,convertor"></script>
    <script type="text/javascript">
        var clientLatLng = null;

        $(function(){
            $.computeDistanceBegin = function() {
                if (clientLatLng == null) {
                    $.getClientLatLng();
                    return;
                }

                $(".compute-distance").each(function(){
                    var mapPoint = new qq.maps.LatLng($(this).attr('data-map-point-x'), $(this).attr('data-map-point-y'));
                    $.computeDistanceBetween(this, mapPoint);
                    $(this).removeClass('compute-distance');
                })
            }

            $.getClientLatLng = function() {
                citylocation = new qq.maps.CityService({
                    complete : function(result){
                        clientLatLng = result.detail.latLng;
                        $.computeDistanceBegin();
                    }
                });
                citylocation.searchLocalCity();
            }

            $.computeDistanceBetween = function(obj, mapPoint) {
                var distance = qq.maps.geometry.spherical.computeDistanceBetween(clientLatLng, mapPoint);
                if (distance < 1000) {
                    $(obj).html(Math.round(distance) + 'm');
                } else {
                    $(obj).html(Math.round(distance / 1000) + 'Km');
                }
            }

            $.SwiperInit = function(box, item, url) {
                $(box).infinitescroll({
                    itemSelector    : item,
                    debug           : false,
                    dataType        : 'html',
                    nextUrl         : url
                }, function(data) {
                    $.computeDistanceBegin();
                });
            }
            $.computeDistanceBegin();

            $(document).on("touchend",".search_submit",function(){
                var keyword = $("#keyword").val();
                $.router.load("{!! u('Seller/search') !!}?keyword=" + keyword, true);
            });
			//caiq
			if($.trim($('#search').val())!=''){
				$('.clearinput').show();
			}
			$('.clearinput').on("touchend",function(){
				$('#search').val('').focus();
				$('.clearinput').hide();
			});
			$('#search').keyup(function(){
				if($.trim($('#search').val())!='')
					$('.clearinput').show();
				else $('.clearinput').hide();
			});
        });
		//caiq
		var pageId = 1,nomore=false;
		$('.content').scroll(function(){
			 viewH =$(this).height(),//可见高度  
			 contentH =$(this).get(0).scrollHeight,//内容高度  
			 scrollTop =$(this).scrollTop();//滚动高度  
			if((contentH - viewH - scrollTop)<=50&&!nomore){
				$.showIndicator();
				pageId++;
				dt = {};
				dt.type = 'a';
				dt.page = pageId;
				dt.keyword = "{{$option['keyword']}}";
                dt.sort = '{{$option['sort']}}';
                dt.sellerId = '{{$option['sellerId']}}';
				$.get("{{u('Seller/search')}}",dt,function(data){
					
					 if(data.length==0){
						nomore = true;
					}
					$('.y-recommend').append(data);
					$.hideIndicator();
				});
				
			}
			
		});
        $(document).on("touchend",".x-clearhis",function(){
            $(this).siblings("li").remove();
            $(this).find("span").text("暂无历史记录")
        });
		//caiq 
		function searchSub(){
			if($.trim($("#search").val())==''){
				$.toast('请输入关键字！');
				return false;
			}else{
				document.forms.search_form.submit();
			}
			
		};		
    </script>
@stop