@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <h1 class="title tl pl10">新维修单</h1>
    </header>
@stop
@section('contentcss')admin-order-bmanage infinite-scroll infinite-scroll-bottom pull-to-refresh-content @stop
@section('distance')data-ptr-distance="20" @stop
@section('content')
    @include('staff.default._layouts.refresh')
    <!-- 下面是正文 -->
    <div class="list-block media-list lists_item_ajax" >
        @if($list['list'])
            @include("staff.default.index.itemrepair")
            <div class="pa w100 tc allEnd none">
                <p class="f12 c-gray mt5 mb5">数据加载完毕</p>
            </div>
        @else
            <div class="x-null tc">
                <i class="icon iconfont">&#xe60c;</i>
                <p>很抱歉，暂无最新订单</p>
            </div>
        @endif
    </div>

    <!-- 加载提示符 -->
    <div class="infinite-scroll-preloader none">
        <div class="preloader"></div>
    </div>
@stop