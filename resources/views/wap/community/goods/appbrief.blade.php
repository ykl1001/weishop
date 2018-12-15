@extends('wap.community._layouts.base')

@section('css')
<style type="text/css">
    /*图文页面*/
    .x-pdel{width: 100%;}
    .x-pdel img{width: 100%;}
</style>
@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back pageloading" href="#" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">{{$top_title}}</h1>
    </header>
@stop

@section('content')
    <!-- new -->
    @include('wap.community._layouts.bottom')
    <!-- /content -->
    <div class="content">
        <!-- <div class="x-lh45 c-green" style="margin-top: 55px;"></div> -->
        <div class="x-pdel">
            <p>{!!$goods_data['brief']!!}</p>
        </div>
    </div>
@stop
