@extends('wap.community._layouts.base')

@section('show_top')
<header class="bar bar-nav">
    <a class="button button-link button-nav pull-left pageloading"  onclick="$.href('{{u('UserCenter/index')}}')" data-transition='slide-out'>
        <span class="icon iconfont">&#xe600;</span>返回
    </a>
    <h1 class="title f16">商家入驻</h1>
</header>
@stop

@section('content')
    <div class="content">
        <div class="y-sjrzimg">
            <img src="{{$staff_settled_image}}">
        </div>
        <div class="tc y-addfanwe">
            <p>如何加入{{$site_name}}?</p>
            <img src="{{ asset('images/y13.jpg') }}">
            @if(empty($seller) || $seller["isCheck"] != 1)
                <a href="{{ u('Seller/reg') }}" class="button button-fill button-danger y-smallbutton mb10">立即加入</a>
            @else
                <a href="" class="button button-fill button-danger y-smallbutton mb10 c-bgfff c-gray">您已经申请通过，下载商家端可进行店铺管理</a>
            @endif
        </div>
        <div class="tc mt10">
            <p class="c-gray f12 mt5">点击下载商家端或用手机扫描二维码直接下载</p>
            <div class="y-sjrzewm fl mt10 mb10">
                <p class="f12 c-gray">扫描二维码下载</p>
                @if(file_get_contents(asset('staffapp.png')))
                <img src="{{ asset('staffapp.png') }}" />
                @else
                    <img src="{{ asset('images/tu.png') }}" style="width:5.65rem;" />
                @endif
            </div>
            <div class="y-sjrzbtn fl mt15 mb10 tl">
                <p><a href="#" onclick="$.openappurl(1);" class="button button-fill button-danger y-smallbutton mb10"><i class="icon iconfont c-white vat mr5">&#xe690;</i>iphone下载</a></p>
                <p><a href="#" onclick="$.openappurl(2);" class="button button-fill button-danger y-smallbutton mb10"><i class="icon iconfont c-white vat mr5">&#xe68f;</i>android下载</a></p>
            </div>
            <p class="c-gray f12 mb10">商家入驻热线：{{ $wap_service_tel }}</p>
        </div>
    </div>
@stop

@section($js)
    <script type="text/javascript">
        $.openappurl = function(type){
            var time = parseInt(Math.random()*(10000-1+1)+10000);
            var openappurl = "{{ u('seller/openappurl') }}?type="+type+'&time='+time;
            if (window.App) {
                openappurl += '&app=1';
                App.open_type('{"url":"' + openappurl + '", "open_url_type": "3"}');
            } else {
                openappurl += '&app=0';
                window.location.href = openappurl;
            }
        }
    </script>
@stop
