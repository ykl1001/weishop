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
    <style type="text/css">
        .y-null404 a.button{display: inline-block;padding: 0 1rem;line-height: 1.7rem;height: 1.7rem;margin: .5rem .25rem 0;}
        .y-404{width: 5rem;height: 5rem;border-radius: 100%;background: #d7d8da;color: #fff;font-size: 1.8rem;line-height: 5rem;font-weight: bold;margin: 0 auto;}
    </style>
    @yield('css')
    <script type="text/javascript">
        var SITE_URL = "{{ u('/') }}";
    </script>
</head>
<body>
    <div class="page-group" id="error">
        <div>
            @yield('content')
        </div>
     </div>
</body>
</html>


