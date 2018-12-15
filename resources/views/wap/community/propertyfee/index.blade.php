@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading" onclick="javascript:$.href('{{$backurl}}');" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <a class="button button-link button-nav pull-right c-gray f14" href="{{ u('PropertyFee/log',['sellerId'=>$args['sellerId']]) }}">缴费记录</a>
        <h1 class="title f16">物业费缴纳</h1>
    </header>
@stop

@section('content')
    <div class="bar bar-header-secondary y-twonobor">
        <div class="buttons-tab y-couponsnav y-splistm y-noborder">
            <a class="tab-link button @if(empty($args['payitemId'])) active @endif" onclick="$.href('{{u('PropertyFee/index',['sellerId'=>$args['sellerId']])}}')">全部缴费</a>
            <a class="tab-link button y-sxbtn @if(!empty($args['payitemId'])) active @endif"><span>
                    @if(!empty($args['payitemId']))
                        @foreach($payitemlist as $k=>$item)
                            @if($item['id'] == $args['payitemId'])
                                {{$item['name']}}
                            @endif
                        @endforeach
                    @else
                        筛选
                    @endif</span>
                <i class="icon iconfont f14 ml5 mr0 va0 y-down">&#xe623;</i>
                <i class="icon iconfont f14 ml5 va0 y-up none">&#xe624;</i>
            </a>
        </div>
    </div>
    <ul class="x-ltmore f12 c-gray tc y-wysx none">
        @foreach($payitemlist as $item)
            <li><a external href="{{ u('PropertyFee/index',['sellerId'=>$args['sellerId'],'payitemId'=>$item['id']]) }}">{{$item['name']}}</a></li>
        @endforeach
    </ul>
    <div class="bar bar-footer">
        <span class="f14 c-gray y-lineh">合计:<span class="c-red f18 totleprice"></span></span>
        <a class="x-menuok c-bg c-white f16 fr" onclick="$.pay()" external>去结算</a>
    </div> 
    <div class="content" id=''>

        <div class="card y-shopcart mt10 y-wyjfmain">
            @if(empty($list['payable']))
                <div class="card-header active">
                    <span class="c-black f14"><i class="icon iconfont mr10 c-gray">&#xe60f;</i>应缴账单</span>
                </div>
                <div class="card-content">
                    <div class="tc mt20 mb20 pt10">
                        <img src="{{asset('wap/community/newclient/images/nothing.png')}}" width="108">
                        <p class="c-gray5 f13">暂无应缴账单</p>
                    </div>
                </div>
            @else
                <div class="card-header active">
                    <span class="c-black f14"><i class="icon iconfont mr10 c-gray">&#xe60f;</i>应缴账单</span>
                </div>
                <div class="card-content">
                    <div class="list-block media-list">
                        <ul class="y-wd">
                            @foreach($list['payable'] as $item)
                                <li>
                                    <a href="#" class="item-link item-content active" data-price="{{ $item['fee'] }}" data-id="{{ $item['id'] }}">
                                        <div class="item-media icon iconfont c-gray mr5">&#xe60f;</div>
                                        <div class="item-inner">
                                            <div class="item-title-row">
                                                <div class="item-title f13 c-black">{{ $item['roomfee']['payitem']['name'] }}</div>
                                                <div class="item-after f13 mt10 c-red">￥{{ $item['fee'] }}</div>
                                            </div>
                                            <div class="item-title-row mt-10">
                                                <div class="item-title f12 c-gray">账单日期：{{ Time::toDate($item['beginTime'],'Y-m-d') }}至{{ Time::toDate($item['endTime'],'Y-m-d') }}</div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        <div class="card y-shopcart y-wyjfmain">
            @if(empty($list['prepay']))
            <div class="card-header active">
                <span class="c-black f14"><i class="icon iconfont mr10 c-gray">&#xe60f;</i>预缴账单</span>
            </div>
            <div class="card-content">
                <div class="tc mt20 mb20 pt10">
                    <img src="{{asset('wap/community/newclient/images/nothing.png')}}" width="108">
                    <p class="c-gray5 f13">暂无预缴账单</p>
                </div>
            </div>
            @else
            <div class="card-header active">
                <span class="c-black f14"><i class="icon iconfont mr10 c-gray">&#xe60f;</i>预缴账单</span>
            </div>
            <div class="card-content">
                <div class="list-block media-list">
                    <ul class="y-wd">
                        @foreach($list['prepay'] as $item)
                            <li>
                                <a href="#" class="item-link item-content active" data-price="{{ $item['fee'] }}" data-id="{{ $item['id'] }}">
                                    <div class="item-media icon iconfont c-gray mr5">&#xe60f;</div>
                                    <div class="item-inner">
                                        <div class="item-title-row">
                                            <div class="item-title f13 c-black">{{ $item['roomfee']['payitem']['name'] }}</div>
                                            <div class="item-after f13 mt10 c-red">￥{{ $item['fee'] }}</div>
                                        </div>
                                        <div class="item-title-row mt-10">
                                            <div class="item-title f12 c-gray">账单日期：{{ Time::toDate($item['beginTime'],'Y-m-d') }}至{{ Time::toDate($item['endTime'],'Y-m-d') }}</div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>
@stop

@section($js)
<script type="text/javascript">
var price = 0;
var ids = [];
function getprice(){
    price = 0;
    ids = [];
    $(".list-block ul li").each(function(i){
        if($(this).children(".item-content").hasClass('active')){
            ids.push($(this).children(".item-content").attr('data-id'));
            price = price*1+$(this).children(".item-content").attr('data-price') * 1;
        }
    })
    ids.join()
    $('.totleprice').html("￥"+price);
    if(price == 0){
        $(".x-menuok").removeClass("c-red").addClass("c-gray97").attr('onclick','#');
    }else{
        $(".x-menuok").removeClass("c-gray97").addClass("c-red").attr('onclick','$.pay()');
    }
}
$(function(){
    getprice();
})

$.pay = function(){
    window.location.href = "{{u('Order/propertypay')}}"+"?ids="+ids;
}


$(document).on('click','.y-splistm a.button', function () {
    $(this).addClass("active").siblings().removeClass("active");
    if(!$(".y-sxbtn").hasClass("active")){
        $(".y-sxbtn span").text("筛选");
    }
});
$(document).on('click','.y-sxbtn', function () {
    if($(".y-wysx").hasClass("none")){
        $(".y-wysx").removeClass("none");
        $(".y-sxbtn .y-down").addClass("none").siblings("i").removeClass("none");
    }else{
        $(".y-wysx").addClass("none");
        $(".y-sxbtn .y-up").addClass("none").siblings("i").removeClass("none");
    }
});
$(document).on('click','.y-wysx li', function () {
    $(".y-sxbtn span").text($(this).find("a").text());
    $(".y-wysx").addClass("none");
    $(".y-sxbtn .y-up").addClass("none").siblings("i").removeClass("none");
});
//全部选中
$(document).on("click", ".y-wyjfmain .card-header i.icon", function(){
    var shopname = $(this).parents(".card-header").siblings(".card-content").find(".item-content");
    if($(this).parents(".card-header").hasClass("active")){
        $(this).parents(".card-header").removeClass("active");
        shopname.removeClass("active");
    }else{
        $(this).parents(".card-header").addClass("active");
        shopname.addClass("active");
    }
    getprice();

});
$(document).on("click", ".y-wyjfmain .card-content .item-media", function(){
    var shopthis = $(this).parents(".card-content").siblings(".card-header");
    var ul = $(this).parents("ul");
    if($(this).parents(".item-content").hasClass("active")){
        $(this).parents(".item-content").removeClass("active");
        shopthis.removeClass("active");
    }else{
        $(this).parents(".item-content").addClass("active");
    }
    if(ul.find(".item-content.active").length==ul.find("li").length){
        shopthis.addClass("active");
    }
    getprice();
});
</script>
@stop
