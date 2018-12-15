@extends('install._layouts.base')
@section('images')
    <img src="{{ asset('install/images/img2.png') }}">
@stop
@section('right_content')
    <p><b>填写数据库信息</b></p>
    <form id="yz_form" name="yz_form" class="ajax-form" method="post" action="{{u('Index/install')}}">
        <div class="x-form mt15 mb20">
            @foreach($froms['dbinfo'] as $key=> $item)
                <p class="showdb">
                    <label>{{ $item['name'] }}：</label>
                    <input type="{{$item['type']}}" name="dbinfo[{{$key}}]" class="f{{$key+1}} @if($item['error']) x-error @endif" placeholder="{{ $item['value'] }}" value="{{$item['value']}}" />
                </p>
                @if($item['error'])
                    <p class="x-pserror">
                        <label></label>
                        <span><i class="x-errorico"></i>注意：{{ $item['msg'] }}</span>
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

        <p class="mt10"><b>填写管理员信息</b></p>
        <div class="x-form mt15 mb20">
            @foreach($froms['admin'] as $key=> $item)
                <p class="showadmin">
                    <label>{{ $item['name'] }}：</label>
                    <input type="{{$item['type']}}" name="admin[{{$key}}]" class="f{{$key+1}} @if($item['error']) x-error @endif" placeholder="{{ $item['value'] }}" value="{{$item['value']}}" />
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
        @if($db_error)
            <div class="x-form mt15 mb20">
                <p class="x-pserror">
                    <label></label>
                    <span><i class="x-errorico"></i>{{$db_error}}</span>
                </p>
            </div>
        @endif
        <p class="mt20 tc">
            <a href="{{ u('Index/check') }}" class="btn btn2 mr15">上一步</a>
            <input type="submit" class="btn next" value="下一步" />
        </p>
    </form>
@stop