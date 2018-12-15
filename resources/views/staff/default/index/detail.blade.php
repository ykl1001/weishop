@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <style type="text/css">
        .p0{padding: 0;}
        .mt0{margin-top: 0;}
        @if($data['isCanCall'] && ($role==7 || $role ==1) && $data['seller']['sendType'] == 2 && $data['sellerFee'] >= $system_send_staff_fee )

        @else
            .bar-footer{height: 3rem;}
            .bar-footer~.content{bottom: 3rem;}
        @endif
        .gray{color: #ccc;}
    </style>
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ $nav_back_url }}','{{ $url_css }}',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
    @include('staff.default._layouts.order')
@stop


@section('contentcss')admin-order-bmanage pull-to-refresh-content  @stop
@section('distance')data-ptr-distance="20" @stop
@section('content')
    @include('staff.default._layouts.refresh')
   <div class="lists_item_ajax">
       @include("staff.default.index.detail_item")
   </div>
@stop


@section('preloader')@stop
@section('show_nav')@stop