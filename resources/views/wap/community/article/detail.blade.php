@extends('wap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back" href="#" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">{{$data['title']}}</h1>
    </header>
@stop
@section('content')
    @include('wap.community._layouts.bottom')
    <div class="content c-bgfff" id=''>
        <div class="y-about f14">
            <p>{!! $data['content'] !!}</p>
        </div>
    </div>
<script type="text/javascript">
    $(function() {
        $(".y-gywm").css("min-height",$(window).height()-45);
    })
</script>
@stop