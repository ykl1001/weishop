@extends('wap.community._layouts.base')

@section('css')
<style type="text/css">
.x-serhot1 li {
    float: left;
    padding: 0 1rem;
    margin: 0 .5rem .5rem .5rem;
    border-radius: .25rem;
    line-height: 1.65rem;
    background: #e0e0e0;
}
</style>
@stop

@section('show_top')
    <style type="text/css">
    .clearinput{
        position: absolute;
        top: -6px;
        right: 1px;
        width: 35px;
        height: 42px;
        display: none;
        background: url("{{ asset('images/ico/clear.png') }}") no-repeat center;
        -webkit-background-size: 16px;
        background-size: 16px;
        z-index: 2;
    }
    </style>
    <!-- <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back" href="javascript:$.back();" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">搜索</h1>
    </header>-->
        <header class="bar bar-nav">
            @if($option['sellerId'])
                <a class="button button-link button-nav pull-left" href="{{u('Seller/detail',['id'=>$option['sellerId']])}}" data-transition='slide-out' external>
                    <span class="icon iconfont">&#xe600;</span>返回
                </a>
            @else
            <a class="button button-link button-nav pull-left" href="{{ u('Index/index') }}" data-transition='slide-out' external>
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
    <div class="content" id=''>
        <!-- 搜索商家\商品 -->
        <!-- <form id="search_form" >
            <div class="search-input x-gsearch mb0">
                <input type="search" placeholder="请输入商家名称关键词" name="keyword" value="{{$option['keyword']}}" id="keyword">
                <label class="icon iconfont icon-search c-gray search_submit" for="search">&#xe65e;</label>
            </div>
        </form >-->
        <!-- 热门搜索 -->
        <div class="content-block-title f14 c-black">热门搜索</div>
        <div class="x-serhot">
            <ul class="c-gray tc clearfix">
            @foreach($hot_data as $hword)
                    <a href="{{ u('Seller/search',['keyword'=>$hword['hotwords'],'sellerId'=>$option['sellerId']]) }}"  class="c-gray">
                        <li >
                            {{$hword['hotwords']}}
                        </li>
                    </a>
            @endforeach
            </ul>
        </div>
			
        <!-- 搜索历史 -->
		<!-- 历史搜素 -->
		@if($history_search)
        <div class="content-block-title f14 c-black x-hislst">历史记录</div>
        <div class="list-block nobor x-hislst">
            <ul>
                @foreach($history_search as $key => $item)
                    @if( !is_array($item) )
                        <li>
                            <a href="{{ u('Seller/search',['keyword'=>$item,'sellerId'=>$option['sellerId']]) }}" class="item-content c-gray" external>
                                <div class="item-inner">
                                    <div class="item-title-row">
                                        <div class="item-title">{{$item}}</div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endif
                @endforeach
                <li class="x-clearhis">
                    <a href="javascript:;" id="clhis_btn" class="item-content c-gray" external>
                        <div class="item-inner">
                            <div class="item-title-row w100 tc">
                                <div class="item-title">
                                    <i class="icon iconfont c-gray f20 vat">&#xe630;</i>
                                    <span>清除历史记录</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
	@endif
@stop

@section($js)
    <!--<script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> -->
    <script >
        // $.SwiperInit('.data-content ul','li',"{{ u('Seller/search',$option) }}");
        $(function() {
            $(document).on("touchend",".search_submit",function(){
                var keyword = $("#keyword").val();
                $.router.load("{!! u('Seller/search') !!}?keyword=" + keyword, true);
                //$("#search_form").submit();
            });

            $(document).on("touchend",".x-clearhis",function(){
				$.showIndicator();
                $.post("{{u('Seller/clearsearch')}}", function(result){
					$('.x-hislst').hide();
					$.hideIndicator();
                    //$.router.load("{!! u('Seller/search')!!}", true);
                });
            });

			$("#keyword").focus(function(){
                $(".searchlst").removeClass("none");
            });

            $("#keyword").keyup(function(){
                if($(this).val()!=""){
                    $(".searchlst").addClass("none");
                }else{
                    $(".searchlst").removeClass("none");
                }
            });

            $(document).on("touchend",".searchlst .icon",function(){
                var clear = $(this);
                var keywords = clear.data('keywords');

                $.post("{{u('Forum/clearsearch')}}", {'keywords':keywords}, function(result){
                    clear.parents("li").slideUp('fast', function() {
                        clear.parents("li").remove();
                    });
                });

                $(this).parents("li").remove();
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
        })
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