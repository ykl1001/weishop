@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="{{ u('UserCenter/index') }}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">分享返现</h1>
    </header>
@stop

@section('content')
   <div class="content c-grayf4" id=''>
        <div class="buttons-row y-cashback">
            <a href="#" class="button f12">
                <p>成功邀请</p>
                <p><span class="f18">0</span>人</p>
            </a>
            <a href="#" class="button f12">
                <p>您已赚到</p>
                <p><span class="f18">0</span>元</p>
            </a>
        </div>
        <div class="y-ewm"><img src="{{ asset('wap/community/newclient/images/ewm.png') }}"></div>
        <div class="buttons-row y-fxfs f14">
            <a href="#" class="button">
                <div class="y-fxfsimg"><img src="{{ asset('wap/community/newclient/images/y10.png') }}"></div>
                <p class="c-gray">分享给微信好友</p>
            </a>
            <a href="#" class="button">
                <div class="y-fxfsimg"><img src="{{ asset('wap/community/newclient/images/y11.png') }}"></div>
                <p class="c-gray">分享到朋友圈</p>
            </a>
        </div>
    </div>
        
@stop

@section($js)
@stop
