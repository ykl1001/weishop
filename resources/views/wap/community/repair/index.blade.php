@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left"  href="{{u("Property/index",['districtId'=>Input::get('districtId')])}}"   data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">故障报修</h1>
    </header>
@stop

@section('content')
    <!-- new -->
    <nav class="bar bar-tab ha">
        <div class="y-ddxqbtn2">
            <a href="{{ u('Repair/repair', ['districtId'=>$args['districtId']])}}" external class="ui-btn fr">我要报修</a>
        </div>
    </nav>
    <div class="content" id=''>
        <!-- 报修列表 -->
        @if(!empty($list))
            <div class="list-block media-list bfh0 y-gzbxbox">
                <ul class="x-troublelst mb20 pb10">
                    @include('wap.community.repair.item')
                </ul>
            </div>
        @else
            <div class="x-null pa w100 tc">
                <i class="icon iconfont">&#xe645;</i>
                <p class="f12 c-gray mt10">暂时没有报修记录</p>
            </div>
        @endif
    </div>
@stop

@section('js')
@stop
