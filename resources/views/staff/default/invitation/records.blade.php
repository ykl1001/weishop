@extends('staff.default._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{u('Invitation/index')}}','#mine_index_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header> 
@stop
@section('contentcss')bcf infinite-scroll infinite-scroll-bottom pull-to-refresh-content @stop
@section('distance')data-distance="20"  data-ptr-distance="20"@stop
@section('content')
    @include('staff.default._layouts.refresh')
    @if(!empty($lists)) 
    <ul class="list-container" id="list">
        @include('staff.default.invitation.records_item')
    </ul>
    @else
    <div class="x-null pa w100 tc">
        <i class="icon iconfont">&#xe645;</i>
        <p class="f12 c-gray mt10">亲，这里什么都没有！</p>
    </div>
    @endif
    <!-- 加载完毕提示 -->
    <div class="pa w100 tc allEnd none">
        <p class="f12 c-gray mt5 mb5">数据加载完毕</p>
    </div>
    <!-- 加载提示符 -->
    <div class="infinite-scroll-preloader none">
        <div class="preloader"></div>
    </div> 
@stop 