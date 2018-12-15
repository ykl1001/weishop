
@extends('install._layouts.base')
@section('images')
    <img src="{{ asset('install/images/ok.png') }}">您的o2o平台系统已完成安装！
@stop
@section('right_content')
    <div class="main">
        <p class="mt20 mb20 tc">
            <a class="btn" href="{{ u('admin#Public/login') }}">点此访问</a>
        </p>
    </div>
@stop