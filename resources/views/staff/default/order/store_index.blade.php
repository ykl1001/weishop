@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <input id="chosedate-input" type="text" readonly onChange="$.onChange(this)"/>
        <div class="surchbmanage "><!--z-open-->
            <div class="surchbmanagebox">
                <input type="search" onkeydown="$.keywords();" id='keywords' placeholder='输入关键字...' value="{{Input::get('keywords')}}"/>
                <span class="icon iconfont" id="keywords_ck">&#xe674;</span>
            </div>
            <div class="closesurchbmanage">关闭</div>
        </div>
        <a class="button button-link button-nav pull-left" href="#" data-transition='slide-out'>
            <span class="icon iconfont">&#xe66f;</span>
        </a>
        <a class="icon button pull-right  button-link button-nav surchbmanagebtn" data-transition='slide-out'>
            <span class="icon iconfont">&#xe674;</span>
        </a>
        <h1 class="title">{{$title}}</h1>

    </header>
@stop
@section('preview')
    <div class="bar bar-header-secondary y-ordernav p0">
        <div class="buttons-tab y-couponsnav">
            <a onclick="JumpURL('{{u('Order/index')}}','#order_index_view',2)" href="#" class="tab-link @if($status == 0) active @endif button">全部@if($list['count'])<span class="f_red">({{$list['count'] or 0}})</span>@endif</a>
            <a onclick="JumpURL('{{u('Order/index',['status'=>3])}}','#order_index_view_3',2)" href="#" class="tab-link @if($status == 3) active @endif button">待付款@if($list['payment'])<span class="f_red">({{$list['payment']}})</span>@endif</a>
            <a onclick="JumpURL('{{u('Order/index',['status'=>4])}}','#order_index_view_4',2)" href="#" class="tab-link @if($status == 4) active @endif button">待发货@if($list['shipped'])<span class="f_red">({{$list['shipped']}})</span>@endif</a>
            @if(in_array($status,[6,7,8]))
                @if($status == 6)
                    <a onclick="JumpURL('{{u('Order/index',['status'=>6])}}','#order_index_view_6',2)"  href="#" class="tab-link @if($status == 6) active @endif button">待评价@if($list['rate'])<span class="f_red">({{$list['rate']}})</span>@endif</a>
                @elseif($status == 7)
                    <a onclick="JumpURL('{{u('Order/index',['status'=>7])}}','#order_index_view_7',2)"  href="#" class="tab-link @if($status == 7) active @endif button">已发货@if($list['affirmCont'])<span class="f_red">({{$list['affirmCont']}})</span>@endif</a>
                @elseif($status == 8)
                    <a onclick="JumpURL('{{u('Order/index',['status'=>8])}}','#order_index_view_8',2)"  href="#" class="tab-link @if($status == 8) active @endif button">已关闭@if($list['cancelCount'])<span class="f_red">({{$list['cancelCount']}})</span>@endif</a>
                @endif
            @else
                <a onclick="JumpURL('{{u('Order/index',['status'=>5])}}','#order_index_view_5',2)" href="#" class="tab-link @if($status == 5) active @endif button y-replace">退款中@if($list['refund'])<span class="f_red">({{$list['refund']}})</span>@endif</a>
            @endif
            <a href="tab2" class="tab-link button y-moreorder"><span>更多</span><i class="icon iconfont f12 ml5">&#xe64a;</i></a>
        </div>
    </div>
    <ul class="x-ltmore f12 c-gray y-ltmore none udb_dsy_show">
        <div class="icon iconfont y-smalljt">&#xe60a;</div>
        @if(in_array($status,[6,7,8]))
            <li onclick="JumpURL('{{u('Order/index',['status'=>5])}}','#order_index_view_5',2)" class="pl20">退款中@if($list['refund'])({{$list['refund']}})@endif</li>
        @endif
        @if($status == 6)
            <li onclick="JumpURL('{{u('Order/index',['status'=>7])}}','#order_index_view_7',2)" class="pl20">已发货@if($list['affirmCont'])({{$list['affirmCont']}})@endif</li>
            <li onclick="JumpURL('{{u('Order/index',['status'=>8])}}','#order_index_view_8',2)" class="pl20">已关闭@if($list['cancelCount'])({{$list['cancelCount']}})@endif</li>
        @elseif($status == 7)
            <li onclick="JumpURL('{{u('Order/index',['status'=>6])}}','#order_index_view_6',2)" class="pl20">待评价@if($list['rate'])({{$list['rate']}})@endif</li>
            <li onclick="JumpURL('{{u('Order/index',['status'=>8])}}','#order_index_view_8',2)" class="pl20">已关闭@if($list['cancelCount'])({{$list['cancelCount']}})@endif</li>
        @elseif($status == 8)
            <li onclick="JumpURL('{{u('Order/index',['status'=>6])}}','#order_index_view_6',2)" class="pl20">待评价@if($list['rate'])({{$list['rate']}})@endif</li>
            <li onclick="JumpURL('{{u('Order/index',['status'=>7])}}','#order_index_view_7',2)" class="pl20">已发货@if($list['affirmCont'])({{$list['affirmCont']}})@endif</li>
        @else
            <li onclick="JumpURL('{{u('Order/index',['status'=>6])}}','#order_index_view_6',2)" class="pl20">待评价@if($list['rate'])({{$list['rate']}})@endif</li>
            <li onclick="JumpURL('{{u('Order/index',['status'=>7])}}','#order_index_view_7',2)" class="pl20">已发货@if($list['affirmCont'])({{$list['affirmCont']}})@endif</li>
            <li onclick="JumpURL('{{u('Order/index',['status'=>8])}}','#order_index_view_8',2)" class="pl20">已关闭@if($list['cancelCount'])({{$list['cancelCount']}})@endif</li>
        @endif
    </ul>
@stop

@section('contentcss')admin-order-bmanage infinite-scroll infinite-scroll-bottom pull-to-refresh-content @stop
@section('distance')data-ptr-distance="20" @stop
@section('content')
    @include('staff.default._layouts.refresh')
    @if($list['orders'])
        <div class="content-block list-container lists_item_ajax">
            @include("staff.default.order.store_item")
        </div>
    @else
        <div class="x-null tc">
            <i class="icon iconfont">&#xe60c;</i>
            <p>很抱歉，暂无订单</p>
        </div>
    @endif
    @include('staff.default._layouts.store_order')
    <script type="text/javascript">
        Zepto(function($){
            $("#{{$id_action.$ajaxurl_page}} #chosedate-input").calendar({
                onClose:function(){
                    $("#{{$id_action.$ajaxurl_page}} .picker-calendar").remove();
                }
            });
            $("#{{$id_action.$ajaxurl_page}} .y-moreorder").click(function(){
                $this = $("#{{$id_action.$ajaxurl_page}} .udb_dsy_show");
                if(!$this.hasClass("none")){
                    $this.addClass("none")
                }else{
                    $this.removeClass("none")
                }
            });
            $("#{{$id_action.$ajaxurl_page}} .content").click(function(){
                $("#{{$id_action.$ajaxurl_page}} .udb_dsy_show").addClass("none");
            });
        });

    </script>
@stop