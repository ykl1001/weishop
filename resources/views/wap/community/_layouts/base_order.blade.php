<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@section('title'){{$site_config['site_title']}}</title>    
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="format-detection"content="telephone=no, email=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">    
    <link rel="stylesheet" href="{{ asset('wap/community/client/css/base.css') }}?{{ TPL_VERSION }}">
    <link rel="stylesheet" href="{{ asset('wap/community/client/css/jquery.mobile.custom.structure.min.css') }}?{{ TPL_VERSION }}">
    <link rel="stylesheet" href="{{ asset('wap/community/client/css/jquery.mobile.icons.min.css') }}?{{ TPL_VERSION }}">
    <link rel="stylesheet" href="{{ asset('wap/community/client/css/theme-a.min.css') }}?{{ TPL_VERSION }}">
    <link rel="stylesheet" href="{{ asset('wap/community/client/css/client.css') }}?{{ TPL_VERSION }}">
    <script src="{{ asset('wap/community/client/js/jquery-2.1.4.min.js') }}?{{ TPL_VERSION }}"></script>
    <script src="{{ asset('wap/community/client/js/jquery.mobile.custom.min.js') }}?{{ TPL_VERSION }}"></script>    
    <script src="{{ asset('wap/community/client/js/TouchSlide.1.1.js') }}?{{ TPL_VERSION }}"></script>
    <script src="{{ asset('wap/community/base.js') }}?{{ TPL_VERSION }}?{{ TPL_VERSION }}"></script>
    <script type="text/javascript">
        var conOrder_url = "{{ u('Order/confirmorder') }}";
        var delOrder_url = "{{ u('Order/delorder') }}";
        var canOrder_url = "{{ u('Order/cancelorder') }}";
        $(function() {
            $("a").attr("data-ajax","false");
            $.init();
        })
    </script>
    @yield('css')
    @yield('js')
</head>
<body>
<!-- /page -->
<div data-role="page" class="d-page">
    <!-- /header -->
    @section('show_top')
        <div data-role="header" data-position="fixed" class="x-header">
            <h1>o2o社区{{$title}}<i class="x-downico"></i></h1>
            <a href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif"  class="x-sjr ui-btns-right">
                <i class="x-serico"></i>
            </a>
        </div>
    @show
    <!-- /content -->
    @yield('content')
    <!-- content end -->
    @yield('jumpoutpage')
</div>

<style type="text/css">

    .yz_color_style .x-btns.ui-btns {margin: 10px;display: block;}
    .yz_color_style .ui-page-theme-a .ui-btns, html .ui-bar-a .ui-btns, html .ui-body-a .ui-btns, html body .ui-group-theme-a .ui-btns, html head + body .ui-btns.ui-btns-a, .ui-page-theme-a .ui-btns:visited, html .ui-bar-a .ui-btns:visited, html .ui-body-a .ui-btns:visited, html body .ui-group-theme-a .ui-btns:visited, html head + body .ui-btns.ui-btns-a:visited {
          background-color: #FFFFFF;
        }
    .yz_color_style .ui-page-theme-a .ui-btns:active, html .ui-bar-a .ui-btns:active, html .ui-body-a .ui-btns:active, html body .ui-group-theme-a .ui-btns:active, html head + body .ui-btns.ui-btns-a:active {
      background-color: #FFFFFF;
    }
    .yz_color_style .ui-page-theme-a .ui-btns:hover, html .ui-bar-a .ui-btns:hover, html .ui-body-a .ui-btns:hover, html body .ui-group-theme-a .ui-btns:hover, html head + body .ui-btns.ui-btns-a:hover {
          background-color: #FFFFFF;
        }
    .yz_color_style .ui-page-theme-a .ui-btns.ui-btns-active, html .ui-bar-a .ui-btns.ui-btns-active, html .ui-body-a .ui-btns.ui-btns-active, html body .ui-group-theme-a .ui-btns.ui-btns-active, html head + body .ui-btns.ui-btns-a.ui-btns-active, .ui-page-theme-a .ui-checkbox-on:after, html .ui-bar-a .ui-checkbox-on:after, html .ui-body-a .ui-checkbox-on:after, html body .ui-group-theme-a .ui-checkbox-on:after, .ui-btns.ui-checkbox-on.ui-btns-a:after, .ui-page-theme-a .ui-flipswitch-active, html .ui-bar-a .ui-flipswitch-active, html .ui-body-a .ui-flipswitch-active, html body .ui-group-theme-a .ui-flipswitch-active, html body .ui-flipswitch.ui-bar-a.ui-flipswitch-active, .ui-page-theme-a .ui-slider-track .ui-btns-active, html .ui-bar-a .ui-slider-track .ui-btns-active, html .ui-body-a .ui-slider-track .ui-btns-active, html body .ui-group-theme-a .ui-slider-track .ui-btns-active, html body div.ui-slider-track.ui-body-a .ui-btns-active {
        background-color: #FFFFFF;
    }
    .yz_color_style .x-tkbtns{ border-top:1px solid #e6e6e6;}
    .yz_color_style .x-tkbtns li{ width:49.5%; border:0; float:left; border-left:1px solid #e6e6e6;}
    .yz_color_style .x-tkbtns li:first-child{ border:none;}
    .yz_color_style .x-tkbtns li .x-btn.ui-btn{ border:0; color:#313233;}
    .yz_color_style .x-tkbtns li.on .x-btn.ui-btn{ color:#ff2d4b;}
    .yz_color_style #showalert .x-tkbtns li { width: 100% !important;}
    .yz_color_style .ui-page-theme-a a:visited, html .ui-bar-a a:visited, html .ui-body-a a:visited, html body .ui-group-theme-a a:visited {
      color: #000000;
    }
</style>
<div class="yz_color_style">
    <!-- 操作提示框 -->
    <div class="m-tkbg none operation" id="operation">
        <div class="x-tkbg">
            <div class="x-tkbgi">
                <div class="m-tkny" id="operation_show">
                    <p class="m-tktt">
                        <!-- 成功图标 -->
                        <!-- <i class="m-ggico"></i> -->
                        <!-- 失败图标 -->
                        <!-- <i class="m-jgico"></i> --><span class="operation_show_title">操作提示</span>
                    </p>
                    <div class="m-tkinfor">
                        <p class="x-tkfont m-tktextare">确定执行该项操作？</p>
                        <ul class="x-tkbtns x-tkbtnstip clearfix">
                            <li class="x-tksure"><a href="javascript:;" class="x-btns ui-btns operation_show_url" data-ajax="false">确定</a></li>
                            <li class="x-tkcansel operation_show_no"><a href="" class="x-btns ui-btns u-stop">取消</a></li>   
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="m-tkbg none showalert" id="showalert">
        <div class="x-tkbg">
            <div class="x-tkbgi">
                <div class="m-tkny" id="operation_show">
                    <p class="m-tktt">
                        <!-- 成功图标 -->
                        <!-- <i class="m-ggico"></i> -->
                        <!-- 失败图标 -->
                        <!-- <i class="m-jgico"></i> --><span class="operation_show_title">处理提示</span>
                    </p>
                    <div class="m-tkinfor">
                        <p class="x-tkfont x-tkfontAlart">操作成功</p>
                        <ul class="x-tkbtns clearfix">
                            <li class="x-tksure"><a href="javascript:;" class="x-btns ui-btns operation_show_alert" data-ajax="false">确定</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="image-from-cropper-box" class="m-tkbg success-show none">
     <div class="x-tkbg">
         <div class="x-tkbgi">
             <div class="m-tkny">
                 <div class="m-tkinfor m-tsct">
                     <div id="image-from-cropper-preview-box">
                         <div>
                             <img id="image-from-cropper-preview" />
                         </div>
                         <span>图片加载中...</span>
                     </div>
                     <ul class="x-tkbtn clearfix">
                         <li class="x-tkbtn1" id="image-from-cropper-btns">
                             <a href="javascript:;" id="image-from-cropper-save-btn" class="x-btn ui-btn" style="margin-right:5px;">确定</a>
                             <a href="javascript:;" class="x-btn ui-btn" style="margin-left:5px;background:#ccc;">取消</a>
                         </li>
                     </ul>
                 </div>
             </div>
         </div>
     </div>
</div>
</body>
</html>