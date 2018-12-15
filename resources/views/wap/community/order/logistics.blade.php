@extends('wap.community._layouts.base')
@section('title'){{$title}}@stop

@section('show_top')
    <style type="text/css">
        .p0{padding: 0;}
        .mt0{margin-top: 0;}
        .bar-footer{height: 3rem;}
        .bar-footer~.content{bottom: 3rem;}
    </style>
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" onclick="$.href('{{u('Order/detail',['id'=>$orderinfo['orderId']])}}')">
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop


@section('content')
    @include('staff.default._layouts.refresh')
    <?php
        $info = ['在途中','已揽收','疑难','已签收','退签','同城 ','派送中','退回','转单'];
    ?>
    <div class="content">
        <div class="list-block media-list m0 y-wlxq">
            <ul>
                <li class="item-content">
                    <div class="item-media">
                        <img src="{{$orderinfo['seller']['logo']}}" width="60">
                    </div>
                    <div class="item-inner">
                        @if($orderinfo['type'] == 0)
                            <div class="item-title f14">物流状态 <span class="c-red">{{$info[$orderinfo['state']] or '无'}}</span></div>
                        @endif
                        <div class="item-subtitle f12 c-gray mt5">
                            <p>运单号:
                                @if(empty($orderinfo['expressNumber']))
                                    无
                                @else
                                    {{$orderinfo['expressNumber']}}
                                @endif
                            </p>
                            <p>信息来源:
                                @if(empty($orderinfo['expressCompany']))
                                    无
                                @else
                                    {{$orderinfo['expressCompany']}}
                                @endif
                            </p>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="card y-wlxqcard">
            <div class="card-header f14">
                @if($orderinfo['type'] == 2)
                    备注内容
                @else
                    物流跟踪
                @endif
            </div>
            <div class="card-content">
                <ul class="y-wllist f12">
                    @if($orderinfo['type'] == 0)
                        @foreach($orderinfo['data'] as $k=>$v)
                            <li @if($k == 0)class="active"@endif>
                                <i>@if($k == 0)<span></span>@endif</i>
                                <p>{!! $v['context'] !!}</p>
                                <p>{{$v['ftime']}}</p>
                            </li>
                        @endforeach
                    @elseif($orderinfo['type'] == 1)
                        <p>暂不支持{{$orderinfo['expressCompany']}}的物流信息跟踪，可咨询商家或者根据订单号在该物流公司官网进行查询！</p>
                    @elseif($orderinfo['type'] == 2)
                        <p>{{$orderinfo['remark'] or '商家采用非物流配送'}}</p>
                    @endif
                </ul>
            </div>
        </div>
    </div>
@stop


