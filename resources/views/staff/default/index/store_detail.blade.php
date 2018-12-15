@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <style type="text/css">
        .p0{padding: 0;}
        .mt0{margin-top: 0;}
        .bar-footer{height: 3rem;}
        .bar-footer~.content{bottom: 3rem;}
    </style>
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ $nav_back_url }}','{{ $url_css }}',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
    @if($data['isCanAccept'] || $data['isPay'] || $data['isLogistics']|| $data['isRefund'])
        <div class="bar bar-tab y-agreerefund">
            @if($data['isPay'])
                <a href="#" onclick="$.isCanCancel({{$data['id']}},1 )" class="button button-light">关闭订单</a>
            @endif
            @if($data['isCanAccept'])
                <a href="#" onclick="JumpURL('{{u('Order/deliver',['id'=>$data['id']])}}','#order_deliver_view',2)" class="button button-light c-bgred">发货</a>
            @endif
            @if($data['isLogistics'])
                <a href="#" onclick="JumpURL('{{u('Order/logistics',['id'=>$data['id']])}}','#order_logistics_view',2)" class="button button-light"external>查看物流</a>
            @endif
            @if($data['isRefund'])
                <a href="#" onclick="JumpURL('{{u('Order/refundview',['id'=>$data['id']])}}','#order_refundview_view',2)" class="button button-light"external>{{$data['orderNewStatusStr']['title']}}</a>
            @endif
        </div>
    @endif
@stop
@section('contentcss')admin-order-bmanage pull-to-refresh-content  @stop
@section('distance')data-ptr-distance="20" @stop
@section('content')
    @include('staff.default._layouts.refresh')
   <div class="lists_item_ajax">
       @include("staff.default.index.store_detail_item")
   </div>
@stop

@section($js)
    <script type="text/javascript">
        $(function() {
            if($(".udb_dsy_item_li .item-title-row").length <= 0){
                $(".udb_dsy_item_li").remove();
            }
        });
    </script>
@stop
@section('preloader')@stop
@section('show_nav')@stop