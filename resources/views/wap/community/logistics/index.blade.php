@extends('wap.community._layouts.base')


@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" onclick="$.href('{{u('UserCenter/index')}}')" href="#" data-transition='slide-out' data-no-cache="true">
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
		<a class="button button-link button-nav pull-right y-splistcd" href="#" data-transition='slide-out'>
			<span class="icon iconfont">&#xe692;</span>
			@foreach($indexnav as $key => $i_nav)			
			@if(Lang::get('api_system.index_nav.'.$i_nav['type']) == 'mine' && (int)$counts['newMsgCount'] > 0)
				<span class="y-redc"></span>
            @endif			
			@endforeach
		</a>
        <h1 class="title f16">退款/售后</h1>
    </header>
    <style>
        /*取消原因*/
        .y-cancelreason{margin: -.5rem;}
        .y-cancelreason li{}
        .y-cancelreason li span{line-height: 1.75rem;display: inline-block;max-width: 90%;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;vertical-align: top;}
        .y-radio{width: .8rem;height: .8rem;display: inline-block;-webkit-appearance: radio;margin-top: .35rem;}
        .y-otherreasons{clear: both;width: 100%;resize: none;min-height: 22px;overflow:auto;
            word-break:break-all;}
    </style>
@stop

@section('content')
	<ul class="x-ltmore f12 c-gray current_icon none">
	<link rel="stylesheet" href="{{ asset('wap/community/newclient/index_iconfont/iconfont.css') }}?{{ TPL_VERSION }}">
	@foreach($indexnav as $key => $i_nav)
		<li class="pl20" onclick="$.href('{{ u(Lang::get('api_system.index_link.'.$i_nav['type'])) }}')">		
			<i class="icon iconfont mr5 vat">{{explode(",",$i_nav['icon'])[0].";"}}</i>
			{{$i_nav['name']}}
		</li>
	@endforeach  
	</ul>
    <div class="content infinite-scroll infinite-scroll-bottom pull-to-refresh-content" data-ptr-distance="55" data-distance="50" id="">
        <div class="pull-to-refresh-layer">
            <div class="preloader"></div>
            <div class="pull-to-refresh-arrow"></div>
        </div>
        @if(!empty($list['orderList']))
            <div class="card-container" id="wdddmain">
                @include('wap.community.logistics.item')
            </div>
            <div class="pa w100 tc allEnd none">
                <p class="f12 c-gray mt5 mb5">数据加载完毕</p>
            </div>
        @else
            <div class="x-null pa w100 tc">
                <i class="icon iconfont">&#xe645;</i>
                <p class="f12 c-gray mt10">很抱歉！你还没有退款/售后的订单！</p>
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
            BACK_URL = "{{$nav_back_url or u('UserCenter/index')}}";
			$(document).off("click", ".y-splistcd");
		$(document).on("click", ".y-splistcd", function(){
			if($(".x-ltmore").hasClass("none")){
				$(".x-ltmore").removeClass("none");
			}else{
				$(".x-ltmore").addClass("none");
			}
		});	
		
		$(document).on("click", ".content", function(){
			$(".x-ltmore").addClass("none");
		}); 
            // 加载开始
            // 上拉加载
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

                $.post("{{ u('Logistics/indexList') }}", data, function(result){
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
                data.status = "{{ $args['status'] }}";

                $.post("{{ u('Logistics/indexList') }}", data, function(result){
                    groupLoading = false;
                    result  = $.trim(result);
                    if (result != "") {
                        groupPageIndex = 2;
                    }
                    $('#wdddmain').html(result);
                    $.pullToRefreshDone('.pull-to-refresh-content');
                });
            });

            //js刷新
            $.pullToRefreshTrigger('.pull-to-refresh-content');
            //加载结束
        });
    </script>
@stop