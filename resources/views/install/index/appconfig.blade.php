@extends('install._layouts.base')
@section('images')
    <img src="{{ asset('install/images/img2.png') }}">
@stop
@section('right_content')
    <form id="yz_form" name="yz_form" class="ajax-form" method="post" action="{{u('Index/saveapp')}}">
        <p class="mt10"><b>填写短信配置</b></p>
        <div class="x-form mt15 mb20">
            @foreach($froms['sms'] as $key=> $item)
                <p class="showadmin">
                    <label>{{ $item['name'] }}：</label>
                    <input type="{{$item['type']}}" name="sms[{{$key}}]" class="f{{$key+1}} @if($item['error']) x-error @endif" placeholder="{{ $item['value'] }}" value="{{$item['value']}}" />
                </p>
                @if($item['error'])
                    <p class="x-pserror">
                        <label></label>
                        <span><i class="x-errorico"></i>{{$item['msg']}}</span>
                    </p>
                @else
                    @if($item['notice'])
                        <p class="">
                            <label></label>
                            <span><i class=""></i>{{$item['notice']}}</span>
                        </p>
                    @endif
                @endif
            @endforeach
        </div>
        <p class="mt10"><b>{{$froms['upload']['upload']['name']}}:</b></p>
        <div class="x-form">
            <p class="showadmin tc" style="width: 100%">
                <span style="padding-top:10px;" class="tc">Oss上传方式:</span><input style="width:50px;height: 20px"  type="radio" name="upload" class=""  value="1" @if($upload==1 )checked="checked" @endif onclick="res(1)"/>
                <span style="line-height: 50px" class="tc">本地上传方式:</span><input style="width:50px;height: 20px"  type="radio" name="upload" class="" value="2" @if($upload==2)checked="checked" @endif onclick="res(2)"/>
            </p>
        </div>

        <div class="oss_show show_db @if($upload==2) none @endif">
            <p class="mt10"><b>OSS图片上传配置</b></p>
            <div class="x-form mt15 mb20">
                @foreach($froms['oss'] as $key=> $item)
                    <p class="showadmin">
                        <label>{{ $item['name'] }}：</label>
                        <input type="text" name="oss[{{$key}}]" class="f{{$key+1}} @if($item['error']) x-error @endif" placeholder="{{ $item['value'] }}" value="{{$item['value']}}" />
                    </p>
                    @if($item['error'])
                        <p class="x-pserror">
                            <label></label>
                            <span><i class="x-errorico"></i>{{$item['msg']}}</span>
                        </p>
                    @else
                        @if($item['notice'])
                            <p class="">
                                <label></label>
                                <span><i class=""></i>{{$item['notice']}}</span>
                            </p>
                        @endif
                    @endif
                @endforeach
            </div>
        </div>
        <div class="sreves_show show_db @if($upload==1) none @endif">
            <p class="mt10"><b>本地上传配置</b></p>
            <div class="x-form mt15 mb20">
                本地上传有系统自带功能 无须配置相关设置，为了你更好的体验建议使用oss图片上传功能。
            </div>
        </div>
        <p class="mt20 tc">
            <a href="{{ u('Index/database') }}" class="btn btn2 mr15">上一步</a>
            <input type="submit" class="btn next" value="下一步" />
        </p>
    </form>
@stop
@section("js")

    <script type="text/javascript">

    function res(cs)
    {
        var param;
        $(".show_db").addClass("none");
        if(cs == 1)
        {
            param = "oss_show";
        }
        if(cs == 2)
        {
            param = "sreves_show";
        }
        $("."+param).removeClass("none");

    }
    </script>


@stop