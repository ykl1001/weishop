@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading" onclick="javascript:$.href('{{$backurl}}');" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">生活缴费</h1>
        <a class="button button-link button-nav pull-right open-popup toedit pageloading changeTo" href="#" data-popup=".popup-about">缴费记录</a>
    </header>
@stop

@section('content')
    <div class="content" id=''>
        <div class="content" id=''>
            <ul class="x-lifepay tc f12">
                <li onclick="$.href('{{u('Property/typepay', ['type'=>1])}}')">
                    <img src="{{asset('wap/images/life1.png')}}">
                    <p>水费</p>
                </li>
                <li onclick="$.href('{{u('Property/typepay', ['type'=>2])}}')">
                    <img src="{{asset('wap/images/life2.png')}}">
                    <p>电费</p>
                </li>
                <li onclick="$.href('{{u('Property/typepay', ['type'=>3])}}')">
                    <img src="{{asset('wap/images/life3.png')}}">
                    <p>燃气费</p>
                </li>
            </ul>
        </div>
    </div>
@stop

@section($js)
<script type="text/javascript">
$(function() {
    //切换
    $(document).on("touchend",".changeTo",function(){
        $.router.load("{{ u('Property/livelog')}}", true);
    })
})
</script>
@stop