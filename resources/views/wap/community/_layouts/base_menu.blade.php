<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@section('title'){{$site_config['site_title']}}</title>
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
    <!-- <link rel="shortcut icon" href="/favicon.ico"> -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no, email=no">

    <link rel="stylesheet" href="{{ asset('wap/community/newclient/suimobile/sm.min.css') }}?{{ TPL_VERSION }}">
    <link rel="stylesheet" href="{{ asset('wap/community/newclient/suimobile/sm-extend.min.css') }}?{{ TPL_VERSION }}">
    <link rel="stylesheet" href="{{ asset('wap/community/newclient/css/base.css') }}?{{ TPL_VERSION }}">
    <link rel="stylesheet" href="{{ asset('wap/community/newclient/iconfont/iconfont.css') }}?{{ TPL_VERSION }}">
    <link rel="stylesheet" href="{{ asset('wap/community/newclient/css/color.css') }}?{{ TPL_VERSION }}">
    <link rel="stylesheet" href="{{ asset('wap/community/newclient/css/style.css') }}?{{ TPL_VERSION }}">
    @yield('css')

</head>
<body>
    <div class="page-group">

            <!-- 中间 -->
            @yield('content')

    </div> 

    <!-- SUI基础js -->
    <script src="{{ asset('wap/community/newclient/suimobile/zepto.min.js') }}?{{ TPL_VERSION }}" charset='utf-8'></script>
    <script src="{{ asset('wap/community/newclient/suimobile/sm.min.js') }}?{{ TPL_VERSION }}" charset='utf-8'></script>
    <script src="{{ asset('wap/community/newclient/suimobile/sm-extend.min.js') }}?{{ TPL_VERSION }}" charset='utf-8'></script>
    <script type="text/javascript">
        $.init();
    </script>

</body>
</html>


