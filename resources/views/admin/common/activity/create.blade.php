@extends('admin._layouts.base')
@section('css')
<style type="text/css">
    .right_box{border: 1px solid #d1d1d1; width: 300px; height: 100px; float: left;margin: 25px 50px 0 15px; cursor: pointer}
    .my_img{ margin: 10px 10px 0 10px;;}
    .fla{float: left}
    .right_box .title{margin-top: 5px; color: #ff0000}
</style>
@stop
@section('right_content')
    @yizan_begin
    <div class="g-fwgl">
        <p class="f-bhtt f14 clearfix">
            <span class="ml15 fl">选择活动类型</span>
        </p>
    </div>

    <div class="right_box">
        <a href="{{ u('Activity/add',array('type'=>1)) }}">
            <div class="fla">
                <img src="{{ asset('images/fenx.jpg') }}" style="width: 80px; height: 80px;" class="my_img">
            </div>
            <div class="fla" style="width: 195px;">
                <h2 class="title">分享送优惠券</h2>
                <p style="color: #404054">允许用户在订单成交后，将系统生成的红包分享到朋友圈，并通知好友抢红包</p>
            </div>
        </a>
    </div>

    <div class="right_box">
        <a href="{{ u('Activity/add',array('type'=>2)) }}">
            <div class="fla">
                <img src="{{ asset('images/zhuc.jpg') }}" style="width: 80px; height: 80px;" class="my_img">
            </div>
            <div class="fla" style="width: 195px;">
                <h2 class="title">注册送优惠券</h2>
                <p style="color: #404054">允许新注册的用户获得优惠券，积极参与注册！</p>
            </div>
        </a>
    </div>

    <div class="right_box">
        <a href="{{ u('Activity/add',array('type'=>4)) }}">
            <div class="fla">
                <img src="{{ asset('images/shou.jpg') }}" style="width: 80px; height: 80px;" class="my_img">
            </div>
            <div class="fla" style="width: 195px;">
                <h2 class="title">首单活动</h2>
                <p style="color: #404054">新用户初次下单，将减免一定费用</p>
            </div>
        </a>
    </div>

    <div class="right_box">
        <a href="{{ u('Activity/add',array('type'=>5)) }}">
            <div class="fla">
                <img src="{{ asset('images/jian.jpg') }}" style="width: 80px; height: 80px;" class="my_img">
            </div>
            <div class="fla" style="width: 195px;">
                <h2 class="title">满减活动</h2>
                <p style="color: #404054">用户一次下单满额减免一定费用</p>
            </div>
        </a>
    </div>

    {{--<div class="right_box">--}}
        {{--<a href="{{ u('Activity/add',array('type'=>3)) }}">--}}
            {{--<div class="fla">--}}
                {{--<img src="{{ asset('images/qiangg.jpg') }}" style="width: 80px; height: 80px;" class="my_img">--}}
            {{--</div>--}}
            {{--<div class="fla" style="width: 195px;">--}}
                {{--<h2 class="title">线下优惠券发放</h2>--}}
                {{--<p style="color: #404054">允许用户在订单成交后，将系统生成的红包分享到朋友圈，并通知好友抢红包</p>--}}
            {{--</div>--}}
        {{--</a>--}}
    {{--</div>--}}
    @yizan_end
@stop
@section('js')

@stop