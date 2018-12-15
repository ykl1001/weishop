@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav y-barnav">
        <a class="button button-link button-nav pull-left back" data-transition='slide-out' data-no-cache="true">
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">评价</h1>
    </header>
@stop

@section('content')
    <nav class="bar bar-tab">
        <p class="buttons-row x-bombtn">
            <a href="#" class="button" id="submit">提交</a>
        </p>
    </nav>
    <div class="content">
        @if($order['isAll'] == 1)
            @include('wap.community.order.commentallorder')
        @else
            @include('wap.community.order.commentorder')
        @endif
    </div>
@stop