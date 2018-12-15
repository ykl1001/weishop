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
    <div class="card m0 y-wdhy">
        <div class="card-header c-graybg c-black f14">
            <ul class="row no-gutter w100">
                <li class="col-33 tc">
                    <p class="c-gray f14">昵称</p>
                </li>
                <li class="col-33 tc">
                    <p class="c-gray f14">返现比率</p>
                </li>
                <li class="col-33 tc">
                    <p class="c-gray f14">羸得奖励</p>
                </li>
            </ul>
        </div>
        @if(!empty($lists)) 
        <div class="card-content" id="list">
            @include('staff.default.invitation.userlists_item')
        </div>
        @else
        <div class="x-null pa w100 tc" style="margin-top: 100px;">
            <i class="icon iconfont">&#xe645;</i>
            <p class="f12 c-gray mt10">亲，这里什么都没有！</p>
        </div>
        @endif
    </div> 

    @if(isset($lists) && count($lists) < 20)
    <div class="content-block-title tc c-gray2 mt20">没有更多了</div>
    @endif
@stop 