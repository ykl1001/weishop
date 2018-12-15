<!DOCTYPE html>
<html>
<head>    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@section('title')首页@show{{$site_config['site_title']}}</title>
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="stylesheet" href="{{ asset('seller/js/suimobile/sm-extend.min.css') }}?{{ TPL_VERSION }}">
    <link rel="stylesheet" href="{{ asset('seller/js/suimobile/sm.min.css') }}?{{ TPL_VERSION }}">
    <link rel="stylesheet" href="{{ asset('seller/css/public.css') }}?{{TPL_VERSION }}">
    <script src="{{ asset('seller/js/suimobile/zepto.min.js') }}" charset='utf-8'></script>
    @yield('css')
    @yield('top_js')
</head>
<body>
<div class="page page-current" id="{{$id_action.$ajaxurl_page}}" data-ajaxurl="{!! u(CONTROLLER_NAME.'/'.ACTION_NAME,$args) !!}">
    @section('show_top')
        <header class="bar bar-nav">
            <a class="button button-link button-nav pull-left back" data-transition='slide-out'>
                <span class="icon iconfont">&#xe64c;</span>
            </a>
            <h1 class="title">{{$title}}</h1>
        </header>
    @show
    <div class="content @yield('contentcss')" @yield('distance')>
        @yield('content')
        @yield('page_js')
    </div>
</div>
<script src="{{ asset('seller/js/suimobile/sm.min.js') }}" charset='utf-8'></script>
<script src="{{ asset('seller/js/public.js') }}" charset='utf-8'></script>
<script src="{{ asset('wap/community/newbase.js') }}" charset='utf-8'></script>
@yield('js')

@yield('bnt_js')
</body>
</html>