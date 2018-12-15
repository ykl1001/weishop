@extends('staff.default._layouts.base')

@section('title'){{$title}}@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left "href="#" onclick="JumpURL('{{u('Seller/index')}}','#seller_index_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">运费设置</h1>
    </header>
    <div class="bar bar-footer bg_none height54">
        <a href="#" onclick="JumpURL('{{u('Seller/freightUpdate')}}','#seller_freightUpdate_view',2)" class="button button-fill button-danger bg_ff2d4b">修改运费</a>
    </div>
@stop

@section('css')
@stop

@section('contentcss')infinite-scroll infinite-scroll-bottom @stop
@section('distance')data-distance="20" @stop

@section('show_nav') @stop

@section('content')
    <div class="card y-yfszcard">
        <div class="card-header f14">默认（除指定地区外）运费:</div>
        <div class="card-content">
            <div class="card-content-inner f12 f_999">
                <p>{{ $list['default']['num'] ? $list['default']['num'] : 0 }}件商品内，运费{{ $list['default']['money'] ? $list['default']['money'] : '0.00' }}元</p>
                <p>每增加{{ $list['default']['addNum'] ? $list['default']['addNum'] : 0 }}件商品，运费增加{{ $list['default']['addMoney'] ? $list['default']['addMoney'] : '0.00' }}元</p>
            </div>
        </div>
    </div>

    @foreach($list['other'] as $key => $value)
    <div class="card y-yfszcard">
        <div class="card-header f14">{{$value['cityName']}}</div>
        <div class="card-content">
            <div class="card-content-inner f12 f_999">
                <p>{{ $value['data']['num'] }}件商品内，运费{{ $value['data']['money'] }}元</p>
                <p>每增加{{ $value['data']['addNum'] }}件商品，运费增加{{ $value['data']['addMoney']}}元</p>
            </div>
        </div>
    </div>
    @endforeach

    <div class="m10 y-yfszprompt">
        <i class="icon iconfont f_red">&#xe648;</i>
        <p class="f12 f_999">默认运费支持按件来计算运费，添加地区设置后，将优先使用地区设置</p>
    </div>
@stop
