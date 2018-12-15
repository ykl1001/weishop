@extends('wap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav y-zgsytop">
        <a class="button button-link button-nav pull-left" id="saosao" onclick="$.sao()" @if($weixinliu == 1) style="display: block" @else style="display: none" @endif external>
            <i class="icon iconfont">&#xe67c;</i>
            <p class="f12">扫一扫</p>
        </a>
        <div class="title tl c-white" onclick="$.href('{{ u('Seller/search')}}')">
            <i class="icon iconfont f13">&#xe65e;</i>
            <input type="text" placeholder="搜索商品、店铺">
            <div class="y-zgsearch"></div>
        </div>
        <a class="button button-link button-nav pull-right" external onclick="$.href('{{ u('Tag/index')}}')">
            <i class="icon iconfont">&#xe636;</i>
            <p class="f12">分类</p>
        </a>
    </header>
@stop
@section('css')
    <style type="text/css">
        .gps-loading{text-align: center;width:60%;height:auto;margin:30px auto;}
        .gps-loading img{width:100%;}
    </style>
@stop
@section('content')
<div class="content infinite-scroll infinite-scroll-bottom"  data-distance="50" id="">
    <div class="gps-loading">
        <img src="{{ asset('wap/images/loading.fast.gif') }}" />
        <p>定位中，请稍候......</p>
    </div>
</div>
@stop

@section($js)

    @include('wap.community._layouts.gps')
    <script type="text/javascript">

    $.gpsPosition(function(gpsLatLng, city, address, mapPointStr,area){
        $.router.load("{{u('Index/index')}}?address="+address+"&mapPointStr="+mapPointStr+"&city="+city+"&area="+area, true);
    })
    </script>
@stop