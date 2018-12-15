@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{u('Seller/index')}}','#seller_index_view',2)"  data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
    <div class="bar bar-header-secondary">
        <div class="list-block media-list y-overallscore">
            <ul>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title-row">
                            <div class="item-title f_5e f12">总体评分：<span class="f_red f15">{{$evaluation['score']}}</span></div>
                            <div class="item-after">
                                <div class="y-start-c">
                                    <i class="icon iconfont">&#xe645;</i>
                                    <i class="icon iconfont">&#xe645;</i>
                                    <i class="icon iconfont">&#xe645;</i>
                                    <i class="icon iconfont">&#xe645;</i>
                                    <i class="icon iconfont">&#xe645;</i>
                                    <div class="y-start-r" style="width:{{$evaluation['score'] / 5 * 100}}%;">
                                        <i class="icon iconfont">&#xe645;</i>
                                        <i class="icon iconfont">&#xe645;</i>
                                        <i class="icon iconfont">&#xe645;</i>
                                        <i class="icon iconfont">&#xe645;</i>
                                        <i class="icon iconfont">&#xe645;</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
@stop
@section('css')
@stop

@section('contentcss')infinite-scroll infinite-scroll-bottom pull-to-refresh-content @stop
@section('distance') data-ptr-distance="20" @stop

@section('content')
    @include('staff.default._layouts.refresh')
    
    @if($evaluation['eva'])
        <div class="lists_item_ajax">
            @if($seller['storeType'] == 1)
                <!-- 全国店 -->
                @include("staff.default.seller.rate_all_item")
            @else
                <!-- 周边店 -->
                @include("staff.default.seller.rate_item")
            @endif
        </div>
    @else
        <div class="x-null tc" style="top:40%">
            <i class="icon iconfont">&#xe60c;</i>
            <p>很抱歉，暂无评价</p>
        </div>
    @endif
@stop