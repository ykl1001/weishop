@extends('wap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
        @if($args['id'] > 0)
            <!-- 邀请注册不显示返回按钮 -->
        @else
            <a class="button button-link button-nav pull-left" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-transition='slide-out'>
                <span class="icon iconfont">&#xe600;</span>返回
            </a>
        @endif
        <h1 class="title f16">微信绑定</h1>
    </header>
@stop

@section('css')
    <style type="text/css">
        input#vcode_input{height: 40px;width: 100px;border: 1px solid #ced6dc;}
        .x-tkfont{padding: 2em 10px;font-size: .875em;color: #999;max-height: 260px;overflow: auto;}
    </style>
@stop

@section('content')
    <div class="content" id="page-reg">
         <!-- 未开通物业提示 -->
        <div class="x-null pa w100 tc">
            <i class="icon iconfont">&#xe645;</i>
            <p class="f12 c-gray mt10">
                @if($result['code'] == 0)
                    微信绑定成功
                @else
                    {{$result['msg']}}
                @endif
            </p>
        </div>
    </div>
@stop

@section($js)

@stop