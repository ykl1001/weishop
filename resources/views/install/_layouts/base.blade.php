<!DOCTYPE html>
<!--[if IE 6]><html lang="zh-CN" class="ie6 ie9- ie8-"><![endif]-->
<!--[if IE 7]><html lang="zh-CN" class="ie7 ie9- ie8-"><![endif]-->
<!--[if IE 8]><html lang="zh-CN" class="ie8 ie9-"><![endif]-->
<!--[if IE 9]><html lang="zh-CN" class="ie9"><![endif]-->
<!--[if (gt IE 8)|!(IE)]><!-->
<html lang="zh-CN">
<!--<![endif]-->
<head>
    <meta charset="UTF-8">
    <title>平台系统-{{ $site_name }}</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome.min.css') }}">
    <!--[if IE 7]><link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome-ie7.css') }}"><![endif]-->
    <link rel="stylesheet" type="text/css" href="{{ asset('install/css/base.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('install/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/zydialog.css') }}">
    <script src="{{ asset('js/jquery.1.8.2.js') }}"></script>

    <!--[if IE 7]><link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome-ie7.css') }}"><![endif]-->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/base.css') }}">
    <script src="{{ asset('js/jquery.1.8.2.js') }}"></script>
    <script src="{{ asset('js/htbase.js') }}"></script>
    <script src="{{ asset('js/zydialog.js') }}"></script>
    <script src="{{ asset('js/jquery.bgiframe.js') }}"></script>
    <script>
        //当前控件器
        var CURR_CONTROLLER = "{{ CONTROLLER_NAME }}";
        //当前操作
        var CURR_ACTION		= "{{ ACTION_NAME }}";
        //网站链接
        var SITE_URL 		= "{{ url('/') }}";
        //图片域名
        var IMAGE_URL 		= "{{ Config::get('app.image_url') }}";
    </script>
</head>
<body>
<div class="w700 ma">
    <div class="header">
        <a href="#"><img src="{{ asset('install/images/logo.png') }}" class="logo" /></a>
        <span class="he1">安装向导</span>
        <span class="f14 fr">方维社区020 v2.0版 20181108</span>
    </div>
        <div class="y-top">
            @yield('images')
        </div>
        <div class="main y-main">
            @yield('right_content')
        </div>
    <div class="footer">Copyright@2008-2016 福建方维信息科技有限公司  版权所有 闽ICP备10206706号-7</div>
</div>
</body>
@yield('js')
</html>