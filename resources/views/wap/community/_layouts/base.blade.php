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
    <!-- SUI基础js -->
    <script src="{{ asset('wap/community/newclient/suimobile/zepto.min.js') }}?{{ TPL_VERSION }}" charset='utf-8'></script>
    @yield('css')
    <script type="text/javascript">
        var SITE_URL = "{{ u('/') }}";
        var BACK_URL = "";
    </script>
</head>
<body>
    <div class="y-partnerbox isShareAlertShowHtml y-partnerbox-visible @if(!$is_share_alert_show)none @endif">
        <div class="y-graybg isShareAlertShowJs"></div>
        <div class="y-partnertc">
            <div class="y-partnercont">
                <div class="y-img"><img src="{{asset('wap/community/newclient/images/y23.png')}}"></div>
                <div class="y-partnermain">
                    <div class="p10 c-bgfff y-ptrmaintop f14">您已成为{{$is_share_alert_show_data['name']}}的第{{$is_share_alert_show_data['count']}}位Ⅰ级合伙人</div>
                    <div class="p15">
                        <a href="{{ $is_share_alert_show_data['downloadAddress'] }}" class="button button-fill c-yellow4 mb15">下载{{$site_config['site_title']}}APP</a>
                        @if($is_share_alert_show_data['url'])
                            <a href="{{$is_share_alert_show_data['url']}}" class="button button-fill c-bggreen">关注{{$site_config['site_title']}}公众号</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-group">

      <div class="page @if($noshow != 1) page-current @endif " @section('pid') @show>
            <!-- 顶部 -->
            @section('show_top')
                <header class="bar bar-nav">
                    <a class="button button-link button-nav pull-left" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-transition='slide-out'>
                        <span class="icon iconfont">&#xe600;</span>返回
                    </a>
                    <a class="button button-link button-nav pull-right open-popup" data-popup=".popup-about"></a>
                    <h1 class="title f16">o2o社区{{$title}}</h1>
                </header>
            @show
            <!-- 底部 -->
            @yield('footer')
            <!-- 中间 -->
            @yield('content')
            <!-- Ajax CSS -->
            @yield('css_ajax')
            <!-- Ajax Js -->
			@yield('js_ajax')
			
            @yield('ajax')
			
      </div>
    </div>
    <script type="text/tpl" id="ajax-list-loading">
        <center style="border:none;font-size:0.8em;">
            <img src="{{ asset('wap/images/loading.fast.gif') }}" style="width:30%;" />
            <p>加载中....</p>
        </center>
    </script>
    <script type="text/javascript">
        if("{{$is_share_alert_show or 0}}" == 1){
            $.post("{{u('user/isShareAlertShow')}}",{},function(){});
        }
        //如果连接为外链，打开webview
        $(document).on('click', 'a', function(e) {
            var regUrl = /^((http|https)\:\/\/)([\w.]+)(\/[\w- \.\/\?%&=]*)?/;
            if($(this).hasClass("appOpenUrl")){
                var appOpenUrl = $(this).attr("appOpenUrl");
            }else{
                var appOpenUrl = $(this).attr("href");
            }

            if (regUrl.test(appOpenUrl) && appOpenUrl.indexOf(SITE_URL) < 0) {
                e.preventDefault();
                $(this).attr("href", "#").attr("appOpenUrl", appOpenUrl).addClass("appOpenUrl");
                if (window.App) {
                    App.open_type('{"url":"' + appOpenUrl + '", "open_url_type": "1"}');
                } else {
                    window.location.href = appOpenUrl;
                }
                return false;
            }
        });

        //ajax加载列表数据
        $.ajaxListFun = function(obj, url, data,callFun){
            var loadHtml = $("#ajax-list-loading").html();
            obj.html(loadHtml);
            $.post(url, data, function(result){
                result  = $.trim(result);
                obj.html(result);
                callFun.call(this,result);
            });
        }
    </script>
    <script src="{{ asset('wap/community/newclient/suimobile/sm.min.js') }}?{{ TPL_VERSION }}" charset='utf-8'></script>
    <script src="{{ asset('wap/community/newclient/suimobile/sm-extend.min.js') }}?{{ TPL_VERSION }}" charset='utf-8'></script>
    <script src='{{ asset('wap/community/newclient/suimobile/sm-city-picker.js') }}' charset='utf-8'></script>
    <!-- 公用js -->
    <script src="{{ asset('wap/community/newbase.js') }}?{{ TPL_VERSION }}" charset='utf-8'></script>
    <script src="{{ asset('js/hammer.min.js') }}?{{ TPL_VERSION }}" charset='utf-8'></script>
    <script src="{{ asset('image.js') }}?{{ TPL_VERSION }}" charset='utf-8'></script>

    <style type="text/css">
        .fanwecutphoto .modal-inner{padding:0;}
        .fanwecutphoto .modal-text{padding:0.8rem;}
    </style>
    <!-- 页面js -->
    @yield('js')

    <script type="text/javascript">
        //订单操作处理URL
        var conOrder_url = "{{ u('Order/confirmorder') }}";
        var delOrder_url = "{{ u('Order/delorder') }}";
        var canOrder_url = "{{ u('Order/cancelorder') }}";
		//门禁钥匙
		var doorKeys_url = "{{ u('Property/getdoorkeys') }}";

        $("input").keyup(function(){
            this.value=this.value.replace(/\ud83c[\udf00-\udfff]|\ud83d[\udc00-\ude4f]|\ud83d[\ude80-\udeff]/g,'');
        });

        //阻止路由 添加迷你加载器
        $(".external").click(function(){
            $.showIndicator();
        });
		//当前页面是否允许摇一摇开门
		$(function(){
			//
			function isopendoor(){
					var s = window.opendoorpage===true?'1':'0';
					var res = '{"data":"'+ s +'"}';
					return res;
			}
			if(window.App){
				window.App.iscanshakeopendoor(isopendoor());
			}
            $.init();
		});

        //返回按钮定义 如果未获取到上一级目录 或者 上一级目录为站外链接 返回按钮固定跳转到首页
        if(!document.referrer || document.referrer.indexOf(SITE_URL) < 0)
        {
            $("header a.back").attr("href", "{{ u('Index/index') }}").removeClass('back');
        }
        $(document).on('click', '.isShareAlertShowJs', function(e) {
            $(".isShareAlertShowHtml").addClass("none");
        });
    </script>
</body>
</html>


