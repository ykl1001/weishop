<!-- <div class="x-goodstop x-sjpjbtn buttons-tab">
    <a class="@if(CONTROLLER_NAME == 'Goods'   && ACTION_NAME == 'index') active @endif button f15" href="#" onclick="$.href('{{u('Goods/index',['id'=>Input::get('id'), 'type'=>1])}}')"  data-no-cache="true">商品</a>
    <a class="@if(CONTROLLER_NAME == 'Goods' && ACTION_NAME == 'comment') active @endif button f15" href="{{u('Goods/comment',['id'=>Input::get('id')])}}" data-no-cache="true">评价</a>
    <a class="@if(CONTROLLER_NAME == 'Seller' && ACTION_NAME == 'detail') active @endif button f15" href="{{u('Seller/detail',['id'=>Input::get('id')])}}" data-no-cache="true">商家</a>
</div>

@if(CONTROLLER_NAME == 'Goods'   && ACTION_NAME == 'index')
    <div class="content-block-title p10 m0 c-yellow3 f14 c-black x-notice"><i class="icon iconfont mr5 f16 fl x-noticeico">&#xe647;</i>
        @if($articles)
            <span>公告：{!!$articles[0]['content']!!}</span>
        @else
        <span>无最新公告信息</span>
    @endif
        </div>
    @endif -->
<!-- 1.9 -->
<header class="bar bar-nav y-sjlistnav">
    <a class="button button-link button-nav pull-left pageloading back" href="javascript:$.href('@if(!empty($nav_back_url) && strpos($nav_back_url, u('Goods/index')) === false && strpos($nav_back_url, u('Goods/comment')) === false && strpos($nav_back_url, u('Goods/detail')) === false ){{$nav_back_url}}@else{{ u('Seller/detail',['id'=>Input::get('id')]) }} @endif')" data-transition='slide-out'>
        <span class="icon iconfont c-white">&#xe600;</span>
    </a>
    <h1 class="title f16 c-white">{{$seller['name']}}</h1>
    <a class="button button-link button-nav pull-right open-popup collect_opration" data-popup=".popup-about">
        <!-- 分享 -->
        <i class="icon share iconfont c-white">&#xe616;</i>

        @if($seller['isCollect'] == 1)
            <!-- 已收藏图片  -->
            <i class="icon collect iconfont c-white m0 on">&#xe654;</i>
        @else
            <!-- 未收藏图片  -->
            <i class="icon collect iconfont c-white m0">&#xe653;</i>
        @endif
    </a>
    <div class="list-block media-list y-sylist y-sjxq clear">
        <ul>
            <li>
                <a href="{{u('Seller/detail',['id'=>Input::get('id')])}}" class="item-link item-content">
                    <div class="item-media mt0"><img src="{{ formatImage($seller['logo'],200,200) }}" width='45'></div>
                    <div class="item-inner c-white pt0">
                        <div class="item-title f12 mt5 maxwidth">
                            起送<span class="mr5">￥{{$seller['serviceFee']}}</span>
                            <span class="mr5">|</span>
                            配送<span>￥{{$seller['deliveryFee']}}</span>
                            @if($seller['isAvoidFee'] == 1)
                                <span>(满{{$seller['avoidFee']}}免)</span>
                            @endif
                        </div>
                        <div class="item-subtitle">公告：
                            @if($articles)
                                {{$articles[0]['content']}}
                            @else
                                无最新公告信息
                            @endif
                        </div>
                    </div>
                    <i class="icon iconfont mr10 pt0 c-white">&#xe602;</i>
                </a>
            </li>
        </ul>
    </div>
    @if(!empty($seller['activity']['full']))
        <div class="y-sjnotice c-white f12 pt10 pb10">
            <span class="p0 y-splistspan">
                <i class="icon iconfont va-2 p0 mr5">&#xe647;</i>
                <?php $first = true; ?>
                @foreach($seller['activity']['full'] as $key => $value)
                    @if($first)
                        <?php $first = false; ?>
                        满{{$value['fullMoney']}}减{{$value['cutMoney']}}元
                    @else
                        ,满{{$value['fullMoney']}}减{{$value['cutMoney']}}元
                    @endif
                @endforeach
                @if(!empty($seller['activity']['new']))
                    @if($seller['activity']['new']['fullMoney'] > 0)
                        新用户在线支付满{{$seller['activity']['new']['fullMoney']}}元立减{{$seller['activity']['new']['cutMoney']}}元
                    @else
                        新用户在线支付立减{{$seller['activity']['new']['cutMoney']}}元
                    @endif
                @endif
            </span>
        </div>
    @endif
</header>
<div class="bar bar-header-secondary p0">
    <div class="x-goodstop buttons-tab pb1">
        @if($seller['countGoods'] >0)
            <a href="#" onclick="$.href('{{ u('Goods/index',['id'=>Input::get('id'),'type'=>1])}}')"  class="button f15 @if(CONTROLLER_NAME=='Goods' && ACTION_NAME=='index' && Input::get('type')==1) active @endif" data-no-cache="true">商品</a>
        @endif
        @if($seller['countService'] >0)
            <a href="#" onclick="$.href('{{ u('Goods/index',['id'=>Input::get('id'),'type'=>2])}}')"  class="button f15 @if(CONTROLLER_NAME=='Goods' && ACTION_NAME=='index' && Input::get('type')==2) active @endif" data-no-cache="true">服务</a>
        @endif
        <a href="#" onclick="$.href('{{u('Goods/comment',['id'=>Input::get('id')])}}')" class="button f15 @if(CONTROLLER_NAME == 'Goods' && ACTION_NAME == 'comment') active @endif" data-no-cache="true">评价</a>
    </div>
</div>

@section("ajax")
    <script type="text/javascript">
        Zepto(function($){
		$(document).on("touchend",".collect_opration .collect",function(){
			var obj = new Object();
			var collect = $(this);
			obj.id = "{{$seller['id']}}";
			obj.type = 2;
			if(collect.hasClass("on")){
				$.post("{{u('UserCenter/delcollect')}}",obj,function(result){
					if(result.code == 0){
						collect.removeClass("on").html('&#xe653;');
						$.toast(result.msg);
					} else if(result.code == 99996){
						$.router.load("{{u('User/login')}}", true);
					} else {
						$.toast(result.msg);
					}
				},'json');
			}else{
				$.post("{{u('UserCenter/addcollect')}}",obj,function(result){
					if(result.code == 0){
						collect.addClass("on").html('&#xe654;');
						$.toast(result.msg);
					} else if(result.code == 99996){
						$.router.load("{{u('User/login')}}", true);
					} else {
						$.toast(result.msg);
					}
				},'json');
			}
		});
	});
    </script>
@stop 