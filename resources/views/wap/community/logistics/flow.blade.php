@extends('wap.community._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" onclick="$.href('{{u('Logistics/index',$args)}}')" href="#" data-transition='slide-out' data-no-cache="true">
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <a class="button button-link button-nav pull-right y-splistcd" href="#" data-transition='slide-out'>
            <span class="icon iconfont">&#xe692;</span>
            @foreach($indexnav as $key => $i_nav)
                @if(Lang::get('api_system.index_nav.'.$i_nav['type']) == 'mine' && (int)$counts['newMsgCount'] > 0)
                    <span class="y-redc"></span>
                @endif
            @endforeach
        </a>
        <h1 class="title f16">选择物流</h1>
    </header>
@stop

@section('content')
    <ul class="x-ltmore f12 c-gray current_icon none">
        <link rel="stylesheet" href="{{ asset('wap/community/newclient/index_iconfont/iconfont.css') }}?{{ TPL_VERSION }}">
        @foreach($indexnav as $key => $i_nav)
            <li class="pl20" onclick="$.href('{{ u(Lang::get('api_system.index_link.'.$i_nav['type'])) }}')">
                <i class="icon iconfont mr5 vat">{{explode(",",$i_nav['icon'])[0].";"}}</i>
                {{$i_nav['name']}}
            </li>
        @endforeach
    </ul>
    <div class="content" id=''>
        <div class="content-block-title m10 f_999 f12">公司列表</div>
        <div class="list-block media-list">
            <ul>
                @foreach($couriercompany as $k=>$v)
                    <li>
                        <a href="#" onclick="$.clickjsjump('{{$k}}')" class="item-link item-content pr10">
                            <div class="item-inner pr10">
                                <div class="item-title f_5e f13">{{$k}}</div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@stop

@section($js)
    <script type="text/javascript">
        $(function(){
            $.clickjsjump = function(url){
                var u = '{{u('Logistics/logistics')}}?orderId={{$args['orderId']}}&name='+url;
                $.router.load(u, true)
            }
        });
    </script>
@stop