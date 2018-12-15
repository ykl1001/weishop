@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ u('Seller/getTagLists',['tradeId'=>$tradeId,'type'=>1,'tpl'=>'system']) }}','#seller_systemgoods_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <a class="button button-link button-nav pull-right y-splistcd" href="#" data-transition='slide-out'>
            <span class="icon iconfont">&#xe643;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
    <ul class="x-ltmore f12 none">
        <li onclick="JumpURL('{{u('Index/index')}}','#index_index_view',2)"><i class="icon iconfont mr020 vat">&#xe658;</i>新订单</li>
        <li onclick="JumpURL('{{u('Order/index')}}','#order_index_view_2',2)"><i class="icon iconfont mr020 vat">&#xe654;</i>订单管理</li>
        <li onclick="JumpURL('{{u('Seller/index')}}','#seller_index_view',2)"><i class="icon iconfont mr020 vat">&#xe656;</i>店铺</li>
        <li onclick="JumpURL('{{u('Mine/index')}}','#mine_index_view',2)"><i class="icon iconfont mr020 vat">&#xe623;</i>我的</li>
    </ul>
@stop
@section('css')
@stop

@section('contentcss')infinite-scroll infinite-scroll-bottom pull-to-refresh-content @stop
@section('distance')data-distance="20"  data-ptr-distance="20"@stop
@section('content')
    @include('staff.default._layouts.refresh')
    <!-- 商品列表 -->
    <div class="row no-gutter y-cnxh lists_item_ajax">
        @include('staff.default.seller.commodity_item')
    </div>
    @if(!$data)
        <div class="x-null tc">
            <i class="icon iconfont">&#xe60c;</i>
            <p>商品库暂无数据</p>
        </div>
    @endif
@stop
@section($js)
<script>
    $(document).off("click", "#{{$id_action.$ajaxurl_page}} .y-splistcd");
    $(document).on("click", "#{{$id_action.$ajaxurl_page}} .y-splistcd", function(){
        if($("#{{$id_action.$ajaxurl_page}} .x-ltmore").hasClass("none")){
            $("#{{$id_action.$ajaxurl_page}} .x-ltmore").removeClass("none");
        }else{
            $("#{{$id_action.$ajaxurl_page}} .x-ltmore").addClass("none");
        }
    });
</script>
@stop