@extends('wap.community._layouts.base')


@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" onclick="$.href('{{u('Order/index',$args)}}')" data-transition='slide-out' data-no-cache="true">
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
        <h1 class="title f16">@if($args['type'])服务选择@else重新选择@endif</h1>
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
    <div class="content c-bgfff" id=''>
        <div class="list-block media-list">
            <ul class="y-nobor2">
                <li>
                    <a class="item-link item-content p0" href="{{u('Logistics/refund',['orderType'=>1,'id'=>$args['id'],'type'=>$args['type']])}}" external >
                        <div class="item-inner pr10 pl10">
                            <div class="item-title-row">
                                <div class="item-title">退货退款</div>
                                <div class="item-after icon iconfont c-gray mt10">&#xe602;</div>
                            </div>
                            <div class="item-title c-gray f12 mt-10">已收到货，需要退还已收到的货物</div>
                        </div>
                    </a>
                </li>
                <li>
                    <a class="item-link item-content p0" href="{{u('Logistics/refund',['orderType'=>2,'id'=>$args['id'],'type'=>$args['type']])}}" external >
                        <div class="item-inner pr10 pl10">
                            <div class="item-title-row">
                                <div class="item-title">仅退款</div>
                                <div class="item-after icon iconfont c-gray mt10">&#xe602;</div>
                            </div>
                            <div class="item-title c-gray f12 mt-10">未收到货，或与卖家协商同意前提下申请</div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
@stop

@section($js)
    <script type="text/javascript">
        $(function(){
            $(document).off("click", ".y-splistcd");
            $(document).on("click", ".y-splistcd", function(){
                if($(".x-ltmore").hasClass("none")){
                    $(".x-ltmore").removeClass("none");
                }else{
                    $(".x-ltmore").addClass("none");
                }
            });

            $(document).on("click", ".content", function(){
                $(".x-ltmore").addClass("none");
            });
        });
    </script>
@stop