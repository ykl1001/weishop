@extends('wap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
        <a href="{{ u('Integral/index') }}" class="button button-link button-nav pull-left" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">积分商城</h1>
    </header>
@stop
@section($css)
    <style>
        .c-bg-h {
            background: gray!important;
        }
    </style>
@stop

@section('content')
    <div class="content">
        <!--
            <div class="x-bigpic pr">
                <img src="{{$data['image']}}" class="w100 vab" />
            </div>
        -->
        <div id="indexAdvSwiper" class="x-bigpic pr swiper-container my-swiper indexAdvSwiper" data-space-between='0' >
            <div class="swiper-wrapper">
                @foreach($data['images'] as $key => $value)
                    <div class="swiper-slide pageloading">
                        <img _src="{{ formatImage($value,640) }}" src="{{ formatImage($value,640) }}" />
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination swiper-pagination-adv"></div>
        </div>
        <div id="indexAdvSwiper" class="swiper-container my-swiper indexAdvSwiper" data-space-between='0' >
            <div class="swiper-wrapper">
                @foreach($data['banner'] as $key => $value)
                    <div class="swiper-slide pageloading" onclick="$.href('{{ $value['url'] }}')">
                        <img _src="{{ formatImage($value['image'],640) }}" src="{{ formatImage($value['image'],640) }}" />
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination swiper-pagination-adv"></div>
        </div>
        <!-- 选择数量 -->
        <div class="list-block media-list y-sylist">
            <ul>
                <li>
                    <a href="#" class="item-link item-content">
                        <div class="item-inner">
                            <div class="item-title-row">
                                <div class="item-title f14">{{$data['name']}}</div>
                                @if($userId > 0)
                                    <div @if($integral > $data['exchangeIntegral']) onclick="$.href('{{u('Order/integralorder',['goodsId' => $data['id']])}}')" @endif class="item-after y-jfdhbtn c-white @if($integral > $data['exchangeIntegral']) c-bg  @else c-bg-h @endif">立即购买</div>
                                @else
                                    <div onclick="$.href('{{u('User/login')}}')" class="item-after y-jfdhbtn c-white c-bg">立即购买</div>
                                @endif
                            </div>
                            <div class="item-subtitle mt-10">
                                <span class="c-gray2 f12"><span class="c-red f20">{{$data['exchangeIntegral']}}</span>分</span>
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        <!-- 商品详情 -->
        <div class="content-block-title f14 c-black">商品详情</div>
        <div class="c-bgfff pb10 y-img">
            <p class="p10">{!! $data['brief'] !!}</p>
        </div>
    </div>
@stop